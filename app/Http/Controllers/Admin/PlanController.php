<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $planes = Plan::withCount('empresas')->orderBy('precio')->get();

        return view('admin.planes.index', compact('planes'));
    }

    public function create()
    {
        return view('admin.planes.form', ['plan' => new Plan(['activo' => true, 'periodo' => 'mensual'])]);
    }

    public function store(Request $request)
    {
        Plan::create($this->validar($request));

        return redirect()->route('admin.planes.index')->with('ok', 'Plan creado correctamente.');
    }

    public function edit(Plan $plane)
    {
        return view('admin.planes.form', ['plan' => $plane]);
    }

    public function update(Request $request, Plan $plane)
    {
        $plane->update($this->validar($request));

        return redirect()->route('admin.planes.index')->with('ok', 'Plan actualizado.');
    }

    public function destroy(Plan $plane)
    {
        if ($plane->empresas()->exists()) {
            return back()->with('error', 'No se puede eliminar: hay empresas usando este plan.');
        }

        $plane->delete();

        return redirect()->route('admin.planes.index')->with('ok', 'Plan eliminado.');
    }

    private function validar(Request $request): array
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:80'],
            'precio' => ['required', 'numeric', 'min:0'],
            'periodo' => ['required', 'in:mensual,anual'],
            'max_usuarios' => ['required', 'integer', 'min:0'],
            'max_pacientes' => ['required', 'integer', 'min:0'],
            'caracteristicas' => ['nullable', 'string'],
        ]);
        $data['activo'] = $request->boolean('activo', true);

        return $data;
    }
}
