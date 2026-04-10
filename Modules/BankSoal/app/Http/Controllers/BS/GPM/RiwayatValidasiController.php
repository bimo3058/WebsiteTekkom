<?php

namespace Modules\BankSoal\Http\Controllers\BS\GPM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatValidasiController extends Controller
{
    /**
     * Tampilkan halaman riwayat validasi
     */
    public function index()
    {
        return view('banksoal::gpm.riwayat-validasi.index');
    }

    /**
     * Tampilkan riwayat validasi bank soal
     */
    public function bankSoal()
    {
        // Query untuk mencari Mata Kuliah yang soalnya SUDAH masuk ke bs_review
        $riwayat_soal = DB::table('bs_mata_kuliah')
            ->join('bs_pertanyaan', 'bs_mata_kuliah.id', '=', 'bs_pertanyaan.mk_id')
            ->join('bs_review', 'bs_pertanyaan.id', '=', 'bs_review.pertanyaan_id') // INNER JOIN: Hanya ambil yang sudah di-review
            ->select(
                'bs_mata_kuliah.id as mk_id',
                'bs_mata_kuliah.kode as mk_kode',
                'bs_mata_kuliah.nama as mk_nama',
                DB::raw('COUNT(DISTINCT bs_pertanyaan.id) as jumlah_soal'), // Hitung jumlah soal yang sudah direview
                DB::raw('MAX(bs_review.created_at) as tanggal_review') // Ambil tanggal review paling terakhir
            )
            ->groupBy('bs_mata_kuliah.id', 'bs_mata_kuliah.kode', 'bs_mata_kuliah.nama')
            ->get();

        return view('banksoal::gpm.riwayat-validasi.bank-soal', compact('riwayat_soal'));
    }

    /**
     * Tampilkan riwayat validasi RPS
     */
    public function rps()
    {
        return view('banksoal::gpm.riwayat-validasi.rps');
    }
}