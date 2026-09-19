@extends('layouts.app')

@section('title', 'Reporte de Facturacion Electronica')
@section('subtitle', 'Estado de los comprobantes ante SUNAT')

@section('content')
@php
    $sunatBadge = function ($estado) {
        return match ($estado) {
            'aceptado' => ['bg-emerald-100 text-emerald-700', 'Aceptado'],
            'rechazado' => ['bg-red-100 text-red-700', 'Rechazado'],
            'generado' => ['bg-sky-100 text-sky-700', 'Generado'],
            'pendiente' => ['bg-amber-100 text-amber-700', 'Pendiente'],
            default => ['bg-slate-100 text-slate-600', 'Sin emitir'],
        };
    };
@endphp

<div class="flex items-center justify-between gap-3 mb-6 no-print">
    <a href="{{ route('reportes.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand-600">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Volver a Reportes
    </a>
    <div class="flex items-center gap-2">
        <a href="{{ route('resumenes.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3v3M17 3v3M4 8h16M5 6h14a1 1 0 011 1v12a1 1 0 01-1 1H5a1 1 0 01-1-1V7a1 1 0 011-1z"/></svg>
            Resumenes de boletas
        </a>
        <a href="{{ route('facturacion.config.edit') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9a3 3 0 100 6 3 3 0 000-6zM19 12a7 7 0 00-.1-1.2l2-1.6-2-3.4-2.4 1a7 7 0 00-2-1.2l-.4-2.6h-4l-.4 2.6a7 7 0 00-2 1.2l-2.4-1-2 3.4 2 1.6A7 7 0 005 12c0 .4 0 .8.1 1.2l-2 1.6 2 3.4 2.4-1c.6.5 1.3.9 2 1.2l.4 2.6h4l.4-2.6c.7-.3 1.4-.7 2-1.2l2.4 1 2-3.4-2-1.6z"/></svg>
            Configuracion SUNAT
        </a>
    </div>
</div>

{{-- KPIs --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <p class="text-xs font-bold text-slate-400 tracking-wide">TOTAL COMPROBANTES</p>
        <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $resumen['total'] }}</p>
        <p class="text-xs text-slate-400 mt-1">Boletas y facturas</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <p class="text-xs font-bold text-emerald-500 tracking-wide">ACEPTADOS</p>
        <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ $resumen['aceptados'] }}</p>
        <p class="text-xs text-slate-400 mt-1">S/ {{ number_format($resumen['monto_aceptado'], 2) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <p class="text-xs font-bold text-red-500 tracking-wide">RECHAZADOS</p>
        <p class="text-3xl font-extrabold text-red-600 mt-1">{{ $resumen['rechazados'] }}</p>
        <p class="text-xs text-slate-400 mt-1">Requieren correccion</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <p class="text-xs font-bold text-amber-500 tracking-wide">PENDIENTES</p>
        <p class="text-3xl font-extrabold text-amber-600 mt-1">{{ $resumen['pendientes'] }}</p>
        <p class="text-xs text-slate-400 mt-1">Por enviar a SUNAT</p>
    </div>
</div>

{{-- Desglose por tipo --}}
@if ($porTipo->count())
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        @foreach ($porTipo as $t)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-slate-700">{{ ucfirst($t->tipo) }}</p>
                    <p class="text-xs text-slate-400">{{ $t->cantidad }} comprobante(s)</p>
                </div>
                <p class="text-lg font-extrabold text-brand-700">S/ {{ number_format($t->total, 2) }}</p>
            </div>
        @endforeach
    </div>
@endif

{{-- Filtros --}}
<form method="GET" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 mb-6 no-print">
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 items-end">
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Desde</label>
            <input type="date" name="desde" value="{{ $desde }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm outline-none focus:border-brand-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Hasta</label>
            <input type="date" name="hasta" value="{{ $hasta }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm outline-none focus:border-brand-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Tipo</label>
            <select name="tipo" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm outline-none focus:border-brand-500 bg-white">
                <option value="">Todos</option>
                <option value="boleta" @selected($tipo === 'boleta')>Boleta</option>
                <option value="factura" @selected($tipo === 'factura')>Factura</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">Estado SUNAT</label>
            <select name="sunat" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm outline-none focus:border-brand-500 bg-white">
                <option value="">Todos</option>
                <option value="aceptado" @selected($sunat === 'aceptado')>Aceptado</option>
                <option value="rechazado" @selected($sunat === 'rechazado')>Rechazado</option>
                <option value="pendiente" @selected($sunat === 'pendiente')>Pendiente</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button class="flex-1 px-4 py-2 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700">Filtrar</button>
            @if ($desde || $hasta || $tipo || $sunat)
                <a href="{{ route('reportes.facturacion') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-sm font-semibold hover:bg-slate-200">Limpiar</a>
            @endif
        </div>
    </div>
</form>

{{-- Tabla --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-slate-400 font-semibold border-b border-slate-100 bg-slate-50">
                    <th class="px-6 py-4">COMPROBANTE</th>
                    <th class="px-6 py-4">CLIENTE</th>
                    <th class="px-6 py-4">FECHA</th>
                    <th class="px-6 py-4 text-right">TOTAL</th>
                    <th class="px-6 py-4 text-center">SUNAT</th>
                    <th class="px-6 py-4 text-right no-print">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($comprobantes as $c)
                    @php [$badgeCls, $badgeTxt] = $sunatBadge($c->sunat_estado); @endphp
                    <tr class="border-b border-slate-50 hover:bg-slate-50/60">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-slate-800">{{ $c->numeroFormateado() }}</p>
                            <p class="text-xs text-slate-400">{{ $c->tipoLabel() }}</p>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ optional($c->cliente)->nombre ?: 'Cliente varios' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $c->fecha->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 text-right font-bold text-slate-800">S/ {{ number_format($c->total, 2) }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs px-2.5 py-1 rounded-full font-semibold {{ $badgeCls }}">{{ $badgeTxt }}</span>
                            @if ($c->sunat_estado === 'rechazado' && $c->sunat_mensaje)
                                <p class="text-[10px] text-red-400 mt-1 max-w-[160px] mx-auto truncate" title="{{ $c->sunat_mensaje }}">{{ $c->sunat_mensaje }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 no-print">
                            <div class="flex items-center justify-end gap-2 text-xs">
                                <a href="{{ route('comprobantes.show', $c) }}" class="text-brand-600 font-semibold hover:underline">Ver</a>
                                <a href="{{ route('comprobantes.pdf', $c) }}" target="_blank" class="text-slate-500 hover:underline">PDF</a>
                                @if ($c->sunat_xml_path)<a href="{{ route('comprobantes.xml', $c) }}" class="text-slate-500 hover:underline">XML</a>@endif
                                @if ($c->sunat_cdr_path)<a href="{{ route('comprobantes.cdr', $c) }}" class="text-slate-500 hover:underline">CDR</a>@endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-16 text-center text-slate-400">No hay comprobantes electronicos en el rango seleccionado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($comprobantes->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 no-print">{{ $comprobantes->links() }}</div>
    @endif
</div>
@endsection
