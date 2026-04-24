<?php

namespace Modules\ManajemenMahasiswa\Services;

use Illuminate\Support\Facades\DB;
use Modules\ManajemenMahasiswa\Models\Badge;
use Modules\ManajemenMahasiswa\Models\UserBadge;
use Modules\ManajemenMahasiswa\Models\XpLog;
use Modules\ManajemenMahasiswa\Models\Streak;
use Modules\ManajemenMahasiswa\Models\Thread;
use Modules\ManajemenMahasiswa\Models\Comment;
use Modules\ManajemenMahasiswa\Models\Vote;
use Illuminate\Database\Eloquent\Model;

class GamificationService
{
    // =========================================================================
    // Tier Reputasi
    // =========================================================================

    /**
     * Daftar tier reputasi berdasarkan level.
     */
    const TIERS = [
        ['min_level' => 1,  'max_level' => 2,  'name' => 'Pendatang Baru',  'icon' => '🌱'],
        ['min_level' => 3,  'max_level' => 4,  'name' => 'Kontributor',     'icon' => '📝'],
        ['min_level' => 5,  'max_level' => 6,  'name' => 'Penjelajah',      'icon' => '🧭'],
        ['min_level' => 7,  'max_level' => 8,  'name' => 'Ahli',            'icon' => '🎯'],
        ['min_level' => 9,  'max_level' => 10, 'name' => 'Pakar',           'icon' => '⭐'],
        ['min_level' => 11, 'max_level' => 12, 'name' => 'Mentor',          'icon' => '🏅'],
        ['min_level' => 13, 'max_level' => 99, 'name' => 'Legenda Kampus',  'icon' => '👑'],
    ];

    /**
     * Ambil informasi tier berdasarkan level.
     */
    public function getTierInfo(int $level): array
    {
        foreach (self::TIERS as $tier) {
            if ($level >= $tier['min_level'] && $level <= $tier['max_level']) {
                return [
                    'name' => $tier['name'],
                    'icon' => $tier['icon'],
                ];
            }
        }
        return ['name' => 'Pendatang Baru', 'icon' => '🌱'];
    }

    // =========================================================================
    // XP
    // =========================================================================

    /**
     * Berikan XP ke user berdasarkan aksi yang dilakukan.
     * Mengembalikan array ['log' => XpLog, 'leveled_up' => bool, 'old_level' => int, 'new_level' => int]
     */
    public function awardXp(int $userId, string $action, ?Model $reference = null): array
    {
        $xpAmount = XpLog::XP_MAP[$action] ?? 0;

        // Hitung level SEBELUM award
        $oldTotalXp = $this->getTotalXp($userId);
        $oldLevel   = $this->calculateLevel($oldTotalXp);

        $log = XpLog::create([
            'user_id'        => $userId,
            'action'         => $action,
            'xp_amount'      => $xpAmount,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id'   => $reference?->id,
        ]);

        // Hitung level SESUDAH award
        $newTotalXp = $oldTotalXp + $xpAmount;
        // XP tidak boleh negatif
        if ($newTotalXp < 0) {
            $newTotalXp = 0;
        }
        $newLevel   = $this->calculateLevel($newTotalXp);
        $leveledUp  = $newLevel > $oldLevel;

        // Setelah memberi XP, cek apakah ada badge baru yang bisa diraih
        $this->checkAndAwardBadges($userId);

        return [
            'log'        => $log,
            'leveled_up' => $leveledUp,
            'old_level'  => $oldLevel,
            'new_level'  => $newLevel,
        ];
    }

    /**
     * Penalti XP untuk pemberi downvote (-1 XP).
     * XP total tidak akan pernah negatif.
     */
    public function penalizeDownvote(int $userId, ?Model $reference = null): array
    {
        return $this->awardXp($userId, XpLog::ACTION_DOWNVOTE_PENALTY, $reference);
    }

    /**
     * Hitung total XP user (minimum 0).
     */
    public function getTotalXp(int $userId): int
    {
        return max(0, (int) XpLog::where('user_id', $userId)->sum('xp_amount'));
    }

