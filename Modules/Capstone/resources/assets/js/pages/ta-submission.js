import {api,upload,unwrap,rows,notify,date} from '../api.js';
import {gradePalette} from './grades.js';

const statuses={
    TA_LOCKED:['Locked','bg-gray-100 text-gray-800','Lock','TA phase is locked. Complete EXPO first.'],
    TA_AWAITING_APPROVAL:['Awaiting Approval','bg-amber-100 text-amber-800','Clock','Register for sidang TA and wait for admin approval before uploading documents.'],
    TA_DOCUMENTS_REQUIRED:['Documents Required','bg-blue-100 text-blue-800','FileText','Upload all required TA documents to proceed.'],
    TA_DOCUMENTS_UNDER_REVIEW:['Under Review','bg-yellow-100 text-yellow-800','Clock','Documents submitted. Waiting for supervisor approval.'],
    TA_DOCUMENTS_APPROVED:['Documents Approved','bg-green-100 text-green-800','CheckCircle','All documents approved! Waiting for sidang schedule.'],
    TA_DRAFT:['Draft Uploaded','bg-primary-100 text-primary-500','FileText','TA draft uploaded and under review.'],
    TA_REVISED:['Revision Submitted','bg-orange-100 text-orange-800','FileText','Revision submitted. Waiting for review.'],
    TA_READY:['Ready for Defense','bg-emerald-100 text-emerald-800','CheckCircle','TA approved! You can now register for defense.'],
    TA_READY_FOR_SIDANG:['Ready for Sidang','bg-indigo-100 text-indigo-800','Calendar','Sidang scheduled. Prepare for your defense.'],
    TA_REGISTERED:['Registered','bg-cyan-100 text-cyan-800','FileCheck','Registered for TA defense.'],
    TA_SCHEDULED:['Scheduled','bg-primary-100 text-primary-500','Calendar','TA defense has been scheduled.'],
    TA_DEFENDED:['Completed','bg-green-100 text-green-800','CheckCircle','Congratulations! TA defense completed.'],
};
export function registerTaSubmission(Alpine){
    Alpine.data('capstoneTaSubmission',()=>({
        loading:true,error:'',data:null,schedule:null,grades:null,uploading:false,registering:false,selectedType:'',file:null,progress:0,date,palette:gradePalette,
        get locked(){return !this.data?.can_access || this.data.status==='TA_LOCKED';},
        get currentStatus(){return this.data?.status || 'TA_LOCKED';},
        get config(){const [label,color,icon,description]=statuses[this.currentStatus] || statuses.TA_LOCKED;return {label,color,icon,description};},
        get currentStep(){return ({TA_AWAITING_APPROVAL:0,TA_DOCUMENTS_REQUIRED:0,TA_DOCUMENTS_UNDER_REVIEW:1,TA_DOCUMENTS_APPROVED:2,TA_READY_FOR_SIDANG:3,TA_REGISTERED:3,TA_SCHEDULED:4,TA_DEFENDED:4})[this.currentStatus] || 0;},
        get registration(){return this.data?.registration || null;},
        get sidangApproved(){return !!this.data?.sidang_approved;},
        get isPending(){return this.registration?.status==='PENDING';},
        get isRejected(){return this.registration?.status==='REJECTED';},
        get canRegister(){return !this.locked && this.currentStatus==='TA_AWAITING_APPROVAL' && !this.isPending;},
        get requirements(){return (this.data?.document_requirements || []).filter(r=>r.is_required);},
        get approvedCount(){return this.requirements.filter(r=>this.documentFor(r.name)?.status==='APPROVED').length;},
        get showDocuments(){return ['TA_DOCUMENTS_REQUIRED','TA_DOCUMENTS_UNDER_REVIEW','TA_DOCUMENTS_APPROVED'].includes(this.currentStatus);},
        get showSchedule(){return ['TA_READY_FOR_SIDANG','TA_REGISTERED','TA_SCHEDULED','TA_DEFENDED','TA_REVISED'].includes(this.currentStatus);},
        get completed(){return ['TA_DEFENDED','TA_REVISED'].includes(this.currentStatus);},
        get readiness(){return this.data?.readiness;},
        get gradeComponents(){return [['SIDANG_TA','Nilai Penguji (SIDANG_TA)','Rata-rata Penguji','emerald'],['BIMBINGAN_TA','Nilai Pembimbing (BIMBINGAN_TA)','Rata-rata Pembimbing','sky']].filter(([key])=>this.grades?.components?.[key]).map(([key,label,average,color])=>({key,label,average,color,...this.grades.components[key]}));},
        roleLabel:role=>({EXAMINER_1:'Penguji 1',EXAMINER_2:'Penguji 2',EXAMINER:'Penguji',SUPERVISOR_1:'Pembimbing 1',SUPERVISOR_2:'Pembimbing 2',UNKNOWN:'Evaluator'})[role] || role,
        documentFor(type){return this.data?.documents?.find(d=>d.document_type===type);},
        documentStatus(type){const status=this.documentFor(type)?.status;return status==='SUBMITTED'?'PENDING':status;},
        score:value=>value==null?'-':Number(value).toFixed(1),
        async init(){await this.load();},
        async load(){this.loading=true;this.error='';try{this.data=unwrap(await api('/mahasiswa/ta-detailed-status'));this.schedule=null;this.grades=null;if(!this.locked&&this.showSchedule){const requests=[api('/mahasiswa/ta-defense-schedules/my-schedule')];if(this.completed)requests.push(api('/mahasiswa/my-grades'));const [schedules,grades]=await Promise.all(requests);this.schedule=rows(schedules).find(s=>['SCHEDULED','DONE'].includes(s.status)) || null;this.grades=grades?unwrap(grades)?.grades?.ta:null;}}catch(e){this.error=e.message;}finally{this.loading=false;}},
        async register(){if(!this.canRegister||this.registering)return;this.registering=true;try{await api('/mahasiswa/ta-registrations',{method:'POST',body:{}});notify('Sidang TA registration sent. Waiting for admin approval.');await this.load();}catch(e){notify(e.message,true);}finally{this.registering=false;}},
        async cancelRegistration(){if(!this.isPending&&!this.isRejected||this.registering)return;this.registering=true;try{await api('/mahasiswa/ta-registrations',{method:'DELETE'});notify('Sidang TA request cancelled.');await this.load();}catch(e){notify(e.message,true);}finally{this.registering=false;}},
        shortDate(value){return value?new Date(value).toLocaleString('id-ID',{day:'numeric',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}):'—';},
        openUpload(type){if(!this.data?.can_upload||this.documentStatus(type)==='APPROVED')return;this.selectedType=type;this.file=null;this.progress=0;const input=document.getElementById('ta-file');if(input)input.value='';document.getElementById('ta-upload').showModal();},
        selectFile(file){this.file=null;if(!file)return;if(!/\.(pdf|doc|docx)$/i.test(file.name)){notify('Please upload a PDF, DOC, or DOCX file',true);return;}if(file.size>10*1024*1024){notify('File size must be less than 10MB',true);return;}this.file=file;},
        async submit(){if(!this.file||!this.selectedType||this.uploading||!this.data?.can_upload)return;this.uploading=true;this.progress=0;try{const body=new FormData();body.append('file',this.file);body.append('document_type',this.selectedType);await upload('/mahasiswa/ta-documents/upload',body,percent=>{this.progress=percent;});document.getElementById('ta-upload').close();notify('Document uploaded successfully');await this.load();}catch(e){notify(e.message,true);if(e.status===403){document.getElementById('ta-upload').close();await this.load();}}finally{this.uploading=false;this.progress=0;}}
    }));
}
