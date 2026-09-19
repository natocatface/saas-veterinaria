<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    use HasFactory, BelongsToEmpresa, Auditable;

    protected $fillable = [
        'empresa_id',
        'cliente_id', 'user_id', 'tipo', 'serie', 'numero', 'fecha',
        'subtotal', 'igv', 'total', 'metodo_pago', 'estado', 'notas',
        'sunat_estado', 'sunat_mensaje', 'sunat_hash', 'sunat_ticket',
        'sunat_xml_path', 'sunat_cdr_path', 'resumen_id',
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

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(ComprobanteItem::class);
    }

    public function notasCredito()
    {
        return $this->hasMany(NotaCredito::class);
    }

    public function comunicacionesBaja()
    {
        return $this->hasMany(ComunicacionBaja::class);
    }

    public function numeroFormateado(): string
    {
        return $this->serie.'-'.str_pad((string) $this->numero, 6, '0', STR_PAD_LEFT);
    }

    public function tipoLabel(): string
    {
        return match ($this->tipo) {
            'factura' => 'Factura',
            'boleta' => 'Boleta',
            default => 'Ticket',
        };
    }
}
