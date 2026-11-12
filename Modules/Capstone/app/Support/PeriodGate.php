<?php

namespace Modules\Capstone\Support;

use Illuminate\Support\Facades\Cache;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\Period;

final class PeriodGate
{
    public static function reason(?Period $period): ?string
    {
        if (! $period) {
            return 'NO_ACTIVE_PERIOD';
        }

        $attributes = $period->getAttributes();
        $active = array_key_exists('is_active', $attributes) ? (bool) $period->getAttribute('is_active') : true;

        if (! $active) {
            return 'NO_ACTIVE_PERIOD';
        }

        $finalized = array_key_exists('is_finalized', $attributes) ? (bool) $period->getAttribute('is_finalized') : false;

        if ($finalized) {
            return 'PERIOD_FINALIZED';
        }

        return null;
    }

    public static function ensureActive(?Period $period): void
    {
        if (self::reason($period) !== null) {
            abort(403, 'Periode tidak aktif. Operasi tidak diizinkan.');
        }
    }

    public static function lockedPeriod(int|string $periodId): Period
    {
        $period = Period::whereKey($periodId)->lockForUpdate()->firstOrFail();
        abort_if($period->is_finalized, 403, 'Periode final tidak dapat diubah.');

        return $period;
    }

    public static function assertNoGroups(int|string $periodId, string $action): void
    {
        abort_if(
            Group::where('period_id', $periodId)->exists(),
            403,
            "Konfigurasi evaluasi tidak dapat {$action} setelah kelompok terbentuk."
        );
    }

    /**
     * @param  array<int>  $current
     * @param  array<int|string>  $requested
     */
    public static function assertNoGroupsIfChanged(int|string $periodId, array $current, array $requested, string $action): void
    {
        if ($current !== array_map('intval', $requested)) {
            self::assertNoGroups($periodId, $action);
        }
    }

    /**
     * @return array<int>
     */
    public static function activeAndFinalizedIds(): array
    {
        return Cache::remember('periods:active_and_finalized_ids', now()->addMinutes(5), function () {
            return Period::where('is_active', true)
                ->orWhere('is_finalized', true)
                ->pluck('id')
                ->toArray();
        });
    }
}
