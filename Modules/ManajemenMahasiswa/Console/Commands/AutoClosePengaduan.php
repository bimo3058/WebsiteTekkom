<?php

namespace Modules\ManajemenMahasiswa\Console\Commands;

use Illuminate\Console\Command;
use Modules\ManajemenMahasiswa\Services\PengaduanService;

class AutoClosePengaduan extends Command
{
    protected $signature   = 'pengaduan:auto-close';
    protected $description = 'Tutup otomatis tiket pengaduan yang sudah dijawab lebih dari 7 hari tanpa respons mahasiswa.';

    public function __construct(private PengaduanService $pengaduanService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $jumlah = $this->pengaduanService->autoCloseExpired();

        $this->info("[pengaduan:auto-close] {$jumlah} tiket ditutup otomatis.");

        return Command::SUCCESS;
    }
}
