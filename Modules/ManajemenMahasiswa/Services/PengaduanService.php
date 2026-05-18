<?php

namespace Modules\ManajemenMahasiswa\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Modules\ManajemenMahasiswa\Models\Pengaduan;
use Modules\ManajemenMahasiswa\Models\PengaduanDelegasi;
use Modules\ManajemenMahasiswa\Models\PengaduanLog;

class PengaduanService
{
    // ── Query helpers ──────────────────────────────────────────────────────

    public function listForUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Pengaduan::query()
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function listAll(int $perPage = 20): LengthAwarePaginator
    {
        return Pengaduan::query()
            ->with(['pelapor'])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    // ── Lifecycle: Mahasiswa ───────────────────────────────────────────────

    public function create(int $userId, string $kategori, bool $isAnonim, array $template): Pengaduan
    {
        $anonToken = $isAnonim ? Str::random(32) : null;

        $pengaduan = Pengaduan::create([
            'user_id'       => $userId,
            'kategori'      => $kategori,
            'is_anonim'     => $isAnonim,
            'anon_token'    => $anonToken,
            'data_template' => $template,
            'status'        => Pengaduan::STATUS_BARU,
        ]);

        $this->logAction($pengaduan, $userId, PengaduanLog::ACTION_DIBUAT);

        return $pengaduan;
    }

    public function closeByMahasiswa(Pengaduan $pengaduan, int $userId): void
    {
        $pengaduan->forceFill([
            'status'    => Pengaduan::STATUS_SELESAI,
            'closed_at' => now(),
            'closed_by' => $userId,
        ])->save();

        $this->logAction($pengaduan, $userId, PengaduanLog::ACTION_DITUTUP_MAHASISWA);
    }

    public function reopenByMahasiswa(Pengaduan $pengaduan, int $userId, string $alasan): void
    {
        $pengaduan->forceFill([
            'status'        => Pengaduan::STATUS_BARU,
            'reopen_count'  => $pengaduan->reopen_count + 1,
            'reopen_reason' => $alasan,
            'auto_close_at' => null,
        ])->save();

        $this->logAction($pengaduan, $userId, PengaduanLog::ACTION_DIAJUKAN_ULANG, $alasan);
    }

    // ── Lifecycle: Admin ───────────────────────────────────────────────────

    public function markRead(Pengaduan $pengaduan, int $readerUserId): void
    {
        if ($pengaduan->read_at) {
            return;
        }

        $pengaduan->forceFill([
            'status'  => Pengaduan::STATUS_DIBACA,
            'read_at' => now(),
            'read_by' => $readerUserId,
        ])->save();

        $this->logAction($pengaduan, $readerUserId, PengaduanLog::ACTION_DIBACA);
    }

    /**
     * Admin tangani sendiri (tanpa delegasi) — langsung kirim jawaban ke mahasiswa.
     */
    public function reply(Pengaduan $pengaduan, int $answererUserId, string $jawaban): Pengaduan
    {
        $pengaduan->forceFill([
            'jawaban'       => $jawaban,
            'answered_at'   => now(),
            'answered_by'   => $answererUserId,
            'status'        => Pengaduan::STATUS_DIJAWAB,
            'auto_close_at' => now()->addDays(7),
        ])->save();

        $this->logAction($pengaduan, $answererUserId, PengaduanLog::ACTION_DIJAWAB);

        return $pengaduan->fresh();
    }

    /**
     * Admin mendelegasikan ke dosen.
     */
    public function delegate(Pengaduan $pengaduan, int $adminId, int $dosenId, string $notesAdmin): PengaduanDelegasi
    {
        // Tutup delegasi aktif sebelumnya jika ada (re-delegate)
        $pengaduan->delegasiAktif?->forceFill(['status' => PengaduanDelegasi::STATUS_DITOLAK])->save();

        $delegasi = PengaduanDelegasi::create([
            'pengaduan_id'  => $pengaduan->id,
            'delegated_by'  => $adminId,
            'delegated_to'  => $dosenId,
            'notes_admin'   => $notesAdmin,
            'status'        => PengaduanDelegasi::STATUS_AKTIF,
            'delegated_at'  => now(),
        ]);

        $pengaduan->forceFill(['status' => Pengaduan::STATUS_DIDELEGASIKAN])->save();

        $this->logAction($pengaduan, $adminId, PengaduanLog::ACTION_DIDELEGASIKAN, $notesAdmin);

        return $delegasi;
    }

    /**
     * Admin meneruskan tanggapan dosen ke mahasiswa (jawaban final).
     */
    public function forwardAnswer(Pengaduan $pengaduan, int $adminId, string $jawaban): Pengaduan
    {
        // Tandai delegasi aktif sebagai selesai
        $pengaduan->delegasiAktif?->forceFill([
            'status'       => PengaduanDelegasi::STATUS_DITANGGAPI,
            'responded_at' => now(),
        ])->save();

        $pengaduan->forceFill([
            'jawaban'       => $jawaban,
            'answered_at'   => now(),
            'answered_by'   => $adminId,
            'status'        => Pengaduan::STATUS_DIJAWAB,
            'auto_close_at' => now()->addDays(7),
        ])->save();

        $this->logAction($pengaduan, $adminId, PengaduanLog::ACTION_DIJAWAB);

        return $pengaduan->fresh();
    }

    /**
     * Admin menutup tiket secara paksa.
     */
    public function closeByAdmin(Pengaduan $pengaduan, int $adminId): void
    {
        $pengaduan->forceFill([
            'status'    => Pengaduan::STATUS_SELESAI,
            'closed_at' => now(),
            'closed_by' => $adminId,
        ])->save();

        $this->logAction($pengaduan, $adminId, PengaduanLog::ACTION_DITUTUP_ADMIN);
    }

    // ── Lifecycle: Dosen ───────────────────────────────────────────────────

    /**
     * Dosen menanggapi delegasi dan mengirim balik ke admin.
     */
    public function dosenRespond(PengaduanDelegasi $delegasi, string $tanggapan, string $notesBalik): void
    {
        $delegasi->forceFill([
            'tanggapan'    => $tanggapan,
            'notes_balik'  => $notesBalik,
            'status'       => PengaduanDelegasi::STATUS_DITANGGAPI,
            'responded_at' => now(),
        ])->save();

        $delegasi->pengaduan->forceFill([
            'status' => Pengaduan::STATUS_DITANGGAPI_DOSEN,
        ])->save();

        $this->logAction(
            $delegasi->pengaduan,
            $delegasi->delegated_to,
            PengaduanLog::ACTION_DITANGGAPI_DOSEN,
            $notesBalik
        );
    }

    /**
     * Dosen menolak delegasi — tiket dikembalikan ke admin (status: dibaca).
     */
    public function dosenReject(PengaduanDelegasi $delegasi, string $alasanTolak): void
    {
        $delegasi->forceFill([
            'alasan_tolak' => $alasanTolak,
            'status'       => PengaduanDelegasi::STATUS_DITOLAK,
            'responded_at' => now(),
        ])->save();

        $delegasi->pengaduan->forceFill([
            'status' => Pengaduan::STATUS_DIBACA,
        ])->save();

        $this->logAction(
            $delegasi->pengaduan,
            $delegasi->delegated_to,
            PengaduanLog::ACTION_DITOLAK_DOSEN,
            $alasanTolak
        );
    }

    // ── Scheduler ─────────────────────────────────────────────────────────

    /**
     * Dipanggil oleh command pengaduan:auto-close setiap hari.
     * Menutup tiket yang sudah dijawab lebih dari 7 hari tanpa respons mahasiswa.
     */
    public function autoCloseExpired(): int
    {
        $tikets = Pengaduan::query()
            ->where('status', Pengaduan::STATUS_DIJAWAB)
            ->where('auto_close_at', '<=', now())
            ->get();

        foreach ($tikets as $tiket) {
            $tiket->forceFill([
                'status'    => Pengaduan::STATUS_SELESAI,
                'closed_at' => now(),
            ])->save();

            $this->logAction($tiket, null, PengaduanLog::ACTION_DITUTUP_OTOMATIS);
        }

        return $tikets->count();
    }

    // ── Internal helper ───────────────────────────────────────────────────

    public function logAction(Pengaduan $pengaduan, ?int $actorUserId, string $action, ?string $notes = null): void
    {
        PengaduanLog::create([
            'pengaduan_id'  => $pengaduan->id,
            'actor_user_id' => $actorUserId,
            'action'        => $action,
            'notes'         => $notes,
            'created_at'    => now(),
        ]);
    }
}
