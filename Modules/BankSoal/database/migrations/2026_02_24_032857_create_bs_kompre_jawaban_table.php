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

            $table->foreign('kompre_session_id')
                ->references('id')->on('bs_kompre_session')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('pertanyaan_id')
                ->references('id')->on('bs_pertanyaan')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('jawaban_dipilih')
                ->references('id')->on('bs_jawaban')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
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
