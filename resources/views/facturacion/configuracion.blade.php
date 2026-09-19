@extends('layouts.app')

@section('title', 'Facturacion Electronica')
@section('subtitle', 'Emision de comprobantes electronicos ante SUNAT')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- ===================== Cabecera ===================== --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-700 to-brand-900 text-white shadow-lg">
        <div class="absolute -right-10 -top-10 w-52 h-52 rounded-full bg-white/5"></div>
        <div class="absolute right-16 bottom-0 w-40 h-40 rounded-full bg-white/5"></div>

        <div class="relative p-6 md:p-8">
            <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/15 flex items-center justify-center shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v12a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1zM14 3v5h5M9 13h6M9 17h6"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-xl md:text-2xl font-extrabold">Facturacion Electronica</h2>
                        <span class="text-lg">&#127477;&#127466;</span>
                        <span class="text-sm text-brand-100 font-semibold">Peru</span>
                    </div>
                    <p class="text-sm text-brand-100/90 mt-1 max-w-xl">
                        Emision de comprobantes electronicos ante SUNAT &middot; UBL 2.1 &middot; Boletas, facturas y notas de credito.
                    </p>
                </div>
                <div class="text-right shrink-0">
                    <span class="inline-block px-3 py-1 rounded-lg bg-white/15 text-xs font-bold tracking-wide">SUNAT</span>
                    <p class="text-[11px] text-brand-100/80 mt-1">Comprobantes de Pago Electronicos</p>
                </div>
            </div>

            {{-- Badges de estado --}}
            <div class="flex flex-wrap items-center gap-2 mt-6">
                @if ($badges['habilitada'])
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-400/20 text-emerald-50 text-xs font-semibold ring-1 ring-emerald-300/40">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span> Habilitada
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-brand-50 text-xs font-semibold ring-1 ring-white/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Deshabilitada
                    </span>
                @endif

                <span class="px-3 py-1 rounded-full bg-white/10 text-brand-50 text-xs font-semibold ring-1 ring-white/20">Driver: {{ $badges['driver'] }}</span>
                <span class="px-3 py-1 rounded-full bg-white/10 text-brand-50 text-xs font-semibold ring-1 ring-white/20">Modo: {{ $badges['entorno'] }}</span>

                @if ($badges['certificado'])
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-400/20 text-emerald-50 text-xs font-semibold ring-1 ring-emerald-300/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Certificado encontrado
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-400/20 text-rose-50 text-xs font-semibold ring-1 ring-rose-300/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Certificado no encontrado
                    </span>
                @endif

                <form method="POST" action="{{ route('facturacion.config.probar') }}" class="ml-auto">
                    @csrf
                    <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-brand-700 text-sm font-bold hover:bg-brand-50 shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Probar conexion con SUNAT
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Resultado de la prueba de conexion --}}
    @if (session()->has('probar_msg'))
        <div class="rounded-2xl border px-5 py-4 text-sm {{ session('probar_ok') ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-amber-50 border-amber-200 text-amber-800' }}">
            <p class="font-semibold">{{ session('probar_msg') }}</p>
            @if (session('probar_checks'))
                <ul class="mt-2 space-y-1">
                    @foreach (session('probar_checks') as $chk)
                        <li class="flex items-center gap-2">
                            @if ($chk['ok'])
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            @else
                                <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            @endif
                            <span>{{ $chk['texto'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl px-5 py-4 text-sm">
            <ul class="list-disc list-inside space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('facturacion.config.update') }}" class="space-y-6">
        @csrf @method('PUT')

        {{-- ===================== Estado y modo ===================== --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800">Estado y modo</h3>
                    <p class="text-sm text-slate-400">Activacion, forma de emision y entorno de SUNAT</p>
                </div>
            </div>

            <label class="flex items-start gap-3 p-4 rounded-2xl border border-slate-100 hover:border-brand-200 cursor-pointer transition">
                <input type="checkbox" name="habilitada" value="1" @checked(old('habilitada', $config->habilitada))
                    class="mt-0.5 w-5 h-5 rounded border-slate-300 text-brand-600 focus:ring-brand-200">
                <span>
                    <span class="block font-semibold text-slate-700">Habilitar facturacion electronica</span>
                    <span class="block text-sm text-slate-400">Si esta desactivada, las ventas no generan comprobante ante SUNAT.</span>
                </span>
            </label>

            <label class="flex items-start gap-3 p-4 mt-3 rounded-2xl border border-slate-100 hover:border-brand-200 cursor-pointer transition">
                <input type="checkbox" name="emitir_automatico" value="1" @checked(old('emitir_automatico', $config->emitir_automatico))
                    class="mt-0.5 w-5 h-5 rounded border-slate-300 text-brand-600 focus:ring-brand-200">
                <span>
                    <span class="block font-semibold text-slate-700">Emitir automaticamente al cerrar la venta</span>
                    <span class="block text-sm text-slate-400">Cada boleta o factura se envia apenas se registra el comprobante.</span>
                </span>
            </label>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Driver de emision</label>
                    <select name="driver" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none bg-white">
                        <option value="ninguno" @selected(old('driver', $config->driver) === 'ninguno')>Ninguno (no emite, deja pendiente)</option>
                        <option value="beta" @selected(old('driver', $config->driver) === 'beta')>Beta (genera XML UBL 2.1, sin enviar)</option>
                        <option value="greenter" @selected(old('driver', $config->driver) === 'greenter')>Greenter (firma y envia a SUNAT)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Entorno SUNAT</label>
                    <select name="entorno" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none bg-white">
                        <option value="beta" @selected(old('entorno', $config->entorno) === 'beta')>Beta (homologacion / pruebas)</option>
                        <option value="produccion" @selected(old('entorno', $config->entorno) === 'produccion')>Produccion</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- ===================== Datos del emisor ===================== --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 21V5a1 1 0 011-1h9a1 1 0 011 1v16M15 8h4a1 1 0 011 1v12M4 21h16M8 8h3M8 12h3M8 16h3"/></svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800">Datos del emisor</h3>
                    <p class="text-sm text-slate-400">Aparecen en el comprobante electronico</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">RUC <span class="text-rose-500">*</span></label>
                    <input name="ruc" value="{{ old('ruc', $config->ruc) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Razon social <span class="text-rose-500">*</span></label>
                    <input name="razon_social" value="{{ old('razon_social', $config->razon_social) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nombre comercial</label>
                    <input name="nombre_comercial" value="{{ old('nombre_comercial', $config->nombre_comercial) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Direccion fiscal</label>
                    <input name="direccion_fiscal" value="{{ old('direccion_fiscal', $config->direccion_fiscal) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Ubigeo</label>
                    <input name="ubigeo" value="{{ old('ubigeo', $config->ubigeo) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Departamento</label>
                    <input name="departamento" value="{{ old('departamento', $config->departamento) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Provincia</label>
                    <input name="provincia" value="{{ old('provincia', $config->provincia) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Distrito</label>
                    <input name="distrito" value="{{ old('distrito', $config->distrito) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-5 mt-5 border-t border-slate-100 pt-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Serie boleta</label>
                    <input name="serie_boleta" value="{{ old('serie_boleta', $config->serie_boleta) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Serie factura</label>
                    <input name="serie_factura" value="{{ old('serie_factura', $config->serie_factura) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Serie nota credito</label>
                    <input name="serie_nota_credito" value="{{ old('serie_nota_credito', $config->serie_nota_credito) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                </div>
            </div>
        </div>

        {{-- ===================== Credenciales SUNAT ===================== --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a4 4 0 11-4 4M11 11L4 18v3h3l1-1v-2h2v-2h2l1-1"/></svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-800">Credenciales SUNAT</h3>
                    <p class="text-sm text-slate-400">Clave SOL y certificado digital</p>
                </div>
            </div>

            <div class="flex items-start gap-2 bg-brand-50 text-brand-800 rounded-2xl px-4 py-3 text-sm mb-5">
                <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M12 3a9 9 0 100 18 9 9 0 000-18z"/></svg>
                <span>En <strong>beta</strong> puedes usar RUC <strong>20000000001</strong> con usuario y clave <strong>MODDATOS</strong>.</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Usuario Clave SOL</label>
                    <input name="sol_usuario" value="{{ old('sol_usuario', $config->sol_usuario) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Clave SOL</label>
                    <input type="password" name="sol_clave" placeholder="{{ $config->sol_clave ? '********' : '' }}" autocomplete="new-password" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                    <p class="text-xs text-slate-400 mt-1">Deja en blanco para conservar la actual.</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Ruta del certificado (.pem)</label>
                    <input name="certificado_path" value="{{ old('certificado_path', $config->certificado_path ?: $config->rutaCertificado()) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none font-mono text-sm">
                    @if ($config->certificadoExiste())
                        <p class="flex items-center gap-1.5 text-xs text-emerald-600 mt-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Certificado encontrado en la ruta indicada.
                        </p>
                    @else
                        <p class="flex items-center gap-1.5 text-xs text-rose-500 mt-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 3.9l-8 14A2 2 0 004 21h16a2 2 0 001.7-3l-8-14a2 2 0 00-3.4 0z"/></svg>
                            No se encontro el certificado en la ruta indicada.
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===================== Barra de acciones ===================== --}}
        <div class="flex items-center justify-between bg-white rounded-3xl border border-slate-100 shadow-sm px-6 py-4">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-700 font-semibold text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Volver
            </a>
            <button class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-700 shadow-lg shadow-brand-500/25">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Guardar configuracion
            </button>
        </div>
    </form>
</div>
@endsection
