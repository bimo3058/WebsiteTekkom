<?php

namespace Modules\Capstone\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'is_active'    => 'boolean',
        'is_locked'    => 'boolean',
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

    public function isBiddingOpen(): bool
    {
        $today = now()->toDateString();
        return $this->is_active
            && $this->bidding_start?->lte(now())
            && $this->bidding_end?->gte(now());
    }
}