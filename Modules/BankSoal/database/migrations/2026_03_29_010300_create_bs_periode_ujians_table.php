<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bs_periode_ujians', function (Blueprint $table) {
            $table->id();
            $table->string('nama_periode');
            $table->string('slug')->unique();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->date('tanggal_mulai_ujian')->nullable();
            $table->date('tanggal_selesai_ujian')->nullable();
            $table->string('status')->default('draft');
            $table->text('deskripsi')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bs_periode_ujians');
    }
};
