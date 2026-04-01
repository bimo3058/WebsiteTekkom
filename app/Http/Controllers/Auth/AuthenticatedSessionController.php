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

        $user      = auth()->user();
        $userRoles = $user->roles()->get();

        // 1. Cek status suspend
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

        // 2. Cache Data
        $user->cacheUserData();
        Cache::put("user:{$user->id}:roles", $userRoles->toArray(), now()->addHours(8));

        // Ambil nama role dalam lowercase untuk pengecekan
        $roleNames = $userRoles->pluck('name')->map(fn($r) => strtolower($r));

        // 3. Audit Log
        AuditLogger::log(
            module:      'auth',
            action:      'LOGIN',
            description: "Login ke sistem sebagai {$roleNames->implode(', ')}",
            userId:      $user->id,
        );

        // 4. Redirect Logic 
        
        // Superadmin selalu prioritas utama
        if ($roleNames->contains('superadmin')) {
            return redirect()->intended(route('superadmin.dashboard'));
        }

        // Mapping Admin ke Modul Spesifik
        $adminRedirects = [
            'admin_banksoal'      => 'banksoal.dashboard',
            'admin_capstone'      => 'capstone.dashboard',
            'admin_eoffice'       => 'eoffice.dashboard',
            'admin_kemahasiswaan' => 'manajemenmahasiswa.mahasiswa.dashboard',
        ];

        foreach ($adminRedirects as $role => $routeName) {
            if ($roleNames->contains($role)) {
                return redirect()->intended(route($routeName));
            }
        }

        // Mahasiswa, Dosen, GPM diarahkan ke Dashboard Global
        if ($roleNames->intersect(['mahasiswa', 'dosen', 'gpm'])->isNotEmpty()) {
            return redirect()->intended(route('dashboard'));
        }

        /**
         * PERUBAHAN DISINI: Protection Layer
         * Jika user tembus sampai sini (artinya login sukses tapi TIDAK PUNYA ROLE),
         * maka kita logout paksa agar tidak masuk ke dashboard sebagai "mahasiswa kosong".
         */
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
        }

        Auth::guard('web')->logout();

        if ($user) {
            $user->clearUserCache();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}