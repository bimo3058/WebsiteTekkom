<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\ManajemenMahasiswa\Models\Thread;
use Modules\ManajemenMahasiswa\Services\ThreadService;
use Modules\ManajemenMahasiswa\Services\CommentService;
use Modules\ManajemenMahasiswa\Services\GamificationService;
use App\Services\SupabaseStorage;
use Modules\ManajemenMahasiswa\Models\RepoMulmed;

class ForumController extends Controller
{
    public function __construct(
        private ThreadService $threadService,
        private CommentService $commentService,
        private GamificationService $gamificationService,
        private SupabaseStorage $supabaseStorage,
    ) {
    }

    /**
     * Halaman utama forum — listing thread + leaderboard + stats user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles->pluck('name');

        $threads = $this->threadService->listThreads($request->all(), 15);
        $leaderboard = $this->gamificationService->getLeaderboard(10);
        $userStats = $this->gamificationService->getUserStats($user->id);
        $categories = Thread::KATEGORI_LABELS;

        $userVotes = \Modules\ManajemenMahasiswa\Models\Vote::where('user_id', $user->id)
            ->where('voteable_type', Thread::class)
            ->whereIn('voteable_id', $threads->pluck('id'))
            ->get()
            ->keyBy(function($v) {
                return $v->voteable_type . '_' . $v->voteable_id;
            });

        $viewData = compact('threads', 'leaderboard', 'userStats', 'categories', 'user', 'userVotes');

        // Admin, GPM, Pengurus, Dosen Koordinator: admin layout
        if ($roles->intersect(['superadmin', 'admin', 'dosen_koordinator', 'pengurus_himpunan', 'gpm', 'admin_kemahasiswaan'])->isNotEmpty()) {
            return view('manajemenmahasiswa::forum.admin', $viewData);
        }

        // Dosen: dosen layout
        if ($roles->intersect(['dosen'])->isNotEmpty()) {
            return view('manajemenmahasiswa::forum.dosen', $viewData);
        }

        return view('manajemenmahasiswa::forum.mahasiswa', $viewData);
    }

    /**
     * Form buat thread baru.
     */
    public function create()
    {
        $user = Auth::user();
        $categories = Thread::KATEGORI_LABELS;

        return view('manajemenmahasiswa::forum.create', compact('categories', 'user'));
    }

    /**
     * Simpan thread baru — Unified Post (teks + media + link dalam 1 request).
     */
    public function store(Request $request)
    {
        $rules = [
            'judul'         => 'required|string|max:255',
            'kategori'      => 'required|string|in:' . implode(',', Thread::KATEGORI_LIST),
            'konten'        => 'nullable|string',
            'media_files'   => 'nullable|array|max:5',
            'media_files.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,webm|max:10240',
            'link_url'      => 'nullable|url|max:2000',
        ];

        $validated = $request->validate($rules);

        // Bangun konten HTML dari komponen unified
        $konten = '';

        // 1) Media files — upload & generate HTML via Supabase
        if ($request->hasFile('media_files')) {
            foreach ($request->file('media_files') as $file) {
                $mime = $file->getMimeType();
                $isImage = str_starts_with($mime, 'image/');
                $folder = $isImage ? 'mk_mulmed/image' : 'mk_mulmed/video';
                
                // Gunakan bucket default dari config (storage_web)
                $path = $this->supabaseStorage->upload($file, $folder);
                
                if ($path) {
                    $url  = $this->supabaseStorage->getPublicUrl($path);
                    
                    // Simpan data file ke tabel mk_repo_mulmed
                    RepoMulmed::create([
                        'nama_file'         => $file->getClientOriginalName(),
                        'path_file'         => $path,
                        'tipe_file'         => $isImage ? RepoMulmed::TIPE_IMAGE : RepoMulmed::TIPE_VIDEO,
                        'judul_file'        => 'Forum Media: ' . $validated['judul'],
                        'deskripsi_meta'    => 'Diunggah pada forum mahasiswa',
                        'visibility_status' => RepoMulmed::VISIBILITY_PUBLIC,
                        'status_arsip'      => RepoMulmed::ARSIP_AKTIF,
                    ]);

                    if ($isImage) {
                        $konten .= '<img src="' . $url . '" alt="Media Post" style="max-width: 100%; border-radius: 8px; margin-bottom: 12px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">';
                    } else {
                        $konten .= '<video width="100%" controls style="border-radius: 8px; margin-bottom: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);"><source src="' . $url . '" type="' . $mime . '"></video>';
                    }
                } else {
                    return back()->withErrors(['media_files' => 'Gagal mengupload gambar ke Supabase. Pastikan konfigurasi benar.'])->withInput();
                }
            }
        }

        // 2) Link — render sebagai card
        if (!empty($validated['link_url'])) {
            $linkUrl = e($validated['link_url']);
            $konten .= '<a href="' . $linkUrl . '" target="_blank" rel="noopener noreferrer" class="d-inline-flex p-3 rounded bg-light border border-primary-subtle text-primary fw-bold text-decoration-none mb-3" style="word-break: break-all;">🔗 ' . $linkUrl . '</a><br>';
        }

        // 3) Teks konten
        if (!empty($validated['konten'])) {
            $konten .= e($validated['konten']);
        }

        // Validasi: minimal harus ada konten (teks, media, atau link)
        if (empty(trim($konten))) {
            return back()->withErrors(['konten' => 'Post harus memiliki konten (teks, gambar/video, atau link).'])->withInput();
        }

        $this->threadService->createThread(Auth::id(), [
            'judul'    => $validated['judul'],
            'konten'   => $konten,
            'kategori' => $validated['kategori'],
        ]);

        return redirect()
            ->route('manajemenmahasiswa.forum.index')
            ->with('success', 'Thread berhasil dibuat! +10 XP 🎉');
    }

