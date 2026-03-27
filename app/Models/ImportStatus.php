<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportStatus extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database (opsional jika nama file sudah jamak, 
     * tapi aman untuk didefinisikan secara eksplisit).
     */
    protected $table = 'import_statuses';

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignment).
     * Penting agar updateOrCreate atau create() di Controller/Job tidak error.
     */
    protected $fillable = [
        'user_id',
        'filename',
        'total_rows',
        'processed_rows',
        'status',
    ];

    /**
     * Casting atribut agar Laravel otomatis mengubah tipe data saat diakses.
     */
    protected $casts = [
        'total_rows' => 'integer',
        'processed_rows' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke User: Mengetahui siapa yang melakukan import.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper Method: Menghitung persentase progres secara otomatis.
     * Bisa dipanggil di Blade atau Controller: $import->percentage
     */
    public function getPercentageAttribute(): int
    {
        if ($this->total_rows <= 0) return 0;
        
        return (int) round(($this->processed_rows / $this->total_rows) * 100);
    }
}