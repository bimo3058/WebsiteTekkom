<?php

namespace Modules\Capstone\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CapstoneRoleMiddleware
{
    public function handle(Request $request, Closure $next, string $expected)
    {
        $user = $request->user();
        if (!$user) abort(401);

        $allowed = match ($expected) {
            'admin'           => $user->hasRole('superadmin') || $user->hasRole('admin_capstone'),
            'dosen'           => $user->hasRole('dosen'),
            'mahasiswa'       => $user->hasRole('mahasiswa'),
            'admin_or_dosen'  => $user->hasRole('superadmin') || $user->hasRole('admin_capstone') || $user->hasRole('dosen'),
            default           => false,
        };

        if (!$allowed) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return $next($request);
    }
}