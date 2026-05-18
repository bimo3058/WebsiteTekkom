<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Dosen;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Nilai;
use Modules\EOffice\Models\Praktikum;

/**
 * Dosen: Lihat & approve publikasi daftar nilai + absensi.
 * Sesuai docx: dosen menyetujui untuk mempublikasikan ke mahasiswa.
 */
class NilaiController extends Controller
{
    /**
     * Daftar nilai mahasiswa pada praktikum tertentu (milik dosen).
     */
    public function index(Request $request, string $praktikumId)
    {
        $user = auth()->user();

        $praktikums = Praktikum::where('dosen_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $praktikum = $praktikums->firstWhere('id', $praktikumId);
        if (!$praktikum) {
            return view('eoffice::manajemen-praktikum.dosen.nilai', [
                'praktikum'  => null,
                'nilaiList'  => collect(),
                'praktikums' => $praktikums,
            ]);
        }

        $daftarPraktikan = DaftarPraktikan::with(['user', 'nilai'])
            ->where('praktikum_id', $praktikum->id)
            ->get();

        $nilaiList = $daftarPraktikan->map(function ($dp) {
            $nilai = $dp->nilai ?? null;
            return [
                'mahasiswa'       => $dp->user,
                'daftar_id'       => $dp->id,
                'nilai_tugas'     => $nilai?->nilai_tugas,
                'nilai_absensi'   => $nilai?->nilai_absensi,
                'nilai_akhir'     => $nilai?->nilai_akhir,
                'disetujui_koor'  => $nilai?->disetujui_koor ?? false,
                'disetujui_dosen' => $nilai?->disetujui_dosen ?? false,
                'dipublikasikan'  => $nilai?->dipublikasikan ?? false,
            ];
        });

        return view('eoffice::manajemen-praktikum.dosen.nilai', compact(
            'praktikum',
            'nilaiList',
            'praktikums'
        ));
    }

    /**
     * Dosen menyetujui dan mempublikasikan nilai ke mahasiswa.
     * Hanya bisa approve jika koor sudah approve terlebih dulu.
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

        $daftarIds = DaftarPraktikan::where('praktikum_id', $praktikum->id)->pluck('id');

        // Cek apakah koor sudah approve semua nilai
        $belumDisetujuiKoor = Nilai::whereIn('daftar_praktikan_id', $daftarIds)
            ->where('disetujui_koor', false)
            ->count();

        if ($belumDisetujuiKoor > 0) {
            return back()->with('error', 'Masih ada nilai yang belum disetujui oleh koordinator. Minta koordinator untuk approve terlebih dulu.');
        }

        // Approve & publikasikan semua nilai
        Nilai::whereIn('daftar_praktikan_id', $daftarIds)
            ->update([
                'disetujui_dosen' => true,
                'dipublikasikan'  => true,
            ]);

        return back()->with('success', 'Nilai berhasil disetujui dan dipublikasikan ke mahasiswa.');
    }
}
