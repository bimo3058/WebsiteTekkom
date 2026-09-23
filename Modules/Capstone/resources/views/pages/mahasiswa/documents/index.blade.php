@extends('capstone::layouts.app')
@section('title','Documents & Workflow')
@section('content')
<div x-data="capstoneDocuments" class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold tracking-tight">Documents &amp; Workflow</h1>
        <p class="text-muted-foreground">Complete each phase sequentially to progress toward graduation.</p>
    </div>

    @include('capstone::partials.loading')

    <div x-show="!loading && !error" x-cloak class="space-y-6">
        <div x-show="workflow.current_phase==='SEMPRO' && !workflow.seminar_schedule?.exists" class="rounded-lg border border-amber-500 bg-amber-50 p-4 flex items-start gap-3">
            <x-capstone::icon name="AlertTriangle" class="h-4 w-4 text-amber-600 mt-0.5" />
            <div>
                <h2 class="font-medium text-amber-800">⏳ Menunggu Jadwal SEMPRO</h2>
                <p class="text-sm text-amber-700">SEMPRO belum dijadwalkan oleh admin. Anda tidak dapat mengupload dokumen bukti SEMPRO hingga jadwal ditetapkan.</p>
            </div>
        </div>

        <div x-show="workflow.is_graduated" class="p-4 rounded-lg bg-green-500/10 border border-green-500/30 text-center">
            <h2 class="text-xl font-bold text-green-600">🎓 Congratulations! All phases completed.</h2>
        </div>

        {{-- Workflow Stepper Timeline (Single Row with Connecting Lines) --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm overflow-hidden">
            <div class="overflow-x-auto pb-2 -mb-2">
                <div class="flex items-start justify-between min-w-[720px] w-full relative">
                    <template x-for="(phase, index) in (workflow.phases || [])" :key="phase.phase">
                        <div class="flex-1 relative flex flex-col items-center px-2" :title="label(phase.phase)+': '+(phase.locked_reason || phase.status)">

                            {{-- Connecting Line (drawn from previous step to current step) --}}
                            <div x-show="index > 0"
                                 class="absolute top-5 -left-1/2 w-full h-[2px] -translate-y-1/2 z-0"
                                 :class="(workflow.phases[index-1]?.status === 'completed') ? 'bg-emerald-500' : 'bg-gray-200'">
                            </div>

                            {{-- Step Circle / Node --}}
                            <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 transition-all duration-200 z-10 relative shadow-sm"
                                 :class="[
                                     phase.status==='completed' ? 'bg-emerald-500 border-emerald-500 text-white shadow-emerald-200' :
                                     phase.phase===workflow.current_phase ? 'bg-white border-primary-600 text-primary-600 ring-4 ring-primary-100 font-bold' :
                                     phase.status==='locked' ? 'bg-gray-100 border-gray-200 text-gray-400' :
                                     'bg-white border-gray-300 text-gray-500'
                                 ]">
                                <x-capstone::icon name="Check" class="h-5 w-5 text-white" x-show="phase.status==='completed'" />
                                <x-capstone::icon name="Lock" class="h-4 w-4 text-gray-400" x-show="phase.status==='locked'" />
                                <span class="text-xs font-bold" x-show="!['completed','locked'].includes(phase.status)" x-text="index + 1"></span>
                            </div>

                            {{-- Phase Label --}}
                            <div class="mt-2.5 text-center flex flex-col items-center">
                                <span class="text-xs font-semibold leading-tight text-center max-w-[130px] line-clamp-2"
                                      :class="phase.status==='completed' ? 'text-emerald-700' : phase.phase===workflow.current_phase ? 'text-primary-700 font-bold' : phase.status==='locked' ? 'text-gray-400' : 'text-gray-700'"
                                      x-text="label(phase.phase)">
                                </span>

                                {{-- Phase Status Badge --}}
                                <div class="mt-1">
                                    <span x-show="phase.status==='completed'" class="inline-flex items-center text-[10px] font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded-full">
                                        Selesai
                                    </span>
                                    <span x-show="phase.phase===workflow.current_phase && phase.status!=='completed'" class="inline-flex items-center text-[10px] font-medium text-primary-700 bg-primary-50 border border-primary-200 px-1.5 py-0.5 rounded-full">
                                        Aktif
                                    </span>
                                    <span x-show="phase.status==='locked'" class="inline-flex items-center text-[10px] font-medium text-gray-400 bg-gray-50 border border-gray-200 px-1.5 py-0.5 rounded-full">
                                        Terkunci
                                    </span>
                                </div>
                            </div>

                            {{-- Documents Under Phase --}}
                            <div class="flex flex-col items-center gap-1.5 w-full mt-3">
                                <template x-for="doc in (phase.documents || [])" :key="doc.type">
                                    <div class="flex flex-col items-center gap-1 w-full max-w-[150px] p-2 rounded-lg bg-gray-50 border border-gray-100 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <span class="text-[11px] font-medium text-gray-800 truncate" x-text="doc.type"></span>
                                        </div>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded font-medium"
                                              :class="doc.status==='APPROVED' ? 'bg-emerald-100 text-emerald-700' : doc.status==='SUBMITTED' ? 'bg-blue-100 text-blue-700' : doc.status==='REJECTED' ? 'bg-red-100 text-red-700' : 'bg-gray-200 text-gray-600'"
                                              x-text="doc.status==='missing' ? (phase.status==='locked' ? 'Locked' : 'Belum Ada') : doc.status">
                                        </span>
                                        <x-capstone::button size="sm" variant="outline" class="h-6 text-[10px] px-2 w-full mt-0.5 justify-center shadow-none hover:bg-white" x-show="doc.can_upload" ::disabled="!doc.can_upload" @click="openUpload(phase,doc)">
                                            <x-capstone::icon name="Upload" class="h-3 w-3 mr-1" />
                                            <span x-text="doc.status==='missing' ? 'Upload' : 'Re-upload'"></span>
                                        </x-capstone::button>
                                        <span class="text-[10px] text-amber-600 leading-tight" x-show="phase.phase==='SEMPRO' && !workflow.seminar_schedule?.exists && phase.status!=='completed'">Menunggu jadwal</span>
                                    </div>
                                </template>
                            </div>

                        </div>
                    </template>
                </div>
            </div>
        </div>

        <x-capstone::data-table title="Submitted Documents" search-placeholder="Search documents..." empty-title="No documents uploaded yet." empty-description="Start by uploading your PDC 1 document above." :columns="['No','phase'=>'Phase','document_type'=>'Document Type','version'=>'Version','status'=>'Status','Uploaded By','created_at'=>'Uploaded At','Feedback','Aksi']">
            <template x-for="(doc,index) in visible" :key="doc.id"><tr class="border-b hover:bg-muted/50"><td class="p-2" x-text="(page-1)*pageSize+index+1"></td><td class="p-2" x-text="label(doc.phase)"></td><td class="p-2" x-text="doc.document_type==='GENERAL' ? 'General' : doc.document_type"></td><td class="p-2 text-muted-foreground" x-text="'v'+doc.version"></td><td class="p-2"><span class="rounded-md px-2 py-0.5 text-xs capitalize" :class="color(doc.status)" x-text="doc.status.toLowerCase()"></span></td><td class="p-2 text-muted-foreground" x-text="doc.student?.name || 'Unknown'"></td><td class="p-2 whitespace-nowrap text-muted-foreground" x-text="date(doc.created_at,true)"></td><td class="p-2"><span class="max-w-[200px] block truncate text-muted-foreground" :title="doc.feedback" x-text="doc.feedback || '-'"></span></td><td class="p-2"><x-capstone::button variant="ghost" size="sm" @click="downloadDocument(doc)" aria-label="Download document"><x-capstone::icon name="Download" /></x-capstone::button></td></tr></template>
        </x-capstone::data-table>
    </div>

    <x-capstone::dialog id="document-upload" title="Upload Document" class="sm:max-w-[425px]">
        <p class="text-sm text-muted-foreground mt-2">Upload your <strong x-text="label(uploadPhase?.phase)"></strong> document (PDF/DOCX, max 10MB).</p>
        <form id="document-upload-form" @submit.prevent="upload"><div class="grid gap-4 py-4"><div x-show="uploadType?.type!=='GENERAL'" class="grid gap-2"><label class="text-sm font-medium" for="upload-document-type">Document Type</label><x-capstone::input id="upload-document-type" ::value="uploadType?.type" disabled /></div><div class="grid gap-2"><label for="document-file" class="text-sm font-medium">File</label><x-capstone::input id="document-file" type="file" @change="file=$event.target.files[0]" accept=".pdf,.doc,.docx" required /><p class="text-sm text-destructive" x-text="errors.file?.[0]"></p></div></div><div class="flex justify-end"><x-capstone::button type="submit" ::disabled="saving || !uploadType?.can_upload"><span x-text="saving ? 'Uploading...' : 'Upload'"></span></x-capstone::button></div></form>
    </x-capstone::dialog>
</div>
@endsection