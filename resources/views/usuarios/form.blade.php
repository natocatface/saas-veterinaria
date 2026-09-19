@extends('layouts.app')

@section('title', $usuario->exists ? 'Editar Usuario' : 'Nuevo Usuario')
@section('subtitle', 'Datos de acceso y rol')

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('usuarios.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand-600 mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Volver a usuarios
    </a>

    <form method="POST" action="{{ $usuario->exists ? route('usuarios.update', $usuario) : route('usuarios.store') }}" class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8 space-y-5">
        @csrf
        @if ($usuario->exists) @method('PUT') @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nombre completo *</label>
                <input name="name" value="{{ old('name', $usuario->name) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Correo *</label>
                <input name="email" type="email" value="{{ old('email', $usuario->email) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Rol *</label>
                <select name="rol" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none bg-white">
                    @foreach ($roles as $r)<option value="{{ $r }}" @selected(old('rol', $usuario->rol) === $r)>{{ ucfirst($r) }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Cargo</label>
                <input name="cargo" value="{{ old('cargo', $usuario->cargo) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Telefono</label>
                <input name="telefono" value="{{ old('telefono', $usuario->telefono) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div class="flex items-end">
                <label class="flex items-center gap-2.5 text-sm text-slate-600 select-none pb-2.5">
                    <input type="checkbox" name="activo" value="1" @checked(old('activo', $usuario->exists ? $usuario->activo : true)) class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 w-5 h-5">
                    Usuario activo
                </label>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">{{ $usuario->exists ? 'Nueva contrasena' : 'Contrasena *' }}</label>
                <input name="password" type="password" {{ $usuario->exists ? '' : 'required' }} placeholder="{{ $usuario->exists ? 'Dejar en blanco para mantener' : '' }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Confirmar contrasena</label>
                <input name="password_confirmation" type="password" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button class="px-6 py-2.5 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-700 shadow-lg shadow-brand-500/25">{{ $usuario->exists ? 'Guardar cambios' : 'Crear usuario' }}</button>
            <a href="{{ route('usuarios.index') }}" class="px-6 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200">Cancelar</a>
        </div>
    </form>
</div>
@endsection
