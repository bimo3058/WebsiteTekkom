<?php

namespace Modules\BankSoal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cpl extends Model
{
    use SoftDeletes;

    protected $table    = 'bs_cpl';
    protected $fillable = ['kode', 'deskripsi'];
    public $timestamps = false;

    public function mataKuliahs()
    {
        return $this->belongsToMany(MataKuliah::class, 'bs_mata_kuliah_cpl', 'cpl_id', 'mk_id');
    }

    public function cpmks()
    {
        return $this->hasMany(Cpmk::class, 'cpl_id');
    }
}
