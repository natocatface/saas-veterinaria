<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    use HasFactory, BelongsToEmpresa, Auditable;

    protected $fillable = [
        'empresa_id',
        'mascota_id', 'user_id', 'fecha', 'motivo', 'sintomas',
        'diagnostico', 'tratamiento', 'peso', 'temperatura', 'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'peso' => 'decimal:2',
            'temperatura' => 'decimal:1',
        ];
    }

    public function mascota()
    {
        return $this->belongsTo(Mascota::class);
    }

    public function veterinario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
