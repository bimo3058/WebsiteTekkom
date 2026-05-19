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
        // For PostgreSQL, Laravel enum() creates a CHECK constraint
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE eo_kp_pengumuman DROP CONSTRAINT IF EXISTS eo_kp_pengumuman_tipe_check;');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optional: Re-add check constraint if needed
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE eo_kp_pengumuman ADD CONSTRAINT eo_kp_pengumuman_tipe_check CHECK (tipe::text = ANY (ARRAY['pengumuman'::character varying, 'timeline'::character varying, 'faq'::character varying]::text[]));");
        }
    }
};
