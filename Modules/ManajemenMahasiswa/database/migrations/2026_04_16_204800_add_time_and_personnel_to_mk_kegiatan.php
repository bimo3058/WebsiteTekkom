<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mk_kegiatan', function (Blueprint $table) {
            // Jam mulai & selesai
            $table->time('jam_mulai')->nullable()->after('tanggal_mulai');
            $table->time('jam_selesai')->nullable()->after('tanggal_selesai');

            // Ketua pelaksana (dari daftar mahasiswa)
            $table->unsignedBigInteger('ketua_pelaksana_id')->nullable()->after('penanggung_jawab');
            $table->foreign('ketua_pelaksana_id')->references('id')->on('students')->onDelete('set null');

            // Dosen pendamping (opsional)
            $table->unsignedBigInteger('dosen_pendamping_id')->nullable()->after('ketua_pelaksana_id');
            $table->foreign('dosen_pendamping_id')->references('id')->on('lecturers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('mk_kegiatan', function (Blueprint $table) {
            $table->dropForeign(['ketua_pelaksana_id']);
            $table->dropForeign(['dosen_pendamping_id']);
            $table->dropColumn(['jam_mulai', 'jam_selesai', 'ketua_pelaksana_id', 'dosen_pendamping_id']);
        });
    }
};
