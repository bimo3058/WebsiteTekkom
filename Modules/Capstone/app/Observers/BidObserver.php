<?php

namespace Modules\Capstone\Observers;

use Modules\Capstone\Jobs\RefreshGroupReadiness;
use Modules\Capstone\Models\Bid;

class BidObserver
{
    public function saved(Bid $bid): void
    {
        RefreshGroupReadiness::dispatch($bid->group_id);
    }

    public function deleted(Bid $bid): void
    {
        RefreshGroupReadiness::dispatch($bid->group_id);
    }
}
