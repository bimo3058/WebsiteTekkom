<?php

namespace Modules\EOffice\Console\Commands;

use Illuminate\Console\Command;
use Modules\EOffice\Services\PeriodePendaftaranService;

class TutupPeriodePendaftaranKadaluarsa extends Command
{
    protected $signature = 'eoffice:periode-pendaftaran:tutup-kadaluarsa';

    protected $description = 'Menutup periode pendaftaran Manajemen Praktikum yang sudah melewati waktu tutup.';

    public function handle(PeriodePendaftaranService $service): int
    {
        $closed = $service->tutupKadaluarsa();
        $this->info("Periode pendaftaran kadaluarsa ditutup: {$closed->count()}");

        return self::SUCCESS;
    }
}
