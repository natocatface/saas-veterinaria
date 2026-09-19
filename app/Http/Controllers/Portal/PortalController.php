<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Mascota;
use App\Models\Vacuna;
use Illuminate\Support\Carbon;

class PortalController extends Controller
{
    private function cliente()
    {
        return auth('cliente')->user();
    }

    public function dashboard()
    {
        $cliente = $this->cliente();
        $mascotaIds = Mascota::where('cliente_id', $cliente->id)->pluck('id');

        return view('portal.dashboard', [
            'cliente' => $cliente,
            'mascotas' => Mascota::where('cliente_id', $cliente->id)->orderBy('nombre')->get(),
            'proximasCitas' => Cita::whereIn('mascota_id', $mascotaIds)->with('mascota', 'veterinario')
                ->where('fecha', '>=', Carbon::now())->orderBy('fecha')->limit(5)->get(),
            'proximasVacunas' => Vacuna::whereIn('mascota_id', $mascotaIds)->with('mascota')
                ->whereNotNull('proxima_dosis')->whereDate('proxima_dosis', '>=', Carbon::today())
                ->orderBy('proxima_dosis')->limit(5)->get(),
        ]);
    }

    public function mascotas()
    {
        $cliente = $this->cliente();

        return view('portal.mascotas', [
            'cliente' => $cliente,
            'mascotas' => Mascota::where('cliente_id', $cliente->id)->orderBy('nombre')->get(),
        ]);
    }

    public function citas()
    {
        $cliente = $this->cliente();
        $mascotaIds = Mascota::where('cliente_id', $cliente->id)->pluck('id');

        return view('portal.citas', [
            'cliente' => $cliente,
            'citas' => Cita::whereIn('mascota_id', $mascotaIds)->with('mascota', 'veterinario')
                ->orderByDesc('fecha')->paginate(15),
        ]);
    }

    public function vacunas()
    {
        $cliente = $this->cliente();
        $mascotaIds = Mascota::where('cliente_id', $cliente->id)->pluck('id');

        return view('portal.vacunas', [
            'cliente' => $cliente,
            'vacunas' => Vacuna::whereIn('mascota_id', $mascotaIds)->with('mascota')
                ->orderByDesc('fecha_aplicacion')->paginate(15),
        ]);
    }
}
