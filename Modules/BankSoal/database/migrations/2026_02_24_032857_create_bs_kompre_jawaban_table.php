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
        Schema::create('bs_kompre_jawaban', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kompre_session_id');
            $table->unsignedBigInteger('pertanyaan_id');
            $table->unsignedBigInteger('jawaban_dipilih');
            $table->integer('urutan_soal');
            $table->enum('kesulitan_now',['easy','intermediate','advanced']);
            $table->boolean('is_benar_now');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bs_kompre_jawaban');
    }
};
