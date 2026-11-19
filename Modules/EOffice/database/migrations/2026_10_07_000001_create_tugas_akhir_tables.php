<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('eo_tugas_akhir', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('praktikum_id')->unique();
            $table->text('ketentuan');
            $table->json('tahap');
            $table->json('komponen');
            $table->json('lampiran')->nullable();
            $table->json('riwayat')->nullable();
            $table->boolean('published')->default(false);
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();
            $table->foreign('praktikum_id')->references('id')->on('eo_praktikum');
        });
        Schema::create('eo_ta_pengumpulan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tugas_akhir_id')->index();
            $table->uuid('praktikan_id')->index();
            $table->uuid('tahap_id');
            $table->string('status')->default('dikumpulkan');
            $table->text('catatan')->nullable();
            $table->timestamp('deadline_revisi')->nullable();
            $table->json('riwayat');
            $table->timestamps();
            $table->unique(['tugas_akhir_id', 'praktikan_id', 'tahap_id'], 'ta_submission_unique');
            $table->foreign('tugas_akhir_id')->references('id')->on('eo_tugas_akhir');
            $table->foreign('praktikan_id')->references('id')->on('daftar_praktikan');
        });
        Schema::create('eo_ta_nilai', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tugas_akhir_id')->index();
            $table->uuid('praktikan_id');
            $table->json('skor');
            $table->json('riwayat');
            $table->unsignedInteger('version')->default(1);
            $table->boolean('disetujui_koor')->default(false);
            $table->boolean('disetujui_dosen')->default(false);
            $table->timestamps();
            $table->unique(['tugas_akhir_id', 'praktikan_id']);
            $table->foreign('tugas_akhir_id')->references('id')->on('eo_tugas_akhir');
            $table->foreign('praktikan_id')->references('id')->on('daftar_praktikan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eo_ta_nilai');
        Schema::dropIfExists('eo_ta_pengumpulan');
        Schema::dropIfExists('eo_tugas_akhir');
    }
};
