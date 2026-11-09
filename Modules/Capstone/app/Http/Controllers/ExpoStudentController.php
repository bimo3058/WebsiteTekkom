<?php

namespace Modules\Capstone\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Capstone\Models\ExpoEvent;
use Modules\Capstone\Models\ExpoRegistration;
use Modules\Capstone\Models\ExpoSelfEvaluation;
use Modules\Capstone\Models\ExpoStudentDocument;
use Modules\Capstone\Models\PeriodAssessmentComponent;
use Modules\Capstone\Services\DocumentStorageService;
use Modules\Capstone\Services\GroupLifecycleService;
use Modules\Capstone\Support\CapstoneActor;

class ExpoStudentController extends Controller
{
    private function registration(Request $request, ExpoEvent $expoEvent): ExpoRegistration
    {
        $studentId = CapstoneActor::student($request->user())->id;

        return ExpoRegistration::where('expo_event_id', $expoEvent->id)->where('status', 'REGISTERED')
            ->whereHas('group.members', fn ($q) => $q->where('student_id', $studentId))->firstOrFail();
    }

    /**
     * Group-level EXPO detail. One representative member submits the
     * self-evaluation and the supporting document for the whole group;
     * the supervisor approves/rejects the submission once per group.
     */
    public function detail(Request $request, ExpoEvent $expoEvent)
    {
        $registration = $this->registration($request, $expoEvent);
        $studentId = CapstoneActor::student($request->user())->id;
        $group = $registration->group()->with('members.student')->firstOrFail();
        // Cross-period (global) events have no own period: each group is
        // graded on its own period's EXPO components.
        $componentPeriodId = $expoEvent->period_id ?? $group->period_id;
        $components = PeriodAssessmentComponent::with('template')->where('period_id', $componentPeriodId)->where('type', 'EXPO')->orderBy('sort_order')->get();
        $scores = ExpoSelfEvaluation::where('expo_registration_id', $registration->id)->get();
        $documents = ExpoStudentDocument::where('expo_registration_id', $registration->id)->get()->keyBy('student_id');
        $groupScores = $scores->keyBy('period_component_id');
        $submitterId = $scores->first()?->student_id;
        $submitter = $submitterId ? $group->members->firstWhere('student_id', $submitterId)?->student : null;
        $document = $documents->first();

        $groupScoresPayload = $components->map(fn ($c) => [
            'period_component_id' => $c->id, 'code' => $c->code, 'name' => $c->name, 'weight' => $c->weight,
            'score' => $groupScores->get($c->id)?->score, 'notes' => $groupScores->get($c->id)?->notes,
        ]);
        $documentPayload = $document ? [
            'id' => $document->id, 'original_name' => $document->original_name, 'status' => $document->status,
            'feedback' => $document->feedback, 'uploaded_by' => $document->student ? ['id' => $document->student_id, 'name' => $document->student->name] : null,
            'created_at' => $document->created_at,
        ] : null;

        return response()->json([
            'expo_event' => $expoEvent, 'registration' => $registration, 'group' => ['id' => $group->id, 'code' => $group->code],
            'members' => $group->members->map(fn ($m) => [
                'id' => $m->student_id, 'name' => $m->student->name, 'nim' => $m->student->nim, 'is_leader' => $m->is_leader,
                'is_representative' => $submitterId === $m->student_id,
                'has_submitted_evaluation' => $scores->contains('student_id', $m->student_id),
                'has_uploaded_document' => $documents->has($m->student_id), 'document_status' => $documents->get($m->student_id)?->status,
            ]),
            'components' => $components->map(fn ($c) => $c->full_component),
            'group_evaluation' => [
                'submitted' => $scores->isNotEmpty(),
                'submitted_by' => $submitter ? ['id' => $submitter->id, 'name' => $submitter->name, 'nim' => $submitter->nim] : null,
                'scores' => $groupScoresPayload,
                'review_status' => $registration->review_status,
                'review_feedback' => $registration->review_feedback,
                'reviewed_at' => $registration->reviewed_at,
            ],
            'document' => $documentPayload,
            'can_submit' => $registration->allowsSubmission(),
            'can_upload_document' => $registration->allowsSubmission()
                && (! $document
                    || $registration->review_status === 'REJECTED'
                    || in_array($document->status, ['REVISE', 'REJECTED'], true)),
            // Backward-compatible aliases (legacy per-member keys) so the
            // Next.js client keeps rendering the group's submission.
            'my_scores' => $groupScoresPayload,
            'my_document' => $document?->only(['id', 'original_name', 'status', 'feedback']),
        ]);
    }

