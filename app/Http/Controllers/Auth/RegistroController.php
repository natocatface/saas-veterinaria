<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegistroController extends Controller
{
    private int $diasPrueba = 15;

    public function show()
    {
        return view('auth.registro', [
            'planes' => Plan::where('activo', true)->orderBy('precio')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'empresa' => ['required', 'string', 'max:150'],
            'ruc' => ['nullable', 'string', 'max:20'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'plan_id' => ['required', 'exists:planes,id'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [], [
            'empresa' => 'nombre de la clinica',
            'name' => 'nombre',
        ]);

        $user = DB::transaction(function () use ($data) {
            $empresa = Empresa::create([
                'nombre' => $data['empresa'],
                'ruc' => $data['ruc'] ?? null,
                'telefono' => $data['telefono'] ?? null,
                'plan_id' => $data['plan_id'],
                'estado' => 'prueba',
                'fecha_inicio' => Carbon::today(),
                'fecha_vencimiento' => Carbon::today()->addDays($this->diasPrueba),
            ]);

            return User::create([
                'empresa_id' => $empresa->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'rol' => 'admin',
                'cargo' => 'Administrador',
                'activo' => true,
            ]);
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('ok', 'Bienvenido a VetSystem. Tu periodo de prueba de '.$this->diasPrueba.' dias ha comenzado.');
    }
}
