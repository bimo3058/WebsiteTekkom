<?php

namespace Modules\Capstone\Services;

use App\Models\User;
use App\Services\SupabaseStorage;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Capstone\Exceptions\DomainRuleException;
use Modules\Capstone\Models\Document;
use Modules\Capstone\Models\ExpoStudentDocument;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\PhaseDocumentRequirement;
use Modules\Capstone\Support\CapstoneActor;

class DocumentStorageService
{
    public function __construct(
        protected SupabaseStorage $supabase,
        protected NotificationService $notifications,
        protected GroupLifecycleService $lifecycle,
        protected IndividualTaWorkflow $individualTa,
        protected GroupStateMachine $stateMachine,
    ) {}

    /**
     * Store file in MinIO (new uploads)
     */
    public function store(UploadedFile $file, string $folder, string $prefix = ''): string
    {
        $safeName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $filename = now()->format('YmdHis').'_'.Str::random(12).'_'.($safeName ?: 'document');
        $folder = trim("capstone/{$folder}/{$prefix}", '/');
        $path = $this->supabase->upload($file, $folder, null, $filename);

        if (! $path) {
            throw new \RuntimeException('Gagal mengunggah dokumen ke Supabase Storage.');
        }

        return $path;
    }

    /**
     * Get file for download - supports both old (local) and new (MinIO)
     */
    public function get(string $path): ?array
    {
        if (str_starts_with($path, 'capstone/')) {
            $file = $this->supabase->download($path);

            return $file ? ['disk' => 'supabase', ...$file] : null;
        }

        // Fallback to local (old files)
        if (Storage::disk('public')->exists($path)) {
            return [
                'disk' => 'public',
                'path' => Storage::disk('public')->path($path),
            ];
        }

        return null;
    }

