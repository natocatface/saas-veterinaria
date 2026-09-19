<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function edit()
    {
        return view('configuracion.edit', ['config' => Configuracion::actual()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'nombre_clinica' => ['required', 'string', 'max:150'],
            'ruc' => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'moneda' => ['required', 'string', 'max:5'],
            'igv_porcentaje' => ['required', 'numeric', 'min:0', 'max:100'],
            'serie_boleta' => ['required', 'string', 'max:10'],
            'serie_factura' => ['required', 'string', 'max:10'],
        ]);

        Configuracion::actual()->update($data);

        return back()->with('ok', 'Configuracion guardada correctamente.');
    }
}
