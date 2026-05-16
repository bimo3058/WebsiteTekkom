<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Mahasiswa;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\EOffice\Models\DaftarPraktikan;
use Modules\EOffice\Models\Nilai;

/**
 * Mahasiswa: Lihat daftar nilai (hanya jika sudah disetujui koor & dosen).
 * Sesuai docx: nilai diperlihatkan ke praktikan apabila sudah disetujui oleh koor dan dosen.
 */
class NilaiController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $daftarPraktikan = DaftarPraktikan::with('praktikum')
            ->where('user_id', $user->id)
            ->whereHas('praktikum', fn($q) => $q->where('status', 'aktif'))
            ->get();

        // Hanya tampilkan nilai yang sudah dipublikasikan
        $nilaiList = Nilai::whereIn('daftar_praktikan_id', $daftarPraktikan->pluck('id'))
            ->where('dipublikasikan', true)
            ->with('daftarPraktikan.praktikum')
            ->get();

        return view('eoffice::manajemen-praktikum.mahasiswa.nilai', compact(
            'nilaiList',
            'daftarPraktikan'
        ));
    }
}
