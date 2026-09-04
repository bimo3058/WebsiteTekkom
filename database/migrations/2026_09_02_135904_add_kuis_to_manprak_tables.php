<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('periode_pendaftarans', function (Blueprint $table) {
            $table->json('konfigurasi_kuis')->nullable()->after('nama_berkas_tambahan');
        });

        Schema::table('pendaftaran_aspraks', function (Blueprint $table) {
            $table->json('jawaban_kuis')->nullable()->after('berkas_cerc_path');
            $table->integer('skor_kuis')->nullable()->after('jawaban_kuis');
        });

        Schema::table('pendaftaran_koordinators', function (Blueprint $table) {
            $table->json('jawaban_kuis')->nullable()->after('berkas_cerc_path');
            $table->integer('skor_kuis')->nullable()->after('jawaban_kuis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('periode_pendaftarans', function (Blueprint $table) {
            $table->dropColumn('konfigurasi_kuis');
        });

        Schema::table('pendaftaran_aspraks', function (Blueprint $table) {
            $table->dropColumn(['jawaban_kuis', 'skor_kuis']);
        });

        Schema::table('pendaftaran_koordinators', function (Blueprint $table) {
            $table->dropColumn(['jawaban_kuis', 'skor_kuis']);
        });
    }
};
