import {api,unwrap,notify,url} from '../api.js';
const types=['SIDANG_TA','EXPO','BIMBINGAN_SEMPRO','BIMBINGAN_TA','NILAI_DOSEN','MILESTONE'];
const dateFields=['start_date','end_date','bidding_start','bidding_end','bidding_reminder_at','pdc1_start','pdc1_end','pdc1_reminder_at','pdc2_start','pdc2_end','pdc2_reminder_at','expo_date','expo_reminder_at','ta_start','ta_end','ta_reminder_at'];
export function periodWizard(id=null){return {
    id,loading:true,error:'',saving:false,errors:{},step:0,types,templates:[],peerTemplates:[],periods:[],tab:'assessment',evaluationType:'SIDANG_TA',search:'',copyPeriod:'',copying:false,finalized:false,
    form:{name:'',is_active:false,min_group_size:3,max_group_size:4,max_supervisor_load:5,...Object.fromEntries(dateFields.map(k=>[k,'']))},
    assessments:Object.fromEntries(types.map(type=>[type,[]])),peerIds:[],url,
    focusHandler:null,
    async init(){await this.load();this.focusHandler=()=>{if(this.step===1)this.refreshTemplates();};window.addEventListener('focus',this.focusHandler);},
    destroy(){if(this.focusHandler)window.removeEventListener('focus',this.focusHandler);},
    async refreshTemplates(){try{const options=unwrap(await api('/admin/period-wizard/options'));this.templates=options.templates;this.peerTemplates=options.peer_templates;this.periods=options.periods;}catch(e){notify(e.message,true);}},
    async load(){this.loading=true;this.error='';try{
        const [options,record]=await Promise.all([api('/admin/period-wizard/options').then(unwrap),this.id ? api('/admin/period-wizard/'+this.id).then(unwrap) : null]);
        this.templates=options.templates;this.peerTemplates=options.peer_templates;this.periods=options.periods;
        if(record){const p=record.period;this.finalized=!!p.is_finalized;for(const key of Object.keys(this.form)){this.form[key]=dateFields.includes(key) ? String(p[key] || '').slice(0,10) : (p[key] ?? this.form[key]);}this.assessments=record.assessments;this.peerIds=record.peer_ids;}
    }catch(e){this.error=e.message;}finally{this.loading=false;}},
    get hasTemplates(){return this.templates.some(t=>t.is_active);},
    get editable(){return !this.loading && !this.saving && !this.copying && !this.finalized && !this.error;},
    get selectedIds(){return this.tab==='peer' ? this.peerIds : this.assessments[this.evaluationType];},
    get choices(){return (this.tab==='peer' ? this.peerTemplates : this.templates).filter(t=>(t.is_active || this.selectedIds.includes(t.id)) && (!this.search || [t.code,t.name,t.description].some(v=>String(v || '').toLowerCase().includes(this.search.toLowerCase()))));},
    weight(ids,peer=false){return (peer ? this.peerTemplates : this.templates).filter(t=>ids.includes(t.id)).reduce((sum,t)=>sum+Number(t.weight),0);},
    get totalWeight(){return this.weight(this.selectedIds,this.tab==='peer');},
    toggle(id){if(!this.editable)return;const ids=this.selectedIds.includes(id) ? this.selectedIds.filter(i=>i!==id) : [...this.selectedIds,id];if(this.tab==='peer')this.peerIds=ids;else this.assessments[this.evaluationType]=ids;},
    toggleAll(checked){if(!this.editable)return;const ids=checked ? [...new Set([...this.selectedIds,...this.choices.map(t=>t.id)])] : this.selectedIds.filter(id=>!this.choices.some(t=>t.id===id));if(this.tab==='peer')this.peerIds=ids;else this.assessments[this.evaluationType]=ids;},
    async copy(){if(!this.copyPeriod || !this.editable)return;this.copying=true;try{const data=unwrap(await api('/admin/period-wizard/'+this.copyPeriod));this.assessments=data.assessments;this.peerIds=data.peer_ids;notify('Konfigurasi disalin. Klik Simpan untuk menerapkan.');}catch(e){notify(e.message,true);}finally{this.copying=false;}},
    validate(step){this.errors={};const fail=(key,message)=>{this.errors[key]=[message];};
        if(step===0){if(!this.form.name.trim())fail('name','Nama periode wajib diisi.');if(!this.form.start_date)fail('start_date','Tanggal mulai wajib diisi.');if(!this.form.end_date || this.form.end_date<=this.form.start_date)fail('end_date','Tanggal akhir harus setelah tanggal mulai.');}
        if(step===1){if(!this.hasTemplates)fail('assessments','Silakan lengkapi Period Setup terlebih dahulu.');for(const type of types){const ids=this.assessments[type];if(ids.length && Math.abs(this.weight(ids)-100)>.01)fail('assessments.'+type,type+': total bobot harus 100%.');}if(this.peerIds.length && Math.abs(this.weight(this.peerIds,true)-100)>.01)fail('peer_ids','Peer Review: total bobot harus 100%.');}
        if(step===2)for(const phase of ['bidding','pdc1','pdc2','ta']){if(this.form[phase+'_end'] && (!this.form[phase+'_start'] || this.form[phase+'_end']<this.form[phase+'_start']))fail(phase+'_end','Tanggal akhir harus sama atau setelah tanggal mulai.');}
        if(step===3){for(const [key,max] of [['min_group_size',10],['max_group_size',10],['max_supervisor_load',50]]){const n=Number(this.form[key]);if(!Number.isInteger(n)||n<1||n>max)fail(key,'Isi angka antara 1 dan '+max+'.');}if(Number(this.form.max_group_size)<Number(this.form.min_group_size))fail('max_group_size','Maksimal anggota harus sama atau lebih besar dari minimal anggota.');}
        return !Object.keys(this.errors).length;
    },
    go(index){if(this.saving||this.copying||index>this.step+1)return;if(index>this.step){for(let i=0;i<index;i++){if(!this.validate(i)){this.step=i;return;}}}this.step=index;},
    back(){if(this.step>0)this.step--;else window.location.assign(url('/admin/periods'));},
    async save(){if(!this.editable || this.step!==4)return;for(let i=0;i<4;i++)if(!this.validate(i)){this.step=i;return;}this.saving=true;try{
        const body={...Object.fromEntries(Object.entries(this.form).map(([k,v])=>[k,v===''?null:v])),min_group_size:Number(this.form.min_group_size),max_group_size:Number(this.form.max_group_size),max_supervisor_load:Number(this.form.max_supervisor_load),assessments:this.assessments,peer_ids:this.peerIds};
        await api('/admin/period-wizard'+(this.id?'/'+this.id:''),{method:this.id?'PUT':'POST',body});notify('Periode berhasil disimpan');window.location.assign(url('/admin/periods'));
    }catch(e){this.errors=e.errors || {};notify(e.message,true);}finally{this.saving=false;}}
};}
export function registerPeriodWizard(Alpine){Alpine.data('periodWizard',periodWizard);}
