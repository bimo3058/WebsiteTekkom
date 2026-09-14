<?php

namespace Modules\Capstone\Http\Controllers;
use App\Http\Controllers\Controller;

use Modules\Capstone\Models\AuditLog;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\TaSubmission;
use Modules\Capstone\Services\GroupStateMachine;
use Modules\Capstone\Support\CapstoneActor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaSubmissionController extends Controller
{
    protected GroupStateMachine $stateMachine;

    public function __construct(GroupStateMachine $stateMachine)
    {
        $this->stateMachine = $stateMachine;
    }

    /**
     * Get my TA submission status.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $studentId = CapstoneActor::student($user)->id;

        $submission = TaSubmission::with(['group.title', 'reviewer'])
            ->where('student_id', $studentId)
            ->first();

        return response()->json(['data' => $submission]);
    }

    /**
     * Upload TA draft (student).
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file_path' => 'required|string',
        ]);

        $user = $request->user();
        $studentId = CapstoneActor::student($user)->id;

        // Find student's group
        $membership = GroupMember::where('student_id', $studentId)->first();
        if (!$membership) {
            return response()->json(['message' => 'You are not in a group.'], 400);
        }

        $group = Group::findOrFail($membership->group_id);

        // Gate: group must be at least PDC2_ACTIVE
        if (!$this->stateMachine->isAtLeast($group, 'PDC2_ACTIVE')) {
            return response()->json(['message' => 'Group must be at least in PDC2_ACTIVE status.'], 400);
        }

        // Create or update TA submission
        $submission = TaSubmission::updateOrCreate(
            ['student_id' => $studentId, 'group_id' => $group->id],
            [
                'status' => 'TA_DRAFT',
                'file_path' => $request->file_path,
                'feedback' => null,
            ]
        );

        return response()->json([
            'message' => 'TA draft uploaded.',
            'data' => $submission,
        ]);
    }

    /**
     * Submit a revision (student).
     */
    public function revise(Request $request)
    {
        $request->validate([
            'file_path' => 'required|string',
        ]);

        $user = $request->user();
        $studentId = CapstoneActor::student($user)->id;

        $submission = TaSubmission::where('student_id', $studentId)->firstOrFail();

        $submission->update([
            'status' => 'TA_REVISED',
            'file_path' => $request->file_path,
            'feedback' => null,
        ]);

        return response()->json([
            'message' => 'TA revision submitted.',
            'data' => $submission->fresh(),
        ]);
    }

    /**
     * Register for TA defense (student).
     */
    public function register(Request $request)
    {
        $user = $request->user();
        $studentId = CapstoneActor::student($user)->id;

        $submission = TaSubmission::where('student_id', $studentId)->firstOrFail();

        // Gate: status must be TA_READY
        if ($submission->status !== 'TA_READY') {
            return response()->json(['message' => 'TA must be in TA_READY status to register.'], 400);
        }

        // Gate: group must be PDC2_COMPLETED
        $group = Group::findOrFail($submission->group_id);
        if ($group->status !== 'PDC2_COMPLETED') {
            return response()->json(['message' => 'Group must be in PDC2_COMPLETED status.'], 400);
        }

        $submission->update(['status' => 'TA_REGISTERED']);

        return response()->json([
            'message' => 'TA defense registration submitted.',
            'data' => $submission->fresh(),
        ]);
    }

    /**
     * Review TA submission (dosen).
     */
    public function review(Request $request, $id)
    {
        $request->validate([
            'result' => 'required|in:APPROVE,REVISE',
            'feedback' => 'nullable|string',
        ]);

        $user = $request->user();
        $lecturerId = CapstoneActor::lecturer($user)->id;
        $submission = TaSubmission::findOrFail($id);
        $this->authorizeSupervisor($submission, $lecturerId);

        if ($request->result === 'APPROVE') {
            $submission->update([
                'status' => 'TA_READY',
                'feedback' => $request->feedback,
                'reviewed_by' => $user->id,
            ]);
        } else {
            $submission->update([
                'feedback' => $request->feedback,
                'reviewed_by' => $user->id,
                // Status stays at current (student needs to revise)
            ]);
        }

        return response()->json([
            'message' => "TA review: {$request->result}",
            'data' => $submission->fresh(),
        ]);
    }

    /**
     * Mark TA as defended (dosen). If all group members defended â†’ group CLOSED.
     */
    public function defended(Request $request, $id)
    {
        $user = $request->user();
        $lecturerId = CapstoneActor::lecturer($user)->id;
        $submission = TaSubmission::findOrFail($id);
        $this->authorizeSupervisor($submission, $lecturerId);

        if ($submission->status !== 'TA_REGISTERED') {
            return response()->json(['message' => 'TA must be in TA_REGISTERED status.'], 400);
        }

        return DB::transaction(function () use ($user, $submission) {
            $submission->update([
                'status' => 'TA_DEFENDED',
                'reviewed_by' => $user->id,
            ]);

            // Audit log
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'TA_DEFENDED',
                'target_type' => 'TaSubmission',
                'target_id' => $submission->id,
                'payload' => ['student_id' => $submission->student_id],
            ]);

            // Check if ALL active group members have defended
            $group = Group::findOrFail($submission->group_id);
            $activeMemberCount = GroupMember::where('group_id', $group->id)->count();
            $defendedCount = TaSubmission::where('group_id', $group->id)
                ->where('status', 'TA_DEFENDED')
                ->count();

            if ($activeMemberCount > 0 && $defendedCount >= $activeMemberCount) {
                $this->stateMachine->transition($group, 'CLOSED');
            }

            return response()->json([
                'message' => 'TA marked as defended.',
                'data' => $submission->fresh(),
                'group' => $group->fresh(),
            ]);
        });
    }

    private function authorizeSupervisor(TaSubmission $submission, int $lecturerId): void
    {
        abort_unless(
            $submission->group->supervisions()->where('supervisor_id', $lecturerId)->exists(),
            403,
            'Anda bukan dosen pembimbing mahasiswa ini.'
        );
    }
}
