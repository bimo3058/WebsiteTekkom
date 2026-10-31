<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Jenis dokumen kegiatan (Proposal, RAB, Surat Undangan, ...) dipilih pengguna lewat
 * dropdown saat mengunggah, supaya dokumen tetap dikenali walau nama berkasnya acak
 * (S-56). Baris lama & foto dibiarkan NULL = "belum dikelompokkan".
 * Daftar nilai sah: Support/JenisDokumenKegiatan.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('mk_repo_mulmed', 'jenis_dokumen')) {
            return;
        }

        Schema::table('mk_repo_mulmed', function (Blueprint $table) {
            $table->string('jenis_dokumen', 30)->nullable()->after('tipe_file');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('mk_repo_mulmed', 'jenis_dokumen')) {
            Schema::table('mk_repo_mulmed', function (Blueprint $table) {
                $table->dropColumn('jenis_dokumen');
            });
        }
    }
};
