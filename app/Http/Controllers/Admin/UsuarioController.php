<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));

        $usuarios = User::query()
            ->with('empresa')
            ->where('es_super_admin', false)
            ->when($q, fn ($query) => $query->where(fn ($sub) => $sub
                ->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")))
            ->orderBy('empresa_id')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.usuarios.index', compact('usuarios', 'q'));
    }
}
