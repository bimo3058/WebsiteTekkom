import {api, rows, notify, url} from '../api.js';

export const phases = ['PDC1','SEMPRO','PDC2','EXPO','TA','SIDANG'];
const labels = {PDC1:'PDC 1',SEMPRO:'Seminar Proposal',PDC2:'PDC 2',EXPO:'Expo',TA:'Tugas Akhir',SIDANG:'Sidang'};
const descriptions = {
    PDC1:'Tahap awal pengembangan project capstone, termasuk proposal dan perencanaan awal.',
    SEMPRO:'Seminar proposal untuk presentasi rencana project kepada dosen pembimbing.',
    PDC2:'Tahap kedua pengembangan dengan fokus pada kemajuan implementasi project.',
    EXPO:'Presentasi dan pameran hasil project capstone kepada publik.',
    TA:'Penulisan dan pengembangan laporan Tugas Akhir secara menyeluruh.',
    SIDANG:'Sidang akhir untuk presentasi dan pembelaan Tugas Akhir.',
};
const colors = {PDC1:'bg-emerald-100 text-emerald-700 border-emerald-200',SEMPRO:'bg-indigo-100 text-indigo-700 border-indigo-200',PDC2:'bg-violet-100 text-violet-700 border-violet-200',EXPO:'bg-amber-100 text-amber-700 border-amber-200',TA:'bg-rose-100 text-rose-700 border-rose-200',SIDANG:'bg-teal-100 text-teal-700 border-teal-200'};
const defaults = {PDC1:['Proposal','Gantt Chart'],SEMPRO:['Buku Bimbingan','Bukti Kemajuan'],PDC2:['Laporan Kemajuan','Bukti Kemajuan'],EXPO:['Poster','Laporan TA'],TA:['Draft TA','Buku Panduan TA'],SIDANG:['Buku TA Final','CD Program']};

