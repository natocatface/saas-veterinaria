@extends('layouts.app')

@section('title', 'Inventario')
@section('subtitle', 'Productos, farmacia y stock')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-gradient-to-br from-amber-500 to-orange-600 text-white rounded-2xl p-5"><p class="text-xs font-bold tracking-widest text-white/80">PRODUCTOS ACTIVOS</p><p class="text-3xl font-extrabold mt-1">{{ $resumen['total'] }}</p></div>
    <div class="bg-gradient-to-br from-rose-500 to-red-600 text-white rounded-2xl p-5"><p class="text-xs font-bold tracking-widest text-white/80">STOCK BAJO</p><p class="text-3xl font-extrabold mt-1">{{ $resumen['bajo'] }}</p></div>
    <div class="bg-gradient-to-br from-emerald-500 to-green-600 text-white rounded-2xl p-5"><p class="text-xs font-bold tracking-widest text-white/80">VALOR INVENTARIO</p><p class="text-3xl font-extrabold mt-1">S/ {{ number_format($resumen['valor'], 0) }}</p></div>
</div>

<div class="flex flex-wrap items-center gap-3 mb-6">
    <form method="GET" class="flex flex-wrap items-center gap-3 flex-1">
        <div class="relative flex-1 min-w-[200px] max-w-sm">
            <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.3-4.3M11 18a7 7 0 100-14 7 7 0 000 14z"/></svg>
            <input name="q" value="{{ $q }}" placeholder="Buscar producto o SKU..." class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none text-sm">
        </div>
        <select name="categoria" onchange="this.form.submit()" class="py-2.5 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 outline-none focus:border-brand-500">
            <option value="">Todas las categorias</option>
            @foreach ($categorias as $c)<option value="{{ $c }}" @selected($categoria===$c)>{{ $c }}</option>@endforeach
        </select>
        <label class="inline-flex items-center gap-2 text-sm text-slate-600 px-3 py-2.5 rounded-xl border border-slate-200 cursor-pointer">
            <input type="checkbox" name="filtro" value="bajo" onchange="this.form.submit()" @checked($filtro==='bajo') class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
            Solo stock bajo
        </label>
    </form>
    <a href="{{ route('export.productos') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2"/></svg><span class="hidden sm:inline">Exportar</span></a>
                    <a href="{{ route('productos.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-700 shadow-lg shadow-brand-500/25">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
        Nuevo Producto
    </a>
</div>

<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-bold tracking-wide text-slate-400 border-b border-slate-100 bg-slate-50/60">
                    <th class="px-6 py-4">PRODUCTO</th>
                    <th class="px-6 py-4">CATEGORIA</th>
                    <th class="px-6 py-4 text-center">STOCK</th>
                    <th class="px-6 py-4 text-right">PRECIO</th>
                    <th class="px-6 py-4 text-right">ACCIONES</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($productos as $p)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-slate-800">{{ $p->nombre }}</p>
                            <p class="text-xs text-slate-400">{{ $p->sku ?: 'Sin SKU' }}</p>
                        </td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">{{ $p->categoria ?: 'General' }}</span></td>
                        <td class="px-6 py-4 text-center">
                            <div class="inline-flex items-center gap-2">
                                <form method="POST" action="{{ route('productos.stock', $p) }}"><input type="hidden" name="cantidad" value="-1">@csrf @method('PATCH')
                                    <button class="w-7 h-7 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 font-bold">−</button>
                                </form>
                                <span class="min-w-[3rem] font-bold {{ $p->stock <= $p->stock_minimo ? 'text-red-600' : 'text-slate-800' }}">{{ $p->stock }}</span>
                                <form method="POST" action="{{ route('productos.stock', $p) }}"><input type="hidden" name="cantidad" value="1">@csrf @method('PATCH')
                                    <button class="w-7 h-7 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 font-bold">+</button>
                                </form>
                            </div>
                            @if ($p->stock <= $p->stock_minimo)<p class="text-[11px] text-red-500 mt-1 font-semibold">Bajo (min {{ $p->stock_minimo }})</p>@endif
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-slate-700">S/ {{ number_format($p->precio, 2) }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('productos.edit', $p) }}" title="Editar" class="p-2 rounded-lg text-slate-400 hover:bg-amber-50 hover:text-amber-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.1 2.1 0 013 3L12 15l-4 1 1-4z"/></svg></a>
                                <form method="POST" action="{{ route('productos.destroy', $p) }}" onsubmit="return confirm('Eliminar este producto?')">@csrf @method('DELETE')
                                    <button title="Eliminar" class="p-2 rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m2 0v12a1 1 0 01-1 1H7a1 1 0 01-1-1V7"/></svg></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-16 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3"><svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 16V8a2 2 0 00-1-1.7l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.7l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg></div>
                        <p class="font-bold text-slate-600">Sin productos</p>
                        <p class="text-sm text-slate-400">No se encontraron productos.</p>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($productos->hasPages())<div class="px-6 py-4 border-t border-slate-100">{{ $productos->links() }}</div>@endif
</div>
@endsection
