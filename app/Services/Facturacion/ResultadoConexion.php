<?php

namespace App\Services\Facturacion;

/**
 * Resultado de una prueba de conexion / verificacion de configuracion con SUNAT.
 */
class ResultadoConexion
{
    /**
     * @param  array<int, array{ok: bool, texto: string}>  $checks
     */
    public function __construct(
        public bool $ok,
        public string $mensaje,
        public array $checks = [],
    ) {
    }

    public static function ok(string $mensaje, array $checks = []): self
    {
        return new self(true, $mensaje, $checks);
    }

    public static function error(string $mensaje, array $checks = []): self
    {
        return new self(false, $mensaje, $checks);
    }
}