    /**
     * Detail thread + comments.
     */
    public function show(int $id, Request $request)
    {
        $user = Auth::user();

        $thread = $this->threadService->findThread($id);
        $comments = $this->commentService->listComments($id);
        
        // Dapatkan data vote dari user untuk highlight upvote/downvote button
        $userVotes = \Modules\ManajemenMahasiswa\Models\Vote::where('user_id', $user->id)
            ->get()
            ->keyBy(function($v) {
                return $v->voteable_type . '_' . $v->voteable_id;
            });

        return view('manajemenmahasiswa::forum.show', compact('thread', 'comments', 'userVotes', 'user'));
    }

    /**
     * Form edit thread.
     */
    public function edit(int $id)
    {
        $user   = Auth::user();
        $thread = $this->threadService->findThread($id);
        $isAdmin = $user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm']);

        if (!$isAdmin && $thread->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit thread ini.');
        }

        $categories = Thread::KATEGORI_LABELS;

        return view('manajemenmahasiswa::forum.edit', compact('thread', 'categories', 'user'));
    }

    /**
     * Update thread — Unified Post.
     */
    public function update(int $id, Request $request)
    {
        $user    = Auth::user();
        $isAdmin = $user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm']);
        $thread  = $this->threadService->findThread($id);

        // Hitung media existing yang dipertahankan
        $removeMedia   = $request->input('remove_media', []);
        $existingMedia = $thread->extractMediaUrls();
        $keptCount     = 0;
        foreach ($existingMedia as $media) {
            if (!in_array($media['url'], $removeMedia)) {
                $keptCount++;
            }
        }

        // Hitung total media (kept + new uploads)
        $newFileCount = $request->hasFile('media_files') ? count($request->file('media_files')) : 0;
        $totalMedia   = $keptCount + $newFileCount;

        if ($totalMedia > 5) {
            return back()->withErrors(['media_files' => 'Maksimal 5 gambar/video per post. Saat ini: ' . $keptCount . ' existing + ' . $newFileCount . ' baru.'])->withInput();
        }

        $validated = $request->validate([
            'judul'         => 'required|string|max:255',
            'kategori'      => 'required|string|in:' . implode(',', Thread::KATEGORI_LIST),
            'konten'        => 'nullable|string',
            'media_files'   => 'nullable|array|max:5',
            'media_files.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,webm|max:10240',
            'link_url'      => 'nullable|url|max:2000',
            'remove_media'  => 'nullable|array',
            'remove_media.*'=> 'string',
        ]);

        // Rebuild konten HTML
        $konten = '';

        // 1) Pertahankan media existing yang tidak dihapus
        foreach ($existingMedia as $media) {
            if (in_array($media['url'], $removeMedia)) {
                continue;
            }
            if ($media['type'] === 'image') {
                $konten .= '<img src="' . $media['url'] . '" alt="Media Post" style="max-width: 100%; border-radius: 8px; margin-bottom: 12px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">';
            } elseif ($media['type'] === 'video') {
                $konten .= '<video width="100%" controls style="border-radius: 8px; margin-bottom: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);"><source src="' . $media['url'] . '" type="video/mp4"></video>';
            }
        }

        // 2) Upload media baru ke Supabase
        if ($request->hasFile('media_files')) {
            foreach ($request->file('media_files') as $file) {
                $mime = $file->getMimeType();
                $isImage = str_starts_with($mime, 'image/');
                $folder = $isImage ? 'mk_mulmed/image' : 'mk_mulmed/video';
                
                // Gunakan bucket default dari config
                $path = $this->supabaseStorage->upload($file, $folder);
                
                if ($path) {
                    $url  = $this->supabaseStorage->getPublicUrl($path);
                    
                    // Simpan data file ke tabel mk_repo_mulmed
                    RepoMulmed::create([
                        'nama_file'         => $file->getClientOriginalName(),
                        'path_file'         => $path,
                        'tipe_file'         => $isImage ? RepoMulmed::TIPE_IMAGE : RepoMulmed::TIPE_VIDEO,
                        'judul_file'        => 'Forum Media: ' . $validated['judul'],
                        'deskripsi_meta'    => 'Diunggah pada edit forum mahasiswa',
                        'visibility_status' => RepoMulmed::VISIBILITY_PUBLIC,
                        'status_arsip'      => RepoMulmed::ARSIP_AKTIF,
                    ]);

                    if ($isImage) {
                        $konten .= '<img src="' . $url . '" alt="Media Post" style="max-width: 100%; border-radius: 8px; margin-bottom: 12px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">';
                    } else {
                        $konten .= '<video width="100%" controls style="border-radius: 8px; margin-bottom: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);"><source src="' . $url . '" type="' . $mime . '"></video>';
                    }
                } else {
                    return back()->withErrors(['media_files' => 'Gagal mengupload gambar baru ke Supabase. Pastikan konfigurasi benar.'])->withInput();
                }
            }
        }

        // 3) Link
        if (!empty($validated['link_url'])) {
            $linkUrl = e($validated['link_url']);
            $konten .= '<a href="' . $linkUrl . '" target="_blank" rel="noopener noreferrer" class="d-inline-flex p-3 rounded bg-light border border-primary-subtle text-primary fw-bold text-decoration-none mb-3" style="word-break: break-all;">🔗 ' . $linkUrl . '</a><br>';
        }

        // 4) Teks
        if (!empty($validated['konten'])) {
            $konten .= e($validated['konten']);
        }

        if (empty(trim($konten))) {
            return back()->withErrors(['konten' => 'Post harus memiliki konten (teks, gambar/video, atau link).'])->withInput();
        }

        $this->threadService->updateThread($id, $user->id, [
            'judul'        => $validated['judul'],
            'konten'       => $konten,
            'kategori'     => $validated['kategori'],
            'remove_media' => $removeMedia,
        ], $isAdmin);

        return redirect()
            ->route('manajemenmahasiswa.forum.show', $id)
            ->with('success', 'Thread berhasil diperbarui! ✏️');
    }

