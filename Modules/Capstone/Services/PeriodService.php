<?php

namespace Modules\Capstone\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Modules\Capstone\Models\CapstonePeriod;

class PeriodService
{
    /**
     * Ambil period aktif, di-cache 1 jam.
     */
    public function getActivePeriod(): ?CapstonePeriod
    {
        return Cache::remember('capstone:period:active', now()->addHour(), fn() =>
            CapstonePeriod::where('is_active', true)
                ->whereNull('deleted_at')
                ->first()
        );
    }

    /**
     * Semua period, di-cache 30 menit.
     */
    public function getAllPeriods(): Collection
    {
        return Cache::remember('capstone:periods:all', now()->addMinutes(30), fn() =>
            CapstonePeriod::orderByDesc('created_at')
                ->whereNull('deleted_at')
                ->get()
        );
    }

    public function createPeriod(array $data): CapstonePeriod
    {
        // Nonaktifkan semua period lain jika ini di-set aktif
        if (!empty($data['is_active'])) {
            CapstonePeriod::where('is_active', true)->update(['is_active' => false]);
        }

        $period = CapstonePeriod::create($data);
        $this->clearCache();

        return $period;
    }

    public function updatePeriod(CapstonePeriod $period, array $data): CapstonePeriod
    {
        if (!empty($data['is_active'])) {
            CapstonePeriod::where('is_active', true)
                ->where('id', '!=', $period->id)
                ->update(['is_active' => false]);
        }

        $period->update($data);
        $this->clearCache();

        return $period->fresh();
    }

    public function setActive(CapstonePeriod $period): void
    {
        CapstonePeriod::where('is_active', true)->update(['is_active' => false]);
        $period->update(['is_active' => true]);
        $this->clearCache();
    }

    public function clearCache(): void
    {
        Cache::forget('capstone:period:active');
        Cache::forget('capstone:periods:all');
    }
}