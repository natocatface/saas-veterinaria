@extends('layouts.admin')

@section('title', 'Planes')
@section('subtitle', 'Planes de suscripcion de la plataforma')

@section('content')
@php
    // Paleta por nivel (se cicla segun el orden de los planes)
    $temas = [
        ['grad' => 'from-slate-500 to-slate-700',  'chip' => 'bg-slate-100 text-slate-600',   'check' => 'text-slate-400',   'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-2.8 0-5 1.8-5 4s2.2 4 5 4 5-1.8 5-4-2.2-4-5-4z"/>'],
        ['grad' => 'from-iris-500 to-iris-700',    'chip' => 'bg-iris-50 text-iris-700',       'check' => 'text-iris-500',    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13 2L4 14h6l-1 8 9-12h-6l1-8z"/>'],
        ['grad' => 'from-amber-500 to-orange-600', 'chip' => 'bg-amber-50 text-amber-700',     'check' => 'text-amber-500',   'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 16l-2-9 6 4 3-6 3 6 6-4-2 9M5 20h14"/>'],
        ['grad' => 'from-emerald-500 to-green-600','chip' => 'bg-emerald-50 text-emerald-700', 'check' => 'text-emerald-500', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 3v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6z"/>'],
    ];
    $destacado = $planes->count() ? intdiv($planes->count() - 1, 2) : 0; // el del medio
@endphp

<div class="flex flex-wrap items-end justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800">Elige la estructura de precios</h2>
        <p class="text-slate-400 mt-1">Define los planes que las clinicas podran contratar.</p>
    </div>
    <a href="{{ route('admin.planes.create') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-iris-600 text-white font-semibold hover:bg-iris-700 shadow-lg shadow-indigo-500/30 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
        Nuevo Plan
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 items-start">
    @forelse ($planes as $plan)
        @php $t = $temas[$loop->index % count($temas)]; $isFeat = $loop->index === $destacado; @endphp
        <div class="relative group rounded-3xl bg-white transition-all duration-300 hover:-translate-y-1
                    {{ $isFeat ? 'ring-2 ring-iris-500 shadow-2xl shadow-indigo-500/20 xl:-mt-2' : 'border border-slate-100 shadow-sm hover:shadow-xl' }}">

            @if ($isFeat)
                <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gradient-to-r from-iris-500 to-iris-700 text-white text-xs font-bold shadow-lg">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.9 6.3 6.9.6-5.2 4.6 1.6 6.7L12 17.3 5.8 20.8l1.6-6.7L2.2 8.9l6.9-.6z"/></svg>
                        Mas popular
                    </span>
                </div>
            @endif

            <div class="p-7">
                <!-- Cabecera -->
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br {{ $t['grad'] }} text-white flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $t['icon'] !!}</svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-extrabold text-slate-800">{{ $plan->nombre }}</h3>
                            <p class="text-xs text-slate-400">{{ $plan->empresas_count }} empresa(s)</p>
                        </div>
                    </div>
                    @if($plan->activo)
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 ring-4 ring-emerald-100" title="Activo"></span>
                    @else
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-300 ring-4 ring-slate-100" title="Inactivo"></span>
                    @endif
                </div>

                <!-- Precio -->
                <div class="mt-6 flex items-end gap-1">
                    <span class="text-sm font-bold text-slate-400 mb-2">S/</span>
                    <span class="text-5xl font-extrabold text-slate-800 tracking-tight">{{ number_format($plan->precio, 0) }}</span>
                    <span class="text-sm text-slate-400 mb-2">/{{ $plan->periodo === 'anual' ? 'ano' : 'mes' }}</span>
                </div>
                @if ($plan->precio > 0 && $plan->periodo === 'mensual')
                    <p class="text-xs text-slate-400 mt-1">Equivale a S/ {{ number_format($plan->precio * 12, 0) }} al ano</p>
                @else
                    <p class="text-xs text-slate-400 mt-1">&nbsp;</p>
                @endif

                <div class="my-6 h-px bg-slate-100"></div>

                <!-- Caracteristicas -->
                <ul class="space-y-3 text-sm text-slate-600 min-h-[9rem]">
                    <li class="flex items-center gap-3">
                        <span class="w-5 h-5 rounded-full {{ $t['chip'] }} flex items-center justify-center shrink-0"><svg class="w-3.5 h-3.5 {{ $t['check'] }}" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                        <span><strong class="text-slate-800">{{ $plan->limiteUsuarios() }}</strong> usuarios</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="w-5 h-5 rounded-full {{ $t['chip'] }} flex items-center justify-center shrink-0"><svg class="w-3.5 h-3.5 {{ $t['check'] }}" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                        <span><strong class="text-slate-800">{{ $plan->limitePacientes() }}</strong> pacientes</span>
                    </li>
                    @if($plan->caracteristicas)
                        @foreach (array_filter(array_map('trim', explode("\n", $plan->caracteristicas))) as $c)
                            <li class="flex items-center gap-3">
                                <span class="w-5 h-5 rounded-full {{ $t['chip'] }} flex items-center justify-center shrink-0"><svg class="w-3.5 h-3.5 {{ $t['check'] }}" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                                <span>{{ $c }}</span>
                            </li>
                        @endforeach
                    @endif
                </ul>

                <!-- Acciones -->
                <div class="mt-7 flex items-center gap-2">
                    <a href="{{ route('admin.planes.edit', $plan) }}"
                       class="flex-1 text-center px-4 py-3 rounded-xl font-semibold text-sm transition
                       {{ $isFeat ? 'bg-iris-600 text-white hover:bg-iris-700 shadow-lg shadow-indigo-500/25' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        Editar plan
                    </a>
                    <form method="POST" action="{{ route('admin.planes.destroy', $plan) }}" onsubmit="return confirm('Eliminar el plan {{ $plan->nombre }}?')">
                        @csrf @method('DELETE')
                        <button class="p-3 rounded-xl bg-red-50 text-red-500 hover:bg-red-100 transition" title="Eliminar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m2 0v12a1 1 0 01-1 1H7a1 1 0 01-1-1V7"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="md:col-span-2 xl:col-span-3 bg-white rounded-3xl border border-slate-100 shadow-sm p-16 text-center">
            <div class="w-16 h-16 rounded-2xl bg-iris-50 text-iris-500 flex items-center justify-center mx-auto mb-4"><svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 7l2 13a1 1 0 001 1h12a1 1 0 001-1l2-13M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></div>
            <p class="text-lg font-bold text-slate-700">Aun no hay planes</p>
            <p class="text-sm text-slate-400 mt-1 mb-6">Crea el primer plan de suscripcion para tus clinicas.</p>
            <a href="{{ route('admin.planes.create') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-iris-600 text-white font-semibold hover:bg-iris-700">Crear primer plan</a>
        </div>
    @endforelse
</div>
@endsection