    /**
     * Delete file from both storages
     */
    public function delete(string $path): void
    {
        if (str_starts_with($path, 'capstone/')) {
            $this->supabase->delete($path);
        }
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Migrate single file from local to MinIO
     */
    public function migrateToS3(string $path): bool
    {
        if (! Storage::disk('public')->exists($path)) {
            Log::warning("File not found for migration: {$path}");

            return false;
        }

        try {
            $targetPath = 'capstone/legacy/'.ltrim($path, '/');
            $content = Storage::disk('public')->get($path);
            $mimeType = Storage::disk('public')->mimeType($path) ?: 'application/octet-stream';

            return $this->supabase->put($targetPath, $content, $mimeType);
        } catch (\Exception $e) {
            Log::error("Migration failed for {$path}: ".$e->getMessage());

            return false;
        }
    }

    public function signedUrl(string $path, int $expiresIn = 300): ?string
    {
        return str_starts_with($path, 'capstone/')
            ? $this->supabase->signedUrl($path, $expiresIn)
            : null;
    }

    /** @return array<int, array<string, mixed>> */
    public function list(string $prefix): array
    {
        return $this->supabase->list(trim('capstone/'.$prefix, '/'));
    }

    // ══════════════════════════════════════════
    // Document Write Orchestration
    // ══════════════════════════════════════════
    //
    // Moved verbatim from DocumentController (Batch C): the controller
    // keeps input validation (Form Requests) + response envelopes; every
    // cross-row guard lives here. Envelope mapping (controller side):
    // DomainRuleException → 400 same message,
    // AuthorizationException → 403 same message (workflow gate,
    // approved-replace refusal, supervisor checks that returned 403 JSON),
    // other \Exception → 500. abort_unless() calls are kept as-is: they
    // already bubble identical 403s through the exception handler.

    /**
     * Submit (upload or revise) a phase document.
     *
     * The workflow shape is computed by the controller's workflow reader
     * (the single reader) and passed in — this method never recomputes it,
     * so forged POSTs are blocked by the same gates as the UI.
     * Disabling a button alone does not protect a forged or stale upload.
     *
     * @param  array<string, mixed>  $workflowData  workflow() response data.
     */
    public function submitDocument(array $workflowData, UploadedFile $file, string $phase, string $documentType, User $user): Document
    {
        $studentId = CapstoneActor::student($user)->id;
        $groupMember = GroupMember::with('group')->where('student_id', $studentId)->first();

        if (! $groupMember) {
            throw new DomainRuleException('You are not in any group.');
        }

        // Recomputed at submission time, before touching storage.
        $phaseInfo = collect($workflowData['phases'] ?? [])->firstWhere('phase', $phase);
        $documentInfo = collect($phaseInfo['documents'] ?? [])->firstWhere('type', $documentType);
        $reason = $phaseInfo['locked_reason'] ?? $documentInfo['locked_reason'] ?? null;
        if (! $phaseInfo || ! ($phaseInfo['can_upload'] ?? false) || ($documentInfo && ! $documentInfo['can_upload'])) {
            throw new AuthorizationException($reason ?? 'Document upload is locked.');
        }

        $existingDoc = Document::where('group_id', $groupMember->group_id)
            ->where('phase', $phase)
            ->where('document_type', $documentType)
            ->first();
        if ($existingDoc?->status === 'APPROVED') {
            throw new AuthorizationException('Approved documents cannot be replaced.');
        }

        $path = $this->store($file, 'documents', $groupMember->group_id.'/'.$phase);
        $originalName = $file->getClientOriginalName();

        if ($existingDoc) {
            // Archive the current file into history before replacing it, then
            // bump the version. History rows are retained for audit but only
            // the latest version is downloadable.
            $existingDoc->versions()->create([
                'version' => (int) $existingDoc->version,
                'file_path' => $existingDoc->file_path,
                'original_name' => $existingDoc->original_name,
                'created_by' => $user->id,
            ]);

            // Update existing record (new version)
            $existingDoc->update([
                'file_path' => $path,
                'original_name' => $originalName,
                'version' => ((int) $existingDoc->version) + 1,
                'status' => 'SUBMITTED',
                'feedback' => null, // Reset feedback on resubmit
            ]);

            return $existingDoc->fresh(['versions']);
        }

        // First-time upload
        return Document::create([
            'group_id' => $groupMember->group_id,
            'student_id' => $studentId,
            'phase' => $phase,
            'document_type' => $documentType,
            'file_path' => $path,
            'original_name' => $originalName,
            'version' => 1,
            'status' => 'SUBMITTED',
        ]);
    }

    /**
     * Review a document (dosen): phase documents and namespaced expo
     * member documents. Approving the last pending member expo document
     * advances the group (EXPO_DONE, cascading when final readiness is
     * met); rejected documents can be re-uploaded by the student.
     *
     * @param  array<string, mixed>  $validated  Validated review data.
     */
    public function reviewDocument(string $id, array $validated, int $lecturerId, int $userId): Document|ExpoStudentDocument
    {
        // Namespaced expo ids (see index): supervisor review of a member
        // expo document.
        if (str_starts_with($id, 'expo-')) {
            $expoId = (int) substr($id, 5);

            return DB::transaction(function () use ($expoId, $lecturerId, $validated, $userId) {
                $expoDocument = ExpoStudentDocument::whereKey($expoId)->lockForUpdate()->firstOrFail();
                Group::whereKey($expoDocument->group_id)->lockForUpdate()->firstOrFail();
                abort_unless(
                    Group::whereKey($expoDocument->group_id)
                        ->supervisedBy($lecturerId)
                        ->exists(),
                    403,
                    'Anda bukan dosen pembimbing kelompok ini.'
                );
                $expoDocument->update([
                    'status' => $validated['status'],
                    'feedback' => $validated['feedback'] ?? null,
                    'reviewed_by' => $userId,
                ]);

                $studentUserId = $expoDocument->student?->user_id;
                if ($studentUserId) {
                    $statusStr = strtolower($validated['status']);
                    $this->notifications->sendToMany(
                        [$studentUserId],
                        'PROPOSAL_'.strtoupper($validated['status']),
                        "Document {$validated['status']}",
                        "Your EXPO document ({$expoDocument->original_name}) has been {$statusStr}".(($validated['feedback'] ?? null) ? " with feedback: {$validated['feedback']}" : '.'),
                        'documents',
                        $expoDocument->id
                    );
                }

                if ($validated['status'] === 'APPROVED') {
                    $this->lifecycle->advanceIfComplete(Group::find($expoDocument->group_id));
                }

                return $expoDocument;
            });
        }

        return DB::transaction(function () use ($id, $lecturerId, $validated, $userId) {
            $document = Document::findOrFail($id);
            Group::whereKey($document->group_id)->lockForUpdate()->firstOrFail();
            $document = Document::whereKey($id)->lockForUpdate()->firstOrFail();
            abort_unless(
                Group::whereKey($document->group_id)
                    ->supervisedBy($lecturerId)
                    ->exists(),
                403,
                'Anda bukan dosen pembimbing kelompok ini.'
            );
            $document->update([
                'status' => $validated['status'],
                'feedback' => $validated['feedback'] ?? null,
                'reviewed_by' => $userId,
            ]);

            // Auto-transition: if all required document subtypes for phase are APPROVED
            $group = Group::findOrFail($document->group_id);
            if ($document->phase === IndividualTaWorkflow::DOCUMENT_PHASE) {
                $this->individualTa->syncSubmission($document->student_id);
            }
            $hasRequirements = PhaseDocumentRequirement::where('period_id', $group->period_id)
                ->where('phase', $document->phase)
                ->where('is_required', true)
                ->exists();

            if ($validated['status'] === 'APPROVED' && $hasRequirements) {
                $this->checkPhaseCompletion($document->group_id, $document->phase);
            }

            // Send notifications
            $studentIds = $group->members()->with('student')->get()
                ->pluck('student.user_id')
                ->filter()
                ->values()
                ->all();
            if ($document->phase === IndividualTaWorkflow::DOCUMENT_PHASE) {
                $studentIds = array_filter([$document->student?->user_id]);
            }
            $statusStr = strtolower($validated['status']);
            $this->notifications->sendToMany(
                $studentIds,
                'PROPOSAL_'.strtoupper($validated['status']), // e.g. PROPOSAL_APPROVED, PROPOSAL_REJECTED (reused for doc status)
                "Document {$validated['status']}",
                "Your {$document->phase} document ({$document->document_type}) has been {$statusStr}".(($validated['feedback'] ?? null) ? " with feedback: {$validated['feedback']}" : '.'),
                'documents',
                $document->id
            );

            return $document;
        });
    }

    /**
     * Check if all required document types for a phase are approved, and auto-transition.
     */
    private function checkPhaseCompletion(int $groupId, string $phase): void
    {
        $group = Group::findOrFail($groupId);
        $requiredTypes = PhaseDocumentRequirement::where('period_id', $group->period_id)
            ->where('phase', $phase)
            ->where('is_required', true)
            ->pluck('name')->toArray();

        if (empty($requiredTypes)) {
            return;
        }

        foreach ($requiredTypes as $type) {
            $hasApproved = Document::where('group_id', $groupId)
                ->where('phase', $phase)
                ->where('document_type', $type)
                ->where('status', 'APPROVED')
                ->exists();

            if (! $hasApproved) {
                return;
            } // Not all types approved yet
        }

        // All required types approved — trigger transition
        try {
            if ($phase === 'PDC1' && $group->status === 'PDC1_ACTIVE') {
                $this->stateMachine->transition($group, 'READY_FOR_SEMPRO');
            } elseif ($phase === 'PDC2' && in_array($group->status, ['PDC2_ACTIVE', 'TA_DRAFT'], true)) {
                $this->stateMachine->transition($group, 'PDC2_READY_FOR_EXPO');
            }
        } catch (\InvalidArgumentException $e) {
            // Transition not valid from current state — ignore
        }
    }
}
