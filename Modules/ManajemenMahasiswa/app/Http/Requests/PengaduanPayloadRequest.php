<?php

namespace Modules\ManajemenMahasiswa\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Modules\ManajemenMahasiswa\Models\Pengaduan;
use Modules\ManajemenMahasiswa\Support\Honeypot;
use Modules\ManajemenMahasiswa\Support\PengaduanBukti;

/**
 * Aturan validasi tunggal untuk KEDUA jalur pengaduan (Reguler & Konfidensial).
 *
 * Sebelumnya jalur konfidensial hanya memvalidasi 5 field tetapi menyimpan 11,
 * sehingga link_bukti dan waktu_kejadian masuk mentah ke database. Akibatnya:
 * skema javascript: bisa tersimpan lalu dirender sebagai href, dan teks
 * sembarang pada waktu_kejadian membuat Carbon::parse melempar 500 permanen
 * di halaman detail tiket. Semua jalur kini melewati aturan yang sama di sini.
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
        return [
            'is_anonim'                 => ['nullable', 'boolean'],
            'kategori'                  => ['required', 'string', 'in:' . implode(',', Pengaduan::KATEGORI_LIST)],
            'template'                  => ['required', 'array'],
            'template.judul'            => ['required', 'string', 'max:255'],
            'template.kronologi'        => ['required', 'string', 'min:20', 'max:5000'],
            'template.angkatan'         => ['nullable', 'string', 'max:20'],
            'template.lokasi'           => ['nullable', 'string', 'max:255'],
            'template.waktu_kejadian'   => ['nullable', 'date'],
            // Backward compatibility: key lama dari form versi sebelumnya
            'template.tanggal_kejadian' => ['nullable', 'date'],
            'template.mata_kuliah'      => ['nullable', 'string', 'max:255'],
            'template.nama_dosen'       => ['nullable', 'string', 'max:255'],
            'template.nama_tendik'      => ['nullable', 'string', 'max:255'],
            'template.frekuensi'        => ['nullable', 'string', 'max:100'],
            // Bukti berupa berkas PDF (bukan link Drive yang membocorkan akun
            // pemilik). Hanya diunggah pada langkah konfirmasi; lihat PengaduanBukti.
            'bukti'                     => ['nullable', 'array', 'max:' . PengaduanBukti::MAX_FILES],
            'bukti.*'                   => ['file', 'mimes:' . implode(',', PengaduanBukti::MIMES), 'max:' . PengaduanBukti::MAX_KB],
        ];
    }

    public function messages(): array
    {
        return [
            'kategori.in'                => 'Kategori pengaduan tidak dikenali.',
            'template.kronologi.min'     => 'Pesan minimal 20 karakter agar dapat ditindaklanjuti.',
            'template.waktu_kejadian.date' => 'Waktu kejadian harus berupa tanggal yang valid.',
            'bukti.max'                  => 'Bukti dukung maksimal ' . PengaduanBukti::MAX_FILES . ' berkas.',
            'bukti.*.mimes'              => 'Bukti dukung harus berupa berkas PDF.',
            'bukti.*.max'                => 'Ukuran tiap bukti dukung maksimal ' . (PengaduanBukti::MAX_KB / 1024) . ' MB.',
            'bukti.*.uploaded'           => 'Bukti dukung gagal diunggah. Coba lagi dengan berkas yang lebih kecil.',
        ];
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
        });
    }

    /**
     * Template yang sudah divalidasi, dipangkas ke daftar key yang dikenal,
     * dan di-trim. Menggantikan normalizeTemplate() yang dulu ada di controller.
     */
    public function normalizedTemplate(): array
    {
        $template = Arr::only((array) $this->validated('template'), [
            'judul',
            'kronologi',
            'angkatan',
            'lokasi',
            'waktu_kejadian',
            'tanggal_kejadian',
            'mata_kuliah',
            'nama_dosen',
            'nama_tendik',
            'frekuensi',
        ]);

        if (!isset($template['waktu_kejadian']) && isset($template['tanggal_kejadian'])) {
            $template['waktu_kejadian'] = $template['tanggal_kejadian'];
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
}
