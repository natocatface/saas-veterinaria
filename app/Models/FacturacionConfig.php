<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

/**
 * Configuracion de Facturacion Electronica (SUNAT - Peru).
 * Singleton por empresa: usar FacturacionConfig::actual().
 */
class FacturacionConfig extends Model
{
    use BelongsToEmpresa;

    protected $table = 'facturacion_configs';

    protected $fillable = [
        'empresa_id',
        'habilitada', 'emitir_automatico', 'driver', 'entorno',
        'ruc', 'razon_social', 'nombre_comercial', 'direccion_fiscal',
        'ubigeo', 'departamento', 'provincia', 'distrito',
        'sol_usuario', 'sol_clave', 'certificado_path',
        'serie_boleta', 'serie_factura', 'serie_nota_credito',
    ];

    protected function casts(): array
    {
        return [
            'habilitada' => 'boolean',
            'emitir_automatico' => 'boolean',
            'sol_clave' => 'encrypted',
        ];
    }

    /**
     * Devuelve (o crea) la configuracion de la empresa actual con valores
     * demo de homologacion SUNAT (beta): RUC 20000000001 / MODDATOS / MODDATOS.
     */
    public static function actual(): self
    {
        return static::first() ?? static::create([
            'habilitada' => false,
            'emitir_automatico' => true,
            'driver' => 'ninguno',
            'entorno' => 'beta',
            'ruc' => '20000000001',
            'razon_social' => 'EMPRESA DEMO S.A.C.',
            'nombre_comercial' => 'Mi Veterinaria',
            'direccion_fiscal' => 'Av. Principal 123',
            'ubigeo' => '150101',
            'departamento' => 'LIMA',
            'provincia' => 'LIMA',
            'distrito' => 'LIMA',
            'sol_usuario' => 'MODDATOS',
            'sol_clave' => 'MODDATOS',
            'serie_boleta' => 'B001',
            'serie_factura' => 'F001',
            'serie_nota_credito' => 'FC01',
        ]);
    }

    /** Ruta absoluta del certificado .pem (usa la ruta por defecto si esta vacia). */
    public function rutaCertificado(): string
    {
        return $this->certificado_path
            ?: storage_path('app/facturacion/pe/certificate.pem');
    }

    public function certificadoExiste(): bool
    {
        $ruta = $this->rutaCertificado();

        return $ruta !== '' && is_file($ruta);
    }

    public function estaHabilitada(): bool
    {
        return (bool) $this->habilitada;
    }

    public function labelEntorno(): string
    {
        return $this->entorno === 'produccion'
            ? 'Produccion'
            : 'Beta (homologacion / pruebas)';
    }

    public function labelDriver(): string
    {
        return match ($this->driver) {
            'greenter' => 'Greenter',
            'beta' => 'Beta',
            default => 'null',
        };
    }
}
