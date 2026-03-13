<?php

namespace Modules\ManajemenMahasiswa\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\ManajemenMahasiswa\Models\PengurusHimaskom;

class PengurusHimaskomService
{
    /**
     * Listing pengurus dengan filter.
     */
    public function list(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return PengurusHimaskom::with(['student', 'kepengurusan'])
            ->when(isset($filters['kepengurusan_id']), fn($q) => $q->byKepengurusan((int) $filters['kepengurusan_id']))
            ->when(isset($filters['divisi']), fn($q) => $q->byDivisi($filters['divisi']))
            ->when(isset($filters['status']), fn($q) => $q->where('status_keaktifan', $filters['status']))
            ->when(isset($filters['search']), fn($q) => $q->whereHas(
                'student',
                fn($sq) => $sq->where('name', 'like', "%{$filters['search']}%")
            ))
            ->orderBy('divisi')
            ->paginate($perPage);
    }

    public function findById(int $id): PengurusHimaskom
    {
        return PengurusHimaskom::with(['student', 'kepengurusan'])->findOrFail($id);
    }

    public function create(array $data): PengurusHimaskom
    {
        return DB::transaction(fn() => PengurusHimaskom::create($data));
    }

    public function update(int $id, array $data): PengurusHimaskom
    {
        return DB::transaction(function () use ($id, $data) {
            $pengurus = PengurusHimaskom::findOrFail($id);
            $pengurus->update($data);
            return $pengurus->fresh();
        });
    }

    public function delete(int $id): void
    {
        PengurusHimaskom::findOrFail($id)->delete();
    }

    /**
     * Sync pengurus untuk satu periode (berguna saat input massal SK kepengurusan).
     * Hapus pengurus lama periode ini, ganti dengan data baru.
     */
    public function syncByKepengurusan(int $kepengurusanId, array $pengurusList): void
    {
        DB::transaction(function () use ($kepengurusanId, $pengurusList) {
            PengurusHimaskom::where('kepengurusan_id', $kepengurusanId)->delete();

            $rows = array_map(fn($p) => array_merge($p, [
                'kepengurusan_id' => $kepengurusanId,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]), $pengurusList);

            PengurusHimaskom::insert($rows);
        });
    }

    /**
     * Nonaktifkan semua pengurus pada periode tertentu.
     */
    public function nonaktifkanPeriode(int $kepengurusanId): int
    {
        return PengurusHimaskom::where('kepengurusan_id', $kepengurusanId)
            ->update(['status_keaktifan' => PengurusHimaskom::STATUS_NONAKTIF]);
    }
}