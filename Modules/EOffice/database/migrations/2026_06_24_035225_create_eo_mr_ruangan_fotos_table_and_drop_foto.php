<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create eo_mr_ruangan_fotos
        Schema::create('eo_mr_ruangan_fotos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ruangan_id');
            $table->string('path_foto');
            $table->integer('urutan')->default(0);
            $table->timestamps();

            $table->foreign('ruangan_id')->references('id')->on('eo_mr_ruangans')->onDelete('cascade');
        });

        // 2. Drop foto column from eo_mr_ruangans
        Schema::table('eo_mr_ruangans', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Re-add foto column
        Schema::table('eo_mr_ruangans', function (Blueprint $table) {
            $table->string('foto')->nullable();
        });

        // 2. Drop eo_mr_ruangan_fotos
        Schema::dropIfExists('eo_mr_ruangan_fotos');
    }
};
