<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Peran dosen diketik bebas seperti jabatan panitia (maks. 60 karakter, boleh kosong).
 * Nilai "Pendamping" hasil migrasi sebelumnya dikosongkan: itu hanya bawaan, bukan isian.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mk_kegiatan_dosen_pendamping', function (Blueprint $table) {
            $table->string('peran', 60)->nullable()->default(null)->change();
        });

        DB::table('mk_kegiatan_dosen_pendamping')->where('peran', 'Pendamping')->update(['peran' => null]);
    }

    public function down(): void
    {
        DB::table('mk_kegiatan_dosen_pendamping')->whereNull('peran')->update(['peran' => 'Pendamping']);

        Schema::table('mk_kegiatan_dosen_pendamping', function (Blueprint $table) {
            $table->string('peran', 30)->default('Pendamping')->nullable(false)->change();
        });
    }
};
