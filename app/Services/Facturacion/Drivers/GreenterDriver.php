<?php

namespace App\Services\Facturacion\Drivers;

use App\Models\ComunicacionBaja;
use App\Models\Comprobante;
use App\Models\FacturacionConfig;
use App\Models\NotaCredito;
use App\Models\ResumenDiario;
use App\Services\Facturacion\Contracts\FacturacionDriver;
use App\Services\Facturacion\NumeroALetras;
use App\Services\Facturacion\ResultadoConexion;
use App\Services\Facturacion\ResultadoEmision;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\Note;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Summary\Summary;
use Greenter\Model\Summary\SummaryDetail;
use Greenter\Model\Voided\Voided;
use Greenter\Model\Voided\VoidedDetail;
use Greenter\See;
use Greenter\Ws\Services\SunatEndpoints;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Driver de emision real ante SUNAT usando Greenter (greenter/lite).
 * Firma el XML con el certificado .pem, lo envia al web service (beta o
 * produccion segun el entorno) y procesa el CDR devuelto.
 */
class GreenterDriver implements FacturacionDriver
{
    public function __construct(private FacturacionConfig $config)
    {
    }

    public function nombre(): string
    {
        return 'SUNAT (Greenter)';
    }

    public function emitir(Comprobante $comprobante): ResultadoEmision
    {
        if (! $this->greenterDisponible()) {
            return ResultadoEmision::pendiente('Greenter no esta instalado (composer install).');
        }
        if (! $this->config->certificadoExiste()) {
            return ResultadoEmision::pendiente('No se encontro el certificado .pem para firmar.');
        }
        if ($comprobante->tipo === 'factura' && ! $this->tieneRucCliente($comprobante)) {
            return ResultadoEmision::pendiente('Una factura requiere un cliente con RUC valido.');
        }

        try {
            $documento = $this->construirInvoice($comprobante);
        } catch (\Throwable $e) {
            return ResultadoEmision::rechazado('Error al construir el comprobante: '.$e->getMessage());
        }

        return $this->enviarDocumento($documento, $this->nombreArchivo($comprobante));
    }

    /**
     * Emite una nota de credito (tipo 07) que afecta a un comprobante.
     */
    public function emitirNota(NotaCredito $nota): ResultadoEmision
    {
        if (! $this->greenterDisponible()) {
            return ResultadoEmision::pendiente('Greenter no esta instalado (composer install).');
        }
        if (! $this->config->certificadoExiste()) {
            return ResultadoEmision::pendiente('No se encontro el certificado .pem para firmar.');
        }

        try {
            $documento = $this->construirNota($nota);
        } catch (\Throwable $e) {
            return ResultadoEmision::rechazado('Error al construir la nota de credito: '.$e->getMessage());
        }

        return $this->enviarDocumento($documento, $this->nombreArchivoNota($nota));
    }

    /**
     * Firma, envia a SUNAT y procesa el CDR de cualquier documento Greenter.
     */
    private function enviarDocumento(object $documento, string $nombreBase): ResultadoEmision
    {
        try {
            $see = $this->crearSee();
            $xmlFirmado = $see->getXmlSigned($documento);
            $rutaXml = $this->guardar('xml', $nombreBase.'.xml', (string) $xmlFirmado);
            $result = $see->send($documento);
        } catch (\Throwable $e) {
            return ResultadoEmision::rechazado('Error al emitir: '.$e->getMessage());
        }

        $hash = $this->hashDesdeXml($xmlFirmado);

        if ($result === null || ! $result->isSuccess()) {
            $err = $result?->getError();
            $msg = $err ? ($err->getCode().' - '.$err->getMessage()) : 'SUNAT no devolvio respuesta.';

            return new ResultadoEmision(false, 'rechazado', 'Rechazado por SUNAT: '.$msg, xmlPath: $rutaXml, hash: $hash);
        }

        $rutaCdr = null;
        if (method_exists($result, 'getCdrZip') && $result->getCdrZip()) {
            $rutaCdr = $this->guardar('cdr', 'R-'.$nombreBase.'.zip', $result->getCdrZip());
        }

        $cdr = method_exists($result, 'getCdrResponse') ? $result->getCdrResponse() : null;
        $codigo = $cdr?->getCode();
        $descripcion = $cdr?->getDescription() ?: 'Documento aceptado por SUNAT.';

        // 0 = aceptado; 2xxx = rechazado; 4xxx = aceptado con observaciones.
        if ($codigo !== null && (int) $codigo >= 2000 && (int) $codigo < 4000) {
            return new ResultadoEmision(false, 'rechazado', 'SUNAT '.$codigo.': '.$descripcion, xmlPath: $rutaXml, cdrPath: $rutaCdr, hash: $hash);
        }

        return ResultadoEmision::aceptado(
            'SUNAT '.($codigo ?? '0').': '.$descripcion,
            xmlPath: $rutaXml,
            cdrPath: $rutaCdr,
            hash: $hash,
        );
    }

