<?php

namespace Modules\EOffice\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\EOffice\Models\Praktikum;
use Symfony\Component\HttpFoundation\Response;

class EnsureKoorOwnership
{
    public function handle(Request $request, Closure $next, string $paramName = 'praktikum_id'): Response
    {
        $user = $request->user();

        // Superadmin & admin_eoffice boleh bypass scope check
        if ($user?->hasAnyRole(['superadmin', 'admin_eoffice'])) {
            return $next($request);
        }

        // Ambil ID dari route segment, lalu fallback ke request input
        $praktikumId = $request->route($paramName)
            ?? $request->route('praktikum')   
            ?? $request->input($paramName)
            ?? $request->input('praktikum_id');

        if (! $praktikumId) {
            // FIXED: Jangan sekadar ->first(). Cek session dulu agar konsisten dengan Dashboard!
            $sessionId = session('koor_praktikum_id');
            
            $query = Praktikum::where('koor_id', $user?->id)
                ->whereIn('status', ['aktif', 'nonaktif'])
                ->orderByRaw("status = 'aktif' DESC")
                ->orderBy('created_at', 'desc');

            $praktikum = null;

            if ($sessionId) {
                // Cari berdasarkan session terlebih dahulu
                $praktikum = (clone $query)->where('id', $sessionId)->first();
            }

            // Fallback: Jika tidak ada session, atau ID di session sudah dihapus, ambil yang aktif pertama
            if (! $praktikum) {
                $praktikum = $query->first();
                // Set session agar berlanjut ke request berikutnya
                if ($praktikum) {
                    session(['koor_praktikum_id' => $praktikum->id]);
                }
            }

            if (! $praktikum) {
                abort(403, 'Anda belum menjadi koordinator praktikum mana pun.');
            }

            // Inject ke request agar controller bisa langsung pakai
            $request->attributes->set('praktikum', $praktikum);

            return $next($request);
        }

        // Ada ID dari request/route — cek ownership secara eksplisit
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