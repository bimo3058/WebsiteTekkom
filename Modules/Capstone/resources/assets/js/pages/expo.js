import {api,context,rows,unwrap,notify,date,url} from '../api.js';
export function registerExpo(Alpine){
    Alpine.data('capstoneExpo',()=>({
        loading:true,error:'',events:[],saving:false,selected:null,action:'register',date,url,
        async init(){await this.load();},
        async load(){this.loading=true;this.error='';try{this.events=rows(await api('/mahasiswa/expo-events'));}catch(e){this.error=e.message;}finally{this.loading=false;}},
        full(event){return event.registrations_count>=event.capacity;},
        confirm(event,action){if(action==='register'&&!event.can_register)return;this.selected=event;this.action=action;document.getElementById('expo-confirm').showModal();},
        async submit(){if(this.saving||!this.selected)return;this.saving=true;try{await api(`/mahasiswa/expo-events/${this.selected.id}/${this.action}`,{method:'POST'});document.getElementById('expo-confirm').close();notify(this.action==='register'?'Successfully registered for expo!':'Successfully withdrawn from expo.');await this.load();}catch(e){notify(e.message,true);}finally{this.saving=false;}}
    }));
    Alpine.data('capstoneExpoDetail',()=>({
        loading:true,error:'',data:null,saving:false,uploading:false,scores:{},notes:{},date,url,
        get endpoint(){return `/mahasiswa/expo-events/${encodeURIComponent(context.params.expoId)}`;},
        async init(){await this.load();},
        async load(){this.loading=true;this.error='';try{this.data=unwrap(await api(this.endpoint+'/detail'));this.scores={};this.notes={};for(const s of this.data.my_scores){this.scores[s.period_component_id]=s.score===null?'':Number(s.score);this.notes[s.period_component_id]=s.notes || '';}}catch(e){this.error=e.message;}finally{this.loading=false;}},
        get submitted(){return this.data?.my_scores?.some(s=>s.score!==null&&s.score!==undefined) || false;},
        get evaluationDone(){return !!this.data?.components?.length&&this.data.components.every(c=>Number(this.scores[c.id])>0)&&this.submitted;},
        get weighted(){return (this.data?.components || []).reduce((sum,c)=>sum+Number(this.scores[c.id] || 0)*Number(c.weight)/100,0).toFixed(2);},
        async submit(){if(this.submitted||this.saving||!this.data?.components.length)return;if(this.data.components.some(c=>!(Number(this.scores[c.id])>=1&&Number(this.scores[c.id])<=100))){notify('Isi nilai 1–100 untuk setiap komponen.',true);return;}this.saving=true;try{await api(this.endpoint+'/evaluation',{method:'POST',body:{scores:this.data.components.map(c=>({period_component_id:c.id,score:Number(this.scores[c.id]),notes:this.notes[c.id] || ''}))}});notify('Self-evaluation berhasil disimpan');await this.load();}catch(e){notify(e.message,true);if(e.status===403)await this.load();}finally{this.saving=false;}},
        async upload(file){if(!file||this.uploading||this.data?.my_document)return;if(file.size>10*1024*1024){notify('Ukuran file maksimal 10MB',true);return;}this.uploading=true;try{const body=new FormData();body.append('file',file);await api(this.endpoint+'/document',{method:'POST',body});notify('Dokumen berhasil diupload');await this.load();}catch(e){notify(e.message,true);}finally{this.uploading=false;const input=document.getElementById('expo-file');if(input)input.value='';}}
    }));
}
