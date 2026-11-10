<?php

namespace Modules\EOffice\Console\Commands;

use Illuminate\Console\Command;
use Modules\EOffice\Models\Peminjaman;

class MrExpireRoomBookingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eoffice:mr-expire-bookings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[Manajemen Ruangan] Mengubah status peminjaman ruangan yang belum disetujui dan melewati batas waktu menjadi ditolak sistem';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Memulai pengecekan peminjaman ruangan kedaluwarsa...');

        Peminjaman::autoExpirePending();

        $this->info('Pengecekan selesai. Peminjaman yang melewati batas waktu telah otomatis ditolak sistem.');

        return Command::SUCCESS;
    }
}

