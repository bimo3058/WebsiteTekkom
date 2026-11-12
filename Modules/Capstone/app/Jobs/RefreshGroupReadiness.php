<?php

namespace Modules\Capstone\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Services\GroupService;

class RefreshGroupReadiness implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $groupId;

    public function __construct(int $groupId)
    {
        $this->groupId = $groupId;
    }

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
