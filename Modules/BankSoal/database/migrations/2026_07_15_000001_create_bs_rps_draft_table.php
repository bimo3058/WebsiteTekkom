<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bs_rps_draft', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('mk_id');
            $table->string('semester');
            $table->string('tahun_ajaran');
            $table->string('creation_method')->default('generator');
            $table->tinyInteger('step_reached')->default(0);
            $table->json('dosen_lain')->nullable();
            $table->json('data_step_1')->nullable();
            $table->json('data_step_2')->nullable();
            $table->json('data_step_3')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('mk_id')->references('id')->on('bs_mata_kuliah')->cascadeOnDelete();
            $table->unique(['user_id', 'mk_id', 'semester', 'tahun_ajaran', 'creation_method'], 'bs_rps_draft_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bs_rps_draft');
    }
};
