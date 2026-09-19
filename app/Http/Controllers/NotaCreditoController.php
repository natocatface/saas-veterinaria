<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use App\Models\FacturacionConfig;
use App\Models\NotaCredito;
use App\Models\Producto;
use App\Services\Facturacion\FacturacionManager;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NotaCreditoController extends Controller
{
    /** Crea y emite una nota de credito para el comprobante afectado. */
    public function store(Request $request, Comprobante $comprobante)
    {
        $data = $request->validate([
            'cod_motivo' => ['required', 'string', 'in:'.implode(',', array_keys(NotaCredito::motivos()))],
            'motivo' => ['nullable', 'string', 'max:200'],
        ]);

        if ($comprobante->tipo === 'ticket') {
            return back()->with('error', 'Los tickets internos no admiten nota de credito.');
        }
        if ($comprobante->sunat_estado !== 'aceptado') {
            return back()->with('error', 'Solo se puede emitir una nota de credito sobre un comprobante aceptado por SUNAT.');
        }

        $manager = new FacturacionManager();
        if (! $manager->config()->estaHabilitada()) {
            return back()->with('error', 'Facturacion electronica deshabilitada.');
        }

        $config = FacturacionConfig::actual();
        $serie = $config->serie_nota_credito ?: 'FC01';
        $motivoTexto = $data['motivo'] ?: (NotaCredito::motivos()[$data['cod_motivo']] ?? 'Anulacion de la operacion');

        $nota = DB::transaction(function () use ($comprobante, $data, $serie, $motivoTexto, $request) {
            $numero = (int) NotaCredito::where('serie', $serie)->max('numero') + 1;

            return NotaCredito::create([
                'comprobante_id' => $comprobante->id,
                'user_id' => $request->user()->id,
                'serie' => $serie,
                'numero' => $numero,
                'fecha' => Carbon::now(),
                'cod_motivo' => $data['cod_motivo'],
                'motivo' => $motivoTexto,
                'subtotal' => $comprobante->subtotal,
                'igv' => $comprobante->igv,
                'total' => $comprobante->total,
            ]);
        });

        $resultado = $manager->emitirNota($nota);

        // Si la nota fue aceptada y el motivo es anulacion, marca el comprobante y repone stock.
        if ($resultado->ok && in_array($data['cod_motivo'], ['01', '02'], true) && $comprobante->estado !== 'anulado') {
            DB::transaction(function () use ($comprobante) {
                foreach ($comprobante->items as $item) {
                    if ($item->producto_id) {
                        Producto::where('id', $item->producto_id)->increment('stock', (int) ceil($item->cantidad));
                    }
                }
                $comprobante->update(['estado' => 'anulado']);
            });
        }

        return back()->with(
            $resultado->ok ? 'ok' : 'error',
            'Nota de credito '.$nota->numeroFormateado().' - SUNAT: '.$resultado->mensaje
        );
    }

    public function descargarXml(NotaCredito $nota)
    {
        abort_unless($nota->sunat_xml_path && Storage::disk('local')->exists($nota->sunat_xml_path), 404);

        return Storage::disk('local')->download($nota->sunat_xml_path, $nota->numeroFormateado().'.xml');
    }

    public function descargarCdr(NotaCredito $nota)
    {
        abort_unless($nota->sunat_cdr_path && Storage::disk('local')->exists($nota->sunat_cdr_path), 404);

        return Storage::disk('local')->download($nota->sunat_cdr_path, 'CDR-'.$nota->numeroFormateado().'.zip');
    }
}