    // =========================================================================
    // Level
    // =========================================================================

    /**
     * Hitung level dari total XP.
     * Formula: Level = floor(sqrt(total_xp / 100)) + 1
     */
    public function calculateLevel(int $totalXp): int
    {
        if ($totalXp <= 0) return 1;
        return (int) floor(sqrt($totalXp / 100)) + 1;
    }

    /**
     * Hitung XP yang dibutuhkan untuk mencapai level tertentu.
     */
    public function getXpForLevel(int $level): int
    {
        if ($level <= 1) return 0;
        return (int) pow($level - 1, 2) * 100;
    }

    /**
     * XP yang dibutuhkan untuk naik ke level berikutnya.
     */
    public function getXpForNextLevel(int $currentLevel): int
    {
        return $this->getXpForLevel($currentLevel + 1);
    }

    // =========================================================================
    // User Stats
    // =========================================================================

    /**
     * Ambil statistik gamification lengkap untuk user.
     */
    public function getUserStats(int $userId): array
    {
        $totalXp      = $this->getTotalXp($userId);
        $level        = $this->calculateLevel($totalXp);
        $xpForCurrent = $this->getXpForLevel($level);
        $xpForNext    = $this->getXpForNextLevel($level);
        $rank         = $this->getUserRank($userId);
        $tier         = $this->getTierInfo($level);
        $streak       = Streak::where('user_id', $userId)->first();
        $badges       = UserBadge::with('badge')
                            ->where('user_id', $userId)
                            ->get()
                            ->pluck('badge');

        return [
            'total_xp'       => $totalXp,
            'level'          => $level,
            'tier_name'      => $tier['name'],
            'tier_icon'      => $tier['icon'],
            'xp_current'     => $totalXp - $xpForCurrent,
            'xp_needed'      => $xpForNext - $xpForCurrent,
            'xp_for_next'    => $xpForNext,
            'rank'           => $rank,
            'current_streak' => $streak?->current_streak ?? 0,
            'longest_streak' => $streak?->longest_streak ?? 0,
            'badges'         => $badges,
        ];
    }

    /**
     * Ranking user berdasarkan total XP (posisi 1 = terbanyak).
     */
    public function getUserRank(int $userId): int
    {
        $userXp = $this->getTotalXp($userId);

        $rank = XpLog::select('user_id')
            ->groupBy('user_id')
            ->havingRaw('SUM(xp_amount) > ?', [$userXp])
            ->count();

        return $rank + 1;
    }

    // =========================================================================
    // Leaderboard
    // =========================================================================

