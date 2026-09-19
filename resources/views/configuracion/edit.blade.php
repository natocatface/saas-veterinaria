@extends('layouts.app')

@section('title', 'Configuracion')
@section('subtitle', 'Datos de la clinica y parametros')

@section('content')
<div class="max-w-3xl">
    <form method="POST" action="{{ route('configuracion.update') }}" class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8 space-y-5">
        @csrf @method('PUT')

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div>
            <h3 class="font-extrabold text-slate-800 mb-1">Datos de la clinica</h3>
            <p class="text-sm text-slate-400 mb-4">Aparecen en los comprobantes emitidos.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nombre de la clinica *</label>
                    <input name="nombre_clinica" value="{{ old('nombre_clinica', $config->nombre_clinica) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">RUC</label>
                    <input name="ruc" value="{{ old('ruc', $config->ruc) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Telefono</label>
                    <input name="telefono" value="{{ old('telefono', $config->telefono) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Direccion</label>
                    <input name="direccion" value="{{ old('direccion', $config->direccion) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Correo</label>
                    <input name="email" type="email" value="{{ old('email', $config->email) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
            </div>
        </div>

        <div class="border-t border-slate-100 pt-5">
            <h3 class="font-extrabold text-slate-800 mb-1">Facturacion</h3>
            <p class="text-sm text-slate-400 mb-4">Parametros de comprobantes.</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Moneda *</label>
                    <input name="moneda" value="{{ old('moneda', $config->moneda) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">IGV (%) *</label>
                    <input name="igv_porcentaje" type="number" step="0.01" value="{{ old('igv_porcentaje', $config->igv_porcentaje) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Serie boleta *</label>
                    <input name="serie_boleta" value="{{ old('serie_boleta', $config->serie_boleta) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Serie factura *</label>
                    <input name="serie_factura" value="{{ old('serie_factura', $config->serie_factura) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                </div>
            </div>
        </div>

        <div class="pt-2">
            <button class="px-6 py-2.5 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-700 shadow-lg shadow-brand-500/25">Guardar configuracion</button>
        </div>
    </form>
</div>
@endsection
