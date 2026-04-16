<?php

namespace Modules\ManajemenMahasiswa\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\ManajemenMahasiswa\Models\Badge;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            [
                'name' => 'Thread Starter',
                'slug' => 'thread-starter',
                'icon' => '📝',
                'description' => 'Membuat 1 diskusi pertama. Keren!',
                'criteria_type' => Badge::CRITERIA_THREAD_COUNT,
                'criteria_value' => 1,
            ],
            [
                'name' => 'Kontributor Aktif',
                'slug' => 'kontributor-aktif',
                'icon' => '💬',
                'description' => 'Telah memberikan 10 komentar yang bermanfaat.',
                'criteria_type' => Badge::CRITERIA_COMMENT_COUNT,
                'criteria_value' => 10,
            ],
            [
                'name' => 'Hot Topic',
                'slug' => 'hot-topic',
                'icon' => '🔥',
                'description' => 'Menerima total 50 upvote dari orang lain.',
                'criteria_type' => Badge::CRITERIA_UPVOTE_COUNT,
                'criteria_value' => 50,
            ],
            [
                'name' => 'Top Helper',
                'slug' => 'top-helper',
                'icon' => '⭐',
                'description' => 'Menjadi jawaban terbaik sebanyak 5 kali.',
                'criteria_type' => Badge::CRITERIA_BEST_ANSWER_COUNT,
                'criteria_value' => 5,
            ],
            [
                'name' => 'Streak Master',
                'slug' => 'streak-master',
                'icon' => '🏆',
                'description' => 'Login setiap hari selama 30 hari tanpa terputus.',
                'criteria_type' => Badge::CRITERIA_STREAK,
                'criteria_value' => 30,
            ],
            [
                'name' => 'Rising Star',
                'slug' => 'rising-star',
                'icon' => '🌟',
                'description' => 'Telah mengumpulkan total 500 XP.',
                'criteria_type' => Badge::CRITERIA_TOTAL_XP,
                'criteria_value' => 500,
            ],
        ];

        foreach ($badges as $badge) {
            DB::table('mk_badges')->updateOrInsert(
                ['slug' => $badge['slug']],
                array_merge($badge, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
