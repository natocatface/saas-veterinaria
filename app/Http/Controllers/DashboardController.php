<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Comprobante;
use App\Models\FacturacionConfig;
use App\Models\Mascota;
use App\Models\Producto;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();

        // ----- KPIs -----
        $citasHoy = Cita::whereDate('fecha', $hoy)->count();
        $totalPacientes = Mascota::where('activo', true)->count();
        $pacientesMes = Mascota::where('created_at', '>=', $inicioMes)->count();
        $totalClientes = Cliente::count();
        $totalProductos = Producto::where('activo', true)->count();
        $stockBajo = Producto::whereColumn('stock', '<=', 'stock_minimo')->count();
        $ingresosMes = (float) Comprobante::where('estado', 'emitido')->where('fecha', '>=', $inicioMes)->sum('total');

        // ----- Grafico 1: Ingresos ultimos 6 meses -----
        $mesesLabels = [];
        $ingresosMeses = [];
        for ($i = 5; $i >= 0; $i--) {
            $ref = Carbon::now()->subMonths($i);
            $mesesLabels[] = ucfirst($ref->isoFormat('MMM'));
            $ingresosMeses[] = (float) Comprobante::where('estado', 'emitido')
                ->whereYear('fecha', $ref->year)->whereMonth('fecha', $ref->month)->sum('total');
        }

        // ----- Grafico 2: Actividad de citas ultimos 7 dias -----
        $diasLabels = [];
        $citasSerie = [];
        for ($i = 6; $i >= 0; $i--) {
            $dia = Carbon::today()->subDays($i);
            $diasLabels[] = ucfirst($dia->isoFormat('ddd'));
            $citasSerie[] = Cita::whereDate('fecha', $dia)->count();
        }

        // ----- Grafico 3: Pacientes por especie -----
        $especies = Mascota::select('especie', DB::raw('count(*) as total'))
            ->groupBy('especie')->orderByDesc('total')->pluck('total', 'especie')->toArray();

        // ----- Grafico 4: Citas por estado -----
        $citasEstado = Cita::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')->pluck('total', 'estado')->toArray();

        // ----- Facturacion electronica (estado SUNAT del mes) -----
        $feStats = ['disponible' => false, 'habilitada' => false, 'aceptados' => 0, 'pendientes' => 0, 'rechazados' => 0];
        if (Schema::hasColumn('comprobantes', 'sunat_estado')) {
            $feStats['disponible'] = true;
            $feStats['habilitada'] = (bool) optional(FacturacionConfig::first())->habilitada;
            $baseFe = fn () => Comprobante::where('tipo', '!=', 'ticket')->where('fecha', '>=', $inicioMes);
            $feStats['aceptados'] = $baseFe()->where('sunat_estado', 'aceptado')->count();
            $feStats['rechazados'] = $baseFe()->where('sunat_estado', 'rechazado')->count();
            $feStats['pendientes'] = $baseFe()->where(function ($q) {
                $q->whereNull('sunat_estado')->orWhereIn('sunat_estado', ['pendiente', 'generado']);
            })->count();
        }

        // ----- Proximas citas -----
        $proximasCitas = Cita::with('mascota.cliente', 'veterinario')
            ->where('fecha', '>=', Carbon::now())->orderBy('fecha')->limit(5)->get();

        return view('dashboard.index', compact(
            'citasHoy', 'totalPacientes', 'pacientesMes', 'totalClientes',
            'totalProductos', 'stockBajo', 'ingresosMes',
            'mesesLabels', 'ingresosMeses', 'diasLabels', 'citasSerie',
            'especies', 'citasEstado', 'proximasCitas', 'feStats'
        ));
    }
}
