<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\PermissionAssigner;
use Illuminate\Console\Command;

class RepairUserPermissions extends Command
{
    protected $signature = 'permissions:repair {--user=} {--dry-run}';
    protected $description = 'Repair user permissions based on their roles';

    public function handle()
    {
        $userId = $this->option('user');
        $dryRun = $this->option('dry-run');

        if ($userId) {
            $users = User::where('id', $userId)->get();
        } else {
            $users = User::all();
        }

        $this->info("Processing " . $users->count() . " users...");

        $stats = [
            'total' => 0,
            'repaired' => 0,
            'skipped' => 0,
            'errors' => 0
        ];

        foreach ($users as $user) {
            $stats['total']++;
            
            if ($dryRun) {
                $verification = PermissionAssigner::verifyPermissions($user);
                if (!$verification['has_correct_permissions']) {
                    $stats['repaired']++;
                    $this->line("User {$user->email} needs repair:");
                    $this->line("  Missing: " . implode(', ', $verification['missing']));
                    $this->line("  Excess: " . implode(', ', $verification['excess']));
                } else {
                    $stats['skipped']++;
                }
                continue;
            }

            try {
                PermissionAssigner::repairPermissions($user);
                $stats['repaired']++;
                $this->info("✓ Repaired permissions for {$user->email}");
            } catch (\Exception $e) {
                $stats['errors']++;
                $this->error("✗ Failed to repair {$user->email}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Users', $stats['total']],
                ['Repaired', $stats['repaired']],
                ['Skipped (already correct)', $stats['skipped']],
                ['Errors', $stats['errors']],
            ]
        );
    }
}