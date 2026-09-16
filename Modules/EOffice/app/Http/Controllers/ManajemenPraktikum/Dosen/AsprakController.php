<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\AsprakPraktikum;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Praktikum;

/**
 * Dosen: Lihat anggota (Asisten Praktikum & Praktikan) yang diampu berdasarkan list praktikum.
 */
class AsprakController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $praktikumList = Praktikum::whereHas('dosens', fn($q) => $q->where('users.id', $user->id))
            ->orderByDesc('created_at')
            ->get();

        $praktikumId = $request->input('praktikum_id', $praktikumList->first()?->id);
        $praktikum   = $praktikumList->firstWhere('id', $praktikumId);

        $search = $request->input('search');

        $perPageAsisten = $request->input('per_page_asisten', 10);
        $perPagePraktikan = $request->input('per_page_praktikan', 10);

        // Asisten & Koordinator
        $queryAsisten = AsprakPraktikum::with(['user', 'user.student', 'modulAsprak.modul'])
            ->where('praktikum_id', $praktikum->id ?? null)
            ->whereNull('deleted_at')
            ->orderBy('role', 'desc');

        $aspraks = $praktikum 
            ? $queryAsisten->paginate($perPageAsisten, ['*'], 'page_asisten')->withQueryString() 
            : collect();

        // Praktikans
        $queryPraktikan = DaftarPraktikan::with(['user', 'user.student'])
            ->where('praktikum_id', $praktikumId)
            ->orderByRaw("CASE WHEN (shift IS NULL OR shift = '') THEN 1 ELSE 0 END, shift ASC")
            ->orderByRaw("CASE WHEN (kelompok IS NULL OR kelompok = '') THEN 1 ELSE 0 END, kelompok ASC")
            ->orderBy('created_at');

        if ($search) {
            $queryPraktikan->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $praktikans = $praktikum 
            ? $queryPraktikan->paginate($perPagePraktikan, ['*'], 'page_praktikan')->withQueryString() 
            : collect();

        $modulPraktikum = $praktikum
            ? $praktikum->modul()->orderBy('urutan')->get()
            : collect();

        return view('eoffice::manajemen-praktikum.dosen.asprak', compact(
            'praktikumList',
            'praktikum',
            'aspraks',
            'praktikans',
            'search',
            'modulPraktikum'
        ));
    }
}
