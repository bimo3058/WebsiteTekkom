<?php

namespace Modules\Capstone\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Modules\Capstone\Models\CapstonePeriod;

class PeriodService
{
    /**
     * Ambil period aktif, di-cache 1 jam.
     * Menggunakan boolean murni agar kompatibel dengan PostgreSQL.
     */
    public function getActivePeriod(): ?CapstonePeriod
    {
        return Cache::remember('capstone:period:active', now()->addHour(), fn() =>
            CapstonePeriod::whereRaw('is_active = true')
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

    /**
     * Membuat period baru dan memastikan hanya satu yang aktif.
     */
    public function createPeriod(array $data): CapstonePeriod
    {
        // Gunakan boolean murni false untuk update massal (Postgres Strict)
        if (!empty($data['is_active'])) {
            CapstonePeriod::where('is_active', true)->update(['is_active' => false]);
        }

        $period = CapstonePeriod::create($data);
        $this->clearCache();

        return $period;
    }

    /**
     * Update period dan reset status aktif jika diperlukan.
     */
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

    /**
     * Set satu period menjadi aktif dan sisanya non-aktif.
     */
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