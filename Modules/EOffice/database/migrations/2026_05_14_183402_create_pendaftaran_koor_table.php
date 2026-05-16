<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftaran_koordinator', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->uuid('praktikum_id');
            $table->float('ipk')->nullable();
            $table->text('motivasi')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('alasan_penolakan')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('praktikum_id')->references('id')->on('eo_praktikum')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_koordinator');
    }
};