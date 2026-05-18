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

        $bidang = [
            'Ristek',
            'Infokom',
            'Ekobis',
            'Kesma',
            'Mikatan',
            'PMO',
            'PSDM',
            'Sosial'
        ];

        foreach ($bidang as $bid) {
            Bidang::firstOrCreate(['nama_bidang' => $bid]);
        }
    }
}
