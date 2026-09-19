<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;

class SuscripcionController extends Controller
{
    public function bloqueado()
    {
        $empresa = auth()->user()->empresa;

        if ($empresa && $empresa->estaActiva()) {
            return redirect()->route('dashboard');
        }

        return view('suscripcion.bloqueado', compact('empresa'));
    }

    public function index(Request $request)
    {
        $empresa = $request->user()->empresa->load('plan');

        $usuarios = User::where('empresa_id', $empresa->id)->where('es_super_admin', false)->count();
        $pacientes = Mascota::where('empresa_id', $empresa->id)->count();

        return view('suscripcion.index', [
            'empresa' => $empresa,
            'planes' => Plan::where('activo', true)->orderBy('precio')->get(),
            'usoUsuarios' => $usuarios,
            'usoPacientes' => $pacientes,
        ]);
    }

    public function cambiarPlan(Request $request)
    {
        $data = $request->validate([
            'plan_id' => ['required', 'exists:planes,id'],
        ]);

        $empresa = $request->user()->empresa;
        $plan = Plan::findOrFail($data['plan_id']);

        if ((int) $empresa->plan_id === $plan->id) {
            return back()->with('error', 'Ya tienes este plan activo.');
        }

        // Validar que el nuevo plan soporte el consumo actual.
        $usuarios = User::where('empresa_id', $empresa->id)->where('es_super_admin', false)->count();
        $pacientes = Mascota::where('empresa_id', $empresa->id)->count();

        if ($plan->max_usuarios > 0 && $usuarios > $plan->max_usuarios) {
            return back()->with('error', "No puedes cambiar al plan {$plan->nombre}: tienes {$usuarios} usuarios y el plan permite {$plan->max_usuarios}.");
        }
        if ($plan->max_pacientes > 0 && $pacientes > $plan->max_pacientes) {
            return back()->with('error', "No puedes cambiar al plan {$plan->nombre}: tienes {$pacientes} pacientes y el plan permite {$plan->max_pacientes}.");
        }

        $empresa->update(['plan_id' => $plan->id]);

        $mensaje = $plan->precio > 0
            ? "Plan cambiado a {$plan->nombre}. El administrador de la plataforma registrara el cobro correspondiente."
            : "Plan cambiado a {$plan->nombre}.";

        return redirect()->route('suscripcion.index')->with('ok', $mensaje);
    }
}
