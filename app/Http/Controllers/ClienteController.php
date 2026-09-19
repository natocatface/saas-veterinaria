<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $estado = $request->get('estado');

        $clientes = Cliente::query()
            ->withCount('mascotas')
            ->when($q, fn ($query) => $query->where(function ($sub) use ($q) {
                $sub->where('nombre', 'like', "%{$q}%")
                    ->orWhere('documento', 'like', "%{$q}%")
                    ->orWhere('telefono', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            }))
            ->when($estado === 'activos', fn ($query) => $query->where('activo', true))
            ->when($estado === 'inactivos', fn ($query) => $query->where('activo', false))
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('clientes.index', compact('clientes', 'q', 'estado'));
    }

    public function create()
    {
        return view('clientes.form', ['cliente' => new Cliente()]);
    }

    public function store(Request $request)
    {
        Cliente::create($this->datos($request));

        return redirect()->route('clientes.index')->with('ok', 'Cliente registrado correctamente.');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load(['mascotas' => fn ($q) => $q->orderBy('nombre')]);

        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.form', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $cliente->update($this->datos($request));

        return redirect()->route('clientes.index')->with('ok', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        if ($cliente->mascotas()->exists()) {
            return back()->with('error', 'No se puede eliminar: el cliente tiene mascotas registradas. Puedes desactivarlo.');
        }

        $cliente->delete();

        return redirect()->route('clientes.index')->with('ok', 'Cliente eliminado.');
    }

    public function toggleEstado(Cliente $cliente)
    {
        $cliente->update(['activo' => ! $cliente->activo]);

        return back()->with('ok', 'Estado del cliente actualizado.');
    }

    private function datos(Request $request): array
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'documento' => ['nullable', 'string', 'max:30'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'notas' => ['nullable', 'string'],
            'password' => ['nullable', 'confirmed', Password::min(6)],
        ]);

        $data['activo'] = $request->boolean('activo');
        $data['acceso_portal'] = $request->boolean('acceso_portal');

        // Solo actualiza la contrasena si se ingreso una nueva (el cast 'hashed' la cifra).
        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }
}
