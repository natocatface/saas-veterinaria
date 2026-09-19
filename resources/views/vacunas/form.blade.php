@extends('layouts.app')

@section('title', $vacuna->exists ? 'Editar Vacuna' : 'Nueva Vacuna')
@section('subtitle', 'Registro de vacunacion')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('vacunas.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand-600 mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Volver a vacunaciones
    </a>

    <form method="POST" action="{{ $vacuna->exists ? route('vacunas.update', $vacuna) : route('vacunas.store') }}" class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8 space-y-5">
        @csrf
        @if ($vacuna->exists) @method('PUT') @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Mascota *</label>
                <select name="mascota_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none bg-white">
                    <option value="">Seleccionar...</option>
                    @foreach ($mascotas as $m)<option value="{{ $m->id }}" @selected(old('mascota_id', $vacuna->mascota_id) == $m->id)>{{ $m->nombre }} — {{ optional($m->cliente)->nombre }}</option>@endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Vacuna *</label>
                <input name="nombre" list="vacunas-sugeridas" value="{{ old('nombre', $vacuna->nombre) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                <datalist id="vacunas-sugeridas">@foreach ($sugeridas as $s)<option value="{{ $s }}">@endforeach</datalist>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Fecha de aplicacion *</label>
                <input type="date" name="fecha_aplicacion" value="{{ old('fecha_aplicacion', optional($vacuna->fecha_aplicacion)->format('Y-m-d')) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Proxima dosis</label>
                <input type="date" name="proxima_dosis" value="{{ old('proxima_dosis', optional($vacuna->proxima_dosis)->format('Y-m-d')) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Lote</label>
                <input name="lote" value="{{ old('lote', $vacuna->lote) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Veterinario</label>
                <select name="user_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none bg-white">
                    <option value="">Sin asignar</option>
                    @foreach ($veterinarios as $vet)<option value="{{ $vet->id }}" @selected(old('user_id', $vacuna->user_id) == $vet->id)>{{ $vet->name }}</option>@endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Notas</label>
                <textarea name="notas" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">{{ old('notas', $vacuna->notas) }}</textarea>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button class="px-6 py-2.5 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-700 shadow-lg shadow-brand-500/25">{{ $vacuna->exists ? 'Guardar cambios' : 'Registrar vacuna' }}</button>
            <a href="{{ route('vacunas.index') }}" class="px-6 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200">Cancelar</a>
        </div>
    </form>
</div>
@endsection
