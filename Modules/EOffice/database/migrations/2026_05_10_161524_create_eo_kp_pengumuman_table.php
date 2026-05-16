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
        // Drop tabel lama (dibuat oleh 2026_05_08 dengan kolom deskripsi/is_published)
        // lalu buat ulang dengan skema baru (konten, tipe, is_active)
        Schema::dropIfExists('eo_kp_pengumuman');

        Schema::create('eo_kp_pengumuman', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('konten');
            $table->enum('tipe', ['pengumuman', 'timeline', 'faq'])->default('pengumuman');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eo_kp_pengumuman');
    }
};
