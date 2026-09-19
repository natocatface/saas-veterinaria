@extends('layouts.app')

@section('title', $producto->exists ? 'Editar Producto' : 'Nuevo Producto')
@section('subtitle', 'Datos del articulo de inventario')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('productos.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand-600 mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Volver al inventario
    </a>

    <form method="POST" action="{{ $producto->exists ? route('productos.update', $producto) : route('productos.store') }}" class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8 space-y-5">
        @csrf
        @if ($producto->exists) @method('PUT') @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nombre *</label>
                <input name="nombre" value="{{ old('nombre', $producto->nombre) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Categoria</label>
                <select name="categoria" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none bg-white">
                    <option value="">General</option>
                    @foreach ($categorias as $c)<option value="{{ $c }}" @selected(old('categoria', $producto->categoria) === $c)>{{ $c }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">SKU / Codigo</label>
                <input name="sku" value="{{ old('sku', $producto->sku) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Stock actual *</label>
                <input type="number" name="stock" value="{{ old('stock', $producto->stock ?? 0) }}" required min="0" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Stock minimo *</label>
                <input type="number" name="stock_minimo" value="{{ old('stock_minimo', $producto->stock_minimo ?? 0) }}" required min="0" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Precio venta (S/) *</label>
                <input type="number" step="0.01" name="precio" value="{{ old('precio', $producto->precio ?? 0) }}" required min="0" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Costo (S/)</label>
                <input type="number" step="0.01" name="costo" value="{{ old('costo', $producto->costo ?? 0) }}" min="0" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <label class="md:col-span-2 flex items-center gap-2.5 text-sm text-slate-600 select-none">
                <input type="checkbox" name="activo" value="1" @checked(old('activo', $producto->exists ? $producto->activo : true)) class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 w-5 h-5">
                Producto activo
            </label>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button class="px-6 py-2.5 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-700 shadow-lg shadow-brand-500/25">{{ $producto->exists ? 'Guardar cambios' : 'Registrar producto' }}</button>
            <a href="{{ route('productos.index') }}" class="px-6 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200">Cancelar</a>
        </div>
    </form>
</div>
@endsection
