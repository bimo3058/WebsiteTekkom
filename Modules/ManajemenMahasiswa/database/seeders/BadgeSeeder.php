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
            // --- Badge awal (sudah ada, disesuaikan ikon) ---
            [
                'name' => 'Thread Starter',
                'slug' => 'thread-starter',
                'icon' => '🚀',
                'image' => 'modules/manajemenmahasiswa/images/badges/ThreadStarterBadge.png',
                'description' => 'Membuat 10 thread pertama. Keren!',
                'criteria_type' => Badge::CRITERIA_THREAD_COUNT,
                'criteria_value' => 10,
            ],
            [
                'name' => 'Kontributor Aktif',
                'slug' => 'kontributor-aktif',
                'icon' => '💬',
                'image' => 'modules/manajemenmahasiswa/images/badges/KontributorAktifBadge.png',
                'description' => 'Telah memberikan 10 komentar yang bermanfaat.',
                'criteria_type' => Badge::CRITERIA_COMMENT_COUNT,
                'criteria_value' => 10,
            ],
            [
                'name' => 'Hot Topic',
                'slug' => 'hot-topic',
                'icon' => '🔥',
                'image' => 'modules/manajemenmahasiswa/images/badges/HotTopicBadge.png',
                'description' => 'Menerima total 50 upvote dari orang lain.',
                'criteria_type' => Badge::CRITERIA_UPVOTE_COUNT,
                'criteria_value' => 50,
            ],
            [
                'name' => 'Helpful',
                'slug' => 'top-helper',
                'icon' => '🤝',
                'image' => 'modules/manajemenmahasiswa/images/badges/HelpfulBadge.png',
                'description' => '10 komentar ditandai Best Answer.',
                'criteria_type' => Badge::CRITERIA_BEST_ANSWER_COUNT,
                'criteria_value' => 10,
            ],
            [
                'name' => 'Marathon',
                'slug' => 'streak-master',
                'icon' => '🏃',
                'image' => 'modules/manajemenmahasiswa/images/badges/MarathonBadge.png',
                'description' => 'Login streak 30 hari berturut-turut.',
                'criteria_type' => Badge::CRITERIA_STREAK,
                'criteria_value' => 30,
            ],
            [
                'name' => 'Rising Star',
                'slug' => 'rising-star',
                'icon' => '🌟',
                'image' => 'modules/manajemenmahasiswa/images/badges/RisingStarBadge.png',
                'description' => 'Telah mengumpulkan total 500 XP.',
                'criteria_type' => Badge::CRITERIA_TOTAL_XP,
                'criteria_value' => 500,
            ],

            // --- Badge baru dari rencana ---
            [
                'name' => 'Storyteller',
                'slug' => 'storyteller',
                'icon' => '📖',
                'image' => 'modules/manajemenmahasiswa/images/badges/StoryTellerBadge.png',
                'description' => 'Buat 50 thread. Anda seorang pencerita ulung!',
                'criteria_type' => Badge::CRITERIA_THREAD_COUNT,
                'criteria_value' => 50,
            ],
            [
                'name' => 'Community Hero',
                'slug' => 'community-hero',
                'icon' => '🦸',
                'image' => 'modules/manajemenmahasiswa/images/badges/CommunityHeroBadge.png',
                'description' => 'Raih 5.000 XP total. Pahlawan komunitas!',
                'criteria_type' => Badge::CRITERIA_TOTAL_XP,
                'criteria_value' => 5000,
            ],
            [
                'name' => 'Viral',
                'slug' => 'viral',
                'icon' => '💥',
                'image' => 'modules/manajemenmahasiswa/images/badges/ViralBadgeBadge.png',
                'description' => 'Menerima total 100 upvote. Konten Anda viral!',
                'criteria_type' => Badge::CRITERIA_UPVOTE_COUNT,
                'criteria_value' => 100,
            ],
            [
                'name' => 'First Blood',
                'slug' => 'first-blood',
                'icon' => '🎯',
                'image' => 'modules/manajemenmahasiswa/images/badges/FirstBloodBadge.png',
                'description' => 'Jawab pertama di 20 thread. Selalu yang tercepat!',
                'criteria_type' => Badge::CRITERIA_FIRST_ANSWER_COUNT,
                'criteria_value' => 20,
            ],
            [
                'name' => 'Mentor Alumni',
                'slug' => 'mentor-alumni',
                'icon' => '🎓',
                'image' => 'modules/manajemenmahasiswa/images/badges/MentorAlumniBadge.png',
                'description' => 'Alumni dengan 50+ komentar di kategori Loker & Karir.',
                'criteria_type' => Badge::CRITERIA_ALUMNI_COMMENT_COUNT,
                'criteria_value' => 50,
            ],
            [
                'name' => 'Professor',
                'slug' => 'professor',
                'icon' => '🧑‍🏫',
                'image' => 'modules/manajemenmahasiswa/images/badges/ExpertBadge.png',
                'description' => 'Dosen dengan 30+ komentar di kategori Tanya Tugas / Info Skripsi.',
                'criteria_type' => Badge::CRITERIA_DOSEN_COMMENT_COUNT,
                'criteria_value' => 30,
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
