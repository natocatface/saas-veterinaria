@extends('layouts.admin')

@section('title', $empresa->nombre)
@section('subtitle', 'Ficha de la empresa')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
    <a href="{{ route('admin.empresas.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-iris-600">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Volver a empresas
    </a>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.empresas.pago.create', $empresa) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
            Registrar pago
        </a>
        <a href="{{ route('admin.empresas.edit', $empresa) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">Editar</a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-iris-50 text-iris-700 flex items-center justify-center text-2xl font-extrabold">{{ mb_strtoupper(mb_substr($empresa->nombre,0,1)) }}</div>
            <div>
                <h2 class="text-xl font-extrabold text-slate-800">{{ $empresa->nombre }}</h2>
                <span class="text-xs px-2.5 py-0.5 rounded-full font-semibold
                    @if($empresa->estado==='activa') bg-emerald-100 text-emerald-700
                    @elseif($empresa->estado==='suspendida') bg-red-100 text-red-700
                    @else bg-amber-100 text-amber-700 @endif">{{ $empresa->estadoLabel() }}</span>
            </div>
        </div>
        <dl class="mt-6 space-y-3 text-sm">
            <div class="flex justify-between"><dt class="text-slate-400">RUC</dt><dd class="font-semibold text-slate-700">{{ $empresa->ruc ?: '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Plan</dt><dd class="font-semibold text-slate-700">{{ optional($empresa->plan)->nombre ?: '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Telefono</dt><dd class="font-semibold text-slate-700">{{ $empresa->telefono ?: '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Correo</dt><dd class="font-semibold text-slate-700">{{ $empresa->email ?: '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Inicio</dt><dd class="font-semibold text-slate-700">{{ optional($empresa->fecha_inicio)->format('d/m/Y') ?: '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">Vence</dt><dd class="font-semibold {{ $empresa->estaVencida() ? 'text-red-600' : 'text-slate-700' }}">{{ optional($empresa->fecha_vencimiento)->format('d/m/Y') ?: '—' }}</dd></div>
        </dl>
    </div>

    <div class="lg:col-span-2 space-y-6">
        <!-- Usuarios -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
            <h3 class="text-lg font-extrabold text-slate-800 mb-4">Usuarios ({{ $empresa->usuarios->count() }})</h3>
            @if ($empresa->usuarios->isEmpty())
                <p class="text-sm text-slate-400 py-6 text-center">Esta empresa no tiene usuarios.</p>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach ($empresa->usuarios as $u)
                        <div class="flex items-center gap-3 py-3">
                            <div class="w-10 h-10 rounded-full bg-iris-600 text-white flex items-center justify-center font-bold text-xs">{{ $u->iniciales() }}</div>
                            <div class="flex-1 min-w-0"><p class="font-semibold text-slate-800 truncate">{{ $u->name }}</p><p class="text-xs text-slate-400">{{ $u->email }}</p></div>
                            <span class="text-xs px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 font-semibold">{{ $u->rolLabel() }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Historial de pagos -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
            <h3 class="text-lg font-extrabold text-slate-800 mb-4">Historial de pagos</h3>
            @if ($empresa->pagos->isEmpty())
                <p class="text-sm text-slate-400 py-6 text-center">Sin pagos registrados. Usa "Registrar pago" para renovar la suscripcion.</p>
            @else
                <div class="overflow-x-auto -mx-2">
                    <table class="w-full text-sm">
                        <thead><tr class="text-left text-xs font-bold tracking-wide text-slate-400 border-b border-slate-100">
                            <th class="px-2 py-2">FECHA</th><th class="px-2 py-2">PERIODO</th><th class="px-2 py-2">VIGENCIA</th><th class="px-2 py-2 text-right">MONTO</th>
                        </tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($empresa->pagos as $pago)
                                <tr>
                                    <td class="px-2 py-2 text-slate-600">{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                                    <td class="px-2 py-2 text-slate-600">{{ ucfirst($pago->periodo) }}</td>
                                    <td class="px-2 py-2 text-slate-500 text-xs">{{ $pago->fecha_inicio->format('d/m/y') }} → {{ $pago->fecha_fin->format('d/m/y') }}</td>
                                    <td class="px-2 py-2 text-right font-bold text-slate-800">S/ {{ number_format($pago->monto, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
