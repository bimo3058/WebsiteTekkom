<?php

namespace Modules\Capstone\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CapstoneExpoRegistration extends Model
{
    protected $table = 'capstone_expo_registrations';

    protected $fillable = [
        'expo_event_id',
        'group_id',
        'status',
        'registered_at',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
    ];

    const STATUS_REGISTERED = 'REGISTERED';
    const STATUS_CANCELLED  = 'CANCELLED';

    public function expoEvent(): BelongsTo
    {
        return $this->belongsTo(CapstoneExpoEvent::class, 'expo_event_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(CapstoneGroup::class, 'group_id');
    }
}