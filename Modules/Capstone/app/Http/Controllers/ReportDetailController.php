<?php

namespace Modules\Capstone\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Capstone\Reports\AssessmentReport;
use Modules\Capstone\Reports\GradeReport;
use Modules\Capstone\Reports\GroupReport;
use Modules\Capstone\Reports\PeerReviewReport;

class ReportDetailController extends Controller
{
    public function assessments(Request $request)
    {
        return app(AssessmentReport::class)->assessments($request);
    }

    public function peerReviews(Request $request)
    {
        return app(PeerReviewReport::class)->peerReviews($request);
    }

    public function peerReviewSummary(Request $request)
    {
        return app(PeerReviewReport::class)->peerReviewSummary($request);
    }

    public function peerReviewDetail(Request $request, $studentId)
    {
        return app(PeerReviewReport::class)->peerReviewDetail($request, $studentId);
    }

    public function finalGrades(Request $request)
    {
        return app(GradeReport::class)->finalGrades($request);
    }

    public function gradeConsistency(Request $request)
    {
        return app(GradeReport::class)->gradeConsistency($request);
    }

    public function groups(Request $request)
    {
        return app(GroupReport::class)->groups($request);
    }

    public function phaseEvaluatorScores(Request $request)
    {
        return app(AssessmentReport::class)->phaseEvaluatorScores($request);
    }

    public function studentEvaluationsSummary(Request $request)
    {
        return app(GradeReport::class)->studentEvaluationsSummary($request);
    }

    public function studentEvaluationDetail(Request $request, $studentId, $evaluationType)
    {
        return app(GradeReport::class)->studentEvaluationDetail($request, $studentId, $evaluationType);
    }

    public function exportStudentEvaluationsSummary(Request $request)
    {
        return app(GradeReport::class)->exportStudentEvaluationsSummary($request);
    }

    public function evaluatorDetail(Request $request, $studentId, $evaluationType, $evaluatorId)
    {
        return app(GradeReport::class)->evaluatorDetail($request, $studentId, $evaluationType, $evaluatorId);
    }
}
