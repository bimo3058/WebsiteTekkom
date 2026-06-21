<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dokumen aturan reward prestasi (SK FT 774 / aturan terbaru) yang diunggah
     * admin. Ditampilkan ke mahasiswa & admin sebagai rujukan saat mengajukan /
     * meninjau reward. Bisa PDF atau gambar; disimpan di Supabase storage.
     */
    public function up(): void
    {
        if (!Schema::hasTable('mk_reward_aturan')) {
            Schema::create('mk_reward_aturan', function (Blueprint $table) {
                $table->id();
                $table->string('judul', 150);
                $table->string('nama_file');
                $table->string('path_file');
                $table->string('tipe_file', 20)->default('document'); // image | document
                $table->unsignedBigInteger('uploaded_by')->nullable();
                $table->timestamps();

                $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mk_reward_aturan');
    }
};
