<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS uniq_jadwal_periode_sesi_tanggal_active ON bs_jadwal_ujians (periode_ujian_id, nama_sesi, tanggal_ujian) WHERE deleted_at IS NULL');
        } else {
            try {
                Schema::table('bs_jadwal_ujians', function ($table) {
                    $table->unique(['periode_ujian_id', 'nama_sesi', 'tanggal_ujian'], 'uniq_jadwal_periode_sesi_tanggal_active');
                });
            } catch (\Throwable $e) {
            }
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS uniq_jadwal_periode_sesi_tanggal_active');
        } else {
            try {
                Schema::table('bs_jadwal_ujians', function ($table) {
                    $table->dropUnique('uniq_jadwal_periode_sesi_tanggal_active');
                });
            } catch (\Throwable $e) {
            }
        }
    }
};
