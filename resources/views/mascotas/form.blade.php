@extends('layouts.app')

@section('title', $mascota->exists ? 'Editar Mascota' : 'Nueva Mascota')
@section('subtitle', 'Ficha del paciente')

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('mascotas.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand-600 mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Volver a pacientes
    </a>

    <form method="POST" action="{{ $mascota->exists ? route('mascotas.update', $mascota) : route('mascotas.store') }}" class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8 space-y-5">
        @csrf
        @if ($mascota->exists) @method('PUT') @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Dueno *</label>
                <select name="cliente_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none bg-white">
                    <option value="">Seleccionar cliente...</option>
                    @foreach ($clientes as $c)<option value="{{ $c->id }}" @selected(old('cliente_id', $mascota->cliente_id) == $c->id)>{{ $c->nombre }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nombre *</label>
                <input name="nombre" value="{{ old('nombre', $mascota->nombre) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Especie *</label>
                <select name="especie" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none bg-white">
                    @foreach ($especies as $e)<option value="{{ $e }}" @selected(old('especie', $mascota->especie) === $e)>{{ $e }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Raza</label>
                <input name="raza" value="{{ old('raza', $mascota->raza) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Sexo</label>
                <select name="sexo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none bg-white">
                    <option value="">—</option>
                    <option value="Macho" @selected(old('sexo', $mascota->sexo)==='Macho')>Macho</option>
                    <option value="Hembra" @selected(old('sexo', $mascota->sexo)==='Hembra')>Hembra</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Color</label>
                <input name="color" value="{{ old('color', $mascota->color) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', optional($mascota->fecha_nacimiento)->format('Y-m-d')) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Peso (kg)</label>
                <input type="number" step="0.01" name="peso" value="{{ old('peso', $mascota->peso) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Notas clinicas</label>
                <textarea name="notas" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">{{ old('notas', $mascota->notas) }}</textarea>
            </div>
            <label class="md:col-span-2 flex items-center gap-2.5 text-sm text-slate-600 select-none">
                <input type="checkbox" name="esterilizado" value="1" @checked(old('esterilizado', $mascota->esterilizado)) class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 w-5 h-5">
                Esterilizado / castrado
            </label>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button class="px-6 py-2.5 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-700 shadow-lg shadow-brand-500/25">{{ $mascota->exists ? 'Guardar cambios' : 'Registrar mascota' }}</button>
            <a href="{{ route('mascotas.index') }}" class="px-6 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200">Cancelar</a>
        </div>
    </form>
</div>
@endsection
