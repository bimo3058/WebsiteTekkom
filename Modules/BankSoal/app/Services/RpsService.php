<?php

namespace Modules\BankSoal\Services;

use Modules\BankSoal\Models\RpsDetail;
use Modules\BankSoal\Enums\RpsStatus;
use Illuminate\Support\Facades\DB;

/**
 * RpsService
 * 
 * Service untuk menangani query dan business logic RPS (Rencana Pembelajaran Semester)
 * Menggunakan Eloquent ORM dengan eager loading untuk optimal database queries
 */
class RpsService
{
    /**
     * Get RPS with status DIAJUKAN (Menunggu Validasi)
     * Dengan eager loading mataKuliah dan dosens untuk menghindari N+1 query
     * 
     * @param int $perPage
     * @return \Illuminate\Pagination\Paginator
     */
    public function getDiajukan(int $perPage = 10)
    {
        return RpsDetail::with('mataKuliah', 'dosens')
            ->where('status', RpsStatus::DIAJUKAN->value)
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);
    }

    /**
     * Get RPS with status REVISI (Menunggu Revisi Dosen)
     * Dengan eager loading untuk optimal queries
     * 
     * @param int $perPage
     * @return \Illuminate\Pagination\Paginator
     */
    public function getRevisi(int $perPage = 10)
    {
        return RpsDetail::with('mataKuliah', 'dosens')
            ->where('status', RpsStatus::REVISI->value)
            ->orderBy('updated_at', 'asc')
            ->paginate($perPage);
    }

    /**
     * Get RPS with status DISETUJUI (Sudah Direvisi dan Disetujui)
     * Dengan eager loading untuk optimal queries
     * 
     * @param int $perPage
     * @return \Illuminate\Pagination\Paginator
     */
    public function getDisetujui(int $perPage = 10)
    {
        return RpsDetail::with('mataKuliah', 'dosens')
            ->where('status', RpsStatus::DISETUJUI->value)
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get complete RPS data for review / preview audit trail
     *
     * @param int $rpsId
     * @return array
     */
    public function getRpsReviewData(int $rpsId): array
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
                'bs_rps_detail.catatan',
                'bs_rps_detail.created_at as tanggal_diajukan',
                $dosenAggregate
            )
            ->where('bs_rps_detail.id', '=', $rpsId)
            ->groupBy('bs_rps_detail.id', 'bs_mata_kuliah.id', 'bs_mata_kuliah.kode', 'bs_mata_kuliah.nama', 'bs_rps_detail.semester', 'bs_rps_detail.tahun_ajaran', 'bs_rps_detail.status', 'bs_rps_detail.dokumen', 'bs_rps_detail.catatan', 'bs_rps_detail.created_at')
            ->first();

        if (!$rps) {
            abort(404, 'RPS tidak ditemukan');
        }

        $selectedCpls = DB::table('bs_rps_cpl as rc')
            ->join('bs_cpl as cpl', 'cpl.id', '=', 'rc.cpl_id')
            ->where('rc.rps_id', $rpsId)
            ->orderBy('cpl.kode')
            ->get(['cpl.id', 'cpl.kode', 'cpl.deskripsi']);

        $cplCpmkMappings = DB::table('bs_rps_cpmk as map')
            ->join('bs_cpl as cpl', 'cpl.id', '=', 'map.cpl_id')
            ->join('bs_cpmk as cpmk', 'cpmk.id', '=', 'map.cpmk_id')
            ->where('map.rps_id', $rpsId)
            ->select(
                'map.rps_id',
                'cpl.id as cpl_id',
                'cpl.kode as cpl_kode',
                'cpl.deskripsi as cpl_deskripsi',
                'cpmk.id as cpmk_id',
                'cpmk.kode as cpmk_kode',
                'cpmk.deskripsi as cpmk_deskripsi'
            )
            ->orderBy('cpl.kode')
            ->orderBy('cpmk.kode')
            ->get()
            ->map(function ($item) {
                $val = $item->cpmk_deskripsi ?? '';
                if (preg_match('/^\((.*?)\)\s+\((.*?)\)(?:\s+\((.*?)\))?$/', $val, $matches)) {
                    $kko = trim($matches[1]);
                    $objek = trim($matches[2]);
                    $konteks = isset($matches[3]) ? trim($matches[3]) : '';
                    $parts = ['Mahasiswa mampu', $kko, $objek];
                    if ($konteks !== '') {
                        $parts[] = $konteks;
                    }
                    $item->cpmk_deskripsi = implode(' ', $parts);
                }
                return $item;
            })
            ->groupBy('cpl_id');

        $draftCpmkItems = DB::table('bs_rps_cpmk as rpc')
            ->join('bs_cpl as cpl', 'cpl.id', '=', 'rpc.cpl_id')
            ->join('bs_cpmk as cpmk', 'cpmk.id', '=', 'rpc.cpmk_id')
            ->where('rpc.rps_id', $rpsId)
            ->orderBy('cpl.kode')
            ->orderBy('cpmk.kode')
            ->get([
                'cpl.kode as cpl_kode',
                'cpl.deskripsi as cpl_deskripsi',
                'cpmk.kode as cpmk_kode',
                'cpmk.deskripsi as cpmk_deskripsi',
            ])
            ->map(function ($item) {
                $val = $item->cpmk_deskripsi ?? '';
                if (preg_match('/^\((.*?)\)\s+\((.*?)\)(?:\s+\((.*?)\))?$/', $val, $matches)) {
                    $kko = trim($matches[1]);
                    $objek = trim($matches[2]);
                    $konteks = isset($matches[3]) ? trim($matches[3]) : '';
                    $parts = ['Mahasiswa mampu', $kko, $objek];
                    if ($konteks !== '') {
                        $parts[] = $konteks;
                    }
                    $item->cpmk_deskripsi = implode(' ', $parts);
                }
                return $item;
            });

        $dosenPengampu = DB::table('bs_rps_dosen as rd')
            ->join('users as u', 'rd.dosen_id', '=', 'u.id')
            ->select('u.id', 'u.name', 'u.email')
            ->where('rd.rps_id', $rpsId)
            ->orderBy('u.name')
            ->get();

        // Fetch parameters and existing review results
        $parameters = DB::table('bs_parameter')->where('jenis', 'rps')->get();
        $existingReview = DB::table('bs_rps_review')
            ->where('rps_id', $rpsId)
            ->first();

        $reviewChecklist = DB::table('bs_hasil_review_rps')
            ->where('rps_detail_id', $rpsId)
            ->pluck('skor', 'parameter_id');

        // Fetch history/log
        $history = DB::table('bs_audit_logs')
            ->where('subject_type', 'rps')
            ->where('subject_id', $rpsId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $totalBobot = $parameters->sum('bobot');
        return compact('rps', 'parameters', 'existingReview', 'history', 'selectedCpls', 'cplCpmkMappings', 'draftCpmkItems', 'dosenPengampu', 'totalBobot', 'reviewChecklist');
    }
}
