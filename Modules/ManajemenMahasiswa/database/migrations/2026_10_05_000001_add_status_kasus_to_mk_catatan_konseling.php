<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan status pemantauan kasus ke catatan konseling GPM:
 * baru, dalam_proses, selesai. Catatan lama ikut bernilai 'baru' lewat default kolom.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('mk_catatan_konseling', 'status_kasus')) {
            Schema::table('mk_catatan_konseling', function (Blueprint $table) {
                $table->string('status_kasus', 20)->default('baru')->after('kategori_kasus');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('mk_catatan_konseling', 'status_kasus')) {
            Schema::table('mk_catatan_konseling', function (Blueprint $table) {
                $table->dropColumn('status_kasus');
            });
        }
    }
};
