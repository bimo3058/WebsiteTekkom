<?php

namespace Modules\Capstone\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'capstone_audit_logs';
    protected $fillable = [
        'user_id',
        'action',
        'target_type',
        'target_id',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
