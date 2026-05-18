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
     * Generate dynamic announcements for the main dashboard.
     * Announcements are derived from existing models — no new tables required.
     *
     * Logic per active period:
     *   - If student HAS registration  → show registration status + closing date
     *   - If student has NO registration and period is open → show open invitation + closing date
     *   - If approved + jadwal exists → show confirmed schedule notice
     * Additionally: recent exam results (last 14 days)
     *
     * @param  int  $userId
     * @return array<int, array{title: string, body: string, date: string, module: string, pinned: bool}>
     */
    public function getForDashboard(int $userId): array
    {
        $items = collect();

        // Pre-load all active periods once
        $activePeriodes = PeriodeUjian::currentlyActive()->get();

        // Pre-load this user's registrations for active periods
        $periodeIds = $activePeriodes->pluck('id');
        $userRegistrations = PendaftarUjian::where('mahasiswa_id', $userId)
            ->whereIn('periode_ujian_id', $periodeIds)
            ->with(['jadwal', 'periode'])
            ->get()
            ->keyBy('periode_ujian_id');

        foreach ($activePeriodes as $periode) {
            $tutup = Carbon::parse($periode->tanggal_selesai)->translatedFormat('d F Y');
            $sisaHari = now()->diffInDays(Carbon::parse($periode->tanggal_selesai)->endOfDay(), false);
            $waktuTutup = $sisaHari <= 0 ? 'hari ini' : "dalam {$sisaHari} hari";

            $pendaftar = $userRegistrations->get($periode->id);

            if ($pendaftar) {
                // ── Mahasiswa SUDAH mendaftar pada periode ini ────────────────
                $this->addRegistrationStatusNotice($items, $pendaftar, $periode, $tutup, $waktuTutup);
            } elseif ($periode->pendaftaran_terbuka) {
                // ── Mahasiswa BELUM mendaftar, periode masih terbuka ──────────
                $this->addOpenPeriodeNotice($items, $periode, $tutup, $waktuTutup, $sisaHari);
            }
        }

        return $items
            ->sortByDesc(fn($item) => $item['_sort_key'] ?? 0)
            ->take(10)
            ->map(fn($item) => array_diff_key($item, ['_sort_key' => null]))
            ->values()
            ->toArray();
    }

    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Mahasiswa sudah mendaftar — tampilkan status + info penutupan / jadwal.
     */
    private function addRegistrationStatusNotice(
        Collection &$items,
        PendaftarUjian $pendaftar,
        PeriodeUjian $periode,
        string $tutup,
        string $waktuTutup
    ): void {
        $status = $pendaftar->status_pendaftaran; // PendaftaranStatus enum
        $namaPeriode = $periode->nama_periode;

        switch ($status) {
            case PendaftaranStatus::Pending:
                $items->push([
                    'title'     => "Pendaftaran {$namaPeriode} — Menunggu Review",
                    'body'      => "Formulir pendaftaran Anda sedang dalam proses verifikasi admin. Pantau status ini secara berkala.",
                    'date'      => now()->format('d M Y'),
                    'module'    => 'bank_soal',
                    'pinned'    => true,
                    'link'      => route('komprehensif.mahasiswa.dashboard'),
                    '_sort_key' => now()->timestamp + 9000,
                ]);
                break;

            case PendaftaranStatus::Approved:
                $jadwal = $pendaftar->jadwal;
                if ($jadwal) {
                    // Jadwal sudah dikonfirmasi
                    $tglUjian = Carbon::parse($jadwal->tanggal_ujian)->translatedFormat('l, d F Y');
                    $mulai = Carbon::parse($jadwal->waktu_mulai)->format('H:i');
                    $selesai = Carbon::parse($jadwal->waktu_selesai)->format('H:i');
                    $sesi = $jadwal->nama_sesi;

                    $items->push([
                        'title'     => "Jadwal Ujian Dikonfirmasi — {$namaPeriode}",
                        'body'      => "Anda terjadwal pada Sesi {$sesi}, {$tglUjian}, pukul {$mulai}–{$selesai} WIB. Hadir 15 menit sebelum ujian dimulai.",
                        'date'      => Carbon::parse($jadwal->tanggal_ujian)->format('d M Y'),
                        'module'    => 'bank_soal',
                        'pinned'    => true,
                        'link'      => route('komprehensif.mahasiswa.dashboard'),
                        '_sort_key' => now()->timestamp + 9500,
                    ]);
                } else {
                    // Disetujui tapi jadwal belum dialokasikan
                    $items->push([
                        'title'     => "Pendaftaran {$namaPeriode} Disetujui",
                        'body'      => "Pendaftaran Anda telah disetujui. Jadwal ujian akan segera dialokasikan oleh admin — pantau halaman ini.",
                        'date'      => now()->format('d M Y'),
                        'module'    => 'bank_soal',
                        'pinned'    => true,
                        'link'      => route('komprehensif.mahasiswa.dashboard'),
                        '_sort_key' => now()->timestamp + 9200,
                    ]);
                }
                break;

            case PendaftaranStatus::Rejected:
                $items->push([
                    'title'     => "Pendaftaran {$namaPeriode} Ditolak",
                    'body'      => "Pendaftaran Anda ditolak. Anda hanya dapat mendaftar kembali pada periode ujian berikutnya.",
                    'date'      => now()->format('d M Y'),
                    'module'    => 'bank_soal',
                    'pinned'    => false,
                    'link'      => route('komprehensif.mahasiswa.dashboard'),
                    '_sort_key' => now()->timestamp + 8000,
                ]);
                break;
        }
    }

    /**
     * Mahasiswa belum mendaftar, periode terbuka — undang mendaftar.
     */
    private function addOpenPeriodeNotice(
        Collection &$items,
        PeriodeUjian $periode,
        string $tutup,
        string $waktuTutup,
        int $sisaHari
    ): void {
        $pinned = $sisaHari <= 3; // Urgent jika ≤ 3 hari lagi

        $body = "Pendaftaran {$periode->nama_periode} telah dibuka sampai dengan tanggal {$tutup}.";

        $items->push([
            'title'     => '[PENGUMUMAN PENDAFTARAN]',
            'body'      => $body,
            'date'      => Carbon::parse($periode->tanggal_mulai)->format('d M Y'),
            'module'    => 'bank_soal',
            'pinned'    => $pinned,
            'link'      => route('komprehensif.mahasiswa.pendaftaran.form'),
            '_sort_key' => $pinned
                ? now()->timestamp + 8500
                : Carbon::parse($periode->tanggal_mulai)->timestamp,
        ]);
    }

}
