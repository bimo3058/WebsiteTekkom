<?php

namespace Modules\ManajemenMahasiswa\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Modules\ManajemenMahasiswa\Models\Pengaduan;
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

        // Tiket konfidensial TIDAK boleh menyimpan identitas pelapor pada log.
        // actor_user_id memang nullable ("null = sistem/otomatis") justru untuk ini:
        // tanpa null, panel "Riwayat Tiket" akan menulis "Oleh: <nama pelapor>"
        // kepada staf — membocorkan identitas yang dijanjikan terlindungi.
        $this->logAction($pengaduan, $isAnonim ? null : $userId, PengaduanLog::ACTION_DIBUAT);

        return $pengaduan;
    }



    // ── Lifecycle: Admin ───────────────────────────────────────────────────

    /**
     * Staff membuka detail. Notification-style: status 'baru' berubah menjadi
     * 'dibaca' (dot biru di tabel hilang). Status lain tidak diturunkan.
     */
    public function markRead(Pengaduan $pengaduan, int $readerUserId): void
    {
        $pertamaDibuka = !$pengaduan->read_at;

        $pengaduan->forceFill([
            'status'  => $pengaduan->status === Pengaduan::STATUS_BARU
                ? Pengaduan::STATUS_DIBACA
                : $pengaduan->status,
            'read_at' => $pengaduan->read_at ?? now(),
            'read_by' => $pengaduan->read_by ?? $readerUserId,
        ])->save();

        if ($pertamaDibuka) {
            $this->logAction($pengaduan, $readerUserId, PengaduanLog::ACTION_DIBACA);
        }
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
