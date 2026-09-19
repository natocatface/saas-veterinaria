<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'empresa_id',
        'name',
        'email',
        'password',
        'rol',
        'es_super_admin',
        'telefono',
        'cargo',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
            'es_super_admin' => 'boolean',
        ];
    }

    /**
     * NOTA: el modelo User NO usa el global scope de empresa a proposito.
     * Aplicarlo provocaria recursion infinita al resolver el usuario autenticado
     * (el scope llama a auth()->user(), que a su vez carga el User). El aislamiento
     * de usuarios por empresa se hace explicitamente en los controladores.
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function esAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    public function esSuperAdmin(): bool
    {
        return (bool) $this->es_super_admin;
    }

    public function iniciales(): string
    {
        $partes = preg_split('/\s+/', trim($this->name));
        $ini = '';
        foreach (array_slice($partes, 0, 2) as $p) {
            $ini .= mb_substr($p, 0, 1);
        }
        return mb_strtoupper($ini ?: 'U');
    }

    public function rolLabel(): string
    {
        if ($this->es_super_admin) {
            return 'Super Admin';
        }

        return match ($this->rol) {
            'admin' => 'Administrador',
            'veterinario' => 'Veterinario',
            'recepcion' => 'Recepcion',
            'groomer' => 'Peluquero',
            default => 'Usuario',
        };
    }
}
