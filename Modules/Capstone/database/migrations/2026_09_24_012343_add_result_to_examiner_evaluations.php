<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('capstone_seminar_evaluations', function (Blueprint $table) {
            $table->string('result', 4)->nullable()->after('score');
        });

        Schema::table('capstone_ta_defense_evaluations', function (Blueprint $table) {
            $table->string('result', 4)->nullable()->after('score');
        });

        $this->backfillSeminarResults();
        $this->backfillTaDefenseResults();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capstone_seminar_evaluations', function (Blueprint $table) {
            $table->dropColumn('result');
        });

        Schema::table('capstone_ta_defense_evaluations', function (Blueprint $table) {
            $table->dropColumn('result');
        });
    }

    /**
     * Infer PASS/FAIL for already-submitted seminar evaluations.
     *
     * The result was previously only recorded in audit-log prose, so rows
     * submitted before this column existed carry NULL. A COMPLETED schedule
     * whose group already moved forward implies PASS; a COMPLETED schedule
     * whose group fell back to an active PDC status implies FAIL.
     */
    private function backfillSeminarResults(): void
    {
        $evaluations = DB::table('capstone_seminar_evaluations as e')
            ->join('capstone_seminar_schedules as s', 's.id', '=', 'e.schedule_id')
            ->join('capstone_groups as g', 'g.id', '=', 's.group_id')
            ->where('e.status', 'SUBMITTED')
            ->whereNull('e.result')
            ->select('e.id', 's.type as schedule_type', 's.status as schedule_status', 'g.status as group_status')
            ->get();

        foreach ($evaluations as $row) {
            $result = $this->inferSeminarResult($row->schedule_type, $row->schedule_status, $row->group_status);

            if ($result !== null) {
                DB::table('capstone_seminar_evaluations')->where('id', $row->id)->update(['result' => $result]);
            }
        }
    }

    private function inferSeminarResult(string $scheduleType, string $scheduleStatus, string $groupStatus): ?string
    {
        if ($scheduleStatus !== 'COMPLETED') {
            return null;
        }

        if ($scheduleType === 'SEMPRO') {
            if ($groupStatus === 'SEMPRO_DONE') {
                return 'PASS';
            }

            if (in_array($groupStatus, ['PDC1_ACTIVE', 'READY_FOR_SEMPRO'], true)) {
                return 'FAIL';
            }
        }

        if ($scheduleType === 'EXPO') {
            if (in_array($groupStatus, ['EXPO_DONE', 'TA_DRAFT', 'PDC2_READY_FOR_EXPO'], true)) {
                return 'PASS';
            }

            if ($groupStatus === 'PDC2_ACTIVE') {
                return 'FAIL';
            }
        }

        return null;
    }

    /**
     * Infer PASS/FAIL for already-submitted TA defense evaluations from the
     * linked TA submission status.
     */
    private function backfillTaDefenseResults(): void
    {
        $evaluations = DB::table('capstone_ta_defense_evaluations as e')
            ->join('capstone_ta_defense_schedules as s', 's.id', '=', 'e.schedule_id')
            ->leftJoin('capstone_ta_submissions as t', function ($join) {
                $join->on('t.student_id', '=', 's.student_id')->on('t.group_id', '=', 's.group_id');
            })
            ->where('e.status', 'SUBMITTED')
            ->whereNull('e.result')
            ->select('e.id', 's.status as schedule_status', 't.status as submission_status')
            ->get();

        foreach ($evaluations as $row) {
            if ($row->schedule_status !== 'COMPLETED') {
                continue;
            }

            $result = match ($row->submission_status) {
                'TA_DEFENDED' => 'PASS',
                'TA_REVISED' => 'FAIL',
                default => null,
            };

            if ($result !== null) {
                DB::table('capstone_ta_defense_evaluations')->where('id', $row->id)->update(['result' => $result]);
            }
        }
    }
};
