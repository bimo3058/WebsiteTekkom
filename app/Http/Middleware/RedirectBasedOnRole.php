<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('telescope', 'telescope/*', 'vendor/telescope/*')) {
            return $next($request);
        }
        // Jika tidak login, biarkan middleware 'auth' bawaan yang bekerja
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // 1. Jika akun disuspend, biarkan middleware CheckSuspended yang menangani
        if ($user->suspended_at) {
            return $next($request);
        }

        // 2. Global Whitelist: Path yang boleh diakses semua role yang sudah login
        // Menggunakan wildcard (*) agar sub-path (seperti pulse/api atau profile/edit) tetap lolos
        $excludedPaths = ['profile*', 'logout', 'pulse*'];
    
        foreach ($excludedPaths as $path) {
            if ($request->is($path)) {
                return $next($request);
            }
        }

        $roleNames = $user->roles->pluck('name')->map(fn($r) => strtolower($r));

        // 3. Logika untuk ADMIN MODUL (Lock ke Modul masing-masing)
        $adminModuleRoles = [
            'admin_banksoal'      => ['route' => 'banksoal.dashboard', 'prefix' => 'bank-soal*'],
            'admin_capstone'      => ['route' => 'capstone.dashboard', 'prefix' => 'capstone*'],
            'admin_eoffice'       => ['route' => 'eoffice.dashboard', 'prefix' => 'eoffice*'],
            'admin_kemahasiswaan' => ['route' => 'manajemenmahasiswa.mahasiswa.dashboard', 'prefix' => 'manajemen-mahasiswa*'],
        ];

        foreach ($adminModuleRoles as $role => $config) {
            if ($roleNames->contains($role)) {
                // Jika sedang mencoba akses di LUAR prefix modulnya, paksa balik ke dashboard modul
                if (!$request->is($config['prefix'])) {
                    return redirect()->route($config['route']);
                }
                return $next($request);
            }
        }

        // 4. Logika untuk SUPERADMIN (Lock ke area /superadmin)
        if ($user->hasRole('superadmin') && request()->is('dashboard')) {   
            return redirect()->route('superadmin.dashboard');
        }

        // 5. Logika untuk USER UMUM (Mahasiswa, Dosen, GPM)
        // Jika mereka mencoba akses root '/', arahkan ke dashboard global
        if ($request->is('/')) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}