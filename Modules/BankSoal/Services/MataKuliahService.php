<?php

namespace Modules\BankSoal\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Modules\BankSoal\Models\MataKuliah;
use Modules\BankSoal\Models\DosenPengampuMk;
use Modules\BankSoal\Support\MasterDataUpsert;

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
                ->select(['id', 'kode', 'nama', 'sks', 'semester', 'is_active'])
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
        if (!isset($data['is_active'])) {
            $data['is_active'] = $this->getActiveStatusForSemester((int) $data['semester']);
        }

        return DB::transaction(function () use ($data, $cplIds) {
            $mk = MataKuliah::create($data);

            if (!empty($cplIds)) {
                $mk->cpl()->sync($cplIds);
            }

            $this->flushCache();
            return $mk->load('cpl');
        });
    }

    /**
     * Nilai identitas MK = seluruh field yang diisi pada form.
     *
     * is_active tidak ikut dibandingkan karena statusnya diturunkan otomatis dari
     * semester (bisa berubah sendiri saat sinkronisasi semester).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function identity(array $data): array
    {
        return [
            'kode'     => (string) ($data['kode'] ?? ''),
            'nama'     => (string) ($data['nama'] ?? ''),
            'sks'      => (int) ($data['sks'] ?? 0),
            'semester' => (string) ($data['semester'] ?? ''),
        ];
    }

    /**
     * Simpan MK dengan aturan identitas whole-row:
     * - semua field sama persis dengan baris yang sudah ada (termasuk yang sudah
     *   di-soft delete) -> baris itu dipakai/dipulihkan, tidak ada baris baru;
     * - ada satu field berbeda -> baris baru ditulis.
     *
     * @param  array<string, mixed>  $data
     * @return array{mata_kuliah: MataKuliah, created: bool, restored: bool}
     */
    public function storeIdentical(array $data): array
    {
        $result = MasterDataUpsert::firstOrCreateIdentical(
            MataKuliah::class,
            $this->identity($data),
            ['is_active' => $data['is_active'] ?? $this->getActiveStatusForSemester((int) $data['semester'])]
        );

        if ($result['created'] || $result['restored']) {
            $this->flushCache();
        }

        return [
            'mata_kuliah' => $result['record']->load('cpl'),
            'created'     => $result['created'],
            'restored'    => $result['restored'],
        ];
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

    public function toggleActive(int $id, bool $isActive): MataKuliah
    {
        $mk = MataKuliah::findOrFail($id);
        $mk->update(['is_active' => $isActive]);
        $this->flushCache();
        return $mk;
    }

    public function syncSemester(): int
    {
        $updatedCount = 0;

        MataKuliah::chunk(100, function ($mks) use (&$updatedCount) {
            foreach ($mks as $mk) {
                $shouldBeActive = $this->getActiveStatusForSemester((int) $mk->semester);
                if ((bool) $mk->is_active !== $shouldBeActive) {
                    $mk->update(['is_active' => $shouldBeActive]);
                    $updatedCount++;
                }
            }
        });

        if ($updatedCount > 0) {
            $this->flushCache();
        }

        return $updatedCount;
    }

    public function getActiveStatusForSemester(int $semester): bool
    {
        // 1. Cek apakah ada PeriodeRps aktif yang ditentukan admin
        try {
            $activePeriode = \Modules\BankSoal\Models\PeriodeRps::where('is_active', true)->first();
            if ($activePeriode && !empty($activePeriode->semester)) {
                $semesterStr = strtolower(trim($activePeriode->semester));
                if (str_contains($semesterStr, 'ganjil')) {
                    return $semester % 2 !== 0;
                }
                if (str_contains($semesterStr, 'genap')) {
                    return $semester % 2 === 0;
                }
                if (is_numeric($activePeriode->semester)) {
                    return $semester % 2 === ((int) $activePeriode->semester) % 2;
                }
            }
        } catch (\Throwable $e) {
            // Abaikan jika tabel/model belum siap
        }

        // 2. Fallback: deteksi otomatis berdasarkan bulan kalender berjalan
        // Ganjil: Juli-Desember (bulan 7-12) atau Januari (bulan 1)
        // Genap: Februari-Juni (bulan 2-6)
        $month = now()->month;
        $isGanjil = ($month >= 7 || $month == 1);

        return $isGanjil ? ($semester % 2 !== 0) : ($semester % 2 === 0);
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