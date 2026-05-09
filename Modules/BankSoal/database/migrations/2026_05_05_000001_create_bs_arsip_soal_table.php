<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bs_arsip_soal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mk_id');
            $table->unsignedBigInteger('dosen_id');
            $table->string('tahun_akademik');
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->string('nama_arsip');
            $table->enum('tipe_ujian', ['uts', 'uas', 'kuis', 'praktek', 'lainnya']);
            $table->date('tanggal_ujian')->nullable();
            $table->integer('jumlah_soal')->default(0);
            $table->decimal('total_bobot', 5, 2)->default(0);
            $table->longText('soal_data')->nullable();
            $table->string('pdf_file_path')->nullable();
            $table->string('pdf_file_hash')->nullable();
            $table->enum('status', ['draft', 'final', 'archived'])->default('draft');
            $table->text('deskripsi')->nullable();
            $table->text('catatan_internal')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('mk_id')->references('id')->on('bs_mata_kuliah')->cascadeOnDelete();
            $table->foreign('dosen_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index('dosen_id');
            $table->index('mk_id');
            $table->index(['tahun_akademik', 'semester']);
            $table->index('tipe_ujian');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bs_arsip_soal');
    }
};