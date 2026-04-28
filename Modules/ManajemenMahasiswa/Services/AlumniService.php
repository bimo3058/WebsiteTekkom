<?php

namespace Modules\ManajemenMahasiswa\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\ManajemenMahasiswa\Models\Alumni;

class AlumniService
{
    /**
     * Retry otomatis jika koneksi Supabase/pgBouncer terputus.
     */
    private function withRetry(callable $callback, int $maxAttempts = 3): mixed
    {
        $attempt = 0;
        while (true) {
            try {
                return $callback();
            } catch (\Throwable $e) {
                $attempt++;
                $msg = $e->getMessage();
                $code = (string) $e->getCode();

                $isConnectionError =
                    in_array($code, ['08006', '08003', '57P01', '7'])
                    || str_contains($msg, 'server closed the connection')
                    || str_contains($msg, 'SSL negotiation')
                    || str_contains($msg, 'could not connect')
                    || str_contains($msg, 'connection unexpectedly')
                    || str_contains($msg, 'pooler.supabase.com');

                if ($isConnectionError && $attempt < $maxAttempts) {
                    usleep(200_000 * $attempt);
                    try {
                        DB::reconnect();
                    } catch (\Throwable) {
                    }
                    continue;
                }
                throw $e;
            }
        }
    }

    /**
     * Listing alumni dengan filter, search, dan pagination.
     */
    public function listAlumni(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->withRetry(function () use ($filters, $perPage) {
            $query = Alumni::with('user')
                ->when(isset($filters['angkatan']), fn($q) => $q->byAngkatan((int) $filters['angkatan']))
                ->when(isset($filters['tahun_lulus']), fn($q) => $q->byTahunLulus((int) $filters['tahun_lulus']))
                ->when(isset($filters['status_karir']), fn($q) => $q->byStatusKarir($filters['status_karir']))
                ->when(isset($filters['bidang_industri']), fn($q) => $q->byBidangIndustri($filters['bidang_industri']))
                ->when(isset($filters['search']), fn($q) => $q->search($filters['search']));

            return $query->orderByDesc('tahun_lulus')->paginate($perPage);
        });
    }

    public function findById(int $id): Alumni
    {
        return $this->withRetry(fn() => Alumni::with('user')->findOrFail($id));
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
                'total' => Alumni::count(),
                'per_angkatan' => Alumni::selectRaw('angkatan, count(*) as total')
                    ->groupBy('angkatan')
                    ->orderBy('angkatan')
                    ->pluck('total', 'angkatan'),
                'per_status' => Alumni::selectRaw("COALESCE(status_karir, 'belum_terdata') as status, count(*) as total")
                    ->groupByRaw("COALESCE(status_karir, 'belum_terdata')")
                    ->pluck('total', 'status'),
            ];
        });
    }

    /**
     * Data persentase serapan kerja per angkatan
     */
    public function getSerapanPerAngkatan(): array
    {
        $data = DB::table('mk_alumni')
            ->selectRaw("angkatan, 
                         COUNT(*) as total, 
                         SUM(CASE WHEN status_karir IN ('bekerja', 'wirausaha') THEN 1 ELSE 0 END) as bekerja")
            ->groupBy('angkatan')
            ->orderBy('angkatan')
            ->get();

        $result = [];
        foreach ($data as $row) {
            $persentase = $row->total > 0 ? round(($row->bekerja / $row->total) * 100, 1) : 0;
            $result[$row->angkatan] = $persentase;
        }

        return $result;
    }

    /**
     * Data distribusi bidang industri alumni yang bekerja
     */
    public function getDistribusiIndustri(): array
    {
        return DB::table('mk_alumni')
            ->selectRaw('bidang_industri, count(*) as total')
            ->whereNotNull('bidang_industri')
            ->groupBy('bidang_industri')
            ->orderByDesc('total')
            ->pluck('total', 'bidang_industri')
            ->toArray();
    }

    /**
     * Rata-rata waktu tunggu kerja per angkatan
     */
    public function getWaktuTungguPerAngkatan(): array
    {
        return DB::table('mk_alumni')
            ->selectRaw('angkatan, AVG(tahun_mulai_bekerja - tahun_lulus) as rata_rata')
            ->whereNotNull('tahun_mulai_bekerja')
            ->whereNotNull('tahun_lulus')
            ->whereRaw('tahun_mulai_bekerja >= tahun_lulus')
            ->groupBy('angkatan')
            ->orderBy('angkatan')
            ->pluck('rata_rata', 'angkatan')
            ->map(fn($val) => round((float) $val, 1))
            ->toArray();
    }

    private function flushCache(): void
    {
        Cache::forget('mk.alumni.summary');
        Cache::forget('mk.dashboard.snapshot');
    }
}