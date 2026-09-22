<?php

namespace Modules\ManajemenMahasiswa\Support;

use App\Services\SupabaseStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\ManajemenMahasiswa\Models\Pengaduan;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bukti dukung pengaduan berupa berkas PDF yang diunggah langsung (satu format baku).
 *
 * Menggantikan "link Google Drive": tautan Drive memperlihatkan akun pemilik
 * berkas kepada pembacanya, sehingga membocorkan identitas pelapor konfidensial.
 *
 * Perlindungan identitas pada berkas:
 *  - nama berkas asli dibuang, diganti nama acak;
 *  - PDF tidak bisa di-encode ulang tanpa perpustakaan PDF, jadi diperiksa dan
 *    dibersihkan sebisanya: ditolak bila terenkripsi atau memuat JavaScript /
 *    aksi peluncur / lampiran, lalu metadata penulis (Info dictionary & XMP)
 *    dikosongkan dengan panjang byte yang sama agar struktur PDF tidak rusak.
 *    Pembersihan ini best-effort: metadata di dalam stream terkompresi tidak
 *    terjangkau, sehingga UI mengingatkan pelapor agar tidak memakai PDF yang
 *    memuat identitasnya.
 *  - hanya PDF yang diterima (standarisasi). Tiket lama yang berisi gambar tetap
 *    bisa dibuka: penyajian di bawah masih mengenali mime gambar.
 *
 * Alur dua langkah (form → konfirmasi → kirim): berkas tidak bisa ikut sebagai
 * input tersembunyi, jadi diunggah saat langkah konfirmasi, disimpan sebagai
 * "pending" di session (ephemeral), lalu di-commit ke tiket saat dikirim.
 */
final class PengaduanBukti
{
    public const MAX_FILES = 3;

    public const MAX_KB = 5120;

    public const MIMES = ['pdf'];

    public const PDF_MIME = 'application/pdf';

    private const FOLDER = 'pengaduan/bukti';

    // ── Pending (session) ────────────────────────────────────────────────

    /**
     * @return array<int, array{path:string,mime:string,size:int}>
     */
    public static function pending(string $scope): array
    {
        return (array) session()->get(self::sessionKey($scope), []);
    }

    /**
     * Ganti bukti pending dengan berkas baru (yang lama dihapus dari storage).
     *
     * @param  array<int, UploadedFile>  $files
     * @return array<int, array{path:string,mime:string,size:int}>
     */
    public static function stage(array $files, string $scope): array
    {
        $files = array_slice(array_values(array_filter($files)), 0, self::MAX_FILES);

        $staged = [];
        foreach ($files as $file) {
            $item = self::store($file);

            if ($item === null) {
                self::deleteAll($staged);

                throw ValidationException::withMessages([
                    'bukti' => 'Berkas bukti tidak dapat diproses. Pastikan berupa PDF yang valid (tanpa kata sandi, JavaScript, atau lampiran), lalu coba lagi.',
                ]);
            }

            $staged[] = $item;
        }

        self::deleteAll(self::pending($scope));
        session()->put(self::sessionKey($scope), $staged);

        return $staged;
    }

    /**
     * Ambil bukti pending untuk dilekatkan ke tiket lalu kosongkan session.
     *
     * @return array<int, array{path:string,mime:string,size:int}>
     */
    public static function commit(string $scope): array
    {
        $items = self::pending($scope);
        session()->forget(self::sessionKey($scope));

        return $items;
    }

    // ── Penyajian ────────────────────────────────────────────────────────

    /**
     * Ubah bukti tersimpan/pending menjadi baris daftar untuk tampilan (partial bukti-items).
     * Path storage tidak ikut: hanya URL hasil $urlFor, jenis, dan ukuran.
     *
     * @param  array<int, array{mime?:string,size?:int}>  $stored
     * @param  callable(int): string  $urlFor  indeks bukti → URL untuk membukanya
     * @return array<int, array{url:string,kind:string,size:int,label:string}>
     */
    public static function toItems(array $stored, callable $urlFor): array
    {
        $items = [];
        foreach (array_values($stored) as $i => $item) {
            $items[] = [
                'url' => $urlFor($i),
                'kind' => ($item['mime'] ?? '') === self::PDF_MIME ? 'pdf' : 'image',
                'size' => (int) ($item['size'] ?? 0),
                'label' => 'Bukti ' . ($i + 1),
            ];
        }

        return $items;
    }

    /**
     * Alirkan satu bukti milik tiket. Pemanggil WAJIB sudah memastikan hak akses.
     */
    public static function respond(Pengaduan $pengaduan, int $index): Response
    {
        return self::stream(data_get($pengaduan->data_template, 'bukti.' . $index), $index);
    }

    /**
     * Alirkan satu bukti PENDING (sudah diunggah, belum dikirim) agar pelapor bisa
     * memeriksa apa yang mereka unggah. Sumbernya session milik pengunggah, jadi
     * path storage tidak pernah datang dari browser dan pihak lain tak bisa membukanya.
     */
    public static function respondPending(string $scope, int $index): Response
    {
        return self::stream(self::pending($scope)[$index] ?? null, $index);
    }

