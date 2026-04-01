<?php

namespace Modules\BankSoal\Http\Controllers\RPS\Gpm;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

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
        return view('banksoal::pages.rps.gpm.index');
    }

    /*
     * Menampilkan daftar RPS yang menunggu validasi
     */
    public function validasiRps()
    {
        $dosenAggregate = DB::raw("STRING_AGG(DISTINCT CONCAT(LEFT(UPPER(users.name), 1), RIGHT(UPPER(users.name), 1), '|', users.name), ', ') as dosens_list");
        
        $rpsData = DB::table('bs_rps_detail')
            ->join('bs_mata_kuliah', 'bs_rps_detail.mk_id', '=', 'bs_mata_kuliah.id')
            ->leftJoin('bs_rps_dosen', 'bs_rps_detail.id', '=', 'bs_rps_dosen.rps_id')
            ->leftJoin('users', 'bs_rps_dosen.dosen_id', '=', 'users.id')
            ->select(
                'bs_rps_detail.id as rps_id',
                'bs_mata_kuliah.id as mk_id',
                'bs_mata_kuliah.kode',
                'bs_mata_kuliah.nama as mk_nama',
                'bs_rps_detail.semester',
                'bs_rps_detail.tahun_ajaran',
                'bs_rps_detail.status',
                'bs_rps_detail.dokumen',
                'bs_rps_detail.created_at as tanggal_diajukan',
                $dosenAggregate
            )
            ->where('bs_rps_detail.status', '=', 'diajukan')
            ->groupBy('bs_rps_detail.id', 'bs_mata_kuliah.id', 'bs_mata_kuliah.kode', 'bs_mata_kuliah.nama', 'bs_rps_detail.semester', 'bs_rps_detail.tahun_ajaran', 'bs_rps_detail.status', 'bs_rps_detail.dokumen', 'bs_rps_detail.created_at')
            ->orderBy('bs_rps_detail.created_at', 'asc')
            ->paginate(10);

        return view('banksoal::gpm.validasi-rps', compact('rpsData'));
    }

    /**
     * Menampilkan detail RPS untuk di-review
     */
    public function validasiRpsReview($rpsId)
    {
        $dosenAggregate = DB::raw("STRING_AGG(DISTINCT CONCAT(LEFT(UPPER(users.name), 1), RIGHT(UPPER(users.name), 1), '|', users.name, '|', users.email), ', ') as dosens_list");
        
        $rps = DB::table('bs_rps_detail')
            ->join('bs_mata_kuliah', 'bs_rps_detail.mk_id', '=', 'bs_mata_kuliah.id')
            ->leftJoin('bs_rps_dosen', 'bs_rps_detail.id', '=', 'bs_rps_dosen.rps_id')
            ->leftJoin('users', 'bs_rps_dosen.dosen_id', '=', 'users.id')
            ->select(
                'bs_rps_detail.id as rps_id',
                'bs_mata_kuliah.id as mk_id',
                'bs_mata_kuliah.kode',
                'bs_mata_kuliah.nama as mk_nama',
                'bs_rps_detail.semester',
                'bs_rps_detail.tahun_ajaran',
                'bs_rps_detail.status',
                'bs_rps_detail.dokumen',
                'bs_rps_detail.created_at as tanggal_diajukan',
                $dosenAggregate
            )
            ->where('bs_rps_detail.id', '=', $rpsId)
            ->groupBy('bs_rps_detail.id', 'bs_mata_kuliah.id', 'bs_mata_kuliah.kode', 'bs_mata_kuliah.nama', 'bs_rps_detail.semester', 'bs_rps_detail.tahun_ajaran', 'bs_rps_detail.status', 'bs_rps_detail.dokumen', 'bs_rps_detail.created_at')
            ->first();

        if (!$rps) {
            abort(404, 'RPS tidak ditemukan');
        }

        // Fetch parameters and existing review results
        $parameters = DB::table('bs_parameter')->get();
        $existingReview = DB::table('bs_rps_review')
            ->where('rps_id', $rpsId)
            ->first();

        // Fetch history/log
        $history = DB::table('bs_audit_logs')
            ->where('subject_type', 'rps')
            ->where('subject_id', $rpsId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('banksoal::gpm.validasi-rps-review', compact('rps', 'parameters', 'existingReview', 'history'));
    }
}
