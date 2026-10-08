<?php

namespace Modules\Capstone\Models;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodRegistration extends Model
{
    protected $table = 'capstone_period_registrations';
    use HasFactory;

    public const STATUS_PENDING = 'PENDING';

    public const STATUS_APPROVED = 'APPROVED';

    public const STATUS_REJECTED = 'REJECTED';

    public const STATUS_FLAGGED = 'FLAGGED';

    protected $fillable = [
        'user_id',
        'period_id',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'flagged_at',
        'flagged_by',
    ];

    protected $casts = [
        'flagged_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(Student::class, 'user_id');
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    /**
     * User who flagged this registration.
     */
    public function flaggedBy()
    {
        return $this->belongsTo(User::class, 'flagged_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope to get approved registrations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to get flagged registrations.
     */
    public function scopeFlagged($query)
    {
        return $query->where('status', self::STATUS_FLAGGED);
    }

    public function isPending(): bool
    {
        return strtoupper((string) $this->status) === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return strtoupper((string) $this->status) === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return strtoupper((string) $this->status) === self::STATUS_REJECTED;
    }
}
