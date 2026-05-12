<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bs_jadwal_ujians', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('periode_ujian_id');
            $table->string('nama_sesi');
            $table->integer('kuota');
            $table->date('tanggal_ujian')->nullable();
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->string('ruangan')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bs_jadwal_ujians');
    }
};
