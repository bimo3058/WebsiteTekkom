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
        // Check if user is superadmin
        if ($user->roles()->where('name', 'SUPERADMIN')->exists()) {
            return redirect()->route('superadmin.dashboard');
        }

        // Check other roles and redirect accordingly
        if ($user->roles()->where('name', 'DOSEN')->exists()) {
            return redirect()->route('banksoal.dashboard'); // Sesuaikan
        }

        if ($user->roles()->where('name', 'MAHASISWA')->exists()) {
            return redirect()->route('mahasiswa.dashboard'); // Sesuaikan
        }

        // Default fallback to global dashboard
        return redirect()->route('dashboard');
    }
}