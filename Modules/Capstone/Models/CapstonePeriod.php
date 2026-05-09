<?php

namespace Modules\Capstone\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class CapstonePeriod extends Model
{
    use SoftDeletes;

    protected $table = 'capstone_periods';

    protected $fillable = [
        'name',
        'is_active',
        'is_locked',
        'min_group_size',
        'max_group_size',
        'max_supervise_load',
        'bidding_start',
        'bidding_end',
        'pdc1_start',
        'pdc1_end',
        'pdc2_start',
        'pdc2_end',
        'ta_start',
        'ta_end',
        'expo_date',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'is_locked'     => 'boolean',
        'min_group_size' => 'integer',
        'max_group_size' => 'integer',
        'bidding_start' => 'date',
        'bidding_end'   => 'date',
        'pdc1_start'    => 'date',
        'pdc1_end'      => 'date',
        'pdc2_start'    => 'date',
        'pdc2_end'      => 'date',
        'ta_start'      => 'date',
        'ta_end'        => 'date',
        'expo_date'     => 'date',
    ];

    /**
     * Scope untuk mempermudah pengambilan periode aktif
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    // --- Relations ---

    public function groups(): HasMany
    {
        return $this->hasMany(CapstoneGroup::class, 'period_id');
    }

    public function expoEvents(): HasMany
    {
        return $this->hasMany(CapstoneExpoEvent::class, 'period_id');
    }

    public function documentRequirements(): HasMany
    {
        return $this->hasMany(CapstonePhaseDocumentRequirement::class, 'period_id');
    }

    // --- Helpers ---

    /**
     * Mengecek apakah bidding sedang dibuka berdasarkan tanggal hari ini
     */
    public function isBiddingOpen(): bool
    {
        if (!$this->is_active || $this->is_locked) {
            return false;
        }

        $today = today(); // Menggunakan jam 00:00:00

        return $this->bidding_start && $this->bidding_end
            && $today->between($this->bidding_start, $this->bidding_end);
    }
}