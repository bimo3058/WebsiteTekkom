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
        Schema::table('eo_kerja_praktik', function (Blueprint $table) {
            if (!Schema::hasColumn('eo_kerja_praktik', 'ipk')) {
                $table->decimal('ipk', 3, 2)->nullable()->after('rencana_tempat')
                      ->comment('Indeks Prestasi Kumulatif mahasiswa');
            }
            if (!Schema::hasColumn('eo_kerja_praktik', 'sks_diambil')) {
                $table->unsignedSmallInteger('sks_diambil')->nullable()->after('ipk')
                      ->comment('Jumlah SKS yang sedang diambil saat ini');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eo_kerja_praktik', function (Blueprint $table) {
            if (Schema::hasColumn('eo_kerja_praktik', 'ipk')) {
                $table->dropColumn('ipk');
            }
            if (Schema::hasColumn('eo_kerja_praktik', 'sks_diambil')) {
                $table->dropColumn('sks_diambil');
            }
        });
    }
};
