@extends('layouts.app')

@section('title', 'Mi Suscripcion')
@section('subtitle', 'Plan, consumo y facturacion de tu clinica')

@section('content')
@php
    $plan = $empresa->plan;
    $maxU = (int) optional($plan)->max_usuarios;
    $maxP = (int) optional($plan)->max_pacientes;
    $pctU = $maxU > 0 ? min(100, round($usoUsuarios / $maxU * 100)) : 0;
    $pctP = $maxP > 0 ? min(100, round($usoPacientes / $maxP * 100)) : 0;
@endphp

<!-- Estado actual -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-6 mb-6">
    <div class="bg-gradient-to-br from-brand-700 to-brand-900 text-white rounded-3xl p-6 shadow-lg">
        <p class="text-xs font-bold tracking-widest text-brand-200">TU PLAN</p>
        <p class="text-3xl font-extrabold mt-2">{{ optional($plan)->nombre ?: 'Sin plan' }}</p>
        <p class="text-brand-100 mt-1">{{ $plan && $plan->precio > 0 ? 'S/ '.number_format($plan->precio, 2).' /'.$plan->periodo : 'Gratis' }}</p>
        <span class="inline-block mt-4 px-3 py-1 rounded-full bg-white/15 text-xs font-semibold">{{ $empresa->estadoLabel() }}</span>
    </div>

    <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-extrabold text-slate-800">Consumo del plan</h3>
            @if ($empresa->fecha_vencimiento)
                <span class="text-sm {{ $empresa->estaVencida() ? 'text-red-600' : 'text-slate-500' }}">Vence: <strong>{{ $empresa->fecha_vencimiento->format('d/m/Y') }}</strong></span>
            @endif
        </div>
        <div class="space-y-5">
            <div>
                <div class="flex justify-between text-sm mb-1.5"><span class="text-slate-600 font-medium">Usuarios</span><span class="text-slate-400">{{ $usoUsuarios }} / {{ $maxU > 0 ? $maxU : 'Ilimitado' }}</span></div>
                <div class="h-2.5 rounded-full bg-slate-100 overflow-hidden"><div class="h-full rounded-full {{ $pctU >= 90 ? 'bg-red-500' : 'bg-brand-500' }}" style="width: {{ $maxU > 0 ? $pctU : 12 }}%"></div></div>
            </div>
            <div>
                <div class="flex justify-between text-sm mb-1.5"><span class="text-slate-600 font-medium">Pacientes</span><span class="text-slate-400">{{ $usoPacientes }} / {{ $maxP > 0 ? $maxP : 'Ilimitado' }}</span></div>
                <div class="h-2.5 rounded-full bg-slate-100 overflow-hidden"><div class="h-full rounded-full {{ $pctP >= 90 ? 'bg-red-500' : 'bg-emerald-500' }}" style="width: {{ $maxP > 0 ? $pctP : 12 }}%"></div></div>
            </div>
        </div>
    </div>
</div>

<!-- Cambiar de plan -->
<h3 class="text-lg font-extrabold text-slate-800 mb-4">Cambiar de plan</h3>
@php $destacado = $planes->count() ? intdiv($planes->count() - 1, 2) : 0; @endphp
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
    @foreach ($planes as $p)
        @php $actual = (int) $empresa->plan_id === $p->id; $isFeat = $loop->index === $destacado; @endphp
        <div class="relative rounded-3xl bg-white p-6 transition
                    {{ $actual ? 'ring-2 ring-brand-500 shadow-lg' : ($isFeat ? 'ring-1 ring-slate-200 shadow-md' : 'border border-slate-100 shadow-sm') }}">
            @if ($actual)
                <span class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full bg-brand-600 text-white text-xs font-bold shadow">Plan actual</span>
            @endif
            <h4 class="text-lg font-extrabold text-slate-800">{{ $p->nombre }}</h4>
            <p class="mt-2"><span class="text-3xl font-extrabold text-slate-800">S/ {{ number_format($p->precio, 0) }}</span><span class="text-sm text-slate-400">/{{ $p->periodo === 'anual' ? 'ano' : 'mes' }}</span></p>
            <ul class="mt-4 space-y-2 text-sm text-slate-600">
                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $p->limiteUsuarios() }} usuarios</li>
                <li class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $p->limitePacientes() }} pacientes</li>
                @foreach (array_slice(array_filter(array_map('trim', explode("\n", (string) $p->caracteristicas))), 0, 3) as $c)
                    <li class="flex items-center gap-2"><svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>{{ $c }}</li>
                @endforeach
            </ul>
            @if ($actual)
                <div class="mt-6 w-full text-center px-4 py-2.5 rounded-xl bg-slate-100 text-slate-400 font-semibold text-sm cursor-default">Plan activo</div>
            @else
                <form method="POST" action="{{ route('suscripcion.plan') }}" onsubmit="return confirm('Cambiar al plan {{ $p->nombre }}?')" class="mt-6">
                    @csrf @method('PUT')
                    <input type="hidden" name="plan_id" value="{{ $p->id }}">
                    <button class="w-full px-4 py-2.5 rounded-xl {{ $isFeat ? 'bg-brand-600 text-white hover:bg-brand-700 shadow-lg shadow-brand-500/25' : 'bg-slate-800 text-white hover:bg-slate-900' }} font-semibold text-sm">
                        {{ $p->precio > (optional($empresa->plan)->precio ?? 0) ? 'Mejorar a este plan' : 'Cambiar a este plan' }}
                    </button>
                </form>
            @endif
        </div>
    @endforeach
</div>

<p class="text-xs text-slate-400 mt-6">Los cambios a un plan de pago se activan y el equipo de la plataforma registra el cobro correspondiente. Un plan con menor limite requiere estar dentro del consumo actual.</p>
@endsection
