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
        $daftarPraktikanAll = DaftarPraktikan::with(['praktikum', 'nilai', 'absensi', 'pengumpulanTugas', 'user', 'user.student'])
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
        $nilaiJenisMap = [];

        $daftarPraktikan = collect();

        if ($dp && $isPublished) {
            $moduls = Modul::where('praktikum_id', $dp->praktikum_id)
                ->with(['tugas', 'asprak.user'])
                ->orderBy('urutan')
                ->get();
                
            $modulIds = $moduls->pluck('id')->toArray();
            
            $daftarPraktikan = DaftarPraktikan::where('praktikum_id', $dp->praktikum_id)
                ->with(['user', 'user.student', 'nilai', 'absensi'])
                ->orderByRaw("CASE WHEN (shift IS NULL OR shift = '') THEN 1 ELSE 0 END, shift ASC")
                ->orderByRaw("CASE WHEN (kelompok IS NULL OR kelompok = '') THEN 1 ELSE 0 END, kelompok ASC")
                ->orderBy('created_at')
                ->get();

            $nilaiJenisAll = \Modules\EOffice\Models\NilaiJenisTugas::whereIn('modul_id', $modulIds)
                ->get();
            foreach ($nilaiJenisAll as $nj) {
                $nilaiJenisMap[$nj->modul_id][$nj->daftar_praktikan_id][$nj->jenis_tugas] = $nj->nilai;
            }
        }

        return view('eoffice::manajemen-praktikum.mahasiswa.nilai', compact(
            'praktikumList',
            'praktikum',
            'dp',
            'daftarPraktikan',
            'moduls',
            'isPublished',
            'nilaiJenisMap'
        ));
    }
}
