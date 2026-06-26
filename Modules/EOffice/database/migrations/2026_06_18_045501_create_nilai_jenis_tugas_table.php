<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_jenis_tugas', function (Blueprint $table) {
            $table->id();
            $table->uuid('daftar_praktikan_id');
            $table->foreignId('modul_id')->constrained('modul_praktikum')->cascadeOnDelete();
            // Jenis: tugas_pendahuluan | praktikum | laporan | responsi
            $table->string('jenis_tugas');
            $table->float('nilai')->nullable(); // 0–100
            $table->timestamps();

            $table->foreign('daftar_praktikan_id')
                  ->references('id')
                  ->on('daftar_praktikan')
                  ->cascadeOnDelete();

            // Satu nilai per mahasiswa per modul per jenis
            $table->unique(['daftar_praktikan_id', 'modul_id', 'jenis_tugas'], 'uniq_nilai_jenis');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_jenis_tugas');
    }
};
