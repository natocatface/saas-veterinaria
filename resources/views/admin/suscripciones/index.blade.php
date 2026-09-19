@extends('layouts.admin')

@section('title', 'Suscripciones')
@section('subtitle', 'Historial de pagos de la plataforma')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-gradient-to-br from-emerald-500 to-green-600 text-white rounded-2xl p-5"><p class="text-xs font-bold tracking-widest text-white/80">TOTAL RECAUDADO</p><p class="text-3xl font-extrabold mt-1">S/ {{ number_format($resumen['total'], 2) }}</p></div>
    <div class="bg-gradient-to-br from-iris-500 to-iris-700 text-white rounded-2xl p-5"><p class="text-xs font-bold tracking-widest text-white/80">ESTE MES</p><p class="text-3xl font-extrabold mt-1">S/ {{ number_format($resumen['mes'], 2) }}</p></div>
    <div class="bg-gradient-to-br from-cyan-500 to-blue-600 text-white rounded-2xl p-5"><p class="text-xs font-bold tracking-widest text-white/80">PAGOS REGISTRADOS</p><p class="text-3xl font-extrabold mt-1">{{ $resumen['cantidad'] }}</p></div>
</div>

<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-bold tracking-wide text-slate-400 border-b border-slate-100 bg-slate-50/60">
                    <th class="px-6 py-4">FECHA</th>
                    <th class="px-6 py-4">EMPRESA</th>
                    <th class="px-6 py-4">PLAN</th>
                    <th class="px-6 py-4">VIGENCIA</th>
                    <th class="px-6 py-4">METODO</th>
                    <th class="px-6 py-4 text-right">MONTO</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($pagos as $p)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-6 py-4 text-slate-600">{{ $p->fecha_pago->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ optional($p->empresa)->nombre ?: '—' }}</td>
                        <td class="px-6 py-4"><span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-semibold">{{ optional($p->plan)->nombre ?: '—' }}</span></td>
                        <td class="px-6 py-4 text-slate-500 text-xs">{{ $p->fecha_inicio->format('d/m/y') }} → {{ $p->fecha_fin->format('d/m/y') }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ ucfirst($p->metodo) }}</td>
                        <td class="px-6 py-4 text-right font-bold text-slate-800">S/ {{ number_format($p->monto, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-16 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3"><svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 7l2 13a1 1 0 001 1h12a1 1 0 001-1l2-13"/></svg></div>
                        <p class="font-bold text-slate-600">Sin pagos registrados</p>
                        <p class="text-sm text-slate-400">Registra un pago desde la ficha de una empresa.</p>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($pagos->hasPages())<div class="px-6 py-4 border-t border-slate-100">{{ $pagos->links() }}</div>@endif
</div>
@endsection
