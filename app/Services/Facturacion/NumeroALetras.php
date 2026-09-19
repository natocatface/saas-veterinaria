<?php

namespace App\Services\Facturacion;

/**
 * Convierte un monto a su representacion en letras para la leyenda 1000 de SUNAT.
 * Ej: 118.00 => "CIENTO DIECIOCHO CON 00/100 SOLES".
 */
class NumeroALetras
{
    private const UNIDADES = [
        '', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE',
        'DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISEIS', 'DIECISIETE',
        'DIECIOCHO', 'DIECINUEVE', 'VEINTE',
    ];

    private const DECENAS = [
        '', '', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA',
        'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA',
    ];

    private const CENTENAS = [
        '', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS',
        'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS',
    ];

    public static function convertir(float $monto, string $moneda = 'SOLES'): string
    {
        $entero = (int) floor($monto);
        $decimos = (int) round(($monto - $entero) * 100);

        $letras = $entero === 0 ? 'CERO' : self::enteroALetras($entero);

        return trim($letras).' CON '.str_pad((string) $decimos, 2, '0', STR_PAD_LEFT).'/100 '.$moneda;
    }

    private static function enteroALetras(int $n): string
    {
        if ($n === 0) {
            return '';
        }
        if ($n < 0) {
            return 'MENOS '.self::enteroALetras(-$n);
        }

        if ($n >= 1000000) {
            $millones = intdiv($n, 1000000);
            $resto = $n % 1000000;
            $pref = $millones === 1 ? 'UN MILLON' : self::enteroALetras($millones).' MILLONES';

            return trim($pref.' '.self::enteroALetras($resto));
        }

        if ($n >= 1000) {
            $miles = intdiv($n, 1000);
            $resto = $n % 1000;
            $pref = $miles === 1 ? 'MIL' : self::enteroALetras($miles).' MIL';

            return trim($pref.' '.self::enteroALetras($resto));
        }

        if ($n >= 100) {
            $centena = intdiv($n, 100);
            $resto = $n % 100;
            if ($n === 100) {
                return 'CIEN';
            }

            return trim(self::CENTENAS[$centena].' '.self::enteroALetras($resto));
        }

        if ($n <= 20) {
            return self::UNIDADES[$n];
        }

        $decena = intdiv($n, 10);
        $unidad = $n % 10;

        // 21..29 => VEINTIUNO .. VEINTINUEVE (el 20 se resuelve arriba)
        if ($n < 30) {
            return 'VEINTI'.self::UNIDADES[$unidad];
        }

        return $unidad === 0
            ? self::DECENAS[$decena]
            : self::DECENAS[$decena].' Y '.self::UNIDADES[$unidad];
    }
}
