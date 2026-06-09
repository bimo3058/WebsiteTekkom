<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\Nilai;
use Modules\EOffice\Models\Praktikum;

/**
 * Koordinator: Lihat & approve daftar nilai (sebelum dosen approve).
 * Sesuai docx: "sama kayak asprak, kayak di teams" + menyetujui ke dosen.
 */
class NilaiController extends Controller
{
    public function index(Request $request)
    {
        $user      = auth()->user();
        $praktikum = Praktikum::where('koor_id', $user->id)->where('status', 'aktif')->first();

        if (!$praktikum) {
            return view('eoffice::manajemen-praktikum.koordinator.nilai', [
                'praktikum' => null,
                'nilaiList' => collect(),
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
            'praktikum',
            'nilaiList',
            'moduls',
            'modulFilter'
        ));
    }

    /**
     * Koordinator menyetujui nilai → siap diteruskan ke dosen untuk publikasi.
     */
    public function approve(Request $request)
    {
        $user      = auth()->user();
        $praktikum = Praktikum::where('koor_id', $user->id)->where('status', 'aktif')->firstOrFail();

        $daftarIds = DaftarPraktikan::where('praktikum_id', $praktikum->id)->pluck('id');

        Nilai::whereIn('daftar_praktikan_id', $daftarIds)
            ->update(['disetujui_koor' => true]);

        return back()->with('success', 'Nilai telah disetujui oleh koordinator. Silakan minta dosen untuk publikasi akhir.');
    }
}
