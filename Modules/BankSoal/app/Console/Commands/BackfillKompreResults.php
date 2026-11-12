<?php

namespace Modules\BankSoal\Console\Commands;

use Illuminate\Console\Command;
use Modules\BankSoal\Enums\KompreSessionStatus;
use Modules\BankSoal\Models\Komprehensif\KompreSession;
use Modules\BankSoal\Services\Komprehensif\KompreResultService;

class BackfillKompreResults extends Command
{
    protected $signature = 'kompre:backfill-results
                            {--dry-run : Hitung ulang tanpa menyimpan perubahan}
                            {--chunk=100 : Jumlah sesi per batch}';

    protected $description = 'Hitung ulang status kelulusan per CPL untuk sesi ujian komprehensif yang sudah selesai';

    public function handle(KompreResultService $resultService): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $chunk = max(1, (int) $this->option('chunk'));

        $processed = 0;
        $lulus = 0;
        $tidakLulus = 0;

        $this->info($dryRun
            ? 'Mode dry-run: perubahan tidak disimpan.'
            : 'Menghitung ulang hasil ujian komprehensif...');

        KompreSession::where('status', KompreSessionStatus::Finished)
            ->where('source', 'cbt')
            ->chunkById($chunk, function ($sessions) use ($resultService, $dryRun, &$processed, &$lulus, &$tidakLulus) {
                foreach ($sessions as $session) {
                    $result = $resultService->evaluate($session);

                    if ($result['lulus']) {
                        $lulus++;
                    } else {
                        $tidakLulus++;
                    }

                    if (! $dryRun) {
                        $session->update([
                            'lulus' => $result['lulus'],
                            'cpl_scores' => $result['cpl_scores'],
                        ]);
                    }

                    $processed++;
                }
            });

        $this->table(
            ['Total sesi', 'Lulus', 'Tidak lulus', 'Mode'],
            [[$processed, $lulus, $tidakLulus, $dryRun ? 'dry-run' : 'tersimpan']]
        );

        return self::SUCCESS;
    }
}
