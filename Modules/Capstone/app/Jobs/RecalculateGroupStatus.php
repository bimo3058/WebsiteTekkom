<?php

namespace Modules\Capstone\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Services\GroupService;

class RecalculateGroupStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $groupId;

    public function __construct(int $groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * Single engine: GroupService::evaluateGroupReadiness owns every
     * FORMING ↔ READY_FOR_BIDDING rule including the FORMING_SOLO and
     * finalized-state guards. Kept as a separate job name for queued
     * payloads already in flight.
     */
    public function handle(): void
    {
        try {
            $group = Group::find($this->groupId);

            if (! $group) {
                return;
            }

            app(GroupService::class)->evaluateGroupReadiness($group);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
