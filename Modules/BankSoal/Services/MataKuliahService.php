<?php

namespace Modules\BankSoal\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Modules\BankSoal\Models\MataKuliah;
use Modules\BankSoal\Models\DosenPengampuMk;

class MataKuliahService
{
    /**
     * Listing MK dengan jumlah soal dan dosen.
     */
    public function list(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return MataKuliah::with('cpl')
            ->withCount(['pertanyaan', 'dosenPengampu'])
            ->when(isset($filters['search']), fn($q) => $q->search($filters['search']))
            ->orderBy('kode')
            ->paginate($perPage);
    }

    /**
     * Semua MK sebagai dropdown — di-cache karena jarang berubah.
     */
    public function listAll(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('bs.mk.all', 3600, fn() =>
            MataKuliah::query()
                ->select(['id', 'kode', 'nama', 'sks', 'semester'])
                ->orderBy('kode')
                ->get()
        );
    }

    public function findById(int $id): MataKuliah
    {
        return MataKuliah::with(['cpl', 'dosenPengampu.user'])->findOrFail($id);
    }

    public function create(array $data, array $cplIds = []): MataKuliah
    {
        return DB::transaction(function () use ($data, $cplIds) {
            $mk = MataKuliah::create($data);

            if (!empty($cplIds)) {
                $mk->cpl()->sync($cplIds);
            }

            $this->flushCache();
            return $mk->load('cpl');
        });
    }

    public function update(int $id, array $data, array $cplIds = []): MataKuliah
    {
        return DB::transaction(function () use ($id, $data, $cplIds) {
            $mk = MataKuliah::findOrFail($id);
            $mk->update($data);

            if (!empty($cplIds)) {
                $mk->cpl()->sync($cplIds);
            }

            $this->flushCache();
            return $mk->fresh('cpl');
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            MataKuliah::findOrFail($id)->delete();
            $this->flushCache();
        });
    }

    // -------------------------------------------------------------------------
    // Dosen Pengampu
    // -------------------------------------------------------------------------

    /**
     * Assign dosen ke MK.
     */
    public function assignDosen(int $mkId, int $userId, bool $isRps = false): DosenPengampuMk
    {
        return DosenPengampuMk::firstOrCreate(
            ['mk_id' => $mkId, 'user_id' => $userId],
            ['is_rps' => $isRps]
        );
    }

    /**
     * Update akses RPS seorang dosen pada MK.
     */
    public function updateAksesRps(int $pengampuId, bool $isRps): DosenPengampuMk
    {
        $pengampu = DosenPengampuMk::findOrFail($pengampuId);
        $pengampu->update(['is_rps' => $isRps]);
        return $pengampu->fresh();
    }

    public function removeDosen(int $pengampuId): void
    {
        DosenPengampuMk::findOrFail($pengampuId)->delete();
    }

    /**
     * Daftar MK yang diampu oleh seorang dosen (untuk filter di halaman soal/RPS).
     */
    public function getMkByDosen(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return MataKuliah::whereHas(
            'dosenPengampu',
            fn($q) => $q->byUser($userId)
        )->orderBy('nama')->get();
    }

    private function flushCache(): void
    {
        Cache::forget('bs.mk.all');
    }
}