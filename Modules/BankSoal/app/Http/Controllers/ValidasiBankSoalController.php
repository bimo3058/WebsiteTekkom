<?php

namespace Modules\BankSoal\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValidasiBankSoalController extends Controller
{
    public function index()
    {
        // Ambil soal pertama yang BELUM di-review
        $soal = DB::table('bs_pertanyaan')
            ->leftJoin('bs_cpl', 'bs_pertanyaan.cpl_id', '=', 'bs_cpl.id')
            ->leftJoin('bs_mata_kuliah', 'bs_pertanyaan.mk_id', '=', 'bs_mata_kuliah.id')
            // INI KUNCI UTAMANYA: Lewati soal yang ID-nya sudah ada di tabel bs_review
            ->whereNotIn('bs_pertanyaan.id', function($query) {
                $query->select('pertanyaan_id')
                      ->from('bs_review');
            })
            ->select(
                'bs_pertanyaan.*',
                'bs_cpl.kode as cpl_kode',
                'bs_cpl.deskripsi as cpl_deskripsi',
                'bs_mata_kuliah.kode as mk_kode',
                'bs_mata_kuliah.nama as mk_nama'
            )
            ->orderBy('bs_pertanyaan.id', 'asc') // Urutkan dari ID terkecil ke terbesar
            ->first();

        // Kalau $soal kosong, berarti semua soal sudah di-review!
        if (!$soal) {
            // Bisa diarahkan ke halaman sukses atau dashboard
            return redirect()->route('banksoal.dashboard')->with('success', 'Semua soal telah selesai di-review!');
        }

        $jawaban = DB::table('bs_jawaban')
            ->where('soal_id', $soal->id)
            ->get();

        return view('banksoal::gpm.validasi-bank-soal-review', compact('soal', 'jawaban'));
    }
}
