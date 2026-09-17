<?php

namespace App\Services;

use App\Models\SuperAdminNotification;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    public function notify(string $type, string $title, string $message, string $severity = 'info', array $context = []): void
    {
        SuperAdminNotification::updateOrCreate(
            [
                'type' => $type,
                'message' => $message,
                'status' => 'unread',
            ],
            [
                'title' => $title,
                'severity' => $severity,
                'context' => $context,
                'occurrence_count' => DB::raw('occurrence_count + 1'),
                'last_occurred_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function markAsRead(int $id): bool
    {
        $notification = SuperAdminNotification::find($id);
        if (!$notification) return false;

        return (bool) $notification->update(['read_at' => now(), 'status' => 'read']);
    }

    public function markAllAsRead(): int
    {
        return SuperAdminNotification::whereNull('read_at')
            ->update(['read_at' => now(), 'status' => 'read']);
    }
}
