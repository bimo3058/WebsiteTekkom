<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daftar_praktikan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('praktikum_id');
            $table->unsignedBigInteger('user_id');
            $table->string('status')->default('terdaftar'); // terdaftar, lulus, gagal
            $table->timestamps();

            $table->foreign('praktikum_id')->references('id')->on('eo_praktikum')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['praktikum_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daftar_praktikan');
    }
};