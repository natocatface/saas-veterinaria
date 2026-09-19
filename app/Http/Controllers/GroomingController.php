<?php

namespace App\Http\Controllers;

use App\Models\Grooming;
use App\Models\Mascota;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class GroomingController extends Controller
{
    public array $servicios = ['Bano completo', 'Bano y corte', 'Corte de pelo', 'Corte de unas', 'Limpieza de oidos', 'Deslanado', 'Spa completo'];
    public array $estados = ['programado', 'en_proceso', 'completado', 'cancelado'];

    public function index(Request $request)
    {
        $estado = $request->get('estado');

        $groomings = Grooming::query()
            ->with('mascota.cliente', 'groomer')
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->orderByDesc('fecha')
            ->paginate(12)
            ->withQueryString();

        $resumen = [
            'hoy' => Grooming::whereDate('fecha', Carbon::today())->count(),
            'programados' => Grooming::where('estado', 'programado')->count(),
            'ingresos' => (float) Grooming::where('estado', 'completado')->whereMonth('fecha', now()->month)->sum('precio'),
        ];

        return view('peluqueria.index', compact('groomings', 'estado', 'resumen') + ['estados' => $this->estados]);
    }

    public function create(Request $request)
    {
        $grooming = new Grooming([
            'mascota_id' => $request->get('mascota_id'),
            'fecha' => Carbon::now()->addHour()->format('Y-m-d\TH:00'),
            'estado' => 'programado',
        ]);

        return view('peluqueria.form', $this->formData($grooming));
    }

    public function store(Request $request)
    {
        Grooming::create($this->validar($request));

        return redirect()->route('peluqueria.index')->with('ok', 'Servicio de peluqueria registrado.');
    }

    public function edit(Grooming $peluqueria)
    {
        return view('peluqueria.form', $this->formData($peluqueria));
    }

    public function update(Request $request, Grooming $peluqueria)
    {
        $peluqueria->update($this->validar($request));

        return redirect()->route('peluqueria.index')->with('ok', 'Servicio actualizado.');
    }

    public function destroy(Grooming $peluqueria)
    {
        $peluqueria->delete();

        return redirect()->route('peluqueria.index')->with('ok', 'Servicio eliminado.');
    }

    public function cambiarEstado(Grooming $peluqueria, string $estado)
    {
        abort_unless(in_array($estado, $this->estados, true), 404);
        $peluqueria->update(['estado' => $estado]);

        return back()->with('ok', 'Estado actualizado.');
    }

    private function formData(Grooming $grooming): array
    {
        return [
            'grooming' => $grooming,
            'mascotas' => Mascota::where('activo', true)->with('cliente')->orderBy('nombre')->get(),
            'groomers' => User::whereIn('rol', ['groomer', 'recepcion', 'admin'])->where('activo', true)->orderBy('name')->get(),
            'servicios' => $this->servicios,
            'estados' => $this->estados,
        ];
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'mascota_id' => ['required', 'exists:mascotas,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'fecha' => ['required', 'date'],
            'servicio' => ['required', 'string', 'max:120'],
            'precio' => ['required', 'numeric', 'min:0'],
            'estado' => ['required', 'in:programado,en_proceso,completado,cancelado'],
            'notas' => ['nullable', 'string'],
        ]);
    }
}
