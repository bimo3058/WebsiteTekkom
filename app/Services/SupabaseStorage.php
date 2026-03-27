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
     */
    public function upload(UploadedFile $file, string $folder = 'uploads', ?string $bucket = null): ?string
    {
        $targetBucket = $bucket ?? $this->bucket; // Gunakan parameter jika ada, jika tidak pakai default
        $extension = $file->getClientOriginalExtension();
        $path      = $folder . '/' . Str::uuid() . '.' . $extension;
        $mimeType  = $file->getMimeType();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->key,
            'Content-Type'  => $mimeType,
            'x-upsert'      => 'false',
        ])->withBody(
            file_get_contents($file->getRealPath()),
            $mimeType
        )->post($this->url . '/storage/v1/object/' . $targetBucket . '/' . $path);

        if ($response->successful()) {
            return $path;
        }

        report(new \RuntimeException(
            "Supabase upload failed to bucket [{$targetBucket}]: " . $response->body()
        ));

        return null;
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