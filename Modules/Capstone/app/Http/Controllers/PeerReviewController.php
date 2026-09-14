<?php

namespace Modules\Capstone\Http\Controllers;
use App\Http\Controllers\Controller;

use Modules\Capstone\Models\PeerReview;
use Modules\Capstone\Models\PeerReviewIndicator;
use Modules\Capstone\Models\PeriodPeerReviewIndicator;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Supervision;
use Modules\Capstone\Support\CapstoneActor;
use Modules\Capstone\Support\BladeFeatureAccess;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\StudentPeerReviewStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PeerReviewController extends Controller
{
    private function periodIndicators(int $periodId)
    {
        return PeriodPeerReviewIndicator::with('template')->where('period_id', $periodId)->orderBy('sort_order')->get();
    }

    /**
     * [Mahasiswa] Get peer review form: group members + indicators + existing reviews.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $studentId = CapstoneActor::student($user)->id;
        $member = GroupMember::where('student_id', $studentId)->first();

        if (!$member) {
            return response()->json(['message' => 'You are not in any group'], 404);
        }

        $group = $member->group()->with(['members.student', 'period', 'title'])->first();

        // Get indicators for the period
        $indicators = PeerReviewIndicator::where('period_id', $group->period_id)
            ->orderBy('sort_order')
            ->get();

        // Get existing peer reviews by this user
        $existingReviews = PeerReview::where('group_id', $group->id)
            ->where('reviewer_id', $studentId)
            ->get();

        $periodIndicators = $this->periodIndicators($group->period_id);
        $legacySubmitted = $existingReviews->contains(fn ($review) => $review->is_final_submission && $review->indicator_id !== null);
        $indicatorKey = $periodIndicators->isNotEmpty() && ! $legacySubmitted ? 'period_indicator_id' : 'indicator_id';
        if ($indicatorKey === 'period_indicator_id') $indicators = $periodIndicators->map->full_indicator;

        // Check if locked
        $isLocked = BladeFeatureAccess::reason('/mahasiswa/peer-review', ['registered'=>true, 'group_status'=>$group->status]) !== null;

        return response()->json([
            'group' => $group,
            'indicators' => $indicators,
            'indicator_key' => $indicatorKey,
            'members' => $group->members,
            'existing_reviews' => $existingReviews,
            'is_locked' => $isLocked,
            'current_user_id' => $studentId,
            'has_submitted' => $existingReviews->contains('is_final_submission', true),
        ]);
    }

    /**
     * [Mahasiswa] Check if peer review is active for the student's period.
     */
    public function status(Request $request)
    {
        $user = $request->user();
        $member = GroupMember::where('student_id', CapstoneActor::student($user)->id)->with('group')->first();

        if (!$member || !$member->group) {
            return response()->json(['active' => false]);
        }

        $active = PeriodPeerReviewIndicator::where('period_id', $member->group->period_id)->exists()
            || PeerReviewIndicator::where('period_id', $member->group->period_id)->exists();

        return response()->json(['active' => $active]);
    }

    /**
     * [Mahasiswa] Submit peer reviews for all group members.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'reviews' => 'required|array|min:1',
            'reviews.*.reviewee_id' => 'required|integer',
            'reviews.*.indicator_id' => 'nullable|integer',
            'reviews.*.period_indicator_id' => 'nullable|integer',
            'reviews.*.score' => 'required|numeric|min:0|max:100',
            'reviews.*.comment' => 'nullable|string|max:5000',
        ]);
        $studentId = CapstoneActor::student($request->user())->id;
        $member = GroupMember::where('student_id', $studentId)->firstOrFail();

        return DB::transaction(function () use ($member, $studentId, $data) {
            // Serialize submissions for the same group, including requests from
            // another tab. Final reviews are immutable through every endpoint.
            $group = Group::whereKey($member->group_id)->lockForUpdate()->firstOrFail();
            $reason = BladeFeatureAccess::reason('/mahasiswa/peer-review', ['registered'=>true, 'group_status'=>$group->status]);
            if ($reason) return response()->json(['message'=>$reason], 403);
            if (PeerReview::where('group_id', $group->id)->where('reviewer_id', $studentId)->where('is_final_submission', true)->exists()) {
                return response()->json(['message'=>'You have already submitted your peer reviews.'], 403);
            }
            $memberIds = GroupMember::where('group_id', $group->id)->where('student_id', '!=', $studentId)->pluck('student_id');
            $periodIndicators = $this->periodIndicators($group->period_id);
            $indicatorKey = $periodIndicators->isNotEmpty() ? 'period_indicator_id' : 'indicator_id';
            $indicatorIds = $periodIndicators->isNotEmpty() ? $periodIndicators->pluck('id') : PeerReviewIndicator::where('period_id', $group->period_id)->pluck('id');
            $expected = [];
            foreach ($memberIds as $revieweeId) foreach ($indicatorIds as $indicatorId) $expected[] = $revieweeId.':'.$indicatorId;
            $actual = array_map(fn ($r) => $r['reviewee_id'].':'.($r[$indicatorKey] ?? ''), $data['reviews']);
            sort($expected); sort($actual);
            // Validate the entire matrix before writing its first row.
            if (empty($expected) || $actual !== $expected) {
                return response()->json(['message'=>'Review every other group member using the indicators for your period.'], 422);
            }
            if ($indicatorKey === 'period_indicator_id' && collect($data['reviews'])->contains(fn ($review) => ! in_array((float) $review['score'], [1.0, 2.0, 3.0, 4.0], true))) {
                return response()->json(['message'=>'Period peer review scores must be integers from 1 to 4.'], 422);
            }
            foreach ($data['reviews'] as $review) {
                $rawScore = $indicatorKey === 'period_indicator_id' ? $review['score'] : $review['score'] / 25;
                PeerReview::updateOrCreate([
                    'group_id'=>$group->id, 'reviewer_id'=>$studentId,
                    'reviewee_id'=>$review['reviewee_id'], $indicatorKey=>$review[$indicatorKey],
                ], [
                    'score'=>$rawScore * 25, 'raw_score'=>$rawScore,
                    'comment'=>$review['comment'] ?? null, 'is_final_submission'=>true, 'submitted_at'=>now(),
                ]);
            }
            $status = StudentPeerReviewStatus::firstOrNew(['student_id'=>$studentId, 'group_id'=>$group->id, 'period_id'=>$group->period_id]);
            $status->has_completed_peer_review = true;
            if (! $status->exists) $status->ta_status = 'TA_BLOCKED';
            $status->save();
            return response()->json(['message'=>'Peer review submitted', 'count'=>count($actual)], 201);
        });
    }

    /**
     * [Dosen] View peer review results for a supervised group.
     */
    public function groupReviews(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:capstone_groups,id',
        ]);

        $lecturerId = CapstoneActor::lecturer($request->user())->id;
        abort_unless(
            Supervision::where('group_id', $request->group_id)->where('supervisor_id', $lecturerId)->exists(),
            403,
            'Anda bukan dosen pembimbing kelompok ini.'
        );

        $reviews = PeerReview::with(['reviewer', 'reviewee', 'indicator', 'periodIndicator.template'])
            ->where('group_id', $request->group_id)
            ->get();

        // Group by reviewee
        $grouped = $reviews->groupBy('reviewee_id')->map(function ($revieweeReviews) {
            $totalWeighted = 0;
            $totalWeight = 0;

            foreach ($revieweeReviews as $r) {
                $weight = $r->periodIndicator?->template?->weight ?? $r->indicator?->weight ?? 1;
                $totalWeighted += $r->score * $weight;
                $totalWeight += $weight;
            }

            return [
                'reviewee' => $revieweeReviews->first()->reviewee,
                'reviews' => $revieweeReviews,
                'weighted_avg' => $totalWeight > 0 ? round($totalWeighted / $totalWeight, 2) : 0,
            ];
        });

        return response()->json($grouped);
    }

    /**
     * [Admin] List peer review indicators for a period.
     */
    public function indicators(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
        ]);

        return response()->json(
            PeerReviewIndicator::where('period_id', $request->period_id)
                ->orderBy('sort_order')
                ->get()
        );
    }

    /**
     * [Admin] Create/update a peer review indicator.
     */
    public function storeIndicator(Request $request)
    {
        $data = $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'weight' => 'required|numeric|min:0|max:100',
            'sort_order' => 'nullable|integer',
        ]);

        $indicator = PeerReviewIndicator::create($data);

        return response()->json($indicator, 201);
    }

    /**
     * [Admin] Update an indicator.
     */
    public function updateIndicator(Request $request, $id)
    {
        $indicator = PeerReviewIndicator::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'weight' => 'sometimes|numeric|min:0|max:100',
            'sort_order' => 'nullable|integer',
        ]);

        $indicator->update($data);

        return response()->json($indicator);
    }

    /**
     * [Admin] Delete an indicator.
     */
    public function destroyIndicator($id)
    {
        $indicator = PeerReviewIndicator::findOrFail($id);
        $indicator->delete();

        return response()->json(['message' => 'Indicator deleted']);
    }
}
