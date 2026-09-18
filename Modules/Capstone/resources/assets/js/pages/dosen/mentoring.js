import {workspace,api,rows,download,notify} from './shared.js';

const progress={FORMING:0,FORMING_SOLO:0,READY_FOR_BIDDING:10,KELOMPOK_FINAL:20,PDC1_ACTIVE:30,READY_FOR_SEMPRO:40,SEMPRO_DONE:50,PDC2_ACTIVE:60,TA_DRAFT:65,PDC2_READY_FOR_EXPO:70,EXPO_REGISTERED:80,EXPO_DONE:90,READY_FOR_TA_INDIVIDUAL:100,CLOSED:100};
export function lecturerGroups(){
    return workspace({items:[],
        async load(){this.loading=true;this.error='';try{const [groups,periods]=await Promise.all([api('/dosen/groups/supervised'),api('/periods-list')]);this.items=rows(groups);this.periods=rows(periods);}catch(e){this.error=e.message;}finally{this.loading=false;}},
        get filtered(){return this.items.filter(g=>this.inPeriod(g.period_id)&&this.matches(g));},
        progress(group){return progress[group.status]||0;},
        latest(group){return [...(group.documents||[])].sort((a,b)=>String(b.updated_at).localeCompare(String(a.updated_at)))[0]||null;},
    });
}

export function lecturerDocuments(taOnly=false){
    return workspace({items:[],groups:[],selectedGroup:'all',phase:'all',status:'all',selected:null,feedback:'',reviewStatus:'APPROVED',downloading:null,
        async init(){const params=new URLSearchParams(window.location.search);this.selectedGroup=params.get('group_id')||'all';await this.load();},
        async load(){this.loading=true;this.error='';try{const [docs,groups,periods]=await Promise.all([api('/dosen/documents'),api('/dosen/groups/supervised'),api('/periods-list')]);this.items=rows(docs);this.groups=rows(groups);this.periods=rows(periods);}catch(e){this.error=e.message;}finally{this.loading=false;}},
        get availableGroups(){return this.groups.filter(g=>this.inPeriod(g.period_id));},
        get filtered(){return this.items.filter(d=>(!taOnly||['TA','SIDANG','TA_INDIVIDUAL'].includes(d.phase))&&this.matches(d)&&this.inPeriod(d.group?.period_id||this.groups.find(g=>String(g.id)===String(d.group_id))?.period_id)&&(this.selectedGroup==='all'||String(d.group_id)===String(this.selectedGroup))&&(this.phase==='all'||d.phase===this.phase)&&(this.status==='all'||d.status===this.status));},
        get phases(){return [...new Set(this.items.map(d=>d.phase))].filter(Boolean);},
        get grouped(){const groups=new Map();for(const doc of this.visible){if(!groups.has(doc.group_id))groups.set(doc.group_id,{id:doc.group_id,group:doc.group,documents:[]});groups.get(doc.group_id).documents.push(doc);}return [...groups.values()];},
        review(doc){this.selected=doc;this.feedback=doc.feedback||'';this.reviewStatus=['APPROVED','REJECTED'].includes(doc.status)?doc.status:'APPROVED';this.errors={};this.open('document-review');},
        async submit(){if(this.selected)await this.mutate('/dosen/documents/'+this.selected.id,{status:this.reviewStatus,feedback:this.feedback},'PUT','document-review');},
        async downloadDocument(doc){if(this.downloading)return;this.downloading=doc.id;try{download(await api('/dosen/documents/'+doc.id+'/download',{blob:true}),doc.file_name||`${doc.phase}-v${doc.version}.pdf`);}catch(e){notify(e.message,true);}finally{this.downloading=null;}},
    });
}

export function lecturerRequests(){
    return workspace({items:[],
        async load(){this.loading=true;this.error='';try{const [groups,periods]=await Promise.all([api('/dosen/groups/pending'),api('/periods-list')]);this.items=rows(groups);this.periods=rows(periods);}catch(e){this.error=e.message;}finally{this.loading=false;}},
        get filtered(){return this.items.filter(g=>this.matches(g)&&this.inPeriod(g.period_id));},
    });
}
