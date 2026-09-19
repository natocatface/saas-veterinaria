@extends('layouts.app')

@section('title', 'Facturacion')
@section('subtitle', 'Comprobantes emitidos')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-gradient-to-br from-emerald-500 to-green-600 text-white rounded-2xl p-5"><p class="text-xs font-bold tracking-widest text-white/80">INGRESOS DEL MES</p><p class="text-3xl font-extrabold mt-1">S/ {{ number_format($resumen['mes'], 2) }}</p></div>
    <div class="bg-gradient-to-br from-cyan-500 to-brand-600 text-white rounded-2xl p-5"><p class="text-xs font-bold tracking-widest text-white/80">VENTAS HOY</p><p class="text-3xl font-extrabold mt-1">S/ {{ number_format($resumen['hoy'], 2) }}</p></div>
    <div class="bg-gradient-to-br from-violet-500 to-purple-600 text-white rounded-2xl p-5"><p class="text-xs font-bold tracking-widest text-white/80">COMPROBANTES (MES)</p><p class="text-3xl font-extrabold mt-1">{{ $resumen['cantidad'] }}</p></div>
</div>

<div class="flex flex-wrap items-center gap-3 mb-6">
    <form method="GET" class="flex flex-wrap items-center gap-3 flex-1">
        <input type="date" name="fecha" value="{{ $fecha }}" onchange="this.form.submit()" class="py-2.5 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 outline-none focus:border-brand-500">
        <select name="tipo" onchange="this.form.submit()" class="py-2.5 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 outline-none focus:border-brand-500">
            <option value="">Todos los tipos</option>
            <option value="ticket" @selected($tipo==='ticket')>Ticket</option>
            <option value="boleta" @selected($tipo==='boleta')>Boleta</option>
            <option value="factura" @selected($tipo==='factura')>Factura</option>
        </select>
        <select name="estado" onchange="this.form.submit()" class="py-2.5 px-4 rounded-xl border border-slate-200 text-sm text-slate-600 outline-none focus:border-brand-500">
            <option value="">Todos</option>
            <option value="emitido" @selected($estado==='emitido')>Emitido</option>
            <option value="anulado" @selected($estado==='anulado')>Anulado</option>
        </select>
        @if($fecha || $tipo || $estado)<a href="{{ route('comprobantes.index') }}" class="text-sm text-slate-400 hover:text-brand-600">Limpiar</a>@endif
    </form>
    <a href="{{ route('export.comprobantes') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2"/></svg><span class="hidden sm:inline">Exportar</span></a>
                    <a href="{{ route('comprobantes.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-700 shadow-lg shadow-brand-500/25">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
        Nuevo Comprobante
    </a>
</div>

<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-bold tracking-wide text-slate-400 border-b border-slate-100 bg-slate-50/60">
                    <th class="px-6 py-4">COMPROBANTE</th>
                    <th class="px-6 py-4">CLIENTE</th>
                    <th class="px-6 py-4">FECHA</th>
                    <th class="px-6 py-4 text-right">TOTAL</th>
                    <th class="px-6 py-4 text-center">ESTADO</th>
                    <th class="px-6 py-4 text-right">ACCIONES</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($comprobantes as $c)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-slate-800">{{ $c->numeroFormateado() }}</p>
                            <p class="text-xs text-slate-400">{{ $c->tipoLabel() }} · {{ ucfirst($c->metodo_pago) }}</p>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ optional($c->cliente)->nombre ?: 'Cliente varios' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $c->fecha->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 text-right font-bold text-slate-800">S/ {{ number_format($c->total, 2) }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs px-2.5 py-1 rounded-full font-semibold {{ $c->estado==='anulado' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">{{ ucfirst($c->estado) }}</span>
                            @if ($c->tipo !== 'ticket' && $c->sunat_estado)
                                @php $se = $c->sunat_estado; @endphp
                                <span class="block mt-1 text-[10px] px-2 py-0.5 rounded-full font-semibold
                                    {{ $se==='aceptado' ? 'bg-emerald-50 text-emerald-600' : ($se==='rechazado' ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600') }}">
                                    SUNAT: {{ ucfirst($se) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('comprobantes.show', $c) }}" class="p-2 rounded-lg text-slate-400 hover:bg-brand-50 hover:text-brand-600" title="Ver"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg></a>
                                @if ($c->estado !== 'anulado')
                                    <form method="POST" action="{{ route('comprobantes.anular', $c) }}" onsubmit="return confirm('Anular este comprobante? Se restituira el stock.')">@csrf @method('PATCH')
                                        <button class="p-2 rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600" title="Anular"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 6L6 18M6 6l12 12"/></svg></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-16 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3"><svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v12a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1zM14 3v5h5"/></svg></div>
                        <p class="font-bold text-slate-600">Sin comprobantes</p>
                        <p class="text-sm text-slate-400">Emite el primer comprobante de venta.</p>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($comprobantes->hasPages())<div class="px-6 py-4 border-t border-slate-100">{{ $comprobantes->links() }}</div>@endif
</div>
@endsection
