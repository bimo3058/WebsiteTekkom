<?php

namespace Modules\Capstone\Concerns;

use Illuminate\Database\Eloquent\Model;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Support\PeriodGate;

trait RequiresActivePeriod
{
    protected function ensurePeriodIsActive(Group $group): void
    {
        $period = $group->relationLoaded('period') ? $group->period : Period::find($group->period_id);

        PeriodGate::ensureActive($period);
    }

    protected function ensurePeriodActiveById(int $periodId): void
    {
        PeriodGate::ensureActive(Period::find($periodId));
    }

    protected function ensureModelPeriodActive(Model $model): void
    {
        $periodRel = $model->relationLoaded('period') ? $model->period : null;

        if ($periodRel) {
            PeriodGate::ensureActive($periodRel);

            return;
        }

        if ($model->period_id) {
            PeriodGate::ensureActive(Period::find($model->period_id));
        }
    }
}
