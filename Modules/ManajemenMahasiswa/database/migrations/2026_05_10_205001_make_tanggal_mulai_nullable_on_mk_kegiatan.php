<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE mk_kegiatan ALTER COLUMN tanggal_mulai DROP NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Because there may be null values, we cannot simply SET NOT NULL without providing a default or cleaning up.
        // We'll leave it as is or do a best effort.
        // DB::statement('ALTER TABLE mk_kegiatan ALTER COLUMN tanggal_mulai SET NOT NULL');
    }
};
