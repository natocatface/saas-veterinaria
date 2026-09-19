<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\MovimientoCaja;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CajaController extends Controller
{
    public function index()
    {
        $cajaActual = Caja::where('estado', 'abierta')->with(['movimientos' => fn ($q) => $q->latest()])->first();
        $historial = Caja::where('estado', 'cerrada')->with('usuario')->latest('fecha_cierre')->limit(10)->get();

        return view('caja.index', compact('cajaActual', 'historial'));
    }

    public function abrir(Request $request)
    {
        if (Caja::where('estado', 'abierta')->exists()) {
            return back()->with('error', 'Ya existe una caja abierta.');
        }

        $data = $request->validate([
            'monto_apertura' => ['required', 'numeric', 'min:0'],
            'notas' => ['nullable', 'string'],
        ]);

        Caja::create([
            'user_id' => $request->user()->id,
            'fecha_apertura' => Carbon::now(),
            'monto_apertura' => $data['monto_apertura'],
            'estado' => 'abierta',
            'notas' => $data['notas'] ?? null,
        ]);

        return redirect()->route('caja.index')->with('ok', 'Caja aperturada correctamente.');
    }

    public function movimiento(Request $request, Caja $caja)
    {
        abort_unless($caja->estado === 'abierta', 422);

        $data = $request->validate([
            'tipo' => ['required', 'in:ingreso,egreso'],
            'concepto' => ['required', 'string', 'max:150'],
            'monto' => ['required', 'numeric', 'min:0.01'],
        ]);

        $caja->movimientos()->create([
            'user_id' => $request->user()->id,
            'tipo' => $data['tipo'],
            'concepto' => $data['concepto'],
            'monto' => $data['monto'],
        ]);

        return back()->with('ok', 'Movimiento registrado.');
    }

    public function cerrar(Request $request, Caja $caja)
    {
        abort_unless($caja->estado === 'abierta', 422);

        $caja->update([
            'estado' => 'cerrada',
            'fecha_cierre' => Carbon::now(),
            'monto_cierre' => $caja->saldoEsperado(),
        ]);

        return redirect()->route('caja.index')->with('ok', 'Caja cerrada. Saldo final: S/ '.number_format($caja->monto_cierre, 2));
    }
}
