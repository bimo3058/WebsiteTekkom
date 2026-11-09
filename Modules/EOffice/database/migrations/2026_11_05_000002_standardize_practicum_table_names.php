<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\{DB,Schema};

return new class extends Migration {
    // Frozen map: historical migrations keep their original names for fresh installs/rollback.
    public const TABLES = [
        'eo_praktikum'=>'eo_prak_praktikum',
        'eo_praktikum_dosen'=>'eo_prak_praktikum_dosen',
        'eo_matkul_praktikum'=>'eo_prak_matkul_praktikum',
        'manprak_periode_pendaftaran'=>'eo_prak_periode_pendaftaran',
        'pendaftaran_koordinator'=>'eo_prak_pendaftaran_koordinator',
        'pendaftaran_asprak'=>'eo_prak_pendaftaran_asprak',
        'pendaftaran_praktikan'=>'eo_prak_pendaftaran_praktikan',
        'daftar_praktikan'=>'eo_prak_daftar_praktikan',
        'asprak_praktikum'=>'eo_prak_asprak_praktikum',
        'modul_praktikum'=>'eo_prak_modul_praktikum',
        'modul_asprak'=>'eo_prak_modul_asprak',
        'materi_modul'=>'eo_prak_materi_modul',
        'tugas_praktikum'=>'eo_prak_tugas_praktikum',
        'pengumpulan_tugas'=>'eo_prak_pengumpulan_tugas',
        'riwayat_pengumpulan_tugas'=>'eo_prak_riwayat_pengumpulan_tugas',
        'absensi_praktikum'=>'eo_prak_absensi_praktikum',
        'nilai_praktikum'=>'eo_prak_nilai_praktikum',
        'nilai_jenis_tugas'=>'eo_prak_nilai_jenis_tugas',
        'pengumuman_praktikum'=>'eo_prak_pengumuman_praktikum',
        'pengajuan_modul'=>'eo_prak_pengajuan_modul',
        'pengajuan_modul_item'=>'eo_prak_pengajuan_modul_item',
        'eo_tugas_akhir'=>'eo_prak_tugas_akhir',
        'eo_ta_pengumpulan'=>'eo_prak_ta_pengumpulan',
        'eo_ta_nilai'=>'eo_prak_ta_nilai',
        'eo_ta_absensi'=>'eo_prak_ta_absensi',
        'praktikum'=>'eo_prak_praktikum_legacy',
    ];

    public function up(): void { $this->rename(self::TABLES); }
    public function down(): void { $this->rename(array_flip(self::TABLES)); }

    private function rename(array $mapping): void
    {
        DB::transaction(function () use ($mapping) {
            if(DB::getDriverName()==='pgsql') {
                DB::statement("SET LOCAL lock_timeout = '5s'");
                DB::statement("SET LOCAL statement_timeout = '30s'");
            }
            $tables=array_fill_keys(Schema::getTableListing(null,false),true);
            foreach($mapping as $old=>$new)if(isset($tables[$old],$tables[$new]))throw new RuntimeException("Kedua tabel {$old} dan {$new} tersedia. Migrasi dihentikan agar data tidak ditimpa.");
            foreach($mapping as $old=>$new)if(isset($tables[$old]))Schema::rename($old,$new);
        });
    }
};
