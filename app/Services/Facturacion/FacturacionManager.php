<?php

namespace App\Services\Facturacion;

use App\Models\Comprobante;
use App\Models\FacturacionConfig;
use App\Services\Facturacion\Contracts\FacturacionDriver;
use App\Services\Facturacion\Drivers\BetaDriver;
use App\Services\Facturacion\Drivers\GreenterDriver;
use App\Services\Facturacion\Drivers\NingunoDriver;

/**
 * Punto de entrada de la facturacion electronica.
 * Resuelve el driver segun la configuracion y coordina emision / pruebas.
 */
class FacturacionManager
{
    private FacturacionConfig $config;

    public function __construct(?FacturacionConfig $config = null)
    {
        $this->config = $config ?? FacturacionConfig::actual();
    }

    public function config(): FacturacionConfig
    {
        return $this->config;
    }

    public function driver(): FacturacionDriver
    {
        return match ($this->config->driver) {
            'greenter' => new GreenterDriver($this->config),
            'beta' => new BetaDriver($this->config),
            default => new NingunoDriver(),
        };
    }

    /**
     * Emite un comprobante y persiste el resultado en sus columnas sunat_*.
     */
    public function emitir(Comprobante $comprobante): ResultadoEmision
    {
        if (! $this->config->estaHabilitada()) {
            $resultado = ResultadoEmision::pendiente('Facturacion electronica deshabilitada.');
        } else {
            $resultado = $this->driver()->emitir($comprobante);
        }

        $comprobante->forceFill($resultado->paraComprobante())->save();

        return $resultado;
    }

    public function probarConexion(): ResultadoConexion
    {
        return $this->driver()->probarConexion();
    }

    /**
     * Emite una nota de credito y persiste el resultado en sus columnas sunat_*.
     */
    public function emitirNota(\App\Models\NotaCredito $nota): ResultadoEmision
    {
        if (! $this->config->estaHabilitada()) {
            $resultado = ResultadoEmision::pendiente('Facturacion electronica deshabilitada.');
        } else {
            $driver = $this->driver();
            $resultado = $driver instanceof GreenterDriver
                ? $driver->emitirNota($nota)
                : ResultadoEmision::pendiente('Las notas de credito requieren el driver Greenter.');
        }

        $nota->forceFill($resultado->paraComprobante())->save();

        return $resultado;
    }

    /**
     * Envia un resumen diario de boletas y guarda el ticket devuelto.
     *
     * @param  iterable  $boletas
     */
    public function enviarResumen(\App\Models\ResumenDiario $resumen, iterable $boletas): ResultadoEmision
    {
        if (! $this->config->estaHabilitada()) {
            $resultado = ResultadoEmision::pendiente('Facturacion electronica deshabilitada.');
        } else {
            $driver = $this->driver();
            $resultado = $driver instanceof GreenterDriver
                ? $driver->enviarResumen($resumen, $boletas)
                : ResultadoEmision::pendiente('Los resumenes requieren el driver Greenter.');
        }

        $resumen->forceFill([
            'sunat_estado' => $resultado->estado,
            'sunat_ticket' => $resultado->ticket ?: $resumen->sunat_ticket,
            'sunat_mensaje' => mb_substr($resultado->mensaje, 0, 250),
            'sunat_hash' => $resultado->hash ?: $resumen->sunat_hash,
            'sunat_xml_path' => $resultado->xmlPath ?: $resumen->sunat_xml_path,
            'sunat_cdr_path' => $resultado->cdrPath ?: $resumen->sunat_cdr_path,
        ])->save();

        return $resultado;
    }

    /** Consulta un resumen por ticket y aplica el efecto sobre sus boletas. */
    public function consultarResumen(\App\Models\ResumenDiario $resumen): ResultadoEmision
    {
        $resultado = $this->consultarPorTicket($resumen);

        if ($resultado->estado === 'aceptado') {
            $resumen->comprobantes()->update([
                'sunat_estado' => 'aceptado',
                'sunat_mensaje' => 'Aceptada en resumen '.$resumen->identificador(),
                'sunat_cdr_path' => $resumen->sunat_cdr_path,
            ]);
        } elseif ($resultado->estado === 'rechazado') {
            // Libera las boletas para reenviarlas en un nuevo resumen.
            $resumen->comprobantes()->update([
                'sunat_estado' => 'pendiente',
                'resumen_id' => null,
                'sunat_ticket' => null,
            ]);
        }

        return $resultado;
    }

    /**
     * Comunica la baja de una factura y guarda el ticket devuelto.
     */
    public function comunicarBaja(\App\Models\ComunicacionBaja $baja, Comprobante $factura): ResultadoEmision
    {
        if (! $this->config->estaHabilitada()) {
            $resultado = ResultadoEmision::pendiente('Facturacion electronica deshabilitada.');
        } else {
            $driver = $this->driver();
            $resultado = $driver instanceof GreenterDriver
                ? $driver->comunicarBaja($baja, $factura)
                : ResultadoEmision::pendiente('La comunicacion de baja requiere el driver Greenter.');
        }

        $baja->forceFill([
            'sunat_estado' => $resultado->estado,
            'sunat_ticket' => $resultado->ticket ?: $baja->sunat_ticket,
            'sunat_mensaje' => mb_substr($resultado->mensaje, 0, 250),
            'sunat_hash' => $resultado->hash ?: $baja->sunat_hash,
            'sunat_xml_path' => $resultado->xmlPath ?: $baja->sunat_xml_path,
            'sunat_cdr_path' => $resultado->cdrPath ?: $baja->sunat_cdr_path,
        ])->save();

        return $resultado;
    }

    /** Consulta una baja por ticket y anula la factura si fue aceptada. */
    public function consultarBaja(\App\Models\ComunicacionBaja $baja): ResultadoEmision
    {
        $resultado = $this->consultarPorTicket($baja);

        if ($resultado->estado === 'aceptado') {
            $factura = $baja->comprobante;
            if ($factura && $factura->estado !== 'anulado') {
                \Illuminate\Support\Facades\DB::transaction(function () use ($factura) {
                    foreach ($factura->items as $item) {
                        if ($item->producto_id) {
                            \App\Models\Producto::where('id', $item->producto_id)->increment('stock', (int) ceil($item->cantidad));
                        }
                    }
                    $factura->update(['estado' => 'anulado', 'sunat_estado' => 'anulado']);
                });
            }
        }

        return $resultado;
    }

    /**
     * Consulta generica por ticket para documentos asincronos (RC / RA).
     */
    private function consultarPorTicket(\Illuminate\Database\Eloquent\Model $doc): ResultadoEmision
    {
        $driver = $this->driver();
        if (! ($driver instanceof GreenterDriver)) {
            return ResultadoEmision::pendiente('La consulta requiere el driver Greenter.');
        }
        if (blank($doc->sunat_ticket)) {
            return ResultadoEmision::pendiente('Aun no hay ticket. Envialo primero.');
        }

        $resultado = $driver->consultarTicket($doc->sunat_ticket);

        $doc->forceFill([
            'sunat_estado' => $resultado->estado,
            'sunat_mensaje' => mb_substr($resultado->mensaje, 0, 250),
            'sunat_cdr_path' => $resultado->cdrPath ?: $doc->sunat_cdr_path,
        ])->save();

        return $resultado;
    }

    /** Badges de estado para la cabecera del modulo. */
    public function badges(): array
    {
        return [
            'habilitada' => $this->config->estaHabilitada(),
            'driver' => $this->config->labelDriver(),
            'entorno' => $this->config->entorno,
            'certificado' => $this->config->certificadoExiste(),
        ];
    }
}