    /**
     * Envia un resumen diario de boletas (RC). Devuelve el ticket de SUNAT.
     *
     * @param  iterable<Comprobante>  $boletas
     */
    public function enviarResumen(ResumenDiario $resumen, iterable $boletas): ResultadoEmision
    {
        if (! $this->greenterDisponible()) {
            return ResultadoEmision::pendiente('Greenter no esta instalado.');
        }
        if (! $this->config->certificadoExiste()) {
            return ResultadoEmision::pendiente('No se encontro el certificado .pem para firmar.');
        }

        $fecGen = $resumen->fecha_referencia instanceof Carbon ? $resumen->fecha_referencia : Carbon::parse($resumen->fecha_referencia);
        $fecRes = $resumen->fecha_generacion instanceof Carbon ? $resumen->fecha_generacion : Carbon::parse($resumen->fecha_generacion);

        $detalles = [];
        foreach ($boletas as $c) {
            $doc = $c->cliente->documento ?? null;
            $tipoCli = $doc && strlen((string) $doc) === 8 ? '1' : ($doc && strlen((string) $doc) === 11 ? '6' : '0');

            $detalles[] = (new SummaryDetail())
                ->setTipoDoc('03')
                ->setSerieNro($c->serie.'-'.$c->numero)
                ->setEstado('1') // 1 = Adicionar
                ->setClienteTipo($tipoCli)
                ->setClienteNro($doc ?: '0')
                ->setTotal((float) $c->total)
                ->setMtoOperGravadas((float) $c->subtotal)
                ->setMtoIGV((float) $c->igv);
        }

        if ($detalles === []) {
            return ResultadoEmision::pendiente('No hay boletas para resumir en esa fecha.');
        }

        try {
            $summary = (new Summary())
                ->setFecGeneracion($fecGen)
                ->setFecResumen($fecRes)
                ->setCorrelativo(str_pad((string) $resumen->correlativo, 3, '0', STR_PAD_LEFT))
                ->setMoneda('PEN')
                ->setCompany($this->construirCompany())
                ->setDetails($detalles);

            $see = $this->crearSee();
            $xml = $see->getXmlSigned($summary);
            $nombre = $this->config->ruc.'-RC-'.$fecGen->format('Ymd').'-'.$resumen->correlativo;
            $rutaXml = $this->guardar('resumen', $nombre.'.xml', (string) $xml);
            $result = $see->send($summary);
        } catch (\Throwable $e) {
            return ResultadoEmision::rechazado('Error al enviar el resumen: '.$e->getMessage());
        }

        if ($result === null || ! $result->isSuccess()) {
            $err = $result?->getError();
            $msg = $err ? ($err->getCode().' - '.$err->getMessage()) : 'SUNAT no devolvio respuesta.';

            return new ResultadoEmision(false, 'rechazado', 'Rechazado por SUNAT: '.$msg, xmlPath: $rutaXml, hash: $this->hashDesdeXml($xml));
        }

        $ticket = method_exists($result, 'getTicket') ? $result->getTicket() : null;

        return new ResultadoEmision(
            true, 'enviado',
            'Resumen enviado. Ticket '.$ticket.'. Consulta el estado en unos minutos.',
            xmlPath: $rutaXml, hash: $this->hashDesdeXml($xml), ticket: $ticket,
        );
    }

