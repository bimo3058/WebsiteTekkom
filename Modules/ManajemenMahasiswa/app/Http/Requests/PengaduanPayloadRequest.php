<?php

namespace Modules\ManajemenMahasiswa\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Modules\ManajemenMahasiswa\Models\Pengaduan;
use Modules\ManajemenMahasiswa\Support\Honeypot;
use Modules\ManajemenMahasiswa\Support\PengaduanBukti;
use Modules\ManajemenMahasiswa\Support\PengaduanPertanyaan;

/**
 * Aturan validasi tunggal untuk form Buat Pengaduan (langkah Konfirmasi & Kirim).
 *
 * Sejak Oktober 2026 hanya ada satu form; pelapor yang ingin anonim cukup
 * mencentang "Sembunyikan identitas saya" (is_anonim). Dulu jalur konfidensial
 * punya alur magic link terpisah yang hanya memvalidasi 5 field tetapi menyimpan
 * 11, sehingga link_bukti dan waktu_kejadian masuk mentah ke database.
 */
class PengaduanPayloadRequest extends FormRequest
{
    /**
     * Otorisasi ditangani middleware role pada route + guard di controller.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'is_anonim'                 => ['nullable', 'boolean'],
            'kategori'                  => ['required', 'string', 'in:' . implode(',', Pengaduan::KATEGORI_LIST)],
            'template'                  => ['required', 'array'],
            'template.judul'            => ['required', 'string', 'max:255'],
            'template.kronologi'        => ['required', 'string', 'min:20', 'max:5000'],
            // Wajib bila akun belum punya angkatan (lihat withValidator).
            'template.angkatan'         => ['nullable', 'digits:4', 'integer', 'between:1990,' . now()->year],
            // Email kontak: wajib untuk pengaduan biasa (pengelola bisa menghubungi
            // pelapor untuk klarifikasi), opsional untuk anonim (diisi bila pelapor
            // bersedia dihubungi).
            'template.email_kontak'     => [$this->boolean('is_anonim') ? 'nullable' : 'required', 'string', 'email:rfc', 'max:255'],
            'template.lokasi'           => ['nullable', 'string', 'max:255'],
            // Kejadian tidak mungkin terjadi di masa depan.
            'template.waktu_kejadian'   => ['nullable', 'date', 'before_or_equal:now'],
            // Backward compatibility: key lama dari form versi sebelumnya
            'template.tanggal_kejadian' => ['nullable', 'date', 'before_or_equal:now'],
            'template.mata_kuliah'      => ['nullable', 'string', 'max:255'],
            'template.nama_dosen'       => ['nullable', 'string', 'max:255'],
            'template.nama_tendik'      => ['nullable', 'string', 'max:255'],
            'template.frekuensi'        => ['nullable', 'string', 'max:100'],
            'template.objek'            => ['nullable', 'string', 'max:255'],
            'template.pihak_terkait'    => ['nullable', 'string', Rule::in(PengaduanPertanyaan::PIHAK)],
            // Bukti berupa berkas PDF (bukan link Drive yang membocorkan akun
            // pemilik), opsional di semua kategori. Hanya diunggah pada langkah
            // konfirmasi; lihat PengaduanBukti.
            'bukti'                     => ['nullable', 'array', 'max:' . PengaduanBukti::MAX_FILES],
            'bukti.*'                   => ['file', 'mimes:' . implode(',', PengaduanBukti::MIMES), 'max:' . PengaduanBukti::MAX_KB],
            // Dicentang saat kembali dari Konfirmasi: berkas yang sudah terunggah dibuang.
            'bukti_hapus'               => ['nullable', 'boolean'],
        ];

        // Pertanyaan Detail Kejadian milik kategori terpilih wajib dijawab semua.
        foreach ($this->pertanyaanKategori() as $key => $field) {
            $rules['template.' . $key] = $this->wajib($rules['template.' . $key]);
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [
            'kategori.required'          => 'Pilih kategori pengaduan.',
            'kategori.in'                => 'Kategori pengaduan tidak dikenali.',
            'template.kronologi.min'     => 'Pesan minimal 20 karakter agar dapat ditindaklanjuti.',
            'template.angkatan.digits'   => 'Angkatan berupa tahun 4 digit, misalnya 2022.',
            'template.angkatan.integer'  => 'Angkatan berupa tahun 4 digit, misalnya 2022.',
            'template.angkatan.between'  => 'Angkatan harus antara 1990 dan ' . now()->year . '.',
            'template.email_kontak.required' => 'Email kontak wajib diisi.',
            'template.email_kontak.email'    => 'Format email kontak tidak valid.',
            'template.email_kontak.max'      => 'Email kontak maksimal 255 karakter.',
            'template.pihak_terkait.in'  => 'Pilihan pihak terkait tidak dikenali.',
            'template.waktu_kejadian.date' => 'Waktu kejadian harus berupa tanggal yang valid.',
            'template.waktu_kejadian.before_or_equal'   => 'Waktu kejadian tidak boleh melebihi waktu saat ini.',
            'template.tanggal_kejadian.before_or_equal' => 'Waktu kejadian tidak boleh melebihi waktu saat ini.',
            'bukti.max'                  => 'Bukti dukung maksimal ' . PengaduanBukti::MAX_FILES . ' berkas.',
            'bukti.*.mimes'              => 'Bukti dukung harus berupa berkas PDF.',
            'bukti.*.max'                => 'Ukuran tiap bukti dukung maksimal ' . (PengaduanBukti::MAX_KB / 1024) . ' MB.',
            'bukti.*.uploaded'           => 'Bukti dukung gagal diunggah. Coba lagi dengan berkas yang lebih kecil.',
        ];

        foreach ($this->pertanyaanKategori() as $key => $field) {
            $messages['template.' . $key . '.required'] = $field['label'] . ' wajib diisi.';
        }

        return $messages;
    }

    public function attributes(): array
    {
        return [
            'template.judul'          => 'subjek',
            'template.kronologi'      => 'pesan',
            'template.waktu_kejadian' => 'waktu kejadian',
            'bukti'                   => 'bukti dukung',
            'bukti.*'                 => 'bukti dukung',
        ];
    }

    /**
     * Honeypot: field jebakan harus kosong dan cap waktu form harus sah.
     * Pesannya sengaja generik agar tidak memberi petunjuk kepada bot.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! Honeypot::passes($this->input(Honeypot::FIELD), $this->input(Honeypot::STAMP_FIELD))) {
                $validator->errors()->add(
                    Honeypot::STAMP_FIELD,
                    'Permintaan tidak dapat diproses. Muat ulang halaman lalu coba lagi.'
                );
            }

            // Angkatan diambil dari akun (pelapor anonim: statistik saja). Akun tanpa
            // data mahasiswa mengetiknya sendiri, kecuali anonim: kolomnya disembunyikan.
            if (! $this->boolean('is_anonim')
                && $this->angkatanAkun() === null
                && blank($this->input('template.angkatan'))) {
                $validator->errors()->add('template.angkatan', 'Angkatan wajib diisi.');
            }
        });
    }

    /**
     * Template yang sudah divalidasi, dipangkas ke field umum + pertanyaan milik
     * kategori terpilih (lihat PengaduanPertanyaan), lalu di-trim. Jawaban kategori
     * lain dibuang supaya tiket tidak menyimpan isian yang tidak relevan.
     */
    public function normalizedTemplate(): array
    {
        $template = (array) $this->validated('template');
        $kategori = (string) $this->validated('kategori');

        if (!isset($template['waktu_kejadian']) && isset($template['tanggal_kejadian'])) {
            $template['waktu_kejadian'] = $template['tanggal_kejadian'];
        }

        $template = Arr::only($template, array_merge(
            ['judul', 'kronologi', 'angkatan', 'email_kontak'],
            PengaduanPertanyaan::kunci($kategori)
        ));

        // Angkatan akun selalu menang atas isian browser.
        $template['angkatan'] = $this->angkatanAkun() ?? ($template['angkatan'] ?? null);

        if (isset($template['email_kontak'])) {
            $template['email_kontak'] = mb_strtolower(trim((string) $template['email_kontak'])) ?: null;
        }

        return array_map(
            fn ($value) => is_string($value) ? trim($value) : $value,
            $template
        );
    }

    public function isAnonim(): bool
    {
        return (bool) $this->validated('is_anonim', false);
    }

    /** Kategori yang sedang dikirim bila dikenali; null bila tidak. */
    private function kategoriValid(): ?string
    {
        $kategori = $this->input('kategori');

        return is_string($kategori) && in_array($kategori, Pengaduan::KATEGORI_LIST, true) ? $kategori : null;
    }

    /** Pertanyaan Detail Kejadian kategori yang sedang dikirim (kosong bila kategori tak dikenal). */
    private function pertanyaanKategori(): array
    {
        $kategori = $this->kategoriValid();

        return $kategori === null ? [] : PengaduanPertanyaan::untuk($kategori)['fields'];
    }

    /** Aturan "nullable" diganti "required". */
    private function wajib(array $rules): array
    {
        return array_merge(['required'], array_values(array_diff($rules, ['nullable'])));
    }

    /** Angkatan dari akun pelapor; null bila akun belum punya data mahasiswa. */
    private function angkatanAkun(): ?string
    {
        $angkatan = optional(optional($this->user())->student)->cohort_year;

        return filled($angkatan) ? (string) $angkatan : null;
    }
}
