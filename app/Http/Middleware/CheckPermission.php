<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        // 1. Izinkan Superadmin bypass semua pengecekan
        if ($user && $user->hasRole('superadmin')) {
            return $next($request);
        }

        // 2. Cek apakah user memiliki permission tersebut
        // Pastikan model User kamu punya method hasPermissionTo() atau logic serupa
        if (!$user || !$user->hasPermissionTo($permission)) {
            abort(403, "Anda tidak memiliki izin akses ($permission).");
        }

        return $next($request);
    }
}