    /**
     * Submit the group self-evaluation (representative member). Immutable
     * while under supervisor review; reopenable after a rejection.
     */
    public function evaluate(Request $request, ExpoEvent $expoEvent)
    {
        $registration = $this->registration($request, $expoEvent);
        $studentId = CapstoneActor::student($request->user())->id;
        $data = $request->validate(['scores' => 'required|array|min:1', 'scores.*.period_component_id' => 'required|integer|distinct', 'scores.*.score' => 'required|numeric|min:1|max:100', 'scores.*.notes' => 'nullable|string|max:5000']);
        $response = DB::transaction(function () use ($registration, $studentId, $expoEvent, $data) {
            $registration = ExpoRegistration::whereKey($registration->id)->where('status', 'REGISTERED')->lockForUpdate()->firstOrFail();
            if (! $registration->allowsSubmission()) {
                $message = $registration->review_status === 'APPROVED'
                    ? 'Self-evaluation kelompok sudah disetujui dosbing dan tidak dapat diubah.'
                    : 'Self-evaluation kelompok sudah dikirim dan sedang direview dosbing.';
                return response()->json(['message' => $message], 403);
            }
            $expected = PeriodAssessmentComponent::where('period_id', $expoEvent->period_id ?? $registration->group->period_id)->where('type', 'EXPO')->pluck('id')->sort()->values()->all();
            $actual = collect($data['scores'])->pluck('period_component_id')->map(fn ($id) => (int) $id)->sort()->values()->all();
            if (empty($expected) || $expected !== $actual) {
                return response()->json(['message' => 'Lengkapi seluruh komponen EXPO pada periode Anda.'], 422);
            }
            // A rejected submission is reopened: the group's previous scores
            // are dropped so the representative can re-score from scratch.
            ExpoSelfEvaluation::where('expo_registration_id', $registration->id)->delete();
            foreach ($data['scores'] as $score) {
                ExpoSelfEvaluation::create([
                    'expo_registration_id' => $registration->id, 'group_id' => $registration->group_id, 'student_id' => $studentId,
                    'period_component_id' => $score['period_component_id'],
                    'score' => $score['score'], 'notes' => $score['notes'] ?? null,
                ]);
            }
            $registration->update(['review_status' => 'SUBMITTED', 'review_feedback' => null]);

            return response()->json(['message' => 'Self-evaluation kelompok berhasil disimpan.'], 201);
        });
        $this->maybeAdvanceGroup($registration, $response);

        return $response;
    }

    /**
     * Upload the group's EXPO document (representative member). One document
     * per registration; replaceable after a REVISE/REJECTED review or a
     * rejected supervisor submission.
     */
    public function document(Request $request, ExpoEvent $expoEvent, DocumentStorageService $storage)
    {
        $registration = $this->registration($request, $expoEvent);
        $studentId = CapstoneActor::student($request->user())->id;
        $request->validate(['file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:10240']);
        $response = DB::transaction(function () use ($request, $registration, $studentId, $storage) {
            $registration = ExpoRegistration::whereKey($registration->id)->where('status', 'REGISTERED')->lockForUpdate()->firstOrFail();
            if ($registration->review_status === 'APPROVED') {
                return response()->json(['message' => 'Dokumen EXPO sudah disetujui dosbing dan tidak dapat diganti.'], 403);
            }
            $existing = ExpoStudentDocument::where('expo_registration_id', $registration->id)->first();
            $replaceable = $registration->review_status === 'REJECTED'
                || ($existing && in_array($existing->status, ['REVISE', 'REJECTED'], true));
            if ($existing && ! $replaceable) {
                return response()->json(['message' => 'Dokumen EXPO sudah diunggah.'], 403);
            }
            $path = $storage->store($request->file('file'), 'expo', $registration->id.'/group');
            try {
                // A rejected/revise document is replaced: the old file and row
                // are removed so the group starts fresh (the supervisor
                // reviews the new upload again).
                if ($existing) {
                    $storage->delete($existing->file_path);
                    $existing->delete();
                }
                $document = ExpoStudentDocument::create(['expo_registration_id' => $registration->id, 'group_id' => $registration->group_id, 'student_id' => $studentId, 'file_path' => $path, 'storage_location' => 'supabase', 'original_name' => $request->file('file')->getClientOriginalName(), 'status' => 'SUBMITTED']);
                if ($registration->review_status !== 'SUBMITTED') {
                    $registration->update(['review_status' => 'SUBMITTED', 'review_feedback' => null]);
                }
            } catch (\Throwable $e) {
                $storage->delete($path);
                throw $e;
            }

            return response()->json(['message' => 'Dokumen berhasil diupload.', 'data' => $document->only(['id', 'original_name', 'status'])], 201);
        });
        $this->maybeAdvanceGroup($registration, $response);

        return $response;
    }

    /**
     * Offer the lifecycle a chance to advance EXPO_REGISTERED → EXPO_DONE
     * once the supervisor approves and the stage is complete. Only on
     * successful submits; never throws (advanceIfComplete degrades to null
     * when nothing applies).
     */
    private function maybeAdvanceGroup(ExpoRegistration $registration, $response): void
    {
        if ($response->getStatusCode() !== 201) {
            return;
        }

        $group = $registration->group()->first();

        if ($group) {
            app(GroupLifecycleService::class)->advanceIfComplete($group);
        }
    }
}
