<?php

namespace Modules\Capstone\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Capstone\Models\{ExpoEvent, ExpoRegistration, ExpoSelfEvaluation, ExpoStudentDocument, GroupMember, PeriodAssessmentComponent};
use Modules\Capstone\Services\DocumentStorageService;
use Modules\Capstone\Support\CapstoneActor;

class ExpoStudentController extends Controller
{
    private function registration(Request $request, ExpoEvent $expoEvent): ExpoRegistration
    {
        $studentId = CapstoneActor::student($request->user())->id;
        return ExpoRegistration::where('expo_event_id', $expoEvent->id)->where('status', 'REGISTERED')
            ->whereHas('group.members', fn ($q) => $q->where('student_id', $studentId))->firstOrFail();
    }

    public function detail(Request $request, ExpoEvent $expoEvent)
    {
        $registration = $this->registration($request, $expoEvent);
        $studentId = CapstoneActor::student($request->user())->id;
        $group = $registration->group()->with('members.student')->firstOrFail();
        $components = PeriodAssessmentComponent::with('template')->where('period_id', $expoEvent->period_id)->where('type', 'EXPO')->orderBy('sort_order')->get();
        $scores = ExpoSelfEvaluation::where('expo_registration_id', $registration->id)->get();
        $documents = ExpoStudentDocument::where('expo_registration_id', $registration->id)->get()->keyBy('student_id');
        $mine = $scores->where('student_id', $studentId)->keyBy('period_component_id');
        return response()->json([
            'expo_event'=>$expoEvent, 'registration'=>$registration, 'group'=>['id'=>$group->id,'code'=>$group->code],
            'members'=>$group->members->map(fn ($m) => [
                'id'=>$m->student_id, 'name'=>$m->student->name, 'nim'=>$m->student->nim, 'is_leader'=>$m->is_leader,
                'has_submitted_evaluation'=>$scores->contains('student_id',$m->student_id),
                'has_uploaded_document'=>$documents->has($m->student_id), 'document_status'=>$documents->get($m->student_id)?->status,
            ]),
            'components'=>$components->map(fn ($c) => $c->full_component),
            'my_scores'=>$components->map(fn ($c) => ['period_component_id'=>$c->id,'code'=>$c->code,'name'=>$c->name,'weight'=>$c->weight,'score'=>$mine->get($c->id)?->score,'notes'=>$mine->get($c->id)?->notes]),
            'my_document'=>$documents->get($studentId)?->only(['id','original_name','status']),
        ]);
    }

    public function evaluate(Request $request, ExpoEvent $expoEvent)
    {
        $registration = $this->registration($request, $expoEvent);
        $studentId = CapstoneActor::student($request->user())->id;
        $data = $request->validate(['scores'=>'required|array|min:1','scores.*.period_component_id'=>'required|integer|distinct','scores.*.score'=>'required|numeric|min:1|max:100','scores.*.notes'=>'nullable|string|max:5000']);
        return DB::transaction(function () use ($registration, $studentId, $expoEvent, $data) {
            ExpoRegistration::whereKey($registration->id)->where('status','REGISTERED')->lockForUpdate()->firstOrFail();
            if (ExpoSelfEvaluation::where('expo_registration_id',$registration->id)->where('student_id',$studentId)->exists()) return response()->json(['message'=>'Self-evaluation sudah dikirim dan tidak dapat diubah.'],403);
            $expected = PeriodAssessmentComponent::where('period_id',$expoEvent->period_id)->where('type','EXPO')->pluck('id')->sort()->values()->all();
            $actual = collect($data['scores'])->pluck('period_component_id')->map(fn($id)=>(int)$id)->sort()->values()->all();
            if (empty($expected) || $expected !== $actual) return response()->json(['message'=>'Lengkapi seluruh komponen EXPO pada periode Anda.'],422);
            foreach ($data['scores'] as $score) ExpoSelfEvaluation::create([
                'expo_registration_id'=>$registration->id,'group_id'=>$registration->group_id,'student_id'=>$studentId,
                'period_component_id'=>$score['period_component_id'],
                'score'=>$score['score'],'notes'=>$score['notes'] ?? null,
            ]);
            return response()->json(['message'=>'Self-evaluation berhasil disimpan.'],201);
        });
    }

    public function document(Request $request, ExpoEvent $expoEvent, DocumentStorageService $storage)
    {
        $registration = $this->registration($request, $expoEvent);
        $studentId = CapstoneActor::student($request->user())->id;
        $request->validate(['file'=>'required|file|mimes:pdf,doc,docx,ppt,pptx|max:10240']);
        return DB::transaction(function () use ($request,$registration,$studentId,$storage) {
            ExpoRegistration::whereKey($registration->id)->where('status','REGISTERED')->lockForUpdate()->firstOrFail();
            if (ExpoStudentDocument::where('expo_registration_id',$registration->id)->where('student_id',$studentId)->exists()) return response()->json(['message'=>'Dokumen EXPO sudah diunggah.'],403);
            $path=$storage->store($request->file('file'),'expo',$registration->id.'/'.$studentId);
            try {
                $document=ExpoStudentDocument::create(['expo_registration_id'=>$registration->id,'group_id'=>$registration->group_id,'student_id'=>$studentId,'file_path'=>$path,'storage_location'=>'supabase','original_name'=>$request->file('file')->getClientOriginalName(),'status'=>'SUBMITTED']);
            } catch (\Throwable $e) { $storage->delete($path); throw $e; }
            return response()->json(['message'=>'Dokumen berhasil diupload.','data'=>$document->only(['id','original_name','status'])],201);
        });
    }
}
