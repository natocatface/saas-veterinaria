<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'ruc', 'email', 'telefono', 'direccion',
        'plan_id', 'estado', 'fecha_inicio', 'fecha_vencimiento',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_vencimiento' => 'date',
        ];
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }

    public function pagos()
    {
        return $this->hasMany(SuscripcionPago::class)->latest('fecha_pago');
    }

    public function estaActiva(): bool
    {
        return in_array($this->estado, ['activa', 'prueba'], true);
    }

    public function estadoLabel(): string
    {
        return match ($this->estado) {
            'activa' => 'Activa',
            'prueba' => 'En prueba',
            'suspendida' => 'Suspendida',
            default => ucfirst($this->estado),
        };
    }

    public function estaVencida(): bool
    {
        return $this->fecha_vencimiento && $this->fecha_vencimiento->lt(Carbon::today());
    }

    public function diasParaVencer(): ?int
    {
        if (! $this->fecha_vencimiento) {
            return null;
        }
        return (int) Carbon::today()->diffInDays($this->fecha_vencimiento, false);
    }

    public function puedeAgregarUsuario(): bool
    {
        $max = (int) optional($this->plan)->max_usuarios;
        if ($max <= 0) {
            return true;
        }
        return $this->usuarios()->count() < $max;
    }

    public function puedeAgregarPaciente(): bool
    {
        $max = (int) optional($this->plan)->max_pacientes;
        if ($max <= 0) {
            return true;
        }
        return Mascota::withoutGlobalScope('empresa')->where('empresa_id', $this->id)->count() < $max;
    }
}
