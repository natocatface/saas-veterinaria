@extends('layouts.app')

@section('title', 'Comprobante '.$comprobante->numeroFormateado())
@section('subtitle', $comprobante->tipoLabel())

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-4 print:hidden">
        <a href="{{ route('comprobantes.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Volver
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('comprobantes.pdf', $comprobante) }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v12a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1zM14 3v5h5M9 15h6M9 12h6"/></svg>PDF</a>
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V4h12v5M6 18H4a1 1 0 01-1-1v-5a1 1 0 011-1h16a1 1 0 011 1v5a1 1 0 01-1 1h-2M6 14h12v6H6z"/></svg>Imprimir</button>
            @if ($comprobante->estado !== 'anulado')
                <form method="POST" action="{{ route('comprobantes.anular', $comprobante) }}" onsubmit="return confirm('Anular este comprobante?')">@csrf @method('PATCH')
                    <button class="px-4 py-2 rounded-xl bg-red-50 text-red-600 text-sm font-semibold hover:bg-red-100">Anular</button>
                </form>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 md:p-8 border-b border-dashed border-slate-200">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-brand-600 text-white flex items-center justify-center"><svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c-2.5 0-4.5 2-4.5 4.2 0 1.5 1.1 2.3 2.5 2.3.9 0 1.3-.4 2-.4s1.1.4 2 .4c1.4 0 2.5-.8 2.5-2.3C16.5 13 14.5 11 12 11z"/></svg></div>
                    <div>
                        <p class="text-lg font-extrabold text-slate-800">{{ $config->nombre_clinica }}</p>
                        @if($config->ruc)<p class="text-xs text-slate-400">RUC: {{ $config->ruc }}</p>@endif
                        @if($config->direccion)<p class="text-xs text-slate-400">{{ $config->direccion }}</p>@endif
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-xs px-2.5 py-1 rounded-full font-semibold {{ $comprobante->estado==='anulado' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">{{ ucfirst($comprobante->estado) }}</span>
                    <p class="mt-2 text-sm font-bold text-slate-700">{{ $comprobante->tipoLabel() }}</p>
                    <p class="text-lg font-extrabold text-brand-700">{{ $comprobante->numeroFormateado() }}</p>
                </div>
            </div>
        </div>

        <div class="px-6 md:px-8 py-4 grid grid-cols-2 gap-4 text-sm border-b border-slate-100">
            <div><p class="text-xs text-slate-400 font-semibold">CLIENTE</p><p class="font-semibold text-slate-700">{{ optional($comprobante->cliente)->nombre ?: 'Cliente varios' }}</p></div>
            <div class="text-right"><p class="text-xs text-slate-400 font-semibold">FECHA</p><p class="font-semibold text-slate-700">{{ $comprobante->fecha->format('d/m/Y H:i') }}</p></div>
            <div><p class="text-xs text-slate-400 font-semibold">PAGO</p><p class="font-semibold text-slate-700">{{ ucfirst($comprobante->metodo_pago) }}</p></div>
            <div class="text-right"><p class="text-xs text-slate-400 font-semibold">ATENDIO</p><p class="font-semibold text-slate-700">{{ optional($comprobante->usuario)->name }}</p></div>
        </div>

        <div class="px-6 md:px-8 py-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-400 font-semibold border-b border-slate-100">
                        <th class="py-2">DESCRIPCION</th>
                        <th class="py-2 text-center">CANT</th>
                        <th class="py-2 text-right">P.UNIT</th>
                        <th class="py-2 text-right">IMPORTE</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($comprobante->items as $item)
                        <tr class="border-b border-slate-50">
                            <td class="py-2 text-slate-700">{{ $item->descripcion }}</td>
                            <td class="py-2 text-center text-slate-600">{{ rtrim(rtrim(number_format($item->cantidad, 2), '0'), '.') }}</td>
                            <td class="py-2 text-right text-slate-600">{{ number_format($item->precio_unitario, 2) }}</td>
                            <td class="py-2 text-right font-semibold text-slate-700">{{ number_format($item->importe, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-6 md:px-8 py-4 bg-slate-50">
            <div class="ml-auto max-w-xs space-y-1 text-sm">
                <div class="flex justify-between text-slate-500"><span>Subtotal</span><span>S/ {{ number_format($comprobante->subtotal, 2) }}</span></div>
                <div class="flex justify-between text-slate-500"><span>IGV</span><span>S/ {{ number_format($comprobante->igv, 2) }}</span></div>
                <div class="flex justify-between text-lg font-extrabold text-slate-800 pt-1 border-t border-slate-200"><span>Total</span><span>S/ {{ number_format($comprobante->total, 2) }}</span></div>
            </div>
        </div>
    </div>

    {{-- ===================== Estado SUNAT ===================== --}}
    @if ($comprobante->tipo !== 'ticket')
        @php
            $sunatEstado = $comprobante->sunat_estado ?: 'sin_emitir';
            $sunatMap = [
                'aceptado'   => ['bg' => 'bg-emerald-100 text-emerald-700', 'dot' => 'bg-emerald-500', 'txt' => 'Aceptado por SUNAT'],
                'generado'   => ['bg' => 'bg-sky-100 text-sky-700',        'dot' => 'bg-sky-500',     'txt' => 'XML generado'],
                'pendiente'  => ['bg' => 'bg-amber-100 text-amber-700',    'dot' => 'bg-amber-500',   'txt' => 'Pendiente de envio'],
                'rechazado'  => ['bg' => 'bg-red-100 text-red-700',        'dot' => 'bg-red-500',     'txt' => 'Rechazado por SUNAT'],
                'sin_emitir' => ['bg' => 'bg-slate-100 text-slate-600',    'dot' => 'bg-slate-400',   'txt' => 'Sin emitir'],
            ];
            $s = $sunatMap[$sunatEstado] ?? $sunatMap['sin_emitir'];
        @endphp
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8 mt-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v12a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1zM14 3v5h5M9 13h6M9 17h6"/></svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800">Facturacion Electronica</h3>
                        <p class="text-sm text-slate-400">Estado del comprobante ante SUNAT</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $s['bg'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $s['dot'] }}"></span> {{ $s['txt'] }}
                </span>
            </div>

            @if ($comprobante->sunat_mensaje)
                <p class="text-sm text-slate-500 bg-slate-50 rounded-xl px-4 py-3 mb-4">{{ $comprobante->sunat_mensaje }}</p>
            @endif

            @if ($comprobante->sunat_hash)
                <p class="text-xs text-slate-400 mb-4 font-mono break-all">Hash: {{ $comprobante->sunat_hash }}</p>
            @endif

            <div class="flex flex-wrap items-center gap-2 print:hidden">
                @if ($comprobante->estado !== 'anulado' && $sunatEstado !== 'aceptado')
                    <form method="POST" action="{{ route('comprobantes.sunat', $comprobante) }}">
                        @csrf
                        <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 shadow-lg shadow-brand-500/25">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v6h6M20 20v-6h-6M4 10a8 8 0 0114-3M20 14a8 8 0 01-14 3"/></svg>
                            {{ $sunatEstado === 'sin_emitir' ? 'Emitir a SUNAT' : 'Reenviar a SUNAT' }}
                        </button>
                    </form>
                @endif

                @if ($comprobante->sunat_xml_path)
                    <a href="{{ route('comprobantes.xml', $comprobante) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v2a1 1 0 001 1h14a1 1 0 001-1v-2"/></svg>
                        XML firmado
                    </a>
                @endif

                @if ($comprobante->sunat_cdr_path)
                    <a href="{{ route('comprobantes.cdr', $comprobante) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v2a1 1 0 001 1h14a1 1 0 001-1v-2"/></svg>
                        CDR (SUNAT)
                    </a>
                @endif
            </div>
        </div>

        {{-- ===================== Notas de credito ===================== --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8 mt-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6M9 8h.01M15 14h.01M7 3h10a2 2 0 012 2v14l-3-2-2 2-2-2-2 2-2-2-3 2V5a2 2 0 012-2z"/></svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800">Notas de Credito</h3>
                    <p class="text-sm text-slate-400">Anulacion o correccion electronica ante SUNAT</p>
                </div>
            </div>

            @forelse ($comprobante->notasCredito as $nc)
                @php $ncOk = $nc->sunat_estado === 'aceptado'; @endphp
                <div class="flex flex-wrap items-center justify-between gap-2 border border-slate-100 rounded-2xl px-4 py-3 mb-2">
                    <div>
                        <p class="font-semibold text-slate-700">{{ $nc->numeroFormateado() }} · <span class="text-slate-400 font-normal">{{ $nc->motivoLabel() }}</span></p>
                        @if ($nc->sunat_mensaje)<p class="text-xs text-slate-400">{{ $nc->sunat_mensaje }}</p>@endif
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs px-2.5 py-1 rounded-full font-semibold {{ $ncOk ? 'bg-emerald-100 text-emerald-700' : ($nc->sunat_estado === 'rechazado' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">{{ ucfirst($nc->sunat_estado ?: 'pendiente') }}</span>
                        @if ($nc->sunat_xml_path)<a href="{{ route('notas.xml', $nc) }}" class="text-xs text-slate-500 hover:underline">XML</a>@endif
                        @if ($nc->sunat_cdr_path)<a href="{{ route('notas.cdr', $nc) }}" class="text-xs text-slate-500 hover:underline">CDR</a>@endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-400 mb-4">Este comprobante no tiene notas de credito.</p>
            @endforelse

            @if ($comprobante->sunat_estado === 'aceptado' && $comprobante->estado !== 'anulado')
                <form method="POST" action="{{ route('notas.store', $comprobante) }}" class="mt-4 flex flex-wrap items-end gap-3 print:hidden" onsubmit="return confirm('Emitir nota de credito para este comprobante?')">
                    @csrf
                    <div class="flex-1 min-w-[220px]">
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Motivo (catalogo 09 SUNAT)</label>
                        <select name="cod_motivo" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none bg-white text-sm">
                            @foreach (\App\Models\NotaCredito::motivos() as $cod => $desc)
                                <option value="{{ $cod }}">{{ $cod }} - {{ $desc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600 shadow-lg shadow-amber-500/25">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6M7 3h10a2 2 0 012 2v14l-3-2-2 2-2-2-2 2-2-2-3 2V5a2 2 0 012-2z"/></svg>
                        Emitir nota de credito
                    </button>
                </form>
            @elseif ($comprobante->sunat_estado !== 'aceptado')
                <p class="text-xs text-slate-400 mt-2">Podras emitir una nota de credito cuando el comprobante sea aceptado por SUNAT.</p>
            @endif
        </div>

        {{-- ===================== Comunicacion de baja (solo facturas) ===================== --}}
        @if ($comprobante->tipo === 'factura')
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8 mt-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-800">Comunicacion de Baja</h3>
                        <p class="text-sm text-slate-400">Anulacion de la factura ante SUNAT (RA)</p>
                    </div>
                </div>

                @forelse ($comprobante->comunicacionesBaja as $ra)
                    <div class="flex flex-wrap items-center justify-between gap-2 border border-slate-100 rounded-2xl px-4 py-3 mb-2">
                        <div>
                            <p class="font-semibold text-slate-700">{{ $ra->identificador() }} <span class="text-slate-400 font-normal font-mono text-xs">{{ $ra->sunat_ticket ? '· ticket '.$ra->sunat_ticket : '' }}</span></p>
                            @if ($ra->sunat_mensaje)<p class="text-xs text-slate-400">{{ $ra->sunat_mensaje }}</p>@endif
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs px-2.5 py-1 rounded-full font-semibold {{ $ra->sunat_estado === 'aceptado' ? 'bg-emerald-100 text-emerald-700' : ($ra->sunat_estado === 'rechazado' ? 'bg-red-100 text-red-700' : ($ra->sunat_estado === 'enviado' ? 'bg-sky-100 text-sky-700' : 'bg-amber-100 text-amber-700')) }}">{{ ucfirst($ra->sunat_estado ?: 'pendiente') }}</span>
                            @if (in_array($ra->sunat_estado, ['enviado', 'pendiente'], true) && $ra->sunat_ticket)
                                <form method="POST" action="{{ route('baja.consultar', $ra) }}" class="print:hidden">@csrf
                                    <button class="text-xs px-3 py-1.5 rounded-lg bg-brand-50 text-brand-700 font-semibold hover:bg-brand-100">Consultar</button>
                                </form>
                            @endif
                            @if ($ra->sunat_xml_path)<a href="{{ route('baja.xml', $ra) }}" class="text-xs text-slate-500 hover:underline">XML</a>@endif
                            @if ($ra->sunat_cdr_path)<a href="{{ route('baja.cdr', $ra) }}" class="text-xs text-slate-500 hover:underline">CDR</a>@endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400 mb-4">Esta factura no tiene comunicaciones de baja.</p>
                @endforelse

                @if ($comprobante->sunat_estado === 'aceptado' && $comprobante->estado !== 'anulado')
                    <form method="POST" action="{{ route('baja.store', $comprobante) }}" class="mt-4 flex flex-wrap items-end gap-3 print:hidden" onsubmit="return confirm('Enviar comunicacion de baja de esta factura a SUNAT?')">
                        @csrf
                        <div class="flex-1 min-w-[220px]">
                            <label class="block text-sm font-semibold text-slate-600 mb-1.5">Motivo de la baja</label>
                            <input name="motivo" value="Error en la operacion" maxlength="200" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none text-sm">
                        </div>
                        <button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-500 text-white text-sm font-semibold hover:bg-red-600 shadow-lg shadow-red-500/25">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Comunicar baja a SUNAT
                        </button>
                    </form>
                @endif
            </div>
        @endif
    @endif
</div>
@endsection
