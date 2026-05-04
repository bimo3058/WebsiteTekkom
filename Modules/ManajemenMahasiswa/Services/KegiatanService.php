<?php

namespace Modules\ManajemenMahasiswa\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Models\RiwayatKegiatan;

class KegiatanService
{
    /**
     * Listing kegiatan dengan relasi eager-load.
     */
    public function listKegiatan(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Kegiatan::with(['kategoriKegiatan', 'bidang', 'kepengurusan', 'ketuaStudent'])
            ->when(isset($filters['bidang_id']), fn($q) => $q->byBidang((int) $filters['bidang_id']))
            ->when(isset($filters['kategori_id']), fn($q) => $q->byKategori((int) $filters['kategori_id']))
            ->when(isset($filters['tahun']), fn($q) => $q->byTahun((int) $filters['tahun']))
            ->when(isset($filters['kepengurusan_id']), fn($q) => $q->where('kepengurusan_id', $filters['kepengurusan_id']))
            ->when(isset($filters['search']), fn($q) => $q->where('nama_kegiatan', 'like', "%{$filters['search']}%"));

        return $query->orderByDesc('tanggal')->paginate($perPage);
    }

    public function findById(int $id): Kegiatan
    {
        return Kegiatan::with([
            'kategoriKegiatan',
            'bidang',
            'kepengurusan',
            'ketuaStudent',
            'lecturer',
            'riwayatKegiatan.student',
        ])->findOrFail($id);
    }

    public function create(array $data): Kegiatan
    {
        return DB::transaction(function () use ($data) {
            return Kegiatan::create($data);
        });
    }

    public function update(int $id, array $data): Kegiatan
    {
        return DB::transaction(function () use ($id, $data) {
            $kegiatan = Kegiatan::findOrFail($id);
            $kegiatan->update($data);
            return $kegiatan->fresh();
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(fn() => Kegiatan::findOrFail($id)->delete());
    }

    // -------------------------------------------------------------------------
    // Riwayat Kegiatan (peserta/panitia)
    // -------------------------------------------------------------------------

    /**
     * Daftarkan banyak mahasiswa sekaligus (bulk insert) ke riwayat kegiatan.
     * Lebih efisien daripada loop insert satu per satu.
     */
    public function syncPeserta(int $kegiatanId, array $peserta): void
    {
        // $peserta = [['student_id' => 1, 'peran' => 'panitia'], ...]
        DB::transaction(function () use ($kegiatanId, $peserta) {
            RiwayatKegiatan::where('kegiatan_id', $kegiatanId)->delete();

            $rows = array_map(fn($p) => array_merge($p, [
                'kegiatan_id' => $kegiatanId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]), $peserta);

            RiwayatKegiatan::insert($rows);
        });
    }

    public function addRiwayat(int $kegiatanId, int $studentId, string $peran): RiwayatKegiatan
    {
        return RiwayatKegiatan::firstOrCreate(
            ['kegiatan_id' => $kegiatanId, 'student_id' => $studentId],
            ['peran'        => $peran]
        );
    }

    public function removeRiwayat(int $riwayatId): void
    {
        RiwayatKegiatan::findOrFail($riwayatId)->delete();
    }

    /**
     * History kegiatan per mahasiswa — untuk profil mahasiswa.
     */
    public function getRiwayatByStudent(int $studentId, int $perPage = 10): LengthAwarePaginator
    {
        return RiwayatKegiatan::with(['kegiatan.kategoriKegiatan', 'kegiatan.bidang'])
            ->byStudent($studentId)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}