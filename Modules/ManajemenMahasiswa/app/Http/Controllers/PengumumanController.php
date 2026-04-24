<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SupabaseStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\ManajemenMahasiswa\Services\PengumumanService;
use Modules\ManajemenMahasiswa\Services\RepoMulmedService;

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

        // Admin, Dosen Koordinator, Pengurus Himpunan, GPM, Admin Kemahasiswaan: lihat semua
        if ($roles->intersect(['superadmin', 'admin', 'dosen_koordinator', 'pengurus_himpunan', 'gpm', 'admin_kemahasiswaan'])->isNotEmpty()) {
            $filters = $request->only(['status', 'search', 'audience']);
            if ($filterKategori && $filterKategori !== 'semua') {
                $filters['kategori'] = $filterKategori;
            }
            $pengumuman = $this->pengumumanService->listAll($filters);

            return view('manajemenmahasiswa::pengumuman.pengumuman-a', compact('pengumuman'));
        }

        // Role lain (Mahasiswa, Alumni, Dosen): bisa filter kategori & search
        $userAudience = $this->resolveAudience($roles);

        $targetKategoriFilter = ($filterKategori && $filterKategori !== 'semua') ? $filterKategori : null;
        $searchString = $request->query('search');

        $pengumuman = $this->pengumumanService->listPublished($userAudience, $targetKategoriFilter, $searchString);

        return view('manajemenmahasiswa::mahasiswa.pengumuman-mahasiswa', compact('pengumuman'));
    }


    //Form buat pengumuman baru.
    //Akses: Admin, Dosen Koordinator, Pengurus Himpunan, GPM
    public function create()
    {
        return view('manajemenmahasiswa::pengumuman.pengumuman-create');
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
            $this->pengumumanService->publish($pengumuman->id);
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

        // Admin, GPM, Pengurus, Dosen Koordinator: admin layout
        if ($roles->intersect(['superadmin', 'admin', 'dosen_koordinator', 'pengurus_himpunan', 'gpm', 'admin_kemahasiswaan'])->isNotEmpty()) {
            return view('manajemenmahasiswa::pengumuman.pengumuman-detail', compact('pengumuman', 'user'));
        }

        return view('manajemenmahasiswa::mahasiswa.pengumuman-mahasiswa-detail', compact('pengumuman', 'user'));
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
            'judul'           => 'required|string|max:255',
            'konten'          => 'required|string|min:50',
            'kategori'        => 'nullable|string|max:100',
            'target_audience' => 'required|in:all,mahasiswa,alumni,dosen,pengurus',
            'status_publish'  => 'required|in:draft,published,archived',
            'poster'          => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'lampiran.*'      => 'nullable|file|mimes:pdf,docx,xlsx,jpg,png|max:10240',
        ]);

        $posterFile = $request->file('poster');
        unset($validated['poster'], $validated['lampiran']);

        $this->pengumumanService->update($id, $validated);

        // Ganti poster jika ada upload baru
        if ($posterFile) {
            $this->repoMulmedService->upload($posterFile, [
                'judul_file'        => 'Poster: ' . $validated['judul'],
                'visibility_status' => 'public',
                'pengumuman_id'     => $id,
            ]);
        }

        // Handle lampiran baru
        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                $this->repoMulmedService->upload($file, [
                    'judul_file'        => $file->getClientOriginalName(),
                    'visibility_status' => 'public',
                    'pengumuman_id'     => $id,
                ]);
            }
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
     */
    public function publish(int $id)
    {
        $pengumuman = $this->pengumumanService->findById($id);
        $this->authorizeOwnerOrAdmin($pengumuman->user_id);

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
        $url  = $this->supabase->getPublicUrl($file->path_file);

        // Fetch file content from Supabase and stream it as a download
        $response = \Illuminate\Support\Facades\Http::timeout(30)->get($url);

        if (!$response->successful()) {
            abort(404, 'File tidak ditemukan.');
        }

        $fileName = $file->nama_file ?? basename($file->path_file);
        $mimeType = $response->header('Content-Type') ?? 'application/octet-stream';

        return response($response->body(), 200, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Content-Length'      => strlen($response->body()),
        ]);
    }
}
