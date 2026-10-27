<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rencana Proker berdiri sendiri (S-49).
     *
     * Saat Ajukan Proker, rencana (baris asli) dikunci dengan status `diajukan` dan
     * Pelaksanaan mendapat SALINAN baris baru. `rencana_id` dipasang pada salinan dan
     * menunjuk ke rencananya, jadi menghapus salinan di Pelaksanaan/Arsip tidak
     * menyentuh rencana. Kegiatan lama dan kegiatan yang ditambahkan langsung ke Arsip
     * tidak punya rencana (NULL).
     */
    public function up(): void
    {
        Schema::table('mk_kegiatan', function (Blueprint $table) {
            if (!Schema::hasColumn('mk_kegiatan', 'rencana_id')) {
                $table->foreignId('rencana_id')->nullable()->after('status')
                      ->constrained('mk_kegiatan')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('mk_kegiatan', function (Blueprint $table) {
            if (Schema::hasColumn('mk_kegiatan', 'rencana_id')) {
                $table->dropConstrainedForeignId('rencana_id');
            }
        });
    }
};
