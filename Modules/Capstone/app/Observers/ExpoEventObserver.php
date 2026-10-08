<?php

namespace Modules\Capstone\Observers;

use Modules\Capstone\Models\ExpoEvent;
use Modules\Capstone\Services\EofficeBookingService;

class ExpoEventObserver
{
    public function created(ExpoEvent $event): void
    {
        app(EofficeBookingService::class)->syncForExpo($event);
    }

    public function updated(ExpoEvent $event): void
    {
        app(EofficeBookingService::class)->syncForExpo($event);
    }

    public function deleted(ExpoEvent $event): void
    {
        app(EofficeBookingService::class)->releaseForExpo($event);
    }

    public function forceDeleted(ExpoEvent $event): void
    {
        app(EofficeBookingService::class)->releaseForExpo($event);
    }
}
