<?php

namespace Modules\EOffice\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\EOffice\Models\Praktikum;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureKoorOwnership
 *
 * MASALAH YANG DISELESAIKAN
 * ─────────────────────────
 * Role `koor_prak` hanya menjawab "apakah user ini seorang koordinator?"
 * Pertanyaan "koordinator praktikum mana?" dijawab oleh DATA, bukan RBAC.
 * Middleware ini menjadi enforcement layer agar koor tidak bisa mengakses
 * praktikum yang bukan miliknya — meski URL-nya diketik manual.
 *
 * CARA KERJA
 * ──────────
 * 1. Ambil `praktikum_id` dari route params atau request body.
 * 2. Pastikan `eo_praktikum.koor_id = auth()->id()`.
 * 3. Jika tidak cocok → 403. Jika cocok → inject `$praktikum` ke request
 *    sehingga controller tidak perlu query ulang.
 *
 * PENGGUNAAN DI ROUTE
 * ───────────────────
 *   Route::middleware(['role:koor_prak', 'koor.owns:praktikum_id'])
 *
 * PENGGUNAAN DI CONTROLLER (tanpa middleware — fallback manual)
 * ─────────────────────────────────────────────────────────────
 *   $praktikum = $request->attributes->get('praktikum');
 *   // sudah di-inject oleh middleware, tidak perlu query ulang
 */
class EnsureKoorOwnership
{
    /**
     * @param  string  $paramName  Nama parameter yang berisi UUID praktikum.
     *                             Default: 'praktikum_id'. Bisa di-override via
     *                             middleware parameter: 'koor.owns:id'
     */
    public function handle(Request $request, Closure $next, string $paramName = 'praktikum_id'): Response
    {
        $user = $request->user();

        // Superadmin & admin_eoffice boleh bypass scope check
        if ($user?->hasAnyRole(['superadmin', 'admin_eoffice'])) {
            return $next($request);
        }

        // Ambil ID dari route segment, lalu fallback ke request input
        $praktikumId = $request->route($paramName)
            ?? $request->route('praktikum')   // resource route: /modul/{praktikum}
            ?? $request->input($paramName)
            ?? $request->input('praktikum_id');

        if (! $praktikumId) {
            // Tidak ada ID di request — coba cari praktikum aktif milik koor ini.
            // Ini case untuk halaman index tanpa parameter (misal: /koor/modul).
            $praktikum = Praktikum::where('koor_id', $user?->id)
                ->where('status', 'aktif')
                ->first();

            if (! $praktikum) {
                abort(403, 'Anda belum menjadi koordinator praktikum mana pun.');
            }

            // Inject ke request agar controller bisa langsung pakai
            $request->attributes->set('praktikum', $praktikum);

            return $next($request);
        }

        // Ada ID — cek ownership
        $praktikum = Praktikum::where('id', $praktikumId)
            ->where('koor_id', $user?->id)
            ->first();

        if (! $praktikum) {
            abort(403, 'Anda tidak memiliki akses ke praktikum ini.');
        }

        $request->attributes->set('praktikum', $praktikum);

        return $next($request);
    }
}