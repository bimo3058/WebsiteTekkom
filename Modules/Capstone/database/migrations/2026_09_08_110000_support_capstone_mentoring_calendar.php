<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('capstone_seminar_schedules', function (Blueprint $table) {
            $table->string('mode')->nullable();
            $table->text('notes')->nullable();
            $table->dropUnique(['group_id', 'type']);
        });
        // PostgreSQL (Supabase) and SQLite support partial unique indexes.
        // A group has one seminar per type, but many mentoring sessions.
        DB::statement("CREATE UNIQUE INDEX capstone_seminar_schedules_group_id_type_unique ON capstone_seminar_schedules (group_id, type) WHERE type <> 'BIMBINGAN'");
    }

    public function down(): void
    {
        // Restoring the old constraint may fail if multiple mentoring sessions
        // exist; deliberately never delete academic records during rollback.
        DB::transaction(function () {
            DB::statement('DROP INDEX capstone_seminar_schedules_group_id_type_unique');
            Schema::table('capstone_seminar_schedules', function (Blueprint $table) {
                $table->unique(['group_id', 'type']);
                $table->dropColumn(['mode', 'notes']);
            });
        });
    }
};
