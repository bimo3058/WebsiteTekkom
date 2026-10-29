<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Langkah wizard CV tertinggi yang pernah disimpan lewat "Simpan & Lanjut".
     * Langkah berikutnya baru terbuka di stepper setelah langkah sebelumnya
     * disimpan dengan tombol itu (R-35).
     */
    public function up(): void
    {
        Schema::table('cv_profiles', function (Blueprint $table) {
            $table->unsignedTinyInteger('langkah_tersimpan')->default(0)->after('template');
        });

        // Baris lama dulunya membuka keenam langkah sekaligus; dipertahankan supaya
        // pemilik CV yang sudah ada tidak tiba-tiba terkunci.
        DB::table('cv_profiles')->update(['langkah_tersimpan' => 5]);
    }

    public function down(): void
    {
        Schema::table('cv_profiles', function (Blueprint $table) {
            $table->dropColumn('langkah_tersimpan');
        });
    }
};
