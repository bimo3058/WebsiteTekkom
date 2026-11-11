<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;

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
    public function handle()
    {
        $threshold = now()->subMinutes(User::ONLINE_TIMEOUT_MINUTES);

        $affected = DB::table('users')
            ->where('is_online', true)
            ->where(function ($query) use ($threshold) {
                $query->whereNull('last_seen_at')->orWhere('last_seen_at', '<', $threshold);
            })
            ->update(['is_online' => false]);

        $this->info("Marked {$affected} users as offline.");
    }
}
