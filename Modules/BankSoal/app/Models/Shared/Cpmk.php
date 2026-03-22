<?php

namespace Modules\BankSoal\Models\Shared;

use Illuminate\Database\Eloquent\Model;

class Cpmk extends Model
{
    protected $table    = 'bs_cpmk';
    protected $fillable = ['kode', 'deskripsi', 'cpl_id'];
    public $timestamps = true;


    public function cpls()
    {
        return $this->belongsToMany(Cpl::class, 'bs_cpl_cpmk', 'cpmk_id', 'cpl_id');
    }

    public function cpl()
    {
        return $this->belongsTo(Cpl::class, 'cpl_id');
    }
}
