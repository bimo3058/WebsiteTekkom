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
        // 1. Tabel Master Fasilitas
        Schema::create('eo_mr_fasilitas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_fasilitas');
            $table->timestamps();
        });

        // 2. Tabel Pivot Many-to-Many Ruangan-Fasilitas
        Schema::create('eo_mr_ruangan_fasilitas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ruangan_id');
            $table->unsignedBigInteger('fasilitas_id');
            $table->timestamps();

            // Foreign Keys
            $table->foreign('ruangan_id')->references('id')->on('eo_mr_ruangans')->onDelete('cascade');
            $table->foreign('fasilitas_id')->references('id')->on('eo_mr_fasilitas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eo_mr_ruangan_fasilitas');
        Schema::dropIfExists('eo_mr_fasilitas');
    }
};
