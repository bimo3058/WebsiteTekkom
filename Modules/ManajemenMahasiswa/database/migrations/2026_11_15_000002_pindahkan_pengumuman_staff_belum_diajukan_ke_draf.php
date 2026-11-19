<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\ManajemenMahasiswa\Models\RepoMulmed;
use Modules\ManajemenMahasiswa\Services\PengumumanService;

/**
 * Pengumuman draft milik staff himpunan (wajib verifikasi) yang BELUM PERNAH diajukan
 * verifikasi dipindahkan ke fitur Draft (mk_pengumuman_drafts).
 *
 * Penyebabnya alur lama: tombol Publikasikan staff langsung membuat baris pengumuman
 * sebelum verifikator dipilih, sehingga bila halaman pilih verifikator dibatalkan,
 * pengumumannya nyangkut di daftar pengumuman berlabel Draft. Sejak 15 Nov 2026
 * pengumuman staff baru dibuat saat pengajuan dikirim
 * (PengumumanController::submitDraftVerification).
 *
 * Pengumuman yang pernah diajukan (punya riwayat pengajuan, mis. ditolak/ditarik)
 * tidak disentuh — riwayat verifikasinya menempel pada pengumuman itu.
 * Gambar & lampiran ikut pindah ke draf, tidak ada berkas yang dihapus.
 */
return new class extends Migration
{
    public function up(): void
    {
        $service = app(PengumumanService::class);

        $kandidat = DB::table('mk_pengumuman as p')
            ->where('p.status_publish', 'draft')
            ->whereNull('p.published_at')
            ->whereNotExists(fn ($q) => $q->from('mk_pengumuman_approval_requests as r')
                ->whereColumn('r.pengumuman_id', 'p.id'))
            ->orderBy('p.id')
            ->get();

        $penulis = User::with('roles')->whereIn('id', $kandidat->pluck('user_id')->unique())->get()->keyBy('id');

        $dipindah = [];

        DB::transaction(function () use ($kandidat, $penulis, $service, &$dipindah) {
            foreach ($kandidat as $p) {
                $user = $penulis->get($p->user_id);
                if (!$user || !$service->requiresApproval($user)) {
                    continue;
                }

                $berkas   = RepoMulmed::where('pengumuman_id', $p->id)->orderBy('id')->get();
                $gambar   = $berkas->filter(fn (RepoMulmed $f) => $f->isGambar())->pluck('id')->values()->all();
                $lampiran = $berkas->reject(fn (RepoMulmed $f) => $f->isGambar())->pluck('id')->values()->all();

                $drafId = DB::table('mk_pengumuman_drafts')->insertGetId([
                    'user_id'           => $p->user_id,
                    'judul'             => $p->judul,
                    'kategori'          => $p->kategori, // format JSON yang sama dengan kolom draf
                    'target_audience'   => $p->target_audience,
                    'konten'            => $p->konten,
                    'poster_repo_id'    => $gambar[0] ?? null,
                    'poster_repo_ids'   => $gambar ? json_encode($gambar) : null,
                    'lampiran_repo_ids' => $lampiran ? json_encode($lampiran) : null,
                    'created_at'        => $p->created_at,
                    'updated_at'        => now(),
                ]);

                // Lepas berkas dari pengumuman dulu agar tidak ikut terhapus.
                RepoMulmed::where('pengumuman_id', $p->id)->update(['pengumuman_id' => null]);
                DB::table('mk_pengumuman')->where('id', $p->id)->delete();

                $dipindah[$p->id] = $drafId;
            }
        });

        if ($dipindah) {
            // Sama dengan PengumumanService::flushCache() (private).
            foreach (['mk.dashboard.snapshot', 'mk.dashboard.tier1', 'mk.dashboard.tier2'] as $kunci) {
                Cache::forget($kunci);
            }
            Log::info('[ManajemenMahasiswa] Pengumuman staff yang belum diajukan dipindah ke Draft.', ['pengumuman_id => draf_id' => $dipindah]);
        }
    }

    public function down(): void
    {
        // Sengaja no-op: draf hasil pemindahan tidak dikembalikan menjadi pengumuman.
    }
};
