<?php

namespace Modules\BankSoal\Models\Shared;

use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    protected $table    = 'bs_mata_kuliah';
    protected $fillable = ['kode', 'nama', 'sks', 'semester'];

    public function cpls()
    {
        return $this->belongsToMany(Cpl::class, 'bs_mata_kuliah_cpl', 'mk_id', 'cpl_id');
    }

    public function dosenPengampu()
    {
        return $this->hasMany(DosenPengampuMk::class, 'mk_id');
    }

    public function rps()
    {
        return $this->hasMany(Rps::class, 'mk_id');
    }
}
