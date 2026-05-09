<?php

use Illuminate\Support\Facades\Route;
use Modules\Capstone\Http\Controllers\AuthBridgeController;
use Modules\Capstone\Http\Controllers\DashboardController;
use Modules\Capstone\Http\Controllers\TitleController;
use Modules\Capstone\Http\Controllers\UserController;
use Modules\Capstone\Http\Controllers\PeriodController;
use Modules\Capstone\Http\Controllers\GroupController;
use Modules\Capstone\Http\Controllers\BidController;
use Modules\Capstone\Http\Controllers\DocumentController;
use Modules\Capstone\Http\Controllers\DocumentTypeController;
use Modules\Capstone\Http\Controllers\ScheduleController;
use Modules\Capstone\Http\Controllers\SemproController;
use Modules\Capstone\Http\Controllers\ExpoController;
use Modules\Capstone\Http\Controllers\ExpoEventController;
use Modules\Capstone\Http\Controllers\TaSubmissionController;
use Modules\Capstone\Http\Controllers\TaDefenseController;
use Modules\Capstone\Http\Controllers\SeminarDashboardController;
use Modules\Capstone\Http\Controllers\NotificationController;
use Modules\Capstone\Http\Controllers\StudentProposalController;
use Modules\Capstone\Http\Controllers\TitleApprovalController;
use Modules\Capstone\Http\Controllers\FinalizationController;
use Modules\Capstone\Http\Controllers\EvaluationController;
use Modules\Capstone\Http\Controllers\AssessmentComponentController;
use Modules\Capstone\Http\Controllers\AssessmentScoreController;
use Modules\Capstone\Http\Controllers\PeerReviewController;
use Modules\Capstone\Http\Controllers\GradeConsistencyController;
use Modules\Capstone\Http\Controllers\DigitalSignatureController;
use Modules\Capstone\Http\Controllers\ReportExportController;

/*
|--------------------------------------------------------------------------
| Capstone API Routes â€” prefix /api/capstone
|--------------------------------------------------------------------------
*/

