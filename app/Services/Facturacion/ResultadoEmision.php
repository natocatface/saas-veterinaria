<?php

namespace App\Services\Facturacion;

/**
 * Resultado de un intento de emision de comprobante ante SUNAT.
 */
class ResultadoEmision
{
    public function __construct(
        public bool $ok,
        public string $estado,        // pendiente | generado | aceptado | rechazado
        public string $mensaje,
        public ?string $xmlPath = null,
        public ?string $cdrPath = null,
        public ?string $hash = null,
        public ?string $ticket = null,
    ) {
    }

    public static function pendiente(string $mensaje): self
    {
        return new self(false, 'pendiente', $mensaje);
    }

    public static function generado(string $mensaje, ?string $xmlPath = null, ?string $hash = null): self
    {
        return new self(true, 'generado', $mensaje, xmlPath: $xmlPath, hash: $hash);
    }

    public static function aceptado(string $mensaje, ?string $xmlPath = null, ?string $cdrPath = null, ?string $hash = null): self
    {
        return new self(true, 'aceptado', $mensaje, xmlPath: $xmlPath, cdrPath: $cdrPath, hash: $hash);
    }

    public static function rechazado(string $mensaje): self
    {
        return new self(false, 'rechazado', $mensaje);
    }

    /** Datos para actualizar el comprobante emitido. */
    public function paraComprobante(): array
    {
        return [
            'sunat_estado' => $this->estado,
            'sunat_mensaje' => mb_substr($this->mensaje, 0, 250),
            'sunat_hash' => $this->hash,
            'sunat_ticket' => $this->ticket,
            'sunat_xml_path' => $this->xmlPath,
            'sunat_cdr_path' => $this->cdrPath,
        ];
    }
}
