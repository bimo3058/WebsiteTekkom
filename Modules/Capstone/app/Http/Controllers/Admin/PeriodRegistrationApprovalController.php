<?php

namespace Modules\Capstone\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Capstone\Http\Controllers\ApiResponseTrait;
use Modules\Capstone\Models\Notification;
use Modules\Capstone\Models\PeriodRegistration;

class PeriodRegistrationApprovalController extends Controller
{
    use ApiResponseTrait;

    /**
     * List period join requests, defaulting to pending first.
     */
    public function index(Request $request)
    {
        $query = PeriodRegistration::with(['period:id,name', 'user:id,student_number,user_id', 'user.user:id,name,email'])
            ->when($request->filled('period_id') && $request->input('period_id') !== 'all', fn ($q) => $q->where('period_id', $request->input('period_id')))
            ->when($request->filled('status') && $request->input('status') !== 'all', fn ($q) => $q->where('status', strtoupper($request->input('status'))))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%'.$request->input('search').'%';
                $q->where(fn ($w) => $w->whereHas('user', fn ($u) => $u->where('student_number', 'like', $term))->orWhereHas('user.user', fn ($u) => $u->where('name', 'like', $term)));
            })
            ->orderByRaw("CASE status WHEN 'PENDING' THEN 0 WHEN 'REJECTED' THEN 1 WHEN 'APPROVED' THEN 2 ELSE 3 END")
            ->orderByDesc('created_at');

        return $this->paginatedResponse($query->paginate((int) $request->input('per_page', 15)));
    }

    public function approve(Request $request, $id)
    {
        $registration = PeriodRegistration::where('status', PeriodRegistration::STATUS_PENDING)->find($id);

        if (! $registration) {
            return $this->errorResponse('Join request not found or already processed.', 404);
        }

        // One approved period per student: block if they got approved elsewhere meanwhile.
        $otherApproved = PeriodRegistration::where('user_id', $registration->user_id)
            ->where('id', '!=', $registration->id)
            ->where('status', PeriodRegistration::STATUS_APPROVED)
            ->exists();

        if ($otherApproved) {
            return $this->errorResponse('Student is already approved in another period.', 409);
        }

        DB::transaction(function () use ($registration, $request) {
            $registration->update([
                'status' => PeriodRegistration::STATUS_APPROVED,
                'rejection_reason' => null,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            $studentUserId = $registration->user?->user_id;
            if ($studentUserId) {
                Notification::create([
                    'user_id' => $studentUserId,
                    'type' => 'PERIOD_JOIN_APPROVED',
                    'title' => 'Join Request Approved',
                    'message' => "Your request to join {$registration->period->name} has been approved! You can now create or join a group.",
                    'related_type' => 'Period',
                    'related_id' => $registration->period_id,
                ]);
            }
        });

        return $this->successResponse(
            $registration->load(['period:id,name', 'user:id,student_number,user_id', 'user.user:id,name,email']),
            'Join request approved.'
        );
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $registration = PeriodRegistration::where('status', PeriodRegistration::STATUS_PENDING)->find($id);

        if (! $registration) {
            return $this->errorResponse('Join request not found or already processed.', 404);
        }

        DB::transaction(function () use ($registration, $request, $validated) {
            $registration->update([
                'status' => PeriodRegistration::STATUS_REJECTED,
                'rejection_reason' => $validated['rejection_reason'],
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            $studentUserId = $registration->user?->user_id;
            if ($studentUserId) {
                Notification::create([
                    'user_id' => $studentUserId,
                    'type' => 'PERIOD_JOIN_REJECTED',
                    'title' => 'Join Request Rejected',
                    'message' => "Your request to join {$registration->period->name} was rejected. Reason: {$validated['rejection_reason']}",
                    'related_type' => 'Period',
                    'related_id' => $registration->period_id,
                ]);
            }
        });

        return $this->successResponse(
            $registration->load(['period:id,name', 'user:id,student_number,user_id', 'user.user:id,name,email']),
            'Join request rejected.'
        );
    }
}
