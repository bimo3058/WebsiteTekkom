<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
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

        // Cache user data + roles sekaligus — pakai cacheUserData() supaya
        // remember_token ikut tersimpan dan tidak ada missing attribute error
        $user->cacheUserData();
        Cache::put("user:{$user->id}:roles", $userRoles->toArray(), now()->addHours(8));

        $roleNames = $userRoles->pluck('name');

        if ($roleNames->intersect(['superadmin', 'admin'])->isNotEmpty()) {
            return redirect()->intended(route('superadmin.dashboard'));
        }

        if ($roleNames->contains('dosen')) {
            return redirect()->intended(route('dashboard'));
        }

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = auth()->user();

        Auth::guard('web')->logout();

        // Hapus cache user saat logout
        if ($user) {
            $user->clearUserCache();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}