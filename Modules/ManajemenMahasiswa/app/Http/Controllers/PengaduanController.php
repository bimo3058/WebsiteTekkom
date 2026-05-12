<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Modules\ManajemenMahasiswa\Models\Pengaduan;
use Modules\ManajemenMahasiswa\Services\PengaduanService;

class PengaduanController extends Controller
{
    private const VIEWER_ROLES = [
        'mahasiswa',
        'dosen',
        'gpm',
        'admin',
        'superadmin',
        'admin_kemahasiswaan',
    ];

    private const STAFF_VIEW_ROLES = [
        'dosen',
        'gpm',
        'admin',
        'superadmin',
        'admin_kemahasiswaan',
    ];

    private const REPLY_ROLES = [
        'gpm',
        'admin',
        'superadmin',
        'admin_kemahasiswaan',
    ];

    private const DELETE_ROLES = [
        'gpm',
        'admin',
        'superadmin',
        'admin_kemahasiswaan',
    ];

    public function __construct(private PengaduanService $pengaduanService)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $this->ensureViewer($user);

        $isStaff = $this->isStaffViewer($user);

        $canCreate = method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['mahasiswa']);
        $canDelete = $this->canDelete($user);

        $filters = [
            'q' => trim((string)$request->query('q', '')),
            'kategori' => (string)$request->query('kategori', ''),
            'status' => (string)$request->query('status', ''),
        ];

        $allowedKategori = Pengaduan::KATEGORI_LIST;
        $allowedStatus = [Pengaduan::STATUS_BARU, Pengaduan::STATUS_DIBACA, Pengaduan::STATUS_DIJAWAB];

        $query = Pengaduan::query();
        if ($isStaff) {
            $query->with(['pelapor']);
        } else {
            $query->where('user_id', $user->id);
        }

        if ($filters['kategori'] !== '' && in_array($filters['kategori'], $allowedKategori, true)) {
            $kategoriUtama = $filters['kategori'];
            $kategoriKeys = array_merge([$kategoriUtama], Pengaduan::legacyKeysFor($kategoriUtama));
            $query->whereIn('kategori', $kategoriKeys);
        }

        if ($filters['status'] !== '' && in_array($filters['status'], $allowedStatus, true)) {
            $query->where('status', $filters['status']);
        }

        if ($filters['q'] !== '') {
            $q = $filters['q'];
            $query->where(function ($sub) use ($q) {
                if (ctype_digit($q)) {
                    $sub->orWhere('id', (int)$q);
                }

                $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
                $sub->orWhereRaw("data_template->>'judul' ILIKE ?", [$like])
                    ->orWhereRaw("data_template->>'hal_aduan' ILIKE ?", [$like])
                    ->orWhereRaw("data_template->>'kronologi' ILIKE ?", [$like]);
            });
        }

        $belumDijawabCount = (clone $query)
            ->where('status', '!=', Pengaduan::STATUS_DIJAWAB)
            ->count();

        $pengaduan = $query
            ->orderByDesc('created_at')
            ->paginate($isStaff ? 20 : 15)
            ->withQueryString();

        $kategoriOptions = $this->kategoriMetaNew();
        $statusOptions = [
            Pengaduan::STATUS_BARU => 'Baru',
            Pengaduan::STATUS_DIBACA => 'Dibaca',
            Pengaduan::STATUS_DIJAWAB => 'Dijawab',
        ];

        return view('manajemenmahasiswa::pengaduan.index', compact(
            'pengaduan',
            'isStaff',
            'canCreate',
            'canDelete',
            'filters',
            'kategoriOptions',
            'statusOptions',
            'belumDijawabCount',
        ));
    }

    public function create(Request $request)
    {
        $user = $request->user();

        $this->ensureMahasiswa($user);

        $isStaff = false;

        $kategoriList = $this->kategoriMetaNew();

        $dosenList = [
            'Prof. Dr. Adian Fatchur Rochim, S.T., M.T.',
            'Prof. Dr. Ir. R. Rizal Isnanto, S.T., M.M., M.T., IPU, ASEAN Eng.',
            'Dr. Oky Dwi Nurhayati, S.T., M.T.',
            'Agung Budi Prasetijo, S.T., M.I.T., Ph.D.',
            'Dr. Maman Somantri, S.T., M.T.',
            'Rinta Kridalukmana, S.Kom., M.T., Ph.D.',
            'Kuntoro Adi Nugroho, S.T., M.Eng., Ph.D.',
            'Yudi Eko Windarto, S.T., M.Kom.',
            'Dr. Delphi Hanggoro, S.T., M.T.',
            'Dania Eridani, S.T., M.Eng.',
            'Ike Pertiwi Windasari, S.T., M.T.',
            'Eko Didik Widianto, S.T., M.T.',
            'Kurniawan Teguh Martono, S.T., M.T.',
            'Risma Septiana, S.T., M.Eng.',
            'Adnan Fauzi, S.T., M.Kom.',
            'Patricia Evericho Mountaines, S.T., M.Cs.',
            'Bellia Dwi Cahya Putri, S.T., M.T.',
            'Ilmam Fauzi Hashbil Alim, S.T., M.Kom.',
            'Erwin Adriono, S.T., M.T.',
            'Arseto Satriyo Nugroho, S.T., M.Eng.',
        ];

        $frekuensiList = [
            'Sekali' => 'Sekali',
            'Kadang-kadang' => 'Kadang-kadang',
            'Sering' => 'Sering',
            'Hampir Setiap Pertemuan Kuliah' => 'Hampir Setiap Pertemuan Kuliah',
        ];

        return view('manajemenmahasiswa::pengaduan.create', compact('kategoriList', 'dosenList', 'frekuensiList', 'isStaff'));
    }

    public function confirm(Request $request)
    {
        $user = $request->user();

        $this->ensureMahasiswa($user);

        $validated = $this->validatePengaduanPayload($request);

        $template = $this->normalizeTemplate($validated['template']);

        return view('manajemenmahasiswa::pengaduan.confirm', [
            'payload' => [
                'is_anonim' => (bool)($validated['is_anonim'] ?? false),
                'kategori' => $validated['kategori'],
                'template' => $template,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $this->ensureMahasiswa($user);

        $validated = $this->validatePengaduanPayload($request);

        $template = $this->normalizeTemplate($validated['template']);

        $pengaduan = $this->pengaduanService->create(
            userId: $user->id,
            kategori: $validated['kategori'],
            isAnonim: (bool)($validated['is_anonim'] ?? false),
            template: $template,
        );

        return redirect()
            ->route('manajemenmahasiswa.pengaduan.show', $pengaduan->id)
            ->with('success', 'Pengaduan berhasil dikirim.');
    }

    public function show(Request $request, Pengaduan $pengaduan)
    {
        $user = $request->user();

        $this->ensureViewer($user);

        $isStaff = $this->isStaffViewer($user);
        $canReply = $this->canReply($user);
        $canDelete = $this->canDelete($user);

        if (!$isStaff && $pengaduan->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke pengaduan ini.');
        }

        if ($isStaff) {
            $this->pengaduanService->markRead($pengaduan, $user->id);
        }

        $kategoriUtama = Pengaduan::normalizeKategori((string)$pengaduan->kategori);
        $kategoriLabel = data_get($this->kategoriMetaNew(), $kategoriUtama . '.label')
            ?? ucwords(str_replace('_', ' ', $kategoriUtama));

        return view('manajemenmahasiswa::pengaduan.show', compact('pengaduan', 'isStaff', 'canReply', 'canDelete', 'kategoriLabel'));
    }

    public function reply(Request $request, Pengaduan $pengaduan)
    {
        $user = $request->user();

        $this->ensureViewer($user);

        if (!$this->canReply($user)) {
            abort(403, 'Anda tidak memiliki akses untuk menjawab pengaduan.');
        }

        $validated = $request->validate([
            'jawaban' => ['required', 'string', 'min:5', 'max:5000'],
        ]);

        $this->pengaduanService->reply($pengaduan, $user->id, $validated['jawaban']);

        return back()->with('success', 'Jawaban berhasil dikirim.');
    }

    public function destroy(Request $request, Pengaduan $pengaduan)
    {
        $user = $request->user();

        $this->ensureViewer($user);

        if (!$this->canDelete($user)) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus pengaduan.');
        }

        $pengaduan->delete();

        return redirect()
            ->route('manajemenmahasiswa.pengaduan.index')
            ->with('success', 'Pengaduan berhasil dihapus.');
    }

    private function ensureViewer($user): void
    {
        if (!$user) {
            abort(403, 'Anda tidak memiliki akses ke layanan pengaduan.');
        }

        if (!method_exists($user, 'hasAnyRole') || !$user->hasAnyRole(self::VIEWER_ROLES)) {
            abort(403, 'Anda tidak memiliki akses ke layanan pengaduan.');
        }
    }

    private function ensureMahasiswa($user): void
    {
        if (!$user) {
            abort(403, 'Anda tidak memiliki akses untuk membuat pengaduan.');
        }

        if (!method_exists($user, 'hasAnyRole') || !$user->hasAnyRole(['mahasiswa'])) {
            abort(403, 'Hanya mahasiswa yang dapat membuat pengaduan.');
        }
    }

    private function isStaffViewer($user): bool
    {
        return method_exists($user, 'hasAnyRole')
            ? $user->hasAnyRole(self::STAFF_VIEW_ROLES)
            : false;
    }

    private function canReply($user): bool
    {
        return method_exists($user, 'hasAnyRole')
            ? $user->hasAnyRole(self::REPLY_ROLES)
            : false;
    }

    private function canDelete($user): bool
    {
        return method_exists($user, 'hasAnyRole')
            ? $user->hasAnyRole(self::DELETE_ROLES)
            : false;
    }

    private function kategoriMetaNew(): array
    {
        return [
            Pengaduan::KATEGORI_AKADEMIK_ADMINISTRASI => [
                'label' => 'Akademik dan Administrasi',
                'example' => 'KRS, transkrip, surat-menyurat, masalah administrasi akademik',
            ],
            Pengaduan::KATEGORI_PROSES_PEMBELAJARAN => [
                'label' => 'Proses Pembelajaran di Kelas',
                'example' => 'Metode mengajar, penilaian, materi tidak sesuai, jadwal perkuliahan',
            ],
            Pengaduan::KATEGORI_FASILITAS_KAMPUS => [
                'label' => 'Fasilitas Kampus (Sarana dan Prasarana)',
                'example' => 'AC/infocus rusak, kursi/kelas, kebersihan, lab/praktikum',
            ],
            Pengaduan::KATEGORI_LAYANAN_IT_SSO => [
                'label' => 'Layanan IT dan Akun SSO',
                'example' => 'SSO/login, email kampus, akses WiFi, LMS/portal bermasalah',
            ],
            Pengaduan::KATEGORI_KEGIATAN_KEMAHASISWAAN => [
                'label' => 'Kegiatan Kemahasiswaan',
                'example' => 'UKM/Himpunan, proposal kegiatan, perizinan, pendanaan',
            ],
            Pengaduan::KATEGORI_KEAMANAN_KETERTIBAN => [
                'label' => 'Keamanan dan Ketertiban Kampus',
                'example' => 'Parkir, kehilangan barang, keamanan area kampus, keributan',
            ],
            Pengaduan::KATEGORI_KESEHATAN_KONSELING => [
                'label' => 'Layanan Kesehatan dan Konseling Mahasiswa',
                'example' => 'Konseling, kesehatan mental, layanan klinik kampus, rujukan',
            ],
            Pengaduan::KATEGORI_TINDAKAN_TIDAK_MENYENANGKAN => [
                'label' => 'Tindakan Tidak Menyenangkan di Lingkungan Kampus',
                'example' => 'Perundungan, pelecehan, intimidasi, perlakuan tidak pantas',
            ],
        ];
    }

    // kategoriMetaAll dihapus: hanya 8 kategori utama yang ditampilkan pada UI.

    private function validatePengaduanPayload(Request $request): array
    {
        return $request->validate([
            'is_anonim' => ['nullable', 'boolean'],
            'kategori' => ['required', 'string', 'in:' . implode(',', Pengaduan::KATEGORI_LIST)],
            'template' => ['required', 'array'],
            'template.judul' => ['required', 'string', 'max:255'],
            'template.hal_aduan' => ['required', 'string', 'max:1000'],
            'template.kronologi' => ['required', 'string', 'min:20', 'max:5000'],
            'template.angkatan' => ['nullable', 'string', 'max:20'],
            'template.lokasi' => ['nullable', 'string', 'max:255'],
            'template.waktu_kejadian' => ['nullable', 'date'],
            // Backward compatibility: older form/key
            'template.tanggal_kejadian' => ['nullable', 'date'],
            'template.mata_kuliah' => ['nullable', 'string', 'max:255'],
            'template.nama_dosen' => ['nullable', 'string', 'max:255'],
            'template.nama_tendik' => ['nullable', 'string', 'max:255'],
            'template.frekuensi' => ['nullable', 'string', 'max:100'],
            'template.link_bukti' => ['nullable', 'url', 'max:2048'],
        ]);
    }

    private function normalizeTemplate(array $template): array
    {
        $normalized = Arr::only($template, [
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

        if (!isset($normalized['waktu_kejadian']) && isset($normalized['tanggal_kejadian'])) {
            $normalized['waktu_kejadian'] = $normalized['tanggal_kejadian'];
        }

        return $normalized;
    }
}
