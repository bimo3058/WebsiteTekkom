<?php

namespace Modules\EOffice\Services;

use App\Models\User;
use Modules\EOffice\Models\Notifikasi;

class NotifikasiService
{
    /**
     * Kirim notifikasi ke satu user.
     */
    public function kirim(int $userId, string $judul, string $pesan, ?int $peminjamanId = null, ?string $practicumRole = null, ?array $target = null): Notifikasi
    {
        $data = [
            'user_id' => $userId,
            'judul' => $judul,
            'pesan' => $pesan,
            'peminjaman_id' => $peminjamanId,
            'is_read' => false,
        ];
        if (\Illuminate\Support\Facades\Schema::hasColumn('eo_notifikasi', 'practicum_role')) {
            $data['practicum_role'] = $practicumRole;
            if ($practicumRole === null && request()->routeIs('eoffice.manprak.*')) {
                $data['practicum_role'] = PracticumNotificationAudience::roleForTitle($judul);
            }
        }
        if ($target && \Illuminate\Support\Facades\Schema::hasColumn('eo_notifikasi','practicum_target')) $data['practicum_target']=$target;
        return Notifikasi::create($data);
    }

    /**
     * Kirim notifikasi ke banyak user sekaligus (bulk insert).
     */
    public function kirimBulk(iterable $userIds, string $judul, string $pesan, ?string $practicumRole = null, ?array $target = null): void
    {
        $now = now();
        $rows = [];
        foreach ($userIds as $uid) {
            $rows[] = [
                'user_id' => $uid,
                'judul' => $judul,
                'pesan' => $pesan,
                'is_read' => false,
                'created_at' => $now,
            ];
        }
        if (!empty($rows)) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('eo_notifikasi', 'practicum_role')) {
                $role = $practicumRole ?? (request()->routeIs('eoffice.manprak.*') ? PracticumNotificationAudience::roleForTitle($judul) : null);
                foreach ($rows as &$row) $row['practicum_role'] = $role;
                unset($row);
            }
            if ($target && \Illuminate\Support\Facades\Schema::hasColumn('eo_notifikasi','practicum_target')) {
                foreach ($rows as &$row) $row['practicum_target']=json_encode($target);
                unset($row);
            }
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
        $count = 0;
        User::whereNull('deleted_at')->select('id')->chunk(1000, function ($users) use ($judul, $pesan, &$count) {
            $userIds = $users->pluck('id');
            $this->kirimBulk($userIds, $judul, $pesan);
            $count += $userIds->count();
        });
        return $count;
    }
}
