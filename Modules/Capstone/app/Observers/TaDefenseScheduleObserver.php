<?php

namespace Modules\Capstone\Observers;

use Modules\Capstone\Models\TaDefenseSchedule;
use Modules\Capstone\Services\EofficeBookingService;

class TaDefenseScheduleObserver
{
    public function created(TaDefenseSchedule $schedule): void
    {
        app(EofficeBookingService::class)->syncForDefense($schedule);
    }

    public function updated(TaDefenseSchedule $schedule): void
    {
        // Always sync: cheap no-op when nothing relevant changed or when
        // the EOffice tables / link columns are absent.
        app(EofficeBookingService::class)->syncForDefense($schedule);
    }

    public function deleted(TaDefenseSchedule $schedule): void
    {
        app(EofficeBookingService::class)->releaseForDefense($schedule);
    }
}
