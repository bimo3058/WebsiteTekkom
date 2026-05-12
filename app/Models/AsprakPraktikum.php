<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class AsprakPraktikum extends Pivot
{
    use SoftDeletes;

    protected $table = 'asprak_praktikum';

    public $incrementing = true; // since it has bigIncrements id

    protected $fillable = [
        'praktikum_id',
        'user_id',
        'role',
        'deskripsi'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class, 'praktikum_id');
    }
}
