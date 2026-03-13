<?php

namespace Modules\Capstone\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CapstoneExpoEvent extends Model
{
    protected $table = 'capstone_expo_events';

    protected $fillable = [
        'period_id',
        'name',
        'date',
        'start_time',
        'end_time',
        'room',
        'capacity',
        'is_published',
        'created_by',
    ];

    protected $casts = [
        'date'         => 'date',
        'is_published' => 'boolean',
    ];

    public function period(): BelongsTo
    {
        return $this->belongsTo(CapstonePeriod::class, 'period_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(CapstoneExpoRegistration::class, 'expo_event_id');
    }
}