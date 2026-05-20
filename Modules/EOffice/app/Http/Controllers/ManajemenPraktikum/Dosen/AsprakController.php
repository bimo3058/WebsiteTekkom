<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\AsprakPraktikum;
use Modules\EOffice\Models\Praktikum;

/**
 * Dosen: Lihat asisten praktikum yang diampu berdasarkan list praktikum.
 */
class AsprakController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $praktikumList = Praktikum::where('dosen_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $praktikumId = $request->input('praktikum_id', $praktikumList->first()?->id);
        $praktikum   = $praktikumList->firstWhere('id', $praktikumId);

        $aspraks = $praktikum
            ? AsprakPraktikum::with(['user', 'modulAsprak.modul'])
                ->where('praktikum_id', $praktikum->id)
                ->where('role', 'asprak')
                ->whereNull('deleted_at')
                ->get()
            : collect();

        $modulPraktikum = $praktikum
            ? $praktikum->modul()->orderBy('urutan')->get()
            : collect();

        return view('eoffice::manajemen-praktikum.dosen.asprak', compact(
            'praktikumList',
            'praktikum',
            'aspraks',
            'modulPraktikum'
        ));
    }
}
