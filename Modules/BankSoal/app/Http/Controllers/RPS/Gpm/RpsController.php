<?php

namespace Modules\BankSoal\Http\Controllers\RPS\Gpm;

use Illuminate\Routing\Controller;

/**
 * [GPM - RPS] Controller untuk review dan verifikasi RPS tingkat GPM
 * 
 * Role: GPM (Gadd Pendidikan dan Mahasiswa)
 * Fitur: RPS (Rencana Pembelajaran Semester)
 * 
 * GPM dapat melihat, mereview, dan memverifikasi RPS yang telah dikerjakan oleh dosen.
 */
class RpsController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        // TODO: Implementasi untuk menampilkan RPS yang perlu di-review
        return view('banksoal::pages.rps.gpm.index');
    }
}
