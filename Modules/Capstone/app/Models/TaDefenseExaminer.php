<?php

namespace Modules\Capstone\Models;

use App\Models\Lecturer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaDefenseExaminer extends Model
{
    protected $table = 'capstone_ta_defense_examiners';
    protected $fillable = [
        'schedule_id',
        'examiner_id',
        'role',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(TaDefenseSchedule::class, 'schedule_id');
    }

    public function examiner(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'examiner_id');
    }
}
