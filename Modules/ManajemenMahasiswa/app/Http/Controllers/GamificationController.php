<?php

namespace Modules\ManajemenMahasiswa\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\ManajemenMahasiswa\Services\GamificationService;

class GamificationController extends Controller
{
    public function __construct(
        private GamificationService $gamificationService,
    ) {}

    /**
     * API: Leaderboard top users.
     */
    public function leaderboard()
    {
        $leaderboard = $this->gamificationService->getLeaderboard(10);

        return response()->json([
            'data' => $leaderboard,
        ]);
    }

    /**
     * API: Stats user saat ini.
     */
    public function userStats()
    {
        $user  = Auth::user();
        $stats = $this->gamificationService->getUserStats($user->id);

        return response()->json([
            'data' => $stats,
        ]);
    }
}
