<?php

namespace Modules\Capstone\Reports;

use Illuminate\Http\Request;
use Modules\Capstone\Http\Controllers\ApiResponseTrait;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\PeerReview;
use Modules\Capstone\Services\GradeCalculationService;

class PeerReviewReport
{
    use ApiResponseTrait;

    /**
     * Get detailed peer reviews data.
     */
    public function peerReviews(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
            'group_id' => 'nullable|integer',
            'student_search' => 'nullable|string',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:10|max:100',
            'sort_by' => 'nullable|in:created_at,reviewer,reviewee,raw_score,score',
            'sort_order' => 'nullable|in:asc,desc',
        ]);

        $periodId = $request->period_id;
        $perPage = $request->input('per_page', 50);
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $query = PeerReview::with(['reviewer', 'reviewee', 'periodIndicator.template', 'group.title'])
            ->whereHas('group', function ($q) use ($periodId) {
                $q->where('period_id', $periodId);
            });

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        if ($request->filled('student_search')) {
            $search = $request->student_search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('reviewer', function ($r) use ($search) {
                    $r->where('student_number', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
                })->orWhereHas('reviewee', function ($r) use ($search) {
                    $r->where('student_number', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
                });
            });
        }

        $this->applyPeerReviewSorting($query, $sortBy, $sortOrder);

        if ($request->input('format') === 'csv') {
            return CsvExporter::peerReviewsCsv($query->get());
        }

        $reviews = $query->paginate($perPage);

        // Transform data to ensure score is a number and add camelCase aliases for frontend
        $data = collect($reviews->items())->map(function ($review) {
            $array = $review->toArray();
            $array['score'] = (float) $array['score']; // Ensure score is number
            $array['periodIndicator'] = $array['period_indicator']; // Add camelCase alias

            return $array;
        });

        return $this->envelopeResponse($data, [
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }

    /**
     * Apply sorting to peer review query based on sort parameters.
     */
    private function applyPeerReviewSorting($query, string $sortBy, string $sortOrder): void
    {
        switch ($sortBy) {
            case 'reviewer':
                $query->join('students as reviewer_student', 'capstone_peer_reviews.reviewer_id', '=', 'reviewer_student.id')
                    ->join('users as reviewer_user', 'reviewer_student.user_id', '=', 'reviewer_user.id')
                    ->orderBy('reviewer_user.name', $sortOrder)
                    ->select('capstone_peer_reviews.*');
                break;
            case 'reviewee':
                $query->join('students as reviewee_student', 'capstone_peer_reviews.reviewee_id', '=', 'reviewee_student.id')
                    ->join('users as reviewee_user', 'reviewee_student.user_id', '=', 'reviewee_user.id')
                    ->orderBy('reviewee_user.name', $sortOrder)
                    ->select('capstone_peer_reviews.*');
                break;
            case 'raw_score':
                $query->orderBy('raw_score', $sortOrder);
                break;
            case 'score':
                $query->orderBy('score', $sortOrder);
                break;
            case 'created_at':
            default:
                $query->orderBy('created_at', $sortOrder);
                break;
        }
    }

    /**
     * Get peer review summary grouped by reviewee (one row per assessed student).
     *
     * Includes every group member in the period (even with zero reviews)
     * so unassessed students show up with a BELUM_DINILAI status.
     */
    public function peerReviewSummary(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
            'group_id' => 'nullable|integer',
            'student_search' => 'nullable|string',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:10|max:100',
            'sort_by' => 'nullable|in:reviewee,group,avg_score,reviewers_count',
            'sort_order' => 'nullable|in:asc,desc',
        ]);

        $periodId = (int) $request->period_id;
        $perPage = (int) $request->input('per_page', 50);
        $page = (int) $request->input('page', 1);
        $sortBy = $request->input('sort_by', 'reviewee');
        $sortOrder = $request->input('sort_order', 'asc');

        $rows = self::summaryRows(
            $periodId,
            $request->filled('group_id') ? $request->input('group_id') : null,
            (string) $request->input('student_search', '')
        );

        usort($rows, function ($a, $b) use ($sortBy, $sortOrder) {
            $dir = $sortOrder === 'desc' ? -1 : 1;
            switch ($sortBy) {
                case 'group':
                    $cmp = strcmp($a['group_name'], $b['group_name'])
                        ?: strcmp($a['reviewee_name'], $b['reviewee_name']);
                    break;
                case 'avg_score':
                    $aVal = $a['avg_score'] ?? -1;
                    $bVal = $b['avg_score'] ?? -1;
                    $cmp = $aVal <=> $bVal;
                    break;
                case 'reviewers_count':
                    $cmp = $a['reviewers_count'] <=> $b['reviewers_count'];
                    break;
                case 'reviewee':
                default:
                    $cmp = strcmp($a['reviewee_name'], $b['reviewee_name']);
                    break;
            }

            return $cmp * $dir;
        });

        $total = count($rows);
        $offset = ($page - 1) * $perPage;
        $paginated = array_slice($rows, $offset, $perPage);

        return $this->envelopeResponse(array_values($paginated), [
            'meta' => [
                'current_page' => $page,
                'last_page' => (int) ceil($total / $perPage),
                'per_page' => $perPage,
                'total' => $total,
            ],
        ]);
    }

    /**
     * Per-reviewee summary rows shared by the JSON summary and the CSV
     * export (which previously called a method that did not exist).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function summaryRows(int $periodId, $groupId = null, string $search = ''): array
    {
        $groupsQuery = Group::with(['title', 'members' => function ($query) {
            $query->with('student');
        }])->where('period_id', $periodId);

        if ($groupId !== null && $groupId !== '') {
            $groupsQuery->where('id', $groupId);
        }

        $groups = $groupsQuery->get();
        $groupIds = $groups->pluck('id')->toArray();

        $reviews = collect();
        if (! empty($groupIds)) {
            $reviews = PeerReview::with(['reviewer', 'periodIndicator.template'])
                ->whereIn('group_id', $groupIds)
                ->get();
        }

        $reviewsByReviewee = $reviews->groupBy('reviewee_id');

        $rows = [];
        foreach ($groups as $group) {
            $groupName = $group->code ?? "Group {$group->id}";
            foreach ($group->members as $member) {
                $student = $member->student;
                if (! $student) {
                    continue;
                }

                $studentName = $student->name ?? 'Unknown';
                $studentNim = $student->nim ?? '';

                if ($search !== '') {
                    $searchLower = strtolower($search);
                    $nameMatch = stripos(strtolower($studentName), $searchLower) !== false;
                    $nimMatch = stripos(strtolower($studentNim), $searchLower) !== false;
                    if (! $nameMatch && ! $nimMatch) {
                        continue;
                    }
                }

                $studentReviews = $reviewsByReviewee->get($student->id, collect());
                $reviewsCount = $studentReviews->count();
                $reviewerIds = $studentReviews->pluck('reviewer_id')->unique()->values();
                $reviewersCount = $reviewerIds->count();

                $avgRaw = $reviewsCount > 0 ? round($studentReviews->avg('raw_score'), 2) : null;
                $avgScore = GradeCalculationService::peerReviewWeightedAverage($studentReviews);

                $rows[] = [
                    'reviewee_id' => $student->id,
                    'reviewee_name' => $studentName,
                    'reviewee_nim' => $studentNim,
                    'group_id' => $group->id,
                    'group_name' => $groupName,
                    'reviewers_count' => $reviewersCount,
                    'reviews_count' => $reviewsCount,
                    'avg_raw_score' => $avgRaw,
                    'avg_score' => $avgScore,
                    'status' => $reviewsCount > 0 ? 'DINILAI' : 'BELUM_DINILAI',
                ];
            }
        }

        return $rows;
    }

    /**
     * Get peer review detail for a single reviewee: who rated them and the
     * per-indicator scores from each reviewer.
     */
    public function peerReviewDetail(Request $request, $studentId)
    {
        $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
        ]);

        $periodId = (int) $request->period_id;

        $group = Group::where('period_id', $periodId)
            ->whereHas('members', function ($q) use ($studentId) {
                $q->where('student_id', $studentId);
            })
            ->with(['title', 'members' => function ($q) {
                $q->with('student');
            }])
            ->first();

        if (! $group) {
            return $this->notFoundResponse('Student not found in this period');
        }

        $student = $group->members->firstWhere('student_id', (int) $studentId)?->student;
        if (! $student) {
            return $this->notFoundResponse('Student not found');
        }

        $reviews = PeerReview::with(['reviewer', 'periodIndicator.template'])
            ->where('group_id', $group->id)
            ->where('reviewee_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->get();

        $byReviewer = $reviews->groupBy('reviewer_id');
        $reviewers = [];
        foreach ($byReviewer as $reviewerRows) {
            /** @var PeerReview $first */
            $first = $reviewerRows->first();
            $details = $reviewerRows->map(function ($review) {
                $template = $review->periodIndicator?->template;

                return [
                    'indicator_code' => $template?->code ?? 'N/A',
                    'indicator_name' => $template?->name ?? 'N/A',
                    'weight' => $template?->weight !== null ? (float) $template->weight : null,
                    'raw_score' => $review->raw_score !== null ? (float) $review->raw_score : null,
                    'score' => $review->score !== null ? (float) $review->score : null,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at,
                ];
            })->values();

            $reviewers[] = [
                'reviewer_id' => $first->reviewer_id,
                'reviewer_name' => $first->reviewer?->name ?? 'Unknown',
                'reviewer_nim' => $first->reviewer?->nim ?? '',
                'reviews_count' => $reviewerRows->count(),
                'avg_raw_score' => round($reviewerRows->avg('raw_score'), 2),
                'avg_score' => GradeCalculationService::peerReviewWeightedAverage($reviewerRows),
                'details' => $details,
            ];
        }

        usort($reviewers, fn ($a, $b) => strcmp($a['reviewer_name'], $b['reviewer_name']));

        return $this->successResponse([
            'reviewee' => [
                'id' => $student->id,
                'name' => $student->name ?? 'Unknown',
                'nim' => $student->nim ?? '',
                'group_id' => $group->id,
                'group_name' => $group->code ?? "Group {$group->id}",
            ],
            'overall' => [
                'reviewers_count' => count($reviewers),
                'reviews_count' => $reviews->count(),
                'avg_raw_score' => $reviews->count() > 0 ? round($reviews->avg('raw_score'), 2) : null,
                'avg_score' => GradeCalculationService::peerReviewWeightedAverage($reviews),
                'status' => $reviews->count() > 0 ? 'DINILAI' : 'BELUM_DINILAI',
            ],
            'reviewers' => $reviewers,
        ]);
    }
}
