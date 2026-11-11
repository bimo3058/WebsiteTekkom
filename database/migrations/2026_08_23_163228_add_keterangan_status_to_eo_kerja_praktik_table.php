<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('eo_kerja_praktik', 'keterangan_status')) {
            return;
        }

        Schema::table('eo_kerja_praktik', function (Blueprint $table) {
            $table->string('keterangan_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('eo_kerja_praktik', 'keterangan_status')) {
            return;
        }

        Schema::table('eo_kerja_praktik', function (Blueprint $table) {
            $table->dropColumn('keterangan_status');
        });
    }
};
