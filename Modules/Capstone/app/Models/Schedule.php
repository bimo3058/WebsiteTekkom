<?php

namespace Modules\Capstone\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'capstone_seminar_schedules';
    protected $fillable = ['group_id', 'type', 'date', 'start_time', 'end_time', 'room', 'mode', 'notes'];

    protected $casts = [
        'date' => 'date',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
