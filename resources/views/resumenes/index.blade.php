@extends('layouts.app')

@section('title', 'Resumenes Diarios de Boletas')
@section('subtitle', 'Reporte en lote de boletas a SUNAT (RC)')

@section('content')
@php
    $badge = function ($estado) {
        return match ($estado) {
            'aceptado' => ['bg-emerald-100 text-emerald-700', 'Aceptado'],
            'rechazado' => ['bg-red-100 text-red-700', 'Rechazado'],
            'enviado' => ['bg-sky-100 text-sky-700', 'Enviado (en proceso)'],
            default => ['bg-amber-100 text-amber-700', 'Pendiente'],
        };
    };
@endphp

<div class="flex items-center justify-between gap-3 mb-6 no-print">
    <a href="{{ route('reportes.facturacion') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand-600">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Reporte de Facturacion
    </a>
</div>

{{-- Generar resumen --}}
<div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8 mb-6">
    <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3v3M17 3v3M4 8h16M5 6h14a1 1 0 011 1v12a1 1 0 01-1 1H5a1 1 0 01-1-1V7a1 1 0 011-1z"/></svg>
        </div>
        <div>
            <h3 class="font-extrabold text-slate-800">Generar resumen del dia</h3>
            <p class="text-sm text-slate-400">Reporta a SUNAT las boletas pendientes de una fecha</p>
        </div>
    </div>

    <form method="GET" class="flex flex-wrap items-end gap-3 mb-4">
        <div>
            <label class="block text-sm font-semibold text-slate-600 mb-1.5">Fecha</label>
            <input type="date" name="fecha" value="{{ $fecha }}" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
        </div>
        <p class="text-sm text-slate-500 pb-2.5">
            Boletas pendientes: <strong class="text-slate-800">{{ $boletasPendientes->count() }}</strong>
            &middot; Total: <strong class="text-slate-800">S/ {{ number_format($boletasPendientes->sum('total'), 2) }}</strong>
        </p>
    </form>

    <form method="POST" action="{{ route('resumenes.generar') }}" onsubmit="return confirm('Generar y enviar el resumen de boletas de {{ $fecha }}?')">
        @csrf
        <input type="hidden" name="fecha" value="{{ $fecha }}">
        <button {{ $boletasPendientes->count() === 0 ? 'disabled' : '' }}
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 shadow-lg shadow-brand-500/25 disabled:opacity-40 disabled:cursor-not-allowed">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
            Generar y enviar a SUNAT
        </button>
    </form>
</div>

{{-- Historial --}}
<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100">
        <h3 class="font-extrabold text-slate-800">Historial de resumenes</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-slate-400 font-semibold border-b border-slate-100 bg-slate-50">
                    <th class="px-6 py-4">RESUMEN</th>
                    <th class="px-6 py-4">FECHA BOLETAS</th>
                    <th class="px-6 py-4 text-center">BOLETAS</th>
                    <th class="px-6 py-4 text-right">TOTAL</th>
                    <th class="px-6 py-4">TICKET</th>
                    <th class="px-6 py-4 text-center">ESTADO</th>
                    <th class="px-6 py-4 text-right no-print">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($resumenes as $r)
                    @php [$cls, $txt] = $badge($r->sunat_estado); @endphp
                    <tr class="border-b border-slate-50 hover:bg-slate-50/60">
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ $r->identificador() }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $r->fecha_referencia->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-center text-slate-600">{{ $r->cantidad }}</td>
                        <td class="px-6 py-4 text-right font-bold text-slate-800">S/ {{ number_format($r->total, 2) }}</td>
                        <td class="px-6 py-4 text-slate-400 font-mono text-xs">{{ $r->sunat_ticket ?: '—' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs px-2.5 py-1 rounded-full font-semibold {{ $cls }}">{{ $txt }}</span>
                        </td>
                        <td class="px-6 py-4 no-print">
                            <div class="flex items-center justify-end gap-2 text-xs">
                                @if (in_array($r->sunat_estado, ['enviado', 'pendiente'], true) && $r->sunat_ticket)
                                    <form method="POST" action="{{ route('resumenes.consultar', $r) }}">
                                        @csrf
                                        <button class="px-3 py-1.5 rounded-lg bg-brand-50 text-brand-700 font-semibold hover:bg-brand-100">Consultar estado</button>
                                    </form>
                                @endif
                                @if ($r->sunat_xml_path)<a href="{{ route('resumenes.xml', $r) }}" class="text-slate-500 hover:underline">XML</a>@endif
                                @if ($r->sunat_cdr_path)<a href="{{ route('resumenes.cdr', $r) }}" class="text-slate-500 hover:underline">CDR</a>@endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-16 text-center text-slate-400">Aun no has generado resumenes diarios.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($resumenes->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 no-print">{{ $resumenes->links() }}</div>
    @endif
</div>
@endsection
