<?php

namespace Modules\Capstone\Models;

use App\Models\Lecturer;
use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Support\EvaluationDeadline;
use Modules\EOffice\Models\Peminjaman;
use Modules\EOffice\Models\Ruangan;

class TaDefenseSchedule extends Model
{
    protected $table = 'capstone_ta_defense_schedules';

    protected $fillable = [
        'student_id',
        'group_id',
        'date',
        'start_time',
        'end_time',
        'room',
        'status',
        'requested_by',
        'rejection_reason',
        'period_id',
        'examiner_1_id',
        'examiner_2_id',
        'location_id',
        'eoffice_ruangan_id',
        'eoffice_peminjaman_id',
        'evaluation_deadline',
        'notes',
        'final_score',
    ];

    protected $casts = [
        'date' => 'date',
        'evaluation_deadline' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Same convention as seminar schedules: default the examiner
        // evaluation deadline to schedule date + 2 days, follow date
        // edits, never override an explicit value.
        static::saving(function (self $schedule) {
            if (! Schema::hasColumn('capstone_ta_defense_schedules', 'evaluation_deadline')) {
                return;
            }

            if (! $schedule->date) {
                return;
            }

            if (EvaluationDeadline::storedDeadline($schedule) === null
                || ($schedule->isDirty('date') && ! $schedule->isDirty('evaluation_deadline'))) {
                $schedule->evaluation_deadline = EvaluationDeadline::fromDate($schedule->date);
            }
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function examiners(): HasMany
    {
        return $this->hasMany(TaDefenseExaminer::class, 'schedule_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'capstone_ta_defense_schedule_student', 'schedule_id', 'student_id')
            ->withTimestamps();
    }

    public function examiner1(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'examiner_1_id');
    }

    public function examiner2(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'examiner_2_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function eofficeRoom(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'eoffice_ruangan_id');
    }

    public function eofficeBooking(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class, 'eoffice_peminjaman_id');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(TaDefenseEvaluation::class, 'schedule_id');
    }
}
