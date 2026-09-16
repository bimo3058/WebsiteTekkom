<?php

namespace Modules\BankSoal\Console\Commands;

use Illuminate\Console\Command;
use Modules\BankSoal\Services\BlindReviewService;

class AutoApproveBlindReview extends Command
{
    protected $signature   = 'banksoal:auto-approve-blind-review';
    protected $description = 'Auto-approve blind review items yang sudah melewati deadline 2 hari.';

    public function handle(BlindReviewService $service): int
    {
        $count = $service->autoApproveExpiredRounds();
        $this->info("Auto-approved {$count} blind review round(s).");
        return Command::SUCCESS;
    }
}
