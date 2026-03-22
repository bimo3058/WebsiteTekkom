<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PopulateMahasiswaFiles extends Command
{
    protected $signature = 'generate:mahasiswa-content';
    protected $description = 'Mengisi boilerplate untuk Model dan Service Manajemen Mahasiswa';

    public function handle()
    {
        $modulePath = base_path('Modules/ManajemenMahasiswa');

        // 1. DATA MODELS (Mapping Model Name => Table Name)
        $models = [
            'Kepengurusan' => 'mk_kepengurusan',
            'Bidang' => 'mk_bidang',
            'KategoriKegiatan' => 'mk_kategori_kegiatan',
            'PengurusHimaskom' => 'mk_pengurus_himaskom',
            'Kegiatan' => 'mk_kegiatan',
            'RiwayatKegiatan' => 'mk_riwayat_kegiatan',
            'Pengumuman' => 'mk_pengumuman',
            'Kemahasiswaan' => 'mk_kemahasiswaan',
            'Prestasi' => 'mk_prestasi',
            'Alumni' => 'mk_alumni',
            'RepoMulmed' => 'mk_repo_mulmed',
            'DashboardAnalitik' => 'mk_dashboard_analitik',
            'ForumMahasiswa' => 'mk_forum_mahasiswa',
            'Discussion' => 'mk_discussion',
            'CommentForum' => 'mk_comment_forum',
        ];

        foreach ($models as $name => $table) {
            $filePath = "{$modulePath}/Models/{$name}.php";
            $content = "<?php\n\nnamespace Modules\ManajemenMahasiswa\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass {$name} extends Model\n{\n    protected \$table = '{$table}';\n    protected \$guarded = ['id'];\n}\n";
            File::put($filePath, $content);
            $this->info("Model {$name} updated.");
        }

        // 2. DATA SERVICES
        $services = [
            'KemahasiswaanService', 'AlumniService', 'KegiatanService', 
            'PengumumanService', 'ForumService', 'RepoMulmedService', 
            'PengurusHimaskomService', 'DashboardAnalitikService'
        ];

        foreach ($services as $service) {
            $filePath = "{$modulePath}/Services/{$service}.php";
            $content = "<?php\n\nnamespace Modules\ManajemenMahasiswa\Services;\n\nclass {$service}\n{\n    public function __construct()\n    {\n        // Logic goes here\n    }\n}\n";
            File::put($filePath, $content);
            $this->info("Service {$service} updated.");
        }

        $this->info("Selesai! Semua file .php telah diisi dengan boilerplate.");
    }
}