<?php

namespace Modules\ManajemenMahasiswa\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Catatan penolakan (S-79, usulan Pak Maman L-41) dalam alur tinjau S-82.
 *
 * Peninjau (KABID/WAKABID/KANIT/WAKANIT, KAHIM/WAKAHIM, admin) mengembalikan kiriman
 * ketua pelaksana — Rencana Proker maupun laporan Pelaksanaan — dengan catatan; status
 * tinjauan menjadi Perlu Perbaikan. Catatan terbuka otomatis ditutup (diperbaiki_at,
 * diperbaiki_oleh) saat ketua pelaksana mengirim ulang (TinjauanKegiatanService::kirim).
 * Rencana dan salinan Pelaksanaan-nya baris berbeda, jadi catatannya terpisah.
 */
class KegiatanCatatan extends Model
{
    protected $table = 'mk_kegiatan_catatan';

    protected $fillable = ['kegiatan_id', 'user_id', 'isi', 'diperbaiki_at', 'diperbaiki_oleh'];

    protected $casts = ['diperbaiki_at' => 'datetime'];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function penulis(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pemerbaiki(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diperbaiki_oleh');
    }

    public function getTerbukaAttribute(): bool
    {
        return $this->diperbaiki_at === null;
    }
}
