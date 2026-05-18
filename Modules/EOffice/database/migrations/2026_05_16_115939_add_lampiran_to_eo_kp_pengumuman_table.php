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
        Schema::table('eo_kp_pengumuman', function (Blueprint $table) {
            $table->string('lampiran')->nullable()->after('konten')->comment('Path file lampiran pengumuman');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eo_kp_pengumuman', function (Blueprint $table) {
            $table->dropColumn('lampiran');
        });
    }
};