export function documentRequirementsPage(phase = '') {
    return {
        phase:phase.toUpperCase(),periods:[],selectedPeriod:'',items:[],loading:true,saving:false,error:'',search:'',status:'all',page:1,pageSize:10,sortKey:'phase',sortDirection:1,newName:'',newDescription:'',requestId:0,nextKey:0,
        url,labels,descriptions,colors,
        async init() {
            try {
                this.periods=rows(await api('/periods-list'));
                const requested=new URLSearchParams(window.location.search).get('period_id');
                this.selectedPeriod=String(this.periods.find(p=>String(p.id)===requested)?.id ?? this.periods.find(p=>p.is_active)?.id ?? this.periods[0]?.id ?? '');
                await this.load();
            } catch(e) {this.error=e.message;this.loading=false;}
        },
        get finalized(){return !!this.periods.find(p=>String(p.id)===String(this.selectedPeriod))?.is_finalized;},
        get editable(){return !!this.selectedPeriod && !this.finalized && !this.loading && !this.saving && !this.error;},
        get requiredCount(){return this.items.filter(r=>r.is_required).length;},
        get filtered(){
            const search=this.search.trim().toLowerCase();
            return this.items.filter(r=>(!search || [r.phase,labels[r.phase],...(r.document_names || [])].some(v=>v?.toLowerCase().includes(search))) && (this.status==='all' || (this.status==='configured')===!!r.has_configured)).sort((a,b)=>(this.sortKey==='count' ? a.document_count-b.document_count : phases.indexOf(a.phase)-phases.indexOf(b.phase))*this.sortDirection);
        },
        get pageCount(){return Math.max(1,Math.ceil(this.filtered.length/this.pageSize));},
        get visible(){return this.filtered.slice((Math.min(this.page,this.pageCount)-1)*this.pageSize,Math.min(this.page,this.pageCount)*this.pageSize);},
        sort(key){this.sortDirection=this.sortKey===key ? -this.sortDirection : 1;this.sortKey=key;},
        phaseUrl(phase=''){return url('/admin/document-requirements'+(phase ? '/'+phase.toLowerCase() : '')+'?period_id='+encodeURIComponent(this.selectedPeriod));},
        async load(){
            const id=++this.requestId,period=this.selectedPeriod;
            this.loading=true;this.error='';this.items=[];this.newName='';this.newDescription='';this.page=1;
            if(!period){this.loading=false;return;}
            try {
                const result=rows(await api('/admin/document-requirements/period/'+period+(this.phase ? '' : '/summary')));
                if(id!==this.requestId)return;
                this.items=this.phase ? result.filter(r=>r.phase===this.phase).map(r=>({...r,_key:++this.nextKey})) : phases.map(phase=>result.find(r=>r.phase===phase) || {phase,document_count:0,required_count:0,document_names:[],has_configured:false});
            } catch(e){if(id===this.requestId)this.error=e.message;} finally {if(id===this.requestId)this.loading=false;}
        },
        add(){
            if(!this.editable || !this.newName.trim())return;
            if(this.items.some(r=>r.name.toLowerCase()===this.newName.trim().toLowerCase())){notify('Dokumen dengan nama ini sudah ada.',true);return;}
            this.items.push({_key:++this.nextKey,phase:this.phase,name:this.newName.trim(),description:this.newDescription.trim(),is_required:true});this.newName='';this.newDescription='';
        },
        remove(item){if(this.editable)this.items=this.items.filter(r=>r._key!==item._key);},
        async save(useDefaults=false){
            if(!this.editable)return;
            const requirements=useDefaults ? phases.flatMap(phase=>defaults[phase].map(name=>({phase,name,is_required:true,description:null}))) : this.items.map(r=>({phase:this.phase,name:r.name.trim(),description:r.description?.trim() || null,is_required:!!r.is_required}));
            if(requirements.some(r=>!r.name)){notify('Nama dokumen wajib diisi.',true);return;}
            if(new Set(requirements.map(r=>r.phase+':'+r.name.toLowerCase())).size!==requirements.length){notify('Nama dokumen dalam satu fase harus berbeda.',true);return;}
            this.saving=true;
            try {
                await api('/admin/document-requirements/bulk',{method:'PUT',body:{period_id:Number(this.selectedPeriod),...(!useDefaults ? {phase:this.phase} : {}),requirements}});
                notify('Document requirements updated successfully');
                if(useDefaults)document.getElementById('defaults-confirm').close();
                await this.load();
            } catch(e){notify(e.message,true);} finally {this.saving=false;}
        },
    };
}

export function documentTypesPage(){
    return {
        items:[],loading:true,error:'',search:'',editing:null,form:{},errors:{},saving:false,deleting:null,
        async init(){await this.load();},
        get filtered(){const q=this.search.toLowerCase();return this.items.filter(r=>(r.name+' '+(r.description || '')).toLowerCase().includes(q));},
        async load(){this.loading=true;this.error='';try{this.items=rows(await api('/admin/document-types'));}catch(e){this.error=e.message;}finally{this.loading=false;}},
        edit(item=null){this.editing=item;this.errors={};this.form={name:item?.name || '',description:item?.description || '',phase:item?.phase || 'ALL'};document.getElementById('type-form').showModal();},
        async save(){if(this.saving)return;this.saving=true;this.errors={};try{await api('/admin/document-types'+(this.editing ? '/'+this.editing.id : ''),{method:this.editing ? 'PUT' : 'POST',body:{...this.form,phase:this.form.phase==='ALL' ? null : this.form.phase}});document.getElementById('type-form').close();notify(this.editing ? 'Document type updated' : 'Document type created');await this.load();}catch(e){this.errors=e.errors || {};notify(e.message,true);}finally{this.saving=false;}},
        confirmDelete(item){this.deleting=item;document.getElementById('confirm-delete').showModal();},
        async remove(){if(this.saving || !this.deleting)return;this.saving=true;try{await api('/admin/document-types/'+this.deleting.id,{method:'DELETE'});document.getElementById('confirm-delete').close();notify('Document type deleted');await this.load();}catch(e){notify(e.message,true);}finally{this.saving=false;}},
    };
}
export function registerDocumentConfiguration(Alpine){Alpine.data('documentRequirements',documentRequirementsPage);Alpine.data('documentTypes',documentTypesPage);}
