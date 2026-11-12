<?php

namespace Modules\Capstone\Reports;

use Illuminate\Http\Request;
use Modules\Capstone\Http\Controllers\ApiResponseTrait;
use Modules\Capstone\Models\Group;

class GroupReport
{
    use ApiResponseTrait;

    /**
     * Get detailed groups data.
     */
    public function groups(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:10|max:100',
        ]);

        $periodId = $request->period_id;
        $perPage = $request->input('per_page', 50);

        $query = Group::where('period_id', $periodId)
            ->with(['title', 'supervisor1', 'supervisor2', 'members' => function ($query) {
                $query->with('student');
            }]);

        $query->orderBy('created_at', 'desc');

        if ($request->input('format') === 'csv') {
            return CsvExporter::groupsCsv($query->get());
        }

        $groups = $query->paginate($perPage);

        $transformedGroups = $groups->map(function ($group) {
            return [
                'id' => $group->id,
                'code' => $group->code,
                'title' => [
                    'title' => $group->title->title ?? "Group {$group->id}",
                    'description' => $group->title->description ?? null,
                ],
                'status' => $group->status,
                'group_mode' => $group->group_mode ?? 'GROUP',
                'supervisor1' => $group->supervisor1 ? [
                    'id' => $group->supervisor1->id,
                    'name' => $group->supervisor1->name,
                    'email' => $group->supervisor1->email,
                ] : null,
                'supervisor2' => $group->supervisor2 ? [
                    'id' => $group->supervisor2->id,
                    'name' => $group->supervisor2->name,
                    'email' => $group->supervisor2->email,
                ] : null,
                'members' => $group->members->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'student' => [
                            'id' => $member->student->id ?? null,
                            'name' => $member->student->name ?? '',
                            'nim' => $member->student->nim ?? '',
                            'email' => $member->student->email ?? '',
                        ],
                        'is_leader' => $member->is_leader,
                    ];
                }),
                'members_count' => $group->members->count(),
                'created_at' => $group->created_at,
            ];
        });

        return $this->envelopeResponse($transformedGroups, [
            'meta' => [
                'current_page' => $groups->currentPage(),
                'last_page' => $groups->lastPage(),
                'per_page' => $groups->perPage(),
                'total' => $groups->total(),
            ],
        ]);
    }
}
