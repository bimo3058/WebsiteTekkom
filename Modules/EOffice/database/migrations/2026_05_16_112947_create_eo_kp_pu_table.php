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
        Schema::create('eo_kp_pu', function (Blueprint $table) {
            $table->id();
            $table->text('pertanyaan');
            $table->text('jawaban');
            $table->string('kategori')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eo_kp_pu');
    }
};
