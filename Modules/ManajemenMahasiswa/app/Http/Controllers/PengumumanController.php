<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\ManajemenMahasiswa\Services\PengumumanService;
use Modules\ManajemenMahasiswa\Services\RepoMulmedService;

class PengumumanController extends Controller
{
    public function __construct(
        private
        PengumumanService $pengumumanService,
        private
        RepoMulmedService $repoMulmedService,
    ) {
    }

    //Daftar semua pengumuman (Admin/Koor/Pengurus view).
    public function index(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles->pluck('name');

        $filterKategori = $request->query('kategori');

        // Admin, Dosen Koordinator, Pengurus Himpunan: lihat semua
        if ($roles->intersect(['superadmin', 'admin', 'dosen_koordinator', 'pengurus_himpunan'])->isNotEmpty()) {
            $filters = $request->only(['status', 'search', 'audience']);
            if ($filterKategori && $filterKategori !== 'semua') {
                $filters['kategori'] = $filterKategori;
            }
            $pengumuman = $this->pengumumanService->listAll($filters);

            return view('manajemenmahasiswa::pengumuman.index', compact('pengumuman'));
        }

        // Role lain (Mahasiswa, Alumni, Dosen): bisa filter kategori & search
        $userAudience = $this->resolveAudience($roles);

        $targetKategoriFilter = ($filterKategori && $filterKategori !== 'semua') ? $filterKategori : null;
        $searchString = $request->query('search');

        $pengumuman = $this->pengumumanService->listPublished($userAudience, $targetKategoriFilter, $searchString);

        return view('manajemenmahasiswa::mahasiswa.pengumuman-mahasiswa', compact('pengumuman'));
    }


    //Form buat pengumuman baru.
    //Akses: Admin, Dosen Koordinator, Pengurus Himpunan
    public function create()
    {
        return view('manajemenmahasiswa::pengumuman.create');
    }

    //Simpan pengumuman baru.
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string|min:50',
            'kategori' => 'nullable|string|max:100',
            'target_audience' => 'required|in:all,mahasiswa,alumni,dosen,pengurus',
            'status_publish' => 'required|in:draft,published',
            'lampiran.*' => 'nullable|file|mimes:pdf,docx,xlsx,jpg,png|max:10240',
        ]);

        $pengumuman = $this->pengumumanService->create(Auth::id(), $validated);

        // Handle lampiran upload
        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                $this->repoMulmedService->upload($file, [
                    'judul_file' => $file->getClientOriginalName(),
                    'visibility_status' => 'public', // Set as public or default visibility
                    'pengumuman_id' => $pengumuman->id
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

        return view('manajemenmahasiswa::pengumuman.show', compact('pengumuman', 'user'));
    }

    /**
     * Form edit pengumuman.
     */
    public function edit(int $id)
    {
        $pengumuman = $this->pengumumanService->findById($id);

        // Hanya pembuat atau admin yang boleh edit
        $this->authorizeOwnerOrAdmin($pengumuman->user_id);

        return view('manajemenmahasiswa::pengumuman.edit', compact('pengumuman'));
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
            'target_audience' => 'required|in:all,mahasiswa,alumni,dosen,pengurus',
            'status_publish' => 'required|in:draft,published,archived',
            'lampiran.*' => 'nullable|file|mimes:pdf,docx,xlsx,jpg,png|max:10240',
        ]);

        $this->pengumumanService->update($id, $validated);

        // Handle lampiran baru
        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                $this->repoMulmedService->upload($file, [
                    'judul_file' => $file->getClientOriginalName(),
                    'visibility_status' => 'public',
                    'pengumuman_id' => $id
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
    public function removeLampiran(int $pengumumanId, int $lampiranId)
    {
        $pengumuman = $this->pengumumanService->findById($pengumumanId);
        $this->authorizeOwnerOrAdmin($pengumuman->user_id);

        $this->repoMulmedService->deletePermanent($lampiranId);

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
}
