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

        $user->cacheUserData();
        Cache::put("user:{$user->id}:roles", $userRoles->toArray(), now()->addHours(8));

        $roleNames = $userRoles->pluck('name');

        // Log login setelah auth berhasil
        AuditLogger::log(
            module:      'auth',
            action:      'LOGIN',
            description: "Login ke sistem sebagai {$roleNames->implode(', ')}",
            userId:      $user->id,
        );

        if ($roleNames->contains('superadmin')) {
            return redirect()->intended(route('superadmin.dashboard'));
        }

        if ($roleNames->contains('admin')) {
            return redirect()->intended(route('dashboard'));
        }

        if ($roleNames->contains('dosen')) {
            return redirect()->intended(route('dashboard'));
        }

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = auth()->user();

        // Log logout SEBELUM guard logout — sesudahnya auth()->id() sudah null
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