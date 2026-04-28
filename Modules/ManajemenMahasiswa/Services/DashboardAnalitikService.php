<?php

namespace Modules\ManajemenMahasiswa\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\ManajemenMahasiswa\Models\DashboardAnalitik;
use Modules\ManajemenMahasiswa\Models\Kemahasiswaan;
use Modules\ManajemenMahasiswa\Models\Alumni;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Models\Pengumuman;

class DashboardAnalitikService
{
    /**
     * Ambil snapshot dashboard — cache 5 menit.
     * Ini adalah metode yang paling sering dipanggil karena jadi homepage
     * bagi ribuan user, jadi wajib di-cache.
     */
    public function getSnapshot(): array
    {
        return Cache::remember('mk.dashboard.snapshot', 300, function () {
            return [
                'total_mahasiswa_aktif' => Kemahasiswaan::aktif()->count(),
                'total_alumni'          => Alumni::count(),
                'total_kegiatan'        => Kegiatan::count(),
                'total_pengumuman'      => Pengumuman::published()->count(),

                // Tren kegiatan per bulan (12 bulan terakhir)
                'kegiatan_per_bulan' => Kegiatan::selectRaw("TO_CHAR(tanggal_mulai, 'YYYY-MM') as bulan, count(*) as total")
                    ->where('tanggal_mulai', '>=', now()->subMonths(12))
                    ->groupBy('bulan')
                    ->orderBy('bulan')
                    ->pluck('total', 'bulan'),

                // Distribusi mahasiswa per angkatan — semua status (aktif, alumni, cuti, do)
                // Tujuan: menampilkan berapa total mahasiswa yang MASUK per angkatan
                'mahasiswa_per_angkatan' => Kemahasiswaan::selectRaw('angkatan, count(*) as total')
                    ->whereNotNull('angkatan')
                    ->groupBy('angkatan')
                    ->orderBy('angkatan')
                    ->pluck('total', 'angkatan'),

                // Distribusi alumni per status pekerjaan
                'alumni_per_status'     => Alumni::selectRaw("COALESCE(status_karir, 'belum_terdata') as status, count(*) as total")
                    ->groupByRaw("COALESCE(status_karir, 'belum_terdata')")
                    ->pluck('total', 'status'),

                // Data Serapan Kerja untuk Chart
                'serapan_per_angkatan'  => app(\Modules\ManajemenMahasiswa\Services\AlumniService::class)->getSerapanPerAngkatan(),
                'distribusi_industri'   => app(\Modules\ManajemenMahasiswa\Services\AlumniService::class)->getDistribusiIndustri(),
            ];
        });
    }

    /**
     * Simpan snapshot analitik ke database (dipanggil dari Scheduler, bukan tiap request).
     */
    public function recordSnapshot(int $generatedByUserId, ?int $lecturerId = null): DashboardAnalitik
    {
        $data = [
            'total_mahasiswa_aktif' => Kemahasiswaan::aktif()->count(),
            'total_alumni'          => Alumni::count(),
            'total_kegiatan'        => Kegiatan::count(),
            'total_pengumuman'      => Pengumuman::published()->count(),
            'tanggal_generate'      => now(),
            'generated_by_user_id'  => $generatedByUserId,
            'lecturer_id'           => $lecturerId,
        ];

        return DashboardAnalitik::create($data);
    }

    /**
     * Riwayat snapshot yang pernah disimpan.
     */
    public function getHistory(int $limit = 30)
    {
        return DashboardAnalitik::with(['generatedBy', 'lecturer'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Invalidate cache dashboard — dipanggil saat ada perubahan data signifikan.
     */
    public function invalidateCache(): void
    {
        Cache::forget('mk.dashboard.snapshot');
    }
}