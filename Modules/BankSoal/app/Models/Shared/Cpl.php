<?php

namespace Modules\BankSoal\Models\Shared;

use Illuminate\Database\Eloquent\Model;


class Cpl extends Model
{
    protected $table    = 'bs_cpl';
    protected $fillable = ['kode', 'deskripsi'];
    public $timestamps = false;

    public function mataKuliahs()
    {
        return $this->belongsToMany(MataKuliah::class, 'bs_mata_kuliah_cpl', 'cpl_id', 'mk_id');
    }

    public function cpmks()
    {
        return $this->belongsToMany(Cpmk::class, 'bs_cpl_cpmk', 'cpl_id', 'cpmk_id');
    }
}
