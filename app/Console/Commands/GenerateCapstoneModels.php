<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class GenerateCapstoneModels extends Command
{
    // Nama command yang akan dipanggil di terminal
    protected $signature = 'generate:capstone-models';
    protected $description = 'Generate semua model Capstone sekaligus';

    public function handle()
    {
        $models = [
            'CapstonePeriod', 'CapstoneGroup', 'CapstoneTitle', 
            'CapstoneGroupMember', 'CapstoneBid', 'CapstoneSupervision', 
            'CapstoneDocument', 'CapstoneSeminarSchedule', 'CapstoneSeminarEvaluation', 
            'CapstoneExpoEvent', 'CapstoneExpoRegistration', 'CapstoneGroupWorkflowLog', 
            'CapstoneNotification', 'CapstonePhaseDocumentRequirement'
        ];

        foreach ($models as $model) {
            $this->info("Sedang membuat model: {$model}...");
            
            // Generate model di dalam folder Modules/Capstone/Models
            Artisan::call('make:model', [
                'name' => "Modules\Capstone\Models\\{$model}",
                '-m' => true, // Otomatis buat file Migration
                // '-f' => true, // Tambahkan ini jika ingin sekalian buat Factory
            ]);
        }

        $this->info('Selesai! Semua model dan migration berhasil dibuat.');
    }
}