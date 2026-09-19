<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // El super admin usa su propio panel, no la interfaz de la clinica.
        if ($user && $user->esSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // Usuario de clinica sin empresa asignada.
        if ($user && ! $user->empresa_id) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Tu cuenta no esta asociada a ninguna empresa.']);
        }

        $empresa = $user?->empresa;

        // Suspension automatica al vencer la suscripcion.
        if ($empresa && $empresa->estado !== 'suspendida' && $empresa->estaVencida()) {
            $empresa->update(['estado' => 'suspendida']);
        }

        // Empresa suspendida / vencida -> pantalla de suscripcion bloqueada.
        if ($empresa && ! $empresa->estaActiva()) {
            return redirect()->route('suscripcion.bloqueado');
        }

        return $next($request);
    }
}
