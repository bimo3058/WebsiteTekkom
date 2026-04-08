<?php

namespace Modules\BankSoal\Services;

use Illuminate\Support\Facades\DB;

/**
 * RpsService
 * 
 * Service untuk menangani query dan business logic RPS (Rencana Pembelajaran Semester)
 * Memisahkan database query logic dari controller untuk maintainability yang lebih baik
 */
class RpsService
{
    /**
     * Get RPS with status DIAJUKAN (Menunggu Validasi)
     * 
     * @param int $perPage
     * @return \Illuminate\Pagination\Paginator
     */
    public function getDiajukan(int $perPage = 10)
    {
        $dosenAggregate = DB::raw("STRING_AGG(DISTINCT CONCAT(LEFT(UPPER(users.name), 1), RIGHT(UPPER(users.name), 1), '|', users.name), ', ') as dosens_list");
        
        return DB::table('bs_rps_detail')
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
            ->paginate($perPage);
    }

    /**
     * Get RPS with status REVISI (Menunggu Revisi Dosen)
     * 
     * @param int $perPage
     * @return \Illuminate\Pagination\Paginator
     */
    public function getRevisi(int $perPage = 10)
    {
        $dosenAggregate = DB::raw("STRING_AGG(DISTINCT CONCAT(LEFT(UPPER(users.name), 1), RIGHT(UPPER(users.name), 1), '|', users.name), ', ') as dosens_list");
        
        return DB::table('bs_rps_detail')
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
                'bs_rps_detail.updated_at as tanggal_review',
                $dosenAggregate
            )
            ->where('bs_rps_detail.status', '=', 'revisi')
            ->groupBy('bs_rps_detail.id', 'bs_mata_kuliah.id', 'bs_mata_kuliah.kode', 'bs_mata_kuliah.nama', 'bs_rps_detail.semester', 'bs_rps_detail.tahun_ajaran', 'bs_rps_detail.status', 'bs_rps_detail.dokumen', 'bs_rps_detail.updated_at')
            ->orderBy('bs_rps_detail.updated_at', 'asc')
            ->paginate($perPage);
    }

    /**
     * Get RPS with status DISETUJUI (Sudah Direvisi dan Disetujui)
     * 
     * @param int $perPage
     * @return \Illuminate\Pagination\Paginator
     */
    public function getDisetujui(int $perPage = 10)
    {
        $dosenAggregate = DB::raw("STRING_AGG(DISTINCT CONCAT(LEFT(UPPER(users.name), 1), RIGHT(UPPER(users.name), 1), '|', users.name), ', ') as dosens_list");
        
        return DB::table('bs_rps_detail')
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
                'bs_rps_detail.updated_at as tanggal_disetujui',
                $dosenAggregate
            )
            ->where('bs_rps_detail.status', '=', 'disetujui')
            ->groupBy('bs_rps_detail.id', 'bs_mata_kuliah.id', 'bs_mata_kuliah.kode', 'bs_mata_kuliah.nama', 'bs_rps_detail.semester', 'bs_rps_detail.tahun_ajaran', 'bs_rps_detail.status', 'bs_rps_detail.dokumen', 'bs_rps_detail.updated_at')
            ->orderBy('bs_rps_detail.updated_at', 'desc')
            ->paginate($perPage);
    }
}
