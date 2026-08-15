<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    use HasFactory;

    protected $table = 'eo_mr_pengaturan';

    protected $fillable = [
        'key',
        'value'
    ];
}
