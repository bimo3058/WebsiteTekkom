<?php

namespace Modules\Capstone\Reports;

use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExporter
{
    public static function streamCsv(string $filename, array $headers, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function buildPeerReviewSummaryRows(int $periodId, $groupId, string $search): array
    {
        return PeerReviewReport::summaryRows($periodId, $groupId, $search);
    }

    /**
     * Export assessments to CSV.
     */
    public static function assessmentsCsv($scores): StreamedResponse
    {
        return response()->streamDownload(function () use ($scores) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Group', 'Student', 'Evaluator', 'Type', 'Component', 'Score', 'Date']);

            foreach ($scores as $score) {
                fputcsv($handle, [
                    $score->group->title->title ?? "Group {$score->group_id}",
                    $score->student->name ?? 'N/A',
                    $score->evaluator->name ?? 'N/A',
                    $score->evaluation_type,
                    $score->component->name ?? 'N/A',
                    $score->score,
                    $score->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 'assessments.csv', ['Content-Type' => 'text/csv']);
    }

    /**
     * Export peer reviews to CSV.
     */
    public static function peerReviewsCsv($reviews): StreamedResponse
    {
        return response()->streamDownload(function () use ($reviews) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Group', 'Reviewer', 'Reviewee', 'Indicator', 'Raw Score', 'Converted Score', 'Comment', 'Date']);

            foreach ($reviews as $review) {
                fputcsv($handle, [
                    $review->group->title->title ?? "Group {$review->group_id}",
                    $review->reviewer->name ?? 'N/A',
                    $review->reviewee->name ?? 'N/A',
                    $review->periodIndicator->template->name ?? 'N/A',
                    $review->raw_score,
                    $review->score,
                    $review->comment ?? '',
                    $review->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 'peer_reviews.csv', ['Content-Type' => 'text/csv']);
    }

    /**
     * Export final grades to CSV.
     */
    public static function finalGradesCsv($students): StreamedResponse
    {
        return response()->streamDownload(function () use ($students) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Group', 'Student Name', 'NIM', 'PDC1 Score', 'PDC1 Status', 'PDC2 Score', 'PDC2 Status', 'TA Score', 'TA Status']);

            foreach ($students as $student) {
                fputcsv($handle, [
                    $student['group_name'],
                    $student['student_name'],
                    $student['student_nim'],
                    $student['pdc1_score'] ?? 'N/A',
                    $student['pdc1_complete'] ? 'Complete' : 'Incomplete',
                    $student['pdc2_score'] ?? 'N/A',
                    $student['pdc2_complete'] ? 'Complete' : 'Incomplete',
                    $student['ta_score'] ?? 'N/A',
                    $student['ta_complete'] ? 'Complete' : 'Incomplete',
                ]);
            }

            fclose($handle);
        }, 'final_grades.csv', ['Content-Type' => 'text/csv']);
    }

    /**
     * Export grade consistency to CSV.
     */
    public static function gradeConsistencyCsv($students): StreamedResponse
    {
        return response()->streamDownload(function () use ($students) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Group', 'Student Name', 'PDC1 Score', 'PDC2 Score', 'Deviation', 'Status']);

            foreach ($students as $student) {
                fputcsv($handle, [
                    $student['group_name'],
                    $student['student_name'],
                    $student['pdc1_score'],
                    $student['pdc2_score'],
                    $student['deviation'],
                    $student['status'],
                ]);
            }

            fclose($handle);
        }, 'grade_consistency.csv', ['Content-Type' => 'text/csv']);
    }

    /**
     * Export groups to CSV.
     */
    public static function groupsCsv($groups): StreamedResponse
    {
        return response()->streamDownload(function () use ($groups) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Group ID', 'Title', 'Status', 'Mode', 'Supervisor 1', 'Supervisor 2', 'Members Count', 'Members']);

            foreach ($groups as $group) {
                $memberNames = $group->members->map(function ($m) {
                    return $m->student->name ?? '';
                })->implode('; ');

                fputcsv($handle, [
                    $group->id,
                    $group->title->title ?? '',
                    $group->status,
                    $group->group_mode ?? 'GROUP',
                    $group->supervisor1->name ?? '',
                    $group->supervisor2->name ?? '',
                    $group->members->count(),
                    $memberNames,
                ]);
            }

            fclose($handle);
        }, 'groups.csv', ['Content-Type' => 'text/csv']);
    }

    /**
     * Export student evaluations to CSV.
     */
    public static function studentEvaluationsCsv($students): StreamedResponse
    {
        return response()->streamDownload(function () use ($students) {
            $handle = fopen('php://output', 'w');

            // Header row with 3 columns per evaluation type
            $headers = ['Group', 'Student Name', 'NIM'];
            $evaluationTypes = ['SEMPRO', 'BIMBINGAN_SEMPRO', 'SIDANG_TA', 'BIMBINGAN_TA', 'EXPO', 'MILESTONE', 'NILAI_DOSEN'];
            foreach ($evaluationTypes as $type) {
                $headers[] = $type.' Score';
                $headers[] = $type.' Status';
                $headers[] = $type.' Components';
            }
            fputcsv($handle, $headers);

            // Data rows
            foreach ($students as $student) {
                $row = [
                    $student['group_name'],
                    $student['student_name'],
                    $student['student_nim'],
                ];

                foreach ($evaluationTypes as $type) {
                    $eval = $student['evaluations'][$type];
                    $row[] = $eval['score'] ?? '';
                    $row[] = $eval['status'];
                    $row[] = $eval['scored_components'].'/'.$eval['total_components'];
                }

                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 'student_evaluations_summary.csv', ['Content-Type' => 'text/csv']);
    }
}
