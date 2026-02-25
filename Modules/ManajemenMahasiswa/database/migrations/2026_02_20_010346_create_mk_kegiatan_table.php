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
        Schema::create('mk_kegiatan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kategori_kegiatan_id')
                ->constrained('mk_kategori_kegiatan')
                ->cascadeOnDelete();

            $table->foreignId('bidang_id')
                ->constrained('mk_bidang')
                ->cascadeOnDelete();

            $table->foreignId('lecturer_id')
                ->nullable()
                ->constrained('lecturers')
                ->nullOnDelete();

            $table->foreignId('ketua_student_id')
                ->nullable()
                ->constrained('students')
                ->nullOnDelete();

            $table->foreignId('kepengurusan_id')
                ->nullable()
                ->constrained('mk_kepengurusan')
                ->nullOnDelete();

            $table->string('nama_kegiatan', 150);
            $table->date('tanggal');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mk_kegiatan');
    }
};
