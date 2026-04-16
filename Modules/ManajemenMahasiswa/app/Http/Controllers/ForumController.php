<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\ManajemenMahasiswa\Models\Thread;
use Modules\ManajemenMahasiswa\Services\ThreadService;
use Modules\ManajemenMahasiswa\Services\CommentService;
use Modules\ManajemenMahasiswa\Services\GamificationService;

class ForumController extends Controller
{
    public function __construct(
        private ThreadService       $threadService,
        private CommentService      $commentService,
        private GamificationService $gamificationService,
    ) {}

    /**
     * Halaman utama forum — listing thread + leaderboard + stats user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $filters = $request->only(['search', 'kategori']);

        $threads     = $this->threadService->listThreads($filters, 10);
        $leaderboard = $this->gamificationService->getLeaderboard(5);
        $userStats   = $this->gamificationService->getUserStats($user->id);
        $categories  = Thread::KATEGORI_LABELS;

        // Record streak saat user mengakses forum
        $this->gamificationService->recordStreak($user->id);

        return view('manajemenmahasiswa::forum.mahasiswa', compact(
            'threads',
            'leaderboard',
            'userStats',
            'categories',
            'user',
        ));
    }

    /**
     * Form buat thread baru.
     */
    public function create()
    {
        $user       = Auth::user();
        $categories = Thread::KATEGORI_LABELS;

        return view('manajemenmahasiswa::forum.create', compact('categories', 'user'));
    }

    /**
     * Simpan thread baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'    => 'required|string|max:255',
            'konten'   => 'required|string|min:10',
            'kategori' => 'required|string|in:' . implode(',', Thread::KATEGORI_LIST),
        ]);

        $this->threadService->createThread(Auth::id(), $validated);

        return redirect()
            ->route('manajemenmahasiswa.forum.index')
            ->with('success', 'Thread berhasil dibuat! +10 XP 🎉');
    }

    /**
     * Detail thread + comments.
     */
    public function show(int $id, Request $request)
    {
        $user   = Auth::user();
        $thread = $this->threadService->findThread($id);
        $comments = $this->commentService->listComments($id, 20);

        // Ambil semua vote user untuk thread ini dan comment-commentnya
        $userVotes = \Modules\ManajemenMahasiswa\Models\Vote::where('user_id', $user->id)
            ->where(function ($q) use ($id, $comments) {
                $q->where(function ($q2) use ($id) {
                    $q2->where('voteable_type', Thread::class)
                       ->where('voteable_id', $id);
                })->orWhere(function ($q2) use ($comments) {
                    $commentIds = $comments->pluck('id')->toArray();
                    // Include reply IDs too
                    $replyIds = $comments->flatMap(fn($c) => $c->replies->pluck('id'))->toArray();
                    $allIds = array_merge($commentIds, $replyIds);
                    $q2->where('voteable_type', \Modules\ManajemenMahasiswa\Models\Comment::class)
                       ->whereIn('voteable_id', $allIds);
                });
            })
            ->get()
            ->keyBy(fn($v) => $v->voteable_type . '_' . $v->voteable_id);

        return view('manajemenmahasiswa::forum.show', compact(
            'thread',
            'comments',
            'userVotes',
            'user',
        ));
    }

    /**
     * Vote thread (AJAX).
     */
    public function vote(Request $request, int $id)
    {
        $request->validate([
            'value' => 'required|integer|in:-1,1',
        ]);

        $result = $this->threadService->vote(Auth::id(), $id, $request->value);

        return response()->json($result);
    }

    /**
     * Hapus thread.
     */
    public function destroy(int $id)
    {
        $user   = Auth::user();
        $roles  = $user->roles->pluck('name');
        $isAdmin = $roles->intersect(['superadmin', 'admin', 'dosen_koordinator'])->isNotEmpty();

        $this->threadService->deleteThread($id, $user->id, $isAdmin);

        return redirect()
            ->route('manajemenmahasiswa.forum.index')
            ->with('success', 'Thread berhasil dihapus.');
    }

    /**
     * Tambah komentar ke thread (POST).
     */
    public function storeComment(Request $request, int $threadId)
    {
        $validated = $request->validate([
            'konten'    => 'required|string|min:3',
            'parent_id' => 'nullable|integer|exists:mk_comments,id',
        ]);

        $this->commentService->addComment(
            Auth::id(),
            $threadId,
            $validated['konten'],
            $validated['parent_id'] ?? null
        );

        return redirect()
            ->route('manajemenmahasiswa.forum.show', $threadId)
            ->with('success', 'Komentar berhasil ditambahkan! +5 XP 💬');
    }

    /**
     * Vote comment (AJAX).
     */
    public function voteComment(Request $request, int $commentId)
    {
        $request->validate([
            'value' => 'required|integer|in:-1,1',
        ]);

        $result = $this->commentService->voteComment(Auth::id(), $commentId, $request->value);

        return response()->json($result);
    }

    /**
     * Hapus komentar.
     */
    public function destroyComment(int $commentId)
    {
        $user   = Auth::user();
        $roles  = $user->roles->pluck('name');
        $isAdmin = $roles->intersect(['superadmin', 'admin', 'dosen_koordinator'])->isNotEmpty();

        $comment = \Modules\ManajemenMahasiswa\Models\Comment::findOrFail($commentId);
        $threadId = $comment->thread_id;

        $this->commentService->deleteComment($commentId, $user->id, $isAdmin);

        return redirect()
            ->route('manajemenmahasiswa.forum.show', $threadId)
            ->with('success', 'Komentar berhasil dihapus.');
    }
}
