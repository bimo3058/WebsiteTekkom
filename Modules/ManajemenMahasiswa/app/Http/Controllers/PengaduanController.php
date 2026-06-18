<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Modules\ManajemenMahasiswa\Models\Pengaduan;
use Modules\ManajemenMahasiswa\Models\PengaduanDelegasi;
use Modules\ManajemenMahasiswa\Services\PengaduanService;

class PengaduanController extends Controller
{
    private const VIEWER_ROLES = [
        'mahasiswa',
        'dosen',
        'gpm',
        'kaprodi',
        'admin',
        'superadmin',
        'admin_kemahasiswaan',
    ];

    private const STAFF_VIEW_ROLES = [
        'dosen',
        'gpm',
        'kaprodi',
        'admin',
        'superadmin',
        'admin_kemahasiswaan',
    ];

    private const REPLY_ROLES = [
        'gpm',
        'kaprodi',
        'admin',
        'superadmin',
        'admin_kemahasiswaan',
    ];

    private const DELETE_ROLES = [
        'gpm',
        'kaprodi',
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
        $allowedStatus = [
            Pengaduan::STATUS_BARU,
            Pengaduan::STATUS_DIBACA,
            Pengaduan::STATUS_DIDELEGASIKAN,
            Pengaduan::STATUS_SELESAI,
        ];

        $isDosenOnly = method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['dosen', 'dosen_koordinator']) && !$user->hasAnyRole(['admin', 'superadmin', 'admin_kemahasiswaan', 'gpm', 'kaprodi']);

        $query = Pengaduan::query();
        if ($isStaff) {
            $query->with(['pelapor', 'delegasiAktif.delegatedTo']);
            if ($isDosenOnly) {
                // Dosen hanya bisa melihat tiket yang didelegasikan ke mereka
                $query->whereHas('delegasi', function($q) use ($user) {
                    $q->where('delegated_to', $user->id);
                });
            }
        } else {
            // Mahasiswa hanya bisa melihat tiket non-anonim di daftar ini
            $query->where('user_id', $user->id)
                  ->where('is_anonim', false);
        }

        $query->where('status', '!=', Pengaduan::STATUS_DRAFT);

        // ── Base query untuk stats (sebelum filter user) ─────────
        $baseQuery = clone $query;

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

        $belumDijawabCount = (clone $baseQuery)
            ->where('status', '!=', Pengaduan::STATUS_SELESAI)
            ->count();

        $selesaiCount = (clone $baseQuery)
            ->where('status', Pengaduan::STATUS_SELESAI)
            ->count();

        // ── Stats lanjutan per role ──────────────────────────────
        $summaryStats = [];

        if ($isStaff) {
            $perluDitindakCount = (clone $baseQuery)
                ->where('status', Pengaduan::STATUS_BARU)
                ->count();

            $baruCount = $perluDitindakCount;

            $menungguDosenCount = (clone $baseQuery)
                ->where('status', Pengaduan::STATUS_DIDELEGASIKAN)
                ->count();

            $totalNonDraft = (clone $baseQuery)->count();
            $ditanganiCount = (clone $baseQuery)
                ->where('status', Pengaduan::STATUS_SELESAI)
                ->count();
            $responsivitas = $totalNonDraft > 0 ? round($ditanganiCount / $totalNonDraft * 100) : 0;

            // Distribusi per kategori (Semua kategori)
            $kategoriDistribusi = (clone $baseQuery)
                ->selectRaw("kategori, COUNT(*) as total")
                ->groupBy('kategori')
                ->orderByDesc('total')
                ->pluck('total', 'kategori');

            $summaryStats = compact(
                'perluDitindakCount', 'baruCount',
                'menungguDosenCount',
                'responsivitas', 'ditanganiCount', 'totalNonDraft',
                'kategoriDistribusi',
            );
        } else {
            // Mahasiswa stats disederhanakan
            $summaryStats = [];
        }

        $pengaduan = $query
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $kategoriOptions = $this->kategoriMetaNew();
        $statusOptions = [
            Pengaduan::STATUS_BARU => 'Baru',
            Pengaduan::STATUS_DIBACA => 'Diproses',
            Pengaduan::STATUS_DIDELEGASIKAN => 'Didelegasikan',
            Pengaduan::STATUS_SELESAI => 'Selesai',
        ];

        $isDosenOnlyView = $isDosenOnly;

