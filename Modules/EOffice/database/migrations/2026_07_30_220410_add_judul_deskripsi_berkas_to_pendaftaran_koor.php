<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manprak_periode_pendaftaran', function (Blueprint $table) {
            $table->string('judul')->nullable()->after('is_aktif');
            $table->text('deskripsi')->nullable()->after('judul');
            $table->string('nama_berkas_tambahan')->nullable()->after('deskripsi');
        });

        Schema::table('pendaftaran_koordinator', function (Blueprint $table) {
            $table->string('berkas_tambahan_path')->nullable()->after('berkas_cerc_path');
        });
    }

    public function down(): void
    {
        Schema::table('manprak_periode_pendaftaran', function (Blueprint $table) {
            $table->dropColumn(['judul', 'deskripsi', 'nama_berkas_tambahan']);
        });

        Schema::table('pendaftaran_koordinator', function (Blueprint $table) {
            $table->dropColumn('berkas_tambahan_path');
        });
    }
};