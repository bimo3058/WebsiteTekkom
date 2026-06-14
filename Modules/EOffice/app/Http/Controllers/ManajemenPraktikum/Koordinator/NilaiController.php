<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\Nilai;
use Modules\EOffice\Models\Praktikum;

class NilaiController extends Controller
{
    public function index(Request $request)
    {
        $praktikum = DashboardController::resolvePraktikum();

        if (!$praktikum) {
            return view('eoffice::manajemen-praktikum.koordinator.nilai', [
                'praktikum' => null,
                'nilaiList' => collect(),
                'moduls'    => collect(),
                'modulFilter' => null,
            ]);
        }

        $modulFilter = $request->input('modul_id');
        $daftarIds   = DaftarPraktikan::where('praktikum_id', $praktikum->id)->pluck('id');

        $query = Nilai::whereIn('daftar_praktikan_id', $daftarIds)
            ->with('daftarPraktikan.user');

        if ($modulFilter) {
            $query->where('modul', $modulFilter);
        }

        $nilaiList = $query->get();
        $moduls    = Modul::where('praktikum_id', $praktikum->id)->orderBy('urutan')->get();

        return view('eoffice::manajemen-praktikum.koordinator.nilai', compact(
            'praktikum', 'nilaiList', 'moduls', 'modulFilter'
        ));
    }

    public function approve(Request $request)
    {
        $praktikum = DashboardController::resolvePraktikum() ?? abort(404);

        $daftarIds = DaftarPraktikan::where('praktikum_id', $praktikum->id)->pluck('id');
        Nilai::whereIn('daftar_praktikan_id', $daftarIds)->update(['disetujui_koor' => true]);

        return back()->with('success', 'Nilai telah disetujui oleh koordinator. Silakan minta dosen untuk publikasi akhir.');
    }
}