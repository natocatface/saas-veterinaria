<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use App\Models\User;
use App\Models\Vacuna;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VacunaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $filtro = $request->get('filtro');

        $vacunas = Vacuna::query()
            ->with('mascota.cliente', 'veterinario')
            ->when($q, fn ($query) => $query->where('nombre', 'like', "%{$q}%")
                ->orWhereHas('mascota', fn ($m) => $m->where('nombre', 'like', "%{$q}%")))
            ->when($filtro === 'proximas', fn ($query) => $query->whereNotNull('proxima_dosis')
                ->whereBetween('proxima_dosis', [Carbon::today(), Carbon::today()->addDays(30)]))
            ->orderByDesc('fecha_aplicacion')
            ->paginate(12)
            ->withQueryString();

        $resumen = [
            'total' => Vacuna::count(),
            'proximas' => Vacuna::whereNotNull('proxima_dosis')->whereBetween('proxima_dosis', [Carbon::today(), Carbon::today()->addDays(30)])->count(),
            'vencidas' => Vacuna::whereNotNull('proxima_dosis')->where('proxima_dosis', '<', Carbon::today())->count(),
        ];

        return view('vacunas.index', compact('vacunas', 'q', 'filtro', 'resumen'));
    }

    public function create(Request $request)
    {
        $vacuna = new Vacuna([
            'mascota_id' => $request->get('mascota_id'),
            'fecha_aplicacion' => Carbon::today()->format('Y-m-d'),
            'user_id' => optional($request->user())->id,
        ]);

        return view('vacunas.form', $this->formData($vacuna));
    }

    public function store(Request $request)
    {
        Vacuna::create($this->validar($request));

        return redirect()->route('vacunas.index')->with('ok', 'Vacuna registrada correctamente.');
    }

    public function edit(Vacuna $vacuna)
    {
        return view('vacunas.form', $this->formData($vacuna));
    }

    public function update(Request $request, Vacuna $vacuna)
    {
        $vacuna->update($this->validar($request));

        return redirect()->route('vacunas.index')->with('ok', 'Vacuna actualizada.');
    }

    public function destroy(Vacuna $vacuna)
    {
        $vacuna->delete();

        return redirect()->route('vacunas.index')->with('ok', 'Registro de vacuna eliminado.');
    }

    private function formData(Vacuna $vacuna): array
    {
        return [
            'vacuna' => $vacuna,
            'mascotas' => Mascota::where('activo', true)->with('cliente')->orderBy('nombre')->get(),
            'veterinarios' => User::whereIn('rol', ['veterinario', 'admin'])->where('activo', true)->orderBy('name')->get(),
            'sugeridas' => ['Antirrabica', 'Sextuple / Polivalente', 'Triple Felina', 'Parvovirus', 'Moquillo', 'Leptospirosis', 'Bordetella', 'Desparasitacion'],
        ];
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'mascota_id' => ['required', 'exists:mascotas,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'nombre' => ['required', 'string', 'max:120'],
            'fecha_aplicacion' => ['required', 'date'],
            'proxima_dosis' => ['nullable', 'date', 'after_or_equal:fecha_aplicacion'],
            'lote' => ['nullable', 'string', 'max:60'],
            'notas' => ['nullable', 'string'],
        ]);
    }
}
