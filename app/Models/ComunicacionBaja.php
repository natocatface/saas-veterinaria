<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

/**
 * Comunicacion de baja (SUNAT tipo RA) de una factura.
 */
class ComunicacionBaja extends Model
{
    use BelongsToEmpresa, Auditable;

    protected $table = 'comunicaciones_baja';

    protected $fillable = [
        'empresa_id', 'comprobante_id', 'user_id',
        'fecha_referencia', 'fecha_generacion', 'correlativo', 'motivo',
        'sunat_estado', 'sunat_ticket', 'sunat_mensaje', 'sunat_hash',
        'sunat_xml_path', 'sunat_cdr_path',
    ];

    protected function casts(): array
    {
        return [
            'fecha_referencia' => 'date',
            'fecha_generacion' => 'date',
        ];
    }

    public function comprobante()
    {
        return $this->belongsTo(Comprobante::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function identificador(): string
    {
        return 'RA-'.$this->fecha_generacion->format('Ymd').'-'.$this->correlativo;
    }
}
