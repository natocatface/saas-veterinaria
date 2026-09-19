<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restablecer contrasena — VetSystem</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','ui-sans-serif']},colors:{brand:{500:'#06b6d4',600:'#0891b2',700:'#0e7490'}}}}};</script>
    <style>body{font-family:'Inter',sans-serif}</style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-md">
        <div class="flex items-center gap-3 justify-center mb-8">
            <div class="w-11 h-11 rounded-xl bg-brand-600 text-white flex items-center justify-center"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c-2.5 0-4.5 2-4.5 4.2 0 1.5 1.1 2.3 2.5 2.3.9 0 1.3-.4 2-.4s1.1.4 2 .4c1.4 0 2.5-.8 2.5-2.3C16.5 13 14.5 11 12 11z"/></svg></div>
            <p class="text-xl font-extrabold text-slate-800">VetSystem</p>
        </div>
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
            <h1 class="text-2xl font-extrabold text-slate-800">Nueva contrasena</h1>
            <p class="mt-2 text-slate-400 text-sm">Define una contrasena nueva para tu cuenta.</p>
            @if ($errors->any())
                <div class="mt-5 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Correo</label>
                    <input name="email" type="email" value="{{ old('email', $email) }}" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nueva contrasena</label>
                    <input name="password" type="password" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Confirmar contrasena</label>
                    <input name="password_confirmation" type="password" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-brand-500 outline-none">
                </div>
                <button class="w-full py-3 rounded-xl bg-brand-600 text-white font-bold hover:bg-brand-700 shadow-lg shadow-brand-500/25">Restablecer contrasena</button>
            </form>
        </div>
    </div>
</body>
</html>
