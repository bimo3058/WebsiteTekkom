<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

        // ✅ PENTING: Check & redirect TERLEBIH DAHULU
        // Sebelum route middleware check permissions
        
        // Jika user bukan SUPERADMIN tapi coba akses /superadmin/*
        if ($request->path() === 'dashboard' || $request->path() === '/') {
            return $this->redirectBasedOnRole(auth()->user());
        }

        // Jika user coba akses /superadmin tapi bukan superadmin
        if (str_starts_with($request->path(), 'superadmin') && 
            !auth()->user()->hasRole('SUPERADMIN')) {
            return $this->redirectBasedOnRole(auth()->user());
        }

        return $next($request);
    }

    private function redirectBasedOnRole($user)
    {
        if ($user->roles()->whereIn('name', ['SUPERADMIN', 'superadmin'])->exists()) {
            return redirect()->route('superadmin.dashboard');
        }

        if ($user->roles()->whereIn('name', ['ADMIN', 'admin'])->exists()) {
            return redirect()->route('admin.dashboard'); // sesuaikan kalau nanti ada route admin tersendiri
        }

        if ($user->roles()->whereIn('name', ['DOSEN', 'dosen'])->exists()) {
            return redirect()->route('banksoal.dashboard');
        }

        if ($user->roles()->whereIn('name', ['MAHASISWA', 'mahasiswa'])->exists()) {
            return redirect()->route('mahasiswa.dashboard');
        }

        return redirect()->route('dashboard');
    }
}