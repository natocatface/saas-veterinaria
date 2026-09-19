<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

class Vacuna extends Model
{
    use HasFactory, BelongsToEmpresa, Auditable;

    protected $fillable = [
        'empresa_id',
        'mascota_id', 'user_id', 'nombre', 'fecha_aplicacion',
        'proxima_dosis', 'lote', 'notas',
    ];

    protected function casts(): array
    {
        return [
            'fecha_aplicacion' => 'date',
            'proxima_dosis' => 'date',
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
