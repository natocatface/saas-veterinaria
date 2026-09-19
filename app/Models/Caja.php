<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory, BelongsToEmpresa;

    protected $table = 'cajas';

    protected $fillable = [
        'empresa_id',
        'user_id', 'fecha_apertura', 'monto_apertura',
        'fecha_cierre', 'monto_cierre', 'estado', 'notas',
    ];

    protected function casts(): array
    {
        return [
            'fecha_apertura' => 'datetime',
            'fecha_cierre' => 'datetime',
            'monto_apertura' => 'decimal:2',
            'monto_cierre' => 'decimal:2',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoCaja::class);
    }

    public function totalIngresos(): float
    {
        return (float) $this->movimientos()->where('tipo', 'ingreso')->sum('monto');
    }

    public function totalEgresos(): float
    {
        return (float) $this->movimientos()->where('tipo', 'egreso')->sum('monto');
    }

    public function saldoEsperado(): float
    {
        return (float) $this->monto_apertura + $this->totalIngresos() - $this->totalEgresos();
    }
}
