<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

/**
 * Resumen diario de boletas (SUNAT tipo RC).
 */
class ResumenDiario extends Model
{
    use BelongsToEmpresa, Auditable;

    protected $table = 'resumenes_diarios';

    protected $fillable = [
        'empresa_id', 'user_id',
        'fecha_referencia', 'fecha_generacion', 'correlativo', 'cantidad', 'total',
        'sunat_estado', 'sunat_ticket', 'sunat_mensaje', 'sunat_hash',
        'sunat_xml_path', 'sunat_cdr_path',
    ];

    protected function casts(): array
    {
        return [
            'fecha_referencia' => 'date',
            'fecha_generacion' => 'date',
            'total' => 'decimal:2',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comprobantes()
    {
        return $this->hasMany(Comprobante::class, 'resumen_id');
    }

    public function identificador(): string
    {
        return 'RC-'.$this->fecha_generacion->format('Ymd').'-'.$this->correlativo;
    }
}
