<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Nota de credito electronica (SUNAT tipo 07) sobre un comprobante afectado.
 */
class NotaCredito extends Model
{
    use HasFactory, BelongsToEmpresa, Auditable;

    protected $table = 'notas_credito';

    protected $fillable = [
        'empresa_id', 'comprobante_id', 'user_id',
        'serie', 'numero', 'fecha', 'cod_motivo', 'motivo',
        'subtotal', 'igv', 'total',
        'sunat_estado', 'sunat_mensaje', 'sunat_hash', 'sunat_ticket',
        'sunat_xml_path', 'sunat_cdr_path',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'subtotal' => 'decimal:2',
            'igv' => 'decimal:2',
            'total' => 'decimal:2',
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

    public function numeroFormateado(): string
    {
        return $this->serie.'-'.str_pad((string) $this->numero, 6, '0', STR_PAD_LEFT);
    }

    /** Descripcion legible del motivo (catalogo 09 SUNAT). */
    public static function motivos(): array
    {
        return [
            '01' => 'Anulacion de la operacion',
            '02' => 'Anulacion por error en el RUC',
            '03' => 'Correccion por error en la descripcion',
            '06' => 'Devolucion total',
            '07' => 'Devolucion por item',
        ];
    }

    public function motivoLabel(): string
    {
        return self::motivos()[$this->cod_motivo] ?? $this->motivo ?? 'Anulacion';
    }
}
