<?php

namespace Modules\Capstone\Models;

use App\Models\Lecturer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CapstoneSupervision extends Model
{
    protected $table = 'capstone_supervisions';

    protected $fillable = ['group_id', 'lecturer_id', 'role'];

    const ROLE_SUPERVISOR_1 = 'SUPERVISOR_1';
    const ROLE_SUPERVISOR_2 = 'SUPERVISOR_2';

    public function group(): BelongsTo
    {
        return $this->belongsTo(CapstoneGroup::class, 'group_id');
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'lecturer_id');
    }
}