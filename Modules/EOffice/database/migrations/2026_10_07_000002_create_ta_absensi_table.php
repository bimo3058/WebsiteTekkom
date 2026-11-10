<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('eo_ta_absensi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tugas_akhir_id');
            $table->uuid('praktikan_id');
            $table->string('status');
            $table->text('keterangan')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->json('riwayat');
            $table->timestamps();
            $table->unique(['tugas_akhir_id', 'praktikan_id']);
            $table->foreign('tugas_akhir_id')->references('id')->on('eo_tugas_akhir');
            $table->foreign('praktikan_id')->references('id')->on('daftar_praktikan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eo_ta_absensi');
    }
};
