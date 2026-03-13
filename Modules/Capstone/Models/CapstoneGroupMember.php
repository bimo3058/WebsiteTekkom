<?php

namespace Modules\Capstone\Models;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CapstoneGroupMember extends Model
{
    protected $table = 'capstone_group_members';

    protected $fillable = [
        'group_id',
        'student_id',
        'period_id',
        'is_leader',
    ];

    protected $casts = [
        'is_leader' => 'boolean',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(CapstoneGroup::class, 'group_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(CapstonePeriod::class, 'period_id');
    }
}