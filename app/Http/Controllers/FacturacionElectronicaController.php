<?php

namespace App\Http\Controllers;

use App\Models\FacturacionConfig;
use App\Services\Facturacion\FacturacionManager;
use Illuminate\Http\Request;

class FacturacionElectronicaController extends Controller
{
    public function edit()
    {
        $config = FacturacionConfig::actual();
        $manager = new FacturacionManager($config);

        return view('facturacion.configuracion', [
            'config' => $config,
            'badges' => $manager->badges(),
        ]);
    }

    public function update(Request $request)
    {
        $config = FacturacionConfig::actual();

        $data = $request->validate([
            'driver' => ['required', 'in:ninguno,beta,greenter'],
            'entorno' => ['required', 'in:beta,produccion'],
            'ruc' => ['required', 'string', 'max:15'],
            'razon_social' => ['required', 'string', 'max:200'],
            'nombre_comercial' => ['nullable', 'string', 'max:200'],
            'direccion_fiscal' => ['nullable', 'string', 'max:200'],
            'ubigeo' => ['nullable', 'string', 'max:6'],
            'departamento' => ['nullable', 'string', 'max:60'],
            'provincia' => ['nullable', 'string', 'max:60'],
            'distrito' => ['nullable', 'string', 'max:60'],
            'sol_usuario' => ['nullable', 'string', 'max:60'],
            'certificado_path' => ['nullable', 'string', 'max:255'],
            'serie_boleta' => ['required', 'string', 'max:6'],
            'serie_factura' => ['required', 'string', 'max:6'],
            'serie_nota_credito' => ['nullable', 'string', 'max:6'],
        ]);

        // Checkboxes
        $data['habilitada'] = $request->boolean('habilitada');
        $data['emitir_automatico'] = $request->boolean('emitir_automatico');

        // La clave SOL solo se actualiza si el usuario escribio una nueva
        if ($request->filled('sol_clave')) {
            $data['sol_clave'] = $request->input('sol_clave');
        }

        $config->update($data);

        return redirect()
            ->route('facturacion.config.edit')
            ->with('ok', 'Configuracion de facturacion electronica guardada.');
    }

    public function probar()
    {
        $resultado = new FacturacionManager(FacturacionConfig::actual());
        $r = $resultado->probarConexion();

        return redirect()
            ->route('facturacion.config.edit')
            ->with('probar_ok', $r->ok)
            ->with('probar_msg', $r->mensaje)
            ->with('probar_checks', $r->checks);
    }
}
