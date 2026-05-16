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
        Schema::table('bs_periode_ujians', function (Blueprint $table) {
            $table->unsignedInteger('kuota_peserta')->nullable()->after('pendaftaran_ditutup_paksa')
                  ->comment('Batas maksimal jumlah peserta ujian komprehensif');
            $table->json('target_wisuda_options')->nullable()->after('kuota_peserta')
                  ->comment('Pilihan target wisuda yang dapat dipilih mahasiswa saat mendaftar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bs_periode_ujians', function (Blueprint $table) {
            $table->dropColumn(['kuota_peserta', 'target_wisuda_options']);
        });
    }
};
