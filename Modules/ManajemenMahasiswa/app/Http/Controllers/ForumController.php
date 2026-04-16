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

        // Fetch necessary data for the forum view
        $threads = $this->threadService->listThreads($request->all(), 15);
        $leaderboard = $this->gamificationService->getLeaderboard(10);
        $userStats = $this->gamificationService->getUserStats($user->id);
        $categories = Thread::KATEGORI_LABELS;

        $viewData = compact('threads', 'leaderboard', 'userStats', 'categories', 'user');

        // Admin, GPM, Pengurus, Dosen Koordinator: admin layout
        if ($roles->intersect(['superadmin', 'admin', 'dosen_koordinator', 'pengurus_himpunan', 'gpm', 'admin_kemahasiswaan'])->isNotEmpty()) {
            return view('manajemenmahasiswa::forum.admin', $viewData);
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
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string|min:10',
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
        $user = Auth::user();

        return view('manajemenmahasiswa::forum.show', compact('id'));
    }
}
