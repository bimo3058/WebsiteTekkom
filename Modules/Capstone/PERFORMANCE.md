# Performa pemuatan Capstone

Migrasi tampilan ke Blade sedang berlangsung. Status halaman, kalender,
penguncian fitur, dependency, dan verifikasi dicatat di
[BLADE_MIGRATION.md](BLADE_MIGRATION.md). Bagian di bawah mencatat optimasi
runtime Next.js yang sebelumnya dikerjakan. Pintu masuk SICATA kini memakai
Laravel Blade melalui `/capstone/launch` atau `/capstone/dashboard`.

Audit skema Supabase dan alur backend CTMS tersedia di
[BACKEND_SCHEMA_AUDIT.md](BACKEND_SCHEMA_AUDIT.md). Pemeriksaan metadata menemukan
244 tabel (51 Capstone), serta tiga migration fitur Blade yang belum diterapkan.

Seluruh 14 halaman mahasiswa kini tersedia dalam folder fitur Blade, dengan
TA Defense tetap mengarah ke TA Submission. Folder dan alur API dicatat di
[README mahasiswa](resources/views/pages/mahasiswa/README.md).

Loading Blade memakai spinner pada halaman serta indikator di bagian atas selama
permintaan API atau unggahan berjalan. Permintaan bersamaan dihitung sampai seluruh
proses selesai; pemeriksaan jumlah notifikasi di latar belakang tidak memicu indikator.
Notifikasi aksi berhasil menampilkan centang beranimasi, sedangkan kegagalan memakai
ikon silang. Animasi mengikuti preferensi `prefers-reduced-motion`, tanpa dependency baru.

Pada implementasi sebelumnya, Next.js menangani tampilan; Laravel menangani autentikasi, otorisasi, dan
query database. Menyatukan folder dan perintah startup tidak menggabungkan kedua
runtime. Beban awal dikurangi dengan meminta data sesuai kebutuhan layar.

## Perubahan

- Dashboard admin menggunakan satu request `/admin/dashboard`, menggantikan tiga
  request dashboard, seluruh periode, dan detail lima kelompok. Respons tambahan
  berisi `total_periods`, `total_groups`, `pending_finalization`, dan
  `recent_groups` (maksimal lima objek dengan `id`, `code`, `status`). Field lama
  tetap tersedia. Total kelompok dan pending dihitung di SQL untuk seluruh data,
  bukan dari lima kelompok terbaru. Ringkasan kelompok tetap memerlukan
  `capstone.groups.view`.
- Identitas pengguna memakai model pengguna yang sudah diautentikasi. Profil
  `student`/`lecturer` tidak eager-load pemilik `user` kembali. `loadMissing`
  memakai ulang profil yang sudah dimuat dalam request tersebut. Endpoint ini
  tetap mengembalikan ID profil akademik, NIM/NIP, dan role yang sama.
- Daftar user mengambil kolom yang dipakai respons, tanpa `sso_data` atau atribut
  akun lain. Relasi diambil sekaligus, pagination menerima `per_page` 1–100
  (default tetap 100 agar selector lama kompatibel).
- Revalidasi sesi tidak lagi otomatis memuat dashboard saat pengguna membuka
  halaman lain. Prefetch saat login/pindah role tetap tersedia. URL prefetch
  workflow mahasiswa disamakan dengan route `/mahasiswa/workflow` yang tersedia.
  Halaman dashboard mahasiswa meminta workflow bersamaan dengan data lainnya,
  sehingga tidak menunggu empat respons selesai untuk memulai request workflow.
- `composer dev` meneruskan ke `npm run dev`, yang sudah menjalankan Laravel,
  Vite, dan Next.js. Tidak ada proses Laravel kedua dari Composer.

## Menjalankan dan menerapkan indeks

Dari root proyek, pengembangan tetap bisa dimulai dengan satu perintah:

```powershell
npm run dev
```

`composer dev` juga menggunakan alur yang sama.

Migration baru menambahkan indeks `(created_at, id)` untuk mengambil kelompok
terbaru secara terurut. Migration ini **belum diterapkan ke database aplikasi**.
Jalankan hanya migration tersebut pada database target yang benar:

```powershell
php artisan migrate --path=Modules/Capstone/database/migrations/2026_09_08_000000_add_capstone_recent_groups_index.php
```

Migration lama sudah mendefinisikan unique index `students.user_id`,
`lecturers.user_id`, `(student_id, period_id)` pada keanggotaan, dan indeks
periode/status/pembimbing. Periksa penerapannya di PostgreSQL sebelum menambah
indeks serupa. Lampiran skema yang diberikan tidak memuat tabel Capstone,
`users`, atau `students`; kondisi indeks database aktif belum diverifikasi.

## Mengukur hasil

1. Ukur frontend dengan build produksi. Jalankan `npm --prefix frontend run build`,
   hentikan Next.js dev yang memakai port 3000, lalu
   `npm --prefix frontend run start`; Laravel tetap harus berjalan. Kompilasi
   halaman saat `next dev` dapat memengaruhi waktu load pertama. Ini mengikuti
   [panduan pengukuran produksi Next.js](https://nextjs.org/docs/app/guides/production-checklist).
2. Di Network browser, catat durasi/TTFB, ukuran respons, dan jumlah request saat
   membuka dashboard admin, mahasiswa, serta halaman daftar user. Tiga request
   data dashboard admin kini menjadi satu; request sesi/notifikasi tetap terpisah.
3. Bedakan hasil cache dan query asli melalui `X-Capstone-Cache` serta
   `X-DB-Query-Cache`. `_fresh=1` melewati response cache Laravel, tetapi tidak
   mematikan cache SQL global. Untuk pengukuran query tanpa cache di lingkungan
   pengujian, nonaktifkan sementara `CAPSTONE_HTTP_CACHE_ENABLED` dan
   `DB_QUERY_CACHE_ENABLED`, muat ulang konfigurasi, dan gunakan sesi browser baru.
4. Untuk melihat pemakaian indeks pada PostgreSQL, jalankan query baca berikut
   pada database pengujian yang volumenya menyerupai produksi:

```sql
EXPLAIN (ANALYZE, BUFFERS)
SELECT id, code, status
FROM capstone_groups
ORDER BY created_at DESC, id DESC
LIMIT 5;
```

Cache HTTP per user/role yang sudah ada tetap dipakai. TTL tidak diperpanjang;
perubahan ini mengurangi pekerjaan pada cache miss. Total SQL `COUNT` masih
bergantung pada jumlah baris: jumlah query yang konstan bukan jaminan durasi
konstan. Latensi ke PostgreSQL/Redis juga perlu diukur di server target.

## Verifikasi

```powershell
php artisan test tests/Unit/CapstoneReadPerformanceTest.php tests/Unit/CapstoneResponseCacheTest.php
npm --prefix frontend run type-check -- --incremental false
```

Tes SQL menggunakan SQLite in-memory dengan migration tabel terkait, terpisah
dari migration EOffice. Tes membandingkan 4 query profil lama dengan 2 query
baru, memastikan pemakaian ulang profil tanpa query tambahan, memeriksa
ringkasan dashboard dengan 8 dan 80 kelompok, batas izin, pagination ID akademik,
serta pembuatan/penghapusan indeks.

Suite integrasi `php artisan test --filter=Capstone` saat pemeriksaan tertahan
oleh migration EOffice yang menghapus `eo_kp_penilaian.nilai_laporan_pembimbing`
yang tidak tersedia di SQLite. Belum ada benchmark waktu respons database
produksi atau pengujian browser dengan akun login.
