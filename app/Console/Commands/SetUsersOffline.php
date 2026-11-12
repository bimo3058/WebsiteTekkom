<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetUsersOffline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:set-users-offline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark users as offline when their heartbeat has been stale for more than 5 minutes';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $threshold = now()->subMinutes(User::ONLINE_TIMEOUT_MINUTES);

        $affected = 0;

        User::query()
            ->where('is_online', true)
            ->where(function ($query) use ($threshold) {
                $query->whereNull('last_seen_at')->orWhere('last_seen_at', '<', $threshold);
            })
            ->orderBy('id')
            ->chunkById(500, function ($users) use (&$affected) {
                $affected += DB::table('users')
                    ->whereIn('id', $users->pluck('id')->all())
                    ->where('is_online', true)
                    ->update(['is_online' => false]);
            });

        $this->info("Marked {$affected} users as offline.");

        return self::SUCCESS;
    }
}
