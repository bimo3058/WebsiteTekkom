<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    /**
     * Path yang di-skip oleh middleware ini (semua role boleh akses).
     * Telescope & Pulse di-exclude agar tidak di-redirect loop.
     */
    private array $excludedPaths = [
        'telescope', 'telescope/*', 'vendor/telescope/*',
        'pulse*',
        'profile*',
        'logout',
        'sso/password',
        'sso/verify',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Jika tidak login, biarkan middleware 'auth' bawaan yang bekerja
        if (! auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // Jika akun suspended, biarkan middleware CheckSuspended yang menangani
        if ($user->isSuspended()) {
            return $next($request);
        }

        // Global whitelist — semua role yang sudah login boleh akses path ini
        foreach ($this->excludedPaths as $path) {
            if ($request->is($path)) {
                return $next($request);
            }
        }

        // Ambil roles via getCachedRoles() — tidak ada query baru kalau cache sudah ada
        $roleNames = $user->getCachedRoles()->pluck('name')->map(fn($r) => strtolower($r));

        // 1. Superadmin — lock ke area /superadmin
        if ($roleNames->contains('superadmin') && $request->is('dashboard')) {
            return redirect()->route('superadmin.dashboard');
        }

        // 2. Admin modul — lock ke prefix modul masing-masing
        $adminModuleRoles = [
            'admin_banksoal'      => ['route' => 'banksoal.dashboard',                        'prefix' => 'bank-soal*'],
            'admin_capstone'      => ['route' => 'capstone.dashboard',                        'prefix' => 'capstone*'],
            'admin_eoffice'       => ['route' => 'eoffice.dashboard',                         'prefix' => 'eoffice*'],
            'admin_kemahasiswaan' => ['route' => 'manajemenmahasiswa.mahasiswa.dashboard',    'prefix' => 'manajemen-mahasiswa*'],
        ];

        foreach ($adminModuleRoles as $role => $config) {
            if ($roleNames->contains($role)) {
                if (! $request->is($config['prefix'])) {
                    return redirect()->route($config['route']);
                }
                return $next($request);
            }
        }

        // 3. User umum (mahasiswa, dosen, gpm) — redirect root ke dashboard global
        if ($request->is('/')) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}