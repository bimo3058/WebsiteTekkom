<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class MrBlacklist extends Model
{
    protected $table = 'eo_mr_blacklists';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
