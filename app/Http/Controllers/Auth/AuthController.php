<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Ingresa tu correo.',
            'email.email' => 'Correo no valido.',
            'password.required' => 'Ingresa tu contrasena.',
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Las credenciales no coinciden con nuestros registros.',
            ]);
        }

        $user = $request->user();

        if (! $user->activo) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Tu cuenta esta desactivada. Contacta al administrador.',
            ]);
        }

        // Super admin -> panel de plataforma
        if ($user->esSuperAdmin()) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        // Usuario de clinica: validar empresa y suscripcion
        if (! $user->empresa_id) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Tu cuenta no esta asociada a ninguna empresa.',
            ]);
        }

        if ($user->empresa && ! $user->empresa->estaActiva()) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'La suscripcion de tu empresa esta suspendida. Contacta al administrador.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
