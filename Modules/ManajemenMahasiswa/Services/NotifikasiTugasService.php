<?php

namespace Modules\ManajemenMahasiswa\Services;

use App\Models\User;
use Modules\ManajemenMahasiswa\Models\ForumReport;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Policies\KegiatanPolicy;
use Modules\ManajemenMahasiswa\Support\RoleHimpunan;
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
        $isKetua         = (bool) array_intersect($roles, ['ketua_unit', 'wakil_ketua_unit', 'ketua_bidang', 'wakil_ketua_bidang', 'ketua_himpunan', 'wakil_ketua_himpunan']);
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
            //
            // Klaim yang diajukan lewat 1 tahun sejak tanggal prestasi tetap masuk
            // antrean (mahasiswa tidak dihalangi), jadi jumlahnya disebut di sini:
            // admin berhak menyetujui atau menolaknya.
            $klaimDiajukan = Prestasi::rewardDiajukan();
            $klaimTerlambat = (clone $klaimDiajukan)
                ->whereRaw("(tanggal + interval '1 year') < claimed_at")
                ->count();

            $tugas[] = [
                'key'   => 'klaim-prestasi',
                'label' => 'Klaim Prestasi',
                'desc'  => 'klaim reward prestasi menunggu keputusan'
                    . ($klaimTerlambat > 0 ? " ({$klaimTerlambat} diajukan lewat 1 tahun, Anda berhak menyetujui atau menolak)" : ''),
                'count' => $klaimDiajukan->count(),
                'url'   => route('manajemenmahasiswa.verifikasi.reward.index'),
                'tone'  => 'danger',
            ];
        }

        // ── Alur tinjau proker (S-82) ────────────────────────────────────────
        array_push($tugas, ...$this->tugasTinjauan($user));

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
     * Tugas alur tinjau Rencana Proker & Pelaksanaan (S-82, 11 Nov 2026).
     *
     *  • Ketua pelaksana: proker yang menunggu ia lengkapi (Menunggu Dilengkapi) atau
     *    perbaiki (Perlu Perbaikan) — muncul begitu KABID membuat proker / menolak.
     *  • Peninjau: kiriman Menunggu Tinjauan di bidang/unitnya (KABID, WAKABID, KANIT,
     *    WAKANIT) atau proker yang ia buat sendiri (KAHIM/admin yang membuat proker).
     *    KAHIM tidak diberi tahu semua bidang supaya loncengnya tidak banjir; ia tetap
     *    bisa meninjau dari daftar (filter Status Tinjauan).
     *
     * Dihitung dari status, jadi hilang sendiri setelah dikirim/disetujui/dikembalikan.
     * Bila hanya satu, tautannya langsung ke detail; bila lebih, ke daftar tersaring.
     *
     * @return list<array>
     */
    private function tugasTinjauan(User $user): array
    {
        $tahap = [
            ['status' => Kegiatan::STATUS_DRAFT, 'route' => 'proker', 'nama' => 'Rencana Proker', 'jenis' => 'rencana proker'],
            ['status' => Kegiatan::STATUS_DISETUJUI, 'route' => 'pelaksanaan', 'nama' => 'Laporan Pelaksanaan', 'jenis' => 'laporan pelaksanaan'],
        ];
        $tugas = [];

        if ($user->hasRole(KegiatanPolicy::ROLE_KETUA_PELAKSANA) && KegiatanPolicy::bisaJadiPengelola($user)) {
            foreach ($tahap as $t) {
                foreach ([
                    Kegiatan::TINJAU_PERBAIKAN  => ['Perbaiki ', 'dikembalikan KABID dengan catatan', 'danger'],
                    Kegiatan::TINJAU_DILENGKAPI => ['Lengkapi ', 'menunggu Anda lengkapi lalu kirim untuk ditinjau', 'warning'],
                ] as $kunci => [$awalan, $desc, $nada]) {
                    $q = Kegiatan::where('status', $t['status'])->tinjauan($kunci)
                        ->whereHas('ketuaPelaksana', fn ($s) => $s->where('user_id', $user->id));
                    $tugas[] = $this->tugasDari($q, "tinjau-{$t['route']}-{$kunci}", $awalan . $t['nama'], "{$t['jenis']} {$desc}", $nada, $t['route'], $kunci);
                }
            }
        }

        if ($user->hasAnyRole(KegiatanPolicy::PEMBUAT_PROKER)) {
            $bidangId = RoleHimpunan::isPimpinanBidang($user) ? RoleHimpunan::bidangId($user) : null;
            foreach ($tahap as $t) {
                $q = Kegiatan::where('status', $t['status'])->tinjauan(Kegiatan::TINJAU_DITINJAU)
                    ->where(function ($w) use ($user, $bidangId, $t) {
                        // Pembuat proker: rencana yang ia buat, dan salinan Pelaksanaan-nya.
                        $t['status'] === Kegiatan::STATUS_DRAFT
                            ? $w->where('user_id', $user->id)
                            : $w->whereHas('rencana', fn ($r) => $r->where('user_id', $user->id));
                        if ($bidangId) {
                            $w->orWhereHas('bidangs', fn ($b) => $b->where('mk_bidang.id', $bidangId));
                        }
                    });
                $tugas[] = $this->tugasDari($q, "tinjau-{$t['route']}-masuk", 'Tinjau ' . $t['nama'], "{$t['jenis']} menunggu tinjauan Anda", 'danger', $t['route'], Kegiatan::TINJAU_DITINJAU);
            }
        }

        return $tugas;
    }

    private function tugasDari($query, string $key, string $label, string $desc, string $tone, string $route, string $kunci): array
    {
        $ids = (clone $query)->limit(2)->pluck('mk_kegiatan.id');

        return [
            'key'   => $key,
            'label' => $label,
            'desc'  => $desc,
            'count' => $ids->count() < 2 ? $ids->count() : $query->count(),
            'url'   => $ids->count() === 1
                ? route("manajemenmahasiswa.{$route}.show", $ids->first())
                : route("manajemenmahasiswa.{$route}.index", ['tinjauan' => $kunci]),
            'tone'  => $tone,
        ];
    }

    /**
     * Total seluruh tugas tertunda; dipakai untuk angka di lonceng topbar.
     */
    public function total(User $user): int
    {
        return array_sum(array_column($this->untuk($user), 'count'));
    }
}
