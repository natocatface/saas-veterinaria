@extends('layouts.admin')

@section('title', $plan->exists ? 'Editar Plan' : 'Nuevo Plan')
@section('subtitle', 'Configuracion del plan de suscripcion')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('admin.planes.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-iris-600 mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Volver a planes
    </a>

    <form method="POST" action="{{ $plan->exists ? route('admin.planes.update', $plan) : route('admin.planes.store') }}" class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8 space-y-5">
        @csrf
        @if ($plan->exists) @method('PUT') @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nombre del plan *</label>
                <input name="nombre" value="{{ old('nombre', $plan->nombre) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Precio (S/) *</label>
                <input type="number" step="0.01" min="0" name="precio" value="{{ old('precio', $plan->precio ?? 0) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Periodo *</label>
                <select name="periodo" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none bg-white">
                    <option value="mensual" @selected(old('periodo', $plan->periodo)==='mensual')>Mensual</option>
                    <option value="anual" @selected(old('periodo', $plan->periodo)==='anual')>Anual</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Max. usuarios (0 = ilimitado) *</label>
                <input type="number" min="0" name="max_usuarios" value="{{ old('max_usuarios', $plan->max_usuarios ?? 0) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Max. pacientes (0 = ilimitado) *</label>
                <input type="number" min="0" name="max_pacientes" value="{{ old('max_pacientes', $plan->max_pacientes ?? 0) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Caracteristicas (una por linea)</label>
                <textarea name="caracteristicas" rows="4" placeholder="Soporte prioritario&#10;Facturacion electronica&#10;Reportes avanzados" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-iris-500 outline-none">{{ old('caracteristicas', $plan->caracteristicas) }}</textarea>
            </div>
            <label class="md:col-span-2 flex items-center gap-2.5 text-sm text-slate-600 select-none">
                <input type="checkbox" name="activo" value="1" @checked(old('activo', $plan->exists ? $plan->activo : true)) class="rounded border-slate-300 text-iris-600 focus:ring-iris-500 w-5 h-5">
                Plan activo (disponible para asignar)
            </label>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button class="px-6 py-2.5 rounded-xl bg-iris-600 text-white font-semibold hover:bg-iris-700 shadow-lg shadow-indigo-500/25">{{ $plan->exists ? 'Guardar cambios' : 'Crear plan' }}</button>
            <a href="{{ route('admin.planes.index') }}" class="px-6 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200">Cancelar</a>
        </div>
    </form>
</div>
@endsection
