<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mk_riwayat_kegiatan', function (Blueprint $table) {
            // Buat peran nullable (agar entri manual tanpa peran bawaan bisa disimpan)
            $table->string('peran', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('mk_riwayat_kegiatan', function (Blueprint $table) {
            $table->string('peran', 255)->nullable(false)->change();
        });
    }
};
