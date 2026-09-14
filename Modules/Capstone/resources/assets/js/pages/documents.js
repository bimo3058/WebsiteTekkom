import {api,rows,unwrap,notify,download,date} from '../api.js';

export function registerDocuments(Alpine){
    Alpine.data('capstoneDocuments',()=>({
        loading:true,error:'',workflow:{},documents:[],search:'',sortKey:'created_at',sortDirection:-1,page:1,pageSize:10,
        uploadPhase:null,uploadType:null,file:null,saving:false,errors:{},date,
        labels:{PDC1:'PDC 1',SEMPRO:'Seminar Proposal',PDC2:'PDC 2',TA_DRAFT:'TA Draft',TA:'TA Draft',EXPO:'Expo',SIDANG:'Sidang TA',TA_INDIVIDUAL_READY:'Ready for TA Individual'},
        async init(){await this.load();},
        async load(){this.loading=true;this.error='';try{
            const result=await Promise.all([api('/mahasiswa/workflow'),api('/mahasiswa/documents')]);
            this.workflow=unwrap(result[0]);this.documents=rows(result[1]);
        }catch(e){this.error=e.message;}finally{this.loading=false;}},
        label(phase){return this.labels[phase] || phase;},
        get filtered(){const term=this.search.toLowerCase();return this.documents.filter(d=>[this.label(d.phase),d.document_type,d.status,d.student?.name].some(v=>String(v||'').toLowerCase().includes(term)))
            .sort((a,b)=>String(a[this.sortKey]??'').localeCompare(String(b[this.sortKey]??''),undefined,{numeric:true})*this.sortDirection);},
        get pageCount(){return Math.max(1,Math.ceil(this.filtered.length/this.pageSize));},
        get visible(){const from=(Math.min(this.page,this.pageCount)-1)*this.pageSize;return this.filtered.slice(from,from+this.pageSize);},
        sort(key){this.sortDirection=this.sortKey===key?-this.sortDirection:1;this.sortKey=key;},
        color(status){return {APPROVED:'bg-green-500 text-white',REJECTED:'bg-destructive text-white',SUBMITTED:'bg-blue-500 text-white'}[status] || 'bg-secondary text-secondary-foreground';},
        openUpload(phase,doc){
            if(!phase.can_upload || !doc.can_upload){notify(doc.locked_reason || phase.locked_reason || 'Upload locked',true);return;}
            this.uploadPhase=phase;this.uploadType=doc;this.file=null;this.errors={};
            document.getElementById('document-upload-form').reset();document.getElementById('document-upload').showModal();
        },
        async upload(){
            if(this.saving||!this.file||!this.uploadType?.can_upload)return;
            this.saving=true;this.errors={};
            try{const body=new FormData();body.append('file',this.file);body.append('phase',this.uploadPhase.phase);body.append('document_type',this.uploadType.type);
                await api('/mahasiswa/documents',{method:'POST',body});document.getElementById('document-upload').close();notify('Document uploaded successfully');await this.load();
            }catch(e){this.errors=e.errors||{};notify(e.message,true);if(e.status===403)await this.load();}finally{this.saving=false;}
        },
        async downloadDocument(doc){try{const blob=await api(`/mahasiswa/documents/${doc.id}/download`,{blob:true});const extension=(doc.file_path || '').split('.').pop();download(blob,`document-${doc.id}.${['pdf','doc','docx'].includes(extension)?extension:'pdf'}`);}catch(e){notify(e.message,true);}}
    }));
}
