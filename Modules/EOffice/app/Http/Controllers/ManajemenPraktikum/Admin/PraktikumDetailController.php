<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Admin;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\AsprakPraktikum;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\Praktikum;

/**
 * Admin: Halaman detail satu praktikum — menampilkan info, daftar praktikan,
 * dan daftar asprak dalam satu layout 3 kolom.
 */
class PraktikumDetailController extends Controller
{
    public function show(Request $request, string $id)
    {
        $praktikum = Praktikum::with(['dosen', 'koordinator', 'matkul'])
            ->findOrFail($id);

        // ── Daftar Praktikan (paginated + searchable) ──────────────────────
        $search = $request->input('search');

        $query = DaftarPraktikan::with(['user'])
            ->where('praktikum_id', $id);

        if ($search) {
            $query->whereHas('user', fn($q) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
            );
        }

        $praktikans = $query->paginate(20)->withQueryString();

        // ── Daftar Asprak ──────────────────────────────────────────────────
        $aspraks = AsprakPraktikum::with(['user', 'modulAsprak.modul'])
            ->where('praktikum_id', $id)
            ->where('role', 'asprak')
            ->whereNull('deleted_at')
            ->get();

        // ── Total Modul ────────────────────────────────────────────────────
        $totalModul = Modul::where('praktikum_id', $id)->count();

        return view('eoffice::manajemen-praktikum.admin.praktikum-detail', compact(
            'praktikum',
            'praktikans',
            'aspraks',
            'totalModul',
            'search'
        ));
    }
}