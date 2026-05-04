<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mk_kegiatan', function (Blueprint $table) {
            // Make bidang_id nullable so "Kegiatan Prodi" doesn't require a bidang
            $table->unsignedBigInteger('bidang_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('mk_kegiatan', function (Blueprint $table) {
            $table->unsignedBigInteger('bidang_id')->nullable(false)->change();
        });
    }
};
