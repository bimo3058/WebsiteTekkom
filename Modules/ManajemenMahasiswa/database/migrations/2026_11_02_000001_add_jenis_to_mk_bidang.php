<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Membedakan Bidang dan Unit himpunan lewat kolom `jenis` (bidang|unit), sekaligus
 * menormalkan penulisan nama_bidang ke penulisan resmi. Baris diubah lewat id-nya
 * (dicari dengan nama lama, case-insensitive) sehingga relasi proker tidak berubah.
 */
return new class extends Migration
{
    /** nama resmi => jenis */
    private const BIDANG = [
        'PSDM'    => 'bidang',
        'SOSIAL'  => 'bidang',
        'EKOBIS'  => 'bidang',
        'RISTEK'  => 'bidang',
        'KESMA'   => 'bidang',
        'MIKATAN' => 'bidang',
        'Infokom' => 'unit',
        'PMO'     => 'unit',
    ];

    public function up(): void
    {
        if (!Schema::hasColumn('mk_bidang', 'jenis')) {
            Schema::table('mk_bidang', function (Blueprint $table) {
                $table->string('jenis', 10)->default('bidang')->after('nama_bidang');
            });
        }

        foreach (self::BIDANG as $nama => $jenis) {
            DB::table('mk_bidang')
                ->whereRaw('LOWER(nama_bidang) = ?', [strtolower($nama)])
                ->update(['nama_bidang' => $nama, 'jenis' => $jenis, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('mk_bidang', 'jenis')) {
            Schema::table('mk_bidang', function (Blueprint $table) {
                $table->dropColumn('jenis');
            });
        }
    }
};
