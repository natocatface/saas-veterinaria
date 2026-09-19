@extends('layouts.app')

@section('title', $cliente->nombre)
@section('subtitle', 'Ficha del cliente')

@section('content')
<a href="{{ route('clientes.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand-600 mb-4">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    Volver a clientes
</a>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-brand-50 text-brand-700 flex items-center justify-center text-2xl font-extrabold">{{ mb_strtoupper(mb_substr($cliente->nombre,0,1)) }}</div>
            <div>
                <h2 class="text-xl font-extrabold text-slate-800">{{ $cliente->nombre }}</h2>
                @if ($cliente->activo)<span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">Activo</span>
                @else<span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 text-xs font-semibold">Inactivo</span>@endif
            </div>
        </div>
        <dl class="mt-6 space-y-3 text-sm">
            <div class="flex justify-between"><dt class="text-slate-400">Documento</dt><dd class="font-semibold text-slate-700">{{ $cliente->documento ?: '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Telefono</dt><dd class="font-semibold text-slate-700">{{ $cliente->telefono ?: '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Correo</dt><dd class="font-semibold text-slate-700">{{ $cliente->email ?: '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Direccion</dt><dd class="font-semibold text-slate-700 text-right">{{ $cliente->direccion ?: '—' }}</dd></div>
        </dl>
        @if ($cliente->notas)<p class="mt-4 text-sm text-slate-500 bg-slate-50 rounded-xl p-3">{{ $cliente->notas }}</p>@endif
        <a href="{{ route('clientes.edit', $cliente) }}" class="mt-6 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-semibold hover:bg-slate-200 w-full justify-center">Editar cliente</a>
    </div>

    <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-extrabold text-slate-800">Mascotas ({{ $cliente->mascotas->count() }})</h3>
            <a href="{{ route('mascotas.create', ['cliente_id' => $cliente->id]) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>Agregar mascota</a>
        </div>
        @if ($cliente->mascotas->isEmpty())
            <p class="text-sm text-slate-400 py-8 text-center">Este cliente aun no tiene mascotas registradas.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach ($cliente->mascotas as $m)
                    <a href="{{ route('mascotas.show', $m) }}" class="flex items-center gap-3 p-3 rounded-2xl border border-slate-100 hover:border-brand-200 hover:bg-brand-50/40 transition">
                        <div class="w-11 h-11 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c-2.5 0-4.5 2-4.5 4.2 0 1.5 1.1 2.3 2.5 2.3.9 0 1.3-.4 2-.4s1.1.4 2 .4c1.4 0 2.5-.8 2.5-2.3C16.5 13 14.5 11 12 11z"/></svg></div>
                        <div><p class="font-semibold text-slate-800">{{ $m->nombre }}</p><p class="text-xs text-slate-400">{{ $m->especie }} · {{ $m->raza ?: 'Sin raza' }}</p></div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
