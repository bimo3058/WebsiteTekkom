<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Alur tinjau ketua pelaksana ↔ KABID (S-82, 11 Nov 2026).
 *
 * KABID membuat proker dan menunjuk ketua pelaksana; ketua pelaksana mengisi lalu
 * mengirim; KABID menyetujui atau mengembalikan dengan catatan. Berlaku di Rencana
 * Proker (status `draft`) dan Pelaksanaan Kegiatan (status `disetujui`).
 *
 * `status_tinjauan`: dilengkapi (Menunggu Dilengkapi) → ditinjau (Menunggu Tinjauan)
 * → perbaikan (Perlu Perbaikan) → ditinjau → … Kosong untuk rencana yang sudah
 * diteruskan dan kegiatan di Arsip.
 *
 * Data lama: draft & pelaksanaan diisi `dilengkapi`, kecuali draft yang masih punya
 * catatan penolakan terbuka (S-79) → `perbaikan`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mk_kegiatan', function (Blueprint $table) {
            $table->string('status_tinjauan', 20)->nullable()->after('status')->index();
            $table->timestamp('dikirim_tinjau_at')->nullable()->after('status_tinjauan');
        });

        DB::table('mk_kegiatan')
            ->whereIn('status', ['draft', 'disetujui'])
            ->update(['status_tinjauan' => 'dilengkapi']);

        DB::table('mk_kegiatan')
            ->where('status', 'draft')
            ->whereExists(fn ($q) => $q->select(DB::raw(1))
                ->from('mk_kegiatan_catatan')
                ->whereColumn('mk_kegiatan_catatan.kegiatan_id', 'mk_kegiatan.id')
                ->whereNull('mk_kegiatan_catatan.diperbaiki_at'))
            ->update(['status_tinjauan' => 'perbaikan']);
    }

    public function down(): void
    {
        Schema::table('mk_kegiatan', function (Blueprint $table) {
            $table->dropIndex(['status_tinjauan']);
            $table->dropColumn(['status_tinjauan', 'dikirim_tinjau_at']);
        });
    }
};
