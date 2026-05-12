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
        Schema::table('mk_prestasi', function (Blueprint $table) {
            $table->date('tanggal')->nullable()->after('tingkat');
            $table->dropColumn('tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mk_prestasi', function (Blueprint $table) {
            $table->dropColumn('tanggal');
            $table->integer('tahun')->nullable();
        });
    }
};
