<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Structural guard: at most one leader per group. Without this, any code
 * path that moves members between groups without demoting them silently
 * produces multi-leader groups (kick/leave UI breaks: every row renders
 * as Leader, so no kick buttons appear and nobody can leave).
 *
 * Partial unique indexes are Postgres-only; skip on other drivers.
 * Run `php artisan capstone:repair-duplicate-leaders --fix` BEFORE
 * migrating, otherwise the index creation fails on existing duplicates.
 */
return new class extends Migration {
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }
        DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS capstone_group_members_single_leader ON capstone_group_members (group_id) WHERE is_leader = true');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }
        DB::statement('DROP INDEX IF EXISTS capstone_group_members_single_leader');
    }
};
