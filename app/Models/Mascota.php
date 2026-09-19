<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

class Mascota extends Model
{
    use HasFactory, BelongsToEmpresa, Auditable;

    protected $fillable = [
        'empresa_id',
        'cliente_id', 'nombre', 'especie', 'raza', 'sexo', 'color',
        'fecha_nacimiento', 'peso', 'esterilizado', 'notas', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'peso' => 'decimal:2',
            'esterilizado' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    public function consultas()
    {
        return $this->hasMany(Consulta::class);
    }

    public function vacunas()
    {
        return $this->hasMany(Vacuna::class);
    }
}
