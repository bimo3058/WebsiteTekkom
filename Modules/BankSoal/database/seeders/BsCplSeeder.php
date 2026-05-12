<?php

namespace Modules\BankSoal\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BsCplSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bs_cpl')->insert([
            [
                'id' => 1,
                'kode' => 'CPL-001',
                'deskripsi' => 'Mampu menerapkan pengetahuan matematis, ilmu alam, dan teknologi informasi untuk memperoleh pemahaman komprehensif dalam prinsip rekayasa komputer.',
                'created_at' => '2026-03-13 13:12:48',
                'updated_at' => '2026-03-13 13:12:48',
            ],
            [
                'id' => 2,
                'kode' => 'CPL-002',
                'deskripsi' => 'Mampu melakukan perancangan komponen, sistem dan proses untuk memenuhi kebutuhan dalam batasan ekonomi, lingkungan, sosial, kesehatan, keselamatan dan keberlanjutan serta mampu memanfaatkan potensi sumber daya lokal maupun nasional dalam perspektif global.',
                'created_at' => '2026-03-13 12:12:48',
                'updated_at' => '2026-03-13 12:12:48',
            ],
            [
                'id' => 3,
                'kode' => 'CPL-003',
                'deskripsi' => 'Mampu untuk mendesain dan melakukan riset baik di laboratorium maupun di lapangan termasuk menganalisis dan menginterpretasi data untuk memperkuat penilaian dalam proses rekayasa.',
                'created_at' => '2026-03-13 11:12:48',
                'updated_at' => '2026-03-13 11:12:48',
            ],
            [
                'id' => 4,
                'kode' => 'CPL-004',
                'deskripsi' => 'Mampu untuk mengidentifikasi, merumuskan, menganalisis, dan menyelesaikan permasalahan di bidang teknik komputer.',
                'created_at' => '2026-03-13 10:12:48',
                'updated_at' => '2026-03-13 10:12:48',
            ],
            [
                'id' => 5,
                'kode' => 'CPL-005',
                'deskripsi' => 'Memiliki keterampilan yang baik dalam menerapkan metode dan menggunakan peralatan yang dibutuhkan untuk melakukan rekayasa di bidang teknik komputer.',
                'created_at' => '2026-03-13 09:12:48',
                'updated_at' => '2026-03-13 09:12:48',
            ],
            [
                'id' => 6,
                'kode' => 'CPL-006',
                'deskripsi' => 'Mampu menyusun ide, hasil pemikiran dan argumen saintifik sesuai kaidah akademik, serta mengkomunikasikan kepada masyarakat akademik dan masyarakat luas.',
                'created_at' => '2026-03-13 08:12:48',
                'updated_at' => '2026-03-13 08:12:48',
            ],
            [
                'id' => 7,
                'kode' => 'CPL-007',
                'deskripsi' => 'Memiliki kemampuan dalam melakukan perencanaan, penyelesaian dan evaluasi tugas dalam suatu pekerjaan dengan memperhatikan batasan-batasan yang ada.',
                'created_at' => '2026-03-13 07:12:48',
                'updated_at' => '2026-03-13 07:12:48',
            ],
            [
                'id' => 8,
                'kode' => 'CPL-008',
                'deskripsi' => 'Mampu bekerjasama secara efektif baik sebagai anggota maupun pemimpin dalam tim kerja lintas disiplin ilmu dan budaya.',
                'created_at' => '2026-03-13 06:12:48',
                'updated_at' => '2026-03-13 06:12:48',
            ],
            [
                'id' => 9,
                'kode' => 'CPL-009',
                'deskripsi' => 'Mampu memahami kebutuhan akan pembelajaran sepanjang hayat termasuk beradaptasi terhadap perkembangan dan inovasi baru teknologi informasi yang cepat serta mampu menjadi agen perubahan serta memiliki rasa tangggung jawab dan etika profesi yang baik.',
                'created_at' => '2026-03-13 05:12:48',
                'updated_at' => '2026-03-13 05:12:48',
            ],
            [
                'id' => 10,
                'kode' => 'CPL-010',
                'deskripsi' => 'Mampu mengidentifikasi kebutuhan untuk menjadi seorang wirausaha di bidang komputer.',
                'created_at' => '2026-03-13 04:12:48',
                'updated_at' => '2026-03-13 04:12:48',
            ],
        ]);
    }
}