    /**
     * Comunica la baja (RA) de una factura. Devuelve el ticket de SUNAT.
     */
    public function comunicarBaja(ComunicacionBaja $baja, Comprobante $factura): ResultadoEmision
    {
        if (! $this->greenterDisponible()) {
            return ResultadoEmision::pendiente('Greenter no esta instalado.');
        }
        if (! $this->config->certificadoExiste()) {
            return ResultadoEmision::pendiente('No se encontro el certificado .pem para firmar.');
        }

        $fecGen = $baja->fecha_referencia instanceof Carbon ? $baja->fecha_referencia : Carbon::parse($baja->fecha_referencia);
        $fecCom = $baja->fecha_generacion instanceof Carbon ? $baja->fecha_generacion : Carbon::parse($baja->fecha_generacion);

        try {
            $detalle = (new VoidedDetail())
                ->setTipoDoc('01') // Factura
                ->setSerie($factura->serie)
                ->setCorrelativo((string) $factura->numero)
                ->setDesMotivoBaja($baja->motivo ?: 'Error en la operacion');

            $voided = (new Voided())
                ->setCorrelativo((string) $baja->correlativo)
                ->setFecGeneracion($fecGen)
                ->setFecComunicacion($fecCom)
                ->setCompany($this->construirCompany())
                ->setDetails([$detalle]);

            $see = $this->crearSee();
            $xml = $see->getXmlSigned($voided);
            $nombre = $this->config->ruc.'-RA-'.$fecCom->format('Ymd').'-'.$baja->correlativo;
            $rutaXml = $this->guardar('baja', $nombre.'.xml', (string) $xml);
            $result = $see->send($voided);
        } catch (\Throwable $e) {
            return ResultadoEmision::rechazado('Error al comunicar la baja: '.$e->getMessage());
        }

        if ($result === null || ! $result->isSuccess()) {
            $err = $result?->getError();
            $msg = $err ? ($err->getCode().' - '.$err->getMessage()) : 'SUNAT no devolvio respuesta.';

            return new ResultadoEmision(false, 'rechazado', 'Rechazado por SUNAT: '.$msg, xmlPath: $rutaXml, hash: $this->hashDesdeXml($xml));
        }

        $ticket = method_exists($result, 'getTicket') ? $result->getTicket() : null;

        return new ResultadoEmision(
            true, 'enviado',
            'Comunicacion de baja enviada. Ticket '.$ticket.'. Consulta el estado en unos minutos.',
            xmlPath: $rutaXml, hash: $this->hashDesdeXml($xml), ticket: $ticket,
        );
    }

    /** Consulta el estado de un envio asincrono por ticket. */
    public function consultarTicket(string $ticket): ResultadoEmision
    {
        if (! $this->greenterDisponible()) {
            return ResultadoEmision::pendiente('Greenter no esta instalado.');
        }

        try {
            $see = $this->crearSee();
            $status = $see->getStatus($ticket);
        } catch (\Throwable $e) {
            return ResultadoEmision::rechazado('Error al consultar el ticket: '.$e->getMessage());
        }

        if ($status === null) {
            return new ResultadoEmision(false, 'enviado', 'SUNAT no devolvio estado. Reintenta.', ticket: $ticket);
        }

        $code = $status->getCode();

        if ($code === '98') {
            return new ResultadoEmision(false, 'enviado', 'En proceso en SUNAT (codigo 98). Vuelve a consultar en unos minutos.', ticket: $ticket);
        }

        if ($status->isSuccess()) {
            $rutaCdr = null;
            if (method_exists($status, 'getCdrZip') && $status->getCdrZip()) {
                $rutaCdr = $this->guardar('cdr', 'R-ticket-'.$ticket.'.zip', $status->getCdrZip());
            }
            $cdr = method_exists($status, 'getCdrResponse') ? $status->getCdrResponse() : null;
            $desc = $cdr?->getDescription() ?: 'Resumen aceptado por SUNAT.';

            return new ResultadoEmision(true, 'aceptado', 'SUNAT '.($cdr?->getCode() ?? '0').': '.$desc, cdrPath: $rutaCdr, ticket: $ticket);
        }

        $err = $status->getError();
        $cdr = method_exists($status, 'getCdrResponse') ? $status->getCdrResponse() : null;
        $msg = $err
            ? $err->getCode().' - '.$err->getMessage()
            : ($cdr ? $cdr->getCode().': '.$cdr->getDescription() : 'Rechazado (codigo '.$code.')');

        return new ResultadoEmision(false, 'rechazado', 'SUNAT: '.$msg, ticket: $ticket);
    }

