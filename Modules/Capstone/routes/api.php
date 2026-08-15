<?php

use Illuminate\Support\Facades\Route;
use Modules\Capstone\Http\Controllers\Admin\AuditLogController;
use Modules\Capstone\Http\Controllers\Admin\DocumentUploadController;
use Modules\Capstone\Http\Controllers\Admin\PhaseDocumentRequirementController;
use Modules\Capstone\Http\Controllers\Admin\StakeholderController;
use Modules\Capstone\Http\Controllers\AssessmentComponentController;
use Modules\Capstone\Http\Controllers\AssessmentComponentTemplateController;
use Modules\Capstone\Http\Controllers\AssessmentScoreController;
use Modules\Capstone\Http\Controllers\AuthBridgeController;
use Modules\Capstone\Http\Controllers\BidController;
use Modules\Capstone\Http\Controllers\BursaIdeController;
use Modules\Capstone\Http\Controllers\DashboardController;
use Modules\Capstone\Http\Controllers\DigitalSignatureController;
use Modules\Capstone\Http\Controllers\DocumentController;
use Modules\Capstone\Http\Controllers\DocumentTypeController;
use Modules\Capstone\Http\Controllers\EvaluationController;
use Modules\Capstone\Http\Controllers\ExpoController;
use Modules\Capstone\Http\Controllers\ExpoEventController;
use Modules\Capstone\Http\Controllers\FileController;
use Modules\Capstone\Http\Controllers\FinalizationController;
use Modules\Capstone\Http\Controllers\GradeConfigurationController;
use Modules\Capstone\Http\Controllers\GradeConsistencyController;
use Modules\Capstone\Http\Controllers\GroupController;
use Modules\Capstone\Http\Controllers\LocationController;
use Modules\Capstone\Http\Controllers\NotificationController;
use Modules\Capstone\Http\Controllers\PeerReviewController;
use Modules\Capstone\Http\Controllers\PeriodAssessmentConfigController;
use Modules\Capstone\Http\Controllers\PeriodController;
use Modules\Capstone\Http\Controllers\PeriodPeerReviewConfigController;
use Modules\Capstone\Http\Controllers\RegistrationController;
use Modules\Capstone\Http\Controllers\ReportDetailController;
use Modules\Capstone\Http\Controllers\ReportExportController;
use Modules\Capstone\Http\Controllers\ReportSummaryController;
use Modules\Capstone\Http\Controllers\ScheduleController;
use Modules\Capstone\Http\Controllers\SeminarDashboardController;
use Modules\Capstone\Http\Controllers\SemproController;
use Modules\Capstone\Http\Controllers\SoloTitleController;
use Modules\Capstone\Http\Controllers\StudentProposalController;
use Modules\Capstone\Http\Controllers\StudentStateController;
use Modules\Capstone\Http\Controllers\SupervisorEvaluationController;
use Modules\Capstone\Http\Controllers\TaDefenseController;
use Modules\Capstone\Http\Controllers\TaDefenseScheduleController;
use Modules\Capstone\Http\Controllers\TaSubmissionController;
use Modules\Capstone\Http\Controllers\TitleApprovalController;
use Modules\Capstone\Http\Controllers\TitleController;
use Modules\Capstone\Http\Controllers\UserController;
use Modules\Capstone\Http\Middleware\CacheCapstoneResponse;

/*
|--------------------------------------------------------------------------
| Capstone API Routes â€” prefix /api/capstone
|--------------------------------------------------------------------------
*/

