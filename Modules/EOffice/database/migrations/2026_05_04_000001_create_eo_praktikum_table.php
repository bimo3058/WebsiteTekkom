<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eo_praktikum', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('kode')->nullable();
            $table->text('deskripsi')->nullable();

            // FK ke tabel users global superapp (bukan pengguna)
            $table->unsignedBigInteger('dosen_id')->nullable();
            $table->unsignedBigInteger('koor_id')->nullable();

            $table->integer('tahun_ajaran');
            $table->string('semester');           // ganjil, genap
            $table->string('status')->default('aktif'); // aktif, nonaktif
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('dosen_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('koor_id')->references('id')->on('users')->onDelete('set null');

            $table->index(['tahun_ajaran', 'semester']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eo_praktikum');
    }
};
