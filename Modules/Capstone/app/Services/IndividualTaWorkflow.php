<?php

namespace Modules\Capstone\Services;

use Modules\Capstone\Models\{Document, Group, GroupMember, PeerReview, PeerReviewIndicator, PeriodAssessmentComponent, PeriodPeerReviewIndicator, PhaseDocumentRequirement, StudentPeerReviewStatus, TaDefenseSchedule, TaRegistration, TaSubmission};
use Modules\Capstone\Repositories\AssessmentScoreRepository;

class IndividualTaWorkflow
{
    // The existing TA phase contains the pre-Expo group draft. Individual thesis
    // files use their own phase so another member's group upload cannot replace them.
    public const DOCUMENT_PHASE = 'TA_INDIVIDUAL';
    public const DOCUMENT_STATUSES = ['TA_DOCUMENTS_REQUIRED', 'TA_DOCUMENTS_UNDER_REVIEW', 'TA_DOCUMENTS_APPROVED'];

    public function forStudent(int $studentId): array
    {
        $member = GroupMember::where('student_id', $studentId)->whereHas('group', fn ($q) => $q->whereNotIn('status', ['CLOSED', 'DISSOLVED']))->first();
        if (! $member) return ['can_access'=>false, 'status'=>'TA_LOCKED', 'group'=>null, 'submission'=>null, 'documents'=>[], 'document_requirements'=>[], 'readiness'=>null];
        $group = Group::with(['title', 'supervisor1', 'supervisor2', 'supervisions.supervisor', 'members.student'])->findOrFail($member->group_id);
        $requirements = PhaseDocumentRequirement::where('period_id', $group->period_id)->whereIn('phase', ['TA', 'EXPO'])->get();
        $documents = Document::where('group_id', $group->id)->where(function ($q) use ($studentId) {
            $q->where('phase', 'EXPO')->orWhere(fn ($q) => $q->where('phase', self::DOCUMENT_PHASE)->where('student_id', $studentId));
        })->orderByDesc('version')->orderByDesc('id')->get();
        $expoDocuments = $documents->where('phase', 'EXPO')->unique('document_type');
        $expoTypes = $requirements->where('phase', 'EXPO')->where('is_required', true)->pluck('name');
        $pendingTypes = $expoTypes->reject(fn ($type) => $expoDocuments->contains(fn ($d) => $d->document_type === $type && $d->status === 'APPROVED'))->values();
        $readiness = ['expo_documents'=>['completed'=>$pendingTypes->isEmpty(), 'pending_types'=>$pendingTypes, 'total_required'=>$expoTypes->count(), 'approved_count'=>$expoTypes->count()-$pendingTypes->count()]];

        $supervisors = collect([$group->supervisor1, $group->supervisor2])->filter()->keyBy('id');
        foreach ($group->supervisions as $supervision) if ($supervision->supervisor) $supervisors->put($supervision->supervisor_id, $supervision->supervisor);
        $components = PeriodAssessmentComponent::where('period_id', $group->period_id)->whereIn('type', ['NILAI_DOSEN','MILESTONE','EXPO'])->get();
        foreach (['NILAI_DOSEN'=>'nilai_dosen','MILESTONE'=>'milestone','EXPO'=>'expo_evaluation'] as $type=>$key) {
            $ids = $components->where('type', $type)->pluck('id');
            $scores = $ids->isEmpty() ? collect() : AssessmentScoreRepository::forType($type)->where('group_id', $group->id)->whereIn('period_component_id', $ids)
                ->whereIn('evaluator_id', $supervisors->keys())->whereNotNull('score')
                ->where(fn ($q) => $q->where('student_id', $studentId)->orWhereNull('student_id'))->get(['evaluator_id','period_component_id']);
            $evaluators = $supervisors->map(function ($supervisor) use ($scores, $ids, $group) {
                $submitted = $scores->where('evaluator_id', $supervisor->id)->pluck('period_component_id')->unique()->count();
                return ['id'=>$supervisor->id, 'name'=>$supervisor->name, 'role'=>$group->supervisor_2_id === $supervisor->id ? 'SUPERVISOR_2' : 'SUPERVISOR_1', 'submitted_components'=>$submitted, 'total_components'=>$ids->count(), 'status'=>$ids->isNotEmpty() && $submitted === $ids->count() ? 'completed' : 'pending'];
            })->values();
            $readiness[$key] = ['required'=>true, 'configured'=>$ids->isNotEmpty(), 'component_count'=>$ids->count(), 'supervisors'=>$evaluators, 'completed'=>$ids->isNotEmpty() && $evaluators->isNotEmpty() && $evaluators->every(fn ($e) => $e['status'] === 'completed')];
        }

        $periodIds = PeriodPeerReviewIndicator::where('period_id', $group->period_id)->pluck('id');
        $indicatorKey = $periodIds->isNotEmpty() ? 'period_indicator_id' : 'indicator_id';
        $indicatorIds = $periodIds->isNotEmpty() ? $periodIds : PeerReviewIndicator::where('period_id', $group->period_id)->pluck('id');
        $memberIds = $group->members->pluck('student_id');
        $reviews = PeerReview::where('group_id', $group->id)->where('is_final_submission', true)->whereIn($indicatorKey, $indicatorIds)->get(['reviewer_id','reviewee_id',$indicatorKey]);
        $incomplete = $group->members->filter(function ($member) use ($reviews, $memberIds, $indicatorIds, $indicatorKey) {
            if ($indicatorIds->isEmpty()) return true;
            foreach ($memberIds as $reviewee) {
                if ($reviewee === $member->student_id) continue;
                foreach ($indicatorIds as $indicatorId) if (! $reviews->contains(fn ($r) => $r->reviewer_id === $member->student_id && $r->reviewee_id === $reviewee && $r->{$indicatorKey} === $indicatorId)) return true;
            }
            return false;
        })->map(fn ($m) => ['student_id'=>$m->student_id,'student_name'=>$m->student->name,'student_nim'=>$m->student->nim])->values();
        $readiness['peer_review'] = ['configured'=>$indicatorIds->isNotEmpty(),'completed'=>$indicatorIds->isNotEmpty() && $incomplete->isEmpty(),'indicator_count'=>$indicatorIds->count(),'total_members'=>$memberIds->count(),'completed_members'=>$memberIds->count()-$incomplete->count(),'incomplete_students'=>$incomplete];
        $readiness['expo_completed'] = in_array($group->status, ['EXPO_DONE','READY_FOR_TA_INDIVIDUAL','TA_IN_PROGRESS','PDC2_COMPLETED'], true);
        $readiness['ready'] = $readiness['expo_completed']
            && collect(['expo_documents','nilai_dosen','milestone','expo_evaluation','peer_review'])->every(fn ($key) => $readiness[$key]['completed']);
        $override = StudentPeerReviewStatus::where('student_id', $studentId)->where('group_id', $group->id)->where('period_id', $group->period_id)->whereIn('ta_status', ['TA_ACTIVE','TA_DONE'])->exists();
        $canAccess = $readiness['ready'] || $override;
        $submission = TaSubmission::with('reviewer')->where('student_id', $studentId)->where('group_id', $group->id)->latest('id')->first();
        $taRequirements = $requirements->where('phase', 'TA')->values();
        $taDocuments = $documents->where('phase', self::DOCUMENT_PHASE)->unique('document_type')->values();
        $required = $taRequirements->where('is_required', true);
        $approved = $required->isNotEmpty() && $required->every(fn ($r) => $taDocuments->contains(fn ($d) => $d->document_type === $r->name && $d->status === 'APPROVED'));
        $status = $approved ? 'TA_DOCUMENTS_APPROVED' : ($taDocuments->contains('status', 'SUBMITTED') ? 'TA_DOCUMENTS_UNDER_REVIEW' : 'TA_DOCUMENTS_REQUIRED');
        if ($submission && in_array($submission->status, ['TA_READY_FOR_SIDANG','TA_REGISTERED','TA_SCHEDULED','TA_DEFENDED','TA_REVISED'], true)) $status = $submission->status;
        $schedule = TaDefenseSchedule::where('group_id', $group->id)->whereIn('status', ['SCHEDULED','DONE'])->where(fn ($q) => $q->where('student_id', $studentId)->orWhereHas('students', fn ($q) => $q->where('students.id', $studentId)))->latest('date')->first();
        if ($schedule) $status = $schedule->status === 'DONE' ? 'TA_DEFENDED' : 'TA_READY_FOR_SIDANG';
        $registration = TaRegistration::where('student_id', $studentId)->where('group_id', $group->id)->latest('id')->first();
        $sidangApproved = $registration && $registration->isApproved();
        if (! $canAccess) {
            $status = 'TA_LOCKED';
        } elseif (! $sidangApproved && ! in_array($status, ['TA_READY_FOR_SIDANG','TA_REGISTERED','TA_SCHEDULED','TA_DEFENDED','TA_REVISED'], true)) {
            $status = 'TA_AWAITING_APPROVAL';
        }
        $editable = $canAccess && $sidangApproved && in_array($status, ['TA_DOCUMENTS_REQUIRED','TA_DOCUMENTS_UNDER_REVIEW'], true);
        return ['can_access'=>$canAccess, 'status'=>$status, 'submission'=>$submission, 'group'=>$group, 'documents'=>$taDocuments,
            'document_requirements'=>$taRequirements, 'readiness'=>$readiness, 'can_upload'=>$editable,
            'registration'=>$registration, 'sidang_approved'=>$sidangApproved];
    }

    public function syncSubmission(int $studentId): void
    {
        $state = $this->forStudent($studentId);
        if ($state['submission'] && in_array($state['status'], self::DOCUMENT_STATUSES, true)) {
            $state['submission']->update(['status'=>$state['status']]);
        }
    }
}
