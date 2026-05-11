<?php

namespace Modules\Capstone\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'capstone_seminar_schedules';
    protected $fillable = ['group_id', 'type', 'date', 'room', 'mode', 'notes'];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
