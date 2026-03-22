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
        Schema::create('bs_pertanyaan', function (Blueprint $table) {
            $table->id();
            $table->string('soal');
            $table->binary('gambar')->nullable();
            $table->integer('bobot');
            $table->unsignedBigInteger('cpl_id');
            $table->unsignedBigInteger('mk_id');

            $table->enum('kesulitan', ['easy','intermediate','advanced'])->nullable();
            $table->enum('status', ['disetujui','revisi','draft','diajukan'])->default('draft');

            $table->timestamps();

            $table->foreign('cpl_id')
                ->references('id')->on('bs_cpl')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('mk_id')
                ->references('id')->on('bs_mata_kuliah')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bs_pertanyaan');
    }
};
