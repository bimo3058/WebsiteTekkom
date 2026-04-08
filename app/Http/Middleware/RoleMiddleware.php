<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Middleware tunggal untuk cek role. Menggantikan CheckRole.
     *
     * Usage di routes:
     *   Route::middleware('role:superadmin')->group(...);
     *   Route::middleware('role:admin_banksoal,admin_capstone')->group(...); // salah satu
     *
     * Superadmin selalu lolos tanpa perlu didaftarkan eksplisit.
     * Unauthenticated user di-redirect ke login (bukan abort 401)
     * agar UX lebih baik untuk web app.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Superadmin bypass semua role check
        if ($user->hasRole('superadmin')) {
            return $next($request);
        }

        // hasAnyRole() sudah pakai getCachedRoles() — tidak ada query baru
        // kalau cache sudah ada (diset saat login di AuthenticatedSessionController)
        if (! $user->hasAnyRole($roles)) {
            abort(403, 'Unauthorized. Required role: ' . implode(' or ', $roles));
        }

        return $next($request);
    }
}