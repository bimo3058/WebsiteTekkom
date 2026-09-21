<?php

namespace Modules\ManajemenMahasiswa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Modules\ManajemenMahasiswa\Models\Pengaduan;

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
            'template.hal_aduan'        => ['required', 'string', 'max:1000'],
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
            // url:http,https menolak skema lain (mis. javascript:) secara eksplisit
            'template.link_bukti'       => ['nullable', 'url:http,https', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'kategori.in'                => 'Kategori pengaduan tidak dikenali.',
            'template.kronologi.min'     => 'Kronologi minimal 20 karakter agar dapat ditindaklanjuti.',
            'template.link_bukti.url'    => 'Link bukti harus berupa tautan http:// atau https:// yang valid.',
            'template.waktu_kejadian.date' => 'Waktu kejadian harus berupa tanggal yang valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'template.judul'          => 'judul',
            'template.hal_aduan'      => 'hal aduan',
            'template.kronologi'      => 'kronologi',
            'template.waktu_kejadian' => 'waktu kejadian',
            'template.link_bukti'     => 'link bukti',
        ];
    }

    /**
     * Template yang sudah divalidasi, dipangkas ke daftar key yang dikenal,
     * dan di-trim. Menggantikan normalizeTemplate() yang dulu ada di controller.
     */
    public function normalizedTemplate(): array
    {
        $template = Arr::only((array) $this->validated('template'), [
            'judul',
            'hal_aduan',
            'kronologi',
            'angkatan',
            'lokasi',
            'waktu_kejadian',
            'tanggal_kejadian',
            'mata_kuliah',
            'nama_dosen',
            'nama_tendik',
            'frekuensi',
            'link_bukti',
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
