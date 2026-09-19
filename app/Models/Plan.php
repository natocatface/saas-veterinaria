<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'planes';

    protected $fillable = [
        'nombre', 'precio', 'periodo', 'max_usuarios', 'max_pacientes', 'caracteristicas', 'activo',
    ];

    protected function casts(): array
    {
        return ['precio' => 'decimal:2', 'activo' => 'boolean'];
    }

    public function empresas()
    {
        return $this->hasMany(Empresa::class);
    }

    public function limiteUsuarios(): string
    {
        return $this->max_usuarios > 0 ? (string) $this->max_usuarios : 'Ilimitado';
    }

    public function limitePacientes(): string
    {
        return $this->max_pacientes > 0 ? (string) $this->max_pacientes : 'Ilimitado';
    }
}
