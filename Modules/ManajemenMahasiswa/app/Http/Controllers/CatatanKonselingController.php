<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\ManajemenMahasiswa\Models\CatatanKonseling;
use Modules\ManajemenMahasiswa\Support\PerPage;

/**
 * Catatan Konseling — buku catatan GPM. Hanya GPM & superadmin (dijaga
 * middleware route); mahasiswa tidak punya akses dan tidak tertaut sama sekali.
 */
class CatatanKonselingController extends Controller
{
    public function index(Request $request)
    {
        // Opsi "semua" di panel Filter berarti tanpa filter.
        $pilihan = fn (string $key) => ($v = trim((string) $request->query($key, ''))) === 'semua' ? '' : $v;

        $filters = [
            'q'        => trim((string) $request->query('q', '')),
            'kategori' => $pilihan('kategori'),
            'status'   => $pilihan('status'),
        ];

        $query = CatatanKonseling::query()->with('pencatat:id,name');

        if ($filters['q'] !== '') {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $filters['q']) . '%';
            $query->where(function ($sub) use ($like) {
                $sub->where('nama_mahasiswa', 'ilike', $like)
                    ->orWhere('nim', 'ilike', $like);
            });
        }

        if ($filters['kategori'] !== '' && array_key_exists($filters['kategori'], CatatanKonseling::KATEGORI_KASUS)) {
            $query->where('kategori_kasus', $filters['kategori']);
        }

        if ($filters['status'] !== '' && array_key_exists($filters['status'], CatatanKonseling::STATUS_KASUS)) {
            $query->where('status_kasus', $filters['status']);
        }

        $catatan = $query->orderByDesc('tanggal')->orderByDesc('id')
            ->paginate(PerPage::resolve($request))
            ->withQueryString();

        $ringkasan = [
            'total'     => CatatanKonseling::count(),
            'bulan_ini' => CatatanKonseling::whereBetween('tanggal', [now()->startOfMonth(), now()->endOfMonth()])->count(),        ];

        $kategoriList = CatatanKonseling::KATEGORI_KASUS;
        $statusList = CatatanKonseling::STATUS_KASUS;

        return view('manajemenmahasiswa::konseling.index', compact('catatan', 'filters', 'ringkasan', 'kategoriList', 'statusList'));
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);
        $data['dicatat_oleh'] = $request->user()->id;

        CatatanKonseling::create($data);

        return redirect()->route('manajemenmahasiswa.konseling.index')
            ->with('success', 'Catatan konseling berhasil disimpan.');
    }

    public function update(Request $request, CatatanKonseling $catatan)
    {
        $catatan->update($this->validasi($request));

        return back()->with('success', 'Catatan konseling berhasil diperbarui.');
    }

    public function destroy(CatatanKonseling $catatan)
    {
        $catatan->delete();

        return back()->with('success', 'Catatan konseling berhasil dihapus.');
    }

    private function validasi(Request $request): array
    {
        $tahunIni = now()->year;

        return $request->validate([
            'nama_mahasiswa' => ['required', 'string', 'max:150'],
            'nim'            => ['nullable', 'string', 'max:30'],
            'angkatan'       => [
                'bail', 'nullable', 'integer', 'min:1990', 'max:' . $tahunIni,
                // Mahasiswa baru bisa dikonseling setelah angkatannya masuk:
                // angkatan tidak boleh lebih baru dari tahun tanggal konseling.
                function (string $attribute, mixed $value, \Closure $fail) use ($request) {
                    $tanggal = strtotime((string) $request->input('tanggal'));
                    if ($tanggal !== false && (int) $value > (int) date('Y', $tanggal)) {
                        $fail('Angkatan ' . $value . ' belum masuk kuliah pada tanggal konseling tersebut.');
                    }
                },
            ],
            'tanggal'           => ['required', 'date', 'before_or_equal:today'],
            'kategori_kasus'    => ['required', Rule::in(array_keys(CatatanKonseling::KATEGORI_KASUS))],
            'status_kasus'      => ['required', Rule::in(array_keys(CatatanKonseling::STATUS_KASUS))],            'kronologi'        => ['nullable', 'string', 'max:5000'],
            'keinginan_pelapor' => ['nullable', 'string', 'max:5000'],
            'tindak_lanjut'     => ['nullable', 'string', 'max:5000'],
            'catatan'           => ['nullable', 'string', 'max:5000'],
        ], [
            'nama_mahasiswa.required'  => 'Nama mahasiswa wajib diisi.',
            'nama_mahasiswa.max'       => 'Nama mahasiswa maksimal 150 karakter.',
            'nim.max'                  => 'NIM maksimal 30 karakter.',
            'angkatan.integer'         => 'Angkatan harus berupa tahun, mis. 2022.',
            'angkatan.min'             => 'Angkatan paling lama 1990.',
            'angkatan.max'             => 'Angkatan ' . ($tahunIni + 1) . ' belum ada. Angkatan terbaru adalah ' . $tahunIni . '.',
            'tanggal.required'         => 'Tanggal konseling wajib diisi.',
            'tanggal.date'             => 'Tanggal konseling tidak valid.',
            'tanggal.before_or_equal'  => 'Tanggal konseling tidak boleh melewati hari ini.',
            'kategori_kasus.required'  => 'Kategori kasus wajib dipilih.',
            'kategori_kasus.in'        => 'Kategori kasus tidak valid.',
            'status_kasus.required'    => 'Status kasus wajib dipilih.',
            'status_kasus.in'          => 'Status kasus tidak valid.',            'kronologi.max'            => 'Kronologi maksimal 5000 karakter.',
            'keinginan_pelapor.max'    => 'Keinginan pelapor maksimal 5000 karakter.',
            'tindak_lanjut.max'        => 'Tindak lanjut maksimal 5000 karakter.',
            'catatan.max'              => 'Catatan maksimal 5000 karakter.',
        ]);
    }
}
