<?php

namespace App\Services\Facturacion\Contracts;

use App\Models\Comprobante;
use App\Services\Facturacion\ResultadoConexion;
use App\Services\Facturacion\ResultadoEmision;

/**
 * Contrato de un driver de emision electronica.
 * Permite intercambiar la implementacion (ninguno, beta, produccion/Greenter)
 * sin tocar el resto del sistema.
 */
interface FacturacionDriver
{
    /** Emite (o prepara) el comprobante ante SUNAT. */
    public function emitir(Comprobante $comprobante): ResultadoEmision;

    /** Verifica la configuracion y la conexion con SUNAT. */
    public function probarConexion(): ResultadoConexion;

    /** Nombre legible del driver. */
    public function nombre(): string;
}
