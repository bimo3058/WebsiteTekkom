<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Penanda dokumen hasil salinan Rencana Proker saat Ajukan (S-59): dokumen ini
 * tidak boleh dihapus di Pelaksanaan Kegiatan.
 *
 * Salinan yang sudah ada sebelum kolom ini dikenali lewat nama berkas yang sama
 * dengan dokumen rencananya (penyalinan mempertahankan nama_file).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('mk_repo_mulmed', 'dari_rencana')) {
            Schema::table('mk_repo_mulmed', function (Blueprint $table) {
                $table->boolean('dari_rencana')->default(false)->after('jenis_dokumen');
            });
        }

        $salinan = DB::table('mk_kegiatan')->whereNotNull('rencana_id')->pluck('rencana_id', 'id');
        foreach ($salinan as $kegiatanId => $rencanaId) {
            $namaRencana = DB::table('mk_repo_mulmed')
                ->where('kegiatan_id', $rencanaId)
                ->where('tipe_file', 'document')
                ->pluck('nama_file');

            if ($namaRencana->isNotEmpty()) {
                DB::table('mk_repo_mulmed')
                    ->where('kegiatan_id', $kegiatanId)
                    ->where('tipe_file', 'document')
                    ->whereIn('nama_file', $namaRencana)
                    ->update(['dari_rencana' => true]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('mk_repo_mulmed', 'dari_rencana')) {
            Schema::table('mk_repo_mulmed', function (Blueprint $table) {
                $table->dropColumn('dari_rencana');
            });
        }
    }
};
