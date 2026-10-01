<?php

namespace Modules\Capstone\Models;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpoStudentDocument extends Model
{
    protected $table = 'capstone_expo_student_documents';

    public $timestamps = true;

    protected $fillable = [
        'expo_registration_id',
        'group_id',
        'student_id',
        'file_path',
        'storage_location',
        'original_name',
        'status',
        'feedback',
        'reviewed_by',
    ];

    public function expoRegistration(): BelongsTo
    {
        return $this->belongsTo(ExpoRegistration::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
