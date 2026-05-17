<?php

namespace Modules\EOffice\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\EOffice\Models\AsprakPraktikum;
use Modules\EOffice\Models\Modul;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureAsprakOwnership
 *
 * MASALAH YANG DISELESAIKAN
 * ─────────────────────────
 * Asprak hanya boleh mengelola MODUL yang di-assign kepadanya via tabel
 * `modul_asprak` (asprak_id → asprak_praktikum.id). Tanpa middleware ini,
 * asprak bisa mengakses modul praktikum lain cukup dengan mengganti ID di URL.
 *
 * CARA KERJA
 * ──────────
 * 1. Resolve `AsistenPraktikum` yang aktif untuk user ini.
 * 2. Jika request menyertakan `modulId` / `modul_id` di route, cek bahwa
 *    modul tersebut ada di `modul_asprak` untuk asprak ini.
 * 3. Inject `$asprak` (dan `$modul` jika ada) ke request attributes.
 *
 * PENGGUNAAN DI ROUTE
 * ───────────────────
 *   Route::middleware(['role:asprak|koor_prak', 'asprak.owns'])
 *
 * Di controller:
 *   $asprak = $request->attributes->get('asprak');
 *   $modul  = $request->attributes->get('modul'); // null jika tidak ada {modul} di route
 */
class EnsureAsprakOwnership
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Koor dan admin dapat bypass — mereka punya akses lebih luas
        if ($user?->hasAnyRole(['superadmin', 'admin_eoffice', 'koor_prak'])) {
            return $next($request);
        }

        // Ambil semua record asprak aktif milik user ini (bisa >1 jika pegang banyak praktikum)
        $allAsprak = AsprakPraktikum::with('praktikum')
            ->where('user_id', $user?->id)
            ->where('role', 'asprak')
            ->whereNull('deleted_at')
            ->get();

        if ($allAsprak->isEmpty()) {
            abort(403, 'Anda belum terdaftar sebagai Asisten Praktikum aktif.');
        }

        // Resolve praktikum yang aktif: query param → session → first
        $selectedPraktikumId = $request->input('praktikum_id')
            ?? session('manprak_asprak_praktikum_id');

        $asprak = ($selectedPraktikumId
            ? $allAsprak->firstWhere('praktikum_id', $selectedPraktikumId)
            : null) ?? $allAsprak->first();

        // Simpan pilihan ke session agar page lain ikut context yang sama
        session(['manprak_asprak_praktikum_id' => $asprak->praktikum_id]);

        $request->attributes->set('asprak', $asprak);
        $request->attributes->set('all_asprak', $allAsprak);

        // Jika route punya parameter modul, cek ownership terhadap asprak yang dipilih
        $modulId = $request->route('modulId')
            ?? $request->route('modul')
            ?? $request->input('modul_id');

        if ($modulId) {
            $modul = Modul::where('id', $modulId)
                ->whereHas('modulAsprak', fn($q) => $q->where('asprak_id', $asprak->id))
                ->first();

            if (! $modul) {
                abort(403, 'Anda tidak memiliki akses ke modul ini.');
            }

            $request->attributes->set('modul', $modul);
        }

        return $next($request);
    }
}
