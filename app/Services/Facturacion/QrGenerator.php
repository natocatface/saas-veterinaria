<?php

namespace App\Services\Facturacion;

/**
 * Genera el codigo QR del comprobante como data URI PNG para incrustarlo en el
 * PDF (dompdf). Prueba las librerias QR mas comunes y, si ninguna esta
 * instalada, devuelve null (el PDF muestra el texto del QR como respaldo).
 *
 * Instala una con:
 *   composer require endroid/qr-code        (usa GD)
 *   composer require simplesoftwareio/simple-qrcode  (usa Imagick)
 */
class QrGenerator
{
    public static function dataUri(string $texto, int $size = 150): ?string
    {
        return self::viaSimpleQrCode($texto, $size)
            ?? self::viaEndroid($texto, $size)
            ?? self::viaChillerlan($texto, $size);
    }

    private static function viaSimpleQrCode(string $texto, int $size): ?string
    {
        $facade = \SimpleSoftwareIO\QrCode\Facades\QrCode::class;
        if (! class_exists($facade)) {
            return null;
        }

        try {
            // PNG requiere la extension Imagick.
            $png = (string) $facade::format('png')->size($size)->margin(1)->errorCorrection('M')->generate($texto);

            return $png !== '' ? 'data:image/png;base64,'.base64_encode($png) : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private static function viaEndroid(string $texto, int $size): ?string
    {
        if (! class_exists(\Endroid\QrCode\Writer\PngWriter::class)) {
            return null;
        }

        try {
            $writer = new \Endroid\QrCode\Writer\PngWriter();
            $qr = \Endroid\QrCode\QrCode::create($texto)->setSize($size)->setMargin(6);

            return $writer->write($qr)->getDataUri();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private static function viaChillerlan(string $texto, int $size): ?string
    {
        if (! class_exists(\chillerlan\QRCode\QRCode::class)) {
            return null;
        }

        try {
            $options = new \chillerlan\QRCode\QROptions([
                'outputType' => \chillerlan\QRCode\Output\QROutputInterface::GDIMAGE_PNG,
                'eccLevel' => \chillerlan\QRCode\Common\EccLevel::M,
                'scale' => max(3, (int) round($size / 33)),
            ]);

            // render() devuelve el data URI directamente para salidas de imagen.
            return (string) (new \chillerlan\QRCode\QRCode($options))->render($texto);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
