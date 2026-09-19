<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Mascota;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CitaController extends Controller
{
    public array $estados = ['pendiente', 'confirmada', 'atendida', 'cancelada'];

    public function index(Request $request)
    {
        $estado = $request->get('estado');
        $fecha = $request->get('fecha');

        $citas = Cita::query()
            ->with('mascota.cliente', 'veterinario')
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->when($fecha, fn ($q) => $q->whereDate('fecha', $fecha))
            ->orderBy('fecha')
            ->paginate(12)
            ->withQueryString();

        $resumen = [
            'hoy' => Cita::whereDate('fecha', Carbon::today())->count(),
            'pendientes' => Cita::where('estado', 'pendiente')->count(),
            'semana' => Cita::whereBetween('fecha', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
        ];

        return view('citas.index', [
            'citas' => $citas,
            'estado' => $estado,
            'fecha' => $fecha,
            'estados' => $this->estados,
            'resumen' => $resumen,
        ]);
    }

    public function create(Request $request)
    {
        $cita = new Cita([
            'mascota_id' => $request->get('mascota_id'),
            'fecha' => Carbon::now()->addHour()->format('Y-m-d\TH:00'),
            'estado' => 'pendiente',
        ]);

        return view('citas.form', $this->formData($cita));
    }

    public function store(Request $request)
    {
        Cita::create($this->validar($request));

        return redirect()->route('citas.index')->with('ok', 'Cita agendada correctamente.');
    }

    public function edit(Cita $cita)
    {
        return view('citas.form', $this->formData($cita));
    }

    public function update(Request $request, Cita $cita)
    {
        $cita->update($this->validar($request));

        return redirect()->route('citas.index')->with('ok', 'Cita actualizada correctamente.');
    }

    public function destroy(Cita $cita)
    {
        $cita->delete();

        return redirect()->route('citas.index')->with('ok', 'Cita eliminada.');
    }

    public function cambiarEstado(Cita $cita, string $estado)
    {
        abort_unless(in_array($estado, $this->estados, true), 404);
        $cita->update(['estado' => $estado]);

        return back()->with('ok', 'Estado de la cita actualizado.');
    }

    private function formData(Cita $cita): array
    {
        return [
            'cita' => $cita,
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
            'estado' => ['required', 'in:pendiente,confirmada,atendida,cancelada'],
            'notas' => ['nullable', 'string'],
        ]);
    }
}
