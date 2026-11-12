<?php

namespace Modules\Capstone\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Capstone\Documents\DocumentQueries;
use Modules\Capstone\Http\Controllers\ApiResponseTrait;
use Modules\Capstone\Http\Controllers\Controller;
use Modules\Capstone\Models\Document;
use Modules\Capstone\Models\ExpoStudentDocument;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\TaSubmission;
use Modules\Capstone\Services\DocumentStorageService;

class DocumentUploadController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected DocumentStorageService $documentStorage,
        protected DocumentQueries $queries,
    ) {}

    /**
     * Get all uploaded documents grouped by group with filters and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        $documents = collect();

        $sourceFilter = $request->input('source');

        if (! $sourceFilter || $sourceFilter === 'phase_documents') {
            $documents = $documents->merge($this->queries->getPhaseDocuments($request));
        }

        if (! $sourceFilter || $sourceFilter === 'expo_documents') {
            $documents = $documents->merge($this->queries->getExpoDocuments($request));
        }

        if (! $sourceFilter || $sourceFilter === 'ta_documents') {
            $documents = $documents->merge($this->queries->getTaDocuments($request));
        }

        $documents = $documents->sortByDesc('uploaded_at')->values();

        // Group documents by group_id
        $grouped = $documents->groupBy('group.id')->map(function ($docs, $groupId) {
            $firstDoc = $docs->first();
            $members = $this->queries->getGroupMembers((int) $groupId);

            $phaseCount = $docs->where('source', 'phase_documents')->count();
            $expoCount = $docs->where('source', 'expo_documents')->count();
            $taCount = $docs->where('source', 'ta_documents')->count();

            return [
                'group_id' => (int) $groupId,
                'group_code' => $firstDoc['group']['code'] ?? 'Unknown',
                'period' => $firstDoc['period'] ?? null,
                'members' => $members,
                'total_documents' => $docs->count(),
                'phase_documents_count' => $phaseCount,
                'expo_documents_count' => $expoCount,
                'ta_documents_count' => $taCount,
                'latest_upload_at' => $docs->first()['uploaded_at'] ?? null,
                'documents' => $docs->values()->toArray(),
            ];
        })->values();

        // Sort by latest_upload_at desc
        $grouped = $grouped->sortByDesc('latest_upload_at')->values();

        // Group-level search: filter by group code or member names
        if ($request->filled('search')) {
            $search = strtolower($request->input('search'));
            $grouped = $grouped->filter(function ($group) use ($search) {
                if (str_contains(strtolower($group['group_code']), $search)) {
                    return true;
                }

                return collect($group['members'])->contains(function ($member) use ($search) {
                    return str_contains(strtolower($member['name']), $search);
                });
            })->values();
        }

        // Manual pagination
        $total = $grouped->count();
        $lastPage = (int) ceil($total / $perPage);
        $currentPage = min(max($page, 1), $lastPage ?: 1);
        $offset = ($currentPage - 1) * $perPage;

        $paginatedGroups = $grouped->slice($offset, $perPage)->values();

        return $this->successResponse([
            'data' => $paginatedGroups,
            'pagination' => [
                'current_page' => $currentPage,
                'last_page' => $lastPage,
                'per_page' => (int) $perPage,
                'total' => $total,
            ],
        ], 'Documents retrieved successfully');
    }

    /**
     * Get summary statistics for document uploads.
     */
    public function summary(Request $request): JsonResponse
    {
        $periodId = $request->input('period_id');

        $phaseQuery = Document::query();
        $expoQuery = ExpoStudentDocument::query();
        $taQuery = TaSubmission::query();

        if ($periodId) {
            $phaseQuery->whereHas('group', fn ($q) => $q->where('period_id', $periodId));
            $expoQuery->whereHas('group', fn ($q) => $q->where('period_id', $periodId));
            $taQuery->whereHas('group', fn ($q) => $q->where('period_id', $periodId));
        }

        $phaseCount = $phaseQuery->count();
        $expoCount = $expoQuery->count();

        $taCount = (clone $taQuery)->whereNotNull('file_path')->count()
            + (clone $taQuery)->whereNotNull('draft_report_path')->count()
            + (clone $taQuery)->whereNotNull('paper_path')->count();

        $groupsWithPhaseDocs = (clone $phaseQuery)->distinct('group_id')->pluck('group_id');
        $groupsWithExpoDocs = (clone $expoQuery)->distinct('group_id')->pluck('group_id');
        $groupsWithTaDocs = (clone $taQuery)->whereNotNull('file_path')->distinct('group_id')->pluck('group_id');

        $groupsWithUploads = $groupsWithPhaseDocs
            ->merge($groupsWithExpoDocs)
            ->merge($groupsWithTaDocs)
            ->unique()
            ->count();

        $studentsWithPhaseDocs = (clone $phaseQuery)->distinct('student_id')->pluck('student_id');
        $studentsWithExpoDocs = (clone $expoQuery)->distinct('student_id')->pluck('student_id');
        $studentsWithTaDocs = (clone $taQuery)->whereNotNull('file_path')->distinct('student_id')->pluck('student_id');

        $studentsWithUploads = $studentsWithPhaseDocs
            ->merge($studentsWithExpoDocs)
            ->merge($studentsWithTaDocs)
            ->unique()
            ->count();

        return $this->successResponse([
            'groups_with_uploads' => $groupsWithUploads,
            'students_with_uploads' => $studentsWithUploads,
            'phase_documents' => $phaseCount,
            'expo_documents' => $expoCount,
            'ta_documents' => $taCount,
            'total_documents' => $phaseCount + $expoCount + $taCount,
        ], 'Summary retrieved successfully');
    }

    /**
     * Download a document.
     */
    public function download(Request $request, int $id)
    {
        $source = $request->input('source');

        if (! $source || ! in_array($source, ['phase_documents', 'expo_documents', 'ta_documents'])) {
            return $this->errorResponse('Invalid or missing source parameter', 400);
        }

        $document = null;
        $filePath = null;
        $originalName = null;

        switch ($source) {
            case 'phase_documents':
                $document = Document::with(['group', 'student'])->find($id);
                if ($document) {
                    $filePath = $document->file_path;
                    $originalName = $this->buildDownloadName($document->document_type, $document->original_name ?? basename($filePath), $filePath, (int) $document->version, $document->group?->code);
                }
                break;

            case 'expo_documents':
                $document = ExpoStudentDocument::with(['group', 'student', 'expoRegistration'])->find($id);
                if ($document) {
                    $filePath = $document->file_path;
                    $originalName = $this->prefixedName('Expo Document', $document->original_name ?? basename($filePath));
                }
                break;

            case 'ta_documents':
                $taSubmission = TaSubmission::with(['group', 'student'])->find($id);
                if ($taSubmission) {
                    $document = $taSubmission;
                    $filePath = $taSubmission->file_path;
                    $originalName = basename($filePath);
                }
                break;
        }

        if (! $document || ! $filePath) {
            return $this->notFoundResponse('Document not found');
        }

        $fileData = $this->documentStorage->get($filePath);

        if (! $fileData) {
            return $this->notFoundResponse('File not found in storage');
        }

        return response($fileData['content'], 200, [
            'Content-Type' => $fileData['mime_type'],
            'Content-Disposition' => 'attachment; filename="'.$originalName.'"',
        ]);
    }

    /**
     * Build the admin download filename as `{slug(type)}_v{version}_{group}.{ext}`.
     * The group suffix avoids collisions when many groups' files land in one folder.
     */
    private function buildDownloadName(?string $type, string $originalName, string $filePath, int $version, ?string $groupCode = null): string
    {
        $slug = static function (?string $value): string {
            $value = trim((string) $value);
            $value = (string) preg_replace('/[^A-Za-z0-9]+/', '_', $value);
            $value = trim($value, '_');

            return $value !== '' ? $value : 'document';
        };

        $allowed = ['pdf', 'doc', 'docx', 'ppt', 'pptx'];
        $ext = null;
        foreach ([$originalName, explode('?', $filePath)[0]] as $candidate) {
            if (is_string($candidate) && preg_match('/\.([A-Za-z0-9]{2,5})$/', $candidate, $matches) && in_array(strtolower($matches[1]), $allowed, true)) {
                $ext = strtolower($matches[1]);
                break;
            }
        }
        $ext ??= 'pdf';

        $base = $slug($type).'_v'.((int) $version);
        if ($groupCode !== null && $groupCode !== '') {
            $base .= '_'.$slug($groupCode);
        }

        return str_replace(['"', "\r", "\n"], '', $base.'.'.$ext);
    }

    private function prefixedName(?string $type, string $base): string
    {
        $base = str_replace(['"', "\r", "\n"], '', $base);
        $prefix = $type ? preg_replace('/[^A-Za-z0-9\-]+/', '', (string) $type) : '';

        if ($prefix !== '' && ! str_starts_with($base, $prefix.'_') && ! str_starts_with($base, $prefix.'-')) {
            return $prefix.'_'.$base;
        }

        return $base;
    }
}
