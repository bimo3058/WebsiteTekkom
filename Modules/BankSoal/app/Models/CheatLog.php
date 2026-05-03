<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;

class CheatLog extends Model
{
    protected $table = 'bs_cheat_logs';

    protected $fillable = [
        'kompre_session_id', 'event_type', 'description', 'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function session()
    {
        return $this->belongsTo(KompreSession::class, 'kompre_session_id');
    }
}
