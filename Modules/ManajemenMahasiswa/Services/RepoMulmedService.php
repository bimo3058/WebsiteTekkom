<?php

namespace Modules\ManajemenMahasiswa\Services;

use App\Services\SupabaseStorage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Models\RepoMulmed;

class RepoMulmedService
{
    public function __construct(private SupabaseStorage $supabase) {}

    /**
     * Listing file repo dengan filter.
     */
    public function list(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return RepoMulmed::with(['kegiatan', 'pengumuman'])
            ->aktif()
            ->when(isset($filters['visibility']), fn($q) => $q->where('visibility_status', $filters['visibility']))
            ->when(isset($filters['tipe_file']), fn($q) => $q->byTipe($filters['tipe_file']))
            ->when(isset($filters['kegiatan_id']), fn($q) => $q->where('kegiatan_id', $filters['kegiatan_id']))
            ->when(isset($filters['search']), fn($q) => $q->where('judul_file', 'like', "%{$filters['search']}%"))
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Upload dan simpan file multimedia.
     *
     * @param UploadedFile $file
     * @param array        $meta  { judul_file, deskripsi_meta, visibility_status, kegiatan_id?, pengumuman_id? }
     */
    public function upload(UploadedFile $file, array $meta): RepoMulmed
    {
        return DB::transaction(function () use ($file, $meta) {
            $tipe   = $this->resolveTipe($file->getMimeType());
            $folder = "mk_mulmed/{$tipe}";
            $path   = $this->supabase->upload($file, $folder);

            if (!$path) {
                throw new \RuntimeException('Gagal mengupload file ke Supabase.');
            }

            return RepoMulmed::create(array_merge($meta, [
                'nama_file'    => $file->getClientOriginalName(),
                'path_file'    => $path,
                'tipe_file'    => $tipe,
                'status_arsip' => RepoMulmed::ARSIP_AKTIF,
            ]));
        });
    }

    /**
     * Update metadata saja (tanpa re-upload file).
     */
    public function updateMeta(int $id, array $meta): RepoMulmed
    {
        $repo = RepoMulmed::findOrFail($id);
        $repo->update($meta);
        return $repo->fresh();
    }

    /**
     * Arsipkan file (soft-delete bisnis, fisik tetap ada).
     */
    public function archive(int $id): void
    {
        RepoMulmed::findOrFail($id)->update(['status_arsip' => RepoMulmed::ARSIP_DIARSIP]);
    }

    /**
     * Hapus permanen — file fisik ikut dihapus.
     */
    public function deletePermanent(int $id): void
    {
        DB::transaction(function () use ($id) {
            $repo = RepoMulmed::findOrFail($id);
            $this->supabase->delete($repo->path_file);
            $repo->delete();
        });
    }

    // -------------------------------------------------------------------------
    // Form kegiatan (Rencana Proker, Pelaksanaan, Laporan & Arsip)
    // -------------------------------------------------------------------------

    /**
     * Batas foto & dokumen berlaku untuk TOTAL file yang tersimpan pada kegiatan.
     *
     * Aturan `max:N` di validasi hanya membatasi satu kali unggah, sehingga user bisa
     * mengunggah N file → Simpan → buka Edit → unggah N lagi, berulang tanpa batas. Di
     * sini sisa file lama (setelah dikurangi yang ditandai hapus pada form yang sama)
     * dijumlahkan dengan file baru, dan diperiksa sebelum ada satu pun file yang benar-
     * benar diunggah ke penyimpanan. Dipakai bersama ketiga subbab.
     *
     * @throws ValidationException
     */
    public function pastikanKuota(Request $request, Kegiatan $kegiatan, int $maksFoto, int $maksDokumen): void
    {
        $akanDihapus = collect($request->input('hapus_file', []))->map(fn($fileId) => (int) $fileId);

        $sisaFileLama = fn(string $tipe) => $kegiatan->repoMulmed
            ->where('tipe_file', $tipe)
            ->reject(fn($file) => $akanDihapus->contains($file->id))
            ->count();

        $pesan = [];

        $totalFoto = $sisaFileLama('image') + count($request->file('foto_kegiatan', []));
        if ($totalFoto > $maksFoto) {
            $pesan['foto_kegiatan'] = 'Total foto kegiatan maksimal ' . $maksFoto
                . ", sedangkan unggahan ini membuatnya menjadi {$totalFoto}. Hapus dulu sebagian foto lama.";
        }

        $totalDokumen = $sisaFileLama('document') + count($request->file('dokumen_kegiatan', []));
        if ($totalDokumen > $maksDokumen) {
            $pesan['dokumen_kegiatan'] = 'Total dokumen kegiatan maksimal ' . $maksDokumen
                . ", sedangkan unggahan ini membuatnya menjadi {$totalDokumen}. Hapus dulu sebagian dokumen lama.";
        }

        if ($pesan) {
            throw ValidationException::withMessages($pesan);
        }
    }

    /**
     * Hapus permanen file yang ditandai `hapus_file[]` di form — hanya file milik
     * kegiatan ini, jadi id file kegiatan lain yang disisipkan diabaikan.
     */
    public function hapusTerpilih(Request $request, Kegiatan $kegiatan): void
    {
        if (!$request->filled('hapus_file')) {
            return;
        }

        foreach ($request->input('hapus_file') as $fileId) {
            $file = RepoMulmed::where('kegiatan_id', $kegiatan->id)->find($fileId);
            if ($file) {
                $this->deletePermanent($file->id);
            }
        }
    }

    /**
     * Simpan foto & dokumen kegiatan yang diunggah lewat form ke repo_mulmed.
     *
     * @param array<int, string> $daftarKolom kolom form yang diterima; Rencana Proker
     *        hanya `dokumen_kegiatan` supaya foto tak bisa diselipkan lewat request.
     */
    public function simpanUnggahan(Request $request, Kegiatan $kegiatan, array $daftarKolom = ['foto_kegiatan', 'dokumen_kegiatan']): void
    {
        foreach ($daftarKolom as $kolom) {
            if (!$request->hasFile($kolom)) {
                continue;
            }

            foreach ($request->file($kolom) as $file) {
                $this->upload($file, [
                    'kegiatan_id'       => $kegiatan->id,
                    'judul_file'        => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'visibility_status' => 'public',
                ]);
            }
        }
    }

    /**
     * Salin satu objek storage menjadi file fisik baru di $folder (S-49).
     *
     * Rencana Proker dan salinannya di Pelaksanaan tidak boleh berbagi path: menghapus
     * salinan akan ikut menghapus file milik rencana. Memakai download() + put() yang
     * sudah ada supaya layanan storage global tidak perlu diubah.
     *
     * @return string path objek baru
     * @throws \RuntimeException bila unduh atau unggah gagal
     */
    public function salinObjek(string $path, string $folder): string
    {
        $isi = $this->supabase->download($path);
        if (!$isi) {
            throw new \RuntimeException("Gagal mengunduh {$path} untuk disalin.");
        }

        $ekstensi = pathinfo($path, PATHINFO_EXTENSION);
        $baru = $folder . '/' . Str::uuid() . ($ekstensi !== '' ? '.' . $ekstensi : '');

        if (!$this->supabase->put($baru, $isi['content'], $isi['mime_type'])) {
            throw new \RuntimeException("Gagal mengunggah salinan {$path}.");
        }

        return $baru;
    }

    /**
     * Hapus objek storage tanpa baris repo — membersihkan salinan yang batal dipakai.
     *
     * @param array<int, string> $paths
     */
    public function hapusObjek(array $paths): void
    {
        foreach ($paths as $path) {
            $this->supabase->delete($path);
        }
    }

    // -------------------------------------------------------------------------
    // Helper
    // -------------------------------------------------------------------------

    private function resolveTipe(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return RepoMulmed::TIPE_IMAGE;
        }
        if (str_starts_with($mimeType, 'video/')) {
            return RepoMulmed::TIPE_VIDEO;
        }
        return RepoMulmed::TIPE_DOCUMENT;
    }
}