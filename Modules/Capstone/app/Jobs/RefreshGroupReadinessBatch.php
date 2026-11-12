<?php

namespace Modules\Capstone\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Services\GroupService;

class RefreshGroupReadinessBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var array<int> */
    public array $groupIds;

    public function __construct(array $groupIds)
    {
        $this->groupIds = $groupIds;
    }

    public function handle(): void
    {
        try {
            $service = app(GroupService::class);

            Group::whereIn('id', $this->groupIds)
                ->chunkById(100, function ($groups) use ($service) {
                    foreach ($groups as $group) {
                        try {
                            $service->evaluateGroupReadiness($group);
                        } catch (\Throwable $e) {
                            report($e);
                        }
                    }
                });
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
