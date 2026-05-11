<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        foreach (['bs_cpl', 'bs_cpmk'] as $table) {
            $maxId = (int) (DB::table($table)->max('id') ?? 0);

            if ($maxId > 0) {
                DB::statement("SELECT setval(pg_get_serial_sequence('{$table}', 'id'), {$maxId}, true)");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Sequence sync is safe to re-run and does not need rollback.
    }
};