    public function probarConexion(): ResultadoConexion
    {
        $checks = [
            ['ok' => $this->greenterDisponible(), 'texto' => 'Libreria Greenter instalada'],
            ['ok' => filled($this->config->ruc), 'texto' => 'RUC del emisor configurado'],
            ['ok' => filled($this->config->sol_usuario), 'texto' => 'Usuario Clave SOL configurado'],
            ['ok' => filled($this->config->sol_clave), 'texto' => 'Clave SOL configurada'],
            ['ok' => $this->config->certificadoExiste(), 'texto' => 'Certificado .pem encontrado'],
        ];

        if (in_array(false, array_column($checks, 'ok'), true)) {
            return ResultadoConexion::error('Configuracion incompleta para emitir con Greenter.', $checks);
        }

        // Verifica que el certificado sea legible (no valida la cadena completa).
        $certOk = @file_get_contents($this->config->rutaCertificado()) !== false;
        $checks[] = ['ok' => $certOk, 'texto' => 'Certificado .pem legible'];

        if (! $certOk) {
            return ResultadoConexion::error('No se pudo leer el certificado .pem.', $checks);
        }

        return ResultadoConexion::ok(
            'Configuracion lista para emitir en '.$this->config->labelEntorno().'. Emite un comprobante de prueba para confirmar con SUNAT.',
            $checks
        );
    }

    // ------------------------------------------------------------------

    private function greenterDisponible(): bool
    {
        return class_exists(See::class);
    }

    private function crearSee(): See
    {
        $see = new See();
        $see->setCertificate(file_get_contents($this->config->rutaCertificado()));
        $see->setService(
            $this->config->entorno === 'produccion'
                ? SunatEndpoints::FE_PRODUCCION
                : SunatEndpoints::FE_BETA
        );
        $see->setClaveSOL(
            $this->config->ruc,
            $this->config->sol_usuario,
            (string) $this->config->sol_clave,
        );

        return $see;
    }

    private function construirCompany(): Company
    {
        $cfg = $this->config;
        $address = (new Address())
            ->setUbigueo($cfg->ubigeo ?: '150101')
            ->setDepartamento($cfg->departamento ?: 'LIMA')
            ->setProvincia($cfg->provincia ?: 'LIMA')
            ->setDistrito($cfg->distrito ?: 'LIMA')
            ->setDireccion($cfg->direccion_fiscal ?: '-');

        return (new Company())
            ->setRuc($cfg->ruc)
            ->setRazonSocial($cfg->razon_social)
            ->setNombreComercial($cfg->nombre_comercial ?: $cfg->razon_social)
            ->setAddress($address);
    }

    /**
     * Mapea los items del comprobante a lineas de Greenter y calcula totales.
     *
     * @return array{detalles: array, gravadas: float, igv: float, total: float}
     */
    private function detallesYtotales(Comprobante $comprobante): array
    {
        $detalles = [];
        $gravadas = 0.0;
        $igvTotal = 0.0;

        foreach ($comprobante->items as $item) {
            $cant = (float) $item->cantidad;
            $valorUnit = round((float) $item->precio_unitario, 6);
            $valorVenta = round($valorUnit * $cant, 2);
            $igvLinea = round($valorVenta * 0.18, 2);
            $precioUnit = round($valorUnit * 1.18, 6);

            $gravadas += $valorVenta;
            $igvTotal += $igvLinea;

            $detalles[] = (new SaleDetail())
                ->setCodProducto((string) ($item->producto_id ?: 'VAR'))
                ->setUnidad('NIU')
                ->setCantidad($cant)
                ->setDescripcion($item->descripcion)
                ->setMtoBaseIgv($valorVenta)
                ->setPorcentajeIgv(18.0)
                ->setIgv($igvLinea)
                ->setTipAfeIgv('10') // Gravado - Operacion Onerosa
                ->setTotalImpuestos($igvLinea)
                ->setMtoValorVenta($valorVenta)
                ->setMtoValorUnitario($valorUnit)
                ->setMtoPrecioUnitario($precioUnit);
        }

        $gravadas = round($gravadas, 2);
        $igvTotal = round($igvTotal, 2);

        return [
            'detalles' => $detalles,
            'gravadas' => $gravadas,
            'igv' => $igvTotal,
            'total' => round($gravadas + $igvTotal, 2),
        ];
    }

    private function construirInvoice(Comprobante $comprobante): Invoice
    {
        $comprobante->loadMissing('items', 'cliente');
        $fecha = $comprobante->fecha instanceof Carbon ? $comprobante->fecha : Carbon::parse($comprobante->fecha);

        $company = $this->construirCompany();
        $client = $this->construirCliente($comprobante);

        $map = $this->detallesYtotales($comprobante);
        $detalles = $map['detalles'];
        $gravadas = $map['gravadas'];
        $igvTotal = $map['igv'];
        $total = $map['total'];

        $legend = (new Legend())
            ->setCode('1000')
            ->setValue(NumeroALetras::convertir($total, 'SOLES'));

        return (new Invoice())
            ->setUblVersion('2.1')
            ->setTipoOperacion('0101') // Venta interna
            ->setTipoDoc($comprobante->tipo === 'factura' ? '01' : '03')
            ->setSerie($comprobante->serie)
            ->setCorrelativo((string) $comprobante->numero)
            ->setFechaEmision($fecha)
            ->setFormaPago(new FormaPagoContado())
            ->setTipoMoneda('PEN')
            ->setCompany($company)
            ->setClient($client)
            ->setMtoOperGravadas($gravadas)
            ->setMtoIGV($igvTotal)
            ->setTotalImpuestos($igvTotal)
            ->setValorVenta($gravadas)
            ->setSubTotal($total)
            ->setMtoImpVenta($total)
            ->setDetails($detalles)
            ->setLegends([$legend]);
    }

