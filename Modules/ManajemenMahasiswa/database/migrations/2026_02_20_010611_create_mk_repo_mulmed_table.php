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
        Schema::create('mk_repo_mulmed', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kegiatan_id')
                ->nullable()
                ->constrained('mk_kegiatan')
                ->cascadeOnDelete();

            $table->foreignId('pengumuman_id')
                ->nullable()
                ->constrained('mk_pengumuman')
                ->cascadeOnDelete();

            $table->string('nama_file', 255);
            $table->string('path_file', 255);
            $table->string('tipe_file', 50);
            $table->string('judul_file', 100);
            $table->text('deskripsi_meta')->nullable();

            $table->string('visibility_status', 20);
            $table->string('status_arsip', 20);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mk_repo_mulmed');
    }
};
