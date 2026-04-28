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
            ->keyBy(function ($v) {
                return $v->voteable_type . '_' . $v->voteable_id;
            });

        // Preload tier info per thread author
        $authorTiers = [];
        foreach ($threads as $thread) {
            $authorId = $thread->user_id;
            if (!isset($authorTiers[$authorId])) {
                $totalXp = $this->gamificationService->getTotalXp($authorId);
                $level = $this->gamificationService->calculateLevel($totalXp);
                $tier = $this->gamificationService->getTierInfo($level);
                $authorTiers[$authorId] = [
                    'level' => $level,
                    'tier_name' => $tier['name'],
                    'tier_icon' => $tier['icon'],
                ];
            }
        }

        // Load reports for admin
        $isAdmin = $user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan']);
        $forumReports = $isAdmin
            ? \Modules\ManajemenMahasiswa\Models\ForumReport::with(['reporter', 'thread.author'])->latest()->get()
            : collect();

        $viewData = compact('threads', 'leaderboard', 'userStats', 'categories', 'user', 'userVotes', 'authorTiers', 'forumReports');

        return view('manajemenmahasiswa::forum.index', $viewData);
    }

    /**
     * Form buat thread baru.
     */
    public function create()
    {
        $user = Auth::user();
        $categories = Thread::KATEGORI_LABELS;
        $drafts = \Modules\ManajemenMahasiswa\Models\ThreadDraft::where('user_id', $user->id)->latest()->get();

        return view('manajemenmahasiswa::forum.create', compact('categories', 'user', 'drafts'));
    }

    /**
     * Simpan / Update Draft (AJAX)
     */
    public function saveDraft(Request $request)
    {
        $request->validate([
            'draft_id' => 'nullable|integer',
            'judul' => 'nullable|string|max:255',
            'kategori' => 'nullable|array',
            'kategori.*' => 'string',
            'konten' => 'nullable|string',
        ]);

        $draftId = $request->input('draft_id');

        if ($draftId) {
            $draft = \Modules\ManajemenMahasiswa\Models\ThreadDraft::where('id', $draftId)
                ->where('user_id', Auth::id())
                ->first();

            if ($draft) {
                $draft->update([
                    'judul' => $request->input('judul'),
                    'kategori' => $request->input('kategori'),
                    'konten' => $request->input('konten'),
                ]);
            } else {
                $draft = \Modules\ManajemenMahasiswa\Models\ThreadDraft::create([
                    'user_id' => Auth::id(),
                    'judul' => $request->input('judul'),
                    'kategori' => $request->input('kategori'),
                    'konten' => $request->input('konten'),
                ]);
            }
        } else {
            $draft = \Modules\ManajemenMahasiswa\Models\ThreadDraft::create([
                'user_id' => Auth::id(),
                'judul' => $request->input('judul'),
                'kategori' => $request->input('kategori'),
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
        \Modules\ManajemenMahasiswa\Models\ThreadDraft::where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return redirect()->back()->with('success', 'Draf berhasil dihapus.');
    }

    /**
     * Simpan thread baru — Unified Post (teks + media + link dalam 1 request).
     */
    public function store(Request $request)
    {
        $rules = [
            'judul' => 'required|string|max:255',
            'kategori' => 'required|array|min:1',
            'kategori.*' => 'string|in:' . implode(',', Thread::KATEGORI_LIST),
            'konten' => 'nullable|string',
            'media_files' => 'nullable|array|max:5',
            'media_files.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,webm|max:10240',
            'link_url' => 'nullable|url|max:2000',
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
                    $url = $this->supabaseStorage->getPublicUrl($path);

                    // Simpan data file ke tabel mk_repo_mulmed
                    RepoMulmed::create([
                        'nama_file' => $file->getClientOriginalName(),
                        'path_file' => $path,
                        'tipe_file' => $isImage ? RepoMulmed::TIPE_IMAGE : RepoMulmed::TIPE_VIDEO,
                        'judul_file' => 'Forum Media: ' . $validated['judul'],
                        'deskripsi_meta' => 'Diunggah pada forum mahasiswa',
                        'visibility_status' => RepoMulmed::VISIBILITY_PUBLIC,
                        'status_arsip' => RepoMulmed::ARSIP_AKTIF,
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

        // Check level before creating thread
        $oldLevel = $this->gamificationService->calculateLevel(
            $this->gamificationService->getTotalXp(Auth::id())
        );

        $this->threadService->createThread(Auth::id(), [
            'judul' => $validated['judul'],
            'konten' => $konten,
            'kategori' => $validated['kategori'],
        ]);

        // Check level after
        $newLevel = $this->gamificationService->calculateLevel(
            $this->gamificationService->getTotalXp(Auth::id())
        );

        // Hapus draf jika post dikirim dari draf
        if ($request->filled('draft_id')) {
            \Modules\ManajemenMahasiswa\Models\ThreadDraft::where('id', $request->input('draft_id'))
                ->where('user_id', Auth::id())
                ->delete();
        }

        $redirect = redirect()->route('manajemenmahasiswa.forum.index')
            ->with('success', 'Thread berhasil dibuat! +10 XP');

        if ($newLevel > $oldLevel) {
            $tier = $this->gamificationService->getTierInfo($newLevel);
            $redirect = $redirect->with('level_up', [
                'old_level' => $oldLevel,
                'new_level' => $newLevel,
                'tier_name' => $tier['name'],
                'tier_icon' => $tier['icon'],
            ]);
        }

        return $redirect;
    }

    /**
     * Detail thread + comments.
     */
    public function show(int $id, Request $request)
    {
        $user = Auth::user();

        $thread = $this->threadService->findThread($id);
        $comments = $this->commentService->listComments($id);

        $userVotes = \Modules\ManajemenMahasiswa\Models\Vote::where('user_id', $user->id)
            ->get()
            ->keyBy(function ($v) {
                return $v->voteable_type . '_' . $v->voteable_id;
            });

        $isPersonalPinned = \Modules\ManajemenMahasiswa\Models\ThreadPersonalPin::where('user_id', $user->id)
            ->where('thread_id', $id)
            ->exists();

        // Preload tier info for thread author + all commenters
        $authorTiers = [];
        $allUserIds = collect([$thread->user_id]);
        foreach ($comments as $comment) {
            $allUserIds->push($comment->user_id);
            if ($comment->allReplies) {
                foreach ($comment->allReplies as $reply) {
                    $allUserIds->push($reply->user_id);
                }
            }
        }
        foreach ($allUserIds->unique() as $authorId) {
            $totalXp = $this->gamificationService->getTotalXp($authorId);
            $level = $this->gamificationService->calculateLevel($totalXp);
            $tier = $this->gamificationService->getTierInfo($level);
            $authorTiers[$authorId] = [
                'level' => $level,
                'tier_name' => $tier['name'],
                'tier_icon' => $tier['icon'],
            ];
        }

        // Record streak (user visited a thread detail page)
        $this->gamificationService->recordStreak($user->id);

        return view('manajemenmahasiswa::forum.show', compact('thread', 'comments', 'userVotes', 'user', 'isPersonalPinned', 'authorTiers'));
    }

    /**
     * Form edit thread.
     */
    public function edit(int $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $thread = $this->threadService->findThread($id);

        if ($thread->user_id !== $user->id) {
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
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $thread = $this->threadService->findThread($id);

        if ($thread->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit thread ini.');
        }

        // Hitung media existing yang dipertahankan
        $removeMedia = $request->input('remove_media', []);
        $existingMedia = $thread->extractMediaUrls();
        $keptCount = 0;
        foreach ($existingMedia as $media) {
            if (!in_array($media['url'], $removeMedia)) {
                $keptCount++;
            }
        }

        // Hitung total media (kept + new uploads)
        $newFileCount = $request->hasFile('media_files') ? count($request->file('media_files')) : 0;
        $totalMedia = $keptCount + $newFileCount;

        if ($totalMedia > 5) {
            return back()->withErrors(['media_files' => 'Maksimal 5 gambar/video per post. Saat ini: ' . $keptCount . ' existing + ' . $newFileCount . ' baru.'])->withInput();
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|array|min:1',
            'kategori.*' => 'string|in:' . implode(',', Thread::KATEGORI_LIST),
            'konten' => 'nullable|string',
            'media_files' => 'nullable|array|max:5',
            'media_files.*' => 'file|mimes:jpg,jpeg,png,gif,webp,mp4,webm|max:10240',
            'link_url' => 'nullable|url|max:2000',
            'remove_media' => 'nullable|array',
            'remove_media.*' => 'string',
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
                    $url = $this->supabaseStorage->getPublicUrl($path);

                    // Simpan data file ke tabel mk_repo_mulmed
                    RepoMulmed::create([
                        'nama_file' => $file->getClientOriginalName(),
                        'path_file' => $path,
                        'tipe_file' => $isImage ? RepoMulmed::TIPE_IMAGE : RepoMulmed::TIPE_VIDEO,
                        'judul_file' => 'Forum Media: ' . $validated['judul'],
                        'deskripsi_meta' => 'Diunggah pada edit forum mahasiswa',
                        'visibility_status' => RepoMulmed::VISIBILITY_PUBLIC,
                        'status_arsip' => RepoMulmed::ARSIP_AKTIF,
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
            'judul' => $validated['judul'],
            'konten' => $konten,
            'kategori' => $validated['kategori'],
            'remove_media' => $removeMedia,
        ]);

        return redirect()
            ->route('manajemenmahasiswa.forum.show', $id)
            ->with('success', 'Thread berhasil diperbarui!');
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
        $isAdmin = Auth::user()->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan']);
        $this->threadService->deleteThread($id, Auth::id(), $isAdmin);

        return redirect()->route('manajemenmahasiswa.forum.index')
            ->with('success', 'Thread berhasil dihapus.');
    }

    /**
     * Pin / Unpin thread.
     */
    public function pin(int $id)
    {
        $isAdmin = Auth::user()->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan']);
        if (!$isAdmin) {
            abort(403, 'Akses ditolak.');
        }

        $thread = $this->threadService->pinThread($id);

        $status = $thread->is_pinned ? 'dipin' : 'di-unpin';
        return back()->with('success', "Thread berhasil {$status}.");
    }

    /**
     * Lock / Unlock thread (admin only).
     */
    public function lockThread(int $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan'])) {
            abort(403, 'Akses ditolak.');
        }

        $thread = Thread::findOrFail($id);
        $newLocked = !$thread->is_locked;
        // Use query builder to avoid touching updated_at
        Thread::where('id', $id)->update(['is_locked' => $newLocked]);

        $status = $newLocked ? 'dikunci' : 'dibuka kuncinya';
        return back()->with('success', "Thread berhasil {$status}.");
    }

    /**
     * Pin / Unpin pribadi thread.
     */
    public function personalPin(int $id)
    {
        $userId = Auth::id();
        $thread = $this->threadService->findThread($id); // ensure thread exists

        $existingPin = \Modules\ManajemenMahasiswa\Models\ThreadPersonalPin::where('user_id', $userId)
            ->where('thread_id', $id)
            ->first();

        if ($existingPin) {
            $existingPin->delete();
            $isPinned = false;
        } else {
            \Modules\ManajemenMahasiswa\Models\ThreadPersonalPin::create([
                'user_id' => $userId,
                'thread_id' => $id,
            ]);
            $isPinned = true;
        }

        if (request()->ajax()) {
            return response()->json(['is_pinned' => $isPinned]);
        }

        $status = $isPinned ? 'dipin secara pribadi' : 'di-unpin dari pin pribadi';
        return back()->with('success', "Thread berhasil {$status}.");
    }

    /**
     * Tambah komentar.
     */
    public function storeComment(int $threadId, Request $request)
    {
        $request->validate(['konten' => 'required|string|min:3']);

        $oldLevel = $this->gamificationService->calculateLevel(
            $this->gamificationService->getTotalXp(Auth::id())
        );

        $this->commentService->addComment(Auth::id(), $threadId, $request->konten, $request->parent_id);

        $newLevel = $this->gamificationService->calculateLevel(
            $this->gamificationService->getTotalXp(Auth::id())
        );

        $redirect = back()->with('success', 'Komentar berhasil ditambahkan! +5 XP');

        if ($newLevel > $oldLevel) {
            $tier = $this->gamificationService->getTierInfo($newLevel);
            $redirect = $redirect->with('level_up', [
                'old_level' => $oldLevel,
                'new_level' => $newLevel,
                'tier_name' => $tier['name'],
                'tier_icon' => $tier['icon'],
            ]);
        }

        return $redirect;
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
     * Update komentar.
     */
    public function updateComment(int $commentId, Request $request)
    {
        $request->validate(['konten' => 'required|string|min:3']);

        try {
            $this->commentService->updateComment($commentId, Auth::id(), $request->konten);
            return back()->with('success', 'Komentar berhasil diperbarui.');
        } catch (\RuntimeException $e) {
            return back()->withErrors(['konten' => $e->getMessage()]);
        }
    }

    /**
     * Hapus komentar.
     */
    public function destroyComment(int $commentId)
    {
        $isAdmin = Auth::user()->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan']);
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

    /**
     * Tandai komentar sebagai Best Answer.
     */
    public function markBestAnswer(int $threadId, int $commentId)
    {
        $user = Auth::user();
        $isAdmin = $user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan']);

        try {
            $this->threadService->markBestAnswer($threadId, $commentId, $user->id);
            return back()->with('success', 'Komentar ditandai sebagai Jawaban Terbaik! Penulis mendapat +15 XP');
        } catch (\RuntimeException $e) {
            return back()->withErrors(['best_answer' => $e->getMessage()]);
        }
    }

    // =========================================================================
    // Report Management (Admin)
    // =========================================================================

    /**
     * Dismiss (hapus) sebuah laporan.
     */
    public function dismissReport(int $reportId)
    {
        $report = \Modules\ManajemenMahasiswa\Models\ForumReport::findOrFail($reportId);
        $report->delete();

        return back()->with('success', 'Laporan berhasil dihapus.');
    }

    /**
     * Hapus thread yang dilaporkan + semua laporannya.
     */
    public function deleteReportedThread(int $reportId)
    {
        $report = \Modules\ManajemenMahasiswa\Models\ForumReport::findOrFail($reportId);
        $thread = $report->thread;

        if ($thread) {
            \Modules\ManajemenMahasiswa\Models\ForumReport::where('thread_id', $thread->id)->delete();
            $thread->delete();
        } else {
            $report->delete();
        }

        return back()->with('success', 'Thread yang dilaporkan berhasil dihapus.');
    }

    /**
     * Kunci thread yang dilaporkan + hapus laporannya.
     */
    public function lockReportedThread(int $reportId)
    {
        $report = \Modules\ManajemenMahasiswa\Models\ForumReport::findOrFail($reportId);
        $thread = $report->thread;

        if ($thread) {
            // Use query builder to avoid touching updated_at
            Thread::where('id', $thread->id)->update(['is_locked' => true]);
            \Modules\ManajemenMahasiswa\Models\ForumReport::where('thread_id', $thread->id)->delete();
        } else {
            $report->delete();
        }

        return back()->with('success', 'Thread berhasil dikunci dan laporan dihapus.');
    }
}
