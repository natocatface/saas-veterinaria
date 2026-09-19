@extends('layouts.app')

@section('title', 'Control de Caja')
@section('subtitle', 'Apertura, movimientos y cierre')

@section('content')
@if ($cajaActual)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4"><p class="text-xs text-slate-400 font-semibold">APERTURA</p><p class="text-xl font-extrabold text-slate-800 mt-1">S/ {{ number_format($cajaActual->monto_apertura, 2) }}</p></div>
                <div class="bg-emerald-50 rounded-2xl border border-emerald-100 p-4"><p class="text-xs text-emerald-600 font-semibold">INGRESOS</p><p class="text-xl font-extrabold text-emerald-700 mt-1">S/ {{ number_format($cajaActual->totalIngresos(), 2) }}</p></div>
                <div class="bg-red-50 rounded-2xl border border-red-100 p-4"><p class="text-xs text-red-600 font-semibold">EGRESOS</p><p class="text-xl font-extrabold text-red-700 mt-1">S/ {{ number_format($cajaActual->totalEgresos(), 2) }}</p></div>
                <div class="bg-brand-600 rounded-2xl p-4 text-white"><p class="text-xs text-white/80 font-semibold">SALDO</p><p class="text-xl font-extrabold mt-1">S/ {{ number_format($cajaActual->saldoEsperado(), 2) }}</p></div>
            </div>

            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-slate-800">Movimientos</h3>
                        <p class="text-xs text-slate-400">Caja abierta el {{ $cajaActual->fecha_apertura->format('d/m/Y H:i') }} por {{ optional($cajaActual->usuario)->name }}</p>
                    </div>
                </div>
                <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                    @forelse ($cajaActual->movimientos as $mov)
                        <div class="flex items-center gap-4 px-6 py-3">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center {{ $mov->tipo==='ingreso' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $mov->tipo==='ingreso' ? 'M12 19V5M5 12l7-7 7 7' : 'M12 5v14M5 12l7 7 7-7' }}"/></svg>
                            </div>
                            <div class="flex-1 min-w-0"><p class="font-semibold text-slate-700 truncate">{{ $mov->concepto }}</p><p class="text-xs text-slate-400">{{ $mov->created_at->format('H:i') }}</p></div>
                            <p class="font-bold {{ $mov->tipo==='ingreso' ? 'text-emerald-600' : 'text-red-600' }}">{{ $mov->tipo==='ingreso' ? '+' : '−' }} S/ {{ number_format($mov->monto, 2) }}</p>
                        </div>
                    @empty
                        <p class="px-6 py-10 text-center text-sm text-slate-400">Sin movimientos registrados aun.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <h3 class="font-extrabold text-slate-800 mb-4">Registrar movimiento</h3>
                <form method="POST" action="{{ route('caja.movimiento', $cajaActual) }}" class="space-y-3">
                    @csrf @method('PATCH')
                    <select name="tipo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none bg-white text-sm">
                        <option value="ingreso">Ingreso</option>
                        <option value="egreso">Egreso</option>
                    </select>
                    <input name="concepto" required placeholder="Concepto" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none text-sm">
                    <input name="monto" type="number" step="0.01" min="0.01" required placeholder="Monto (S/)" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none text-sm">
                    <button class="w-full py-2.5 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-700 text-sm">Registrar</button>
                </form>
            </div>

            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <h3 class="font-extrabold text-slate-800 mb-2">Cerrar caja</h3>
                <p class="text-sm text-slate-400 mb-4">Saldo esperado: <span class="font-bold text-slate-700">S/ {{ number_format($cajaActual->saldoEsperado(), 2) }}</span></p>
                <form method="POST" action="{{ route('caja.cerrar', $cajaActual) }}" onsubmit="return confirm('Cerrar la caja del dia?')">
                    @csrf @method('PATCH')
                    <button class="w-full py-2.5 rounded-xl bg-slate-800 text-white font-semibold hover:bg-slate-900 text-sm">Cerrar caja</button>
                </form>
            </div>
        </div>
    </div>
@else
    <div class="max-w-md mx-auto bg-white rounded-3xl border border-slate-100 shadow-sm p-8 text-center">
        <div class="w-16 h-16 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mx-auto mb-4"><svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h12a2 2 0 012 2v2H5a2 2 0 00-2 2zM3 11h16a2 2 0 012 2v4a2 2 0 01-2 2H5a2 2 0 01-2-2zM16 14h.01"/></svg></div>
        <h3 class="text-xl font-extrabold text-slate-800">No hay caja abierta</h3>
        <p class="text-sm text-slate-400 mt-1 mb-6">Apertura la caja para registrar ventas y movimientos del dia.</p>
        <form method="POST" action="{{ route('caja.abrir') }}" class="space-y-3 text-left">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Monto de apertura (S/)</label>
                <input name="monto_apertura" type="number" step="0.01" min="0" value="0" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Notas</label>
                <input name="notas" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
            </div>
            <button class="w-full py-3 rounded-xl bg-brand-600 text-white font-bold hover:bg-brand-700 shadow-lg shadow-brand-500/25">Aperturar caja</button>
        </form>
    </div>
@endif

@if ($historial->isNotEmpty())
    <div class="mt-6 bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100"><h3 class="font-extrabold text-slate-800">Historial de cierres</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs font-bold tracking-wide text-slate-400 border-b border-slate-100 bg-slate-50/60">
                    <th class="px-6 py-3">APERTURA</th><th class="px-6 py-3">CIERRE</th><th class="px-6 py-3">RESPONSABLE</th><th class="px-6 py-3 text-right">MONTO FINAL</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($historial as $h)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-6 py-3 text-slate-600">{{ $h->fecha_apertura->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ optional($h->fecha_cierre)->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-3 text-slate-600">{{ optional($h->usuario)->name }}</td>
                            <td class="px-6 py-3 text-right font-bold text-slate-800">S/ {{ number_format($h->monto_cierre, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
