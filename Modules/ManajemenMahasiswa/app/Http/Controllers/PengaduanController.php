<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\ManajemenMahasiswa\Http\Requests\PengaduanPayloadRequest;
use Modules\ManajemenMahasiswa\Models\Pengaduan;
use Modules\ManajemenMahasiswa\Models\PengaduanLog;
use Modules\ManajemenMahasiswa\Services\PengaduanService;
use Modules\ManajemenMahasiswa\Support\PerPage;

class PengaduanController extends Controller
{
    /**
     * Role yang berstatus mahasiswa dan boleh membuat pengaduan. Pengurus
     * himpunan tetap mahasiswa; harus sama dengan whitelist route pembuatan.
     */
    public const PELAPOR_ROLES = [
        'mahasiswa',
        'pengurus_himpunan',
        'ketua_himpunan',
        'ketua_bidang',
        'ketua_unit',
        'staff_himpunan',
    ];

    private const VIEWER_ROLES = [
        'mahasiswa',
        'pengurus_himpunan',
        'ketua_himpunan',
        'ketua_bidang',
        'ketua_unit',
        'staff_himpunan',
        'gpm',
        'kaprodi',
        'dpm',
        'admin',
        'superadmin',
        'admin_kemahasiswaan',
        'ketua_departemen',
    ];

    private const STAFF_VIEW_ROLES = [
        'gpm',
        'kaprodi',
        'dpm',
        'admin',
        'superadmin',
        'admin_kemahasiswaan',
        'ketua_departemen',
    ];

    /**
     * Penghapusan dibatasi ke admin & pengelola database saja.
     *
     * gpm/dpm/kaprodi/ketua_departemen sengaja DICABUT: mereka bisa menjadi
     * pihak terlapor, dan tidak boleh mampu menghapus aduan tentang dirinya
     * sendiri berikut seluruh jejak auditnya. GPM tetap penerima aduan PPKS,
     * hanya tidak lagi memegang hak hapus.
     */
    private const DELETE_ROLES = [
        'admin',
        'superadmin',
    ];

    public function __construct(private PengaduanService $pengaduanService)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $this->ensureViewer($user);

        $isStaff = $this->isStaffViewer($user);

        $canCreate = method_exists($user, 'hasAnyRole') && $user->hasAnyRole(self::PELAPOR_ROLES);
        $canDelete = $this->canDelete($user);

        $filters = [
            'q' => trim((string)$request->query('q', '')),
            'kategori' => (string)$request->query('kategori', ''),
            'sort' => $request->query('sort') === 'terlama' ? 'terlama' : 'terbaru',
        ];

        $allowedKategori = Pengaduan::KATEGORI_LIST;

        $query = Pengaduan::query();
        if ($isStaff) {
            $query->with(['pelapor']);
        } else {
            // Mahasiswa hanya bisa melihat tiket non-anonim di daftar ini
            $query->where('user_id', $user->id)
                  ->where('is_anonim', false);
        }

        $query->where('status', '!=', Pengaduan::STATUS_DRAFT);

        // ── Base query untuk stats (sebelum filter) ─────────
        $baseQuery = clone $query;

        if ($filters['kategori'] !== '' && in_array($filters['kategori'], $allowedKategori, true)) {
            $kategoriUtama = $filters['kategori'];
            $kategoriKeys = array_merge([$kategoriUtama], Pengaduan::legacyKeysFor($kategoriUtama));
            $query->whereIn('kategori', $kategoriKeys);
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

        $baruCount = (clone $baseQuery)
            ->where('status', Pengaduan::STATUS_BARU)
            ->count();

        $arah = $filters['sort'] === 'terlama' ? 'asc' : 'desc';

        $pengaduan = $query
            ->orderBy('created_at', $arah)
            ->orderBy('id', $arah)
            ->paginate(PerPage::resolve($request))
            ->withQueryString();

        $pengaduan->getCollection()->each(function ($item) {
            if ($item->is_anonim) {
                $item->setRelation('pelapor', null);
            }
        });

        $kategoriOptions = $this->kategoriMetaNew();

        return view('manajemenmahasiswa::pengaduan.index', compact(
            'pengaduan',
            'isStaff',
            'canCreate',
            'canDelete',
            'filters',
            'kategoriOptions',
            'baruCount',
        ));
    }


    /**
     * Pemilih jalur kini berupa modal di halaman daftar. URL lama dialihkan ke
     * sana supaya tautan/bookmark lama tetap bekerja.
     */
    public function jalur(Request $request)
    {
        $this->ensureMahasiswa($request->user());

        return redirect()->route('manajemenmahasiswa.pengaduan.index', ['buat' => 1]);
    }

