<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PortalAuthController extends Controller
{
    public function showLogin()
    {
        return view('portal.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $cliente = \App\Models\Cliente::withoutGlobalScope('empresa')
            ->where('email', $credentials['email'])->first();

        if (! $cliente || ! $cliente->acceso_portal || ! $cliente->activo
            || ! $cliente->password || ! \Illuminate\Support\Facades\Hash::check($credentials['password'], $cliente->password)) {
            throw ValidationException::withMessages(['email' => 'Credenciales incorrectas o acceso no habilitado.']);
        }

        Auth::guard('cliente')->login($cliente, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->route('portal.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('cliente')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.login');
    }
}
