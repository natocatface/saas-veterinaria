@extends(auth()->user()->esSuperAdmin() ? 'layouts.admin' : 'layouts.app')

@section('title', 'Mi Perfil')
@section('subtitle', 'Datos de tu cuenta y seguridad')

@section('content')
<div class="max-w-3xl space-y-6">
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
            <ul class="list-disc list-inside space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <!-- Datos personales -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-14 h-14 rounded-2xl bg-brand-600 text-white flex items-center justify-center text-xl font-extrabold">{{ $usuario->iniciales() }}</div>
            <div>
                <h3 class="text-lg font-extrabold text-slate-800">{{ $usuario->name }}</h3>
                <p class="text-sm text-slate-400">{{ $usuario->rolLabel() }}{{ $usuario->empresa ? ' · '.$usuario->empresa->nombre : '' }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('perfil.update') }}" class="space-y-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nombre completo *</label>
                    <input name="name" value="{{ old('name', $usuario->name) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Correo *</label>
                    <input name="email" type="email" value="{{ old('email', $usuario->email) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Telefono</label>
                    <input name="telefono" value="{{ old('telefono', $usuario->telefono) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Cargo</label>
                    <input name="cargo" value="{{ old('cargo', $usuario->cargo) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                </div>
            </div>
            <button class="px-6 py-2.5 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-700 shadow-lg shadow-brand-500/25">Guardar datos</button>
        </form>
    </div>

    <!-- Cambiar contrasena -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 md:p-8">
        <h3 class="text-lg font-extrabold text-slate-800 mb-1">Cambiar contrasena</h3>
        <p class="text-sm text-slate-400 mb-5">Usa una contrasena segura que no utilices en otros sitios.</p>
        <form method="POST" action="{{ route('perfil.password') }}" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Contrasena actual *</label>
                <input name="current_password" type="password" required class="w-full max-w-md px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 max-w-2xl">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nueva contrasena *</label>
                    <input name="password" type="password" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Confirmar nueva *</label>
                    <input name="password_confirmation" type="password" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                </div>
            </div>
            <button class="px-6 py-2.5 rounded-xl bg-slate-800 text-white font-semibold hover:bg-slate-900">Actualizar contrasena</button>
        </form>
    </div>
</div>
@endsection
