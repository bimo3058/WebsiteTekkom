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

        DB::statement("UPDATE bs_dosen_pengampu_mk SET is_rps = FALSE WHERE is_rps IS NULL");
        DB::statement("ALTER TABLE bs_dosen_pengampu_mk ALTER COLUMN is_rps SET DEFAULT FALSE");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement("ALTER TABLE bs_dosen_pengampu_mk ALTER COLUMN is_rps DROP DEFAULT");
    }
};
