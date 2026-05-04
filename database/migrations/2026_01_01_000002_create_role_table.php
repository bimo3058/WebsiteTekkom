<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama')->unique(); // admin, dosen, mahasiswa, asprak, koor_prak
            $table->string('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role');
    }
};
