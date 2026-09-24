import {basePage,api,rows,unwrap,query,dialog,mergePage} from './shared.js';
import {context} from '../../api.js';
export const evaluationTypes=['BIMBINGAN_SEMPRO','SEMPRO','NILAI_DOSEN','MILESTONE','EXPO','BIMBINGAN_TA','SIDANG_TA'];
export function assessmentConfig(peer=false,edit=false){return mergePage(basePage(),{peer,edit,type:peer?'PEER_REVIEW':(context.params?.id || 'SEMPRO'),templates:[],selectedIds:[],copyFrom:'',version:0,
    async init(){try{console.log('[Config] init start, URL period_id:',new URLSearchParams(window.location.search).get('period_id'));this.periodId=new URLSearchParams(window.location.search).get('period_id')||'';console.log('[Config] after URL read, periodId:',this.periodId);await this.periodsLoad();console.log('[Config] after periodsLoad, periodId:',this.periodId,'periods:',this.periods.length);await this.load();console.log('[Config] after init load, items:',this.items.length);}catch(e){this.error=e.message;this.loading=false;}},
    endpoint(){return `/admin/periods/${this.periodId}/${this.peer?'peer-review-config':'assessment-config'}`;},
    async load(){const version=++this.version;this.loading=true;this.error='';console.log('[Config] load called, periodId:',this.periodId,'version:',version);try{if(!this.periodId){this.items=[];this.loading=false;return;}
        if(this.edit){const data=unwrap(await api(this.endpoint()+query(this.peer?{}:{type:this.type})));if(version!==this.version)return;this.templates=data.all_templates || [];this.selectedIds=(data.selected_indicators || data.selected_components || []).map(i=>Number(i.template_id));}
        else {const types=this.peer?['PEER_REVIEW']:evaluationTypes;const results=await Promise.allSettled(types.map(async type=>{const data=unwrap(await api(this.endpoint()+query(this.peer?{}:{type})));return {id:type,name:type.replaceAll('_',' '),components:data.selected_indicators || data.selected_components || []};}));const configs=results.filter(r=>r.status==='fulfilled').map(r=>r.value);const failures=results.filter(r=>r.status==='rejected');if(failures.length)console.error('[Config] failed types:',failures.map((f,i)=>types[i]+': '+f.reason?.message));if(version===this.version)this.items=configs;}
    }catch(e){console.error('[Config] load error:',e);if(version===this.version)this.error=e.message;}finally{console.log('[Config] load done, version:',version,'current:',this.version,'items:',this.items?.length);if(version===this.version)this.loading=false;}},
    get selectedTemplates(){return this.selectedIds.map(id=>this.templates.find(t=>Number(t.id)===Number(id))).filter(Boolean);},
    get totalWeight(){return this.selectedTemplates.reduce((sum,t)=>sum+Number(t.weight||0),0);},
    toggle(id){if(this.period?.is_finalized)return;id=Number(id);this.selectedIds=this.selectedIds.includes(id)?this.selectedIds.filter(i=>i!==id):[...this.selectedIds,id];},
    move(index,direction){const next=index+direction;if(this.period?.is_finalized||next<0||next>=this.selectedIds.length)return;[this.selectedIds[index],this.selectedIds[next]]=[this.selectedIds[next],this.selectedIds[index]];},
    editUrl(item){return this.url(`/admin/${this.peer?'peer-review':'period-assessment-config'}/${item.id}/edit`)+query({period_id:this.periodId});},
    async save(){if(!this.periodId||this.period?.is_finalized)return;if(await this.run(()=>api(this.endpoint(),{method:'POST',body:{...(this.peer?{}:{type:this.type}),template_ids:this.selectedIds}})))await this.load();},
    async copy(){if(!this.copyFrom||this.period?.is_finalized)return;if(await this.run(()=>api(this.endpoint()+'/copy',{method:'POST',body:{source_period_id:Number(this.copyFrom),...(this.peer?{}:{type:this.type})}})))await this.load();},
});}
export function gradeConfig(){return mergePage(basePage(),{weights:{pdc1:{},pdc2:{},ta:{}},version:0,
    async init(){try{await this.periodsLoad();await this.load();}catch(e){this.error=e.message;this.loading=false;}},
    async load(){const version=++this.version;this.loading=true;this.error='';try{if(!this.periodId)return;const data=unwrap(await api('/admin/grade-configuration/'+this.periodId));if(version===this.version)this.weights=Object.fromEntries(['pdc1','pdc2','ta'].map(p=>[p,{...data[p]?.weights}]));}catch(e){if(version===this.version)this.error=e.message;}finally{if(version===this.version)this.loading=false;}},
    total(phase){return Object.values(this.weights[phase]).reduce((sum,w)=>sum+Number(w),0);},
    get valid(){return Object.keys(this.weights).every(p=>Math.abs(this.total(p)-100)<0.001&&Object.values(this.weights[p]).every(w=>w!==''&&Number.isFinite(Number(w))&&Number(w)>=0));},
    async save(){if(!this.valid||!this.periodId)return;const body=Object.fromEntries(Object.entries(this.weights).map(([phase,weights])=>[phase+'_weights',Object.fromEntries(Object.entries(weights).map(([k,v])=>[k,Number(v)]))]));await this.run(()=>api('/admin/grade-configuration/'+this.periodId,{method:'POST',body}));},
    async reset(){if(await this.run(()=>api('/admin/grade-configuration/'+this.periodId+'/reset',{method:'POST'}))){dialog('reset-grades').close();await this.load();}},
});}
