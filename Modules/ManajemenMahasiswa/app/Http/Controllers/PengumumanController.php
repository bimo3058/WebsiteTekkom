<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SupabaseStorage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Modules\ManajemenMahasiswa\Services\PengumumanService;
use Modules\ManajemenMahasiswa\Services\RepoMulmedService;
use Modules\ManajemenMahasiswa\Models\Pengumuman;
use Modules\ManajemenMahasiswa\Models\PengumumanDraft;
use Modules\ManajemenMahasiswa\Models\RepoMulmed;
use Modules\ManajemenMahasiswa\Models\PengumumanPersonalPin;
use Modules\ManajemenMahasiswa\Models\PengumumanApprovalRequest;
use Modules\ManajemenMahasiswa\Support\PerPage;

class PengumumanController extends Controller
{
    /** Batas jumlah gambar pengumuman yang boleh diunggah sekaligus. */
    private const MAX_GAMBAR = 5;

    /** Batas ukuran per berkas (gambar & lampiran) dalam KB, satuan aturan `max:`. */
    private const MAX_UKURAN_KB = 5120;

    public function __construct(
        private PengumumanService $pengumumanService,
        private RepoMulmedService $repoMulmedService,
        private SupabaseStorage $supabase,
    ) {
    }

    //Daftar semua pengumuman (Admin/Koor/Pengurus view).
    public function index(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles->pluck('name');

        $filterKategori = $request->query('kategori');

        // Admin murni (list lama): superadmin, admin, admin_kemahasiswaan
        // Blog-card baru: pengurus himpunan + gpm + dosen + dosen_koordinator
        $adminRoles    = ['superadmin', 'admin', 'admin_kemahasiswaan'];
        $blogCardRoles = ['pengurus_himpunan', 'staff_himpunan', 'ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'gpm', 'ketua_departemen', 'dpm', 'dosen', 'dosen_koordinator'];

        $isAdmin        = $roles->intersect(['superadmin', 'admin', 'admin_kemahasiswaan'])->isNotEmpty();
        $isAdminView    = $roles->intersect($adminRoles)->isNotEmpty();
        $isPengurusView = !$isAdminView && $roles->intersect($blogCardRoles)->isNotEmpty();

        if ($isAdminView || $isPengurusView) {
            // Admin melihat tabel baris (PerPage::TABEL), pengurus melihat grid
            // kartu yang pilihannya kelipatan 6 (PerPage::KARTU) supaya baris
            // terakhir tidak pincang. Daftarnya harus sama dengan dropdown
            // "Per page" di partials/table-footer.
            $perPage = $isAdminView
                ? PerPage::resolve($request)
                : PerPage::resolve($request, PerPage::KARTU, 12);

            $filters = $request->only(['status', 'search', 'audience']);
            if ($filterKategori && $filterKategori !== 'semua') {
                $filters['kategori'] = $filterKategori;
            }

            $pengumuman = $this->pengumumanService->listAll($filters, $perPage, Auth::id(), $isAdmin);

            // Admin murni → tampilan list lama; Pengurus himpunan → blog-card baru
            $view = $isAdminView
                ? 'manajemenmahasiswa::pengumuman.pengumuman-admin'
                : 'manajemenmahasiswa::pengumuman.pengumuman-a';

            return view($view, compact('pengumuman'));
        }

        // Mahasiswa/alumni juga memakai tampilan grid kartu.
        $perPage = PerPage::resolve($request, PerPage::KARTU, 12);

        // Role lain (Mahasiswa, Alumni, Dosen): bisa filter kategori & search
        $userAudience = $this->resolveAudience($roles);

        $targetKategoriFilter = ($filterKategori && $filterKategori !== 'semua') ? $filterKategori : null;
        $searchString = $request->query('search');

        $pengumuman = $this->pengumumanService->listPublished($userAudience, $targetKategoriFilter, $searchString, $perPage, Auth::id());

        return view('manajemenmahasiswa::mahasiswa.pengumuman-mahasiswa', compact('pengumuman'));
    }


    //Form buat pengumuman baru.
    //Akses: Admin, Dosen Koordinator, Pengurus Himpunan, GPM
    public function create()
    {
        $user = Auth::user();

        $drafts = PengumumanDraft::where('user_id', $user->id)->latest()->get();

        // Payload lampiran per draf, dipakai JS saat tombol "Load Draft" ditekan.
        $draftAttachments = $this->draftAttachments($drafts);

        $maxUkuranMb = intdiv(self::MAX_UKURAN_KB, 1024);

        return view('manajemenmahasiswa::pengumuman.pengumuman-create', compact('drafts', 'draftAttachments', 'maxUkuranMb'));
    }

    /**
     * Simpan / Update Draft (AJAX).
     *
     * Poster & lampiran langsung diupload ke storage saat draf disimpan, lalu
     * ID barisnya dicatat di draf. Tanpa ini file akan hilang begitu halaman
     * di-reload, karena input file tidak bisa diisi ulang dari server.
     */
    public function saveDraft(Request $request)
    {
        try {
            $request->validate([
                'draft_id' => 'nullable|integer',
                'judul' => 'nullable|string|max:255',
                'kategori' => 'nullable|string|max:100',
                'target_audience' => 'nullable|in:all,mahasiswa,alumni,dosen,pengurus',
                'konten' => 'nullable|string',
                'poster' => 'nullable|array|max:' . self::MAX_GAMBAR,
                'poster.*' => 'image|mimes:jpg,jpeg,png|max:' . self::MAX_UKURAN_KB,
                'lampiran.*' => 'nullable|file|mimes:pdf,docx,xlsx,jpg,png|max:' . self::MAX_UKURAN_KB,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('Draft validation failed: ', $e->errors());
            throw $e;
        }

        $attributes = [
            'judul' => $request->input('judul'),
            'kategori' => $request->input('kategori'),
            'target_audience' => $request->input('target_audience'),
            'konten' => $request->input('konten'),
        ];

        $draft = PengumumanDraft::where('id', $request->input('draft_id'))
            ->where('user_id', Auth::id())
            ->first();

        if ($draft) {
            $draft->update($attributes);
        } else {
            $draft = PengumumanDraft::create($attributes + ['user_id' => Auth::id()]);
        }

        $judulPengumuman = $request->input('judul') ?: 'Draf pengumuman';

        // Gambar baru menggantikan seluruh set gambar lama — file lama dihapus
        // agar tidak jadi sampah di storage.
        if ($request->hasFile('poster')) {
            $idLama = $draft->posterRepoIds();

            $idBaru = $this->unggahGambar($request->file('poster'), $judulPengumuman);

            $draft->update([
                'poster_repo_ids' => $idBaru,
                'poster_repo_id'  => $idBaru[0] ?? null,
            ]);

            foreach ($idLama as $repoId) {
                $this->repoMulmedService->deletePermanent($repoId);
            }
        }

        if ($request->hasFile('lampiran')) {
            $lampiranIds = $draft->lampiran_repo_ids ?? [];

            foreach ($request->file('lampiran') as $file) {
                $lampiranIds[] = $this->repoMulmedService->upload($file, [
                    'judul_file' => $file->getClientOriginalName(),
                    'visibility_status' => 'public',
                ])->id;
            }

            $draft->update(['lampiran_repo_ids' => $lampiranIds]);
        }

        return response()->json([
            'success' => true,
            'draft_id' => $draft->id,
            'attachments' => $this->draftAttachments(collect([$draft->fresh()]))[$draft->id],
            'message' => 'Draf berhasil disimpan.'
        ]);
    }

    /**
     * Hapus Draft beserta file yang sudah terlanjur diupload untuk draf itu.
     */
    public function deleteDraft($id)
    {
        $draft = PengumumanDraft::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$draft) {
            return redirect()->back()->with('success', 'Draf berhasil dihapus.');
        }

        foreach ($draft->allRepoIds() as $repoId) {
            $this->repoMulmedService->deletePermanent($repoId);
        }

        $draft->delete();

        return redirect()->back()->with('success', 'Draf berhasil dihapus.');
    }

    /**
     * Susun daftar file (gambar + lampiran) untuk sekumpulan draf.
     *
     * Semua baris mk_repo_mulmed diambil dalam satu query agar tidak N+1
     * saat halaman "Buat Pengumuman" menampilkan banyak draf sekaligus.
     *
     * @param  \Illuminate\Support\Collection<PengumumanDraft>  $drafts
     * @return array<int, array{gambar: array, lampiran: array}>
     */
    private function draftAttachments($drafts): array
    {
        $semuaId = $drafts->flatMap(fn (PengumumanDraft $draft) => $draft->allRepoIds())->unique();

        $files = RepoMulmed::whereIn('id', $semuaId)->get()->keyBy('id');

        $ringkas = fn (?RepoMulmed $file) => $file ? [
            'id'   => $file->id,
            'nama' => $file->nama_file,
            'url'  => $file->url,
        ] : null;

        $petakan = fn (array $ids) => collect($ids)
            ->map(fn ($id) => $ringkas($files->get($id)))
            ->filter()
            ->values()
            ->all();

        return $drafts->mapWithKeys(fn (PengumumanDraft $draft) => [
            $draft->id => [
                'gambar'   => $petakan($draft->posterRepoIds()),
                'lampiran' => $petakan($draft->lampiran_repo_ids ?? []),
            ],
        ])->all();
    }

    /**
     * Unggah gambar pengumuman sesuai urutan yang dikirim form.
     *
     * Indeks 0 adalah cover: diunggah paling awal sehingga id-nya paling kecil —
     * inilah gambar yang diambil halaman daftar & detail sebagai sampul — dan
     * judul filenya diberi prefix "Poster:" agar mudah dikenali.
     *
     * @param  array<\Illuminate\Http\UploadedFile>  $files
     * @return array<int>  ID mk_repo_mulmed sesuai urutan gambar
     */
    private function unggahGambar(array $files, string $judul, ?int $pengumumanId = null): array
    {
        $ids = [];

        foreach (array_values($files) as $index => $file) {
            $meta = [
                'judul_file' => $index === 0 ? 'Poster: ' . $judul : $file->getClientOriginalName(),
                'visibility_status' => 'public',
            ];

            if ($pengumumanId) {
                $meta['pengumuman_id'] = $pengumumanId;
            }

            $ids[] = $this->repoMulmedService->upload($file, $meta)->id;
        }

        return $ids;
    }

    /**
     * Jumlah gambar yang saat ini menempel pada sebuah pengumuman.
     */
    private function jumlahGambar(Pengumuman $pengumuman): int
    {
        return collect($pengumuman->repoMulmed)
            ->filter(fn (RepoMulmed $file) => $file->isGambar())
            ->count();
    }

    /**
     * Ambil draf milik user yang sedang login. Null jika tidak ada / bukan miliknya.
     */
    private function userDraft(mixed $draftId): ?PengumumanDraft
    {
        if (!$draftId) {
            return null;
        }

        return PengumumanDraft::where('id', $draftId)
            ->where('user_id', Auth::id())
            ->first();
    }

    //Simpan pengumuman baru.
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string|min:50',
            'kategori' => 'nullable|string|max:100',
            'target_audience' => 'required|in:all,mahasiswa,alumni',
            'status_publish' => 'required|in:draft,published',
            'poster' => 'nullable|array|max:' . self::MAX_GAMBAR,
            'poster.*' => 'image|mimes:jpg,jpeg,png|max:' . self::MAX_UKURAN_KB,
            'lampiran.*' => 'nullable|file|mimes:pdf,docx,xlsx,jpg,png|max:' . self::MAX_UKURAN_KB,
        ]);

