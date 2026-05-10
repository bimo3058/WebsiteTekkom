<?php

namespace Modules\BankSoal\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\BankSoal\Models\Rps;
use Modules\BankSoal\Models\RpsDetail;
use Modules\BankSoal\Models\HasilReviewRps;
use Modules\BankSoal\Models\Parameter;

class RpsService
{
    private const DISK = 'public';

    // =========================================================================
    // RPS (header)
    // =========================================================================

    public function list(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return Rps::with(['mataKuliah', 'dosen'])
            ->withCount('detail')
            ->when(isset($filters['mk_id']), fn($q) => $q->byMk((int) $filters['mk_id']))
            ->when(isset($filters['semester']), fn($q) => $q->bySemester($filters['semester']))
            ->when(isset($filters['dosen_id']), fn($q) => $q->where('dosen_id', $filters['dosen_id']))
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function findById(int $id): Rps
    {
        return Rps::with(['mataKuliah', 'dosen', 'detail.hasilReview.parameter'])->findOrFail($id);
    }

    public function create(int $dosenUserId, array $data): Rps
    {
        return DB::transaction(fn() => Rps::create(array_merge($data, [
            'dosen_id' => $dosenUserId,
        ])));
    }

    public function update(int $id, array $data): Rps
    {
        return DB::transaction(function () use ($id, $data) {
            $rps = Rps::findOrFail($id);
            $rps->update($data);
            return $rps->fresh();
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(fn() => Rps::findOrFail($id)->delete());
    }

    // =========================================================================
    // RPS Detail (versi/upload dokumen)
    // =========================================================================

    /**
     * Upload versi baru dokumen RPS.
     * Tiap upload = satu RpsDetail baru dengan status draft.
     */
    public function uploadDokumen(int $rpsId, UploadedFile $file): RpsDetail
    {
        return DB::transaction(function () use ($rpsId, $file) {
            Rps::findOrFail($rpsId); // pastikan RPS ada

            $path    = $file->store("bs_rps/{$rpsId}", self::DISK);
            $dokumen = file_get_contents($file->getRealPath()); // bytea

            return RpsDetail::create([
                'rps_id'     => $rpsId,
                'dokumen'    => $dokumen,
                'status_rps' => RpsDetail::STATUS_DRAFT,
                'nilai_akhir' => 0,
            ]);
        });
    }

    /**
     * Ambil detail terbaru dari satu RPS.
     */
    public function getDetailTerbaru(int $rpsId): ?RpsDetail
    {
        return RpsDetail::where('rps_id', $rpsId)->latest()->first();
    }

    // =========================================================================
    // Workflow RpsDetail
    // =========================================================================

    public function ajukanDetail(int $detailId): RpsDetail
    {
        return $this->transisiDetailStatus($detailId, RpsDetail::STATUS_DIAJUKAN);
    }

    public function setujuiDetail(int $detailId): RpsDetail
    {
        return $this->transisiDetailStatus($detailId, RpsDetail::STATUS_DISETUJUI);
    }

    public function kembalikanRevisi(int $detailId, string $catatan): RpsDetail
    {
        return DB::transaction(function () use ($detailId, $catatan) {
            $detail = $this->transisiDetailStatus($detailId, RpsDetail::STATUS_REVISI);
            $detail->update(['catatan' => $catatan]);
            return $detail->fresh();
        });
    }

    // =========================================================================
    // Review RPS (penilaian per parameter)
    // =========================================================================

    /**
     * Simpan hasil review satu RpsDetail oleh reviewer.
     * Input: [{ parameter_id, skor }, ...]
     * Nilai akhir dihitung otomatis dari skor tertimbang.
     */
    public function simpanReview(int $detailId, array $penilaian): RpsDetail
    {
        return DB::transaction(function () use ($detailId, $penilaian) {
            $detail = RpsDetail::with('hasilReview')->findOrFail($detailId);

            if ($detail->status_rps !== RpsDetail::STATUS_DIAJUKAN) {
                throw new \RuntimeException('Hanya RPS dengan status diajukan yang dapat direview.');
            }

            // Hapus review lama, simpan baru
            HasilReviewRps::where('rps_detail_id', $detailId)->delete();

            $rows = array_map(fn($p) => [
                'rps_detail_id' => $detailId,
                'parameter_id'  => $p['parameter_id'],
                'skor'          => $p['skor'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ], $penilaian);

            HasilReviewRps::insert($rows);

            // Hitung nilai akhir tertimbang
            $nilaiAkhir = $this->hitungNilaiAkhir($detailId);
            $detail->update(['nilai_akhir' => $nilaiAkhir]);

            return $detail->fresh('hasilReview');
        });
    }

    /**
     * Ambil semua parameter penilaian yang tersedia.
     */
    public function getParameter(): \Illuminate\Database\Eloquent\Collection
    {
        return Parameter::orderBy('aspek')->get();
    }

    // =========================================================================
    // Dashboard Queries
    // =========================================================================

    public function getDiajukan(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return RpsDetail::with('mataKuliah', 'dosens')
            ->where('status', 'diajukan')
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get RpsDetail records with status 'revisi' for dashboard
     * Eager loads relationships to avoid N+1 queries
     */
    public function getRevisi(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return RpsDetail::with('mataKuliah', 'dosens')
            ->where('status', 'revisi')
            ->orderBy('updated_at', 'asc')
            ->limit($limit)
            ->get();
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    private function transisiDetailStatus(int $detailId, string $newStatus): RpsDetail
    {
        return DB::transaction(function () use ($detailId, $newStatus) {
            $detail = RpsDetail::findOrFail($detailId);

            if (!$detail->canTransitionTo($newStatus)) {
                throw new \RuntimeException(
                    "Tidak bisa mengubah status RPS dari '{$detail->status_rps}' ke '{$newStatus}'."
                );
            }

            $detail->update(['status_rps' => $newStatus]);
            return $detail->fresh();
        });
    }

    private function hitungNilaiAkhir(int $detailId): float
    {
        $reviews    = HasilReviewRps::with('parameter')->where('rps_detail_id', $detailId)->get();
        $totalBobot = $reviews->sum(fn($r) => $r->parameter->bobot ?? 0);

        if ($totalBobot === 0) {
            return 0;
        }

        $skorTertimbang = $reviews->sum(fn($r) => $r->skor * ($r->parameter->bobot ?? 0));

        return round($skorTertimbang / $totalBobot, 2);
    }
}