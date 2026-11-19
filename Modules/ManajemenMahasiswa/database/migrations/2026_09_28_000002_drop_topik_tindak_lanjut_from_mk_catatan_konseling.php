<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fitur Topik & Tindak Lanjut di Catatan Konseling dicabut (keputusan pemilik
 * modul 28 Sep 2026). Migrasi pembuat tabel sudah tidak membuat kedua kolom
 * ini; migrasi ini membersihkan database yang terlanjur dibuat dengan versi
 * lama. Dijaga hasColumn supaya aman di instalasi baru.
 */
return new class extends Migration
{
    public function up(): void
    {
        $kolom = array_values(array_filter(
            ['topik', 'tindak_lanjut'],
            fn (string $k) => Schema::hasColumn('mk_catatan_konseling', $k)
        ));

        if ($kolom !== []) {
            Schema::table('mk_catatan_konseling', function (Blueprint $table) use ($kolom) {
                $table->dropColumn($kolom);
            });
        }
    }

    public function down(): void
    {
        // Sengaja tidak dikembalikan: fitur sudah dicabut.
    }
};
