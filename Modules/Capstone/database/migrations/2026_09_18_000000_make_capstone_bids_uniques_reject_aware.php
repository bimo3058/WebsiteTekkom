<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Capstone: bidding uniques ignore REJECTED rows.
 *
 * A rejected bid keeps its row as history ("Riwayat Ditolak") while its slot
 * is freed, so the group may bid the same or another title again. The original
 * full-table uniques on (group_id, priority) and (group_id, title_id) blocked
 * that re-bid with a 23505 unique violation. Replace them with partial unique
 * indexes covering only active bids (status <> 'REJECTED'), matching the
 * "active bid" semantics used across BidController and StudentTitleAccess.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('capstone_bids', function (Blueprint $table) {
            $table->dropUnique('capstone_bids_group_id_priority_unique');
            $table->dropUnique('capstone_bids_group_id_title_id_unique');
        });
        DB::statement("CREATE UNIQUE INDEX capstone_bids_group_priority_active_unique ON capstone_bids (group_id, priority) WHERE status <> 'REJECTED'");
        DB::statement("CREATE UNIQUE INDEX capstone_bids_group_title_active_unique ON capstone_bids (group_id, title_id) WHERE status <> 'REJECTED'");
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS capstone_bids_group_priority_active_unique');
        DB::statement('DROP INDEX IF EXISTS capstone_bids_group_title_active_unique');
        Schema::table('capstone_bids', function (Blueprint $table) {
            $table->unique(['group_id', 'priority']);
            $table->unique(['group_id', 'title_id']);
        });
    }
};
