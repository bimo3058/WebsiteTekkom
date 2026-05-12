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
        Schema::create('bs_jawaban', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('soal_id');
            $table->char('opsi',1);
            $table->string('deskripsi');
            $table->binary('gambar')->nullable();
            $table->boolean('is_benar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bs_jawaban');
    }
};