    /**
     * Top users berdasarkan total XP.
     */
    public function getLeaderboard(int $limit = 10): \Illuminate\Support\Collection
    {
        return XpLog::select('user_id', DB::raw('SUM(xp_amount) as total_xp'))
            ->groupBy('user_id')
            ->orderByDesc('total_xp')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                $user   = \App\Models\User::find($row->user_id);
                $level  = $this->calculateLevel($row->total_xp);
                $tier   = $this->getTierInfo($level);
                $badges = UserBadge::with('badge')
                    ->where('user_id', $row->user_id)
                    ->get()
                    ->pluck('badge');

                return (object) [
                    'user_id'   => $row->user_id,
                    'name'      => $user?->name ?? 'Unknown',
                    'total_xp'  => (int) $row->total_xp,
                    'level'     => $level,
                    'tier_name' => $tier['name'],
                    'tier_icon' => $tier['icon'],
                    'badges'    => $badges,
                ];
            });
    }

    // =========================================================================
    // Streak
    // =========================================================================

    /**
     * Record streak harian. Return true jika ada streak baru.
     */
    public function recordStreak(int $userId): bool
    {
        $streak = Streak::firstOrCreate(
            ['user_id' => $userId],
            ['current_streak' => 0, 'longest_streak' => 0]
        );

        $isNew = $streak->recordActivity();

        if ($isNew) {
            // Beri XP daily login
            $this->awardXp($userId, XpLog::ACTION_DAILY_LOGIN);

            // Bonus streak setiap 7 hari berturut-turut
            if ($streak->current_streak > 0 && $streak->current_streak % 7 === 0) {
                $this->awardXp($userId, XpLog::ACTION_STREAK_BONUS);
            }

            // Bonus streak setiap 30 hari berturut-turut (+100 XP)
            if ($streak->current_streak > 0 && $streak->current_streak % 30 === 0) {
                $this->awardXp($userId, XpLog::ACTION_STREAK_BONUS_30);
            }
        }

        return $isNew;
    }

    // =========================================================================
    // Badges
    // =========================================================================

    /**
     * Evaluasi semua badge dan berikan yang memenuhi syarat.
     */
    public function checkAndAwardBadges(int $userId): void
    {
        $badges = Badge::all();
        $earnedBadgeIds = UserBadge::where('user_id', $userId)->pluck('badge_id')->toArray();

        foreach ($badges as $badge) {
            // Sudah punya badge ini — skip
            if (in_array($badge->id, $earnedBadgeIds)) {
                continue;
            }

            $currentValue = $this->getCriteriaValue($userId, $badge->criteria_type);

            if ($currentValue >= $badge->criteria_value) {
                UserBadge::create([
                    'user_id'  => $userId,
                    'badge_id' => $badge->id,
                    'earned_at' => now(),
                ]);
            }
        }
    }

    /**
     * Ambil nilai criteria saat ini untuk user.
     */
    private function getCriteriaValue(int $userId, string $criteriaType): int
    {
        return match ($criteriaType) {
            Badge::CRITERIA_THREAD_COUNT => Thread::where('user_id', $userId)->count(),
            Badge::CRITERIA_COMMENT_COUNT => Comment::where('user_id', $userId)->count(),
            Badge::CRITERIA_UPVOTE_COUNT => Vote::whereHas('voteable', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->where('value', 1)->count(),
            Badge::CRITERIA_BEST_ANSWER_COUNT => Comment::where('user_id', $userId)->where('is_best_answer', true)->count(),
            Badge::CRITERIA_STREAK => Streak::where('user_id', $userId)->value('longest_streak') ?? 0,
            Badge::CRITERIA_TOTAL_XP => $this->getTotalXp($userId),
            Badge::CRITERIA_FIRST_ANSWER_COUNT => $this->getFirstAnswerCount($userId),
            Badge::CRITERIA_ALUMNI_COMMENT_COUNT => $this->getAlumniCategoryCommentCount($userId),
            Badge::CRITERIA_DOSEN_COMMENT_COUNT => $this->getDosenCategoryCommentCount($userId),
            default => 0,
        };
    }

    /**
     * Hitung berapa kali user menjadi penjawab pertama di thread.
     */
    private function getFirstAnswerCount(int $userId): int
    {
        return Comment::where('user_id', $userId)
            ->whereNull('parent_id')
            ->whereRaw('id = (SELECT MIN(c2.id) FROM mk_comments c2 WHERE c2.thread_id = mk_comments.thread_id AND c2.parent_id IS NULL)')
            ->count();
    }

    /**
     * Hitung komentar alumni di kategori Loker & Karir.
     */
    private function getAlumniCategoryCommentCount(int $userId): int
    {
        $user = \App\Models\User::find($userId);
        if (!$user || !$user->hasRole('alumni')) {
            return 0;
        }

        return Comment::where('user_id', $userId)
            ->whereHas('thread', function ($q) {
                $q->whereJsonContains('kategori', 'loker_karir');
            })
            ->count();
    }

    /**
     * Hitung komentar dosen di kategori Tanya Tugas / Info Skripsi.
     */
    private function getDosenCategoryCommentCount(int $userId): int
    {
        $user = \App\Models\User::find($userId);
        if (!$user || !$user->hasAnyRole(['dosen', 'dosen_koordinator'])) {
            return 0;
        }

        return Comment::where('user_id', $userId)
            ->whereHas('thread', function ($q) {
                $q->where(function ($q2) {
                    $q2->whereJsonContains('kategori', 'tanya_tugas')
                       ->orWhereJsonContains('kategori', 'info_skripsi');
                });
            })
            ->count();
    }
}
