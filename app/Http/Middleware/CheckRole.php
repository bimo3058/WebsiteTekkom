<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Menggunakan variadic parameter (...$role) agar otomatis menangkap koma dari route.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $userId = auth()->id();
        $cached = Cache::get("user:{$userId}:roles");

        if ($cached) {
            // Pastikan semua role dari cache di-lowercase untuk perbandingan adil
            $userRoles = collect($cached)->pluck('name')->map(fn($n) => strtolower($n));
        } else {
            $rolesCollection = auth()->user()->roles()->get();
            Cache::put("user:{$userId}:roles", $rolesCollection->toArray(), now()->addHours(8));
            $userRoles = $rolesCollection->pluck('name')->map(fn($n) => strtolower($n));
        }

        // Cek apakah ada role user yang cocok dengan salah satu role yang diminta di route
        $hasRole = collect($roles)->some(function($role) use ($userRoles) {
            return $userRoles->contains(strtolower(trim($role)));
        });

        if (!$hasRole) {
            // Debugging: Jika masih 403, buka comment baris di bawah ini untuk melihat isi role
            // dd($userRoles, $roles); 
            abort(403, 'Role tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}