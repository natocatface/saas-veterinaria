<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    use BelongsToEmpresa;

    protected $table = 'auditorias';

    protected $fillable = [
        'empresa_id', 'user_id', 'accion', 'modelo', 'modelo_id', 'descripcion', 'ip',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function accionLabel(): string
    {
        return match ($this->accion) {
            'creo' => 'Creo',
            'edito' => 'Edito',
            'elimino' => 'Elimino',
            default => ucfirst($this->accion),
        };
    }
}