Route::prefix('capstone')->group(function () {

    // â”€â”€ Auth Bridge (no auth required) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::post('/auth/exchange', [AuthBridgeController::class, 'exchange']);
    Route::post('/auth/logout', [AuthBridgeController::class, 'logout'])
        ->middleware('auth:sanctum');

    Route::middleware(['auth:sanctum'])->group(function () {

        // Get current user (semua role)
        Route::get('/user', function (\Illuminate\Http\Request $r) {
            return $r->user()->load(['student', 'lecturer', 'roles']);
        });

        // Notifications (semua role)
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

        // â”€â”€ Admin â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['capstone.role:admin'])->prefix('admin')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'admin']);
            Route::apiResource('periods', PeriodController::class);
            Route::apiResource('users', UserController::class);
            Route::apiResource('expo-events', ExpoEventController::class);
            Route::put('/expo-events/{expoEvent}/publish', [ExpoEventController::class, 'publish']);
            Route::apiResource('document-types', DocumentTypeController::class);

            Route::get('/groups', [GroupController::class, 'listGroups']);
            Route::get('/schedules', [ScheduleController::class, 'index']);

            // Finalization
            Route::get('/finalization', [FinalizationController::class, 'index']);
            Route::get('/finalization/dosen-load', [FinalizationController::class, 'dosenLoad']);
            Route::post('/finalization/allocate', [FinalizationController::class, 'allocate']);
            Route::post('/finalization/allocate-student-proposed', [FinalizationController::class, 'allocateStudentProposed']);
            Route::post('/finalization/finalize-period', [FinalizationController::class, 'finalizePeriod']);
            Route::post('/finalization/lock', [FinalizationController::class, 'lock']);

            // SEMPRO
            Route::get('/sempro/schedules', [SemproController::class, 'index']);
            Route::post('/sempro/schedule', [SemproController::class, 'schedule']);
            Route::put('/sempro/schedules/{id}/approve', [SemproController::class, 'approve']);
            Route::put('/sempro/schedules/{id}/reject', [SemproController::class, 'reject']);

            // Expo (legacy)
            Route::get('/expo/schedules', [ExpoController::class, 'index']);
            Route::put('/expo/schedules/{id}/approve', [ExpoController::class, 'approve']);
            Route::put('/expo/schedules/{id}/reject', [ExpoController::class, 'reject']);

            // TA Defense
            Route::get('/ta-defense/schedules', [TaDefenseController::class, 'index']);
            Route::post('/ta-defense/schedule', [TaDefenseController::class, 'schedule']);
            Route::put('/ta-defense/schedules/{id}/approve', [TaDefenseController::class, 'approve']);
            Route::put('/ta-defense/schedules/{id}/reject', [TaDefenseController::class, 'reject']);

            // Group operations
            Route::post('/groups/{group}/approve-member-leave', [GroupController::class, 'approveMemberLeave']);
            Route::post('/groups/{group}/assign-supervisor-2', [GroupController::class, 'assignSupervisor2']);

            // Assessment Components
            Route::get('/assessment-components', [AssessmentComponentController::class, 'index']);
            Route::post('/assessment-components', [AssessmentComponentController::class, 'store']);
            Route::post('/assessment-components/bulk', [AssessmentComponentController::class, 'bulkStore']);
            Route::put('/assessment-components/{id}', [AssessmentComponentController::class, 'update']);
            Route::delete('/assessment-components/{id}', [AssessmentComponentController::class, 'destroy']);

            Route::get('/assessment-scores/summary', [AssessmentScoreController::class, 'summary']);

            // Peer Review
            Route::get('/peer-review/indicators', [PeerReviewController::class, 'indicators']);
            Route::post('/peer-review/indicators', [PeerReviewController::class, 'storeIndicator']);
            Route::put('/peer-review/indicators/{id}', [PeerReviewController::class, 'updateIndicator']);
            Route::delete('/peer-review/indicators/{id}', [PeerReviewController::class, 'destroyIndicator']);

            // Grade Consistency
            Route::get('/grade-consistency', [GradeConsistencyController::class, 'index']);
            Route::post('/grade-consistency/generate', [GradeConsistencyController::class, 'generate']);
            Route::put('/grade-consistency/{id}', [GradeConsistencyController::class, 'update']);

            // Digital Signatures
            Route::post('/digital-signatures/sign', [DigitalSignatureController::class, 'sign']);
            Route::get('/digital-signatures/verify/{hash}', [DigitalSignatureController::class, 'verify']);

            // Report Export
            Route::get('/reports/{type}/export', [ReportExportController::class, 'export']);
        });

        // â”€â”€ Dosen â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['capstone.role:dosen'])->prefix('dosen')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'dosen']);
            Route::apiResource('titles', TitleController::class);

            Route::get('/documents', [DocumentController::class, 'index']);
            Route::put('/documents/{id}', [DocumentController::class, 'update']);

            Route::get('/evaluations', [EvaluationController::class, 'index']);
            Route::post('/evaluations', [EvaluationController::class, 'store']);

            Route::get('/groups/pending', [GroupController::class, 'pendingGroups']);
            Route::get('/groups/supervised', [GroupController::class, 'supervisedGroups']);
            Route::get('/groups', [GroupController::class, 'listGroups']);
            Route::get('/schedules', [ScheduleController::class, 'index']);

            // Title approvals
            Route::get('/title-approvals', [TitleApprovalController::class, 'index']);
            Route::get('/title-approvals/{id}', [TitleApprovalController::class, 'show']);
            Route::put('/title-approvals/{id}/approve', [TitleApprovalController::class, 'approve']);
            Route::put('/title-approvals/{id}/reject', [TitleApprovalController::class, 'reject']);

            // Bidding recommendation
            Route::get('/bids', [BidController::class, 'lecturerBids']);
            Route::put('/bids/{id}/recommend', [BidController::class, 'recommend']);

            // Evaluations
            Route::post('/sempro/{schedule}/evaluate', [SemproController::class, 'evaluate']);
            Route::post('/expo/{schedule}/evaluate', [ExpoController::class, 'evaluate']);
            Route::put('/ta/{id}/review', [TaSubmissionController::class, 'review']);
            Route::put('/ta/{id}/defended', [TaSubmissionController::class, 'defended']);
            Route::post('/ta-defense/{schedule}/evaluate', [TaDefenseController::class, 'evaluate']);

            // Seminar dashboard
            Route::get('/seminar-schedules/supervisor', [SeminarDashboardController::class, 'supervisorSchedules']);
            Route::get('/seminar-schedules/examiner', [SeminarDashboardController::class, 'examinerSchedules']);
            Route::get('/evaluation-context/{type}/{id}', [SeminarDashboardController::class, 'evaluationContext']);

            // Assessment scores submission
            Route::get('/assessment-scores', [AssessmentScoreController::class, 'index']);
            Route::post('/assessment-scores', [AssessmentScoreController::class, 'store']);

            // Peer review (lecturer view)
            Route::get('/peer-review', [PeerReviewController::class, 'groupReviews']);

            // Digital signatures (own)
            Route::get('/digital-signatures', [DigitalSignatureController::class, 'mySignatures']);
        });

        // â”€â”€ Mahasiswa â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['capstone.role:mahasiswa'])->prefix('mahasiswa')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'mahasiswa']);
            Route::get('/titles', [TitleController::class, 'index']);
            Route::get('/titles/{title}', [TitleController::class, 'show']);

            // Group management
            Route::get('/group', [GroupController::class, 'index']);
            Route::post('/group', [GroupController::class, 'store']);
            Route::delete('/group', [GroupController::class, 'deleteGroup']);
            Route::post('/group/leave', [GroupController::class, 'leaveGroup']);
            Route::post('/group/add-member', [GroupController::class, 'addMember']);
            Route::delete('/group/members/{memberId}', [GroupController::class, 'removeMember']);
            Route::post('/group/propose-supervisors', [GroupController::class, 'proposeSupervisors']);
            Route::post('/group-invitations/{id}/accept', [GroupController::class, 'acceptInvite']);
            Route::post('/group-invitations/{id}/reject', [GroupController::class, 'rejectInvite']);

            // Bidding
            Route::get('/bids', [BidController::class, 'index']);
            Route::post('/bids', [BidController::class, 'store']);
            Route::delete('/bids/{id}', [BidController::class, 'destroy']);

            // Documents
            Route::post('/documents', [DocumentController::class, 'store']);
            Route::get('/documents', [DocumentController::class, 'index']);
            Route::get('/workflow', [DocumentController::class, 'workflow']);

            Route::get('/schedules', [ScheduleController::class, 'index']);
            Route::get('/seminar-schedules', [SeminarDashboardController::class, 'studentSchedules']);
            Route::get('/ta-defense', [TaDefenseController::class, 'myDefense']);

            // Expo events
            Route::get('/expo-events', [ExpoEventController::class, 'studentEvents']);
            Route::post('/expo-events/{expoEvent}/register', [ExpoEventController::class, 'register']);

            // Student proposals
            Route::get('/lecturers', [StudentProposalController::class, 'lecturers']);
            Route::post('/propose-title', [StudentProposalController::class, 'store']);
            Route::get('/my-proposal', [StudentProposalController::class, 'myProposal']);
            Route::put('/my-proposal', [StudentProposalController::class, 'update']);

            // Peer review
            Route::get('/peer-review', [PeerReviewController::class, 'index']);
            Route::get('/peer-review/status', [PeerReviewController::class, 'status']);
            Route::post('/peer-review', [PeerReviewController::class, 'store']);

            // TA submissions
            Route::get('/ta', [TaSubmissionController::class, 'index']);
            Route::post('/ta/upload', [TaSubmissionController::class, 'upload']);
            Route::put('/ta/revise', [TaSubmissionController::class, 'revise']);
            Route::post('/ta/register', [TaSubmissionController::class, 'register']);
        });

        // â”€â”€ Shared (Admin + Dosen) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['capstone.role:admin_or_dosen'])->group(function () {
            Route::apiResource('schedules', ScheduleController::class)->except(['index', 'show']);
        });
    });
});
