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
        Schema::table('tugas_praktikum', function (Blueprint $table) {
            $table->dateTime('deadline_acc')->nullable()->after('deadline');
        });

        Schema::create('riwayat_pengumpulan_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengumpulan_tugas_id')->constrained('pengumpulan_tugas')->cascadeOnDelete();
            $table->string('file_path');
            $table->text('catatan')->nullable();
            $table->boolean('is_revision')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pengumpulan_tugas');
        Schema::table('tugas_praktikum', function (Blueprint $table) {
            $table->dropColumn('deadline_acc');
        });
    }
};