    /**
     * @param  mixed  $item  {path,mime,size} atau null bila tidak ada
     */
    private static function stream(mixed $item, int $index): Response
    {
        if (! is_array($item) || empty($item['path'])) {
            abort(404);
        }

        $file = app(SupabaseStorage::class)->download($item['path'], self::bucket());

        if ($file === null) {
            abort(404);
        }

        // Tiket lama masih menyimpan gambar; jenis selain daftar ini tidak pernah
        // disajikan apa adanya agar berkas tak dikenal tidak dieksekusi peramban.
        $extension = match ($item['mime'] ?? '') {
            self::PDF_MIME => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => null,
        };
        $mime = $extension === null ? 'application/octet-stream' : $item['mime'];

        return response($file['content'], 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => ($extension === null ? 'attachment' : 'inline')
                . '; filename="bukti-' . ($index + 1) . '.' . ($extension ?? 'bin') . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
            // Bukti disematkan di pop-up halaman kita sendiri; selain itu berkas
            // tidak boleh memuat apa pun dari luar (pola sama dipakai EOffice).
            'Content-Security-Policy' => "default-src 'none'; frame-ancestors 'self'",
        ]);
    }

    // ── Internal ─────────────────────────────────────────────────────────

    private static function bucket(): ?string
    {
        return config('manajemenmahasiswa.pengaduan_bukti_bucket') ?: null;
    }

    private static function sessionKey(string $scope): string
    {
        return 'mm_pengaduan_bukti.' . $scope;
    }

    /**
     * @param  array<int, array{path:string}>  $items
     */
    private static function deleteAll(array $items): void
    {
        $storage = app(SupabaseStorage::class);

        foreach ($items as $item) {
            if (! empty($item['path'])) {
                $storage->delete($item['path'], self::bucket());
            }
        }
    }

    /**
     * Periksa & bersihkan PDF, lalu unggah dengan nama acak. Lihat catatan kelas.
     *
     * @return array{path:string,mime:string,size:int}|null
     */
    private static function store(UploadedFile $file): ?array
    {
        $raw = @file_get_contents($file->getRealPath());
        if ($raw === false
            || ! str_starts_with(ltrim(substr($raw, 0, 64)), '%PDF-')
            || ! str_contains(substr($raw, -2048), '%%EOF')) {
            return null; // bukan PDF, atau terpotong
        }

        // Polyglot (gambar yang disambung PDF) ditolak: berkas yang juga terbaca
        // sebagai gambar bisa ditafsirkan berbeda oleh pembaca yang berbeda.
        if (@getimagesizefromstring($raw) !== false) {
            return null;
        }

        // Nama PDF boleh ditulis /J#61vaScript; urai dulu agar pemeriksaan tidak bisa dikelabui.
        $decoded = preg_replace_callback('/#([0-9A-Fa-f]{2})/', fn (array $m) => chr(hexdec($m[1])), $raw) ?? $raw;
        if (preg_match('#/(JS|JavaScript|Launch|EmbeddedFile|RichMedia|XFA|Encrypt|SubmitForm|ImportData)(?![A-Za-z0-9])#', $decoded)) {
            return null;
        }

        $contents = self::scrubPdfMetadata($raw);
        $path = self::FOLDER . '/' . Str::uuid() . '.pdf';

        if (! app(SupabaseStorage::class)->put($path, $contents, self::PDF_MIME, self::bucket())) {
            return null;
        }

        return ['path' => $path, 'mime' => self::PDF_MIME, 'size' => strlen($contents)];
    }

    /**
     * Kosongkan metadata penulis dengan panjang byte TETAP (spasi), supaya offset
     * tabel xref tidak bergeser dan PDF tetap terbuka.
     */
    private static function scrubPdfMetadata(string $pdf): string
    {
        // Info dictionary: /Author (…) atau /Author <hex>
        $pdf = preg_replace_callback(
            '/(\/(?:Author|Creator|Producer|Title|Subject|Keywords|Company|Manager)\s*)(\((?:[^()\\\\]|\\\\.|\((?:[^()\\\\]|\\\\.)*\))*\)|<[0-9A-Fa-f\s]*>)/s',
            fn (array $m) => $m[1] . '(' . str_repeat(' ', strlen($m[2]) - 2) . ')',
            $pdf
        ) ?? $pdf;

        // XMP (teks XML, umumnya tidak dikompres): isi elemen penulis/alat dikosongkan.
        return preg_replace_callback(
            '/(<(dc:creator|dc:title|dc:description|dc:contributor|dc:publisher|pdf:Author|pdf:Producer|pdf:Keywords|xmp:CreatorTool)\b[^>]*>)(.*?)(<\/\2>)/s',
            fn (array $m) => $m[1] . str_repeat(' ', strlen($m[3])) . $m[4],
            $pdf
        ) ?? $pdf;
    }
}
