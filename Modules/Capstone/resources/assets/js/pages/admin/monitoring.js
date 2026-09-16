import {basePage,api,rows,unwrap,query,download,dialog,mergePage} from './shared.js';
export function progressAdmin(){return mergePage(basePage(),{status:'',
    async init(){try{await this.periodsLoad(false);await this.load();}catch(e){this.error=e.message;this.loading=false;}},
    async load(){this.loading=true;this.error='';try{this.items=rows(await api('/admin/analytics/group-progress'+query({period_id:this.periodId})));}catch(e){this.error=e.message;}finally{this.loading=false;}},
    percent(item){const phases=item.progress?.phases || [];return phases.length?Math.round(phases.filter(p=>p.status==='completed').length/phases.length*100):Number(item.progress_percentage||0);},
    async exportCsv(){const quote=v=>'"'+String(v??'').replaceAll('"','""')+'"';const csv=[['Kelompok','Judul','Periode','Status','Progress'],...this.filtered.map(g=>[this.groupName(g),g.title?.title,g.period?.name,g.status,this.percent(g)+'%'])].map(r=>r.map(quote).join(',')).join('\r\n');download(new Blob(['\uFEFF'+csv],{type:'text/csv'}),'group-progress.csv');},
});}
export function peerDashboard(){return mergePage(basePage(),{
    async init(){try{await this.periodsLoad(false);await this.load();}catch(e){this.error=e.message;this.loading=false;}},
    async load(){this.loading=true;this.error='';try{this.items=rows(await api('/admin/peer-review-dashboard/groups'+query({period_id:this.periodId})));}catch(e){this.error=e.message;}finally{this.loading=false;}},
    get completed(){return this.items.reduce((n,g)=>n+g.completed_count,0);},
    get members(){return this.items.reduce((n,g)=>n+g.total_members,0);},
    confirm(group){this.selected=group;dialog('peer-reminder').showModal();},
    async remind(){if(await this.run(()=>api('/admin/peer-review-dashboard/send-reminder/'+this.selected.group_id,{method:'POST'}),'Pengingat dikirim'))dialog('peer-reminder').close();},
});}
export function documentUploads(){return mergePage(basePage(),{summary:{},source:'',status:'',dateFrom:'',dateTo:'',groupId:'',pagination:{last_page:1,total:0},expanded:null,
    async init(){try{this.groupId=new URLSearchParams(window.location.search).get('group_id')||'';await this.periodsLoad(false);await this.load();}catch(e){this.error=e.message;this.loading=false;}},
    async load(){this.loading=true;this.error='';try{const [list,summary]=await Promise.all([api('/admin/document-uploads'+query({period_id:this.periodId,group_id:this.groupId,source:this.source,status:this.status,date_from:this.dateFrom,date_to:this.dateTo,search:this.search,page:this.page,per_page:this.pageSize})),api('/admin/document-uploads/summary'+query({period_id:this.periodId}))]);this.items=rows(list);this.pagination=unwrap(list).pagination||{};this.summary=unwrap(summary);}catch(e){this.error=e.message;}finally{this.loading=false;}},
    async downloadDoc(doc){await this.run(async()=>{const blob=await api('/admin/document-uploads/'+doc.id+'/download'+query({source:doc.source}),{blob:true});download(blob,doc.original_name||doc.document_type+'.pdf');},'Dokumen diunduh');},
});}
