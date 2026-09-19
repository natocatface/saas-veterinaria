<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Mascota;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEmpresas = Empresa::count();
        $activas = Empresa::where('estado', 'activa')->count();
        $prueba = Empresa::where('estado', 'prueba')->count();
        $suspendidas = Empresa::where('estado', 'suspendida')->count();

        // MRR: suma del precio del plan de las empresas activas (normalizado a mensual)
        $mrr = (float) Empresa::where('estado', 'activa')
            ->join('planes', 'empresas.plan_id', '=', 'planes.id')
            ->selectRaw("SUM(CASE WHEN planes.periodo = 'anual' THEN planes.precio/12 ELSE planes.precio END) as mrr")
            ->value('mrr');

        $totalUsuarios = User::where('es_super_admin', false)->count();
        $totalPacientes = Mascota::count();
        $totalPlanes = Plan::where('activo', true)->count();

        // Empresas por plan
        $porPlan = Empresa::query()
            ->leftJoin('planes', 'empresas.plan_id', '=', 'planes.id')
            ->selectRaw("COALESCE(planes.nombre, 'Sin plan') as plan, count(*) as total")
            ->groupBy('plan')
            ->pluck('total', 'plan')
            ->toArray();

        $empresasRecientes = Empresa::with('plan')->withCount('usuarios')->latest()->limit(8)->get();

        return view('admin.dashboard', compact(
            'totalEmpresas', 'activas', 'prueba', 'suspendidas', 'mrr',
            'totalUsuarios', 'totalPacientes', 'totalPlanes', 'porPlan', 'empresasRecientes'
        ));
    }
}
