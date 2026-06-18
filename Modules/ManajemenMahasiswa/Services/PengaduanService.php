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
     * Dosen menyelesaikan tugas delegasi dan menutup pengaduan.
     */
    public function dosenRespond(PengaduanDelegasi $delegasi, ?string $notesBalik): void
    {
        $delegasi->forceFill([
            'notes_balik'  => $notesBalik,
            'status'       => PengaduanDelegasi::STATUS_DITANGGAPI,
            'responded_at' => now(),
        ])->save();

        $delegasi->pengaduan->forceFill([
            'status'    => Pengaduan::STATUS_SELESAI,
            'closed_at' => now(),
            'closed_by' => $delegasi->delegated_to,
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
