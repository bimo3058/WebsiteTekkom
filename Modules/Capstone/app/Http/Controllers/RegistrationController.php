<?php

namespace Modules\Capstone\Http\Controllers;

use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\Notification;
use Modules\Capstone\Models\Period;
use Modules\Capstone\Models\PeriodRegistration;
use Modules\Capstone\Support\CapstoneActor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    use ApiResponseTrait;

    /**
     * Check if the authenticated user is registered for a specific period.
     * Returns the request status so the UI can show pending/approved/rejected.
     */
    public function check(Request $request, $periodId)
    {
        $user = $request->user();
        $studentId = CapstoneActor::student($user)->id;
        $registration = PeriodRegistration::where('user_id', $studentId)
            ->where('period_id', $periodId)
            ->first();

        return $this->successResponse([
            'is_registered' => $registration && $registration->isApproved(),
            'is_approved' => $registration && $registration->isApproved(),
            'status' => $registration?->status,
            'registration' => $registration,
        ]);
    }

    /**
     * Request to join a period. Creates a PENDING registration
     * that requires admin approval (one request at a time).
     */
    public function register(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:capstone_periods,id',
        ]);

        $user = $request->user();
        $period = Period::findOrFail($request->period_id);

        // Guard: Only students can register
        if (! $user->hasRole('mahasiswa')) {
            return $this->unauthorizedResponse('Only students can register for an academic period.');
        }

        // Guard: Period must be open
        if (! $period->isRegistrationOpen()) {
            return $this->errorResponse('Registration for this period is closed.', 400);
        }

        $studentId = CapstoneActor::student($user)->id;

        // Guard: one request at a time. A rejected request may be replaced
        // (re-apply), but pending/approved blocks a new join.
        $existingRegistration = PeriodRegistration::where('user_id', $studentId)->first();

        if ($existingRegistration && ! $existingRegistration->isRejected()) {
            $existingPeriod = Period::find($existingRegistration->period_id);
            $periodName = $existingPeriod?->name ?? 'another period';

            if ($existingRegistration->isPending()) {
                return $this->errorResponse("Your request to join '{$periodName}' is still pending admin approval. Cancel it before joining another period.", 400);
            }

            return $this->errorResponse("You are already registered in period '{$periodName}'. You must leave your current group before registering for a new period.", 400);
        }

        DB::beginTransaction();
        try {
            if ($existingRegistration && $existingRegistration->isRejected()) {
                $existingRegistration->delete();
            }

            $registration = PeriodRegistration::create([
                'user_id' => $studentId,
                'period_id' => $period->id,
                'status' => PeriodRegistration::STATUS_PENDING,
            ]);

            // Notify the requester. Admins see the badge via
            // dashboard pending count + approval page.
            Notification::create([
                    'user_id' => $user->id,
                    'type' => 'PERIOD_JOIN_REQUESTED',
                    'title' => 'Join Request Sent',
                    'message' => "Your request to join {$period->name} has been sent. Please wait for admin approval.",
                    'related_type' => 'Period',
                    'related_id' => $period->id,
                ]);

            DB::commit();

            return $this->createdResponse([
                'registration' => $registration,
            ], "Join request for {$period->name} sent. Waiting for admin approval.");
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('Registration failed: '.$e->getMessage(), 500);
        }
    }

    /**
     * Cancel the authenticated user's pending (or rejected) join request.
     */
    public function cancel(Request $request)
    {
        $user = $request->user();
        $studentId = CapstoneActor::student($user)->id;

        $registration = PeriodRegistration::where('user_id', $studentId)->first();

        if (! $registration) {
            return $this->errorResponse('You have no join request to cancel.', 404);
        }

        if ($registration->isApproved()) {
            return $this->errorResponse('Approved registrations cannot be cancelled here. Leave your group first or contact admin.', 400);
        }

        $registration->delete();

        return $this->successResponse(null, 'Join request cancelled. You can now join another period.');
    }

    /**
     * Get the authenticated user's currently registered period.
     * Auto-registers (APPROVED) users that already have a group but no registration (legacy repair).
     */
    public function myPeriod(Request $request)
    {
        $user = $request->user();

        $studentId = CapstoneActor::student($user)->id;
        $registration = PeriodRegistration::where('user_id', $studentId)
            ->with('period')
            ->first();

        // If no registration found, check if user has a group membership
        if (! $registration) {
            $groupMembership = GroupMember::where('student_id', $studentId)
                ->whereHas('group', function ($q) {
                    $q->whereNotIn('status', ['CLOSED', 'DISSOLVED']);
                })
                ->with('group')
                ->first();

            if ($groupMembership) {
                // Auto-approve legacy memberships so existing groups keep working.
                $registration = PeriodRegistration::create([
                    'user_id' => $studentId,
                    'period_id' => $groupMembership->group->period_id,
                    'status' => PeriodRegistration::STATUS_APPROVED,
                ]);

                $registration->load('period');

                return $this->successResponse([
                    'period' => $registration->isApproved() ? $registration->period : null,
                    'registration' => $registration,
                    'auto_registered' => true,
                    'message' => "You have been automatically registered for {$registration->period->name} based on your group membership.",
                ]);
            }

            return $this->successResponse([
                'period' => null,
                'registration' => null,
                'message' => 'Not registered for any period',
            ]);
        }

        return $this->successResponse([
            'period' => $registration->isApproved() ? $registration->period : null,
            'registration' => $registration,
            'auto_registered' => false,
        ]);
    }
}
