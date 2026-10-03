<?php

namespace Modules\EOffice\Models;

use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    protected $table = 'eo_mr_fasilitas';

    protected $fillable = [
        'nama_fasilitas'
    ];

}
