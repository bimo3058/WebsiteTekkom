<?php

namespace App\Http\Middleware;

<<<<<<< HEAD
use App\Models\SesiLogin;
=======
>>>>>>> 907aff17a69304925ed419e8a818c3b3b4292d9f
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
<<<<<<< HEAD
 * CheckRole (HasRole) Middleware
 *
 * Validasi bahwa role aktif pengguna (dari SESI_LOGIN) memenuhi
 * level minimum yang dibutuhkan menggunakan hierarchy:
 *
 *   mahasiswa (1) < asprak (2) < koor_prak (3) < dosen (4) < admin (5)
 *
 * Jika user punya role_aktif >= role_required → allow.
 * Contoh: koor_prak (3) >= asprak (2) → allowed.
 *         asprak (2) < koor_prak (3) → 403.
 *
 * Cara pakai di routes:
 *   ->middleware('role:admin')     → hanya admin
 *   ->middleware('role:asprak')    → asprak, koor_prak, dosen, admin
 *   ->middleware('role:koor_prak') → koor_prak, dosen, admin
 *   ->middleware('role:dosen')     → dosen, admin
 */
class CheckRole
{
    /**
     * Role hierarchy (code only, not in DB).
     */
    private const HIERARCHY = [
        'mahasiswa' => 1,
        'asprak'    => 2,
        'koor_prak' => 3,
        'dosen'     => 4,
        'admin'     => 5,
    ];

    public function handle(Request $request, Closure $next, string $requiredRole): Response
    {
        $pengguna = $request->user();

        if (! $pengguna) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        // Ambil role aktif dari SESI_LOGIN terbaru
        $roleAktifNama = $this->getRoleAktif($pengguna);

        if (! $roleAktifNama) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak memiliki sesi login aktif. Silakan login ulang.',
            ], 403);
        }

        $levelUser     = self::HIERARCHY[$roleAktifNama] ?? 0;
        $levelRequired = self::HIERARCHY[$requiredRole]  ?? 0;

        if ($levelUser < $levelRequired) {
            return response()->json([
                'success' => false,
                'message' => "Akses ditolak. Minimal role '{$requiredRole}' dibutuhkan, Anda login sebagai '{$roleAktifNama}'.",
            ], 403);
        }

        // Simpan role aktif di request untuk akses di controller
        $request->merge(['_role_aktif' => $roleAktifNama]);

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
=======
 * @deprecated Gunakan RoleMiddleware ('role:...') sebagai gantinya.
 * CheckRole dipertahankan hanya untuk backward compatibility dengan route
 * yang masih pakai 'check.role:...' — migrasi ke 'role:...' saat ada kesempatan.
 */
class CheckRole extends RoleMiddleware
{
    // Mewarisi semua logika dari RoleMiddleware.
    // Tidak perlu kode tambahan — alias murni.
}
>>>>>>> 907aff17a69304925ed419e8a818c3b3b4292d9f
