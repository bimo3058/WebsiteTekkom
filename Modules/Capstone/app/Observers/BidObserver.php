<?php

namespace Modules\Capstone\Observers;

use Modules\Capstone\Models\Bid;

class BidObserver
{
    public function saved(Bid $bid): void
    {
        $bid->group->refreshReadinessSnapshot();
    }

    public function deleted(Bid $bid): void
    {
        $bid->group->refreshReadinessSnapshot();
    }
}
