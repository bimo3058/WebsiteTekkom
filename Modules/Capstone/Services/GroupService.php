<?php

namespace Modules\Capstone\Services;

use Illuminate\Support\Collection;
use Modules\Capstone\Models\CapstoneGroup;
use Modules\Capstone\Models\CapstoneGroupWorkflowLog;

class GroupService
{
    /**
     * Statistik jumlah group per status dalam satu period.
     */
    public function getStatusStats(int $periodId): Collection
    {
        return CapstoneGroup::where('period_id', $periodId)
            ->whereNull('deleted_at')
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    /**
     * Semua group dalam period tertentu dengan relasi eager load.
     */
    public function getByPeriod(int $periodId): Collection
    {
        return CapstoneGroup::where('period_id', $periodId)
            ->with(['members.student', 'title', 'supervisor1', 'supervisor2'])
            ->whereNull('deleted_at')
            ->get();
    }

    /**
     * Cari group milik mahasiswa di period tertentu.
     */
    public function getStudentGroup(int $studentId, int $periodId): ?CapstoneGroup
    {
        return CapstoneGroup::whereHas('members', function ($q) use ($studentId, $periodId) {
            $q->where('student_id', $studentId)
              ->where('period_id', $periodId);
        })
        ->with(['title', 'supervisor1', 'supervisor2', 'members.student'])
        ->whereNull('deleted_at')
        ->first();
    }

    /**
     * Transisi status group dan catat ke workflow log.
     */
    public function transitionStatus(CapstoneGroup $group, string $newStatus, int $changedBy): void
    {
        $oldStatus = $group->status;

        $group->update(['status' => $newStatus]);

        CapstoneGroupWorkflowLog::create([
            'group_id'   => $group->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => $changedBy,
            'changed_at' => now(),
        ]);
    }

    public function createGroup(int $periodId, array $data = []): CapstoneGroup
    {
        return CapstoneGroup::create(array_merge([
            'period_id' => $periodId,
            'status'    => CapstoneGroup::STATUS_FORMING,
        ], $data));
    }
}