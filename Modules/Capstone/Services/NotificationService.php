<?php

namespace Modules\Capstone\Services;

use Illuminate\Support\Collection;
use Modules\Capstone\Models\CapstoneNotification;

class NotificationService
{
    /**
     * Jumlah notifikasi yang belum dibaca milik user.
     */
    public function getUnreadCount(int $userId): int
    {
        return CapstoneNotification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Semua notifikasi user, terbaru di atas.
     */
    public function getForUser(int $userId, int $limit = 20): Collection
    {
        return CapstoneNotification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Kirim notifikasi ke user.
     */
    public function send(int $userId, string $type, array $data = []): CapstoneNotification
    {
        return CapstoneNotification::create([
            'type'             => $type,
            'notifiable_type'  => 'App\Models\User',
            'notifiable_id'    => $userId,
            'data'             => $data,
        ]);
    }

    /**
     * Tandai semua notifikasi user sebagai sudah dibaca.
     */
    public function markAllRead(int $userId): void
    {
        CapstoneNotification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}