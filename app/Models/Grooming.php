<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

class Grooming extends Model
{
    use HasFactory, BelongsToEmpresa, Auditable;

    protected $fillable = [
        'empresa_id',
        'mascota_id', 'user_id', 'fecha', 'servicio', 'precio', 'estado', 'notas',
    ];

    protected function casts(): array
    {
        return ['fecha' => 'datetime', 'precio' => 'decimal:2'];
    }

    public function mascota()
    {
        return $this->belongsTo(Mascota::class);
    }

    public function groomer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
