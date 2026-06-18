<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tugas_praktikum', function (Blueprint $table) {
            // Tambah kolom jenis_tugas setelah modul_id
            // Nullable dulu agar data lama tidak error, lalu bisa diset default
            $table->string('jenis_tugas')->nullable()->after('modul_id');
        });
    }

    public function down(): void
    {
        Schema::table('tugas_praktikum', function (Blueprint $table) {
            $table->dropColumn('jenis_tugas');
        });
    }
};
