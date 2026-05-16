<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Models\Nilai;
use Modules\EOffice\Models\DaftarPraktikan;

class NilaiController extends Controller
{
    /**
     * Daftar nilai mahasiswa pada praktikum tertentu (milik dosen).
     */
    public function index(Request $request, string $praktikumId)
    {
        $user = auth()->user();

        // Ambil semua praktikum milik dosen untuk dropdown pemilih
        $praktikums = Praktikum::where('dosen_id', $user->id)
            ->where('status', 'aktif')
            ->orderByDesc('created_at')
            ->get();

        // Jika praktikumId = '0' atau tidak valid, tampilkan halaman pilih praktikum
        $praktikum = $praktikums->firstWhere('id', $praktikumId);
        if (!$praktikum) {
            return view('eoffice::manajemen-praktikum.dosen.nilai', [
                'praktikum'  => null,
                'nilaiList'  => collect(),
                'praktikums' => $praktikums,
            ]);
        }

        // Ambil semua daftar_praktikan di praktikum ini beserta nilainya
        $daftarPraktikan = DaftarPraktikan::with(['user', 'nilai'])
            ->where('praktikum_id', $praktikum->id)
            ->get();

        // Rekapitulasi nilai per mahasiswa
        $nilaiList = $daftarPraktikan->map(function ($dp) {
            $nilai = $dp->nilai ?? null;
            return [
                'mahasiswa'       => $dp->user,
                'daftar_id'       => $dp->id,
                'nilai_tugas'     => $nilai?->nilai_tugas,
                'nilai_absensi'   => $nilai?->nilai_absensi,
                'nilai_akhir'     => $nilai?->nilai_akhir,
                'disetujui_dosen' => $nilai?->disetujui_dosen ?? false,
                'dipublikasikan'  => $nilai?->dipublikasikan ?? false,
            ];
        });

        return view('eoffice::manajemen-praktikum.dosen.nilai', compact(
            'praktikum',
            'nilaiList'
        ));
    }

    /**
     * Dosen menyetujui publikasi nilai untuk satu praktikum.
     */
    public function approve(Request $request, string $praktikumId)
    {
        $user = auth()->user();

        $praktikum = Praktikum::where('id', $praktikumId)
            ->where('dosen_id', $user->id)
            ->first();

        if (!$praktikum) {
            return back()->with('error', 'Praktikum tidak ditemukan.');
        }

        // Approve semua nilai di praktikum ini
        $daftarIds = DaftarPraktikan::where('praktikum_id', $praktikum->id)
            ->pluck('id');

        Nilai::whereIn('daftar_praktikan_id', $daftarIds)
            ->update([
                'disetujui_dosen' => true,
                'dipublikasikan'  => true,
            ]);

        return back()->with('success', 'Nilai berhasil disetujui dan dipublikasikan.');
    }
}