    private function construirNota(NotaCredito $nota): Note
    {
        $comprobante = $nota->comprobante;
        $comprobante->loadMissing('items', 'cliente');
        $fecha = $nota->fecha instanceof Carbon ? $nota->fecha : Carbon::parse($nota->fecha);

        $company = $this->construirCompany();
        $client = $this->construirCliente($comprobante);

        $map = $this->detallesYtotales($comprobante);
        $total = $map['total'];

        $legend = (new Legend())
            ->setCode('1000')
            ->setValue(NumeroALetras::convertir($total, 'SOLES'));

        return (new Note())
            ->setUblVersion('2.1')
            ->setTipoDoc('07') // Nota de credito
            ->setSerie($nota->serie)
            ->setCorrelativo((string) $nota->numero)
            ->setFechaEmision($fecha)
            ->setTipDocAfectado($comprobante->tipo === 'factura' ? '01' : '03')
            ->setNumDocfectado($comprobante->serie.'-'.str_pad((string) $comprobante->numero, 6, '0', STR_PAD_LEFT))
            ->setCodMotivo($nota->cod_motivo)
            ->setDesMotivo($nota->motivo ?: $nota->motivoLabel())
            ->setTipoMoneda('PEN')
            ->setCompany($company)
            ->setClient($client)
            ->setMtoOperGravadas($map['gravadas'])
            ->setMtoIGV($map['igv'])
            ->setTotalImpuestos($map['igv'])
            ->setMtoImpVenta($total)
            ->setDetails($map['detalles'])
            ->setLegends([$legend]);
    }

    private function construirCliente(Comprobante $comprobante): Client
    {
        $cliente = $comprobante->cliente;
        $doc = $cliente->documento ?? null;

        if ($comprobante->tipo === 'factura') {
            $tipoDoc = '6';
            $numDoc = $doc ?: '00000000000';
        } elseif ($doc && strlen((string) $doc) === 8) {
            $tipoDoc = '1'; // DNI
            $numDoc = $doc;
        } elseif ($doc && strlen((string) $doc) === 11) {
            $tipoDoc = '6'; // RUC
            $numDoc = $doc;
        } else {
            $tipoDoc = '0'; // Sin documento / varios
            $numDoc = '0';
        }

        return (new Client())
            ->setTipoDoc($tipoDoc)
            ->setNumDoc($numDoc)
            ->setRznSocial($cliente->nombre ?? 'CLIENTE VARIOS');
    }

    private function tieneRucCliente(Comprobante $comprobante): bool
    {
        $doc = $comprobante->cliente->documento ?? null;

        return $doc && strlen((string) $doc) === 11;
    }

    private function nombreArchivo(Comprobante $comprobante): string
    {
        return $this->config->ruc.'-'
            .($comprobante->tipo === 'factura' ? '01' : '03').'-'
            .$comprobante->serie.'-'
            .str_pad((string) $comprobante->numero, 6, '0', STR_PAD_LEFT);
    }

    private function nombreArchivoNota(NotaCredito $nota): string
    {
        return $this->config->ruc.'-07-'
            .$nota->serie.'-'
            .str_pad((string) $nota->numero, 6, '0', STR_PAD_LEFT);
    }

    private function guardar(string $sub, string $nombre, string $contenido): string
    {
        $ruta = 'facturacion/pe/'.$sub.'/'.$nombre;
        Storage::disk('local')->put($ruta, $contenido);

        return $ruta;
    }

    private function hashDesdeXml(?string $xml): ?string
    {
        if (! $xml) {
            return null;
        }
        if (preg_match('/<ds:DigestValue>(.*?)<\/ds:DigestValue>/', $xml, $m)) {
            return $m[1];
        }

        return substr(sha1($xml), 0, 40);
    }
}
