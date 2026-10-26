<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom "Catatan Tambahan" (catatan) di Catatan Konseling dicabut (keputusan
 * pemilik modul 17 Okt 2026). Catatan lama dari sebelum ada Kronologi hanya
 * berisi kolom ini, jadi isinya dipindah ke kronologi dulu supaya tidak hilang:
 * kronologi kosong → diisi catatan; keduanya terisi → catatan ditambahkan di
 * bawah kronologi. Kedua kolom terenkripsi (cast `encrypted` di model).
 * Dijaga hasColumn supaya aman di instalasi baru.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('mk_catatan_konseling', 'catatan')) {
            return;
        }

        // Termasuk catatan yang sudah dihapus (soft delete).
        DB::table('mk_catatan_konseling')
            ->whereNotNull('catatan')
            ->orderBy('id')
            ->get(['id', 'catatan', 'kronologi'])
            ->each(function ($row) {
                $catatan = trim(Crypt::decryptString($row->catatan));
                if ($catatan === '') {
                    return;
                }

                $kronologi = $row->kronologi !== null ? trim(Crypt::decryptString($row->kronologi)) : '';
                $gabungan = $kronologi === ''
                    ? $catatan
                    : $kronologi . "\n\nCatatan tambahan:\n" . $catatan;

                DB::table('mk_catatan_konseling')
                    ->where('id', $row->id)
                    ->update(['kronologi' => Crypt::encryptString($gabungan)]);
            });

        Schema::table('mk_catatan_konseling', function (Blueprint $table) {
            $table->dropColumn('catatan');
        });
    }

    public function down(): void
    {
        // Struktur saja; isi yang sudah digabung ke kronologi tidak dipisah lagi.
        if (! Schema::hasColumn('mk_catatan_konseling', 'catatan')) {
            Schema::table('mk_catatan_konseling', function (Blueprint $table) {
                $table->text('catatan')->nullable();
            });
        }
    }
};
