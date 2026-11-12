<?php

namespace Modules\Capstone\Observers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Capstone\Jobs\RefreshGroupReadiness;
use Modules\Capstone\Models\AuditLog;
use Modules\Capstone\Models\Group;

class GroupObserver
{
    public function saved(Group $group): void
    {
        $this->clearGroupCaches($group);

        if ($this->onlyReadinessChanged($group)) {
            return;
        }

        RefreshGroupReadiness::dispatch($group->id);
    }

    public function updated(Group $group): void
    {
        if (! $group->wasChanged('status')) {
            return;
        }

        try {
            $oldStatus = $group->getOriginal('status');
            $newStatus = $group->status;

            AuditLog::create([
                'action' => 'GROUP_STATUS_CHANGED',
                'target_type' => Group::class,
                'target_id' => $group->id,
                'payload' => [
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'period_id' => $group->period_id,
                ],
            ]);

            if ($newStatus === 'DISSOLVED') {
                AuditLog::create([
                    'action' => 'GROUP_DISSOLVED',
                    'target_type' => Group::class,
                    'target_id' => $group->id,
                    'payload' => [
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                        'period_id' => $group->period_id,
                    ],
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning("Group {$group->id} status audit skipped: {$e->getMessage()}");
        }
    }

    public function deleted(Group $group): void
    {
        $this->clearGroupCaches($group);
    }

    private function onlyReadinessChanged(Group $group): bool
    {
        $changes = array_keys($group->getChanges());
        $relevant = array_diff($changes, ['readiness_status', 'updated_at']);

        return $relevant === [];
    }

    private function clearGroupCaches(Group $group): void
    {
        if ($group->wasChanged('period_id') || $group->wasChanged('status')) {
            Cache::forget('periods:active_and_finalized_ids');
        }
    }
}
