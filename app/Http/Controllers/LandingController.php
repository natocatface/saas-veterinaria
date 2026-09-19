<?php

namespace App\Http\Controllers;

use App\Models\Plan;

class LandingController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return auth()->user()->esSuperAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('dashboard');
        }

        return view('landing', [
            'planes' => Plan::where('activo', true)->orderBy('precio')->get(),
        ]);
    }
}
