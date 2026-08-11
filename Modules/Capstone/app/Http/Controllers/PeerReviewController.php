<?php

namespace Modules\Capstone\Http\Controllers;
use App\Http\Controllers\Controller;

use Modules\Capstone\Models\PeerReview;
use Modules\Capstone\Models\PeerReviewIndicator;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Supervision;
use Modules\Capstone\Services\GroupStateMachine;
use Modules\Capstone\Support\CapstoneActor;
use Illuminate\Http\Request;

class PeerReviewController extends Controller
{
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

        $group = $member->group()->with(['members.student', 'period'])->first();

        // Get indicators for the period
        $indicators = PeerReviewIndicator::where('period_id', $group->period_id)
            ->orderBy('sort_order')
            ->get();

        // Get existing peer reviews by this user
        $existingReviews = PeerReview::where('group_id', $group->id)
            ->where('reviewer_id', $studentId)
            ->get();

        // Get other members (exclude self)
        $otherMembers = $group->members()
            ->where('student_id', '!=', $studentId)
            ->with('student')
            ->get();

        // Check if locked
        $stateMachine = app(GroupStateMachine::class);
        $isLocked = !$stateMachine->isAtLeast($group, 'EXPO_REGISTERED');

        return response()->json([
            'group' => $group,
            'indicators' => $indicators,
            'members' => $otherMembers,
            'existing_reviews' => $existingReviews,
            'is_locked' => $isLocked,
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

        $active = PeerReviewIndicator::where('period_id', $member->group->period_id)->exists();

        return response()->json(['active' => $active]);
    }

    /**
     * [Mahasiswa] Submit peer reviews for all group members.
     */
    public function store(Request $request)
    {
        $request->validate([
            'reviews' => 'required|array|min:1',
            'reviews.*.reviewee_id' => 'required|exists:students,id',
            'reviews.*.indicator_id' => 'required|exists:capstone_peer_review_indicators,id',
            'reviews.*.score' => 'required|numeric|min:0|max:100',
            'reviews.*.comment' => 'nullable|string',
        ]);

        $user = $request->user();
        $studentId = CapstoneActor::student($user)->id;
        $member = GroupMember::with('group')->where('student_id', $studentId)->firstOrFail();

        $stateMachine = app(GroupStateMachine::class);
        if (!$stateMachine->isAtLeast($member->group, 'EXPO_REGISTERED')) {
            return response()->json(['message' => 'Peer review is locked until your group reaches the Expo stage.'], 403);
        }

        $saved = [];

        foreach ($request->reviews as $review) {
            $isGroupMember = GroupMember::where('group_id', $member->group_id)
                ->where('student_id', $review['reviewee_id'])
                ->where('student_id', '!=', $studentId)
                ->exists();

            if (! $isGroupMember) {
                return response()->json(['message' => 'Reviewee bukan anggota kelompok Anda.'], 422);
            }

            $saved[] = PeerReview::updateOrCreate(
                [
                    'group_id' => $member->group_id,
                    'reviewer_id' => $studentId,
                    'reviewee_id' => $review['reviewee_id'],
                    'indicator_id' => $review['indicator_id'],
                ],
                [
                    'score' => $review['score'],
                    'comment' => $review['comment'] ?? null,
                ]
            );
        }

        return response()->json(['message' => 'Peer review submitted', 'count' => count($saved)], 201);
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

        $reviews = PeerReview::with(['reviewer', 'reviewee', 'indicator'])
            ->where('group_id', $request->group_id)
            ->get();

        // Group by reviewee
        $grouped = $reviews->groupBy('reviewee_id')->map(function ($revieweeReviews) {
            $totalWeighted = 0;
            $totalWeight = 0;

            foreach ($revieweeReviews as $r) {
                $weight = $r->indicator->weight;
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
