<?php

namespace Modules\ManajemenMahasiswa\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\ManajemenMahasiswa\Models\KategoriKegiatan;
use Modules\ManajemenMahasiswa\Models\Bidang;

class KategoriBidangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategori = [
            'Kegiatan Prodi',
            'Kegiatan Himpunan'
        ];

        foreach ($kategori as $kat) {
            KategoriKegiatan::firstOrCreate(['nama_kategori' => $kat]);
        }

        // nama resmi => jenis (bidang|unit)
        $bidang = [
            'PSDM'    => Bidang::JENIS_BIDANG,
            'SOSIAL'  => Bidang::JENIS_BIDANG,
            'EKOBIS'  => Bidang::JENIS_BIDANG,
            'RISTEK'  => Bidang::JENIS_BIDANG,
            'KESMA'   => Bidang::JENIS_BIDANG,
            'MIKATAN' => Bidang::JENIS_BIDANG,
            'Infokom' => Bidang::JENIS_UNIT,
            'PMO'     => Bidang::JENIS_UNIT,
        ];

        foreach ($bidang as $nama => $jenis) {
            // Dicocokkan case-insensitive agar baris lama ("Ristek") ikut dinormalkan,
            // bukan diduplikasi.
            $row = Bidang::whereRaw('LOWER(nama_bidang) = ?', [strtolower($nama)])->first();

            $row
                ? $row->update(['nama_bidang' => $nama, 'jenis' => $jenis])
                : Bidang::create(['nama_bidang' => $nama, 'jenis' => $jenis]);
        }
    }
}
