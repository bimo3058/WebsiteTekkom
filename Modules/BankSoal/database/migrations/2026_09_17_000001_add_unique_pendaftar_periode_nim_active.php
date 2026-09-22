<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Partial unique index: cegah NIM duplikat di periode yang sama selama aktif (deleted_at IS NULL)
        // Soft-deleted (rejected) boleh daftar ulang -> tidak masuk index
        // PostgreSQL mendukung WHERE, MySQL tidak -> gunakan raw statement untuk Postgres, fallback untuk MySQL
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS uniq_pendaftar_periode_nim_active ON bs_pendaftar_ujians (periode_ujian_id, nim) WHERE deleted_at IS NULL');
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS uniq_pendaftar_periode_mahasiswa_active ON bs_pendaftar_ujians (periode_ujian_id, mahasiswa_id) WHERE deleted_at IS NULL');
        } else {
            // MySQL fallback: unique index mencakup deleted_at (nilai NULL dianggap unik, tapi duplikat NULL tetap lolos di MySQL <8.0.13, jadi perlu aplikasi layer juga)
            // Tetap buat index untuk bantu query, enforcement utama di aplikasi + partial index via trigger jika perlu
            try {
                Schema::table('bs_pendaftar_ujians', function ($table) {
                    $table->unique(['periode_ujian_id', 'nim'], 'uniq_pendaftar_periode_nim_active');
                });
            } catch (\Throwable $e) {
                // Index mungkin sudah ada
            }
            try {
                Schema::table('bs_pendaftar_ujians', function ($table) {
                    $table->unique(['periode_ujian_id', 'mahasiswa_id'], 'uniq_pendaftar_periode_mahasiswa_active');
                });
            } catch (\Throwable $e) {
            }
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS uniq_pendaftar_periode_nim_active');
            DB::statement('DROP INDEX IF EXISTS uniq_pendaftar_periode_mahasiswa_active');
        } else {
            try {
                Schema::table('bs_pendaftar_ujians', function ($table) {
                    $table->dropUnique('uniq_pendaftar_periode_nim_active');
                });
            } catch (\Throwable $e) {
            }
            try {
                Schema::table('bs_pendaftar_ujians', function ($table) {
                    $table->dropUnique('uniq_pendaftar_periode_mahasiswa_active');
                });
            } catch (\Throwable $e) {
            }
        }
    }
};