    public function create(Request $request)
    {
        $user = $request->user();

        $this->ensureMahasiswa($user);

        // Form ini khusus jalur Reguler. Jalur Konfidensial memakai alur
        // magic link terpisah yang dimulai dari modal pemilih jalur.
        if ($request->query('jalur') !== 'reguler') {
            return redirect()->route('manajemenmahasiswa.pengaduan.index', ['buat' => 1]);
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


    public function confirm(PengaduanPayloadRequest $request)
    {
        $this->ensureMahasiswa($request->user());

        $request->flash();

        return view('manajemenmahasiswa::pengaduan.confirm', [
            'payload' => [
                'is_anonim' => $request->isAnonim(),
                'kategori' => $request->validated('kategori'),
                'template' => $request->normalizedTemplate(),
            ],
        ]);
    }

    public function store(PengaduanPayloadRequest $request)
    {
        $user = $request->user();

        $this->ensureMahasiswa($user);

        $pengaduan = $this->pengaduanService->create(
            userId: $user->id,
            kategori: $request->validated('kategori'),
            isAnonim: $request->isAnonim(),
            template: $request->normalizedTemplate(),
        );

        if ($pengaduan->is_anonim) {
            return redirect()
                ->route('manajemenmahasiswa.pengaduan.track', ['token' => $pengaduan->anon_token])
                ->with('success', 'Pengaduan konfidensial berhasil dikirim. Simpan tautan ini untuk memantau tiket Anda.');
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
        $canDelete = $this->canDelete($user);

        if (!$isStaff && $pengaduan->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke pengaduan ini.');
        }

        // Auto-clear 'baru' saat staff membuka detail (notification-style)
        if ($isStaff) {
            $this->pengaduanService->markRead($pengaduan, $user->id);
        }

        $kategoriUtama = Pengaduan::normalizeKategori((string)$pengaduan->kategori);
        $kategoriLabel = data_get($this->kategoriMetaNew(), $kategoriUtama . '.label')
            ?? ucwords(str_replace('_', ' ', $kategoriUtama));

        $pengaduan->load(['logs.actor']);

        if ($pengaduan->is_anonim) {
            $pengaduan->setRelation('pelapor', null);

            // Sembunyikan identitas pelapor dari log riwayat tiket
            $pengaduan->logs->each(function ($log) use ($pengaduan) {
                if ($log->actor_user_id !== null && (int) $log->actor_user_id === (int) $pengaduan->user_id) {
                    $log->setRelation('actor', null);
                }
            });

            $template = $pengaduan->data_template;
            if (is_array($template)) {
                unset($template['angkatan']);
                $pengaduan->data_template = $template;
            }
        }

        return view('manajemenmahasiswa::pengaduan.show', compact(
            'pengaduan', 'isStaff', 'canDelete', 'kategoriLabel'
        ));
    }



    public function destroy(Request $request, Pengaduan $pengaduan)
    {
        $user = $request->user();

        $this->ensureViewer($user);

        if (!$this->canDelete($user)) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus pengaduan.');
        }

        // Dicatat SEBELUM dihapus. Log adalah satu-satunya jejak siapa yang
        // menghapus; dengan SoftDeletes baris log tidak ikut ter-cascade.
        $this->pengaduanService->logAction(
            $pengaduan,
            $user->id,
            PengaduanLog::ACTION_DIHAPUS,
            'Dihapus oleh ' . ($user->name ?? ('pengguna #' . $user->id))
        );

        $pengaduan->delete();

        return redirect()
            ->route('manajemenmahasiswa.pengaduan.index')
            ->with('success', 'Pengaduan berhasil dihapus.');
    }

    public function toggleTercatat(Request $request, Pengaduan $pengaduan)
    {
        $user = $request->user();
        $this->ensureViewer($user);

        if (!$this->isStaffViewer($user)) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah status pengaduan.');
        }

        // Toggle: jika sudah tercatat → kembalikan ke dibaca, jika belum → tandai tercatat
        if ($pengaduan->status === Pengaduan::STATUS_TERCATAT) {
            $pengaduan->update(['status' => Pengaduan::STATUS_DIBACA]);
            $this->pengaduanService->logAction($pengaduan, $user->id, PengaduanLog::ACTION_BATAL_TERCATAT);
            $message = 'Pengaduan ditandai belum tercatat.';
        } else {
            $pengaduan->update(['status' => Pengaduan::STATUS_TERCATAT]);
            $this->pengaduanService->logAction($pengaduan, $user->id, PengaduanLog::ACTION_TERCATAT);
            $message = 'Pengaduan ditandai tercatat.';
        }

        // Support AJAX request dari checkbox di tabel
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => $pengaduan->status,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
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

        if (!method_exists($user, 'hasAnyRole') || !$user->hasAnyRole(self::PELAPOR_ROLES)) {
            abort(403, 'Hanya mahasiswa yang dapat membuat pengaduan.');
        }
    }

    private function isStaffViewer($user): bool
    {
        return method_exists($user, 'hasAnyRole')
            ? $user->hasAnyRole(self::STAFF_VIEW_ROLES)
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
}

