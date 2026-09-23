<?php

namespace Modules\EOffice\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;
use Modules\EOffice\Models\Peminjaman;
use Modules\EOffice\Notifications\PeminjamanStatusUpdated;

class PrunePeminjamanNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eoffice:prune-peminjaman-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus notifikasi peminjaman ruangan yang acaranya sudah lewat dari 7 hari';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 7 Hari sejak tanggal peminjaman terakhir (grace period)
        $sevenDaysAgo = Carbon::now()->subDays(7)->toDateString();

        // Kumpulkan semua ID Peminjaman yang kadaluwarsa (<= $sevenDaysAgo)
        $expiredPeminjamanIds = Peminjaman::where('tanggal_pinjam', '<=', $sevenDaysAgo)
            ->pluck('id')
            ->toArray();

        if (empty($expiredPeminjamanIds)) {
            $this->info('Tidak ada notifikasi peminjaman usang yang perlu dihapus.');
            return;
        }

        $count = 0;

        // Chunk notifikasi agar RAM tetap aman meskipun ada ribuan data
        DatabaseNotification::where('type', PeminjamanStatusUpdated::class)
            ->chunkById(200, function ($notifications) use ($expiredPeminjamanIds, &$count) {
                $idsToDelete = [];

                foreach ($notifications as $notification) {
                    $peminjamanId = $notification->data['peminjaman_id'] ?? null;

                    // Jika notifikasi ini terkait dengan Peminjaman yang sudah expired
                    if ($peminjamanId && in_array($peminjamanId, $expiredPeminjamanIds)) {
                        $idsToDelete[] = $notification->id;
                    }
                }

                // Hapus secara massal (bulk delete) untuk chunk ini
                if (!empty($idsToDelete)) {
                    DatabaseNotification::whereIn('id', $idsToDelete)->delete();
                    $count += count($idsToDelete);
                }
            });

        $this->info("Berhasil menghapus {$count} baris notifikasi peminjaman yang sudah melewati batas 7 hari pasca-acara.");
    }
}
