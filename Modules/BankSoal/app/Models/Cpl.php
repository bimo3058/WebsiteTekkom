<?php

namespace Modules\BankSoal\Models\Shared;

use Illuminate\Database\Eloquent\Model;

/**
 * [Shared - Model] Capaian Pembelajaran Lulusan
 * 
 * Model ini digunakan oleh semua role
 */
class Cpl extends Model
{
    protected $table    = 'bs_cpl';
    protected $fillable = ['kode', 'deskripsi'];

    public function mataKuliahs()
    {
        return $this->belongsToMany(MataKuliah::class, 'bs_mata_kuliah_cpl', 'cpl_id', 'mk_id');
    }
}
