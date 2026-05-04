<?php

namespace App\Http\Middleware;

use App\Models\Praktikum;
use App\Models\SesiLogin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * PraktikumAccess Middleware
 *
 * Validasi akses berdasarkan praktikum_id:
 * - admin  → akses semua praktikum
 * - dosen  → hanya praktikum yang dia ampu (PRAKTIKUM.dosen_id = user.id)
 * - lainnya → 403
 *
 * Cara pakai di routes:
 *   ->middleware('praktikum_access')
 *
 * Middleware ini membaca {praktikum_id} dari:
 *   1. Route parameter:  /admin/praktikum/{praktikum_id}/...
 *   2. Request body:     { "praktikum_id": "uuid" }
 *   3. Query param:      ?praktikum_id=uuid
 */
class PraktikumAccess
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

        // Ambil role aktif
        $roleAktif = $this->getRoleAktif($pengguna);

        // Admin → akses semua praktikum, langsung pass
        if ($roleAktif === 'admin') {
            return $next($request);
        }

        // Ambil praktikum_id dari berbagai sumber
        $praktikumId = $request->route('praktikum_id')
                    ?? $request->route('id')
                    ?? $request->input('praktikum_id');

        if (! $praktikumId) {
            // Tidak ada praktikum_id → biarkan lewat (endpoint list, bukan per-praktikum)
            return $next($request);
        }

        // Cari praktikum
        $praktikum = Praktikum::find($praktikumId);

        if (! $praktikum) {
            return response()->json([
                'success' => false,
                'message' => 'Praktikum tidak ditemukan.',
            ], 404);
        }

        // Dosen → hanya praktikum yang diampu
        if ($roleAktif === 'dosen') {
            if ($praktikum->dosen_id !== $pengguna->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Anda bukan dosen pengampu praktikum ini.',
                ], 403);
            }

            // Simpan praktikum ke request untuk controller
            $request->merge(['_praktikum' => $praktikum]);
            return $next($request);
        }

        // Role lain (koor_prak, asprak, mahasiswa) → 403 default
        // Mereka harus akses via endpoint khusus mereka, bukan via /admin atau /dosen
        return response()->json([
            'success' => false,
            'message' => 'Akses ditolak. Anda tidak memiliki hak akses ke resource ini.',
        ], 403);
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
