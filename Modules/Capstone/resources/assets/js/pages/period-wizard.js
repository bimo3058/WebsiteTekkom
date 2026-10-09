import {api,unwrap,notify,url} from '../api.js';
const types=['SEMPRO','SIDANG_TA','EXPO','BIMBINGAN_SEMPRO','BIMBINGAN_TA','NILAI_DOSEN','MILESTONE'];
const dateFields=['start_date','end_date','bidding_start','bidding_end','bidding_reminder_at','pdc1_start','pdc1_end','pdc1_reminder_at','pdc2_start','pdc2_end','pdc2_reminder_at','expo_date','expo_reminder_at','ta_start','ta_end','ta_reminder_at'];
export function periodWizard(id=null){return {
    id,loading:true,error:'',saving:false,errors:{},step:0,types,templates:[],peerTemplates:[],periods:[],
    tab:'assessment',evaluationType:'SEMPRO',search:'',filter:'all',sortBy:'default',page:1,perPage:10,
    copyPeriod:'',copying:false,finalized:false,configuredAssessment:{},configuredPeer:false,
    form:{name:'',is_active:false,min_group_size:3,max_group_size:4,max_supervisor_load:5,...Object.fromEntries(dateFields.map(k=>[k,'']))},
    assessments:Object.fromEntries(types.map(type=>[type,[]])),peerIds:[],url,
    focusHandler:null,
    async init(){await this.load();this.focusHandler=()=>{if(this.step===1||this.step===2)this.refreshTemplates();};window.addEventListener('focus',this.focusHandler);},
    destroy(){if(this.focusHandler)window.removeEventListener('focus',this.focusHandler);},
    async refreshTemplates(){try{const options=unwrap(await api('/admin/period-wizard/options'));this.templates=options.templates;this.peerTemplates=options.peer_templates;this.periods=options.periods;}catch(e){notify(e.message,true);}},
    async load(){this.loading=true;this.error='';try{
        const [options,record]=await Promise.all([api('/admin/period-wizard/options').then(unwrap),this.id ? api('/admin/period-wizard/'+this.id).then(unwrap) : null]);
        this.templates=options.templates;this.peerTemplates=options.peer_templates;this.periods=options.periods;
        if(record){const p=record.period;this.finalized=!!p.is_finalized;for(const key of Object.keys(this.form)){this.form[key]=dateFields.includes(key) ? String(p[key] || '').slice(0,10) : (p[key] ?? this.form[key]);}this.assessments={...Object.fromEntries(types.map(type=>[type,[]])),...record.assessments};this.peerIds=record.peer_ids;
            for(const type of types){if((this.assessments[type]||[]).length)this.configuredAssessment[type]=true;}
            if((this.peerIds||[]).length)this.configuredPeer=true;
        }
    }catch(e){this.error=e.message;}finally{this.loading=false;}},
    get hasTemplates(){return this.templates.some(t=>t.is_active);},
    get editable(){return !this.loading && !this.saving && !this.copying && !this.finalized && !this.error;},
    get selectedIds(){return this.tab==='peer' ? this.peerIds : this.assessments[this.evaluationType];},
    get sourceList(){return this.tab==='peer' ? this.peerTemplates : this.templates;},
    get choices(){
        const ids=this.selectedIds;
        const q=this.search.trim().toLowerCase();
        let list=this.sourceList.filter(t=>(t.is_active || ids.includes(t.id)) && (!q || [t.code,t.name,t.description].some(v=>String(v || '').toLowerCase().includes(q))));
        if(this.filter==='high')list=list.filter(t=>Number(t.weight)>=50);
        if(this.filter==='mid')list=list.filter(t=>{const w=Number(t.weight);return w>=20&&w<50;});
        if(this.filter==='low')list=list.filter(t=>Number(t.weight)<20);
        if(this.sortBy==='kode')list=[...list].sort((a,b)=>String(a.code||'').localeCompare(String(b.code||'')));
        if(this.sortBy==='nama')list=[...list].sort((a,b)=>String(a.name||'').localeCompare(String(b.name||'')));
        if(this.sortBy==='bobot_desc')list=[...list].sort((a,b)=>Number(b.weight)-Number(a.weight));
        if(this.sortBy==='bobot_asc')list=[...list].sort((a,b)=>Number(a.weight)-Number(b.weight));
        return list;
    },
    get totalResults(){return this.choices.length;},
    get totalPages(){return Math.max(1,Math.ceil(this.choices.length/this.perPage));},
    get pagedChoices(){const start=(this.page-1)*this.perPage;return this.choices.slice(start,start+this.perPage);},
    get pageFrom(){return this.choices.length ? (this.page-1)*this.perPage+1 : 0;},
    get pageTo(){return Math.min(this.choices.length,this.page*this.perPage);},
    get pageNumbers(){
        const total=this.totalPages,current=this.page;
        if(total<=5)return Array.from({length:total},(_,i)=>i+1);
        if(current<=2)return [1,2,3,'...',total];
        if(current>=total-1)return [1,'...',total-2,total-1,total];
        return [1,'...',current,'...',total];
    },
    get activeIds(){return this.step===2 ? this.peerIds : this.assessments[this.evaluationType];},
    get activeWeight(){return this.weight(this.activeIds,this.step===2);},
    get activeCount(){return this.activeIds.length;},
    get activeTemplates(){
        const map=new Map(this.sourceListFor(this.step===2).map(t=>[t.id,t]));
        return this.activeIds.map(id=>map.get(id)).filter(Boolean);
    },
    sourceListFor(peer){return peer ? this.peerTemplates : this.templates;},
    weight(ids,peer=false){return (peer ? this.peerTemplates : this.templates).filter(t=>ids.includes(t.id)).reduce((sum,t)=>sum+Number(t.weight),0);},
    get totalWeight(){return this.weight(this.selectedIds,this.tab==='peer');},
    pillClass(w){const n=Number(w);if(n>=50)return 'bg-rose-50 text-rose-700';if(n>=20)return 'bg-amber-50 text-amber-700';return 'bg-teal-50 text-teal-700';},
    typeLabel(type){return String(type||'').replaceAll('_',' ');},
    reviewLabel(type){
        const labels={SEMPRO:'Sempro',SIDANG_TA:'Sidang TA',EXPO:'Expo TA',BIMBINGAN_SEMPRO:'Bimbingan Sempro',BIMBINGAN_TA:'Bimbingan TA',NILAI_DOSEN:'Nilai Dosen',MILESTONE:'Milestone'};
        return labels[type] || String(type||'').split('_').map(w=>w==='TA'?'TA':w.charAt(0)+w.slice(1).toLowerCase()).join(' ');
    },
    templateCodes(type){
        const map=new Map(this.templates.map(t=>[t.id,t.code]));
        return (this.assessments[type]||[]).map(id=>map.get(id)||'-');
    },
    get peerCodes(){
        const map=new Map(this.peerTemplates.map(t=>[t.id,t.code]));
        return this.peerIds.map(id=>map.get(id)||'-');
    },
    get reviewDuration(){return (this.form.start_date||'-')+' — '+(this.form.end_date||'-');},
    selectType(type){this.evaluationType=type;this.search='';this.filter='all';this.sortBy='default';this.page=1;},
    get typeIndex(){return this.types.indexOf(this.evaluationType);},
    prevType(){if(this.typeIndex>0)this.selectType(this.types[this.typeIndex-1]);else this.back();},
    nextType(){if(this.typeIndex<this.types.length-1)this.selectType(this.types[this.typeIndex+1]);else this.next();},
    setPeerContext(){this.tab='peer';this.search='';this.filter='all';this.sortBy='default';this.page=1;},
    setAssessmentContext(){this.tab='assessment';this.search='';this.filter='all';this.sortBy='default';this.page=1;},
    onSearch(){this.page=1;},
    setPerPage(n){this.perPage=Number(n);this.page=1;},
    goPage(n){if(n==='...')return;const v=Number(n);if(v>=1&&v<=this.totalPages)this.page=v;},
    nextPage(){if(this.page<this.totalPages)this.page++;},
    prevPage(){if(this.page>1)this.page--;},
    toggle(id){if(!this.editable)return;const ids=this.selectedIds.includes(id) ? this.selectedIds.filter(i=>i!==id) : [...this.selectedIds,id];if(this.tab==='peer')this.peerIds=ids;else this.assessments[this.evaluationType]=ids;},
    toggleActive(id){
        if(!this.editable)return;
        if(this.step===2){this.peerIds=this.peerIds.includes(id) ? this.peerIds.filter(i=>i!==id) : [...this.peerIds,id];return;}
        const cur=this.assessments[this.evaluationType];
        this.assessments[this.evaluationType]=cur.includes(id) ? cur.filter(i=>i!==id) : [...cur,id];
    },
    toggleAll(checked){if(!this.editable)return;const ids=checked ? [...new Set([...this.selectedIds,...this.choices.map(t=>t.id)])] : this.selectedIds.filter(id=>!this.choices.some(t=>t.id===id));if(this.tab==='peer')this.peerIds=ids;else this.assessments[this.evaluationType]=ids;},
    toggleAllActive(checked){
        if(!this.editable)return;
        const visible=this.pagedChoices.map(t=>t.id);
        if(this.step===2){
            this.peerIds=checked ? [...new Set([...this.peerIds,...visible])] : this.peerIds.filter(id=>!visible.includes(id));
            return;
        }
        const cur=this.assessments[this.evaluationType];
        this.assessments[this.evaluationType]=checked ? [...new Set([...cur,...visible])] : cur.filter(id=>!visible.includes(id));
    },
    get activeAllChecked(){return this.pagedChoices.length>0 && this.pagedChoices.every(t=>this.activeIds.includes(t.id));},
    async copy(){if(!this.copyPeriod || !this.editable)return;this.copying=true;try{const data=unwrap(await api('/admin/period-wizard/'+this.copyPeriod));this.assessments={...Object.fromEntries(types.map(type=>[type,[]])),...data.assessments};this.peerIds=data.peer_ids;notify('Konfigurasi disalin. Klik Simpan untuk menerapkan.');}catch(e){notify(e.message,true);}finally{this.copying=false;}},
    simpanKonfigurasi(){
        if(!this.editable)return;
        if(this.step===1){
            if(!this.validateAssessments()){notify(this.errors[Object.keys(this.errors)[0]][0],true);return;}
            this.configuredAssessment[this.evaluationType]=true;
            notify('Konfigurasi '+this.typeLabel(this.evaluationType)+' disimpan sebagai draft.');
        }else if(this.step===2){
            if(!this.validatePeer()){notify(this.errors[Object.keys(this.errors)[0]][0],true);return;}
            this.configuredPeer=true;
            notify('Konfigurasi Peer Review disimpan sebagai draft.');
        }
    },
    get simpanDisabled(){if(!this.editable)return true;if(this.step===1)return this.activeWeight!==100||this.activeCount===0;if(this.step===2)return this.activeWeight!==100||this.activeCount===0;return true;},
    validateAssessments(){
        for(const type of types){const ids=this.assessments[type];if(ids.length && Math.abs(this.weight(ids)-100)>.01){this.errors['assessments.'+type]=[this.typeLabel(type)+': total bobot harus 100%.'];return false;}}
        return true;
    },
    validatePeer(){
        if(this.peerIds.length && Math.abs(this.weight(this.peerIds,true)-100)>.01){this.errors['peer_ids']=['Peer Review: total bobot harus 100%.'];return false;}
        return true;
    },
    validate(step){this.errors={};const fail=(key,message)=>{this.errors[key]=[message];};
        if(step===0){if(!this.form.name.trim())fail('name','Nama periode wajib diisi.');if(!this.form.start_date)fail('start_date','Tanggal mulai wajib diisi.');if(!this.form.end_date || this.form.end_date<=this.form.start_date)fail('end_date','Tanggal akhir harus setelah tanggal mulai.');}
        if(step===1){if(!this.hasTemplates)fail('assessments','Silakan lengkapi Period Setup terlebih dahulu.');if(!this.validateAssessments())return false;}
        if(step===2){if(!this.validatePeer())return false;}
        if(step===3)for(const phase of ['bidding','pdc1','pdc2','ta']){if(this.form[phase+'_end'] && (!this.form[phase+'_start'] || this.form[phase+'_end']<this.form[phase+'_start']))fail(phase+'_end','Tanggal akhir harus sama atau setelah tanggal mulai.');}
        if(step===4){for(const [key,max] of [['min_group_size',10],['max_group_size',10],['max_supervisor_load',50]]){const n=Number(this.form[key]);if(!Number.isInteger(n)||n<1||n>max)fail(key,'Isi angka antara 1 dan '+max+'.');}if(Number(this.form.max_group_size)<Number(this.form.min_group_size))fail('max_group_size','Maksimal anggota harus sama atau lebih besar dari minimal anggota.');}
        return !Object.keys(this.errors).length;
    },
    syncTab(){this.tab=this.step===2 ? 'peer' : 'assessment';},
    go(index){if(this.saving||this.copying||index>this.step+1)return;if(index>this.step){for(let i=0;i<index;i++){if(!this.validate(i)){this.step=i;this.syncTab();return;}}}this.step=index;this.syncTab();this.page=1;},
    next(){this.go(this.step+1);},
    backToList(){window.location.assign(url('/admin/periods'));},
    back(){if(this.step>0){this.step--;this.syncTab();this.page=1;}else window.location.assign(url('/admin/periods'));},
    async save(){if(!this.editable || this.step!==5)return;for(let i=0;i<5;i++)if(!this.validate(i)){this.step=i;this.syncTab();return;}this.saving=true;try{
        const body={...Object.fromEntries(Object.entries(this.form).map(([k,v])=>[k,v===''?null:v])),min_group_size:Number(this.form.min_group_size),max_group_size:Number(this.form.max_group_size),max_supervisor_load:Number(this.form.max_supervisor_load),assessments:this.assessments,peer_ids:this.peerIds};
        await api('/admin/period-wizard'+(this.id?'/'+this.id:''),{method:this.id?'PUT':'POST',body});notify('Periode berhasil disimpan');window.location.assign(url('/admin/periods'));
    }catch(e){this.errors=e.errors || {};notify(e.message,true);}finally{this.saving=false;}}
};}
export function registerPeriodWizard(Alpine){Alpine.data('periodWizard',periodWizard);}
