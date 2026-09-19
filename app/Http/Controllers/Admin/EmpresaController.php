<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class EmpresaController extends Controller
{
    public array $estados = ['activa', 'prueba', 'suspendida'];

    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $estado = $request->get('estado');

        $empresas = Empresa::query()
            ->with('plan')
            ->withCount('usuarios')
            ->when($q, fn ($query) => $query->where('nombre', 'like', "%{$q}%")->orWhere('ruc', 'like', "%{$q}%"))
            ->when($estado, fn ($query) => $query->where('estado', $estado))
            ->orderBy('nombre')
            ->paginate(12)
            ->withQueryString();

        return view('admin.empresas.index', [
            'empresas' => $empresas,
            'q' => $q,
            'estado' => $estado,
            'estados' => $this->estados,
        ]);
    }

    public function create()
    {
        return view('admin.empresas.form', [
            'empresa' => new Empresa(['estado' => 'prueba']),
            'planes' => Plan::where('activo', true)->orderBy('precio')->get(),
            'estados' => $this->estados,
            'admin' => null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'ruc' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'plan_id' => ['nullable', 'exists:planes,id'],
            'estado' => ['required', Rule::in($this->estados)],
            'fecha_vencimiento' => ['nullable', 'date'],
            // Usuario administrador de la empresa
            'admin_name' => ['required', 'string', 'max:120'],
            'admin_email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'admin_password' => ['required', 'confirmed', Password::min(6)],
        ]);

        DB::transaction(function () use ($data, $request) {
            $empresa = Empresa::create([
                'nombre' => $data['nombre'],
                'ruc' => $data['ruc'] ?? null,
                'email' => $data['email'] ?? null,
                'telefono' => $data['telefono'] ?? null,
                'direccion' => $data['direccion'] ?? null,
                'plan_id' => $data['plan_id'] ?? null,
                'estado' => $data['estado'],
                'fecha_inicio' => Carbon::today(),
                'fecha_vencimiento' => $data['fecha_vencimiento'] ?? null,
            ]);

            User::create([
                'empresa_id' => $empresa->id,
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => Hash::make($data['admin_password']),
                'rol' => 'admin',
                'cargo' => 'Administrador',
                'activo' => true,
            ]);
        });

        return redirect()->route('admin.empresas.index')->with('ok', 'Empresa registrada con su administrador.');
    }

    public function show(Empresa $empresa)
    {
        $empresa->load('plan', 'usuarios', 'pagos.plan');

        return view('admin.empresas.show', compact('empresa'));
    }

    public function edit(Empresa $empresa)
    {
        return view('admin.empresas.form', [
            'empresa' => $empresa,
            'planes' => Plan::where('activo', true)->orderBy('precio')->get(),
            'estados' => $this->estados,
            'admin' => $empresa->usuarios()->where('rol', 'admin')->first(),
        ]);
    }

    public function update(Request $request, Empresa $empresa)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'ruc' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'plan_id' => ['nullable', 'exists:planes,id'],
            'estado' => ['required', Rule::in($this->estados)],
            'fecha_vencimiento' => ['nullable', 'date'],
        ]);

        $empresa->update($data);

        return redirect()->route('admin.empresas.index')->with('ok', 'Empresa actualizada.');
    }

    public function destroy(Empresa $empresa)
    {
        if ($empresa->usuarios()->count() > 0) {
            return back()->with('error', 'No se puede eliminar: la empresa tiene usuarios y datos asociados. Suspendela en su lugar.');
        }

        $empresa->delete();

        return redirect()->route('admin.empresas.index')->with('ok', 'Empresa eliminada.');
    }

    public function cambiarEstado(Empresa $empresa, string $estado)
    {
        abort_unless(in_array($estado, $this->estados, true), 404);
        $empresa->update(['estado' => $estado]);

        return back()->with('ok', 'Estado de la empresa actualizado a '.$empresa->estadoLabel().'.');
    }
}
