@extends('layouts.admin')

@section('title', 'Registrar Pago')
@section('subtitle', $empresa->nombre)

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('admin.empresas.show', $empresa) }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-iris-600 mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Volver a la empresa
    </a>

    <div class="bg-iris-50 border border-iris-100 rounded-2xl p-4 mb-6 text-sm text-slate-600 flex flex-wrap gap-x-6 gap-y-1">
        <span>Plan actual: <strong class="text-slate-800">{{ optional($empresa->plan)->nombre ?: 'Sin plan' }}</strong></span>
        <span>Estado: <strong class="text-slate-800">{{ $empresa->estadoLabel() }}</strong></span>
        <span>Vence: <strong class="text-slate-800">{{ optional($empresa->fecha_vencimiento)->format('d/m/Y') ?: '—' }}</strong></span>
    </div>

    <form method="POST" action="{{ route('admin.empresas.pago.store', $empresa) }}" class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8 space-y-5">
        @csrf
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Monto (S/) *</label>
                <input type="number" step="0.01" min="0" name="monto" value="{{ old('monto', optional($empresa->plan)->precio ?? 0) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Periodo *</label>
                <select name="periodo" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none bg-white">
                    <option value="mensual" @selected(old('periodo')==='mensual')>Mensual (+1 mes)</option>
                    <option value="anual" @selected(old('periodo')==='anual')>Anual (+1 ano)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Metodo *</label>
                <select name="metodo" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none bg-white">
                    <option value="transferencia">Transferencia</option>
                    <option value="tarjeta">Tarjeta</option>
                    <option value="efectivo">Efectivo</option>
                    <option value="yape">Yape / Plin</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Fecha de pago *</label>
                <input type="date" name="fecha_pago" value="{{ old('fecha_pago', now()->format('Y-m-d')) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Referencia / operacion</label>
                <input name="referencia" value="{{ old('referencia') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Notas</label>
                <textarea name="notas" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none">{{ old('notas') }}</textarea>
            </div>
        </div>
        <div class="flex items-center gap-3 pt-2">
            <button class="px-6 py-2.5 rounded-xl bg-iris-600 text-white font-semibold hover:bg-iris-700 shadow-lg shadow-indigo-500/25">Registrar pago y renovar</button>
            <a href="{{ route('admin.empresas.show', $empresa) }}" class="px-6 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200">Cancelar</a>
        </div>
    </form>
</div>
@endsection
