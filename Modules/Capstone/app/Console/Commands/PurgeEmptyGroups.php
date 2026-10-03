<?php

namespace Modules\Capstone\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Services\GroupService;

/**
 * Purge groups that have no remaining active members.
 *
 * Finds capstone_groups with zero non-soft-deleted capstone_group_members
 * rows and hard-deletes them (with a GROUP_AUTO_DELETED audit entry each).
 * Dry-run by default; pass --apply to write.
 */
class PurgeEmptyGroups extends Command
{
    protected $signature = 'capstone:purge-empty-groups {--apply : Actually delete the empty groups (default is dry-run)}';

    protected $description = 'Delete groups with no remaining active members (dry-run by default)';

    public function handle(GroupService $groups): int
    {
        $emptyIds = Group::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('capstone_group_members')
                ->whereColumn('capstone_group_members.group_id', 'capstone_groups.id')
                ->whereNull('capstone_group_members.deleted_at');
        })->pluck('id');

        if ($emptyIds->isEmpty()) {
            $this->info('No empty groups found.');

            return self::SUCCESS;
        }

        $rows = Group::whereIn('id', $emptyIds)
            ->get(['id', 'status', 'period_id'])
            ->map(fn (Group $group) => [$group->id, $group->status, $group->period_id])
            ->all();
        $this->table(['group_id', 'status', 'period_id'], $rows);

        if (! $this->option('apply')) {
            $this->warn(count($rows).' empty group(s) found. Re-run with --apply to delete.');

            return self::SUCCESS;
        }

        $deleted = 0;
        foreach ($emptyIds as $groupId) {
            DB::transaction(function () use ($groups, $groupId, &$deleted) {
                if ($groups->destroyIfEmpty((int) $groupId, 'purge')) {
                    $deleted++;
                }
            });
        }

        $this->info("Deleted {$deleted} empty group(s).");

        return self::SUCCESS;
    }
}
