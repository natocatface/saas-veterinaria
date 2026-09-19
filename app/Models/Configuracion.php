<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use BelongsToEmpresa;

    protected $table = 'configuraciones';

    protected $fillable = [
        'empresa_id',
        'nombre_clinica', 'ruc', 'direccion', 'telefono', 'email',
        'moneda', 'igv_porcentaje', 'serie_boleta', 'serie_factura',
    ];

    protected function casts(): array
    {
        return ['igv_porcentaje' => 'decimal:2'];
    }

    public static function actual(): self
    {
        return static::first() ?? static::create([]);
    }
}
