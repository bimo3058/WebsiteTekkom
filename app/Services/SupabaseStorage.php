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
     * Upload file ke Supabase Storage.
     * Mengembalikan path relatif file jika berhasil, null jika gagal.
     */
    public function upload(UploadedFile $file, string $folder = 'uploads'): ?string
    {
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
        )->post($this->storageUrl($path));

        if ($response->successful()) {
            return $path;
        }

        report(new \RuntimeException(
            'Supabase upload failed: ' . $response->body()
        ));

        return null;
    }

    /**
     * Hapus file dari Supabase Storage berdasarkan path relatif.
     */
    public function delete(string $path): bool
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->key,
            'Content-Type'  => 'application/json',
        ])->delete($this->url . '/storage/v1/object/' . $this->bucket, [
            'prefixes' => [$path],
        ]);

        return $response->successful();
    }

    /**
     * Ambil public URL file (untuk public bucket).
     */
    public function publicUrl(string $path): string
    {
        return $this->url . '/storage/v1/object/public/' . $this->bucket . '/' . $path;
    }

    /**
     * Ambil signed URL file (untuk private bucket), default expire 1 jam.
     */
    public function signedUrl(string $path, int $expiresIn = 3600): ?string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->key,
            'Content-Type'  => 'application/json',
        ])->post($this->url . '/storage/v1/object/sign/' . $this->bucket . '/' . $path, [
            'expiresIn' => $expiresIn,
        ]);

        if ($response->successful()) {
            return $this->url . '/storage/v1' . $response->json('signedURL');
        }

        return null;
    }

    private function storageUrl(string $path): string
    {
        return $this->url . '/storage/v1/object/' . $this->bucket . '/' . $path;
    }
}