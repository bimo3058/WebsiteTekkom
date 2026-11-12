<?php

namespace Modules\Capstone\Documents;

use Illuminate\Http\Request;
use Modules\Capstone\Models\Document;
use Modules\Capstone\Models\ExpoStudentDocument;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\TaSubmission;

class DocumentQueries
{
    /**
     * Get members for a group.
     */
    public function getGroupMembers(int $groupId): array
    {
        return GroupMember::with(['student'])
            ->where('group_id', $groupId)
            ->whereNull('deleted_at')
            ->get()
            ->map(fn (GroupMember $member) => [
                'id' => $member->student->id ?? null,
                'name' => $member->student->name ?? 'Unknown',
                'nim' => $member->student->nim ?? null,
                'email' => $member->student->email ?? null,
                'is_leader' => $member->is_leader,
            ])
            ->toArray();
    }

    /**
     * Get phase documents with filters.
     */
    public function getPhaseDocuments(Request $request): array
    {
        $query = Document::with(['student', 'group.period', 'reviewer']);

        if ($request->filled('period_id')) {
            $query->whereHas('group', fn ($q) => $q->where('period_id', $request->input('period_id')));
        }

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->input('group_id'));
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        return $query->get()->map(function ($doc) {
            return [
                'id' => $doc->id,
                'source' => 'phase_documents',
                'source_label' => 'Phase Documents',
                'file_path' => $doc->file_path,
                'original_name' => basename($doc->file_path),
                'document_type' => $doc->document_type ?? $doc->phase,
                'phase' => $doc->phase,
                'status' => $doc->status,
                'feedback' => $doc->feedback,
                'uploaded_at' => $doc->created_at->toIso8601String(),
                'student' => $doc->student ? [
                    'id' => $doc->student->id,
                    'name' => $doc->student->name,
                    'nim' => $doc->student->nim ?? null,
                    'email' => $doc->student->email,
                ] : null,
                'group' => $doc->group ? [
                    'id' => $doc->group->id,
                    'code' => $doc->group->code,
                ] : null,
                'period' => $doc->group?->period ? [
                    'id' => $doc->group->period->id,
                    'name' => $doc->group->period->name,
                ] : null,
                'reviewer' => $doc->reviewer ? [
                    'id' => $doc->reviewer->id,
                    'name' => $doc->reviewer->name,
                ] : null,
            ];
        })->toArray();
    }

    /**
     * Get expo documents with filters.
     */
    public function getExpoDocuments(Request $request): array
    {
        $query = ExpoStudentDocument::with(['student', 'group.period', 'expoRegistration.expoEvent', 'reviewer']);

        if ($request->filled('period_id')) {
            $query->whereHas('group', fn ($q) => $q->where('period_id', $request->input('period_id')));
        }

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->input('group_id'));
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        return $query->get()->map(function ($doc) {
            return [
                'id' => $doc->id,
                'source' => 'expo_documents',
                'source_label' => 'Expo Documents',
                'file_path' => $doc->file_path,
                'original_name' => $doc->original_name,
                'document_type' => 'Expo Document',
                'phase' => 'EXPO',
                'status' => $doc->status,
                'feedback' => $doc->feedback,
                'uploaded_at' => $doc->created_at->toIso8601String(),
                'student' => $doc->student ? [
                    'id' => $doc->student->id,
                    'name' => $doc->student->name,
                    'nim' => $doc->student->nim ?? null,
                    'email' => $doc->student->email,
                ] : null,
                'group' => $doc->group ? [
                    'id' => $doc->group->id,
                    'code' => $doc->group->code,
                ] : null,
                'period' => $doc->group?->period ? [
                    'id' => $doc->group->period->id,
                    'name' => $doc->group->period->name,
                ] : null,
                'expo_event' => $doc->expoRegistration?->expoEvent ? [
                    'id' => $doc->expoRegistration->expoEvent->id,
                    'name' => $doc->expoRegistration->expoEvent->name,
                ] : null,
                'reviewer' => $doc->reviewer ? [
                    'id' => $doc->reviewer->id,
                    'name' => $doc->reviewer->name,
                ] : null,
            ];
        })->toArray();
    }

    /**
     * Get TA documents with filters.
     */
    public function getTaDocuments(Request $request): array
    {
        $query = TaSubmission::with(['student', 'group.period', 'reviewer']);

        if ($request->filled('period_id')) {
            $query->whereHas('group', fn ($q) => $q->where('period_id', $request->input('period_id')));
        }

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->input('group_id'));
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $query->where(function ($q) {
            $q->whereNotNull('file_path')
                ->orWhereNotNull('draft_report_path')
                ->orWhereNotNull('paper_path');
        });

        $documents = [];
        $submissions = $query->get();

        foreach ($submissions as $submission) {
            if ($submission->file_path) {
                $documents[] = $this->queries->formatTaDocument($submission, 'file_path', 'TA File');
            }
            if ($submission->draft_report_path) {
                $documents[] = $this->queries->formatTaDocument($submission, 'draft_report_path', 'Draft Report');
            }
            if ($submission->paper_path) {
                $documents[] = $this->queries->formatTaDocument($submission, 'paper_path', 'Paper');
            }
        }

        return $documents;
    }

    /**
     * Format TA submission as document.
     */
    public function formatTaDocument(TaSubmission $submission, string $field, string $documentType): array
    {
        $filePath = $submission->{$field};

        return [
            'id' => $submission->id,
            'source' => 'ta_documents',
            'source_label' => 'TA Documents',
            'file_path' => $filePath,
            'original_name' => basename($filePath),
            'document_type' => $documentType,
            'phase' => 'TA',
            'status' => $submission->status,
            'feedback' => $submission->feedback,
            'uploaded_at' => $submission->created_at->toIso8601String(),
            'student' => $submission->student ? [
                'id' => $submission->student->id,
                'name' => $submission->student->name,
                'nim' => $submission->student->nim ?? null,
                'email' => $submission->student->email,
            ] : null,
            'group' => $submission->group ? [
                'id' => $submission->group->id,
                'code' => $submission->group->code,
            ] : null,
            'period' => $submission->group?->period ? [
                'id' => $submission->group->period->id,
                'name' => $submission->group->period->name,
            ] : null,
            'reviewer' => $submission->reviewer ? [
                'id' => $submission->reviewer->id,
                'name' => $submission->reviewer->name,
            ] : null,
        ];
    }
}
