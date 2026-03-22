<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Usage di routes:
     * Route::middleware('role:admin')->group(...);
     * Route::middleware('role:admin,dosen')->group(...); // salah satu
     * Route::middleware('role:admin,module:capstone')->group(...); // role + module spesifik
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth()->user();

        if (! $user) {
            abort(401);
        }

        // Load roles kalau belum
        $user->loadMissing('roles');

        // Support multiple roles: middleware('role:admin,superadmin')
        // User cukup punya salah satu
        if (! $user->hasAnyRole($roles)) {
            abort(403, 'Unauthorized. Required role: ' . implode(' or ', $roles));
        }

        return $next($request);
    }
}