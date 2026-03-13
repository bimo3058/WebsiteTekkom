<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateBankSoal extends Command
{
    protected $signature = 'generate:banksoal-content';
    protected $description = 'Mengisi boilerplate untuk Model dan Service Bank Soal';

    public function handle()
    {
        $modulePath = base_path('Modules/BankSoal');

        // 1. DATA MODELS (Mapping Model Name => Table Name)
        $models = [
            'MataKuliah'     => 'bs_mata_kuliah',
            'Cpl'            => 'bs_cpl',
            'DosenPengampuMk' => 'bs_dosen_pengampu_mk',
            'Pertanyaan'     => 'bs_pertanyaan',
            'Jawaban'        => 'bs_jawaban',
            'Rps'            => 'bs_rps',
            'RpsDetail'      => 'bs_rps_detail',
            'Parameter'      => 'bs_parameter',
            'HasilReviewRps' => 'bs_hasil_review_rps',
            'KompreSession'  => 'bs_kompre_session',
            'KompreJawaban'  => 'bs_kompre_jawaban',
        ];

        File::ensureDirectoryExists("{$modulePath}/Models");

        foreach ($models as $name => $table) {
            $filePath = "{$modulePath}/Models/{$name}.php";
            $content  = <<<PHP
<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;

class {$name} extends Model
{
    protected \$table = '{$table}';
    protected \$guarded = ['id'];
}
PHP;
            File::put($filePath, $content);
            $this->info("Model {$name} updated.");
        }

        // 2. DATA SERVICES
        $services = [
            'MataKuliahService',
            'PertanyaanService',
            'RpsService',
            'KompreService',
        ];

        File::ensureDirectoryExists("{$modulePath}/Services");

        foreach ($services as $service) {
            $filePath = "{$modulePath}/Services/{$service}.php";
            $content  = <<<PHP
<?php

namespace Modules\BankSoal\Services;

class {$service}
{
    public function __construct()
    {
        // Logic goes here
    }
}
PHP;
            File::put($filePath, $content);
            $this->info("Service {$service} updated.");
        }

        $this->info('Selesai! Semua file .php telah diisi dengan boilerplate.');
    }
}