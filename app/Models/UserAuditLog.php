<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAuditLog extends Model
{
    use HasFactory;

    protected $table = 'user_audit_logs';

    protected $fillable = [
        'user_id',
        'action',
        'source',
        'details',
    ];

    protected $casts = [
        'details' => 'json',
    ];

    /**
     * Get the user associated with this audit log
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}