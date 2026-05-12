<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /**
         * MODIFIKASI LOKAL:
         * Menambahkan return untuk melewati error 'Duplicate object' pada constraint 
         * foreign key yang sudah terdaftar di PostgreSQL.
         */
        return;

        // 1. Cek keberadaan kolom sebelum menambahkan
        if (!Schema::hasColumn('mk_kegiatan', 'kategori_kegiatan_id')) {
            Schema::table('mk_kegiatan', function (Blueprint $table) {
                $table->unsignedBigInteger('kategori_kegiatan_id')->nullable()->after('user_id');
            });
        }

        // 2. Tambahkan foreign key
        if (DB::connection()->getDriverName() === 'pgsql') {
            Schema::table('mk_kegiatan', function (Blueprint $table) {
                $table->foreign('kategori_kegiatan_id')
                    ->references('id')
                    ->on('mk_kategori_kegiatan')
                    ->onDelete('restrict');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mk_kegiatan', function (Blueprint $table) {
            if (DB::connection()->getDriverName() === 'pgsql') {
                $table->dropForeign(['kategori_kegiatan_id']);
            }

            if (Schema::hasColumn('mk_kegiatan', 'kategori_kegiatan_id')) {
                $table->dropColumn('kategori_kegiatan_id');
            }
        });
    }
};