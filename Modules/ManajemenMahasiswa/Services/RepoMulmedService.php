<?php

namespace Modules\ManajemenMahasiswa\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\ManajemenMahasiswa\Models\RepoMulmed;

class RepoMulmedService
{
    private const DISK = 'public';

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
            $tipe     = $this->resolveTipe($file->getMimeType());
            $path     = $file->store("mk_mulmed/{$tipe}", self::DISK);
            $namaFile = $file->getClientOriginalName();

            return RepoMulmed::create(array_merge($meta, [
                'nama_file'    => $namaFile,
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
            Storage::disk(self::DISK)->delete($repo->path_file);
            $repo->delete();
        });
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