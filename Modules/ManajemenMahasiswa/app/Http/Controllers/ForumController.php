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

class ForumController extends Controller
{
    public function __construct(
        private ThreadService $threadService,
        private CommentService $commentService,
        private GamificationService $gamificationService,
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
     * Simpan thread baru.
     */
    public function store(Request $request)
    {
        $format = $request->input('format', 'text');

        // Base validation
        $rules = [
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|in:' . implode(',', Thread::KATEGORI_LIST),
        ];

        // Format-specific validation
        if ($format === 'media') {
            $rules['media_files'] = 'required|array|min:1|max:5';
            $rules['media_files.*'] = 'file|mimes:jpg,jpeg,png,gif,webp,mp4,webm|max:10240';
            $rules['media_caption'] = 'nullable|string';
        } else {
            $rules['konten'] = 'required|string|min:10';
        }

        $validated = $request->validate($rules);

        // Build konten based on format
        $konten = $validated['konten'] ?? '';

        if ($format === 'media' && $request->hasFile('media_files')) {
            $mediaHtml = '';

            foreach ($request->file('media_files') as $file) {
                $path = $file->store('forum/media', 'public');
                $url = Storage::url($path);
                $mime = $file->getMimeType();

                if (str_starts_with($mime, 'image/')) {
                    $mediaHtml .= '<img src="' . $url . '" alt="Media Post" style="max-width: 100%; border-radius: 8px; margin-bottom: 15px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">';
                } elseif (str_starts_with($mime, 'video/')) {
                    $mediaHtml .= '<video width="100%" controls style="border-radius: 8px; margin-bottom: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);"><source src="' . $url . '" type="' . $mime . '"></video>';
                }
            }

            $caption = $request->input('media_caption', '');
            $konten = $mediaHtml . ($caption ? '<br>' . e($caption) : '');
        }

        $this->threadService->createThread(Auth::id(), [
            'judul' => $validated['judul'],
            'konten' => $konten,
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
