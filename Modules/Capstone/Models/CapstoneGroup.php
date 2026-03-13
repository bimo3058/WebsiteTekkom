<?php

namespace Modules\Capstone\Models;

use App\Models\Lecturer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CapstoneGroup extends Model
{
    use SoftDeletes;

    protected $table = 'capstone_groups';

    protected $fillable = [
        'period_id',
        'title_id',
        'status',
        'supervisor_1_id',
        'supervisor_2_id',
    ];

    // Status constants
    const STATUS_FORMING         = 'FORMING';
    const STATUS_BIDDING         = 'BIDDING';
    const STATUS_TITLE_SELECTED  = 'TITLE_SELECTED';
    const STATUS_PDC1_ACTIVE     = 'PDC1_ACTIVE';
    const STATUS_SEM_PRO         = 'SEM_PRO';
    const STATUS_REVISI_PDC1     = 'REVISI_PDC1';
    const STATUS_PDC2_ACTIVE     = 'PDC2_ACTIVE';
    const STATUS_PDC2_COMPLETED  = 'PDC2_COMPLETED';
    const STATUS_EXPO_ELIGIBLE   = 'EXPO_ELIGIBLE';
    const STATUS_EXPO_DONE       = 'EXPO_DONE';
    const STATUS_CLOSED          = 'CLOSED';

    public function period(): BelongsTo
    {
        return $this->belongsTo(CapstonePeriod::class, 'period_id');
    }

    public function title(): BelongsTo
    {
        return $this->belongsTo(CapstoneTitle::class, 'title_id');
    }

    public function supervisor1(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'supervisor_1_id');
    }

    public function supervisor2(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'supervisor_2_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(CapstoneGroupMember::class, 'group_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(CapstoneBid::class, 'group_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CapstoneDocument::class, 'group_id');
    }

    public function seminarSchedules(): HasMany
    {
        return $this->hasMany(CapstoneSeminarSchedule::class, 'group_id');
    }

    public function workflowLogs(): HasMany
    {
        return $this->hasMany(CapstoneGroupWorkflowLog::class, 'group_id');
    }

    public function supervisions(): HasMany
    {
        return $this->hasMany(CapstoneSupervision::class, 'group_id');
    }

    public function expoRegistrations(): HasMany
    {
        return $this->hasMany(CapstoneExpoRegistration::class, 'group_id');
    }
}