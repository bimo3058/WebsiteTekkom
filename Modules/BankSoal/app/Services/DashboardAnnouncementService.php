<?php

namespace Modules\BankSoal\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\BankSoal\Enums\PendaftaranStatus;
use Modules\BankSoal\Models\Komprehensif\PendaftarUjian;
use Modules\BankSoal\Models\Komprehensif\PeriodeUjian;
use Modules\BankSoal\Models\RPS\RpsReview;
use Modules\BankSoal\Models\RPS\Rps;

class DashboardAnnouncementService
{
    /**
     * Generate role-specific announcements for the main dashboard.
     *
     * @param  int  $userId
     * @param  array  $userRoles
     * @return array<int, array{title: string, body: string, date: string, module: string, pinned: bool, url: string|null, _ts: int, badge: string}>
     */
    public function getForDashboard(int $userId, array $userRoles = []): array
    {
        $items = collect();

        // Detect roles
        $isMahasiswa = in_array('mahasiswa', $userRoles);
        $isDosen = in_array('dosen', $userRoles) || in_array('dosen_koor', $userRoles);
        $isGpm = in_array('gpm', $userRoles);

        // Mahasiswa notifications
        if ($isMahasiswa) {
            $this->getMahasiswaNotifications($items, $userId);
        }

        // Dosen notifications
        if ($isDosen) {
            $this->getDosenNotifications($items, $userId);
        }

        // GPM notifications
        if ($isGpm) {
            $this->getGpmNotifications($items, $userId);
        }

        return $items
            ->sortByDesc(fn ($item) => [$item['pinned'] ? 1 : 0, $item['_ts']])
            ->take(5)
            ->values()
            ->toArray();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // MAHASISWA NOTIFICATIONS
    // ──────────────────────────────────────────────────────────────────────────

    private function getMahasiswaNotifications(Collection &$items, int $userId): void
    {
        // 1. Periode pendaftaran ujian komprehensif
        $activePeriodes = PeriodeUjian::currentlyActive()->get();
        $periodeIds = $activePeriodes->pluck('id');

        $userRegistrations = PendaftarUjian::where('mahasiswa_id', $userId)
            ->whereIn('periode_ujian_id', $periodeIds)
            ->with(['jadwal', 'periode'])
            ->get()
            ->keyBy('periode_ujian_id');

        foreach ($activePeriodes as $periode) {
            $pendaftar = $userRegistrations->get($periode->id);

            if (!$pendaftar) {
                // Periode dibuka — mahasiswa belum mendaftar
                if ($periode->pendaftaran_terbuka) {
                    $this->notifPeriodeDibuka($items, $periode);
                }
                continue;
            }

            $status = $pendaftar->status_pendaftaran;

            if ($status === PendaftaranStatus::Approved) {
                if ($pendaftar->jadwal) {
                    // Jadwal dikonfirmasi
                    $this->notifJadwalDikonfirmasi($items, $pendaftar, $periode);
                } else {
                    // Disetujui — menunggu jadwal
                    $this->notifDisetujui($items, $pendaftar, $periode);
                }
            } elseif ($status === PendaftaranStatus::Rejected) {
                // Ditolak
                $this->notifDitolak($items, $pendaftar, $periode);
            }
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // DOSEN NOTIFICATIONS
    // ──────────────────────────────────────────────────────────────────────────

    private function getDosenNotifications(Collection &$items, int $userId): void
    {
        // 1. RPS yang sudah direvisi/ditinjau oleh GPM
        $rpsReviewed = RpsReview::whereHas('rps', function ($q) use ($userId) {
                $q->whereHas('dosen', function ($dq) use ($userId) {
                    $dq->where('user_id', $userId);
                });
            })
            ->where('status', 'reviewed')
            ->where('created_at', '>=', now()->subDays(7))
            ->with('rps')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        foreach ($rpsReviewed as $review) {
            $rps = $review->rps;
            $matkulNama = $rps && $rps->mata_kuliah ? $rps->mata_kuliah->nama : 'RPS';
            $items->push([
                'title' => "📝 RPS Telah Ditinjau GPM — {$matkulNama}",
                'body' => "RPS Anda telah ditinjau oleh GPM. Silakan periksa catatan review dan lakukan revisi jika diperlukan.",
                'date' => $review->created_at->diffForHumans(),
                'module' => 'bank_soal',
                'pinned' => true,
                'url' => route('banksoal.rps.dosen.show', $rps->id),
                '_ts' => $review->created_at->timestamp,
                'badge' => 'bank_soal',
            ]);
        }

        // 2. RPS yang dikembalikan untuk revisi
        $rpsRevision = Rps::whereHas('dosen', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('status', 'needs_revision')
            ->where('updated_at', '>=', now()->subDays(7))
            ->orderByDesc('updated_at')
            ->limit(3)
            ->get();

        foreach ($rpsRevision as $rps) {
            $matkulNama = $rps && $rps->mata_kuliah ? $rps->mata_kuliah->nama : 'RPS';
            $items->push([
                'title' => "🔄 RPS Perlu Direvisi — {$matkulNama}",
                'body' => "RPS Anda memerlukan revisi. Silakan periksa catatan dari GPM dan lakukan perbaikan.",
                'date' => $rps->updated_at->diffForHumans(),
                'module' => 'bank_soal',
                'pinned' => false,
                'url' => route('banksoal.rps.dosen.show', $rps->id),
                '_ts' => $rps->updated_at->timestamp,
                'badge' => 'bank_soal',
            ]);
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GPM NOTIFICATIONS
    // ──────────────────────────────────────────────────────────────────────────

    private function getGpmNotifications(Collection &$items, int $userId): void
    {
        // 1. RPS yang menunggu review
        $rpsPendingReview = Rps::where('status', 'submitted')
            ->where('submitted_at', '>=', now()->subDays(14))
            ->orderByDesc('submitted_at')
            ->limit(5)
            ->get();

        foreach ($rpsPendingReview as $rps) {
            $firstDosen = $rps->dosen->first();
            $dosenName = $firstDosen ? $firstDosen->name : 'Dosen';
            $matkulNama = $rps && $rps->mata_kuliah ? $rps->mata_kuliah->nama : 'RPS';
            $items->push([
                'title' => "📋 Permintaan Review RPS — {$matkulNama}",
                'body' => "RPS dari {$dosenName} menunggu review. Silakan tinjau dan berikan feedback.",
                'date' => $rps->submitted_at ? $rps->submitted_at->diffForHumans() : 'Baru saja',
                'module' => 'bank_soal',
                'pinned' => true,
                'url' => route('banksoal.rps.gpm.review', $rps->id),
                '_ts' => $rps->submitted_at ? $rps->submitted_at->timestamp : now()->timestamp,
                'badge' => 'bank_soal',
            ]);
        }

        // 2. RPS yang sudah lama tidak direview (reminder)
        $rpsOverdue = Rps::where('status', 'submitted')
            ->where('submitted_at', '<', now()->subDays(7))
            ->orderBy('submitted_at')
            ->limit(3)
            ->get();

        foreach ($rpsOverdue as $rps) {
            $daysPending = now()->diffInDays($rps->submitted_at);
            $matkulNama = $rps && $rps->mata_kuliah ? $rps->mata_kuliah->nama : 'RPS';
            $items->push([
                'title' => "⏰ RPS Belum Direview — {$matkulNama}",
                'body' => "RPS ini telah menunggu review selama {$daysPending} hari. Prioritaskan untuk segera ditinjau.",
                'date' => $rps->submitted_at ? $rps->submitted_at->diffForHumans() : '',
                'module' => 'bank_soal',
                'pinned' => true,
                'url' => route('banksoal.rps.gpm.review', $rps->id),
                '_ts' => $rps->submitted_at ? $rps->submitted_at->timestamp : now()->timestamp,
                'badge' => 'bank_soal',
            ]);
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // MAHASISWA NOTIFICATION HELPERS
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Tipe 1: Periode pendaftaran dibuka — mahasiswa belum mendaftar.
     */
    private function notifPeriodeDibuka(Collection &$items, PeriodeUjian $periode): void
    {
        $tutup = Carbon::parse($periode->tanggal_selesai)->translatedFormat('d F Y');
        $sisaHari = (int) now()->diffInDays(Carbon::parse($periode->tanggal_selesai)->endOfDay(), false);
        $urgensi = $sisaHari <= 3 ? " Hanya tersisa {$sisaHari} hari lagi!" : '';

        $items->push([
            'title' => "📢 Pendaftaran Ujian Komprehensif Dibuka — {$periode->nama_periode}",
            'body' => "Periode pendaftaran telah dibuka hingga {$tutup}.{$urgensi} Segera lengkapi formulir pendaftaran Anda.",
            'date' => Carbon::parse($periode->tanggal_mulai)->diffForHumans(),
            'module' => 'bank_soal',
            'pinned' => true,
            'url' => route('komprehensif.mahasiswa.dashboard'),
            '_ts' => Carbon::parse($periode->tanggal_mulai)->timestamp + 8000,
            'badge' => 'bank_soal',
        ]);
    }

    /**
     * Tipe 2: Pendaftaran disetujui — menunggu alokasi jadwal.
     */
    private function notifDisetujui(Collection &$items, PendaftarUjian $pendaftar, PeriodeUjian $periode): void
    {
        $items->push([
            'title' => "✅ Pendaftaran Disetujui — {$periode->nama_periode}",
            'body' => "Pendaftaran Anda telah disetujui. Jadwal ujian akan segera dialokasikan oleh admin — pantau halaman ini secara berkala.",
            'date' => ($pendaftar->updated_at ?? now())->diffForHumans(),
            'module' => 'bank_soal',
            'pinned' => true,
            'url' => route('komprehensif.mahasiswa.dashboard'),
            '_ts' => ($pendaftar->updated_at ?? now())->timestamp + 9000,
            'badge' => 'bank_soal',
        ]);
    }

    /**
     * Tipe 3: Pendaftaran ditolak.
     */
    private function notifDitolak(Collection &$items, PendaftarUjian $pendaftar, PeriodeUjian $periode): void
    {
        $items->push([
            'title' => "❌ Pendaftaran Ditolak — {$periode->nama_periode}",
            'body' => "Pendaftaran Anda tidak memenuhi persyaratan. Anda dapat mendaftar kembali pada periode ujian komprehensif berikutnya.",
            'date' => ($pendaftar->updated_at ?? now())->diffForHumans(),
            'module' => 'bank_soal',
            'pinned' => false,
            'url' => route('komprehensif.mahasiswa.dashboard'),
            '_ts' => ($pendaftar->updated_at ?? now())->timestamp + 5000,
            'badge' => 'bank_soal',
        ]);
    }

    /**
     * Tipe 4: Jadwal ujian dikonfirmasi.
     */
    private function notifJadwalDikonfirmasi(Collection &$items, PendaftarUjian $pendaftar, PeriodeUjian $periode): void
    {
        $jadwal = $pendaftar->jadwal;
        $tglUjian = Carbon::parse($jadwal->tanggal_ujian)->translatedFormat('l, d F Y');
        $mulai = Carbon::parse($jadwal->waktu_mulai)->format('H:i');
        $selesai = Carbon::parse($jadwal->waktu_selesai)->format('H:i');
        $sesi = $jadwal->nama_sesi;

        $items->push([
            'title' => "📅 Jadwal Ujian Dikonfirmasi — {$periode->nama_periode}",
            'body' => "Anda terjadwal pada Sesi {$sesi}, {$tglUjian}, pukul {$mulai}–{$selesai} WIB. Hadir 15 menit sebelum ujian dimulai.",
            'date' => Carbon::parse($jadwal->tanggal_ujian)->diffForHumans(),
            'module' => 'bank_soal',
            'pinned' => true,
            'url' => route('komprehensif.mahasiswa.dashboard'),
            '_ts' => Carbon::parse($jadwal->tanggal_ujian)->timestamp + 9500,
            'badge' => 'bank_soal',
        ]);
    }
}
