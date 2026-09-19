<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Mascota;
use Illuminate\Http\Request;

class MascotaController extends Controller
{
    public array $especies = ['Perro', 'Gato', 'Ave', 'Conejo', 'Roedor', 'Reptil', 'Otro'];

    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $especie = $request->get('especie');

        $mascotas = Mascota::query()
            ->with('cliente')
            ->when($q, fn ($query) => $query->where(function ($sub) use ($q) {
                $sub->where('nombre', 'like', "%{$q}%")
                    ->orWhere('raza', 'like', "%{$q}%")
                    ->orWhereHas('cliente', fn ($c) => $c->where('nombre', 'like', "%{$q}%"));
            }))
            ->when($especie, fn ($query) => $query->where('especie', $especie))
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('mascotas.index', [
            'mascotas' => $mascotas,
            'q' => $q,
            'especie' => $especie,
            'especies' => $this->especies,
        ]);
    }

    public function create(Request $request)
    {
        $mascota = new Mascota(['cliente_id' => $request->get('cliente_id')]);

        return view('mascotas.form', [
            'mascota' => $mascota,
            'clientes' => Cliente::where('activo', true)->orderBy('nombre')->get(),
            'especies' => $this->especies,
        ]);
    }

    public function store(Request $request)
    {
        $empresa = $request->user()->empresa;
        if ($empresa && ! $empresa->puedeAgregarPaciente()) {
            return back()->withInput()->with('error', 'Alcanzaste el limite de pacientes de tu plan. Mejora tu plan para registrar mas.');
        }

        Mascota::create($this->validar($request));

        return redirect()->route('mascotas.index')->with('ok', 'Mascota registrada correctamente.');
    }

    public function show(Mascota $mascota)
    {
        $mascota->load('cliente', 'citas.veterinario');

        return view('mascotas.show', compact('mascota'));
    }

    public function edit(Mascota $mascota)
    {
        return view('mascotas.form', [
            'mascota' => $mascota,
            'clientes' => Cliente::where('activo', true)->orderBy('nombre')->get(),
            'especies' => $this->especies,
        ]);
    }

    public function update(Request $request, Mascota $mascota)
    {
        $mascota->update($this->validar($request));

        return redirect()->route('mascotas.index')->with('ok', 'Mascota actualizada correctamente.');
    }

    public function destroy(Mascota $mascota)
    {
        $mascota->delete();

        return redirect()->route('mascotas.index')->with('ok', 'Mascota eliminada.');
    }

    public function toggleEstado(Mascota $mascota)
    {
        $mascota->update(['activo' => ! $mascota->activo]);

        return back()->with('ok', 'Estado de la mascota actualizado.');
    }

    private function validar(Request $request): array
    {
        $data = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'nombre' => ['required', 'string', 'max:100'],
            'especie' => ['required', 'string', 'max:40'],
            'raza' => ['nullable', 'string', 'max:80'],
            'sexo' => ['nullable', 'in:Macho,Hembra'],
            'color' => ['nullable', 'string', 'max:60'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'peso' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'esterilizado' => ['nullable', 'boolean'],
            'notas' => ['nullable', 'string'],
        ]);
        $data['esterilizado'] = $request->boolean('esterilizado');
        $data['activo'] = true;

        return $data;
    }
}
