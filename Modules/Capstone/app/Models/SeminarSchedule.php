<?php

namespace Modules\Capstone\Models;

use App\Models\Lecturer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeminarSchedule extends Model
{
    protected $table = 'capstone_seminar_schedules';
    protected $fillable = [
        'group_id',
        'type',
        'date',
        'start_time',
        'end_time',
        'room',
        'examiner_1_id',
        'examiner_2_id',
        'status',
        'requested_by',
        'rejection_reason',
        'location_id',
        'eoffice_ruangan_id',
        'eoffice_peminjaman_id',
        'final_score',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function examiner1(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'examiner_1_id');
    }

    public function examiner2(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'examiner_2_id');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(SeminarEvaluation::class, 'schedule_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function eofficeRoom(): BelongsTo
    {
        return $this->belongsTo(\Modules\EOffice\Models\Ruangan::class, 'eoffice_ruangan_id');
    }

    public function eofficeBooking(): BelongsTo
    {
        return $this->belongsTo(\Modules\EOffice\Models\Peminjaman::class, 'eoffice_peminjaman_id');
    }
}
