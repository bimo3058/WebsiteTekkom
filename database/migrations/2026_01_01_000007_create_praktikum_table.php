<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('praktikum', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->string('kode')->nullable();
            $table->text('deskripsi')->nullable();
            $table->uuid('dosen_id')->nullable();
            $table->uuid('koor_id')->nullable();   // field tambahan dari klarifikasi
            $table->integer('tahun_ajaran');
            $table->string('semester');              // ganjil, genap
            $table->string('status')->default('aktif'); // aktif, nonaktif
            $table->timestamps(); // dibuat_pada via created_at
            $table->softDeletes();

            $table->foreign('dosen_id')->references('id')->on('pengguna')->onDelete('set null');
            $table->foreign('koor_id')->references('id')->on('pengguna')->onDelete('set null');

            $table->index(['tahun_ajaran', 'semester']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('praktikum');
    }
};
