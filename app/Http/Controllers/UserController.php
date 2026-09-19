<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public array $roles = ['admin', 'veterinario', 'recepcion', 'groomer'];

    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $empresaId = $request->user()->empresa_id;

        $usuarios = User::query()
            ->where('empresa_id', $empresaId)
            ->where('es_super_admin', false)
            ->when($q, fn ($query) => $query->where(fn ($sub) => $sub
                ->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('usuarios.index', compact('usuarios', 'q'));
    }

    public function create()
    {
        return view('usuarios.form', ['usuario' => new User(['activo' => true]), 'roles' => $this->roles]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'rol' => ['required', Rule::in($this->roles)],
            'cargo' => ['nullable', 'string', 'max:80'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        $empresa = $request->user()->empresa;
        if ($empresa && ! $empresa->puedeAgregarUsuario()) {
            return back()->withInput()->with('error', 'Alcanzaste el limite de usuarios de tu plan ('.optional($empresa->plan)->limiteUsuarios().'). Mejora tu plan para agregar mas.');
        }

        $data['password'] = Hash::make($data['password']);
        $data['activo'] = $request->boolean('activo', true);
        $data['empresa_id'] = $request->user()->empresa_id;
        User::create($data);

        return redirect()->route('usuarios.index')->with('ok', 'Usuario creado correctamente.');
    }

    public function edit(Request $request, User $usuario)
    {
        $this->autorizarEmpresa($request, $usuario);

        return view('usuarios.form', ['usuario' => $usuario, 'roles' => $this->roles]);
    }

    public function update(Request $request, User $usuario)
    {
        $this->autorizarEmpresa($request, $usuario);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($usuario->id)],
            'rol' => ['required', Rule::in($this->roles)],
            'cargo' => ['nullable', 'string', 'max:80'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'confirmed', Password::min(6)],
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $data['activo'] = $request->boolean('activo', true);
        $usuario->update($data);

        return redirect()->route('usuarios.index')->with('ok', 'Usuario actualizado.');
    }

    public function destroy(Request $request, User $usuario)
    {
        $this->autorizarEmpresa($request, $usuario);

        if ($usuario->id === $request->user()->id) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('ok', 'Usuario eliminado.');
    }

    public function toggleEstado(Request $request, User $usuario)
    {
        $this->autorizarEmpresa($request, $usuario);

        if ($usuario->id === $request->user()->id) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $usuario->update(['activo' => ! $usuario->activo]);

        return back()->with('ok', 'Estado del usuario actualizado.');
    }

    private function autorizarEmpresa(Request $request, User $usuario): void
    {
        abort_if($usuario->es_super_admin || $usuario->empresa_id !== $request->user()->empresa_id, 403, 'No autorizado.');
    }
}
