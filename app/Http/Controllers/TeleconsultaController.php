<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use App\Models\Teleconsulta;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TeleconsultaController extends Controller
{
    public array $estados = ['programada', 'en_curso', 'realizada', 'cancelada'];

    public function index(Request $request)
    {
        $estado = $request->get('estado');

        $teleconsultas = Teleconsulta::query()
            ->with('mascota.cliente', 'veterinario')
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->orderByDesc('fecha')
            ->paginate(12)
            ->withQueryString();

        $resumen = [
            'hoy' => Teleconsulta::whereDate('fecha', Carbon::today())->count(),
            'programadas' => Teleconsulta::where('estado', 'programada')->count(),
            'realizadas' => Teleconsulta::where('estado', 'realizada')->count(),
        ];

        return view('telemedicina.index', compact('teleconsultas', 'estado', 'resumen') + ['estados' => $this->estados]);
    }

    public function create(Request $request)
    {
        $tele = new Teleconsulta([
            'mascota_id' => $request->get('mascota_id'),
            'fecha' => Carbon::now()->addHour()->format('Y-m-d\TH:00'),
            'estado' => 'programada',
        ]);

        return view('telemedicina.form', $this->formData($tele));
    }

    public function store(Request $request)
    {
        Teleconsulta::create($this->validar($request));

        return redirect()->route('telemedicina.index')->with('ok', 'Teleconsulta programada.');
    }

    public function edit(Teleconsulta $telemedicina)
    {
        return view('telemedicina.form', $this->formData($telemedicina));
    }

    public function update(Request $request, Teleconsulta $telemedicina)
    {
        $telemedicina->update($this->validar($request));

        return redirect()->route('telemedicina.index')->with('ok', 'Teleconsulta actualizada.');
    }

    public function destroy(Teleconsulta $telemedicina)
    {
        $telemedicina->delete();

        return redirect()->route('telemedicina.index')->with('ok', 'Teleconsulta eliminada.');
    }

    public function cambiarEstado(Teleconsulta $telemedicina, string $estado)
    {
        abort_unless(in_array($estado, $this->estados, true), 404);
        $telemedicina->update(['estado' => $estado]);

        return back()->with('ok', 'Estado actualizado.');
    }

    private function formData(Teleconsulta $tele): array
    {
        return [
            'tele' => $tele,
            'mascotas' => Mascota::where('activo', true)->with('cliente')->orderBy('nombre')->get(),
            'veterinarios' => User::whereIn('rol', ['veterinario', 'admin'])->where('activo', true)->orderBy('name')->get(),
            'estados' => $this->estados,
        ];
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'mascota_id' => ['required', 'exists:mascotas,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'fecha' => ['required', 'date'],
            'motivo' => ['nullable', 'string', 'max:150'],
            'enlace' => ['nullable', 'url', 'max:255'],
            'estado' => ['required', 'in:programada,en_curso,realizada,cancelada'],
            'notas' => ['nullable', 'string'],
        ]);
    }
}
