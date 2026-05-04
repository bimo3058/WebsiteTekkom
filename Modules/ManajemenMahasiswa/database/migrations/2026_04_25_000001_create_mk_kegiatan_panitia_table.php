<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mk_kegiatan_panitia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('mk_kegiatan')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->timestamps();

            // Pastikan tidak ada duplikasi panitia per kegiatan
            $table->unique(['kegiatan_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mk_kegiatan_panitia');
    }
};
