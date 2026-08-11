<?php

namespace Modules\Capstone\Services;

use App\Services\SupabaseStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentStorageService
{
    public function __construct(private readonly SupabaseStorage $supabase)
    {
    }

    /**
     * Store file in MinIO (new uploads)
     */
    public function store(UploadedFile $file, string $folder, string $prefix = ''): string
    {
        $safeName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $filename = now()->format('YmdHis').'_'.Str::random(12).'_'.($safeName ?: 'document');
        $folder = trim("capstone/{$folder}/{$prefix}", '/');
        $path = $this->supabase->upload($file, $folder, null, $filename);

        if (! $path) {
            throw new \RuntimeException('Gagal mengunggah dokumen ke Supabase Storage.');
        }

        return $path;
    }

    /**
     * Get file for download - supports both old (local) and new (MinIO)
     */
    public function get(string $path): ?array
    {
        if (str_starts_with($path, 'capstone/')) {
            $file = $this->supabase->download($path);

            return $file ? ['disk' => 'supabase', ...$file] : null;
        }

        // Fallback to local (old files)
        if (Storage::disk('public')->exists($path)) {
            return [
                'disk' => 'public',
                'path' => Storage::disk('public')->path($path),
            ];
        }

        return null;
    }

    /**
     * Delete file from both storages
     */
    public function delete(string $path): void
    {
        if (str_starts_with($path, 'capstone/')) {
            $this->supabase->delete($path);
        }
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Migrate single file from local to MinIO
     */
    public function migrateToS3(string $path): bool
    {
        if (! Storage::disk('public')->exists($path)) {
            Log::warning("File not found for migration: {$path}");

            return false;
        }

        try {
            $targetPath = 'capstone/legacy/'.ltrim($path, '/');
            $content = Storage::disk('public')->get($path);
            $mimeType = Storage::disk('public')->mimeType($path) ?: 'application/octet-stream';

            return $this->supabase->put($targetPath, $content, $mimeType);
        } catch (\Exception $e) {
            Log::error("Migration failed for {$path}: ".$e->getMessage());

            return false;
        }
    }

    public function signedUrl(string $path, int $expiresIn = 300): ?string
    {
        return str_starts_with($path, 'capstone/')
            ? $this->supabase->signedUrl($path, $expiresIn)
            : null;
    }

    /** @return array<int, array<string, mixed>> */
    public function list(string $prefix): array
    {
        return $this->supabase->list(trim('capstone/'.$prefix, '/'));
    }
}
