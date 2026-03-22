<?php

namespace Modules\ManajemenMahasiswa\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Modules\ManajemenMahasiswa\Models\Pengumuman;

class PengumumanService
{
    /**
     * Listing pengumuman untuk admin/operator.
     */
    public function listAll(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return Pengumuman::with('author')
            ->when(isset($filters['status']), fn($q) => $q->where('status_publish', $filters['status']))
            ->when(isset($filters['audience']), fn($q) => $q->forAudience($filters['audience']))
            ->when(isset($filters['search']), fn($q) => $q->where('judul', 'like', "%{$filters['search']}%"))
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Listing pengumuman publik untuk mahasiswa/alumni/dosen.
     * Di-cache karena sering dibaca oleh ribuan user.
     */
    public function listPublished(string $audience, int $perPage = 10): LengthAwarePaginator
    {
        // Pagination tidak bisa di-cache sepenuhnya; cache hanya untuk count
        return Pengumuman::with('author')
            ->published()
            ->forAudience($audience)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function findById(int $id): Pengumuman
    {
        return Pengumuman::with(['author', 'repoMulmed'])->findOrFail($id);
    }

    public function create(int $userId, array $data): Pengumuman
    {
        return DB::transaction(function () use ($userId, $data) {
            $pengumuman = Pengumuman::create(array_merge($data, ['user_id' => $userId]));
            $this->flushCache();
            return $pengumuman;
        });
    }

    public function update(int $id, array $data): Pengumuman
    {
        return DB::transaction(function () use ($id, $data) {
            $pengumuman = Pengumuman::findOrFail($id);
            $pengumuman->update($data);
            $this->flushCache();
            return $pengumuman->fresh();
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            Pengumuman::findOrFail($id)->delete();
            $this->flushCache();
        });
    }

    /**
     * Publish pengumuman (dari draft).
     */
    public function publish(int $id): Pengumuman
    {
        return $this->update($id, ['status_publish' => Pengumuman::STATUS_PUBLISHED, 'scheduled_at' => null]);
    }

    /**
     * Jadwalkan pengumuman.
     */
    public function schedule(int $id, \DateTimeInterface $scheduledAt): Pengumuman
    {
        return $this->update($id, [
            'status_publish' => Pengumuman::STATUS_SCHEDULED,
            'scheduled_at'   => $scheduledAt,
        ]);
    }

    /**
     * Proses semua pengumuman yang sudah terjadwal — panggil via Command/Scheduler.
     */
    public function processScheduled(): int
    {
        $count = Pengumuman::scheduledReady()->count();

        Pengumuman::scheduledReady()->update([
            'status_publish' => Pengumuman::STATUS_PUBLISHED,
        ]);

        if ($count > 0) {
            $this->flushCache();
        }

        return $count;
    }

    private function flushCache(): void
    {
        Cache::forget('mk.dashboard.snapshot');
    }
}