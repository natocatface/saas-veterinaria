<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\User;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $accion = $request->get('accion');
        $modelo = $request->get('modelo');

        $registros = Auditoria::query()
            ->with('usuario')
            ->when($accion, fn ($q) => $q->where('accion', $accion))
            ->when($modelo, fn ($q) => $q->where('modelo', $modelo))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $modelos = Auditoria::query()->select('modelo')->distinct()->orderBy('modelo')->pluck('modelo');

        return view('auditoria.index', compact('registros', 'accion', 'modelo', 'modelos'));
    }
}
