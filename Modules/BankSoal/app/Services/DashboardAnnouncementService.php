<?php

namespace Modules\BankSoal\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\BankSoal\Enums\PendaftaranStatus;
use Modules\BankSoal\Models\Komprehensif\PendaftarUjian;
use Modules\BankSoal\Models\Komprehensif\PeriodeUjian;

class DashboardAnnouncementService
{
    /**
     * Generate personalised announcements for the main dashboard.
     *
     * 4 notification types (ordered by priority):
     *   1. Periode pendaftaran dibuka   — mahasiswa belum mendaftar + periode terbuka
     *   2. Pendaftaran disetujui        — approved, belum ada jadwal
     *   3. Pendaftaran ditolak          — rejected
     *   4. Jadwal dikonfirmasi          — approved + jadwal tersedia
     *
     * @param  int  $userId
     * @return array<int, array{title: string, body: string, date: string, module: string, pinned: bool, link: string|null, _ts: int}>
     */
    public function getForDashboard(int $userId): array
    {
        $items = collect();

        // Pre-load active periods
        $activePeriodes = PeriodeUjian::currentlyActive()->get();
        $periodeIds     = $activePeriodes->pluck('id');

        // Pre-load this user's registrations for active periods
        $userRegistrations = PendaftarUjian::where('mahasiswa_id', $userId)
            ->whereIn('periode_ujian_id', $periodeIds)
            ->with(['jadwal', 'periode'])
            ->get()
            ->keyBy('periode_ujian_id');

        foreach ($activePeriodes as $periode) {
            $pendaftar = $userRegistrations->get($periode->id);

            if (! $pendaftar) {
                // Tipe 1: Periode dibuka — mahasiswa belum mendaftar
                if ($periode->pendaftaran_terbuka) {
                    $this->notifPeriodeDibuka($items, $periode);
                }
                continue;
            }

            $status = $pendaftar->status_pendaftaran;

            if ($status === PendaftaranStatus::Approved) {
                if ($pendaftar->jadwal) {
                    // Tipe 4: Jadwal dikonfirmasi
                    $this->notifJadwalDikonfirmasi($items, $pendaftar, $periode);
                } else {
                    // Tipe 2: Disetujui — menunggu jadwal
                    $this->notifDisetujui($items, $pendaftar, $periode);
                }
            } elseif ($status === PendaftaranStatus::Rejected) {
                // Tipe 3: Ditolak
                $this->notifDitolak($items, $pendaftar, $periode);
            }
            // Pending: tidak ditampilkan di dashboard utama
        }

        return $items
            ->sortByDesc(fn ($item) => [$item['pinned'] ? 1 : 0, $item['_ts']])
            ->take(10)
            ->values()
            ->toArray();
    }

    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Tipe 1: Periode pendaftaran dibuka — mahasiswa belum mendaftar.
     */
    private function notifPeriodeDibuka(Collection &$items, PeriodeUjian $periode): void
    {
        $tutup    = Carbon::parse($periode->tanggal_selesai)->translatedFormat('d F Y');
        $sisaHari = (int) now()->diffInDays(Carbon::parse($periode->tanggal_selesai)->endOfDay(), false);
        $urgensi  = $sisaHari <= 3 ? " Hanya tersisa {$sisaHari} hari lagi!" : '';

        $items->push([
            'title'  => "📢 Pendaftaran Ujian Komprehensif Dibuka — {$periode->nama_periode}",
            'body'   => "Periode pendaftaran telah dibuka hingga {$tutup}.{$urgensi} Segera lengkapi formulir pendaftaran Anda.",
            'date'   => Carbon::parse($periode->tanggal_mulai)->translatedFormat('d M Y'),
            'module' => 'bank_soal',
            'pinned' => true,
            'url'    => route('komprehensif.mahasiswa.dashboard'),
            '_ts'    => Carbon::parse($periode->tanggal_mulai)->timestamp + 8000,
        ]);
    }

    /**
     * Tipe 2: Pendaftaran disetujui — menunggu alokasi jadwal.
     */
    private function notifDisetujui(Collection &$items, PendaftarUjian $pendaftar, PeriodeUjian $periode): void
    {
        $items->push([
            'title'  => "✅ Pendaftaran Disetujui — {$periode->nama_periode}",
            'body'   => "Pendaftaran Anda telah disetujui. Jadwal ujian akan segera dialokasikan oleh admin — pantau halaman ini secara berkala.",
            'date'   => ($pendaftar->updated_at ?? now())->translatedFormat('d M Y'),
            'module' => 'bank_soal',
            'pinned' => true,
            'url'    => route('komprehensif.mahasiswa.dashboard'),
            '_ts'    => ($pendaftar->updated_at ?? now())->timestamp + 9000,
        ]);
    }

    /**
     * Tipe 3: Pendaftaran ditolak.
     */
    private function notifDitolak(Collection &$items, PendaftarUjian $pendaftar, PeriodeUjian $periode): void
    {
        $items->push([
            'title'  => "❌ Pendaftaran Ditolak — {$periode->nama_periode}",
            'body'   => "Pendaftaran Anda tidak memenuhi persyaratan. Anda dapat mendaftar kembali pada periode ujian komprehensif berikutnya.",
            'date'   => ($pendaftar->updated_at ?? now())->translatedFormat('d M Y'),
            'module' => 'bank_soal',
            'pinned' => false,
            'url'    => route('komprehensif.mahasiswa.dashboard'),
            '_ts'    => ($pendaftar->updated_at ?? now())->timestamp + 5000,
        ]);
    }

    /**
     * Tipe 4: Jadwal ujian dikonfirmasi.
     */
    private function notifJadwalDikonfirmasi(Collection &$items, PendaftarUjian $pendaftar, PeriodeUjian $periode): void
    {
        $jadwal   = $pendaftar->jadwal;
        $tglUjian = Carbon::parse($jadwal->tanggal_ujian)->translatedFormat('l, d F Y');
        $mulai    = Carbon::parse($jadwal->waktu_mulai)->format('H:i');
        $selesai  = Carbon::parse($jadwal->waktu_selesai)->format('H:i');
        $sesi     = $jadwal->nama_sesi;

        $items->push([
            'title'  => "📅 Jadwal Ujian Dikonfirmasi — {$periode->nama_periode}",
            'body'   => "Anda terjadwal pada Sesi {$sesi}, {$tglUjian}, pukul {$mulai}–{$selesai} WIB. Hadir 15 menit sebelum ujian dimulai.",
            'date'   => Carbon::parse($jadwal->tanggal_ujian)->translatedFormat('d M Y'),
            'module' => 'bank_soal',
            'pinned' => true,
            'url'    => route('komprehensif.mahasiswa.dashboard'),
            '_ts'    => Carbon::parse($jadwal->tanggal_ujian)->timestamp + 9500,
        ]);
    }
}
