<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Pengumuman berstatus pending_review ("Menunggu Verifikasi") tanpa satu pun pengajuan
 * verifikasi yang masih pending dikembalikan ke draft.
 *
 * Penyebabnya alur lama: tombol Publikasikan milik staff memasang pending_review
 * SEBELUM verifikator dipilih, sehingga pengumuman menggantung bila halaman pilih
 * verifikator ditutup — tidak terbit, tidak ada pengajuan, tidak muncul di dashboard
 * verifikasi. Sejak 15 Nov 2026 pending_review hanya dipasang saat pengajuan dikirim
 * (PengumumanService::requestApproval).
 */
return new class extends Migration
{
    public function up(): void
    {
        $ids = DB::table('mk_pengumuman as p')
            ->where('p.status_publish', 'pending_review')
            ->whereNotExists(fn ($q) => $q->from('mk_pengumuman_approval_requests as r')
                ->whereColumn('r.pengumuman_id', 'p.id')
                ->where('r.status', 'pending'))
            ->pluck('p.id');

        if ($ids->isEmpty()) {
            return;
        }

        DB::table('mk_pengumuman')->whereIn('id', $ids)
            ->update(['status_publish' => 'draft', 'updated_at' => now()]);

        Log::info('[ManajemenMahasiswa] Pengumuman menggantung dikembalikan ke draft.', ['ids' => $ids->all()]);
    }

    public function down(): void
    {
        // Sengaja no-op: status menggantung tidak dikembalikan.
    }
};