        // Remove poster & lampiran from $validated before creating Pengumuman
        $gambarFiles = $request->file('poster', []);
        unset($validated['poster'], $validated['lampiran']);

        $pengumuman = $this->pengumumanService->create(Auth::id(), $validated);

        // File yang sudah diupload saat draf disimpan — tinggal dipindah kepemilikannya.
        $draft            = $this->userDraft($request->input('draft_id'));
        $draftGambarIds   = $draft?->posterRepoIds() ?? [];
        $draftLampiranIds = $draft?->lampiran_repo_ids ?? [];

        // Gambar yang dipilih di form menang atas gambar bawaan draf.
        if ($gambarFiles) {
            foreach ($draftGambarIds as $repoId) {
                $this->repoMulmedService->deletePermanent($repoId);
            }
            $draftGambarIds = [];

            $this->unggahGambar($gambarFiles, $validated['judul'], $pengumuman->id);
        }

        $fileDariDraft = array_values(array_filter(array_merge($draftGambarIds, $draftLampiranIds)));

        if ($fileDariDraft) {
            RepoMulmed::whereIn('id', $fileDariDraft)->update(['pengumuman_id' => $pengumuman->id]);
        }

        // Lepaskan referensi agar file tidak ikut terhapus saat draf dibuang.
        $draft?->update([
            'poster_repo_id'    => null,
            'poster_repo_ids'   => null,
            'lampiran_repo_ids' => null,
        ]);

