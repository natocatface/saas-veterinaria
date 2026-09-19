<?php

namespace App\Services\Facturacion\Drivers;

use App\Models\Comprobante;
use App\Services\Facturacion\Contracts\FacturacionDriver;
use App\Services\Facturacion\ResultadoConexion;
use App\Services\Facturacion\ResultadoEmision;

/**
 * Driver por defecto: no emite. Deja el comprobante en estado pendiente.
 * Util para operar el POS sin enviar aun a SUNAT.
 */
class NingunoDriver implements FacturacionDriver
{
    public function emitir(Comprobante $comprobante): ResultadoEmision
    {
        return ResultadoEmision::pendiente(
            'Driver "Ninguno": el comprobante se registro pero no se envio a SUNAT.'
        );
    }

    public function probarConexion(): ResultadoConexion
    {
        return ResultadoConexion::error(
            'No hay driver de emision seleccionado. Elige "Beta" para generar comprobantes electronicos.',
            [
                ['ok' => false, 'texto' => 'Driver de emision: Ninguno (no emite)'],
            ]
        );
    }

    public function nombre(): string
    {
        return 'Ninguno';
    }
}
