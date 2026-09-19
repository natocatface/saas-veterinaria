<?php

namespace App\Services\Facturacion\Drivers;

use App\Models\Comprobante;
use App\Models\FacturacionConfig;
use App\Services\Facturacion\Contracts\FacturacionDriver;
use App\Services\Facturacion\ResultadoConexion;
use App\Services\Facturacion\ResultadoEmision;
use App\Services\Facturacion\UblXmlBuilder;
use Illuminate\Support\Facades\Storage;

/**
 * Driver Beta (homologacion SUNAT).
 *
 * Genera el XML UBL 2.1 del comprobante y lo guarda en storage. Verifica que
 * la configuracion este completa (RUC, credenciales Clave SOL y certificado).
 *
 * La FIRMA digital y el ENVIO real a los web services de SUNAT requieren la
 * libreria Greenter (composer require greenter/lite) + tu certificado .pem.
 * Cuando esten instalados, aqui se reemplaza la generacion por el envio real
 * y se guarda el CDR devuelto por SUNAT.
 */
class BetaDriver implements FacturacionDriver
{
    public function __construct(private FacturacionConfig $config)
    {
    }

    public function emitir(Comprobante $comprobante): ResultadoEmision
    {
        $faltantes = $this->camposFaltantes();
        if ($faltantes !== []) {
            return ResultadoEmision::pendiente(
                'Faltan datos para emitir: '.implode(', ', $faltantes).'.'
            );
        }

        $builder = new UblXmlBuilder($this->config);
        $xml = $builder->build($comprobante);

        $nombre = $this->config->ruc.'-'
            .$builder->tipoDocumento($comprobante).'-'
            .$comprobante->serie.'-'
            .str_pad((string) $comprobante->numero, 6, '0', STR_PAD_LEFT).'.xml';

        $ruta = 'facturacion/pe/xml/'.$nombre;
        Storage::disk('local')->put($ruta, $xml);

        $hash = substr(sha1($xml), 0, 40);

        if (! $this->config->certificadoExiste()) {
            return ResultadoEmision::generado(
                'XML UBL 2.1 generado. Falta el certificado .pem para firmar y enviar a SUNAT (instala Greenter).',
                xmlPath: $ruta,
                hash: $hash,
            );
        }

        // TODO (produccion): firmar el XML y enviarlo a SUNAT con Greenter,
        // luego devolver ResultadoEmision::aceptado(...) con el CDR.
        return ResultadoEmision::generado(
            'XML UBL 2.1 generado y listo para firmar/enviar a SUNAT ('.$this->config->labelEntorno().').',
            xmlPath: $ruta,
            hash: $hash,
        );
    }

    public function probarConexion(): ResultadoConexion
    {
        $checks = [
            ['ok' => filled($this->config->ruc), 'texto' => 'RUC del emisor configurado'],
            ['ok' => filled($this->config->razon_social), 'texto' => 'Razon social configurada'],
            ['ok' => filled($this->config->sol_usuario), 'texto' => 'Usuario Clave SOL configurado'],
            ['ok' => filled($this->config->sol_clave), 'texto' => 'Clave SOL configurada'],
            ['ok' => $this->config->certificadoExiste(), 'texto' => 'Certificado .pem encontrado'],
            ['ok' => class_exists(\Greenter\See::class), 'texto' => 'Libreria Greenter instalada (envio real)'],
        ];

        $criticos = array_slice($checks, 0, 4);
        $configOk = ! in_array(false, array_column($criticos, 'ok'), true);

        if (! $configOk) {
            return ResultadoConexion::error(
                'Configuracion incompleta. Completa RUC, razon social y credenciales Clave SOL.',
                $checks
            );
        }

        if (! $this->config->certificadoExiste()) {
            return ResultadoConexion::error(
                'Datos correctos, pero no se encontro el certificado .pem en la ruta indicada.',
                $checks
            );
        }

        if (! class_exists(\Greenter\See::class)) {
            return ResultadoConexion::ok(
                'Configuracion valida. Instala Greenter (composer require greenter/lite) para el envio real a SUNAT.',
                $checks
            );
        }

        return ResultadoConexion::ok(
            'Configuracion completa. Listo para emitir en entorno '.$this->config->labelEntorno().'.',
            $checks
        );
    }

    public function nombre(): string
    {
        return 'Beta (SUNAT homologacion)';
    }

    /** @return array<int, string> */
    private function camposFaltantes(): array
    {
        $faltantes = [];
        if (blank($this->config->ruc)) {
            $faltantes[] = 'RUC';
        }
        if (blank($this->config->razon_social)) {
            $faltantes[] = 'razon social';
        }
        if (blank($this->config->sol_usuario)) {
            $faltantes[] = 'usuario Clave SOL';
        }
        if (blank($this->config->sol_clave)) {
            $faltantes[] = 'clave SOL';
        }

        return $faltantes;
    }
}
