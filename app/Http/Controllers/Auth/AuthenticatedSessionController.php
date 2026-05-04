<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = auth()->user();

        // 1. Cek suspend DULU sebelum query apapun — hindari query roles yang sia-sia
        if ($user->isSuspended()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = 'Akun Anda telah ditangguhkan.';
            if ($user->suspension_reason) {
                $message .= ' Alasan: ' . $user->suspension_reason;
            }
            return back()->withErrors(['email' => $message])->onlyInput('email');
        }

        // 2. Query roles sekali, pakai untuk cache + redirect logic
        $userRoles  = $user->roles()->get();
        $roleNames  = $userRoles->pluck('name')->map(fn($r) => strtolower($r));

        // 3. Cache — format konsisten dengan getCachedRoles() di User.php
        $user->cacheUserData();
        Cache::put(
            "user:{$user->id}:roles",
            $userRoles->map(fn($r) => [
                'id'          => $r->id,
                'name'        => $r->name,
                'module'      => $r->module,
                'is_academic' => (bool) $r->is_academic,
            ])->toArray(),
            now()->addHours(8)
        );

        // 4. Simpan session_version agar middleware CheckSessionVersion bisa bekerja
        $request->session()->put('session_version', $user->session_version);

        $user->recordLogin();

        // 5. Audit Log
        AuditLogger::log(
            module:      'auth',
            action:      'LOGIN',
            description: "Login ke sistem sebagai {$roleNames->implode(', ')}",
            userId:      $user->id,
        );

        // 6. Redirect Logic

        // Superadmin selalu prioritas utama
        if ($roleNames->contains('superadmin')) {
            return redirect()->intended(route('superadmin.dashboard'));
        }

        // Admin modul — masing-masing dikunci ke dashboard modulnya
        $adminRedirects = [
            'admin_banksoal'      => 'banksoal.dashboard',
            'admin_capstone'      => 'capstone.dashboard',
            'admin_eoffice'       => 'eoffice.dashboard',
            'admin_kemahasiswaan' => 'manajemenmahasiswa.dashboard',
        ];

        foreach ($adminRedirects as $role => $routeName) {
            if ($roleNames->contains($role)) {
                return redirect()->intended(route($routeName));
            }
        }

        // Mahasiswa, Dosen, GPM ke dashboard global
        if ($roleNames->intersect(['mahasiswa', 'dosen', 'gpm', 'pengurus_himpunan', 'alumni'])->isNotEmpty()) {
            return redirect()->intended(route('dashboard'));
        }

        // Protection layer: login sukses tapi tidak punya role yang dikenali
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withErrors([
            'email' => 'Akun Anda belum memiliki akses (Role) yang terdaftar. Silakan hubungi Administrator.'
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if ($user) {
            AuditLogger::log(
                module:      'auth',
                action:      'LOGOUT',
                description: "Logout dari sistem",
                userId:      $user->id,
            );

            $user->clearUserCache();
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}