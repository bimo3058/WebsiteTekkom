<?php

namespace Modules\EOffice\Services;

use App\Models\User;
use Modules\EOffice\Models\Notifikasi;

class NotifikasiService
{
    /**
     * Kirim notifikasi ke satu user.
     */
    public function kirim(int $userId, string $judul, string $pesan, ?int $peminjamanId = null): Notifikasi
    {
        return Notifikasi::create([
            'user_id'       => $userId,
            'judul'         => $judul,
            'pesan'         => $pesan,
            'peminjaman_id' => $peminjamanId,
            'is_read'       => false,
        ]);
    }

    /**
     * Kirim notifikasi ke banyak user sekaligus (bulk insert).
     */
    public function kirimBulk(iterable $userIds, string $judul, string $pesan): void
    {
        $now  = now();
        $rows = [];
        foreach ($userIds as $uid) {
            $rows[] = [
                'user_id'    => $uid,
                'judul'      => $judul,
                'pesan'      => $pesan,
                'is_read'    => false,
                'created_at' => $now,
            ];
        }
        if (!empty($rows)) {
            Notifikasi::insert($rows);
        }
    }

    /**
     * Kirim ke SEMUA user yang aktif di sistem (tidak di-soft-delete).
     * Dipakai saat admin membuka periode pendaftaran — notif ke semua,
     * termasuk dosen, mahasiswa, asprak, koor, dsb.
     */
    public function kirimKeSemuaUser(string $judul, string $pesan): int
    {
        $userIds = User::whereNull('deleted_at')->pluck('id');
        $this->kirimBulk($userIds, $judul, $pesan);
        return $userIds->count();
    }
}
