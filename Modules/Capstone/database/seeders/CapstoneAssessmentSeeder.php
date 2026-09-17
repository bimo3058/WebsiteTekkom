<?php

namespace Modules\Capstone\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CapstoneAssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            ['id' => 6, 'code' => 'CPL-2', 'name' => 'dua', 'description' => 'tes', 'weight' => '25.00', 'is_active' => true, 'created_by' => 80, 'sort_order' => 0, 'created_at' => '2026-04-21 11:27:21', 'updated_at' => '2026-04-21 11:27:21'],
            ['id' => 7, 'code' => 'CPL-3', 'name' => 'tiga', 'description' => 'tes', 'weight' => '25.00', 'is_active' => true, 'created_by' => 80, 'sort_order' => 0, 'created_at' => '2026-04-21 11:27:35', 'updated_at' => '2026-04-21 11:27:35'],
            ['id' => 8, 'code' => 'CPL-4', 'name' => 'empat', 'description' => 'tes', 'weight' => '30.00', 'is_active' => true, 'created_by' => 80, 'sort_order' => 0, 'created_at' => '2026-04-21 11:27:48', 'updated_at' => '2026-04-21 11:27:48'],
            ['id' => 9, 'code' => 'MIL-1', 'name' => 'satu', 'description' => 'tes', 'weight' => '50.00', 'is_active' => true, 'created_by' => 80, 'sort_order' => 0, 'created_at' => '2026-04-22 08:01:55', 'updated_at' => '2026-04-22 08:01:55'],
            ['id' => 10, 'code' => 'MIL-2', 'name' => 'dua', 'description' => 'tes', 'weight' => '50.00', 'is_active' => true, 'created_by' => 80, 'sort_order' => 0, 'created_at' => '2026-04-22 08:02:06', 'updated_at' => '2026-04-22 08:02:06'],
            ['id' => 11, 'code' => 'EXPO-1', 'name' => 'satu', 'description' => 'tes', 'weight' => '50.00', 'is_active' => true, 'created_by' => 80, 'sort_order' => 0, 'created_at' => '2026-04-22 08:02:50', 'updated_at' => '2026-04-22 08:02:50'],
            ['id' => 12, 'code' => 'EXPO-2', 'name' => 'dua', 'description' => 'tes', 'weight' => '50.00', 'is_active' => true, 'created_by' => 80, 'sort_order' => 0, 'created_at' => '2026-04-22 08:03:07', 'updated_at' => '2026-04-22 08:03:07'],
            ['id' => 13, 'code' => 'PR-1', 'name' => 'Peer Reviews 1', 'description' => 'tes', 'weight' => '50.00', 'is_active' => true, 'created_by' => 80, 'sort_order' => 0, 'created_at' => '2026-04-28 15:01:23', 'updated_at' => '2026-04-28 15:01:23'],
            ['id' => 14, 'code' => 'PR-2', 'name' => 'Peer Review 2', 'description' => 'tes', 'weight' => '50.00', 'is_active' => true, 'created_by' => 80, 'sort_order' => 0, 'created_at' => '2026-04-28 15:01:44', 'updated_at' => '2026-04-28 15:01:44'],
            ['id' => 15, 'code' => 'MILESTONE-1', 'name' => 'Kemampuan Perencanaan Proyek', 'description' => 'Mahasiswa mampu menyusun perencanaan proyek yang jelas, terstruktur, dan sesuai dengan target capaian', 'weight' => '33.33', 'is_active' => true, 'created_by' => null, 'sort_order' => 1, 'created_at' => '2026-04-28 15:28:48', 'updated_at' => '2026-04-28 15:28:48'],
            ['id' => 16, 'code' => 'MILESTONE-2', 'name' => 'Pengelolaan Waktu dan Dana', 'description' => 'Mahasiswa mampu mengelola proyek sesuai dengan timeline yang telah ditentukan dan menggunakan dana secara efisien', 'weight' => '33.33', 'is_active' => true, 'created_by' => null, 'sort_order' => 2, 'created_at' => '2026-04-28 15:28:48', 'updated_at' => '2026-04-28 15:28:48'],
            ['id' => 17, 'code' => 'MILESTONE-3', 'name' => 'Identifikasi Kebutuhan', 'description' => 'Mahasiswa mampu mengidentifikasi kebutuhan proyek dengan tepat, termasuk resource, stakeholder, dan risiko', 'weight' => '33.34', 'is_active' => true, 'created_by' => null, 'sort_order' => 3, 'created_at' => '2026-04-28 15:28:48', 'updated_at' => '2026-04-28 15:28:48'],
            ['id' => 22, 'code' => 'CPMK-6', 'name' => 'Kemampuan Berfikir', 'description' => 'testing', 'weight' => '25.00', 'is_active' => true, 'created_by' => 80, 'sort_order' => 0, 'created_at' => '2026-05-08 04:06:01', 'updated_at' => '2026-05-08 04:06:01'],
            ['id' => 23, 'code' => 'CPMK 1', 'name' => 'CPMK 1', 'description' => null, 'weight' => '15.00', 'is_active' => true, 'created_by' => 79, 'sort_order' => 0, 'created_at' => '2026-06-16 13:44:12', 'updated_at' => '2026-06-16 13:44:12'],
            ['id' => 28, 'code' => 'CPMK-5', 'name' => 'CPMK 5', 'description' => 'Kemampuan mahasiswa dalam memahami dan mengikuti perkembangan teknologi (C100, C200, С300).', 'weight' => '20.00', 'is_active' => true, 'created_by' => null, 'sort_order' => 5, 'created_at' => '2026-06-16 14:01:30', 'updated_at' => '2026-06-16 14:01:30'],
            ['id' => 1, 'code' => 'CPMK-1', 'name' => 'CPMK 1', 'description' => 'Kemampuan mahasiswa dalam menerapkan pengetahuan matematis atau ilmu alam dalamperancangan dan pengembangan solusi proyek ketika menganalisis dan menyelesaikan masalah kompleks di dunia nyata (C100).', 'weight' => '15.00', 'is_active' => true, 'created_by' => 80, 'sort_order' => 1, 'created_at' => '2026-04-21 10:18:25', 'updated_at' => '2026-06-16 14:03:58'],
            ['id' => 2, 'code' => 'CPMK-2', 'name' => 'CPMK 2', 'description' => 'Kemampuan mahasiswa dalam melakukan perancangan dan pengembangan komponen, sistem, atau proses berdasarkan aspek ekonomi, manufakturabilitas, sustainabilitas, atau aspek lainnyaseperti aspek lingkungan dan legal (C100, C200, C300).', 'weight' => '25.00', 'is_active' => true, 'created_by' => 80, 'sort_order' => 2, 'created_at' => '2026-04-21 10:18:41', 'updated_at' => '2026-06-16 14:04:02'],
            ['id' => 3, 'code' => 'CPMK-3', 'name' => 'CPMK 3', 'description' => 'Kemampuan mahasiswa dalam melakukan riset dalam proses perancangan desain (C100, C200, C300).', 'weight' => '25.00', 'is_active' => true, 'created_by' => 80, 'sort_order' => 3, 'created_at' => '2026-04-21 10:19:33', 'updated_at' => '2026-06-16 14:04:06'],
            ['id' => 4, 'code' => 'CPMK-4', 'name' => 'CPMK 4', 'description' => 'Kemampuan mahasiswa dalam melakukan identifikasi, perumusan, dan analisis permasalahan kompleks. (C100).', 'weight' => '15.00', 'is_active' => true, 'created_by' => 80, 'sort_order' => 4, 'created_at' => '2026-04-21 10:20:01', 'updated_at' => '2026-06-16 14:04:11'],
            ['id' => 29, 'code' => '050505', 'name' => '050505', 'description' => 'ini buat tes', 'weight' => '100.00', 'is_active' => false, 'created_by' => 80, 'sort_order' => 0, 'created_at' => '2026-06-18 20:13:06', 'updated_at' => '2026-06-18 20:13:15'],
            ['id' => 5, 'code' => 'CPL-1', 'name' => 'satu', 'description' => 'tes', 'weight' => '20.00', 'is_active' => false, 'created_by' => 80, 'sort_order' => 0, 'created_at' => '2026-04-21 11:27:08', 'updated_at' => '2026-06-18 23:08:38'],
        ];

        $components = [
            [13, 'BIMBINGAN_SEMPRO', 'CPL-1', 'satu', 'tes', '20.00', 0, '2026-04-21 11:28:14'], [14, 'BIMBINGAN_SEMPRO', 'CPL-2', 'dua', 'tes', '25.00', 1, '2026-04-21 11:28:14'], [15, 'BIMBINGAN_SEMPRO', 'CPL-3', 'tiga', 'tes', '25.00', 2, '2026-04-21 11:28:14'], [16, 'BIMBINGAN_SEMPRO', 'CPL-4', 'empat', 'tes', '30.00', 3, '2026-04-21 11:28:14'],
            [17, 'BIMBINGAN_EXPO', 'EXPO-1', 'satu', 'tes', '50.00', 0, '2026-04-22 08:03:46'], [18, 'BIMBINGAN_EXPO', 'EXPO-2', 'dua', 'tes', '50.00', 1, '2026-04-22 08:03:46'], [21, 'EXPO', 'EXPO-1', 'satu', 'tes', '50.00', 0, '2026-04-22 08:07:24'], [22, 'EXPO', 'EXPO-2', 'dua', 'tes', '50.00', 1, '2026-04-22 08:07:24'],
            [27, 'SEMPRO', 'CPMK-1', 'Satu', 'tes', '20.00', 0, '2026-04-22 19:06:42'], [28, 'SEMPRO', 'CPMK-2', 'Dua', 'tes', '25.00', 1, '2026-04-22 19:06:42'], [29, 'SEMPRO', 'CPMK-3', 'Tiga', 'tes', '25.00', 2, '2026-04-22 19:06:42'], [30, 'SEMPRO', 'CPMK-4', 'Empat', 'tes', '30.00', 3, '2026-04-22 19:06:42'],
            [31, 'BIMBINGAN_PDC1', 'CPMK-1', 'Satu', 'tes', '20.00', 0, '2026-04-22 19:06:52'], [32, 'BIMBINGAN_PDC1', 'CPMK-2', 'Dua', 'tes', '25.00', 1, '2026-04-22 19:06:52'], [33, 'BIMBINGAN_PDC1', 'CPMK-3', 'Tiga', 'tes', '25.00', 2, '2026-04-22 19:06:52'], [34, 'BIMBINGAN_PDC1', 'CPMK-4', 'Empat', 'tes', '30.00', 3, '2026-04-22 19:06:52'],
            [35, 'NILAI_DOSEN', 'CPMK-1', 'Satu', 'tes', '20.00', 0, '2026-04-23 00:19:46'], [36, 'NILAI_DOSEN', 'CPMK-2', 'Dua', 'tes', '25.00', 1, '2026-04-23 00:19:46'], [37, 'NILAI_DOSEN', 'CPMK-3', 'Tiga', 'tes', '25.00', 2, '2026-04-23 00:19:46'], [38, 'NILAI_DOSEN', 'CPMK-4', 'Empat', 'tes', '30.00', 3, '2026-04-23 00:19:46'],
            [39, 'MILESTONE', 'MIL-1', 'satu', 'tes', '50.00', 0, '2026-04-24 10:10:27'], [40, 'MILESTONE', 'MIL-2', 'dua', 'tes', '50.00', 1, '2026-04-24 10:10:27'],
        ];

        DB::transaction(function () use ($templates, $components) {
            DB::table('capstone_assessment_component_templates')->upsert(
                $templates,
                ['id'],
                ['code', 'name', 'description', 'weight', 'is_active', 'created_by', 'sort_order', 'created_at', 'updated_at'],
            );

            // $componentRows = array_map(fn (array $row) => [
            //     'id' => $row[0], 'period_id' => 6, 'type' => $row[1], 'code' => $row[2],
            //     'name' => $row[3], 'description' => $row[4], 'weight' => $row[5],
            //     'sort_order' => $row[6], 'created_at' => $row[7], 'updated_at' => $row[7],
            // ], $components);

            // DB::table('capstone_assessment_components')->upsert(
            //     $componentRows,
            //     ['id'],
            //     ['period_id', 'type', 'code', 'name', 'description', 'weight', 'sort_order', 'created_at', 'updated_at'],
            // );
        });
    }
}
