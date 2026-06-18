<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\Nilai;

/**
 * Mahasiswa: Lihat absensi & nilai per modul (hanya jika sudah dipublikasikan oleh koor & dosen).
 */
class NilaiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Semua pendaftaran mahasiswa di praktikum aktif/nonaktif
        $daftarPraktikanAll = DaftarPraktikan::with(['praktikum', 'nilai', 'absensi', 'pengumpulanTugas'])
            ->where('user_id', $user->id)
            ->whereHas('praktikum', fn($q) => $q->whereIn('status', ['aktif', 'nonaktif']))
            ->get();

        $praktikumList = $daftarPraktikanAll->map(fn($dp) => $dp->praktikum)->filter();

        // Pilih praktikum dari dropdown atau ambil yang pertama
        $praktikumId = $request->input('praktikum_id');
        $dp = $praktikumId
            ? $daftarPraktikanAll->firstWhere('praktikum_id', $praktikumId)
            : $daftarPraktikanAll->first();

        $praktikum   = $dp?->praktikum;
        $isPublished = $dp?->nilai?->dipublikasikan ?? false;
        $moduls      = collect();

        if ($dp && $isPublished) {
            $moduls = Modul::where('praktikum_id', $dp->praktikum_id)
                ->with('tugas')
                ->orderBy('urutan')
                ->get();
        }

        return view('eoffice::manajemen-praktikum.mahasiswa.nilai', compact(
            'praktikumList',
            'praktikum',
            'dp',
            'moduls',
            'isPublished'
        ));
    }
}
