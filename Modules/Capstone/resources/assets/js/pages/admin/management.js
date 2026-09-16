import {basePage,api,rows,unwrap,query,dialog,allRows,mergePage} from './shared.js';
import {context} from '../../api.js';
export function adminGroups(detail=false){return mergePage(basePage(),{detail,group:null,lecturers:[],supervisorId:'',status:'',pagination:{last_page:1,total:0},
    async init(){try{await this.periodsLoad(false);if(detail)this.lecturers=await allRows('/admin/users?role=dosen');await this.load();}catch(e){this.error=e.message;this.loading=false;}},
    async load(){this.loading=true;this.error='';try{if(detail)this.group=unwrap(await api('/admin/groups/'+context.params.id));else {const data=await api('/admin/groups'+query({period_id:this.periodId,status:this.status,search:this.search,page:this.page,per_page:this.pageSize}));this.items=rows(data);this.pagination=data.pagination || unwrap(data)?.pagination || {};}}catch(e){this.error=e.message;}finally{this.loading=false;}},
    async assign(){if(!this.supervisorId)return;if(await this.run(()=>api(`/admin/groups/${this.group.id}/assign-supervisor-2`,{method:'POST',body:{supervisor_2_id:Number(this.supervisorId)}})))await this.load();},
});}
export function expoAdmin(){return mergePage(basePage(),{editing:null,form:{},action:'',
    async init(){try{await this.periodsLoad(false);await this.load();}catch(e){this.error=e.message;this.loading=false;}},
    async load(){this.loading=true;this.error='';try{this.items=rows(await api('/admin/expo-events'+query({period_id:this.periodId})));}catch(e){this.error=e.message;}finally{this.loading=false;}},
    edit(item=null){this.editing=item;this.errors={};this.form={period_id:String(item?.period_id||this.periodId||''),name:item?.name||'',date:(item?.date||'').slice(0,10),start_time:(item?.start_time||'').slice(0,5),end_time:(item?.end_time||'').slice(0,5),room:item?.room||'',capacity:item?.capacity||20,is_published:item?.is_published||false};dialog('expo-form').showModal();},
    async save(){if(await this.run(()=>api('/admin/expo-events'+(this.editing?'/'+this.editing.id:''),{method:this.editing?'PUT':'POST',body:{...this.form,period_id:Number(this.form.period_id),capacity:Number(this.form.capacity)}}))){dialog('expo-form').close();await this.load();}},
    confirm(item,action){this.selected=item;this.action=action;dialog('expo-confirm').showModal();},
    async apply(){const endpoint='/admin/expo-events/'+this.selected.id;if(await this.run(()=>api(endpoint+(this.action==='publish'?'/publish':''),{method:this.action==='publish'?'PUT':'DELETE'}))){dialog('expo-confirm').close();await this.load();}},
});}
export function semproAdmin(){return mergePage(basePage(),{groups:[],lecturers:[],locations:[],form:{},mode:'create',reason:'',
    async init(){try{await this.periodsLoad(false);const [groups,lecturers,locations]=await Promise.all([allRows('/admin/groups'),allRows('/admin/users?role=dosen'),api('/locations')]);this.groups=groups;this.lecturers=rows(lecturers);this.locations=rows(locations);await this.load();}catch(e){this.error=e.message;this.loading=false;}},
    async load(){this.loading=true;this.error='';try{const schedules=rows(await api('/admin/sempro/schedules'));this.items=schedules.filter(s=>!this.periodId||String(s.group?.period_id)===String(this.periodId));}catch(e){this.error=e.message;}finally{this.loading=false;}},
    get eligible(){return this.groups.filter(g=>g.status==='READY_FOR_SEMPRO'&&(!this.periodId||String(g.period_id)===String(this.periodId)));},
    edit(item=null,mode=null){this.selected=item;this.mode=mode || (item?'approve':'create');this.errors={};this.form={group_id:item?.group_id||'',date:(item?.date||'').slice(0,10),start_time:(item?.start_time||'').slice(0,5),end_time:(item?.end_time||'').slice(0,5),room:item?.room||'',examiner_1_id:item?.examiner_1_id||'',examiner_2_id:item?.examiner_2_id||''};dialog('sempro-form').showModal();},
    async save(){const body={...this.form,group_id:Number(this.form.group_id),examiner_1_id:Number(this.form.examiner_1_id),examiner_2_id:Number(this.form.examiner_2_id)};if(await this.run(()=>api(this.mode==='create'?'/admin/sempro/schedule':`/admin/sempro/schedules/${this.selected.id}`+(this.mode==='approve'?'/approve':''),{method:this.mode==='create'?'POST':'PUT',body}))){dialog('sempro-form').close();await this.load();}},
    cancel(item){this.selected=item;dialog('sempro-cancel').showModal();},
    async cancelSave(){if(await this.run(()=>api(`/admin/sempro/schedules/${this.selected.id}/cancel`,{method:'PUT'}))){dialog('sempro-cancel').close();await this.load();}},
    reject(item){this.selected=item;this.reason='';dialog('sempro-reject').showModal();},
    async rejectSave(){if(!this.reason.trim())return;if(await this.run(()=>api(`/admin/sempro/schedules/${this.selected.id}/reject`,{method:'PUT',body:{rejection_reason:this.reason.trim()}}))){dialog('sempro-reject').close();await this.load();}},
});}
export function finalizationAdmin(){return mergePage(basePage(),{titles:[],groups:[],lecturers:[],loads:[],locked:false,action:'',form:{},
    async init(){try{await this.periodsLoad();this.lecturers=await allRows('/admin/users?role=dosen');await this.load();}catch(e){this.error=e.message;this.loading=false;}},
    async load(){this.loading=true;this.error='';try{if(!this.periodId)return;const [data,loads,groups]=await Promise.all([api('/admin/finalization'+query({period_id:this.periodId})),api('/admin/finalization/dosen-load'+query({period_id:this.periodId})),allRows('/admin/groups')]);this.titles=rows(data);this.items=this.titles;this.loads=rows(loads);this.groups=groups.filter(g=>String(g.period_id)===String(this.periodId));this.locked=data.is_locked;}catch(e){this.error=e.message;}finally{this.loading=false;}},
    allocate(title,bid=null){this.selected=title;this.action=bid?'allocate':'allocate-student-proposed';this.form={bid_id:bid?.id,group_id:bid?.group_id || title.proposed_by_group_id || '',title_id:title.id,supervisor_1_id:bid?.proposed_supervisor_1_id||title.lecturer_id||'',supervisor_2_id:bid?.proposed_supervisor_2_id||''};dialog('allocate-form').showModal();},
    async save(){const body=Object.fromEntries(Object.entries(this.form).filter(([,v])=>v!==undefined).map(([k,v])=>[k,v===''?null:Number(v)]));if(await this.run(()=>api('/admin/finalization/'+this.action,{method:'POST',body}))){dialog('allocate-form').close();await this.load();}},
    confirm(action){this.action=action;dialog('finalization-confirm').showModal();},
    async apply(){if(!this.periodId)return;if(await this.run(()=>api('/admin/finalization/'+this.action,{method:'POST',body:{period_id:Number(this.periodId)}}))){dialog('finalization-confirm').close();await this.periodsLoad();await this.load();}},
});}
