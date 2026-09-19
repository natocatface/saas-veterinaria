@extends('layouts.app')

@section('title', $grooming->exists ? 'Editar Servicio' : 'Nuevo Servicio')
@section('subtitle', 'Peluqueria y estetica')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('peluqueria.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand-600 mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Volver a peluqueria
    </a>

    <form method="POST" action="{{ $grooming->exists ? route('peluqueria.update', $grooming) : route('peluqueria.store') }}" class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8 space-y-5">
        @csrf
        @if ($grooming->exists) @method('PUT') @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Mascota *</label>
                <select name="mascota_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none bg-white">
                    <option value="">Seleccionar...</option>
                    @foreach ($mascotas as $m)<option value="{{ $m->id }}" @selected(old('mascota_id', $grooming->mascota_id) == $m->id)>{{ $m->nombre }} — {{ optional($m->cliente)->nombre }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Servicio *</label>
                <input name="servicio" list="servicios-list" value="{{ old('servicio', $grooming->servicio) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                <datalist id="servicios-list">@foreach ($servicios as $s)<option value="{{ $s }}">@endforeach</datalist>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Precio (S/) *</label>
                <input type="number" step="0.01" min="0" name="precio" value="{{ old('precio', $grooming->precio ?? 0) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Fecha y hora *</label>
                <input type="datetime-local" name="fecha" value="{{ old('fecha', optional($grooming->fecha)->format('Y-m-d\TH:i')) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Responsable</label>
                <select name="user_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none bg-white">
                    <option value="">Sin asignar</option>
                    @foreach ($groomers as $g)<option value="{{ $g->id }}" @selected(old('user_id', $grooming->user_id) == $g->id)>{{ $g->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Estado *</label>
                <select name="estado" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none bg-white">
                    @foreach ($estados as $e)<option value="{{ $e }}" @selected(old('estado', $grooming->estado ?: 'programado') === $e)>{{ ucfirst(str_replace('_',' ',$e)) }}</option>@endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Notas</label>
                <textarea name="notas" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">{{ old('notas', $grooming->notas) }}</textarea>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button class="px-6 py-2.5 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-700 shadow-lg shadow-brand-500/25">{{ $grooming->exists ? 'Guardar cambios' : 'Registrar servicio' }}</button>
            <a href="{{ route('peluqueria.index') }}" class="px-6 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200">Cancelar</a>
        </div>
    </form>
</div>
@endsection
