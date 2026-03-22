<?php

namespace Modules\ManajemenMahasiswa\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Modules\ManajemenMahasiswa\Models\Alumni;

class AlumniService
{
    /**
     * Listing alumni dengan filter, search, dan pagination.
     */
    public function listAlumni(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Alumni::with('user')
            ->when(isset($filters['angkatan']), fn($q) => $q->byAngkatan((int) $filters['angkatan']))
            ->when(isset($filters['tahun_lulus']), fn($q) => $q->byTahunLulus((int) $filters['tahun_lulus']))
            ->when(isset($filters['status']), fn($q) => $q->byStatus($filters['status']))
            ->when(isset($filters['search']), fn($q) => $q->search($filters['search']));

        return $query->orderByDesc('tahun_lulus')->paginate($perPage);
    }

    public function findById(int $id): Alumni
    {
        return Alumni::with('user')->findOrFail($id);
    }

    public function create(array $data): Alumni
    {
        return DB::transaction(function () use ($data) {
            $alumni = Alumni::create($data);
            $this->flushCache();
            return $alumni;
        });
    }

    public function update(int $id, array $data): Alumni
    {
        return DB::transaction(function () use ($id, $data) {
            $alumni = Alumni::findOrFail($id);
            $alumni->update($data);
            $this->flushCache();
            return $alumni->fresh();
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            Alumni::findOrFail($id)->delete();
            $this->flushCache();
        });
    }

    /**
     * Summary distribusi alumni — cache 10 menit.
     */
    public function getSummary(): array
    {
        return Cache::remember('mk.alumni.summary', 600, function () {
            return [
                'total'        => Alumni::count(),
                'per_angkatan' => Alumni::selectRaw('angkatan, count(*) as total')
                    ->groupBy('angkatan')
                    ->orderBy('angkatan')
                    ->pluck('total', 'angkatan'),
                'per_status'   => Alumni::selectRaw('status_posisi_pekerjaan, count(*) as total')
                    ->groupBy('status_posisi_pekerjaan')
                    ->pluck('total', 'status_posisi_pekerjaan'),
            ];
        });
    }

    private function flushCache(): void
    {
        Cache::forget('mk.alumni.summary');
        Cache::forget('mk.dashboard.snapshot');
    }
}