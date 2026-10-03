<?php

namespace Modules\ManajemenMahasiswa\Services;

use App\Models\User;
use Modules\ManajemenMahasiswa\Models\ForumReport;
use Modules\ManajemenMahasiswa\Models\PengumumanApprovalRequest;
use Modules\ManajemenMahasiswa\Models\Prestasi;
use Modules\ManajemenMahasiswa\Models\RiwayatKegiatan;

/**
 * Kumpulan "tugas" yang menunggu tindakan pengguna — antrean verifikasi
 * pengumuman, verifikasi data, dan laporan forum.
 *
 * Dulu tiap angka dihitung langsung di dalam sidebar dan ditempel sebagai
 * lingkaran merah di menunya. Sekarang semuanya bermuara ke lonceng notifikasi
 * di topbar, jadi hitungannya dikumpulkan di satu tempat ini supaya tidak
 * tersebar lagi di view.
 */
class NotifikasiTugasService
{
    /**
     * Daftar tugas yang relevan untuk satu pengguna, sudah disaring per peran.
     *
     * Tugas dengan jumlah 0 tidak ikut dikembalikan, jadi pemanggil cukup
     * memeriksa apakah array-nya kosong.
     *
     * @return array<int, array{key: string, label: string, desc: string, count: int, url: string, tone: string}>
     */
    public function untuk(User $user): array
    {
        $roles = $user->roles->pluck('name')->toArray();

        // Pembagian peran disalin apa adanya dari sidebar supaya tidak ada
        // pengguna yang tiba-tiba melihat antrean yang bukan haknya.
        $isKetua         = (bool) array_intersect($roles, ['ketua_unit', 'ketua_bidang', 'ketua_himpunan']);
        $isAdminVerifier = (bool) array_intersect($roles, ['admin', 'admin_kemahasiswaan', 'superadmin', 'dpm']);
        $isStaffHimpunan = in_array('staff_himpunan', $roles) && !$isKetua && !$isAdminVerifier;
        $isVerifikatorData = (bool) array_intersect($roles, ['superadmin', 'admin_kemahasiswaan']);
        $bisaLihatLaporanForum = (bool) array_intersect(
            $roles,
            ['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm', 'dpm', 'ketua_departemen']
        );

        $tugas = [];

        // ── Verifikasi pengumuman ────────────────────────────────────────────
        // Ketua hanya melihat permintaan yang ditujukan kepadanya; admin melihat
        // seluruh antrean karena bisa menimpa keputusan siapa pun.
        if ($isKetua || $isAdminVerifier) {
            $q = PengumumanApprovalRequest::where('status', 'pending');
            if ($isKetua && !$isAdminVerifier) {
                $q->where('verifier_id', $user->id);
            }

            $tugas[] = [
                'key'   => 'verifikasi-pengumuman',
                'label' => 'Verifikasi Pengumuman',
                'desc'  => 'pengumuman menunggu persetujuan Anda',
                'count' => $q->count(),
                'url'   => route('manajemenmahasiswa.pengumuman.verifikasi.index'),
                'tone'  => 'danger',
            ];
        }

        // Staff himpunan tidak memverifikasi, tapi perlu tahu pengajuannya
        // sendiri masih menggantung — karena itu nadanya "warning", bukan "danger".
        if ($isStaffHimpunan) {
            $tugas[] = [
                'key'   => 'pengajuan-saya',
                'label' => 'Pengajuan Menunggu',
                'desc'  => 'pengumuman Anda belum diverifikasi',
                'count' => PengumumanApprovalRequest::where('requester_id', $user->id)
                    ->where('status', 'pending')->count(),
                'url'   => route('manajemenmahasiswa.pengumuman.riwayat.verifikasi'),
                'tone'  => 'warning',
            ];
        }

        // ── Verifikasi data mahasiswa ────────────────────────────────────────
        if ($isVerifikatorData) {
            $tugas[] = [
                'key'   => 'verifikasi-prestasi',
                'label' => 'Verifikasi Prestasi',
                'desc'  => 'prestasi menunggu ditinjau',
                'count' => Prestasi::pending()->count(),
                'url'   => route('manajemenmahasiswa.verifikasi.index', ['tab' => 'prestasi']),
                'tone'  => 'danger',
            ];

            $tugas[] = [
                'key'   => 'verifikasi-kegiatan',
                'label' => 'Verifikasi Kegiatan',
                'desc'  => 'riwayat kegiatan menunggu ditinjau',
                'count' => RiwayatKegiatan::manualOnly()->pending()->count(),
                'url'   => route('manajemenmahasiswa.verifikasi.index', ['tab' => 'riwayat']),
                'tone'  => 'danger',
            ];

            // Klaim reward (konversi nilai MK, SK FT 774) antreannya terpisah dari
            // verifikasi prestasi: yang diputus di sini pengajuan reward, bukan
            // benar-tidaknya prestasi. Halaman tujuannya sudah menyaring "menunggu".
            $tugas[] = [
                'key'   => 'klaim-prestasi',
                'label' => 'Klaim Prestasi',
                'desc'  => 'klaim reward prestasi menunggu keputusan',
                'count' => Prestasi::rewardDiajukan()->count(),
                'url'   => route('manajemenmahasiswa.verifikasi.reward.index'),
                'tone'  => 'danger',
            ];
        }

        // ── Laporan forum ────────────────────────────────────────────────────
        if ($bisaLihatLaporanForum) {
            $tugas[] = [
                'key'   => 'laporan-forum',
                'label' => 'Laporan Forum',
                'desc'  => 'laporan thread belum ditindaklanjuti',
                'count' => ForumReport::where('status', 'pending')->count(),
                'url'   => route('manajemenmahasiswa.forum.reports'),
                'tone'  => 'danger',
            ];
        }

        return array_values(array_filter($tugas, fn (array $t) => $t['count'] > 0));
    }

    /**
     * Total seluruh tugas tertunda; dipakai untuk angka di lonceng topbar.
     */
    public function total(User $user): int
    {
        return array_sum(array_column($this->untuk($user), 'count'));
    }
}