        // Handle lampiran upload
        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                $this->repoMulmedService->upload($file, [
                    'judul_file' => $file->getClientOriginalName(),
                    'visibility_status' => 'public',
                    'pengumuman_id' => $pengumuman->id,
                ]);
            }
        }

        // Publish langsung jika diminta
        if ($validated['status_publish'] === 'published') {
            // Staff himpunan wajib verifikasi sebelum publish
            if ($this->pengumumanService->requiresApproval(Auth::user())) {
                // Simpan sebagai pending_review, redirect ke halaman verification-request
                $this->pengumumanService->update($pengumuman->id, [
                    'status_publish' => 'pending_review',
                ]);

                $draft?->delete();

                return redirect()
                    ->route('manajemenmahasiswa.pengumuman.verification.request', $pengumuman->id)
                    ->with('info', 'Pengumuman berhasil dibuat. Silakan pilih verifikator untuk mempublikasikan.');
            }

            $this->pengumumanService->publish($pengumuman->id);
        }

        $draft?->delete();

        return redirect()
            ->route('manajemenmahasiswa.pengumuman.index')
            ->with('success', 'Pengumuman berhasil dibuat.');
    }

    /**
     * Detail pengumuman.
     */
    public function show(int $id)
    {
        $pengumuman = $this->pengumumanService->findById($id);
        $user = Auth::user();
        $roles = $user->roles->pluck('name');
        $isPersonalPinned = $this->pengumumanService->isPersonalPinned($id, $user->id);

        // Bagian "Pengumuman Lainnya" di bawah artikel — disaring memakai audiens
        // pembaca, jadi isinya sama dengan yang boleh ia lihat di daftar.
        $lainnya = $this->pengumumanService->lainnya($id, $this->resolveAudience($roles));

        // Admin, GPM, Pengurus, Dosen: admin layout
        if ($roles->intersect(['superadmin', 'admin', 'dosen_koordinator', 'dosen', 'pengurus_himpunan', 'gpm', 'ketua_departemen', 'dpm', 'admin_kemahasiswaan'])->isNotEmpty()) {
            return view('manajemenmahasiswa::pengumuman.pengumuman-detail', compact('pengumuman', 'user', 'isPersonalPinned', 'lainnya'));
        }

        return view('manajemenmahasiswa::mahasiswa.pengumuman-mahasiswa-detail', compact('pengumuman', 'user', 'isPersonalPinned', 'lainnya'));
    }

    /**
     * Toggle pin global pengumuman — hanya admin, gpm, admin_kemahasiswaan, superadmin.
     */
    public function pin(int $id)
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan'])) {
            abort(403, 'Akses ditolak.');
        }

        $pengumuman = $this->pengumumanService->pinPengumuman($id);
        $status = $pengumuman->is_pinned ? 'dipin secara global' : 'di-unpin dari pin global';

        return back()->with('success', "Pengumuman berhasil {$status}.");
    }

    /**
     * Toggle pin pribadi pengumuman — semua user terautentikasi.
     */
    public function personalPin(int $id)
    {
        $isPinned = $this->pengumumanService->togglePersonalPin($id, Auth::id());

        if (request()->expectsJson()) {
            return response()->json(['is_pinned' => $isPinned]);
        }

        $status = $isPinned ? 'dipin secara pribadi' : 'di-unpin dari pin pribadi';
        return back()->with('success', "Pengumuman berhasil {$status}.");
    }

    /**
     * Form edit pengumuman.
     */
    public function edit(int $id)
    {
        $pengumuman = $this->pengumumanService->findById($id);

        // Hanya pembuat atau admin yang boleh edit
        $this->authorizeOwnerOrAdmin($pengumuman->user_id);

        $maxGambar = self::MAX_GAMBAR;
        $maxUkuranMb = intdiv(self::MAX_UKURAN_KB, 1024);

        return view('manajemenmahasiswa::pengumuman.pengumuman-edit', compact('pengumuman', 'maxGambar', 'maxUkuranMb'));
    }

    /**
     * Update pengumuman.
     */
    public function update(Request $request, int $id)
    {
        $pengumuman = $this->pengumumanService->findById($id);
        $this->authorizeOwnerOrAdmin($pengumuman->user_id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string|min:50',
            'kategori' => 'nullable|string|max:100',
            'target_audience' => 'required|in:all,mahasiswa,alumni',
            'status_publish' => 'required|in:draft,published,archived',
            'poster' => 'nullable|array|max:' . self::MAX_GAMBAR,
            'poster.*' => 'image|mimes:jpg,jpeg,png|max:' . self::MAX_UKURAN_KB,
            'lampiran.*' => 'nullable|file|mimes:pdf,docx,xlsx,jpg,png|max:' . self::MAX_UKURAN_KB,
        ]);

        $gambarBaru = $request->file('poster', []);
        unset($validated['poster'], $validated['lampiran']);

        // Fix #3: Re-verifikasi jika staff (requiresApproval) mengedit konten/judul
        // pengumuman yang sudah published — perubahan konten perlu disetujui ulang.
        $requiresApproval = $this->pengumumanService->requiresApproval(Auth::user());
        $alreadyPublished = $pengumuman->status_publish === Pengumuman::STATUS_PUBLISHED;
        $contentChanged   = $validated['judul'] !== $pengumuman->judul
            || strip_tags($validated['konten']) !== strip_tags($pengumuman->konten ?? '');

        // Perlu verifikasi ulang jika: (a) mau publish baru, ATAU (b) edit konten yang sudah published
        $needsVerification = $requiresApproval
            && ($validated['status_publish'] === 'published'
                || ($alreadyPublished && $contentChanged));

        if ($needsVerification) {
            $validated['status_publish'] = 'pending_review';
        }

        $this->pengumumanService->update($id, $validated);

        // Tambah gambar baru. Cover (gambar pertama) tidak bisa diganti dari
        // halaman edit, jadi gambar baru selalu masuk di belakang — dan total
        // gambar dibatasi supaya tidak melewati MAX_GAMBAR.
        if ($gambarBaru) {
            $sisaSlot = max(0, self::MAX_GAMBAR - $this->jumlahGambar($pengumuman));

            if ($sisaSlot === 0) {
                return back()
                    ->withInput()
                    ->with('error', 'Gambar sudah mencapai batas ' . self::MAX_GAMBAR . '. Hapus salah satu sebelum menambah.');
            }

            foreach (array_slice(array_values($gambarBaru), 0, $sisaSlot) as $file) {
                $this->repoMulmedService->upload($file, [
                    'judul_file' => $file->getClientOriginalName(),
                    'visibility_status' => 'public',
                    'pengumuman_id' => $id,
                ]);
            }
        }

        // Handle lampiran baru
        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                $this->repoMulmedService->upload($file, [
                    'judul_file' => $file->getClientOriginalName(),
                    'visibility_status' => 'public',
                    'pengumuman_id' => $id,
                ]);
            }
        }

        if ($needsVerification) {
            return redirect()
                ->route('manajemenmahasiswa.pengumuman.verification.request', $id)
                ->with('info', 'Pengumuman diperbarui. Silakan pilih verifikator untuk mempublikasikannya.');
        }

        return redirect()
            ->route('manajemenmahasiswa.pengumuman.show', $id)
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Hapus pengumuman.
     */
    public function remove(int $id)
    {
        $pengumuman = $this->pengumumanService->findById($id);
        $this->authorizeOwnerOrAdmin($pengumuman->user_id);

        $this->pengumumanService->delete($id);

        return redirect()
            ->route('manajemenmahasiswa.pengumuman.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }

    /**
     * Publish pengumuman dari draft.
     * Staff himpunan diarahkan ke alur verifikasi terlebih dahulu.
     */
    public function publish(int $id)
    {
        $pengumuman = $this->pengumumanService->findById($id);
        $this->authorizeOwnerOrAdmin($pengumuman->user_id);

        if ($this->pengumumanService->requiresApproval(Auth::user())) {
            $this->pengumumanService->update($id, [
                'status_publish' => 'pending_review',
            ]);

            return redirect()
                ->route('manajemenmahasiswa.pengumuman.verification.request', $id)
                ->with('info', 'Pengumuman perlu diverifikasi sebelum dipublikasikan. Silakan pilih verifikator.');
        }

        $this->pengumumanService->publish($id);

        return back()->with('success', 'Pengumuman berhasil dipublikasikan.');
    }

    /**
     * Hapus lampiran tertentu.
     */
    public function removeLampiran(Request $request, int $pengumumanId, int $lampiranId)
    {
        $pengumuman = $this->pengumumanService->findById($pengumumanId);
        $this->authorizeOwnerOrAdmin($pengumuman->user_id);

        $this->repoMulmedService->deletePermanent($lampiranId);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Lampiran berhasil dihapus.']);
        }

        return back()->with('success', 'Lampiran berhasil dihapus.');
    }

    // Helpers
    private function resolveAudience($roles): string
    {
        if ($roles->contains('alumni'))
            return 'alumni';
        if ($roles->contains('dosen'))
            return 'dosen';
        if ($roles->contains('pengurus_himpunan'))
            return 'pengurus';
        return 'mahasiswa';
    }

    private function authorizeOwnerOrAdmin(int $ownerId): void
    {
        $user = Auth::user();
        $roles = $user->roles->pluck('name');
        $isAdminOrKoor = $roles->intersect(['superadmin', 'admin', 'dosen_koordinator'])->isNotEmpty();

        if ($user->id !== $ownerId && !$isAdminOrKoor) {
            abort(403, 'Anda tidak memiliki akses untuk aksi ini.');
        }
    }

    /**
     * Upload gambar inline dari editor konten ke Supabase.
     * Mengembalikan JSON { url: "..." } untuk disisipkan ke editor.
     */
    public function uploadInlineImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        $path = $this->supabase->upload($request->file('image'), 'mk_mulmed/image');

        if (!$path) {
            return response()->json(['error' => 'Gagal mengupload gambar.'], 500);
        }

        return response()->json(['url' => $this->supabase->getPublicUrl($path)]);
    }

    /**
     * Download / redirect ke lampiran file di Supabase.
     */
    public function downloadLampiran(int $lampiran)
    {
        $file = RepoMulmed::findOrFail($lampiran);

        return $this->streamBerkas($file);
    }

    /**
     * Download seluruh gambar milik satu pengumuman.
     *
     * Gambar tampil di galeri, bukan di kartu lampiran, sehingga sebelumnya tidak
     * punya jalur unduh sama sekali. Satu gambar diunduh apa adanya; lebih dari
     * satu dibungkus ZIP supaya tidak memaksa pengguna mengunduh satu per satu.
     */
    public function downloadGambar(int $pengumuman)
    {
        $item = $this->pengumumanService->findById($pengumuman);

        $gambar = $item->repoMulmed->filter(fn (RepoMulmed $f) => $f->isGambar())->values();

        abort_if($gambar->isEmpty(), 404, 'Pengumuman ini tidak memiliki gambar.');

        if ($gambar->count() === 1) {
            return $this->streamBerkas($gambar->first());
        }

        abort_unless(
            class_exists(\ZipArchive::class),
            500,
            'Ekstensi PHP Zip (ZipArchive) belum aktif, jadi gambar tidak bisa diunduh sekaligus.'
        );

        $tmp = tempnam(sys_get_temp_dir(), 'pengumuman-gambar-');
        $zip = new \ZipArchive();

        if ($zip->open($tmp, \ZipArchive::OVERWRITE | \ZipArchive::CREATE) !== true) {
            @unlink($tmp);
            abort(500, 'Gagal menyiapkan arsip ZIP.');
        }

        $dipakai = [];
        foreach ($gambar as $i => $file) {
            $isi = $this->ambilBerkas($file);
            if ($isi === null) {
                continue; // berkas hilang di storage — lewati, jangan gagalkan seluruh unduhan
            }

            $zip->addFromString($this->namaUnikDalamZip($file, $i, $dipakai), $isi);
        }

        $jumlah = $zip->numFiles;
        $zip->close();

        if ($jumlah === 0) {
            @unlink($tmp);
            abort(404, 'Berkas gambar tidak ditemukan di penyimpanan.');
        }

        return response()
            ->download($tmp, $this->namaArsip($item), ['Content-Type' => 'application/zip'])
            ->deleteFileAfterSend();
    }

    /**
     * Tarik isi satu berkas dari Supabase; null kalau tidak terjangkau.
     */
    private function ambilBerkas(RepoMulmed $file): ?string
    {
        $response = \Illuminate\Support\Facades\Http::timeout(30)
            ->get($this->supabase->getPublicUrl($file->path_file));

        return $response->successful() ? $response->body() : null;
    }

    /**
     * Kirim satu berkas sebagai unduhan.
     */
    private function streamBerkas(RepoMulmed $file)
    {
        $isi = $this->ambilBerkas($file);

        abort_if($isi === null, 404, 'File tidak ditemukan.');

        $namaFile = $file->nama_file ?: basename($file->path_file);

        return response($isi, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $namaFile . '"',
            'Content-Length' => strlen($isi),
        ]);
    }

    /**
     * Nama berkas di dalam ZIP. Gambar hasil unggahan bisa bernama sama
     * (mis. "image.png" dari dua perangkat), jadi yang bentrok diberi sufiks.
     */
    private function namaUnikDalamZip(RepoMulmed $file, int $index, array &$dipakai): string
    {
        $nama = $file->nama_file ?: basename($file->path_file);
        $nama = preg_replace('/[\\\\\/:*?"<>|]+/', '_', $nama) ?: ('gambar-' . ($index + 1));

        if (!isset($dipakai[$nama])) {
            $dipakai[$nama] = 1;

            return $nama;
        }

        $ext  = pathinfo($nama, PATHINFO_EXTENSION);
        $base = pathinfo($nama, PATHINFO_FILENAME);
        $urut = ++$dipakai[$nama];

        return $ext === '' ? "{$base} ({$urut})" : "{$base} ({$urut}).{$ext}";
    }

    /**
     * Nama file ZIP, diambil dari judul pengumuman agar mudah dikenali.
     */
    private function namaArsip(Pengumuman $pengumuman): string
    {
        $judul = trim(preg_replace('/[\\\\\/:*?"<>|]+/', '_', (string) $pengumuman->judul));

        return ($judul !== '' ? $judul : 'pengumuman-' . $pengumuman->id) . ' - Gambar.zip';
    }

    // =========================================================================
    // Approval Workflow (Staff Himpunan → Ketua)
    // =========================================================================

    /**
     * Halaman form pengajuan verifikasi — tampil setelah staff klik Publikasikan.
     */
    public function verificationRequest(int $pengumumanId)
    {
        $pengumuman = $this->pengumumanService->findById($pengumumanId);

        // Hanya pemilik yang boleh akses
        if ($pengumuman->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk aksi ini.');
        }

        // Hanya pengumuman pending_review yang boleh diajukan
        if ($pengumuman->status_publish !== 'pending_review') {
            return redirect()
                ->route('manajemenmahasiswa.pengumuman.show', $pengumumanId)
                ->with('info', 'Pengumuman ini tidak dalam status menunggu verifikasi.');
        }

        $verifiers = $this->pengumumanService->getAvailableVerifiers();

        return view('manajemenmahasiswa::pengumuman.pengumuman-verification-request', compact('pengumuman', 'verifiers'));
    }

    /**
     * Proses submit pengajuan verifikasi.
     */
    public function submitVerificationRequest(Request $request, int $pengumumanId)
    {
        $pengumuman = $this->pengumumanService->findById($pengumumanId);

        if ($pengumuman->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk aksi ini.');
        }

        // Fix #1: Validasi backend bahwa verifier_id benar-benar punya role ketua yang valid
        $validVerifierRoles = ['ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'dpm'];
        $validated = $request->validate([
            'verifier_id' => [
                'required',
                'exists:users,id',
                Rule::exists('model_has_roles', 'model_id')
                    ->where('model_type', 'App\Models\User')
                    ->whereIn('role_id', function ($q) use ($validVerifierRoles) {
                        $q->select('id')->from('roles')->whereIn('name', $validVerifierRoles);
                    }),
            ],
            'pesan_pengaju' => 'nullable|string|max:1000',
        ]);

        try {
            $this->pengumumanService->requestApproval(
                $pengumumanId,
                Auth::id(),
                (int) $validated['verifier_id'],
                $validated['pesan_pengaju'] ?? null
            );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('manajemenmahasiswa.pengumuman.riwayat.verifikasi')
            ->with('success', 'Pengajuan verifikasi berhasil dikirim. Menunggu persetujuan verifikator.');
    }

    /**
     * Tarik kembali pengajuan verifikasi yang masih pending.
     * Fix #10: Admin/superadmin/admin_kemahasiswaan bisa cancel request milik staff manapun.
     */
    public function cancelVerificationRequest(int $pengumumanId)
    {
        $pengumuman = $this->pengumumanService->findById($pengumumanId);
        $user       = Auth::user();

        $isAdminOverride = $user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan']);

        if (!$isAdminOverride && $pengumuman->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk aksi ini.');
        }

        // Jika admin yang cancel, gunakan requester_id asli pengumuman
        $requesterId = $isAdminOverride ? $pengumuman->user_id : $user->id;
        $this->pengumumanService->cancelApprovalRequest($pengumumanId, $requesterId);

        return redirect()
            ->route('manajemenmahasiswa.pengumuman.index')
            ->with('success', 'Pengajuan verifikasi berhasil ditarik kembali. Pengumuman dikembalikan ke draft.');
    }

    /**
     * Halaman riwayat verifikasi pengumuman untuk staff himpunan.
     * Menampilkan semua request yang pernah diajukan oleh staff beserta statusnya.
     */
    public function riwayatVerifikasiStaff(Request $request)
    {
        $user = Auth::user();
        $statusFilter = $request->query('status', 'all');

        $requests     = $this->pengumumanService->getRequestsByRequester($user->id, $statusFilter);
        $stats        = $this->pengumumanService->getStatsByRequester($user->id); // Fix #5: single GROUP BY query
        $pendingCount = $stats['pending'];

        return view('manajemenmahasiswa::pengumuman.pengumuman-riwayat-verifikasi', compact(
            'requests', 'statusFilter', 'pendingCount', 'stats'
        ));
    }

    /**
     * Dashboard verifikasi pengumuman.
     * Admin/superadmin/admin_kemahasiswaan melihat semua request dari semua staff.
     * Ketua himpunan/bidang/unit hanya melihat request yang ditujukan ke mereka.
     */
    public function verifikasiIndex(Request $request)
    {
        $user = Auth::user();
        $statusFilter = $request->query('status', 'pending');
        $isAdmin = $user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan', 'dpm']);

        if ($isAdmin) {
            $requests     = $this->pengumumanService->getAllRequests($statusFilter, PerPage::resolve($request));
            $pendingCount = $this->pengumumanService->getAllPendingCount();
        } else {
            $requests     = $this->pengumumanService->getRequestsForVerifier($user->id, $statusFilter);
            $pendingCount = $this->pengumumanService->getPendingForVerifier($user->id)->count();
        }

        return view('manajemenmahasiswa::pengumuman.pengumuman-verifikasi', compact('requests', 'statusFilter', 'pendingCount', 'isAdmin'));
    }

    /**
     * Setujui pengumuman — publish langsung.
     * Admin/superadmin/admin_kemahasiswaan dapat approve request manapun.
     */
    public function approveVerifikasi(Request $request, int $requestId)
    {
        $approvalRequest = PengumumanApprovalRequest::findOrFail($requestId);
        $user = Auth::user();

        $canOverride = $user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan', 'dpm']);

        if (!$canOverride && $approvalRequest->verifier_id !== $user->id) {
            abort(403, 'Anda bukan verifikator untuk pengumuman ini.');
        }

        if (!$approvalRequest->isPending()) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'catatan' => 'nullable|string|max:1000',
        ]);

        try {
            $this->pengumumanService->approveRequest($requestId, $request->input('catatan'));
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pengumuman berhasil disetujui dan dipublikasikan.');
    }

    /**
     * Tolak pengumuman — kembalikan ke draft.
     * Admin/superadmin/admin_kemahasiswaan dapat reject request manapun.
     */
    public function rejectVerifikasi(Request $request, int $requestId)
    {
        $approvalRequest = PengumumanApprovalRequest::findOrFail($requestId);
        $user = Auth::user();

        $canOverride = $user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan', 'dpm']);

        if (!$canOverride && $approvalRequest->verifier_id !== $user->id) {
            abort(403, 'Anda bukan verifikator untuk pengumuman ini.');
        }

        if (!$approvalRequest->isPending()) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'catatan' => 'required|string|max:1000',
        ]);

        $this->pengumumanService->rejectRequest($requestId, $request->input('catatan'));

        return back()->with('success', 'Pengumuman berhasil ditolak dan dikembalikan ke draft.');
    }
}
