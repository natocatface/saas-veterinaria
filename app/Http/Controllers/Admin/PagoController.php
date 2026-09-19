<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\SuscripcionPago;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    public function index()
    {
        $pagos = SuscripcionPago::with('empresa', 'plan')->latest('fecha_pago')->paginate(15);

        $inicioMes = Carbon::now()->startOfMonth();
        $resumen = [
            'total' => (float) SuscripcionPago::sum('monto'),
            'mes' => (float) SuscripcionPago::where('fecha_pago', '>=', $inicioMes)->sum('monto'),
            'cantidad' => SuscripcionPago::count(),
        ];

        return view('admin.suscripciones.index', compact('pagos', 'resumen'));
    }

    public function create(Empresa $empresa)
    {
        $empresa->load('plan');

        return view('admin.suscripciones.pago', compact('empresa'));
    }

    public function store(Request $request, Empresa $empresa)
    {
        $data = $request->validate([
            'monto' => ['required', 'numeric', 'min:0'],
            'periodo' => ['required', 'in:mensual,anual'],
            'metodo' => ['required', 'string', 'max:30'],
            'referencia' => ['nullable', 'string', 'max:60'],
            'fecha_pago' => ['required', 'date'],
            'notas' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data, $empresa) {
            // La nueva vigencia inicia desde el vencimiento vigente (si es futuro) o desde hoy.
            $inicio = ($empresa->fecha_vencimiento && $empresa->fecha_vencimiento->isFuture())
                ? $empresa->fecha_vencimiento->copy()
                : Carbon::today();
            $fin = $data['periodo'] === 'anual' ? $inicio->copy()->addYear() : $inicio->copy()->addMonth();

            SuscripcionPago::create([
                'empresa_id' => $empresa->id,
                'plan_id' => $empresa->plan_id,
                'monto' => $data['monto'],
                'periodo' => $data['periodo'],
                'metodo' => $data['metodo'],
                'referencia' => $data['referencia'] ?? null,
                'fecha_pago' => $data['fecha_pago'],
                'fecha_inicio' => $inicio,
                'fecha_fin' => $fin,
                'estado' => 'pagado',
                'notas' => $data['notas'] ?? null,
            ]);

            $empresa->update([
                'estado' => 'activa',
                'fecha_vencimiento' => $fin,
                'fecha_inicio' => $empresa->fecha_inicio ?? Carbon::today(),
            ]);
        });

        return redirect()->route('admin.empresas.show', $empresa)->with('ok', 'Pago registrado. La suscripcion fue renovada.');
    }
}
