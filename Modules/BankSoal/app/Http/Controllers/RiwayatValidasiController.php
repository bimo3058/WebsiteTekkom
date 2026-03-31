<?php

namespace Modules\BankSoal\Http\Controllers\BankSoal\Gpm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        return view('banksoal::gpm.riwayat-validasi.bank-soal');
    }
}
