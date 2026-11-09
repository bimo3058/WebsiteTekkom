<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hapus kolom ipk dari mk_kemahasiswaan dan mk_alumni.
     * Fitur IPK dicabut dari modul Manajemen Mahasiswa (form, tampilan detail,
     * dan sinkronisasi Kemahasiswaan ↔ Alumni), jadi kolomnya tidak lagi dipakai.
     *
     * Nilai IPK yang sudah tersimpan ikut hilang. down() hanya memulihkan
     * struktur kolom, bukan isinya.
     */
    public function up(): void
    {
        Schema::table('mk_kemahasiswaan', function (Blueprint $table) {
            if (Schema::hasColumn('mk_kemahasiswaan', 'ipk')) {
                $table->dropColumn('ipk');
            }
        });

        Schema::table('mk_alumni', function (Blueprint $table) {
            if (Schema::hasColumn('mk_alumni', 'ipk')) {
                $table->dropColumn('ipk');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mk_kemahasiswaan', function (Blueprint $table) {
            if (!Schema::hasColumn('mk_kemahasiswaan', 'ipk')) {
                $table->decimal('ipk', 3, 2)->nullable()->after('status')
                      ->comment('IPK mahasiswa, range 0.00 - 4.00');
            }
        });

        Schema::table('mk_alumni', function (Blueprint $table) {
            if (!Schema::hasColumn('mk_alumni', 'ipk')) {
                $table->decimal('ipk', 3, 2)->nullable()->after('tahun_lulus')
                      ->comment('IPK mahasiswa saat lulus, range 0.00 - 4.00');
            }
        });
    }
};
