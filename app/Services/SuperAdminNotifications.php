<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\ImportStatus;
use App\Models\User;
use Illuminate\Support\Collection;

class SuperAdminNotifications
{
    public const OPTIONS = [
        'authentication' => ['label' => 'Login dan logout pengguna', 'description' => 'Aktivitas masuk dan keluar sistem yang tercatat di audit log.'],
        'user_management' => ['label' => 'Pengguna dan hak akses', 'description' => 'Pembuatan akun, perubahan role atau permission, suspend, dan force logout.'],
        'module_activity' => ['label' => 'Aktivitas aplikasi', 'description' => 'Aktivitas Bank Soal, Capstone, E-Office, dan Manajemen Mahasiswa yang tercatat di audit log pusat.'],
        'imports' => ['label' => 'Hasil impor pengguna', 'description' => 'Impor yang Anda jalankan telah selesai atau gagal.'],
    ];

    public function preferences(User $user): array
    {
        $defaults = array_fill_keys(array_keys(self::OPTIONS), true);
        $stored = $user->notification_preferences ?? [];

        return array_replace($defaults, array_intersect_key($stored, $defaults));
    }

    public function recent(User $user): Collection
    {
        return \App\Models\SuperAdminNotification::query()
            ->whereNull('read_at')
            ->orderByDesc('last_occurred_at')
            ->limit(10)
            ->get()
            ->map(fn ($notif) => [
                'id' => 'notif-'.$notif->id,
                'title' => $notif->title,
                'description' => $notif->message,
                'time' => $notif->last_occurred_at->diffForHumans(),
                'timestamp' => $notif->last_occurred_at->timestamp,
                'url' => route('superadmin.notifications.index'), // Default URL
            ]);
    }

    public function countRecent(User $user): int
    {
        return \App\Models\SuperAdminNotification::whereNull('read_at')->count();
    }
}
