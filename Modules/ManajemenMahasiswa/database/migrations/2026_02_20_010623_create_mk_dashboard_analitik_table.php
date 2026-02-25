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
        Schema::create('mk_dashboard_analitik', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lecturer_id')
                ->nullable()
                ->constrained('lecturers')
                ->nullOnDelete();

            $table->foreignId('generated_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('tanggal_generate');

            $table->integer('total_mahasiswa_aktif');
            $table->integer('total_alumni');
            $table->integer('total_kegiatan');
            $table->integer('total_pengumuman');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mk_dashboard_analitik');
    }
};
