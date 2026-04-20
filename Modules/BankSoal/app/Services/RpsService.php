<?php

namespace Modules\BankSoal\Services;

use Modules\BankSoal\Models\RpsDetail;
use Modules\BankSoal\Enums\RpsStatus;

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
}
