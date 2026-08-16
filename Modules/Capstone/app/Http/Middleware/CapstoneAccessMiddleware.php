<?php

namespace Modules\Capstone\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Capstone\Support\CapstoneActor;

class CapstoneAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (! $user->tokenCan('capstone:access') || ! $user->can('capstone.view')) {
            return response()->json(['message' => 'Akses Capstone tidak valid.'], 403);
        }

        $role = CapstoneActor::role($user, $request->header('X-Capstone-Role'));
        if (! $role) {
            return response()->json(['message' => 'User tidak punya role Capstone.'], 403);
        }

        if ($role === 'mahasiswa') {
            if (! $user->student) {
                $user->tokens()->where('name', 'capstone-fe')->delete();

                return response()->json([
                    'message' => 'Data mahasiswa belum terdaftar di SICATA.',
                    'code' => 'SICATA_STUDENT_NOT_REGISTERED',
                    'redirect_url' => route('dashboard'),
                ], 403);
            }
        } elseif ($role === 'dosen') {
            CapstoneActor::lecturer($user);
        }

        $request->attributes->set('capstone_role', $role);

        return $next($request);
    }
}
