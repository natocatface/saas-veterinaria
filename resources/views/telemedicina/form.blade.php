@extends('layouts.app')

@section('title', $tele->exists ? 'Editar Teleconsulta' : 'Nueva Teleconsulta')
@section('subtitle', 'Consulta veterinaria en linea')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('telemedicina.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand-600 mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Volver a telemedicina
    </a>

    <form method="POST" action="{{ $tele->exists ? route('telemedicina.update', $tele) : route('telemedicina.store') }}" class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8 space-y-5">
        @csrf
        @if ($tele->exists) @method('PUT') @endif

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
                    @foreach ($mascotas as $m)<option value="{{ $m->id }}" @selected(old('mascota_id', $tele->mascota_id) == $m->id)>{{ $m->nombre }} — {{ optional($m->cliente)->nombre }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Fecha y hora *</label>
                <input type="datetime-local" name="fecha" value="{{ old('fecha', optional($tele->fecha)->format('Y-m-d\TH:i')) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Veterinario</label>
                <select name="user_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none bg-white">
                    <option value="">Sin asignar</option>
                    @foreach ($veterinarios as $v)<option value="{{ $v->id }}" @selected(old('user_id', $tele->user_id) == $v->id)>{{ $v->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Motivo</label>
                <input name="motivo" value="{{ old('motivo', $tele->motivo) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Estado *</label>
                <select name="estado" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none bg-white">
                    @foreach ($estados as $e)<option value="{{ $e }}" @selected(old('estado', $tele->estado ?: 'programada') === $e)>{{ ucfirst(str_replace('_',' ',$e)) }}</option>@endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Enlace de videollamada</label>
                <input name="enlace" type="url" value="{{ old('enlace', $tele->enlace) }}" placeholder="https://meet.google.com/..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Notas</label>
                <textarea name="notas" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">{{ old('notas', $tele->notas) }}</textarea>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button class="px-6 py-2.5 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-700 shadow-lg shadow-brand-500/25">{{ $tele->exists ? 'Guardar cambios' : 'Programar teleconsulta' }}</button>
            <a href="{{ route('telemedicina.index') }}" class="px-6 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200">Cancelar</a>
        </div>
    </form>
</div>
@endsection
