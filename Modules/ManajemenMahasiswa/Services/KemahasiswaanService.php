<?php

namespace Modules\ManajemenMahasiswa\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Modules\ManajemenMahasiswa\Models\Kemahasiswaan;
use Modules\ManajemenMahasiswa\Models\Prestasi;

class KemahasiswaanService
{
    /**
     * Listing mahasiswa dengan filter, search, dan pagination.
     * Di-cache per kombinasi filter untuk mengurangi DB hit pada traffic tinggi.
     */
    public function listMahasiswa(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Kemahasiswaan::with('user')
            ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['angkatan']), fn($q) => $q->byAngkatan((int) $filters['angkatan']))
            ->when(isset($filters['search']), fn($q) => $q->search($filters['search']));

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    /**
     * Detail mahasiswa beserta prestasi.
     */
    public function findById(int $id): Kemahasiswaan
    {
        return Kemahasiswaan::with(['user', 'prestasi'])
            ->findOrFail($id);
    }

    public function getByUser(int $userId): ?Kemahasiswaan
    {
        return Kemahasiswaan::with('prestasi')
            ->where('user_id', $userId)
            ->first();
    }

    public function create(array $data): Kemahasiswaan
    {
        return DB::transaction(function () use ($data) {
            $mahasiswa = Kemahasiswaan::create($data);
            $this->flushSummaryCache();
            return $mahasiswa;
        });
    }

    /**
     * Update data mahasiswa.
     */
    public function update(int $id, array $data): Kemahasiswaan
    {
        return DB::transaction(function () use ($id, $data) {
            $mahasiswa = Kemahasiswaan::findOrFail($id);
            $mahasiswa->update($data);
            $this->flushSummaryCache();
            return $mahasiswa->fresh();
        });
    }

    /**
     * Hapus data mahasiswa.
     */
    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            Kemahasiswaan::findOrFail($id)->delete();
            $this->flushSummaryCache();
        });
    }

    /**
     * Tambahkan prestasi ke mahasiswa tertentu.
     */
    public function addPrestasi(int $kemahasiswaanId, array $data): Prestasi
    {
        return Prestasi::create(array_merge($data, [
            'kemahasiswaan_id' => $kemahasiswaanId,
        ]));
    }

    /**
     * Hapus prestasi.
     */
    public function deletePrestasi(int $prestasiId): void
    {
        Prestasi::findOrFail($prestasiId)->delete();
    }

    /**
     * Summary stats — di-cache 10 menit karena dibaca dashboard.
     */
    public function getSummary(): array
    {
        return Cache::remember('mk.kemahasiswaan.summary', 600, function () {
            return [
                'total_aktif'  => Kemahasiswaan::aktif()->count(),
                'total_alumni' => Kemahasiswaan::alumni()->count(),
                'total_cuti'   => Kemahasiswaan::where('status', Kemahasiswaan::STATUS_CUTI)->count(),
                'per_angkatan' => Kemahasiswaan::aktif()
                    ->selectRaw('angkatan, count(*) as total')
                    ->groupBy('angkatan')
                    ->orderBy('angkatan')
                    ->pluck('total', 'angkatan'),
            ];
        });
    }

    private function flushSummaryCache(): void
    {
        Cache::forget('mk.kemahasiswaan.summary');
    }
}