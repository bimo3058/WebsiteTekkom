<?php

namespace Modules\BankSoal\Http\Controllers\BS\GPM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\BankSoal\Services\RpsService;

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
    public function bankSoal(Request $request, \Modules\BankSoal\Services\ValidasiBankSoalService $validasiService)
    {
        $counts = $validasiService->getCounts();
        $search = $request->input('search');

        $baseQuery = DB::table('bs_mata_kuliah')
            ->join('bs_pertanyaan', 'bs_mata_kuliah.id', '=', 'bs_pertanyaan.mk_id')
            ->join('bs_review', 'bs_pertanyaan.id', '=', 'bs_review.pertanyaan_id') // INNER JOIN: Hanya ambil yang sudah di-review
            ->select(
                'bs_mata_kuliah.id as mk_id',
                'bs_mata_kuliah.kode as mk_kode',
                'bs_mata_kuliah.nama as mk_nama',
                DB::raw('COUNT(DISTINCT bs_pertanyaan.id) as jumlah_soal'), // Hitung jumlah soal yang sudah direview
                DB::raw('MAX(bs_review.created_at) as tanggal_review'), // Ambil tanggal review paling terakhir
                DB::raw("SUM(CASE WHEN bs_review.status_review IN ('Revisi Total', 'Kurang Sesuai', 'Revisi') THEN 1 ELSE 0 END) as jumlah_revisi")
            )
            ->groupBy('bs_mata_kuliah.id', 'bs_mata_kuliah.kode', 'bs_mata_kuliah.nama');

        $all_riwayat_soal = (clone $baseQuery)->get();
        
        $riwayat_soal = (clone $baseQuery)
            ->when($search, function($query) use ($search) {
                return $query->where('bs_mata_kuliah.nama', 'like', '%' . $search . '%')
                             ->orWhere('bs_mata_kuliah.kode', 'like', '%' . $search . '%');
            })
            ->get();

        return view('banksoal::gpm.riwayat-validasi.bank-soal', compact('riwayat_soal', 'all_riwayat_soal', 'counts', 'search'));
    }

    /**
     * Tampilkan riwayat validasi RPS
     * Menampilkan semua RPS dengan status 'disetujui'
     */
    public function rps(RpsService $rpsService)
    {
        $riwayat_rps = $rpsService->getDisetujui(15);

        return view('banksoal::gpm.riwayat-validasi.rps', compact('riwayat_rps'));
    }

    public function detailBankSoal($id)
    {
        // 1. Ambil info mata kuliah
        $mataKuliah = \Illuminate\Support\Facades\DB::table('bs_mata_kuliah')
            ->where('id', $id)
            ->first();

        if (!$mataKuliah) {
            abort(404, 'Data Mata Kuliah tidak ditemukan');
        }

        // 2. Ambil daftar soal yang sudah direview untuk mata kuliah tersebut
        $riwayatSoal = \Modules\BankSoal\Models\Pertanyaan::with(['jawaban', 'cpl'])
            ->join('bs_review', 'bs_pertanyaan.id', '=', 'bs_review.pertanyaan_id')
            ->where('bs_pertanyaan.mk_id', $id)
            ->select(
                'bs_pertanyaan.*',
                'bs_review.status_review',
                'bs_review.catatan',
                'bs_review.created_at as tanggal_review'
            )
            ->orderBy('bs_review.created_at', 'desc')
            ->paginate(5);

        return view('banksoal::gpm.riwayat-validasi.bank-soal-detail', compact('mataKuliah', 'riwayatSoal'));    }
}