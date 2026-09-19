<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use App\Models\ResumenDiario;
use App\Services\Facturacion\FacturacionManager;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ResumenDiarioController extends Controller
{
    public function index(Request $request)
    {
        $fecha = $request->get('fecha', Carbon::yesterday()->toDateString());

        $boletasPendientes = $this->boletasPendientes($fecha);

        $resumenes = ResumenDiario::with('usuario')
            ->orderByDesc('fecha_generacion')
            ->orderByDesc('id')
            ->paginate(12);

        return view('resumenes.index', [
            'resumenes' => $resumenes,
            'fecha' => $fecha,
            'boletasPendientes' => $boletasPendientes,
        ]);
    }

    /** Genera y envia el resumen diario de las boletas pendientes de una fecha. */
    public function generar(Request $request)
    {
        $data = $request->validate([
            'fecha' => ['required', 'date'],
        ]);
        $fecha = $data['fecha'];

        $manager = new FacturacionManager();
        if (! $manager->config()->estaHabilitada()) {
            return back()->with('error', 'Facturacion electronica deshabilitada.');
        }

        $boletas = $this->boletasPendientes($fecha)->get();
        if ($boletas->isEmpty()) {
            return back()->with('error', 'No hay boletas pendientes para resumir en esa fecha.');
        }

        $resumen = DB::transaction(function () use ($fecha, $boletas, $request) {
            $correlativo = (int) ResumenDiario::whereDate('fecha_referencia', $fecha)->max('correlativo') + 1;

            return ResumenDiario::create([
                'user_id' => $request->user()->id,
                'fecha_referencia' => $fecha,
                'fecha_generacion' => Carbon::today(),
                'correlativo' => $correlativo,
                'cantidad' => $boletas->count(),
                'total' => $boletas->sum('total'),
            ]);
        });

        $resultado = $manager->enviarResumen($resumen, $boletas);

        if ($resultado->ok) {
            Comprobante::whereIn('id', $boletas->pluck('id'))->update([
                'resumen_id' => $resumen->id,
                'sunat_estado' => 'enviado',
                'sunat_ticket' => $resultado->ticket,
            ]);
        }

        return redirect()->route('resumenes.index', ['fecha' => $fecha])
            ->with($resultado->ok ? 'ok' : 'error', 'Resumen '.$resumen->identificador().' - SUNAT: '.$resultado->mensaje);
    }

    /** Consulta el estado del resumen por su ticket (el efecto lo aplica el Manager). */
    public function consultar(ResumenDiario $resumen)
    {
        $resultado = (new FacturacionManager())->consultarResumen($resumen);

        return back()->with($resultado->ok ? 'ok' : 'error', 'SUNAT: '.$resultado->mensaje);
    }

    public function descargarXml(ResumenDiario $resumen)
    {
        abort_unless($resumen->sunat_xml_path && Storage::disk('local')->exists($resumen->sunat_xml_path), 404);

        return Storage::disk('local')->download($resumen->sunat_xml_path, $resumen->identificador().'.xml');
    }

    public function descargarCdr(ResumenDiario $resumen)
    {
        abort_unless($resumen->sunat_cdr_path && Storage::disk('local')->exists($resumen->sunat_cdr_path), 404);

        return Storage::disk('local')->download($resumen->sunat_cdr_path, 'CDR-'.$resumen->identificador().'.zip');
    }

    /** Boletas emitidas de una fecha que aun no fueron aceptadas ni resumidas. */
    private function boletasPendientes(string $fecha)
    {
        return Comprobante::query()
            ->where('tipo', 'boleta')
            ->whereDate('fecha', $fecha)
            ->where('estado', 'emitido')
            ->whereNull('resumen_id')
            ->where(function ($q) {
                $q->whereNull('sunat_estado')->orWhereIn('sunat_estado', ['pendiente', 'generado']);
            });
    }
}
