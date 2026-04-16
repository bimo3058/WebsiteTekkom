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
        Schema::create('bs_jadwal_ujians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_ujian_id')->constrained('bs_periode_ujians')->cascadeOnDelete();
            $table->string('nama_sesi');
            $table->integer('kuota');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->string('ruangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bs_jadwal_ujians');
    }
};
