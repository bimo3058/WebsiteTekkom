<?php

namespace Modules\BankSoal\Models\Komprehensif;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ImportBatch extends Model
{
    protected $table = 'bs_import_batches';

    protected $fillable = [
        'user_id',
        'tipe',
        'nama_file',
        'file_hash',
        'total_baris',
        'berhasil',
        'dilewati',
        'gagal',
        'errors',
        'status',
    ];

    protected $casts = [
        'errors' => 'array',
        'total_baris' => 'integer',
        'berhasil' => 'integer',
        'dilewati' => 'integer',
        'gagal' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sessions()
    {
        return $this->hasMany(KompreSession::class, 'import_batch_id');
    }
}
