<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel periode pendaftaran (koor & asprak)
        Schema::create('manprak_periode_pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->uuid('praktikum_id');
            $table->string('jenis')->default('koor'); // koor | asprak
            $table->string('nama')->nullable();
            $table->dateTime('dibuka_pada')->nullable();
            $table->dateTime('ditutup_pada')->nullable();
            $table->boolean('is_aktif')->default(false);
            $table->unsignedBigInteger('dibuka_oleh')->nullable(); // admin user_id
            $table->timestamps();

            $table->foreign('praktikum_id')->references('id')->on('eo_praktikum')->cascadeOnDelete();
            $table->foreign('dibuka_oleh')->references('id')->on('users')->nullOnDelete();
        });

        // Tambah kolom ke pendaftaran_koordinator
        Schema::table('pendaftaran_koordinator', function (Blueprint $table) {
            // Siapa yang mereview (dosen)
            $table->unsignedBigInteger('direview_oleh')->nullable()->after('alasan_penolakan');
            $table->timestamp('direview_pada')->nullable()->after('direview_oleh');
            // Status dosen vs admin: dosen approve dulu, baru admin final approve
            $table->string('status_dosen')->default('menunggu')->after('status'); // menunggu|disetujui|ditolak
            $table->text('catatan_dosen')->nullable()->after('status_dosen');

            $table->foreign('direview_oleh')->references('id')->on('users')->nullOnDelete();
        });

        // Tambah kolom ke pendaftaran_asprak
        Schema::table('pendaftaran_asprak', function (Blueprint $table) {
            // Review oleh koor
            $table->unsignedBigInteger('direview_oleh')->nullable()->after('alasan_penolakan');
            $table->timestamp('direview_pada')->nullable()->after('direview_oleh');
            $table->text('catatan_koor')->nullable()->after('direview_pada');

            $table->foreign('direview_oleh')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran_asprak', function (Blueprint $table) {
            $table->dropForeign(['direview_oleh']);
            $table->dropColumn(['direview_oleh', 'direview_pada', 'catatan_koor']);
        });
        Schema::table('pendaftaran_koordinator', function (Blueprint $table) {
            $table->dropForeign(['direview_oleh']);
            $table->dropColumn(['direview_oleh', 'direview_pada', 'status_dosen', 'catatan_dosen']);
        });
        Schema::dropIfExists('manprak_periode_pendaftaran');
    }
};
