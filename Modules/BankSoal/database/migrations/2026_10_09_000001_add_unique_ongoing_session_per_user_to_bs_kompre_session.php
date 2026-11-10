<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Cegah dua sesi ujian "ongoing" untuk satu mahasiswa.
     *
     * Sebelumnya alur validasi token memakai pola check-then-create tanpa
     * constraint, sehingga dua request paralel (dobel klik / dua tab) dapat
     * membuat dua sesi sekaligus. Partial unique index ini adalah jaring
     * pengaman terakhir di level database.
     *
     * Partial index hanya didukung PostgreSQL; driver lain dibiarkan
     * mengandalkan pengecekan aplikasi.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $this->finishDuplicateOngoingSessions();

        DB::statement(
            "CREATE UNIQUE INDEX IF NOT EXISTS uniq_kompre_session_ongoing_user
             ON bs_kompre_session (user_id)
             WHERE status = 'ongoing'"
        );
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS uniq_kompre_session_ongoing_user');
    }

    /**
     * Tutup sesi duplikat sebelum index dibuat.
     *
     * Sesi yang dipertahankan adalah yang memiliki jumlah jawaban terbanyak
     * (tie-breaker: id terkecil). Sesi lain ditandai selesai dengan skor
     * dihitung dari jawaban yang ada agar riwayat tetap konsisten.
     */
    private function finishDuplicateOngoingSessions(): void
    {
        $duplicatedUserIds = DB::table('bs_kompre_session')
            ->select('user_id')
            ->where('status', 'ongoing')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('user_id');

        foreach ($duplicatedUserIds as $userId) {
            $sessions = DB::table('bs_kompre_session')
                ->where('user_id', $userId)
                ->where('status', 'ongoing')
                ->orderBy('id')
                ->get(['id']);

            $keptId = $sessions
                ->map(fn ($session) => [
                    'id' => $session->id,
                    'jawaban' => DB::table('bs_kompre_jawaban')
                        ->where('kompre_session_id', $session->id)
                        ->count(),
                ])
                ->sortByDesc('jawaban')
                ->first()['id'];

            foreach ($sessions as $session) {
                if ($session->id === $keptId) {
                    continue;
                }

                $agg = DB::table('bs_kompre_jawaban as j')
                    ->leftJoin('bs_jawaban as b', 'b.id', '=', 'j.jawaban_dipilih')
                    ->where('j.kompre_session_id', $session->id)
                    ->selectRaw('COUNT(j.id) as total')
                    ->selectRaw('SUM(CASE WHEN b.is_benar THEN 1 ELSE 0 END) as benar')
                    ->first();

                $total = (int) ($agg->total ?? 0);
                $benar = (int) ($agg->benar ?? 0);

                DB::table('bs_kompre_session')
                    ->where('id', $session->id)
                    ->update([
                        'status' => 'finished',
                        'finished_at' => now(),
                        'score' => $total > 0 ? round(($benar / $total) * 100, 2) : 0.0,
                    ]);
            }
        }
    }
};