    /**
     * Vote thread.
     */
    public function vote(int $id, Request $request)
    {
        $value = (int) $request->input('value', 1);
        $result = $this->threadService->vote(Auth::id(), $id, $value);
        
        return response()->json($result);
    }

    /**
     * Hapus thread.
     */
    public function destroy(int $id)
    {
        $isAdmin = Auth::user()->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm']);
        $this->threadService->deleteThread($id, Auth::id(), $isAdmin);

        return redirect()->route('manajemenmahasiswa.forum.index')
            ->with('success', 'Thread berhasil dihapus.');
    }

    /**
     * Pin / Unpin thread.
     */
    public function pin(int $id)
    {
        $isAdmin = Auth::user()->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm']);
        if (!$isAdmin) {
            abort(403, 'Akses ditolak.');
        }

        $thread = $this->threadService->pinThread($id);
        
        $status = $thread->is_pinned ? 'dipin' : 'di-unpin';
        return back()->with('success', "Thread berhasil {$status}.");
    }

    /**
     * Tambah komentar.
     */
    public function storeComment(int $threadId, Request $request)
    {
        $request->validate(['konten' => 'required|string|min:3']);
        $this->commentService->addComment(Auth::id(), $threadId, $request->konten, $request->parent_id);

        return back()->with('success', 'Komentar berhasil ditambahkan!');
    }

    /**
     * Vote komentar.
     */
    public function voteComment(int $commentId, Request $request)
    {
        $value = (int) $request->input('value', 1);
        $result = $this->commentService->voteComment(Auth::id(), $commentId, $value);
        
        return response()->json($result);
    }

    /**
     * Hapus komentar.
     */
    public function destroyComment(int $commentId)
    {
        $isAdmin = Auth::user()->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm']);
        $this->commentService->deleteComment($commentId, Auth::id(), $isAdmin);

        return back()->with('success', 'Komentar berhasil dihapus.');
    }

    /**
     * Report thread.
     */
    public function reportThread(int $threadId, Request $request)
    {
        $request->validate([
            'alasan' => 'required|string|min:5|max:1000'
        ]);

        $thread = $this->threadService->findThread($threadId);

        if ($thread->user_id === Auth::id()) {
            return back()->withErrors(['alasan' => 'Anda tidak bisa melaporkan thread Anda sendiri.']);
        }

        $existingReport = \Modules\ManajemenMahasiswa\Models\ForumReport::where('user_id', Auth::id())
            ->where('thread_id', $threadId)
            ->first();

        if ($existingReport) {
            return back()->withErrors(['alasan' => 'Anda sudah melaporkan thread ini sebelumnya.']);
        }

        \Modules\ManajemenMahasiswa\Models\ForumReport::create([
            'user_id' => Auth::id(),
            'thread_id' => $threadId,
            'alasan' => $request->alasan,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Laporan berhasil dikirim dan akan segera diproses oleh admin.');
    }
}
