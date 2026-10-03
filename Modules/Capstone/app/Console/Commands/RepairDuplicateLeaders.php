<?php

namespace Modules\Capstone\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Capstone\Models\GroupMember;

/**
 * Repair groups that ended up with more than one leader (e.g. solo-bid
 * merges performed before moved members were demoted). Keeps the
 * earliest-joined leader per group — the founder/owner — and demotes
 * the rest. Dry-run by default; pass --fix to write.
 */
class RepairDuplicateLeaders extends Command
{
    protected $signature = 'capstone:repair-duplicate-leaders {--fix : Apply the demotions (default is dry-run)}';

    protected $description = 'Demote duplicate group leaders, keeping the earliest-joined leader per group';

    public function handle(): int
    {
        $groupIds = GroupMember::where('is_leader', true)
            ->select('group_id')
            ->groupBy('group_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('group_id');

        if ($groupIds->isEmpty()) {
            $this->info('No groups with duplicate leaders found.');

            return self::SUCCESS;
        }

        $rows = [];
        foreach ($groupIds as $groupId) {
            $leaders = GroupMember::where('group_id', $groupId)
                ->where('is_leader', true)
                ->with('student')
                ->orderBy('created_at')
                ->orderBy('id')
                ->get();
            $keep = $leaders->first();
            foreach ($leaders->skip(1) as $demote) {
                $rows[] = [$groupId, $demote->student_id, $demote->student?->name ?? '-', $keep->student_id];
            }
        }

        $this->table(['group_id', 'demote_student_id', 'demote_name', 'keep_student_id'], $rows);

        if (! $this->option('fix')) {
            $this->warn('Dry-run only. Re-run with --fix to apply.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($groupIds) {
            foreach ($groupIds as $groupId) {
                $keeperId = GroupMember::where('group_id', $groupId)
                    ->where('is_leader', true)
                    ->orderBy('created_at')
                    ->orderBy('id')
                    ->value('id');
                GroupMember::where('group_id', $groupId)
                    ->where('is_leader', true)
                    ->where('id', '!=', $keeperId)
                    ->update(['is_leader' => false]);
            }
        });

        $this->info('Demoted duplicate leaders.');

        return self::SUCCESS;
    }
}
