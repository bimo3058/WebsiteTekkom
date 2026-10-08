<?php

namespace Modules\Capstone\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Capstone\Models\Notification;
use Modules\Capstone\Models\TaRegistration;
use Modules\Capstone\Services\IndividualTaWorkflow;
use Modules\Capstone\Support\CapstoneActor;

class TaRegistrationController extends Controller
{
    use ApiResponseTrait;

    /**
     * Request approval to enter the TA document-upload phase ("Daftar Sidang TA").
     * Requires TA readiness (expo, supervisor scores, peer review); creates PENDING.
     */
    public function store(Request $request, IndividualTaWorkflow $workflow)
    {
        $user = $request->user();

        if (! $user->hasRole('mahasiswa')) {
            return $this->unauthorizedResponse('Only students can register for sidang TA.');
        }

        $studentId = CapstoneActor::student($user)->id;
        $state = $workflow->forStudent($studentId);

        if (! $state['group']) {
            return $this->errorResponse('You must be in an active group first.', 400);
        }

        if (! $state['can_access']) {
            return $this->errorResponse('Complete the TA prerequisites (expo, supervisor scores, peer review) before registering.', 400);
        }

        $existing = TaRegistration::where('student_id', $studentId)
            ->where('group_id', $state['group']->id)
            ->latest('id')
            ->first();

        if ($existing && ! $existing->isRejected()) {
            if ($existing->isPending()) {
                return $this->errorResponse('Your sidang TA registration is still pending admin approval. Cancel it before registering again.', 400);
            }

            return $this->errorResponse('Your sidang TA registration is already approved. Upload the required documents.', 400);
        }

        DB::beginTransaction();
        try {
            if ($existing && $existing->isRejected()) {
                $existing->delete();
            }

            $registration = TaRegistration::create([
                'student_id' => $studentId,
                'group_id' => $state['group']->id,
                'period_id' => $state['group']->period_id,
                'status' => TaRegistration::STATUS_PENDING,
            ]);

            Notification::create([
                'user_id' => $user->id,
                'type' => 'TA_SIDANG_REQUESTED',
                'title' => 'Sidang TA Registration Sent',
                'message' => 'Your sidang TA registration has been sent. Please wait for admin approval before uploading documents.',
                'related_type' => 'TaRegistration',
                'related_id' => $registration->id,
            ]);

            DB::commit();

            return $this->createdResponse([
                'registration' => $registration,
            ], 'Sidang TA registration sent. Waiting for admin approval.');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('Registration failed: '.$e->getMessage(), 500);
        }
    }

    /**
     * Cancel the authenticated student's pending (or rejected) sidang TA request.
     */
    public function destroy(Request $request)
    {
        $user = $request->user();
        $studentId = CapstoneActor::student($user)->id;

        $registration = TaRegistration::where('student_id', $studentId)->latest('id')->first();

        if (! $registration) {
            return $this->errorResponse('You have no sidang TA request to cancel.', 404);
        }

        if ($registration->isApproved()) {
            return $this->errorResponse('Approved registrations cannot be cancelled. Contact admin instead.', 400);
        }

        $registration->delete();

        return $this->successResponse(null, 'Sidang TA request cancelled. You can register again.');
    }
}
