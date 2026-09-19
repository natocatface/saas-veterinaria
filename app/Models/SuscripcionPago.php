<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuscripcionPago extends Model
{
    use HasFactory;

    protected $table = 'suscripcion_pagos';

    protected $fillable = [
        'empresa_id', 'plan_id', 'monto', 'periodo', 'metodo',
        'referencia', 'fecha_pago', 'fecha_inicio', 'fecha_fin', 'estado', 'notas',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'fecha_pago' => 'date',
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
