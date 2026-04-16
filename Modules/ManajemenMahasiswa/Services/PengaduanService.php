<?php

namespace Modules\ManajemenMahasiswa\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\ManajemenMahasiswa\Models\Pengaduan;

class PengaduanService
{
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

    public function create(int $userId, string $kategori, bool $isAnonim, array $template): Pengaduan
    {
        return Pengaduan::create([
            'user_id'       => $userId,
            'kategori'      => $kategori,
            'is_anonim'     => $isAnonim,
            'data_template' => $template,
            'status'        => Pengaduan::STATUS_BARU,
        ]);
    }

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
    }

    public function reply(Pengaduan $pengaduan, int $answererUserId, string $jawaban): Pengaduan
    {
        $pengaduan->forceFill([
            'jawaban'     => $jawaban,
            'answered_at' => now(),
            'answered_by' => $answererUserId,
            'status'      => Pengaduan::STATUS_DIJAWAB,
        ])->save();

        return $pengaduan->fresh();
    }
}
