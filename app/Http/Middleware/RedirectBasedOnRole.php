<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    private array $excludedPaths = [
        'telescope', 'telescope/*', 'vendor/telescope/*',
        'pulse*',
        'profile*',
        'logout',
        'sso/password',
        'sso/verify',
    ];

    // Role yang diarahkan ke dashboard global
    private array $generalRoles = [
        'mahasiswa',
        'dosen',
        'gpm',
        'pengurus_himpunan', // ← tambah
        'alumni',            // ← tambah
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        if ($user->isSuspended()) {
            return $next($request);
        }

        foreach ($this->excludedPaths as $path) {
            if ($request->is($path)) {
                return $next($request);
            }
        }

        $roleNames = $user->getCachedRoles()->pluck('name')->map(fn($r) => strtolower($r));

        // 1. Superadmin — lock ke area /superadmin
        if ($roleNames->contains('superadmin') && $request->is('dashboard')) {
            return redirect()->route('superadmin.dashboard');
        }

        // 2. Admin modul — lock ke prefix modul masing-masing
        $adminModuleRoles = [
            'admin_banksoal'      => ['route' => 'banksoal.dashboard',                     'prefix' => 'bank-soal*'],
            'admin_capstone'      => ['route' => 'capstone.dashboard',                     'prefix' => 'capstone*'],
            'admin_eoffice'       => ['route' => 'eoffice.dashboard',                      'prefix' => 'eoffice*'],
            'admin_kemahasiswaan' => ['route' => 'manajemenmahasiswa.dashboard',           'prefix' => 'manajemen-mahasiswa*'],
        ];

        foreach ($adminModuleRoles as $role => $config) {
            if ($roleNames->contains($role)) {
                if (! $request->is($config['prefix'])) {
                    return redirect()->route($config['route']);
                }
                return $next($request);
            }
        }

        // 3. User umum — redirect root ke dashboard global
        if ($request->is('/') && $roleNames->intersect($this->generalRoles)->isNotEmpty()) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}