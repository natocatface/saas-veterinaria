<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory, BelongsToEmpresa, Auditable;

    protected $fillable = [
        'empresa_id',
        'nombre', 'categoria', 'sku', 'stock', 'stock_minimo',
        'precio', 'costo', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'costo' => 'decimal:2',
            'activo' => 'boolean',
        ];
    }
}
