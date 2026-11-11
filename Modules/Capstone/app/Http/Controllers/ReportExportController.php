<?php

namespace Modules\Capstone\Http\Controllers;
use App\Http\Controllers\Controller;

use Modules\Capstone\Models\AssessmentScore;
use Modules\Capstone\Models\PeerReview;
use Modules\Capstone\Models\GradeConsistencyCheck;
use Modules\Capstone\Models\Group;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportController extends Controller
{
    /**
     * Export data as CSV.
     * Supported types: assessments, peer-reviews, grade-consistency, groups
     */
    public function export(Request $request, $type)
    {
        $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
            'group_id' => 'nullable|integer',
            'student_search' => 'nullable|string',
            'mode' => 'nullable|in:summary,detail',
        ]);

        $periodId = $request->period_id;

        return match ($type) {
            'assessments' => $this->exportAssessments($periodId, $request->query('eval_type')),
            'peer-reviews' => $this->exportPeerReviews($periodId, $request),
            'grade-consistency' => $this->exportGradeConsistency($periodId),
            'groups' => $this->exportGroups($periodId),
            default => response()->json(['message' => 'Unknown report type'], 400),
        };
    }

    private function exportAssessments($periodId, $evalType = null): StreamedResponse
    {
        $query = AssessmentScore::with(['component', 'evaluator', 'student', 'group.title'])
            ->whereHas('group', fn($q) => $q->where('period_id', $periodId));

        if ($evalType) {
            $query->where('evaluation_type', $evalType);
        }

        $scores = $query->get();

        return $this->streamCsv('assessments.csv', [
            'Group',
            'Title',
            'Student',
            'Evaluator',
            'Component Code',
            'Component Name',
            'Weight',
            'Score',
            'Type',
            'Notes'
        ], $scores->map(fn($s) => [
                $s->group->id ?? '',
                $s->group->title->title ?? '',
                $s->student->name ?? 'Group-level',
                $s->evaluator->name ?? '',
                $s->component->code ?? '',
                $s->component->name ?? '',
                $s->component->weight ?? '',
                $s->score,
                $s->evaluation_type,
                $s->notes ?? '',
            ])->toArray());
    }

    private function exportPeerReviews($periodId, Request $request): StreamedResponse
    {
        if (($request->query('mode') ?? $request->input('mode', 'summary')) === 'detail') {
            return $this->exportPeerReviewsDetail($periodId, $request);
        }

        $rows = $this->buildPeerReviewSummaryRows(
            (int) $periodId,
            $request->input('group_id'),
            (string) $request->input('student_search', '')
        );

        return $this->streamCsv('peer_reviews_summary.csv', [
            'Group',
            'Reviewee',
            'NIM',
            'Status',
            'Reviewers Count',
            'Reviews Count',
            'Avg Raw Score',
            'Avg Score',
        ], array_map(fn($r) => [
                $r['group_name'],
                $r['reviewee_name'],
                $r['reviewee_nim'],
                $r['status'] === 'DINILAI' ? 'Dinilai' : 'Belum dinilai',
                $r['reviewers_count'],
                $r['reviews_count'],
                $r['avg_raw_score'] ?? '',
                $r['avg_score'] ?? '',
            ], $rows));
    }

    private function exportPeerReviewsDetail($periodId, Request $request): StreamedResponse
    {
        $query = PeerReview::with(['reviewer', 'reviewee', 'periodIndicator.template', 'group.title'])
            ->whereHas('group', fn($q) => $q->where('period_id', $periodId));

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->input('group_id'));
        }

        if ($request->filled('student_search')) {
            $search = $request->input('student_search');
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

        $reviews = $query->get();

        return $this->streamCsv('peer_reviews_detail.csv', [
            'Group',
            'Reviewer',
            'Reviewee',
            'Indicator',
            'Weight',
            'Raw Score',
            'Score',
            'Comment'
        ], $reviews->map(fn($r) => [
                $r->group->title->title ?? $r->group->code ?? $r->group_id,
                $r->reviewer->name ?? '',
                $r->reviewee->name ?? '',
                $r->periodIndicator->template->name ?? $r->indicator->name ?? '',
                $r->periodIndicator->template->weight ?? $r->indicator->weight ?? '',
                $r->raw_score,
                $r->score,
                $r->comment ?? '',
            ])->toArray());
    }

    /**
     * One row per assessed student (includes members with zero reviews).
     */
    private function buildPeerReviewSummaryRows(int $periodId, $groupId, string $search): array
    {
        $groupsQuery = Group::where('period_id', $periodId)
            ->with(['title', 'members' => fn($q) => $q->with('student')]);

        if ($groupId && $groupId !== 'all') {
            $groupsQuery->where('id', $groupId);
        }

        $groups = $groupsQuery->get();
        $groupIds = $groups->pluck('id')->toArray();

        $reviews = collect();
        if (! empty($groupIds)) {
            $reviews = PeerReview::with(['periodIndicator.template'])
                ->whereIn('group_id', $groupIds)
                ->get();
        }

        $byReviewee = $reviews->groupBy('reviewee_id');

        $rows = [];
        foreach ($groups as $group) {
            foreach ($group->members as $member) {
                $student = $member->student;
                if (! $student) {
                    continue;
                }

                $name = $student->name ?? 'Unknown';
                $nim = $student->nim ?? '';

                if ($search !== '') {
                    $searchLower = strtolower($search);
                    if (stripos(strtolower($name), $searchLower) === false
                        && stripos(strtolower($nim), $searchLower) === false) {
                        continue;
                    }
                }

                $studentReviews = $byReviewee->get($student->id, collect());
                $count = $studentReviews->count();

                $rows[] = [
                    'group_name' => $group->code ?? "Group {$group->id}",
                    'reviewee_name' => $name,
                    'reviewee_nim' => $nim,
                    'status' => $count > 0 ? 'DINILAI' : 'BELUM_DINILAI',
                    'reviewers_count' => $studentReviews->pluck('reviewer_id')->unique()->count(),
                    'reviews_count' => $count,
                    'avg_raw_score' => $count > 0 ? round($studentReviews->avg('raw_score'), 2) : null,
                    'avg_score' => $this->peerReviewWeightedAverage($studentReviews),
                ];
            }
        }

        usort($rows, fn ($a, $b) => strcmp($a['group_name'], $b['group_name'])
            ?: strcmp($a['reviewee_name'], $b['reviewee_name']));

        return $rows;
    }

    private function peerReviewWeightedAverage($reviews): ?float
    {
        if ($reviews === null || count($reviews) === 0) {
            return null;
        }

        $totalWeighted = 0;
        $totalWeight = 0;
        foreach ($reviews as $review) {
            $weight = $review->periodIndicator?->template?->weight;
            if ($weight !== null && $review->raw_score !== null) {
                $totalWeighted += ((float) $review->raw_score * 25) * (float) $weight;
                $totalWeight += (float) $weight;
            }
        }

        if ($totalWeight > 0) {
            return round($totalWeighted / $totalWeight, 2);
        }

        $scores = collect($reviews)->pluck('score')->filter(fn ($s) => $s !== null);
        if ($scores->isEmpty()) {
            return null;
        }

        return round($scores->avg(), 2);
    }

    private function exportGradeConsistency($periodId): StreamedResponse
    {
        $checks = GradeConsistencyCheck::with(['group.title', 'student', 'checker'])
            ->whereHas('group', fn($q) => $q->where('period_id', $periodId))
            ->get();

        return $this->streamCsv('grade_consistency.csv', [
            'Group ID',
            'Title',
            'Student',
            'PDC1 Score',
            'PDC2 Score',
            'Deviation',
            'Status',
            'Checked By',
            'Notes'
        ], $checks->map(fn($c) => [
                $c->group_id,
                $c->group->title->title ?? '',
                $c->student->name ?? '',
                $c->pdc1_score ?? '',
                $c->pdc2_score ?? '',
                $c->deviation ?? '',
                $c->status,
                $c->checker->name ?? '',
                $c->notes ?? '',
            ])->toArray());
    }

    private function exportGroups($periodId): StreamedResponse
    {
        $groups = Group::where('period_id', $periodId)
            ->with(['title', 'members.student', 'supervisor1', 'supervisor2'])
            ->get();

        return $this->streamCsv('groups.csv', [
            'Group ID',
            'Title',
            'Status',
            'Mode',
            'Supervisor 1',
            'Supervisor 2',
            'Members'
        ], $groups->map(fn($g) => [
                $g->id,
                $g->title->title ?? '',
                $g->status,
                $g->group_mode ?? 'GROUP',
                $g->supervisor1->name ?? '',
                $g->supervisor2->name ?? '',
                $g->members->map(fn($m) => $m->student->name ?? '')->implode('; '),
            ])->toArray());
    }

    /**
     * Helper to stream CSV response.
     */
    private function streamCsv(string $filename, array $headers, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
