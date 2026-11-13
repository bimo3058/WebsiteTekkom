<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Opsi sumber dana "Lainnya" dicabut (Surya, 10 Nov 2026): proposal HIMASKOM hanya
 * mengenal Dana Departemen, Kas Himpunan, dan Kas Bidang. Kolomnya baru ada beberapa
 * jam dan tidak pernah terisi (0 baris), jadi dibuang tanpa kehilangan data.
 *
 * Aman dijalankan ulang: kolom dicek dulu (instalasi baru tidak pernah membuatnya).
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['dana_lainnya', 'dana_lainnya_keterangan'] as $kolom) {
            if (Schema::hasColumn('mk_kegiatan', $kolom)) {
                Schema::table('mk_kegiatan', fn (Blueprint $t) => $t->dropColumn($kolom));
            }
        }
    }

    public function down(): void
    {
        Schema::table('mk_kegiatan', function (Blueprint $table) {
            if (!Schema::hasColumn('mk_kegiatan', 'dana_lainnya')) {
                $table->decimal('dana_lainnya', 15, 2)->nullable()->after('kas_bidang');
                $table->string('dana_lainnya_keterangan', 150)->nullable()->after('dana_lainnya');
            }
        });
    }
};
