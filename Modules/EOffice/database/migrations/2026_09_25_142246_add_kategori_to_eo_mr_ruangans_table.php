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
        Schema::table('eo_mr_ruangans', function (Blueprint $table) {
            $table->enum('kategori', ['Kelas', 'Laboratorium', 'Sidang'])->default('Kelas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eo_mr_ruangans', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};
