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
        Schema::table('eo_kerja_praktik', function (Blueprint $table) {
            if (!Schema::hasColumn('eo_kerja_praktik', 'kelas')) {
                $table->string('kelas', 50)->nullable()->after('ipk')
                      ->comment('Kelas pilihan mahasiswa');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eo_kerja_praktik', function (Blueprint $table) {
            if (Schema::hasColumn('eo_kerja_praktik', 'kelas')) {
                $table->dropColumn('kelas');
            }
        });
    }
};
