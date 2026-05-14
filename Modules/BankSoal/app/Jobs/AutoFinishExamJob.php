<?php

namespace Modules\BankSoal\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\BankSoal\Enums\KompreSessionStatus;
use Modules\BankSoal\Models\Komprehensif\KompreSession;
use Modules\BankSoal\Services\CbtSessionService;

/**
 * Job untuk menyelesaikan sesi ujian secara otomatis dari server.
 *
 * Dipakai sebagai perlindungan server-side terhadap manipulasi timer client.
 * Di-dispatch saat sesi dimulai dengan delay = (waktu mulai + durasi + 30 detik buffer).
 *
 * CATATAN: Job ini hanya berfungsi jika queue driver BUKAN 'sync'.
 * Konfigurasikan QUEUE_CONNECTION=database atau redis di .env.
 */
class AutoFinishExamJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Maksimum percobaan ulang jika job gagal. */
    public int $tries = 3;

    /** Timeout eksekusi job dalam detik. */
    public int $timeout = 30;

    public function __construct(private readonly int $sessionId) {}

    public function handle(CbtSessionService $cbtService): void
    {
        $session = KompreSession::find($this->sessionId);

        // Guard: sesi mungkin sudah di-finish via submit manual atau admin force-submit.
        if (! $session || $session->status !== KompreSessionStatus::Ongoing) {
            return;
        }

        $cbtService->finishSession($session);
    }
}
