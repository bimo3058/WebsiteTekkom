<?php

namespace Modules\Capstone\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Capstone\Exceptions\DomainRuleException;
use Modules\Capstone\Http\Requests\Document\ReviewDocumentRequest;
use Modules\Capstone\Http\Requests\Document\StoreDocumentRequest;
use Modules\Capstone\Models\Document;
use Modules\Capstone\Models\ExpoStudentDocument;
use Modules\Capstone\Models\Group;
use Modules\Capstone\Models\GroupMember;
use Modules\Capstone\Models\PhaseDocumentRequirement;
use Modules\Capstone\Models\SeminarSchedule;
use Modules\Capstone\Services\DocumentStorageService;
use Modules\Capstone\Services\IndividualTaWorkflow;
use Modules\Capstone\Services\WorkflowService;
use Modules\Capstone\Support\BladeFeatureAccess;
use Modules\Capstone\Support\CapstoneActor;
use Modules\Capstone\Support\WorkflowPhases;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentStorageService $documentStorage,
    ) {}

    // UI stepper phases (1:1 with the dashboard blade, see StepperPhaseContractTest).
    // The approval engine uses WorkflowPhases::PHASES (`TA_DRAFT`, no `SIDANG`);
    // `TA` here maps to engine `TA_DRAFT` wherever the two meet.
    const PHASES = WorkflowPhases::UI_PHASES;

    // Unlock rules: phase => prerequisite phase that must be APPROVED
    const UNLOCK_RULES = [
        'PDC1' => null,          // Always unlocked if group is APPROVED
        'SEMPRO' => 'PDC1',        // PDC1 approved â†’ unlock Sempro
        'PDC2' => 'SEMPRO',      // Sempro approved â†’ unlock PDC2
        'EXPO' => 'PDC2',          // PDC2 approved -> unlock EXPO
        'TA' => 'PDC2',        // PDC2 approved â†’ unlock TA
        'SIDANG' => 'EXPO',          // EXPO approved â†’ unlock Sidang
    ];

    /**
     * Get the workflow status for a group (which phases are unlocked/completed).
     */
    public function workflow(Request $request)
    {
        $user = Auth::user();
        $groupMember = GroupMember::with('group')
            ->where('student_id', CapstoneActor::student($user)->id)
            ->first();

        if (! $groupMember || ! $groupMember->group) {
            return response()->json(['phases' => [], 'current_phase' => null]);
        }

        $periodId = $groupMember->group->period_id;
        $allRequirements = PhaseDocumentRequirement::where('period_id', $periodId)->get();
        $documents = Document::where('group_id', $groupMember->group_id)->get();
        $phases = [];
        $semproScheduled = SeminarSchedule::where('group_id', $groupMember->group_id)->where('type', 'SEMPRO')
            ->whereNotIn('status', ['CANCELLED', 'REJECTED', 'PENDING', 'PENDING_APPROVAL'])->exists();
        // Mirror of $semproScheduled for EXPO: expo registration auto-creates
        // an APPROVED EXPO schedule, withdrawal cancels it. EXPO documents
        // stay locked (menunggu dijadwalkan) until the group is registered.
        $expoScheduled = SeminarSchedule::where('group_id', $groupMember->group_id)->where('type', 'EXPO')
            ->whereNotIn('status', ['CANCELLED', 'REJECTED', 'PENDING', 'PENDING_APPROVAL'])->exists();
        $groupReason = BladeFeatureAccess::reason('/mahasiswa/documents', [
            'registered' => true,
            'group_status' => $groupMember->group->status,
            'pdc1_started' => BladeFeatureAccess::hasStartedPdc1($groupMember->group),
        ]);

        foreach (self::PHASES as $phase) {
            $shape = WorkflowService::phaseShape($documents, $allRequirements, $phase);
            $requiredTypes = $shape['required_types'];
            $typesStatus = $shape['types_status'];
            $allApproved = $shape['all_approved'];
            $anyRejected = $shape['any_rejected'];
            $anySubmitted = $shape['any_submitted'];
            $uploadedCount = $shape['uploaded_count'];

            $phaseStatus = 'locked';
            $prereq = self::UNLOCK_RULES[$phase];

            if ($prereq === null) {
                $phaseStatus = 'unlocked';
            } else {
                $prereqReqs = $allRequirements->where('phase', $prereq)->where('is_required', true)->pluck('name')->toArray();
                if (empty($prereqReqs)) {
                    $prereqReqs = ['GENERAL'];
                }

                $prereqAllApproved = true;
                foreach ($prereqReqs as $pType) {
                    $pDoc = $documents->where('phase', $prereq)->where('document_type', $pType)->where('status', 'APPROVED')->first();
                    if ($pType === 'GENERAL' && empty($allRequirements->where('phase', $prereq)->toArray())) {
                        $pDoc = $documents->where('phase', $prereq)->where('status', 'APPROVED')->first();
                    }
                    if (! $pDoc) {
                        $prereqAllApproved = false;
                        break;
                    }
                }

                if ($prereqAllApproved) {
                    $phaseStatus = 'unlocked';
                }
            }

            if ($phaseStatus === 'unlocked') {
                if ($phase === 'EXPO') {
                    // Custom rule for EXPO: requires at least 1 APPROVED TA draft
                    // document (from the documents/workflow page). A
                    // capstone_ta_submissions row is NOT required.
                    $hasTaDraft = $groupMember->group->hasApprovedTaDraftDocument();
                    if (! $hasTaDraft) {
                        $phaseStatus = 'locked';
                    }
                }

                if ($phaseStatus === 'unlocked') {
                    if ($allApproved) {
                        $phaseStatus = 'completed';
                    } elseif ($anyRejected) {
                        $phaseStatus = 'revision';
                    } elseif ($anySubmitted) {
                        $phaseStatus = 'submitted';
                    } elseif ($uploadedCount > 0) {
                        $phaseStatus = 'draft';
                    }
                }
            }

            $phaseInfo = [
                'phase' => $phase,
                'status' => $phaseStatus,
                'documents' => $typesStatus,
                'required_types' => $requiredTypes,
                'document_count' => $shape['document_count'],
            ];

            // Per-phase group-status gate: the document-approval chain alone
            // must not unlock uploads for stages the group has not reached
            // (e.g. PDC2 while still SEMPRO_DONE). store() replays this
            // workflow, so forged POSTs are blocked as well.
            $phaseGateReason = BladeFeatureAccess::phaseGateReason($phase, $groupMember->group->status);
            if ($phaseGateReason !== null) {
                $phaseInfo['status'] = 'locked';
            }
            if ($groupReason) {
                $phaseInfo['status'] = 'locked';
            }
            $phaseInfo['locked_reason'] = $groupReason ?? $phaseGateReason ?? BladeFeatureAccess::documentUploadReason($phaseInfo, $semproScheduled, null, $expoScheduled);
            $phaseInfo['can_upload'] = $phaseInfo['locked_reason'] === null;
            $phaseInfo['documents'] = array_map(function ($document) use ($phaseInfo, $semproScheduled, $expoScheduled) {
                $document['locked_reason'] = $phaseInfo['locked_reason'] ?? BladeFeatureAccess::documentUploadReason($phaseInfo, $semproScheduled, $document['status'], $expoScheduled);
                $document['can_upload'] = $document['locked_reason'] === null;

                return $document;
            }, $typesStatus);
            $phases[] = $phaseInfo;
        }

        $currentPhase = null;
        foreach ($phases as $p) {
            if ($p['status'] !== 'completed') {
                $currentPhase = $p['phase'];
                break;
            }
        }

        $allCompleted = collect($phases)->every(fn ($p) => $p['status'] === 'completed');

        return response()->json([
            'phases' => $phases,
            'current_phase' => $currentPhase,
            'is_graduated' => $allCompleted,
            'seminar_schedule' => ['exists' => $semproScheduled],
            'expo_schedule' => ['exists' => $expoScheduled],
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = CapstoneActor::role(
            $user,
            $request->attributes->get('capstone_role') ?? $request->header('X-Capstone-Role')
        );

        if ($role === 'mahasiswa') {
            $groupMember = GroupMember::where('student_id', CapstoneActor::student($user)->id)->first();
            if (! $groupMember) {
                return response()->json(['data' => []]);
            }
            $documents = Document::where('group_id', $groupMember->group_id)
                ->where(fn ($query) => $query->where('phase', '!=', IndividualTaWorkflow::DOCUMENT_PHASE)->orWhere('student_id', CapstoneActor::student($user)->id))
                ->with(['student'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json(['data' => $documents]);
        }

        if ($role === 'dosen') {
            $lecturerId = CapstoneActor::lecturer($user)->id;
            $query = Document::with(['student', 'group.title']);
            $supervisedGroupIds = Group::supervisedBy($lecturerId)->pluck('id');

            $query->whereIn('group_id', $supervisedGroupIds);

            if ($request->has('group_id')) {
                $query->where('group_id', $request->group_id);
            }

            $documents = $query->orderBy('created_at', 'desc')->get();

            // Supervisor EXPO review: member expo documents (daftar expo
            // uploads) for supervised groups. EXPO has no examiner
            // evaluation, so supervisors approve these here. Namespaced
            // `expo-{id}` ids route review/download to the expo table;
            // the payload mirrors phase documents so the dosen documents
            // UI (review dialog, download, filters, grouping) works
            // unchanged.
            $expoDocuments = ExpoStudentDocument::with(['student', 'group.title'])
                ->whereIn('group_id', $supervisedGroupIds)
                ->when($request->has('group_id'), fn ($docs) => $docs->where('group_id', $request->input('group_id')))
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn ($doc) => [
                    'id' => 'expo-'.$doc->id,
                    'group_id' => $doc->group_id,
                    'group' => $doc->group ? [
                        'id' => $doc->group->id,
                        'code' => $doc->group->code,
                        'period_id' => $doc->group->period_id,
                        'title' => $doc->group->title ? ['title' => $doc->group->title->title] : null,
                    ] : null,
                    'student' => $doc->student,
                    'phase' => 'EXPO',
                    'document_type' => 'Expo Document',
                    'file_name' => $doc->original_name,
                    'version' => 1,
                    'status' => $doc->status,
                    'feedback' => $doc->feedback,
                    'created_at' => $doc->created_at,
                ]);

            $documents = $documents->concat($expoDocuments)->sortByDesc('created_at')->values();

            return response()->json(['data' => $documents]);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();

        // The workflow reader is the single source of upload gates; the
        // service replays it, so forged POSTs are blocked as well.
        // Disabling a button alone does not protect a forged or stale upload.
        $workflow = $this->workflow($request)->getData(true);

        try {
            $document = $this->documentStorage->submitDocument(
                $workflow,
                $request->file('file'),
                $validated['phase'],
                $validated['document_type'] ?? 'GENERAL',
                $user
            );
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (DomainRuleException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to upload document: '.$e->getMessage()], 500);
        }

        if ($document->wasRecentlyCreated) {
            return response()->json(['message' => 'Document uploaded successfully', 'data' => $document], 201);
        }

        return response()->json(['message' => 'Document revised (replaced) successfully', 'data' => $document], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function download(Request $request, string $id)
    {
        // Namespaced expo ids (see index) resolve against the member
        // expo documents table instead of phase documents.
        if (str_starts_with($id, 'expo-')) {
            $expoDocument = ExpoStudentDocument::findOrFail((int) substr($id, 5));
            $user = $request->user();
            $role = CapstoneActor::role(
                $user,
                $request->attributes->get('capstone_role') ?? $request->header('X-Capstone-Role')
            );

            $allowed = $role === 'admin';
            if ($role === 'mahasiswa') {
                $student = CapstoneActor::student($user);
                $allowed = $student && GroupMember::where('group_id', $expoDocument->group_id)
                    ->where('student_id', $student->id)
                    ->exists();
            }
            if ($role === 'dosen') {
                $lecturer = CapstoneActor::lecturer($user);
                $allowed = $lecturer && Group::whereKey($expoDocument->group_id)
                    ->supervisedBy($lecturer->id)
                    ->exists();
            }

            abort_unless($allowed, 403);

            $file = $this->documentStorage->get($expoDocument->file_path);
            abort_unless($file, 404, 'File not found');

            return response($file['content'], 200, [
                'Content-Type' => $file['mime_type'],
                'Content-Disposition' => 'attachment; filename="'.$this->downloadFilename('Expo Document', $expoDocument->original_name, $expoDocument->file_path).'"',
            ]);
        }

        $document = Document::findOrFail($id);
        $user = $request->user();
        $role = CapstoneActor::role(
            $user,
            $request->attributes->get('capstone_role') ?? $request->header('X-Capstone-Role')
        );

        $allowed = $role === 'admin';
        if ($role === 'mahasiswa') {
            $student = CapstoneActor::student($user);
            $allowed = $student && GroupMember::where('group_id', $document->group_id)
                ->where('student_id', $student->id)
                ->exists();
            if ($document->phase === IndividualTaWorkflow::DOCUMENT_PHASE) {
                $allowed = $allowed && $document->student_id === $student->id;
            }
        }
        if ($role === 'dosen') {
            $lecturer = CapstoneActor::lecturer($user);
            $allowed = $lecturer && Group::whereKey($document->group_id)
                ->supervisedBy($lecturer->id)
                ->exists();
        }

        abort_unless($allowed, 403);

        // Latest-only downloads: history rows are retained for audit but never served.
        $requestedVersion = $request->query('version');
        abort_if($requestedVersion !== null && (int) $requestedVersion !== (int) $document->version, 404, 'Document version not found');
        $filePath = $document->file_path;
        $originalName = $document->original_name;

        $file = $this->documentStorage->get($filePath);
        abort_unless($file, 404, 'File not found');

        $document->loadMissing('group:id,code');

        return response($file['content'], 200, [
            'Content-Type' => $file['mime_type'],
            'Content-Disposition' => 'attachment; filename="'.$this->downloadFilename($document->document_type, $originalName, $filePath, (int) $document->version, $role === 'admin' ? $document->group?->code : null).'"',
        ]);
    }

    /**
     * Build the download filename as `{slug(document_type)}_v{version}[_{group}].{ext}`,
     * e.g. `Absen_Kehadiran_Pengunjung_v3.pdf`. The group suffix is only used
     * for admin downloads to avoid collisions when many groups' files land in
     * one folder. Extension is preserved from the original upload.
     */
    private function downloadFilename(?string $type, ?string $originalName, string $filePath, int $version, ?string $groupCode = null): string
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

    /**
     * Update the specified resource in storage (Dosen review).
     */
    public function update(ReviewDocumentRequest $request, string $id)
    {
        $user = Auth::user();
        $lecturerId = CapstoneActor::lecturer($user)->id;

        try {
            $document = $this->documentStorage->reviewDocument($id, $request->validated(), $lecturerId, $user->id);
        } catch (HttpException $e) {
            // Supervisor guards bubble identical 403s through the handler.
            throw $e;
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to review document: '.$e->getMessage()], 500);
        }

        $message = $document instanceof ExpoStudentDocument
            ? 'Expo document review updated'
            : 'Document review updated';

        return response()->json(['message' => $message, 'data' => $document]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
