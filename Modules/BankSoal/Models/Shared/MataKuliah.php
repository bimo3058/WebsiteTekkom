<?php

namespace Modules\BankSoal\Models\Shared;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    use HasFactory;

    protected $table = 'bs_mata_kuliah';

    protected $fillable = [
        'kode', 'nama', 'sks', 'semester',
    ];
}
