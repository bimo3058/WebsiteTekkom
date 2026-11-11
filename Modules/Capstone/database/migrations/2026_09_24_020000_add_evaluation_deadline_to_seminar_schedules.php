<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Capstone\Support\EvaluationDeadline;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('capstone_seminar_schedules', 'evaluation_deadline')) {
            Schema::table('capstone_seminar_schedules', function (Blueprint $table) {
                $table->timestamp('evaluation_deadline')->nullable()->after('date');
            });
        }

        $this->backfillSeminarDeadlines();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capstone_seminar_schedules', function (Blueprint $table) {
            $table->dropColumn('evaluation_deadline');
        });
    }

    /**
     * Backfill schedule date + 2 days for rows created before the column
     * existed. Done in PHP for cross-driver portability.
     */
    private function backfillSeminarDeadlines(): void
    {
        DB::table('capstone_seminar_schedules')
            ->whereNull('evaluation_deadline')
            ->whereNotNull('date')
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('capstone_seminar_schedules')->where('id', $row->id)->update([
                        'evaluation_deadline' => EvaluationDeadline::fromDate($row->date),
                    ]);
                }
            });
    }
};
