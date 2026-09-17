<?php

namespace Modules\Capstone\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Capstone\Models\{Document, Group, TaSubmission};
use Modules\Capstone\Services\{DocumentStorageService, IndividualTaWorkflow};
use Modules\Capstone\Support\CapstoneActor;

class IndividualTaController extends Controller
{
    public function status(Request $request, IndividualTaWorkflow $workflow)
    {
        return response()->json($workflow->forStudent(CapstoneActor::student($request->user())->id));
    }

    public function upload(Request $request, IndividualTaWorkflow $workflow, DocumentStorageService $storage)
    {
        $data = $request->validate(['document_type'=>'required|string|max:255','file'=>'required|file|mimes:pdf,doc,docx|max:10240']);
        $studentId = CapstoneActor::student($request->user())->id;
        $initial = $workflow->forStudent($studentId);
        abort_unless($initial['can_access'], 403, 'Complete the TA prerequisites before uploading.');
        return DB::transaction(function () use ($request, $workflow, $storage, $data, $studentId, $initial) {
            Group::whereKey($initial['group']->id)->lockForUpdate()->firstOrFail();
            $state = $workflow->forStudent($studentId);
            abort_unless($state['can_upload'] ?? false, 403, 'TA document uploads are locked.');
            abort_unless($state['document_requirements']->contains('name', $data['document_type']), 422, 'Select a configured TA document type.');
            $document = Document::where('group_id', $state['group']->id)->where('student_id', $studentId)->where('phase', IndividualTaWorkflow::DOCUMENT_PHASE)->where('document_type', $data['document_type'])->lockForUpdate()->latest('id')->first();
            abort_if($document?->status === 'APPROVED', 403, 'Approved TA documents cannot be replaced.');
            $path = $storage->store($request->file('file'), 'ta-individual', $state['group']->id.'/'.$studentId);
            try {
                $document ??= new Document(['group_id'=>$state['group']->id,'student_id'=>$studentId,'phase'=>IndividualTaWorkflow::DOCUMENT_PHASE,'document_type'=>$data['document_type'],'version'=>1]);
                $document->fill(['file_path'=>$path,'status'=>'SUBMITTED','feedback'=>null,'reviewed_by'=>null])->save();
                TaSubmission::firstOrCreate(['group_id'=>$state['group']->id,'student_id'=>$studentId], ['status'=>'TA_DOCUMENTS_UNDER_REVIEW','period_id'=>$state['group']->period_id]);
                $workflow->syncSubmission($studentId);
            } catch (\Throwable $e) { $storage->delete($path); throw $e; }
            return response()->json(['message'=>'Document uploaded successfully','data'=>$document], 201);
        });
    }
}
