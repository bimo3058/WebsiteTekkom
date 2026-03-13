<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EoAuditLog extends Model
{
    public $timestamps  = false;
    protected $table    = 'eo_audit_logs';
    protected $fillable = ['user_id', 'action', 'subject_type', 'subject_id', 'description', 'old_data', 'new_data', 'created_at'];
    protected $casts    = ['old_data' => 'array', 'new_data' => 'array', 'created_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class)->withTrashed(); }
}