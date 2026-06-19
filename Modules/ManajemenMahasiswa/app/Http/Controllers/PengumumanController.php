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
use Modules\ManajemenMahasiswa\Models\PengumumanPersonalPin;
use Modules\ManajemenMahasiswa\Models\PengumumanApprovalRequest;

class PengumumanController extends Controller
{
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
        $blogCardRoles = ['pengurus_himpunan', 'staff_himpunan', 'ketua_himpunan', 'wakil_ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'gpm', 'ketua_departemen', 'dpm', 'dosen', 'dosen_koordinator'];

        $isAdmin        = $roles->intersect(['superadmin', 'admin', 'admin_kemahasiswaan'])->isNotEmpty();
        $isAdminView    = $roles->intersect($adminRoles)->isNotEmpty();
        $isPengurusView = !$isAdminView && $roles->intersect($blogCardRoles)->isNotEmpty();

        if ($isAdminView || $isPengurusView) {
            // Per-page default: list lama pakai 10, blog-card pakai 9
            $defaultPerPage = $isAdminView ? 10 : 9;
            $perPage = max(5, min(100, (int) $request->input('per_page', $defaultPerPage)));

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

        $perPage = max(5, min(100, (int) $request->input('per_page', 10)));

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
        return view('manajemenmahasiswa::pengumuman.pengumuman-create', compact('drafts'));
    }

    /**
     * Simpan / Update Draft (AJAX)
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
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('Draft validation failed: ', $e->errors());
            throw $e;
        }

        $draftId = $request->input('draft_id');

        if ($draftId) {
            $draft = PengumumanDraft::where('id', $draftId)
                ->where('user_id', Auth::id())
                ->first();

            if ($draft) {
                $draft->update([
                    'judul' => $request->input('judul'),
                    'kategori' => $request->input('kategori'),
                    'target_audience' => $request->input('target_audience'),
                    'konten' => $request->input('konten'),
                ]);
            } else {
                $draft = PengumumanDraft::create([
                    'user_id' => Auth::id(),
                    'judul' => $request->input('judul'),
                    'kategori' => $request->input('kategori'),
                    'target_audience' => $request->input('target_audience'),
                    'konten' => $request->input('konten'),
                ]);
            }
        } else {
            $draft = PengumumanDraft::create([
                'user_id' => Auth::id(),
                'judul' => $request->input('judul'),
                'kategori' => $request->input('kategori'),
                'target_audience' => $request->input('target_audience'),
                'konten' => $request->input('konten'),
            ]);
        }

        return response()->json([
            'success' => true,
            'draft_id' => $draft->id,
            'message' => 'Draf berhasil disimpan.'
        ]);
    }

    /**
     * Hapus Draft
     */
    public function deleteDraft($id)
    {
        PengumumanDraft::where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return redirect()->back()->with('success', 'Draf berhasil dihapus.');
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
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'lampiran.*' => 'nullable|file|mimes:pdf,docx,xlsx,jpg,png|max:10240',
        ]);

        // Remove poster & lampiran from $validated before creating Pengumuman
        $posterFile = $request->file('poster');
        unset($validated['poster'], $validated['lampiran']);

        $pengumuman = $this->pengumumanService->create(Auth::id(), $validated);

        // Handle poster upload via RepoMulmed
        if ($posterFile) {
            $this->repoMulmedService->upload($posterFile, [
                'judul_file' => 'Poster: ' . $validated['judul'],
                'visibility_status' => 'public',
                'pengumuman_id' => $pengumuman->id,
            ]);
        }

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

                // Hapus draf jika post dikirim dari draf
                if ($request->filled('draft_id')) {
                    PengumumanDraft::where('id', $request->input('draft_id'))
                        ->where('user_id', Auth::id())
                        ->delete();
                }

                return redirect()
                    ->route('manajemenmahasiswa.pengumuman.verification.request', $pengumuman->id)
                    ->with('info', 'Pengumuman berhasil dibuat. Silakan pilih verifikator untuk mempublikasikan.');
            }

            $this->pengumumanService->publish($pengumuman->id);
        }

        // Hapus draf jika post dikirim dari draf
        if ($request->filled('draft_id')) {
            PengumumanDraft::where('id', $request->input('draft_id'))
                ->where('user_id', Auth::id())
                ->delete();
        }

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

        // Admin, GPM, Pengurus, Dosen: admin layout
        if ($roles->intersect(['superadmin', 'admin', 'dosen_koordinator', 'dosen', 'pengurus_himpunan', 'gpm', 'ketua_departemen', 'dpm', 'admin_kemahasiswaan'])->isNotEmpty()) {
            return view('manajemenmahasiswa::pengumuman.pengumuman-detail', compact('pengumuman', 'user', 'isPersonalPinned'));
        }

        return view('manajemenmahasiswa::mahasiswa.pengumuman-mahasiswa-detail', compact('pengumuman', 'user', 'isPersonalPinned'));
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

        return view('manajemenmahasiswa::pengumuman.pengumuman-edit', compact('pengumuman'));
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
            'poster' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'lampiran.*' => 'nullable|file|mimes:pdf,docx,xlsx,jpg,png|max:10240',
        ]);

        $posterFile = $request->file('poster');
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

        // Ganti poster jika ada upload baru
        if ($posterFile) {
            $this->repoMulmedService->upload($posterFile, [
                'judul_file' => 'Poster: ' . $validated['judul'],
                'visibility_status' => 'public',
                'pengumuman_id' => $id,
            ]);
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
        $file = \Modules\ManajemenMahasiswa\Models\RepoMulmed::findOrFail($lampiran);
        $url = $this->supabase->getPublicUrl($file->path_file);

        // Fetch file content from Supabase and stream it as a download
        $response = \Illuminate\Support\Facades\Http::timeout(30)->get($url);

        if (!$response->successful()) {
            abort(404, 'File tidak ditemukan.');
        }

        $fileName = $file->nama_file ?? basename($file->path_file);
        $mimeType = $response->header('Content-Type') ?? 'application/octet-stream';

        return response($response->body(), 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Content-Length' => strlen($response->body()),
        ]);
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
        $validVerifierRoles = ['ketua_himpunan', 'wakil_ketua_himpunan', 'ketua_bidang', 'ketua_unit', 'dpm'];
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
            $requests     = $this->pengumumanService->getAllRequests($statusFilter);
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
