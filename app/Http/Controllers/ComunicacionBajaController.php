<?php

namespace App\Http\Controllers;

use App\Models\ComunicacionBaja;
use App\Models\Comprobante;
use App\Services\Facturacion\FacturacionManager;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ComunicacionBajaController extends Controller
{
    /** Crea y envia la comunicacion de baja (RA) de una factura. */
    public function store(Request $request, Comprobante $comprobante)
    {
        $data = $request->validate([
            'motivo' => ['nullable', 'string', 'max:200'],
        ]);

        if ($comprobante->tipo !== 'factura') {
            return back()->with('error', 'La comunicacion de baja aplica solo a facturas. Para boletas usa el resumen o una nota de credito.');
        }
        if ($comprobante->sunat_estado !== 'aceptado') {
            return back()->with('error', 'Solo se puede dar de baja una factura aceptada por SUNAT.');
        }

        $manager = new FacturacionManager();
        if (! $manager->config()->estaHabilitada()) {
            return back()->with('error', 'Facturacion electronica deshabilitada.');
        }

        $fechaRef = ($comprobante->fecha instanceof Carbon ? $comprobante->fecha : Carbon::parse($comprobante->fecha))->toDateString();
        $motivo = $data['motivo'] ?: 'Error en la operacion';

        $baja = DB::transaction(function () use ($comprobante, $fechaRef, $motivo, $request) {
            $correlativo = (int) ComunicacionBaja::whereDate('fecha_generacion', Carbon::today())->max('correlativo') + 1;

            return ComunicacionBaja::create([
                'comprobante_id' => $comprobante->id,
                'user_id' => $request->user()->id,
                'fecha_referencia' => $fechaRef,
                'fecha_generacion' => Carbon::today(),
                'correlativo' => $correlativo,
                'motivo' => $motivo,
            ]);
        });

        $resultado = $manager->comunicarBaja($baja, $comprobante);

        return back()->with(
            $resultado->ok ? 'ok' : 'error',
            'Comunicacion de baja '.$baja->identificador().' - SUNAT: '.$resultado->mensaje
        );
    }

    /** Consulta el estado de la baja por ticket (el Manager anula la factura si es aceptada). */
    public function consultar(ComunicacionBaja $baja)
    {
        $resultado = (new FacturacionManager())->consultarBaja($baja);

        return back()->with($resultado->ok ? 'ok' : 'error', 'SUNAT: '.$resultado->mensaje);
    }

    public function descargarXml(ComunicacionBaja $baja)
    {
        abort_unless($baja->sunat_xml_path && Storage::disk('local')->exists($baja->sunat_xml_path), 404);

        return Storage::disk('local')->download($baja->sunat_xml_path, $baja->identificador().'.xml');
    }

    public function descargarCdr(ComunicacionBaja $baja)
    {
        abort_unless($baja->sunat_cdr_path && Storage::disk('local')->exists($baja->sunat_cdr_path), 404);

        return Storage::disk('local')->download($baja->sunat_cdr_path, 'CDR-'.$baja->identificador().'.zip');
    }
}
