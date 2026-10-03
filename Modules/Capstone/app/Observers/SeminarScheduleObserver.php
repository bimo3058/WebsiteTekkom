<?php

namespace Modules\Capstone\Observers;

use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Services\EofficeBookingService;

class SeminarScheduleObserver
{
    public function created(SeminarSchedule $schedule): void
    {
        app(EofficeBookingService::class)->syncForSeminar($schedule);
    }

    public function updated(SeminarSchedule $schedule): void
    {
        // Always sync: cheap no-op when nothing relevant changed or when
        // the EOffice tables / link columns are absent.
        app(EofficeBookingService::class)->syncForSeminar($schedule);
    }

    public function deleted(SeminarSchedule $schedule): void
    {
        app(EofficeBookingService::class)->releaseForSeminar($schedule);
    }
}
