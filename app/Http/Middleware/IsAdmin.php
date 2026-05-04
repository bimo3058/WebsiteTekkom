<?php

namespace App\Http\Middleware;

use App\Models\SesiLogin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * IsAdmin Middleware
 *
 * Hanya mengizinkan user yang role aktif-nya TEPAT 'admin'.
 * Tidak menggunakan hierarchy — harus admin persis.
 *
 * Cara pakai: ->middleware('is_admin')
 */
class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $pengguna = $request->user();

        if (! $pengguna) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $roleAktif = $this->getRoleAktif($pengguna);

        if ($roleAktif !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya admin yang dapat mengakses resource ini.',
            ], 403);
        }

        return $next($request);
    }

    /**
     * Ambil nama role aktif dari SESI_LOGIN terbaru.
     */
    private function getRoleAktif($pengguna): ?string
    {
        $sesi = SesiLogin::with('roleAktif')
            ->where('pengguna_id', $pengguna->id)
            ->whereNull('logout_pada')
            ->latest('login_pada')
            ->first();

        return $sesi?->roleAktif?->nama;
    }
}
