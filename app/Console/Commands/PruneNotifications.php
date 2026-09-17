<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\ImportStatus;
use App\Models\BsAuditLog;
use App\Models\MkAuditLog;
use App\Models\EoAuditLog;
use App\Models\CapstoneAuditLog;
use Illuminate\Console\Command;

class PruneNotifications extends Command
{
    protected $signature = 'app:prune-notifications';
    protected $description = 'Hapus log audit dan status impor yang lebih dari 7 hari';

    public function handle()
    {
        $date = now()->subDays(7);
        $count = 0;

        $this->info("Membersihkan data yang lebih lama dari " . $date->toDateTimeString());

        // 1. Prune Central Audit Logs
        $count += AuditLog::where('created_at', '<', $date)->delete();

        // 2. Prune Module-Specific Audit Logs (to keep DB in sync)
        $count += BsAuditLog::where('created_at', '<', $date)->delete();
        $count += MkAuditLog::where('created_at', '<', $date)->delete();
        $count += EoAuditLog::where('created_at', '<', $date)->delete();
        $count += CapstoneAuditLog::where('created_at', '<', $date)->delete();

        // 3. Prune Import Statuses
        $count += ImportStatus::where('updated_at', '<', $date)->delete();

        // 4. Prune SuperAdmin Notifications
        $count += \App\Models\SuperAdminNotification::where('created_at', '<', $date)->delete();

        $this->info("Berhasil menghapus {$count} record lama.");
    }
}