Route::prefix('capstone')->group(function () {

    // â”€â”€ Auth Bridge (no auth required) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Route::post('/auth/exchange', [AuthBridgeController::class, 'exchange'])
        ->middleware('module.active:capstone');
    Route::post('/auth/logout', [AuthBridgeController::class, 'logout'])
        ->middleware('auth:sanctum');

    Route::middleware([
        'auth:sanctum',
        'capstone.access',
        'module.active:capstone',
        CacheCapstoneResponse::class,
    ])->group(function () {

        // Get current user (semua role)
        Route::get('/user', [AuthBridgeController::class, 'me']);
        Route::get('/auth/user', [AuthBridgeController::class, 'me']);
        Route::post('/user/active-role', [AuthBridgeController::class, 'setActiveRole']);
        Route::post('/auth/active-role', [AuthBridgeController::class, 'setActiveRole']);

        Route::get('/periods-list', [PeriodController::class, 'index']);

        // Notifications (semua role)
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

        // â”€â”€ Admin â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['capstone.role:admin'])->prefix('admin')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'admin']);
            Route::apiResource('periods', PeriodController::class);
            Route::get('/users', [UserController::class, 'index']);
            Route::apiResource('expo-events', ExpoEventController::class);
            Route::put('/expo-events/{expoEvent}/publish', [ExpoEventController::class, 'publish']);
            Route::apiResource('document-types', DocumentTypeController::class);

            Route::get('/groups', [GroupController::class, 'listGroups'])
                ->middleware('permission:capstone.groups.view');
            Route::get('/groups/{group}', [GroupController::class, 'show'])
                ->middleware('permission:capstone.groups.view');
            Route::get('/schedules', [ScheduleController::class, 'index']);

            Route::get('/ta-defense-schedules/eligible-students', [TaDefenseScheduleController::class, 'eligibleStudents']);
            Route::put('/ta-defense-schedules/{id}/cancel', [TaDefenseScheduleController::class, 'cancel']);
            Route::put('/ta-defense-schedules/{id}/assign-examiners', [TaDefenseScheduleController::class, 'assignExaminers']);
            Route::apiResource('ta-defense-schedules', TaDefenseScheduleController::class)->except(['destroy']);

            Route::apiResource('assessment-templates', AssessmentComponentTemplateController::class);
            Route::get('/evaluation-setup/check', [AssessmentComponentTemplateController::class, 'check']);
            Route::get('/periods/{period}/assessment-config', [PeriodAssessmentConfigController::class, 'show']);
            Route::post('/periods/{period}/assessment-config', [PeriodAssessmentConfigController::class, 'store']);
            Route::post('/periods/{period}/assessment-config/copy', [PeriodAssessmentConfigController::class, 'copy']);
            Route::get('/periods/{period}/peer-review-config', [PeriodPeerReviewConfigController::class, 'show']);
            Route::post('/periods/{period}/peer-review-config', [PeriodPeerReviewConfigController::class, 'store']);
            Route::post('/periods/{period}/peer-review-config/copy', [PeriodPeerReviewConfigController::class, 'copy']);

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
            Route::post('/groups/{group}/assign-supervisor-2', [GroupController::class, 'assignSupervisor2'])
                ->middleware('permission:capstone.groups.manage');

            // Assessment Components
            Route::get('/assessment-components', [AssessmentComponentController::class, 'index']);
            Route::post('/assessment-components', [AssessmentComponentController::class, 'store']);
            Route::post('/assessment-components/bulk', [AssessmentComponentController::class, 'bulkStore']);
            Route::put('/assessment-components/{id}', [AssessmentComponentController::class, 'update']);
            Route::delete('/assessment-components/{id}', [AssessmentComponentController::class, 'destroy']);

            Route::get('/assessment-scores/summary', [AssessmentScoreController::class, 'summary']);
            Route::get('/supervisor-evaluation/schedules/{scheduleId}/summary', [SupervisorEvaluationController::class, 'adminScheduleSummary']);
            Route::get('/supervisor-evaluation/schedules/{scheduleId}/export', [SupervisorEvaluationController::class, 'exportScheduleSummary']);
            Route::get('/supervisor-evaluation/schedules/{scheduleId}/grades', [SupervisorEvaluationController::class, 'getGradesForSchedule']);

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
            Route::get('/reports/summary', [ReportSummaryController::class, 'summary']);
            Route::get('/reports/assessments', [ReportDetailController::class, 'assessments']);
            Route::get('/reports/peer-reviews', [ReportDetailController::class, 'peerReviews']);
            Route::get('/reports/final-grades', [ReportDetailController::class, 'finalGrades']);
            Route::get('/reports/grade-consistency', [ReportDetailController::class, 'gradeConsistency']);
            Route::get('/reports/groups', [ReportDetailController::class, 'groups']);
            Route::get('/reports/student-evaluations-summary', [ReportDetailController::class, 'studentEvaluationsSummary']);
            Route::get('/reports/student-evaluations/{studentId}/{evaluationType}', [ReportDetailController::class, 'studentEvaluationDetail']);
            Route::get('/reports/evaluator-detail/{studentId}/{evaluationType}/{evaluatorId}', [ReportDetailController::class, 'evaluatorDetail']);
            Route::get('/reports/student-evaluations-summary/export', [ReportDetailController::class, 'exportStudentEvaluationsSummary']);
            Route::get('/reports/phase-evaluations', [ReportDetailController::class, 'phaseEvaluatorScores']);
            Route::get('/reports/{type}/export', [ReportExportController::class, 'export']);

            Route::get('/document-requirements', [PhaseDocumentRequirementController::class, 'index']);
            Route::get('/document-requirements/period/{periodId}', [PhaseDocumentRequirementController::class, 'byPeriod']);
            Route::get('/document-requirements/period/{periodId}/summary', [PhaseDocumentRequirementController::class, 'summary']);
            Route::post('/document-requirements', [PhaseDocumentRequirementController::class, 'store']);
            Route::put('/document-requirements/bulk', [PhaseDocumentRequirementController::class, 'bulkUpdate']);
            Route::put('/document-requirements/{id}', [PhaseDocumentRequirementController::class, 'update']);
            Route::delete('/document-requirements/{id}', [PhaseDocumentRequirementController::class, 'destroy']);

            Route::apiResource('stakeholders', StakeholderController::class)->except(['show']);
            Route::post('/titles/{title}/stakeholders', [StakeholderController::class, 'attachToTitle']);
            Route::delete('/titles/{title}/stakeholders/{stakeholder}', [StakeholderController::class, 'detachFromTitle']);

            Route::get('/grade-configuration/{periodId}', [GradeConfigurationController::class, 'getFullConfiguration']);
            Route::post('/grade-configuration/{periodId}', [GradeConfigurationController::class, 'updateWeights']);
            Route::post('/grade-configuration/{periodId}/reset', [GradeConfigurationController::class, 'resetToDefaults']);
            Route::get('/grade-configuration/{periodId}/pdc1', [GradeConfigurationController::class, 'getPDC1Weights']);
            Route::get('/grade-configuration/{periodId}/pdc2', [GradeConfigurationController::class, 'getPDC2Weights']);
            Route::get('/grade-configuration/{periodId}/ta', [GradeConfigurationController::class, 'getTAWeights']);
            Route::get('/grade-configuration/calculate-ta/{studentId}', [GradeConfigurationController::class, 'calculateTAGrade']);
            Route::get('/student-grades/{studentId}', [GradeConfigurationController::class, 'getStudentGrades']);

            Route::get('/student-state/{studentId}', [StudentStateController::class, 'getStudentTAStatus']);
            Route::post('/student-state/{studentId}/force-unlock', [StudentStateController::class, 'forceUnlockTA']);
            Route::get('/groups/{groupId}/ta-statuses', [StudentStateController::class, 'getGroupTAStatus']);

            Route::get('/audit-logs/action-types', [AuditLogController::class, 'actionTypes']);
            Route::get('/audit-logs', [AuditLogController::class, 'index']);
            Route::get('/audit-logs/{id}', [AuditLogController::class, 'show']);
            Route::middleware('permission:capstone.documents.review')->group(function () {
                Route::get('/document-uploads/summary', [DocumentUploadController::class, 'summary']);
                Route::get('/document-uploads', [DocumentUploadController::class, 'index']);
                Route::get('/document-uploads/{id}/download', [DocumentUploadController::class, 'download']);
            });
        });

        // â”€â”€ Dosen â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        Route::middleware(['capstone.role:dosen'])->prefix('dosen')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'dosen']);
            Route::apiResource('titles', TitleController::class);

            Route::middleware('permission:capstone.documents.review')->group(function () {
                Route::get('/documents', [DocumentController::class, 'index']);
                Route::get('/documents/{id}/download', [DocumentController::class, 'download']);
                Route::put('/documents/{id}', [DocumentController::class, 'update']);
            });

            Route::middleware('permission:capstone.evaluations.submit')->group(function () {
                Route::get('/evaluations', [EvaluationController::class, 'index']);
                Route::post('/evaluations', [EvaluationController::class, 'store']);
            });

            Route::middleware('permission:capstone.groups.view')->group(function () {
                Route::get('/groups/pending', [GroupController::class, 'pendingGroups']);
                Route::get('/groups/supervised', [GroupController::class, 'supervisedGroups']);
                Route::get('/groups', [GroupController::class, 'listGroups']);
                Route::get('/groups/{group}', [GroupController::class, 'show']);
                Route::get('/students', [GroupController::class, 'supervisedStudents']);
            });
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
            Route::get('/ta-defense-schedules/examiner', [TaDefenseScheduleController::class, 'examinerSchedules']);
            Route::get('/supervisor-evaluation/groups', [SupervisorEvaluationController::class, 'groups']);
            Route::get('/supervisor-evaluation/schedules', [SupervisorEvaluationController::class, 'schedules']);
            Route::get('/supervisor-evaluation/pending-count', [SupervisorEvaluationController::class, 'pendingCount']);
            Route::get('/supervisor-evaluation/form/{groupId}', [SupervisorEvaluationController::class, 'form']);
            Route::post('/supervisor-evaluation', [SupervisorEvaluationController::class, 'store']);
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
            Route::get('/documents/{id}/download', [DocumentController::class, 'download']);
            Route::get('/workflow', [DocumentController::class, 'workflow']);

            Route::get('/schedules', [ScheduleController::class, 'index']);
            Route::get('/seminar-schedules', [SeminarDashboardController::class, 'studentSchedules']);
            Route::get('/ta-defense', [TaDefenseController::class, 'myDefense']);

            // Expo events
            Route::get('/expo-events', [ExpoEventController::class, 'studentEvents']);
            Route::post('/expo-events/{expoEvent}/register', [ExpoEventController::class, 'register']);

            Route::get('/my-period', [RegistrationController::class, 'myPeriod']);
            Route::get('/periods/{periodId}/check-registration', [RegistrationController::class, 'check']);
            Route::post('/periods/register', [RegistrationController::class, 'register']);

            Route::get('/bursa-ide', [BursaIdeController::class, 'index']);
            Route::post('/bursa-ide/{groupId}/request-join', [BursaIdeController::class, 'requestJoin']);
            Route::get('/join-requests', [BursaIdeController::class, 'myRequests']);
            Route::post('/join-requests/{id}/accept', [BursaIdeController::class, 'acceptRequest']);
            Route::post('/join-requests/{id}/reject', [BursaIdeController::class, 'rejectRequest']);

            Route::get('/solo-titles', [SoloTitleController::class, 'index']);
            Route::post('/solo-titles/{id}/bid', [SoloTitleController::class, 'store']);
            Route::put('/solo-titles/{id}/accept', [SoloTitleController::class, 'acceptBidder']);
            Route::put('/solo-titles/{id}/reject', [SoloTitleController::class, 'rejectBidder']);

            Route::get('/my-grades', [GradeConfigurationController::class, 'getMyGrades']);
            Route::get('/ta-status', [StudentStateController::class, 'getMyTAStatus']);
            Route::get('/ta-defense-schedules/my-schedule', [TaDefenseScheduleController::class, 'mySchedule']);

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

        Route::get('/locations', [LocationController::class, 'active']);
        Route::get('/locations/physical', [LocationController::class, 'physical']);
        Route::get('/locations/online', [LocationController::class, 'online']);
        Route::get('/locations/all', [LocationController::class, 'index']);
        Route::get('/locations/available', [LocationController::class, 'available']);
        Route::middleware(['capstone.role:admin'])->group(function () {
            Route::apiResource('locations', LocationController::class)->except(['index']);
        });

        Route::post('/files/upload', [FileController::class, 'upload']);
        Route::get('/files', [FileController::class, 'list']);
        Route::get('/files/download/{path}', [FileController::class, 'download'])->where('path', '.*');
        Route::get('/files/show/{path}', [FileController::class, 'show'])->where('path', '.*');
        Route::delete('/files/{path}', [FileController::class, 'delete'])->where('path', '.*');
    });
});
