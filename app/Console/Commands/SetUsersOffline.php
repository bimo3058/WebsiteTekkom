<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
    protected $description = 'Mark users as offline if they have not been seen for more than 5 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $threshold = Carbon::now()->subMinutes(5);

        $affected = DB::table('users')
            ->where('is_online', true)
            ->where('last_seen_at', '<', $threshold)
            ->update(['is_online' => false]);

        $this->info("Marked {$affected} users as offline.");
    }
}
