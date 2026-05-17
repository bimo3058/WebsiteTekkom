<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eo_matkul_praktikum', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kode')->unique();
            $table->string('nama');
            $table->integer('sks');
            $table->unsignedBigInteger('semester')->nullable();
            $table->timestamps();  // created_at & updated_at, default CURRENT_TIMESTAMP
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eo_matkul_praktikum');
    }
};
