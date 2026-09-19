<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Cliente extends Authenticatable
{
    use HasFactory, Notifiable, BelongsToEmpresa, Auditable;

    protected $fillable = [
        'empresa_id',
        'nombre', 'documento', 'telefono', 'email', 'direccion', 'notas', 'activo',
        'password', 'acceso_portal',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'acceso_portal' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function mascotas()
    {
        return $this->hasMany(Mascota::class);
    }
}
