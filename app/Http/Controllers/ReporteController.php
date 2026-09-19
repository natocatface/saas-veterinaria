<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\ComprobanteItem;
use App\Models\Mascota;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index()
    {
        $inicioMes = Carbon::now()->startOfMonth();

        // ----- KPIs -----
        $ingresosMes = (float) Comprobante::where('estado', 'emitido')->where('fecha', '>=', $inicioMes)->sum('total');
        $ventasMes = Comprobante::where('estado', 'emitido')->where('fecha', '>=', $inicioMes)->count();
        $ticketPromedio = $ventasMes > 0 ? $ingresosMes / $ventasMes : 0;
        $totalPacientes = Mascota::where('activo', true)->count();
        $totalClientes = Cliente::count();

        // ----- Ingresos ultimos 6 meses -----
        $mesesLabels = [];
        $mesesData = [];
        for ($i = 5; $i >= 0; $i--) {
            $ref = Carbon::now()->subMonths($i);
            $mesesLabels[] = ucfirst($ref->isoFormat('MMM YYYY'));
            $mesesData[] = (float) Comprobante::where('estado', 'emitido')
                ->whereYear('fecha', $ref->year)->whereMonth('fecha', $ref->month)->sum('total');
        }

        // ----- Top productos vendidos (por importe) -----
        $topProductos = ComprobanteItem::query()
            ->select('descripcion', DB::raw('SUM(cantidad) as unidades'), DB::raw('SUM(importe) as total'))
            ->whereHas('comprobante', fn ($q) => $q->where('estado', 'emitido'))
            ->groupBy('descripcion')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        // ----- Ingresos por metodo de pago -----
        $porMetodo = Comprobante::query()
            ->select('metodo_pago', DB::raw('SUM(total) as total'))
            ->where('estado', 'emitido')
            ->groupBy('metodo_pago')
            ->pluck('total', 'metodo_pago')
            ->toArray();

        // ----- Citas por estado -----
        $citasEstado = Cita::query()
            ->select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        // ----- Pacientes por especie -----
        $especies = Mascota::select('especie', DB::raw('count(*) as total'))
            ->groupBy('especie')->orderByDesc('total')->pluck('total', 'especie')->toArray();

        // ----- Top clientes por facturacion -----
        $topClientes = Comprobante::query()
            ->select('cliente_id', DB::raw('SUM(total) as total'), DB::raw('count(*) as compras'))
            ->where('estado', 'emitido')
            ->whereNotNull('cliente_id')
            ->groupBy('cliente_id')
            ->orderByDesc('total')
            ->with('cliente')
            ->limit(5)
            ->get();

        return view('reportes.index', compact(
            'ingresosMes', 'ventasMes', 'ticketPromedio', 'totalPacientes', 'totalClientes',
            'mesesLabels', 'mesesData', 'topProductos', 'porMetodo', 'citasEstado', 'especies', 'topClientes'
        ));
    }

    /** Reporte de comprobantes electronicos y su estado ante SUNAT. */
    public function facturacionElectronica(Request $request)
    {
        $desde = $request->get('desde');
        $hasta = $request->get('hasta');
        $sunat = $request->get('sunat');
        $tipo = $request->get('tipo');

        $pendiente = fn ($q) => $q->where(function ($x) {
            $x->whereNull('sunat_estado')->orWhereIn('sunat_estado', ['pendiente', 'generado']);
        });

        $base = fn () => Comprobante::query()
            ->where('tipo', '!=', 'ticket')
            ->when($desde, fn ($q) => $q->whereDate('fecha', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('fecha', '<=', $hasta));

        $resumen = [
            'total' => $base()->count(),
            'aceptados' => $base()->where('sunat_estado', 'aceptado')->count(),
            'rechazados' => $base()->where('sunat_estado', 'rechazado')->count(),
            'pendientes' => $base()->where($pendiente)->count(),
            'monto_aceptado' => (float) $base()->where('sunat_estado', 'aceptado')->sum('total'),
            'monto_total' => (float) $base()->where('estado', 'emitido')->sum('total'),
        ];

        $porTipo = $base()
            ->select('tipo', DB::raw('count(*) as cantidad'), DB::raw('SUM(total) as total'))
            ->groupBy('tipo')
            ->get();

        $comprobantes = $base()
            ->when($tipo, fn ($q) => $q->where('tipo', $tipo))
            ->when($sunat === 'pendiente', $pendiente)
            ->when($sunat && $sunat !== 'pendiente', fn ($q) => $q->where('sunat_estado', $sunat))
            ->with('cliente')
            ->orderByDesc('fecha')
            ->paginate(15)
            ->withQueryString();

        return view('reportes.facturacion', compact(
            'resumen', 'porTipo', 'comprobantes', 'desde', 'hasta', 'sunat', 'tipo'
        ));
    }
}