        return view('manajemenmahasiswa::pengaduan.index', compact(
            'pengaduan',
            'isStaff',
            'isDosenOnlyView',
            'canCreate',
            'canDelete',
            'filters',
            'kategoriOptions',
            'statusOptions',
            'belumDijawabCount',
            'selesaiCount',
            'summaryStats',
        ));
    }

    public function jalur(Request $request)
    {
        $user = $request->user();
        $this->ensureMahasiswa($user);

        return view('manajemenmahasiswa::pengaduan.jalur');
    }

    public function create(Request $request)
    {
        $user = $request->user();

        $this->ensureMahasiswa($user);

        // Redirect ke halaman pilihan jalur jika tidak ada jalur valid
        $jalur = $request->query('jalur');
        if (!in_array($jalur, ['reguler', 'konfidensial'])) {
            return redirect()->route('manajemenmahasiswa.pengaduan.jalur');
        }

        $isStaff = false;

        $kategoriList = $this->kategoriMetaNew();

        $dosenList = User::whereHas('roles', fn($q) => $q->whereIn('name', ['dosen', 'dosen_koordinator']))
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

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

        $request->flash();

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

        if ($pengaduan->is_anonim) {
            return redirect()
                ->route('manajemenmahasiswa.pengaduan.track.success', ['token' => $pengaduan->anon_token]);
        }

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

        $isDosenOnly = method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['dosen', 'dosen_koordinator']) && !$user->hasAnyRole(['admin', 'superadmin', 'admin_kemahasiswaan', 'gpm', 'kaprodi']);
        if ($isDosenOnly) {
            $hasDelegation = $pengaduan->delegasi()->where('delegated_to', $user->id)->exists();
            if (!$hasDelegation) {
                abort(403, 'Anda tidak memiliki akses ke pengaduan ini.');
            }
        }

        if ($isStaff) {
            $this->pengaduanService->markRead($pengaduan, $user->id);
        }

        $kategoriUtama = Pengaduan::normalizeKategori((string)$pengaduan->kategori);
        $kategoriLabel = data_get($this->kategoriMetaNew(), $kategoriUtama . '.label')
            ?? ucwords(str_replace('_', ' ', $kategoriUtama));

        // Untuk modal delegasi: daftar dosen yang bisa dipilih
        $dosenList = [];
        if ($canReply) {
            $dosenList = User::whereHas('roles', fn($q) => $q->whereIn('name', ['dosen', 'dosen_koordinator']))
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        $pengaduan->load(['delegasi.delegatedBy', 'delegasi.delegatedTo', 'logs.actor', 'delegasiAktif', 'delegasiTerakhir.delegatedTo', 'delegasiTerakhir.delegatedBy']);

        $isDelegatedToMe = $pengaduan->delegasiAktif && $pengaduan->delegasiAktif->delegated_to === $user->id && $pengaduan->delegasiAktif->status === 'aktif';

        // Referensi delegasi yang ditampilkan di panel:
        // - delegasiAktif: jika masih berstatus 'aktif' (menunggu respons dosen)
        // - delegasiTerakhir: digunakan untuk membaca tanggapan dosen setelah delegasi selesai/di-forward
        $delegasiPanel = $pengaduan->delegasiAktif ?? $pengaduan->delegasiTerakhir;

        return view('manajemenmahasiswa::pengaduan.show', compact(
            'pengaduan', 'isStaff', 'canReply', 'canDelete', 'kategoriLabel', 'dosenList', 'isDelegatedToMe', 'delegasiPanel'
        ));
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

    public function delegate(Request $request, Pengaduan $pengaduan)
    {
        $user = $request->user();
        $this->ensureViewer($user);
        if (!$this->canReply($user)) {
            abort(403, 'Anda tidak memiliki akses untuk mendelegasikan pengaduan.');
        }
        if ($pengaduan->isSelesai()) {
            abort(403, 'Tiket sudah ditutup.');
        }

        $validated = $request->validate([
            'delegated_to' => ['required', 'integer', 'exists:users,id'],
            'notes_admin'  => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $this->pengaduanService->delegate(
            $pengaduan,
            $user->id,
            (int) $validated['delegated_to'],
            $validated['notes_admin']
        );

        return back()->with('success', 'Pengaduan berhasil didelegasikan.');
    }


    public function closeByAdmin(Request $request, Pengaduan $pengaduan)
    {
        $user = $request->user();
        $this->ensureViewer($user);
        if (!$this->canReply($user)) {
            abort(403);
        }
        if ($pengaduan->isSelesai()) {
            return back()->with('info', 'Tiket sudah selesai.');
        }

        $this->pengaduanService->closeByAdmin($pengaduan, $user->id);

        return back()->with('success', 'Tiket berhasil ditutup.');
    }

    public function markProses(Request $request, Pengaduan $pengaduan)
    {
        $user = $request->user();
        $this->ensureViewer($user);
        if (!$this->canReply($user)) {
            abort(403);
        }

        if ($pengaduan->status !== Pengaduan::STATUS_BARU) {
            return back()->with('info', 'Status tiket tidak valid untuk diproses.');
        }

        $this->pengaduanService->markProses($pengaduan, $user->id);

        return back()->with('success', 'Status tiket berhasil diubah menjadi Diproses.');
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
