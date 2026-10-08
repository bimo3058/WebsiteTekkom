<?php

namespace Modules\Capstone\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Capstone\Http\Controllers\ApiResponseTrait;
use Modules\Capstone\Models\Notification;
use Modules\Capstone\Models\TaRegistration;

class TaRegistrationApprovalController extends Controller
{
    use ApiResponseTrait;

    /**
     * List sidang TA registration requests, defaulting to pending first.
     */
    public function index(Request $request)
    {
        $query = TaRegistration::with(['period:id,name', 'group:id,code', 'student:id,student_number,user_id', 'student.user:id,name,email'])
            ->when($request->filled('period_id') && $request->input('period_id') !== 'all', fn ($q) => $q->where('period_id', $request->input('period_id')))
            ->when($request->filled('status') && $request->input('status') !== 'all', fn ($q) => $q->where('status', strtoupper($request->input('status'))))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%'.$request->input('search').'%';
                $q->where(fn ($w) => $w->whereHas('student', fn ($u) => $u->where('student_number', 'like', $term))->orWhereHas('student.user', fn ($u) => $u->where('name', 'like', $term)));
            })
            ->orderByRaw("CASE status WHEN 'PENDING' THEN 0 WHEN 'REJECTED' THEN 1 WHEN 'APPROVED' THEN 2 ELSE 3 END")
            ->orderByDesc('created_at');

        return $this->paginatedResponse($query->paginate((int) $request->input('per_page', 15)));
    }

    public function approve(Request $request, $id)
    {
        $registration = TaRegistration::where('status', TaRegistration::STATUS_PENDING)->find($id);

        if (! $registration) {
            return $this->errorResponse('Sidang TA request not found or already processed.', 404);
        }

        DB::transaction(function () use ($registration, $request) {
            $registration->update([
                'status' => TaRegistration::STATUS_APPROVED,
                'rejection_reason' => null,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            $studentUserId = $registration->student?->user_id;
            if ($studentUserId) {
                Notification::create([
                    'user_id' => $studentUserId,
                    'type' => 'TA_SIDANG_APPROVED',
                    'title' => 'Sidang TA Registration Approved',
                    'message' => 'Your sidang TA registration has been approved! You can now upload the required TA documents.',
                    'related_type' => 'TaRegistration',
                    'related_id' => $registration->id,
                ]);
            }
        });

        return $this->successResponse(
            $registration->load(['period:id,name', 'group:id,code', 'student:id,student_number,user_id', 'student.user:id,name,email']),
            'Sidang TA request approved.'
        );
    }

    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $registration = TaRegistration::where('status', TaRegistration::STATUS_PENDING)->find($id);

        if (! $registration) {
            return $this->errorResponse('Sidang TA request not found or already processed.', 404);
        }

        DB::transaction(function () use ($registration, $request, $validated) {
            $registration->update([
                'status' => TaRegistration::STATUS_REJECTED,
                'rejection_reason' => $validated['rejection_reason'],
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            $studentUserId = $registration->student?->user_id;
            if ($studentUserId) {
                Notification::create([
                    'user_id' => $studentUserId,
                    'type' => 'TA_SIDANG_REJECTED',
                    'title' => 'Sidang TA Registration Rejected',
                    'message' => "Your sidang TA registration was rejected. Reason: {$validated['rejection_reason']}",
                    'related_type' => 'TaRegistration',
                    'related_id' => $registration->id,
                ]);
            }
        });

        return $this->successResponse(
            $registration->load(['period:id,name', 'group:id,code', 'student:id,student_number,user_id', 'student.user:id,name,email']),
            'Sidang TA request rejected.'
        );
    }
}
