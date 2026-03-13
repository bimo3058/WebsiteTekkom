<?php

namespace Modules\Capstone\Models;

use App\Models\Lecturer;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CapstoneSeminarSchedule extends Model
{
    use SoftDeletes;

    protected $table = 'capstone_seminar_schedules';

    protected $fillable = [
        'group_id',
        'type',
        'date',
        'start_time',
        'end_time',
        'room',
        'status',
        'examiner_1_id',
        'examiner_2_id',
        'requested_by',
        'rejection_reason',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    const TYPE_SEMPRO = 'SEMPRO';
    const TYPE_EXPO   = 'EXPO';

    const STATUS_PENDING_APPROVAL = 'PENDING_APPROVAL';
    const STATUS_SCHEDULED        = 'SCHEDULED';
    const STATUS_COMPLETED        = 'COMPLETED';
    const STATUS_CANCELLED        = 'CANCELLED';

    public function group(): BelongsTo
    {
        return $this->belongsTo(CapstoneGroup::class, 'group_id');
    }

    public function examiner1(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'examiner_1_id');
    }

    public function examiner2(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'examiner_2_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(CapstoneSeminarEvaluation::class, 'schedule_id');
    }
}