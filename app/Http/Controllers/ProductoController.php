<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public array $categorias = ['Alimento', 'Farmacia', 'Higiene', 'Accesorios', 'Servicios', 'Otro'];

    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $categoria = $request->get('categoria');
        $filtro = $request->get('filtro');

        $productos = Producto::query()
            ->when($q, fn ($query) => $query->where(function ($sub) use ($q) {
                $sub->where('nombre', 'like', "%{$q}%")->orWhere('sku', 'like', "%{$q}%");
            }))
            ->when($categoria, fn ($query) => $query->where('categoria', $categoria))
            ->when($filtro === 'bajo', fn ($query) => $query->whereColumn('stock', '<=', 'stock_minimo'))
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        $resumen = [
            'total' => Producto::where('activo', true)->count(),
            'bajo' => Producto::whereColumn('stock', '<=', 'stock_minimo')->count(),
            'valor' => Producto::selectRaw('COALESCE(SUM(stock * costo),0) as v')->value('v'),
        ];

        return view('productos.index', [
            'productos' => $productos,
            'q' => $q,
            'categoria' => $categoria,
            'filtro' => $filtro,
            'categorias' => $this->categorias,
            'resumen' => $resumen,
        ]);
    }

    public function create()
    {
        return view('productos.form', ['producto' => new Producto(), 'categorias' => $this->categorias]);
    }

    public function store(Request $request)
    {
        Producto::create($this->validar($request));

        return redirect()->route('productos.index')->with('ok', 'Producto registrado correctamente.');
    }

    public function edit(Producto $producto)
    {
        return view('productos.form', ['producto' => $producto, 'categorias' => $this->categorias]);
    }

    public function update(Request $request, Producto $producto)
    {
        $producto->update($this->validar($request));

        return redirect()->route('productos.index')->with('ok', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('productos.index')->with('ok', 'Producto eliminado.');
    }

    public function ajustarStock(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'cantidad' => ['required', 'integer'],
        ]);

        $nuevo = max(0, $producto->stock + $data['cantidad']);
        $producto->update(['stock' => $nuevo]);

        return back()->with('ok', 'Stock actualizado a '.$nuevo.' unidades.');
    }

    private function validar(Request $request): array
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'categoria' => ['nullable', 'string', 'max:50'],
            'sku' => ['nullable', 'string', 'max:50'],
            'stock' => ['required', 'integer', 'min:0'],
            'stock_minimo' => ['required', 'integer', 'min:0'],
            'precio' => ['required', 'numeric', 'min:0'],
            'costo' => ['nullable', 'numeric', 'min:0'],
        ]);
        $data['costo'] = $data['costo'] ?? 0;
        $data['activo'] = $request->boolean('activo', true);

        return $data;
    }
}
