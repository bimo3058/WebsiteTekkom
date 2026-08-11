<?php

namespace Modules\Capstone\Models;

use App\Models\Lecturer;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'capstone_groups';

    // WARNING: Do not mutate title_id or assignment_type directly.
    // They are intentionally excluded from $fillable.
    // Use assignTitleFromFinalization() and assignTypeFromFinalization() only.
    // These methods are called exclusively by FinalizationService.
    protected $fillable = [
        'period_id', 'status', 'supervisor_1_id', 'supervisor_2_id',
        'group_mode', 'has_existing_group', 'code', 'is_solo',
        'has_active_proposal', 'readiness_status', 'finalization_notes',
        'finalized_at', 'finalized_by',
    ];

    /**
     * Assign title_id — ONLY callable from FinalizationService.
     * Direct $group->title_id = X or $group->update(['title_id' => X]) is blocked
     * because title_id is not in $fillable.
     */
    public function assignTitleFromFinalization(int $titleId): void
    {
        $this->attributes['title_id'] = $titleId;
    }

    /**
     * Assign assignment_type — ONLY callable from FinalizationService.
     */
    public function assignTypeFromFinalization(string $type): void
    {
        $this->attributes['assignment_type'] = $type;
    }

    public function title()
    {
        return $this->belongsTo(Title::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'capstone_group_members', 'group_id', 'student_id');
    }

    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    public function supervisorProposals()
    {
        return $this->hasMany(GroupSupervisorProposal::class);
    }

    public function supervisions()
    {
        return $this->hasMany(Supervision::class);
    }

    public function supervisors()
    {
        return $this->belongsToMany(Lecturer::class, 'capstone_supervisions', 'group_id', 'supervisor_id')
            ->withPivot('role');
    }

    public function taSubmissions()
    {
        return $this->hasMany(TaSubmission::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Cache field — source of truth is supervisions table.
     */
    public function supervisor1()
    {
        return $this->belongsTo(Lecturer::class, 'supervisor_1_id');
    }

    /**
     * Cache field — source of truth is supervisions table.
     */
    public function supervisor2()
    {
        return $this->belongsTo(Lecturer::class, 'supervisor_2_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    /**
     * Scope groups supervised by a lecturer.
     *
     * capstone_supervisions is the source of truth, while supervisor_1_id and
     * supervisor_2_id keep imported/legacy records accessible during repair.
     */
    public function scopeSupervisedBy(Builder $query, int $lecturerId): Builder
    {
        return $query->where(function (Builder $scope) use ($lecturerId) {
            $scope->where('supervisor_1_id', $lecturerId)
                ->orWhere('supervisor_2_id', $lecturerId)
                ->orWhereHas('supervisions', fn (Builder $supervisions) => $supervisions
                    ->where('supervisor_id', $lecturerId));
        });
    }

    public function isSupervisedBy(int $lecturerId): bool
    {
        return (int) $this->supervisor_1_id === $lecturerId
            || (int) $this->supervisor_2_id === $lecturerId
            || $this->supervisions()->where('supervisor_id', $lecturerId)->exists();
    }

    /**
     * Get active members (non-deleted, current members).
     */
    public function activeMembers()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function seminarSchedules()
    {
        return $this->hasMany(SeminarSchedule::class);
    }

    public function taDefenseSchedules()
    {
        return $this->hasMany(TaDefenseSchedule::class);
    }
}
