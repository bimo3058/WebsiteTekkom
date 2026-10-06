<?php

namespace Modules\ManajemenMahasiswa\Support;

use Modules\ManajemenMahasiswa\Models\Pengaduan;

/**
 * Pertanyaan "Detail Kejadian" per kategori pengaduan — satu sumber untuk form,
 * validasi, halaman Konfirmasi, Detail, dan Lacak.
 *
 * Polanya sama di semua kategori (meniru form Helpdesk FT Undip): empat pertanyaan
 * WAJIB dengan irama apa/di mana → siapa → kapan → seberapa sering. Yang berbeda
 * hanya label, contoh isian, dan pilihan jawabannya, supaya pertanyaan selalu
 * relevan dengan kategori yang dipilih (dulu semua kategori ditanya Mata Kuliah,
 * Dosen, dan Tendik sekaligus).
 *
 * Kunci jawaban disimpan di data_template. Kunci lama (lokasi, waktu_kejadian,
 * mata_kuliah, nama_dosen, nama_tendik, frekuensi) dipakai ulang agar tiket lama
 * tetap terbaca; kunci baru hanya objek dan pihak_terkait.
 */
final class PengaduanPertanyaan
{
    public const FREKUENSI_UMUM = ['Baru sekali', 'Beberapa kali', 'Sering atau berulang'];

    public const FREKUENSI_KULIAH = ['Sekali', 'Beberapa pertemuan', 'Hampir setiap pertemuan'];

    /** Status pihak, bukan nama — tiket PPKS tidak boleh memaksa pelapor menyebut nama. */
    public const PIHAK = [
        'Mahasiswa',
        'Dosen',
        'Tenaga kependidikan',
        'Petugas keamanan',
        'Pihak luar kampus',
        'Tidak diketahui',
    ];

    /** Label bawaan tiap kunci, dipakai untuk tiket lama yang menyimpan kunci di luar kategorinya. */
    private const LABEL_BAWAAN = [
        'objek'          => 'Objek Aduan',
        'lokasi'         => 'Lokasi Kejadian',
        'waktu_kejadian' => 'Waktu Kejadian',
        'mata_kuliah'    => 'Mata Kuliah',
        'nama_dosen'     => 'Dosen Terkait',
        'nama_tendik'    => 'Tendik Terkait',
        'pihak_terkait'  => 'Pihak Terkait',
        'frekuensi'      => 'Frekuensi',
    ];

