<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\ManajemenMahasiswa\Http\Requests\PengaduanPayloadRequest;
use Modules\ManajemenMahasiswa\Models\Pengaduan;
use Modules\ManajemenMahasiswa\Models\PengaduanLog;
use Modules\ManajemenMahasiswa\Services\PengaduanService;
use Modules\ManajemenMahasiswa\Services\PengaduanStatistikService;
use Modules\ManajemenMahasiswa\Support\PengaduanBukti;
use Modules\ManajemenMahasiswa\Support\PengaduanPertanyaan;
use Modules\ManajemenMahasiswa\Support\PengaduanQuota;
use Modules\ManajemenMahasiswa\Support\PerPage;

class PengaduanController extends Controller
{
    /** Kunci bukti "pending" di session untuk form Buat Pengaduan (lihat PengaduanBukti). */
    private const BUKTI_SCOPE = 'reguler';


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
        'superadmin',
        'admin_kemahasiswaan',
        'ketua_departemen',
    ];

    private const STAFF_VIEW_ROLES = [
        'gpm',
        'kaprodi',
        'dpm',
        'superadmin',
        'admin_kemahasiswaan',
        'ketua_departemen',
    ];

    /**
     * Penghapusan dibatasi ke superadmin saja.
     *
     * gpm/dpm/kaprodi/ketua_departemen sengaja DICABUT: mereka bisa menjadi
     * pihak terlapor, dan tidak boleh mampu menghapus aduan tentang dirinya
     * sendiri berikut seluruh jejak auditnya. GPM tetap penerima aduan PPKS,
     * hanya tidak lagi memegang hak hapus.
     */
    private const DELETE_ROLES = [
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

        // Nilai di luar daftar (termasuk "semua") dianggap tanpa filter. Status tidak
        // ditampilkan ke pelapor, jadi filternya hanya berlaku untuk staff — kalau
        // tidak, pelapor bisa menebak status tiketnya lewat ?status= di URL.
        $kategori = (string)$request->query('kategori', '');
        $status = $isStaff ? (string)$request->query('status', '') : '';
        // Filter "melihat pola" (pihak terlapor) khusus staff.
        $pihak = $isStaff ? (string)$request->query('pihak', '') : '';

        $filters = [
            'q' => trim((string)$request->query('q', '')),
            'kategori' => in_array($kategori, Pengaduan::KATEGORI_LIST, true) ? $kategori : '',
            'status' => in_array($status, ['baru', 'tercatat'], true) ? $status : '',
            'pihak' => in_array($pihak, PengaduanPertanyaan::PIHAK, true) ? $pihak : '',
        ];

        // Tautan dari kartu "Masalah berulang" di Statistik: ?cocok[kunci]=jawaban untuk
        // SEMUA pertanyaan teks kategori, dicocokkan persis (sama dengan cara statistik
        // menghitung) dan dibatasi periode statistiknya, supaya daftar berisi tepat
        // pengaduan yang dihitung di sana. Kotak cari menampilkan jawaban pertama; bila
        // isinya diganti, pencarian kembali bebas.
        $filters['cocok'] = [];
        $cocok = $request->query('cocok');
        if ($isStaff && $filters['kategori'] !== '' && is_array($cocok)) {
            $kunciTeks = PengaduanPertanyaan::kunciTeks($filters['kategori']);
            $jawaban = [];
            foreach ($kunciTeks as $kunci) {
                $jawaban[$kunci] = is_string($cocok[$kunci] ?? null) ? PengaduanStatistikService::rapikan($cocok[$kunci]) : '';
            }
            $lengkap = $kunciTeks !== [] && !in_array('', $jawaban, true);
            if ($lengkap && mb_strtolower(PengaduanStatistikService::rapikan($filters['q'])) === mb_strtolower(reset($jawaban))) {
                $filters['cocok'] = $jawaban;
            }
        }
        $periode = (string)$request->query('periode', '');
        $filters['periode'] = $filters['cocok'] !== [] && array_key_exists($periode, PengaduanStatistikService::PERIODE)
            ? $periode : '';

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

        if ($filters['kategori'] !== '') {
            $kategoriUtama = $filters['kategori'];
            $kategoriKeys = array_merge([$kategoriUtama], Pengaduan::legacyKeysFor($kategoriUtama));
            $query->whereIn('kategori', $kategoriKeys);
        }

        // "Baru" = semua yang belum tercatat, sama dengan badge di tabel.
        if ($filters['status'] === 'tercatat') {
            $query->whereIn('status', Pengaduan::TERCATAT_STATUSES);
        } elseif ($filters['status'] === 'baru') {
            $query->whereNotIn('status', Pengaduan::TERCATAT_STATUSES);
        }

        if ($filters['pihak'] !== '') {
            $query->whereRaw("data_template->>'pihak_terkait' = ?", [$filters['pihak']]);
        }

        if ($filters['cocok'] !== []) {
            foreach ($filters['cocok'] as $kunci => $jawaban) {
                $query->whereRaw(
                    "lower(btrim(regexp_replace(data_template->>?, '\\s+', ' ', 'g'))) = ?",
                    [$kunci, mb_strtolower($jawaban)]
                );
            }

            if ($filters['periode'] !== '' && $filters['periode'] !== 'semua') {
                [$mulai, $selesai] = app(PengaduanStatistikService::class)->rentang($filters['periode']);
                $query->whereBetween('created_at', [$mulai, $selesai]);
            }
        } elseif ($filters['q'] !== '') {
            $q = $filters['q'];
            $query->where(function ($sub) use ($q, $isStaff) {
                if (ctype_digit($q)) {
                    $sub->orWhere('id', (int)$q);
                }

                $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
                $sub->orWhereRaw("data_template->>'judul' ILIKE ?", [$like])
                    ->orWhereRaw("data_template->>'hal_aduan' ILIKE ?", [$like])
                    ->orWhereRaw("data_template->>'kronologi' ILIKE ?", [$like]);

                // Lokasi & objek membantu staff melihat laporan berulang di tempat/hal yang sama.
                if ($isStaff) {
                    $sub->orWhereRaw("data_template->>'lokasi' ILIKE ?", [$like])
                        ->orWhereRaw("data_template->>'objek' ILIKE ?", [$like]);
                }
            });
        }

        // Angka di header dihitung tanpa filter, sama seperti $baruCount, supaya
        // pencarian kosong tidak menampilkan "Total 0 pengaduan · 3 baru".
        $totalCount = (clone $baseQuery)->count();
        $baruCount = (clone $baseQuery)
            ->whereNotIn('status', Pengaduan::TERCATAT_STATUSES)
            ->count();

        // Kuota harian habis: tombol "Buat Pengaduan" dinonaktifkan di halaman,
        // bukan baru ditolak setelah pelapor memilih jalur.
        $kuotaPesan = $canCreate && PengaduanQuota::exhausted($user->id)
            ? PengaduanQuota::message($user->id)
            : null;

        $pengaduan = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
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
            'totalCount',
            'baruCount',
            'kuotaPesan',
        ));
    }


    /**
     * Pemilih jalur sudah tidak ada (satu form untuk semua pelapor). URL lama
     * dialihkan ke form supaya tautan/bookmark lama tetap bekerja.
     */
    public function jalur(Request $request)
    {
        $this->ensureMahasiswa($request->user());

        return redirect()->route('manajemenmahasiswa.pengaduan.create');
    }

    public function create(Request $request)
    {
        $user = $request->user();

        $this->ensureMahasiswa($user);

        if (PengaduanQuota::exhausted($user->id)) {
            return redirect()
                ->route('manajemenmahasiswa.pengaduan.index')
                ->with('error', PengaduanQuota::message($user->id));
        }

        $isStaff = false;

        $kategoriList = $this->kategoriMetaNew();

        $dosenList = User::whereHas('roles', fn($q) => $q->whereIn('name', ['dosen', 'dosen_koordinator']))
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        $buktiPendingItems = $this->buktiPendingItems();

        return view('manajemenmahasiswa::pengaduan.create', compact('kategoriList', 'dosenList', 'isStaff', 'buktiPendingItems'));
    }

    /**
     * Pratinjau bukti yang sudah diunggah tetapi belum dikirim (pengunggahnya sendiri).
     */
    public function buktiPending(Request $request, int $index)
    {
        $this->ensureMahasiswa($request->user());

        return PengaduanBukti::respondPending(self::BUKTI_SCOPE, $index);
    }

    /**
     * @return array<int, array{url:string,kind:string,size:int,label:string}> baris daftar tiap bukti pending
     */
    private function buktiPendingItems(): array
    {
        return PengaduanBukti::toItems(
            PengaduanBukti::pending(self::BUKTI_SCOPE),
            fn (int $i) => route('manajemenmahasiswa.pengaduan.bukti.pending', ['index' => $i])
        );
    }


    public function confirm(PengaduanPayloadRequest $request)
    {
        $user = $request->user();

        $this->ensureMahasiswa($user);

        if (PengaduanQuota::exhausted($user->id)) {
            return redirect()
                ->route('manajemenmahasiswa.pengaduan.index')
                ->with('error', PengaduanQuota::message($user->id));
        }

        // Bukti opsional. Berkas baru menggantikan bukti pending; tanpa berkas baru,
        // yang lama dipertahankan kecuali pelapor mencentang "jangan lampirkan".
        if ($request->hasFile('bukti')) {
            PengaduanBukti::stage($request->file('bukti'), self::BUKTI_SCOPE);
        } elseif ($request->boolean('bukti_hapus')) {
            PengaduanBukti::discard(self::BUKTI_SCOPE);
        }

        $request->flash();

        return view('manajemenmahasiswa::pengaduan.confirm', [
            'payload' => [
                'is_anonim' => $request->isAnonim(),
                'kategori' => $request->validated('kategori'),
                'template' => $request->normalizedTemplate(),
                'bukti_items' => $this->buktiPendingItems(),
            ],
        ]);
    }

    public function store(PengaduanPayloadRequest $request)
    {
        $user = $request->user();

        $this->ensureMahasiswa($user);

        if (PengaduanQuota::exhausted($user->id)) {
            return redirect()
                ->route('manajemenmahasiswa.pengaduan.index')
                ->with('error', PengaduanQuota::message($user->id));
        }

        $template = $request->normalizedTemplate();

        if (PengaduanBukti::pending(self::BUKTI_SCOPE) !== []) {
            $template['bukti'] = PengaduanBukti::commit(self::BUKTI_SCOPE);
        }

        $pengaduan = $this->pengaduanService->create(
            userId: $user->id,
            kategori: $request->validated('kategori'),
            isAnonim: $request->isAnonim(),
            template: $template,
        );

        // Penghitung kuota ada di cache dan tidak ditempelkan pada tiket, jadi
        // tidak menjadi jalur balik ke identitas pelapor anonim.
        PengaduanQuota::consume($user->id);

        if ($pengaduan->is_anonim) {
            // Tautan rahasia hanya ditampilkan SEKALI (flash); sistem sengaja tidak
            // menyimpan kaitan antara akun ini dan pengaduannya.
            return redirect()
                ->route('manajemenmahasiswa.pengaduan.index')
                ->with('anon_link', route('manajemenmahasiswa.pengaduan.track', ['token' => $pengaduan->anon_token]));
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

        if ($pengaduan->is_anonim) {
            $pengaduan->setRelation('pelapor', null);

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

    /**
     * Membuka satu bukti dukung. Staf boleh membuka semua tiket; pelapor hanya
     * tiket non-anonim miliknya (tiket anonim tak punya user_id, pelapornya
     * memakai route magic link di AnonPengaduanController::bukti).
     */
    public function bukti(Request $request, Pengaduan $pengaduan, int $index)
    {
        $user = $request->user();

        $this->ensureViewer($user);

        if (!$this->isStaffViewer($user) && $pengaduan->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke pengaduan ini.');
        }

        return PengaduanBukti::respond($pengaduan, $index);
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

    /**
     * Statistik Layanan Pengaduan (staf saja): bahan evaluasi mutu per periode.
     */
    public function statistik(Request $request, PengaduanStatistikService $statistik)
    {
        $user = $request->user();
        $this->ensureViewer($user);

        if (!$this->isStaffViewer($user)) {
            abort(403, 'Statistik pengaduan hanya untuk pengelola.');
        }

        $periode = array_key_exists((string) $request->query('periode'), PengaduanStatistikService::PERIODE)
            ? (string) $request->query('periode')
            : PengaduanStatistikService::PERIODE_BAWAAN;

        return view('manajemenmahasiswa::pengaduan.statistik', [
            'periode'  => $periode,
            'evaluasi' => $statistik->evaluasi($periode),
        ]);
    }

    public function toggleTercatat(Request $request, Pengaduan $pengaduan)
    {
        $user = $request->user();
        $this->ensureViewer($user);

        if (!$this->isStaffViewer($user)) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah status pengaduan.');
        }

        // Toggle: jika sudah tercatat → kembalikan ke baru, jika belum → tandai tercatat.
        // Status lama selesai/didelegasikan tampil "Tercatat", jadi ikut dianggap tercatat.
        if ($pengaduan->isTercatat()) {
            $pengaduan->update(['status' => Pengaduan::STATUS_BARU]);
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

