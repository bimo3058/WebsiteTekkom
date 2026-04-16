<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SupabaseStorage
{
    private string $url;
    private string $key;
    private string $bucket;

    public function __construct()
    {
        $this->url    = rtrim(config('services.supabase.url'), '/');
        $this->key    = config('services.supabase.key');
        $this->bucket = config('services.supabase.bucket');
    }

    /**
     * Tambahkan parameter $bucket opsional di akhir.
     * Upload dengan custom filename format atau UUID
     * @param string|null $fileName Custom filename (tanpa extension). Jika null, gunakan UUID
     */
    public function upload(UploadedFile $file, string $folder = 'uploads', ?string $bucket = null, ?string $fileName = null): ?string
    {
        $targetBucket = $bucket ?? $this->bucket; // Gunakan parameter jika ada, jika tidak pakai default
        $extension = $file->getClientOriginalExtension();
        // Jika $fileName diberikan, gunakan itu; jika tidak, gunakan UUID
        $filename = $fileName ? $fileName . '.' . $extension : Str::uuid() . '.' . $extension;
        $path      = $folder . '/' . $filename;
        $mimeType  = $file->getMimeType();
        
        \Log::info('Supabase Upload Started', [
            'url' => $this->url,
            'bucket' => $targetBucket,
            'path' => $path,
            'mime_type' => $mimeType,
            'file_size' => $file->getSize(),
        ]);

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->key,
                    'Content-Type'  => $mimeType,
                    'x-upsert'      => 'false',
                ])
                ->withBody(
                    file_get_contents($file->getRealPath()),
                    $mimeType
                )
                ->post($this->url . '/storage/v1/object/' . $targetBucket . '/' . $path);

            \Log::info('Supabase Response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                \Log::info('Supabase upload successful', ['path' => $path]);
                return $path;
            }

            \Log::error('Supabase upload failed', [
                'status' => $response->status(),
                'bucket' => $targetBucket,
                'path' => $path,
                'response' => $response->body(),
                'json' => $response->json(),
            ]);

            return null;

        } catch (\Exception $e) {
            \Log::error('Supabase upload exception', [
                'bucket' => $targetBucket,
                'path' => $path,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Generate public URL dari path
     * Format: {SUPABASE_URL}/storage/v1/object/public/{bucket}/{path}
     */
    public function getPublicUrl(string $path, ?string $bucket = null): string
    {
        $targetBucket = $bucket ?? $this->bucket;
        return $this->url . '/storage/v1/object/public/' . $targetBucket . '/' . $path;
    }

    /**
     * Hapus file dengan dukungan pilihan bucket.
     */
    public function delete(string $path, ?string $bucket = null): bool
    {
        $targetBucket = $bucket ?? $this->bucket;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->key,
            'Content-Type'  => 'application/json',
        ])->delete($this->url . '/storage/v1/object/' . $targetBucket, [
            'prefixes' => [$path],
        ]);

        return $response->successful();
    }

    /**
     * Ambil public URL dengan dukungan pilihan bucket.
     */
    public function publicUrl(string $path, ?string $bucket = null): string
    {
        $targetBucket = $bucket ?? $this->bucket;
        return $this->url . '/storage/v1/object/public/' . $targetBucket . '/' . $path;
    }

    /**
     * Ambil signed URL dengan dukungan pilihan bucket.
     */
    public function signedUrl(string $path, int $expiresIn = 3600, ?string $bucket = null): ?string
    {
        $targetBucket = $bucket ?? $this->bucket;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->key,
            'Content-Type'  => 'application/json',
        ])->post($this->url . '/storage/v1/object/sign/' . $targetBucket . '/' . $path, [
            'expiresIn' => $expiresIn,
        ]);

        if ($response->successful()) {
            return $this->url . '/storage/v1' . $response->json('signedURL');
        }

        return null;
    }

    // Helper internal tidak perlu diubah, biarkan mereferensi ke $this->bucket default
    private function storageUrl(string $path): string
    {
        return $this->url . '/storage/v1/object/' . $this->bucket . '/' . $path;
    }
}