    /**
     * @return array<string, array{
     *     subjek: string,
     *     pesan: string,
     *     fields: array<string, array{label: string, type: string, placeholder?: string, options?: array<int, string>}>
     * }>
     */
    public static function semua(): array
    {
        $waktu = fn (string $label) => ['label' => $label, 'type' => 'datetime'];
        $frekuensi = fn (array $options = self::FREKUENSI_UMUM) => [
            'label' => 'Seberapa Sering Terjadi', 'type' => 'select', 'options' => $options,
        ];

        return [
            Pengaduan::KATEGORI_AKADEMIK_ADMINISTRASI => [
                'subjek' => 'Contoh: Surat aktif kuliah belum terbit setelah 2 minggu',
                'pesan'  => 'Ceritakan layanan yang Anda ajukan, kapan diajukan, dan kendala yang dialami…',
                'fields' => [
                    'objek'          => ['label' => 'Layanan yang Diurus', 'type' => 'text', 'placeholder' => 'Contoh: Surat keterangan aktif kuliah'],
                    'nama_tendik'    => ['label' => 'Petugas / Unit Pelayanan', 'type' => 'text', 'placeholder' => 'Contoh: Bagian Akademik Departemen'],
                    'waktu_kejadian' => $waktu('Waktu Pengajuan'),
                    'frekuensi'      => $frekuensi(),
                ],
            ],
            Pengaduan::KATEGORI_PROSES_PEMBELAJARAN => [
                'subjek' => 'Contoh: Nilai tugas besar belum diumumkan',
                'pesan'  => 'Ceritakan apa yang terjadi di perkuliahan dan dampaknya bagi Anda…',
                'fields' => [
                    'mata_kuliah'    => ['label' => 'Mata Kuliah', 'type' => 'text', 'placeholder' => 'Contoh: Basis Data'],
                    'nama_dosen'     => ['label' => 'Dosen Pengampu', 'type' => 'dosen'],
                    'waktu_kejadian' => $waktu('Waktu Perkuliahan'),
                    'frekuensi'      => $frekuensi(self::FREKUENSI_KULIAH),
                ],
            ],
            Pengaduan::KATEGORI_FASILITAS_KAMPUS => [
                'subjek' => 'Contoh: AC Ruang A.3.12 tidak berfungsi',
                'pesan'  => 'Jelaskan kondisi fasilitas dan sejak kapan masalahnya terjadi…',
                'fields' => [
                    'objek'          => ['label' => 'Fasilitas yang Bermasalah', 'type' => 'text', 'placeholder' => 'Contoh: AC, proyektor, kursi'],
                    'lokasi'         => ['label' => 'Lokasi / Ruangan', 'type' => 'text', 'placeholder' => 'Contoh: Ruang A.3.12'],
                    'waktu_kejadian' => $waktu('Waktu Diketahui'),
                    'frekuensi'      => $frekuensi(),
                ],
            ],
            Pengaduan::KATEGORI_LAYANAN_IT_SSO => [
                'subjek' => 'Contoh: Tidak bisa login SSO setelah ganti kata sandi',
                'pesan'  => 'Jelaskan langkah yang Anda lakukan dan pesan galat yang muncul…',
                'fields' => [
                    'objek'          => ['label' => 'Layanan / Aplikasi', 'type' => 'text', 'placeholder' => 'Contoh: SSO Undip, WiFi, e-mail kampus'],
                    'lokasi'         => ['label' => 'Lokasi Akses', 'type' => 'text', 'placeholder' => 'Contoh: Lab Komputer lt. 2, atau dari rumah'],
                    'waktu_kejadian' => $waktu('Waktu Kendala'),
                    'frekuensi'      => $frekuensi(),
                ],
            ],
            Pengaduan::KATEGORI_KEGIATAN_KEMAHASISWAAN => [
                'subjek' => 'Contoh: Proposal kegiatan belum mendapat tanggapan',
                'pesan'  => 'Ceritakan kegiatan yang dimaksud dan kendala yang dialami…',
                'fields' => [
                    'objek'          => ['label' => 'Nama Kegiatan / Organisasi', 'type' => 'text', 'placeholder' => 'Contoh: Makrab HIMASKOM 2026'],
                    'lokasi'         => ['label' => 'Lokasi Kegiatan', 'type' => 'text', 'placeholder' => 'Contoh: Gedung Kuliah Bersama'],
                    'waktu_kejadian' => $waktu('Waktu Kegiatan'),
                    'frekuensi'      => $frekuensi(),
                ],
            ],
            Pengaduan::KATEGORI_KEAMANAN_KETERTIBAN => [
                'subjek' => 'Contoh: Helm hilang di parkiran motor',
                'pesan'  => 'Ceritakan kronologi kejadian sejelas mungkin…',
                'fields' => [
                    'lokasi'         => ['label' => 'Lokasi Kejadian', 'type' => 'text', 'placeholder' => 'Contoh: Parkiran motor Gedung Kuliah Bersama'],
                    'pihak_terkait'  => ['label' => 'Pihak yang Terlibat', 'type' => 'select', 'options' => self::PIHAK],
                    'waktu_kejadian' => $waktu('Waktu Kejadian'),
                    'frekuensi'      => $frekuensi(),
                ],
            ],
            Pengaduan::KATEGORI_KESEHATAN_KONSELING => [
                'subjek' => 'Contoh: Jadwal konseling sulit didapat',
                'pesan'  => 'Ceritakan layanan yang Anda butuhkan dan kendala yang dialami…',
                'fields' => [
                    'objek'          => ['label' => 'Layanan yang Diadukan', 'type' => 'text', 'placeholder' => 'Contoh: Konseling, klinik kampus, rujukan'],
                    'nama_tendik'    => ['label' => 'Petugas / Unit Layanan', 'type' => 'text', 'placeholder' => 'Contoh: Poliklinik Undip'],
                    'waktu_kejadian' => $waktu('Waktu Layanan'),
                    'frekuensi'      => $frekuensi(),
                ],
            ],
            Pengaduan::KATEGORI_TINDAKAN_TIDAK_MENYENANGKAN => [
                'subjek' => 'Contoh: Perlakuan tidak pantas saat kegiatan kelompok',
                'pesan'  => 'Ceritakan apa yang terjadi sejauh Anda nyaman. Anda tidak wajib menyebutkan nama siapa pun…',
                'fields' => [
                    'lokasi'         => ['label' => 'Lokasi Kejadian', 'type' => 'text', 'placeholder' => 'Contoh: Ruang kelas, grup chat, luar kampus'],
                    'pihak_terkait'  => ['label' => 'Status Pihak Terlapor', 'type' => 'select', 'options' => self::PIHAK],
                    'waktu_kejadian' => $waktu('Waktu Kejadian'),
                    'frekuensi'      => $frekuensi(),
                ],
            ],
        ];
    }

    /**
     * Set pertanyaan satu kategori (kunci lama ikut dipetakan); kosong bila tak dikenal.
     */
    public static function untuk(string $kategori): array
    {
        return self::semua()[Pengaduan::normalizeKategori($kategori)] ?? ['subjek' => '', 'pesan' => '', 'fields' => []];
    }

    /** @return array<int, string> kunci jawaban milik satu kategori */
    public static function kunci(string $kategori): array
    {
        return array_keys(self::untuk($kategori)['fields']);
    }

    /**
     * Baris label–nilai untuk Konfirmasi/Detail/Lacak: pertanyaan kategori tiket
     * (selalu tampil, kosong = "—"), lalu jawaban tiket lama yang tersimpan di
     * luar set kategorinya (hanya bila berisi).
     *
     * @return array<int, array{label: string, value: ?string}>
     */
    public static function barisInfo(string $kategori, array $template): array
    {
        if (empty($template['waktu_kejadian']) && !empty($template['tanggal_kejadian'])) {
            $template['waktu_kejadian'] = $template['tanggal_kejadian'];
        }

        $fields = self::untuk($kategori)['fields'];
        $rows = [];

        foreach ($fields as $key => $field) {
            $rows[] = ['label' => $field['label'], 'value' => self::nilai($key, $template[$key] ?? null)];
        }

        foreach (self::LABEL_BAWAAN as $key => $label) {
            if (!isset($fields[$key]) && !empty($template[$key])) {
                $rows[] = ['label' => $label, 'value' => self::nilai($key, $template[$key])];
            }
        }

        return $rows;
    }

    private static function nilai(string $key, mixed $value): ?string
    {
        if ($value === null || $value === '' || is_array($value)) {
            return null;
        }

        if ($key === 'waktu_kejadian') {
            try {
                return \Carbon\Carbon::parse($value)->translatedFormat('d F Y, H:i');
            } catch (\Throwable) {
                // tampilkan apa adanya
            }
        }

        return (string) $value;
    }
}
