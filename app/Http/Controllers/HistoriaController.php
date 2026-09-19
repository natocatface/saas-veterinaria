<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\Mascota;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HistoriaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));

        $consultas = Consulta::query()
            ->with('mascota.cliente', 'veterinario')
            ->when($q, fn ($query) => $query->whereHas('mascota', fn ($m) => $m->where('nombre', 'like', "%{$q}%")
                ->orWhereHas('cliente', fn ($c) => $c->where('nombre', 'like', "%{$q}%"))))
            ->orderByDesc('fecha')
            ->paginate(12)
            ->withQueryString();

        return view('historia.index', compact('consultas', 'q'));
    }

    public function create(Request $request)
    {
        $consulta = new Consulta([
            'mascota_id' => $request->get('mascota_id'),
            'fecha' => Carbon::now()->format('Y-m-d\TH:i'),
            'user_id' => optional($request->user())->id,
        ]);

        return view('historia.form', $this->formData($consulta));
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        $consulta = Consulta::create($data);

        // Actualiza el peso de la mascota si se registro
        if (! empty($data['peso'])) {
            $consulta->mascota()->update(['peso' => $data['peso']]);
        }

        return redirect()->route('historia.show', $consulta)->with('ok', 'Consulta registrada en la historia clinica.');
    }

    public function show(Consulta $historia)
    {
        $historia->load('mascota.cliente', 'veterinario');

        return view('historia.show', ['consulta' => $historia]);
    }

    public function edit(Consulta $historia)
    {
        return view('historia.form', $this->formData($historia));
    }

    public function update(Request $request, Consulta $historia)
    {
        $historia->update($this->validar($request));

        return redirect()->route('historia.show', $historia)->with('ok', 'Consulta actualizada.');
    }

    public function destroy(Consulta $historia)
    {
        $historia->delete();

        return redirect()->route('historia.index')->with('ok', 'Consulta eliminada.');
    }

    private function formData(Consulta $consulta): array
    {
        return [
            'consulta' => $consulta,
            'mascotas' => Mascota::where('activo', true)->with('cliente')->orderBy('nombre')->get(),
            'veterinarios' => User::whereIn('rol', ['veterinario', 'admin'])->where('activo', true)->orderBy('name')->get(),
        ];
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'mascota_id' => ['required', 'exists:mascotas,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'fecha' => ['required', 'date'],
            'motivo' => ['nullable', 'string', 'max:150'],
            'sintomas' => ['nullable', 'string'],
            'diagnostico' => ['nullable', 'string'],
            'tratamiento' => ['nullable', 'string'],
            'peso' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'temperatura' => ['nullable', 'numeric', 'min:0', 'max:99'],
            'observaciones' => ['nullable', 'string'],
        ]);
    }
}
