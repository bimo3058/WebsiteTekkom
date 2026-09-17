<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuperAdminNotification extends Model
{
    protected $table = 'superadmin_notifications';

    protected $fillable = [
        'user_id',
        'type',
        'severity',
        'title',
        'message',
        'context',
        'status',
        'occurrence_count',
        'last_occurred_at',
        'read_at',
        'resolved_at',
    ];

    protected $casts = [
        'context' => 'json',
        'read_at' => 'datetime',
        'resolved_at' => 'datetime',
        'last_occurred_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
