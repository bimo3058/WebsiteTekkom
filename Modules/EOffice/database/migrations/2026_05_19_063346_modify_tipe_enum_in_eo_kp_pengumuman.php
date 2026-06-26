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
            // Changing enum to string is the safest approach across different DBs
            $table->string('tipe', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eo_kp_pengumuman', function (Blueprint $table) {
            // Revert back is hard because of the existing data, we can just leave it as string or try to revert to enum.
            // Leaving it as string is usually fine.
            $table->string('tipe', 255)->change();
        });
    }
};
