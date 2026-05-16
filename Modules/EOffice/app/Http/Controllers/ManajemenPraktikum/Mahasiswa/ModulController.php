<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Modul;

class ModulController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $daftarPraktikan = DaftarPraktikan::with('praktikum')
            ->where('user_id', $user->id)
            ->get();

        $praktikumAktifId = $request->input('praktikum_id',
            $daftarPraktikan->first()?->praktikum_id
        );

        $terdaftarDi = $daftarPraktikan->firstWhere('praktikum_id', $praktikumAktifId)?->praktikum
            ?? $daftarPraktikan->first()?->praktikum;

        $modulList = $terdaftarDi
            ? Modul::with(['materi', 'modulAsprak.asprak.user'])
                ->where('praktikum_id', $terdaftarDi->id)
                ->orderBy('urutan')
                ->get()
            : collect();

        return view('eoffice::manajemen-praktikum.mahasiswa.modul', compact(
            'daftarPraktikan',
            'terdaftarDi',
            'modulList'
        ));
    }
}
