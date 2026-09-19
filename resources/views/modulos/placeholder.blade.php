@extends('layouts.app')

@section('title', $titulo)
@section('subtitle', 'Modulo del sistema')

@section('content')
@php
    $icons = [
        'users'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M16 14a4 4 0 10-8 0M12 7a3 3 0 100 6 3 3 0 000-6zM3 20c0-2.5 2-4 5-4M21 20c0-2.5-2-4-5-4"/>',
        'calendar' => '<path stroke-linecap="round" stroke-linejoin="round" d="M7 3v3M17 3v3M4 8h16M5 6h14a1 1 0 011 1v12a1 1 0 01-1 1H5a1 1 0 01-1-1V7a1 1 0 011-1z"/>',
        'clipboard'=> '<path stroke-linecap="round" stroke-linejoin="round" d="M9 4h6v2H9zM7 4H6a1 1 0 00-1 1v15a1 1 0 001 1h12a1 1 0 001-1V5a1 1 0 00-1-1h-1"/>',
        'video'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.5-2.5v9L15 14M4 6h9a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1z"/>',
        'shield'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 3v6c0 5-3.4 8-8 9-4.6-1-8-4-8-9V6z"/>',
        'scissors' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6 9a3 3 0 100-6 3 3 0 000 6zM6 21a3 3 0 100-6 3 3 0 000 6zM8.5 8.5L20 20M8.5 15.5L20 4M12 12l3 3"/>',
        'box'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 16V8a2 2 0 00-1-1.7l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.7l7 4a2 2 0 002 0l7-4A2 2 0 0021 16zM3.3 7L12 12l8.7-5M12 22V12"/>',
        'document' => '<path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l5 5v12a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1zM14 3v5h5M9 13h6M9 17h6"/>',
        'wallet'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h12a2 2 0 012 2v2H5a2 2 0 00-2 2zM3 11h16a2 2 0 012 2v4a2 2 0 01-2 2H5a2 2 0 01-2-2zM16 14h.01"/>',
        'chart'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 20V10M12 20V4M19 20v-6M3 20h18"/>',
        'cog'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9a3 3 0 100 6 3 3 0 000-6z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.4 13a7 7 0 000-2l2-1.5-2-3.4-2.3 1a7 7 0 00-1.7-1l-.4-2.6h-4l-.4 2.6a7 7 0 00-1.7 1l-2.3-1-2 3.4L4.6 11a7 7 0 000 2l-2 1.5 2 3.4 2.3-1a7 7 0 001.7 1l.4 2.6h4l.4-2.6a7 7 0 001.7-1l2.3 1 2-3.4z"/>',
        'sliders'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 6h10M18 6h2M4 12h2M10 12h10M4 18h7M15 18h5M14 4v4M6 10v4M11 16v4"/>',
    ];
    $svg = $icons[$icono] ?? $icons['box'];
@endphp

<div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="bg-gradient-to-br from-brand-700 to-brand-900 p-10 text-white relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-48 h-48 bg-white/10 rounded-full"></div>
        <div class="relative z-10 flex items-center gap-5">
            <div class="w-16 h-16 rounded-2xl bg-white/15 flex items-center justify-center">
                <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">{!! $svg !!}</svg>
            </div>
            <div>
                <h2 class="text-3xl font-extrabold">{{ $titulo }}</h2>
                <p class="text-brand-100 mt-1 max-w-xl">{{ $desc }}</p>
            </div>
        </div>
    </div>

    <div class="p-10 text-center">
        <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-50 text-amber-700 text-sm font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Modulo en construccion
        </span>
        <h3 class="mt-5 text-xl font-extrabold text-slate-800">Proximamente disponible</h3>
        <p class="mt-2 text-slate-400 max-w-md mx-auto">Este modulo forma parte de la siguiente fase del proyecto. La base del sistema, autenticacion y dashboard ya estan operativos.</p>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 mt-6 px-5 py-3 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Volver al Dashboard
        </a>
    </div>
</div>
@endsection
