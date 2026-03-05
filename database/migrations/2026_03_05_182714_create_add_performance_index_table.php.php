<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Capstone
        DB::statement('
            CREATE INDEX IF NOT EXISTS idx_capstone_groups_period_status 
            ON capstone_groups(period_id, status) WHERE deleted_at IS NULL
        ');

        DB::statement('
            CREATE INDEX IF NOT EXISTS idx_capstone_titles_status_approved 
            ON capstone_titles(status, approved_by_admin) WHERE deleted_at IS NULL
        ');

        DB::statement('
            CREATE INDEX IF NOT EXISTS idx_capstone_bids_group_status 
            ON capstone_bids(group_id, status)
        ');

        // BankSoal
        DB::statement('
            CREATE INDEX IF NOT EXISTS idx_pertanyaan_mk_status 
            ON bs_pertanyaan(mk_id, status)
        ');

        // ManajemenMahasiswa
        DB::statement('
            CREATE INDEX IF NOT EXISTS idx_kegiatan_tanggal 
            ON mk_kegiatan(tanggal, bidang_id)
        ');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS idx_capstone_groups_period_status');
        DB::statement('DROP INDEX IF EXISTS idx_capstone_titles_status_approved');
        DB::statement('DROP INDEX IF EXISTS idx_capstone_bids_group_status');
        DB::statement('DROP INDEX IF EXISTS idx_pertanyaan_mk_status');
        DB::statement('DROP INDEX IF EXISTS idx_kegiatan_tanggal');
    }
};
