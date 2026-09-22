import {basePage,api,rows,unwrap,query,dialog,allRows,mergePage,notify} from './shared.js';
import {context} from '../../api.js';
export function adminGroups(detail=false){return mergePage(basePage(),{detail,group:null,lecturers:[],supervisorId:'',status:'',sortBy:'',allItems:[],selectedMembers:[],messageText:'',flagTarget:null,flagReason:'',unflagTarget:null,deleteTarget:null,deleteReason:'',
    async init(){try{await this.periodsLoad(false);if(detail){this.lecturers=await allRows('/admin/users?role=dosen');await this.load();}else{await this.loadAll();}}catch(e){this.error=e.message;this.loading=false;}},
    async load(){this.loading=true;this.error='';try{if(detail)this.group=unwrap(await api('/admin/groups/'+context.params.id));else{await this.loadAll();}}catch(e){this.error=e.message;}finally{this.loading=false;}},
    async loadAll(){this.loading=true;this.error='';try{this.allItems=await allRows('/admin/groups',{period_id:this.periodId,status:this.status});this.items=this.allItems;this.page=1;}catch(e){this.error=e.message;}finally{this.loading=false;}},
    get sortedGroups(){const list=[...this.filtered];if(this.sortBy==='code')list.sort((a,b)=>String(a.code||'').localeCompare(String(b.code||'')));else if(this.sortBy==='status')list.sort((a,b)=>String(a.status||'').localeCompare(String(b.status||'')));return list;},
    get groupTotal(){return this.filtered.length;},
    get groupLastPage(){return Math.max(1,Math.ceil(this.groupTotal/Number(this.pageSize)));},
    get groupPage(){return Math.min(this.page,this.groupLastPage);},
    get pagedGroups(){return this.sortedGroups.slice((this.groupPage-1)*Number(this.pageSize),this.groupPage*Number(this.pageSize));},
    get groupFrom(){return this.groupTotal?(this.groupPage-1)*Number(this.pageSize)+1:0;},
    get groupTo(){return Math.min(this.groupPage*Number(this.pageSize),this.groupTotal);},
    get groupPageList(){const last=this.groupLastPage,current=this.groupPage;if(last<=5)return Array.from({length:last},(_,i)=>i+1);if(current<=2)return [1,2,3,'…',last];if(current>=last-1)return [1,'…',last-2,last-1,last];return [1,'…',current,'…',last];},
    async assign(){if(!this.supervisorId)return;if(await this.run(()=>api(`/admin/groups/${this.group.id}/assign-supervisor-2`,{method:'POST',body:{supervisor_2_id:Number(this.supervisorId)}})))await this.load();},
    ketua(item){const members=item.members||[];return members.find(m=>m.is_leader)||members[0]||null;},
    ketuaName(item){const k=this.ketua(item);return k?(k.student?.name||k.student?.user?.name||'—'):'—';},
    memberName(m){return m.student?.name||m.student?.user?.name||'—';},
    initials(name){return String(name||'?').trim().split(/\s+/).slice(0,2).map(w=>w.charAt(0).toUpperCase()).join('');},
    supervisorNames(item){const names=[];const push=n=>{if(n&&!names.includes(n))names.push(n);};push(item.supervisor1?.name||item.supervisor1?.user?.name);push(item.supervisor2?.name||item.supervisor2?.user?.name);for(const s of item.supervisions||[])push(s.supervisor?.name||s.supervisor?.user?.name);return names.filter(Boolean);},
    groupStatusClass(status){return ['FORMING','FORMING_SOLO','READY_FOR_BIDDING'].includes(status)?'bg-red-50 text-red-600 border border-red-200':['READY_FOR_FINALIZATION','TITLE_APPROVED','TITLE_PROPOSED'].includes(status)?'bg-amber-50 text-amber-700 border border-amber-200':'bg-emerald-50 text-emerald-700 border border-emerald-200';},
    groupStatusLabel(item){return item.status_label||String(item.status||'Unknown').replace(/_/g,' ');},
    longDate(value){return value?new Date(value).toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric'}):'—';},
    get phases(){return this.group?.workflow?.phases||[];},
    get progressPct(){return this.group?.progress_percentage??0;},
    phaseLabel(phase){return {PDC1:'PDC 1',SEMPRO:'Sempro',PDC2:'PDC 2',TA_DRAFT:'TA Draft',EXPO:'Expo'}[phase]||phase;},
    phasePill(status){return status==='completed'?'bg-emerald-50 text-emerald-600':['submitted','revision','draft','unlocked'].includes(status)?'bg-amber-50 text-amber-600':'bg-slate-100 text-slate-500';},
    phasePillLabel(status){return status==='completed'?'Selesai':['submitted','revision','draft','unlocked'].includes(status)?'Proses':'Belum';},
    docRowClass(status){return status==='APPROVED'?'text-slate-700':status==='REJECTED'||status==='SUBMITTED'?'text-amber-600':'text-slate-400';},
    toggleMember(id){this.selectedMembers=this.selectedMembers.includes(id)?this.selectedMembers.filter(i=>i!==id):[...this.selectedMembers,id];},
    openMessage(){if(!this.selectedMembers.length)return;this.messageText='';dialog('group-message').showModal();},
    async sendMessage(){if(!this.messageText.trim()||!this.selectedMembers.length)return;const members=(this.group?.members||[]).filter(m=>this.selectedMembers.includes(m.id));const userIds=members.map(m=>m.student?.user_id).filter(Boolean);if(await this.run(()=>api(`/admin/groups/${this.group.id}/message`,{method:'POST',body:{user_ids:userIds,message:this.messageText.trim()}}),'Pesan dikirim.')){dialog('group-message').close();this.selectedMembers=[];this.messageText='';}},
    openFlag(member){this.flagTarget=member;this.flagReason='';dialog('group-flag').showModal();},
    async sendFlag(){if(!this.flagReason.trim()||!this.flagTarget)return;if(await this.run(()=>api(`/admin/groups/${this.group.id}/members/${this.flagTarget.id}/flag`,{method:'POST',body:{reason:this.flagReason.trim()}}),'Mahasiswa di-flag.')){dialog('group-flag').close();this.flagTarget=null;this.flagReason='';await this.load();}},
    openUnflag(member){this.unflagTarget=member;dialog('group-unflag').showModal();},
    async sendUnflag(){if(!this.unflagTarget)return;if(await this.run(()=>api(`/admin/groups/${this.group.id}/members/${this.unflagTarget.id}/unflag`,{method:'POST'}),'Mahasiswa dikembalikan.')){dialog('group-unflag').close();this.unflagTarget=null;await this.load();}},
    openDelete(item){this.deleteTarget=item||this.group;this.deleteReason='';dialog('group-delete').showModal();},
    closeDelete(){dialog('group-delete').close();dialog('group-delete-confirm').close();this.deleteTarget=null;this.deleteReason='';},
    get deleteReasonValid(){return this.deleteReason.trim().length>=10;},
    confirmDelete(){if(!this.deleteTarget||!this.deleteReasonValid)return;dialog('group-delete').close();dialog('group-delete-confirm').showModal();},
    backToDeleteReason(){dialog('group-delete-confirm').close();dialog('group-delete').showModal();},
    async sendDelete(){
        if(!this.deleteTarget||!this.deleteReasonValid)return;
        const id=this.deleteTarget.id;
        if(await this.run(()=>api(`/admin/groups/${id}`,{method:'DELETE',body:{reason:this.deleteReason.trim()}}),'Kelompok dihapus permanen.')){
            const wasDetail=this.detail;this.closeDelete();
            if(wasDetail)location.href=this.url('/admin/groups');else await this.loadAll();
        }
    },
});}
export function expoAdmin(){return mergePage(basePage(),{editing:null,form:{},action:'',eofficeRooms:[],
    async init(){try{await this.periodsLoad(false);this.eofficeRooms=rows(await api('/locations/eoffice-rooms'));await this.load();}catch(e){this.error=e.message;this.loading=false;}},
    async load(){this.loading=true;this.error='';try{this.items=rows(await api('/admin/expo-events'+query({period_id:this.periodId})));}catch(e){this.error=e.message;}finally{this.loading=false;}},
    edit(item=null){this.editing=item;this.errors={};this.form={period_id:String(item?.period_id||this.periodId||''),name:item?.name||'',date:(item?.date||'').slice(0,10),start_time:(item?.start_time||'').slice(0,5),end_time:(item?.end_time||'').slice(0,5),eoffice_ruangan_id:String(item?.eoffice_ruangan_id||''),capacity:item?.capacity||20,is_published:item?.is_published||false};dialog('expo-form').showModal();},
    async save(){if(await this.run(()=>api('/admin/expo-events'+(this.editing?'/'+this.editing.id:''),{method:this.editing?'PUT':'POST',body:{...this.form,period_id:Number(this.form.period_id),eoffice_ruangan_id:Number(this.form.eoffice_ruangan_id),capacity:Number(this.form.capacity)}}))){dialog('expo-form').close();await this.load();}},
    confirm(item,action){this.selected=item;this.action=action;dialog('expo-confirm').showModal();},
    async apply(){const endpoint='/admin/expo-events/'+this.selected.id;if(await this.run(()=>api(endpoint+(this.action==='publish'?'/publish':''),{method:this.action==='publish'?'PUT':'DELETE'}))){dialog('expo-confirm').close();await this.load();}},
});}
export function semproAdmin(){return mergePage(basePage(),{groups:[],lecturers:[],eofficeRooms:[],form:{},mode:'create',reason:'',
    async init(){try{await this.periodsLoad(false);const [groups,lecturers,eofficeRooms]=await Promise.all([allRows('/admin/groups'),allRows('/admin/users?role=dosen'),api('/locations/eoffice-rooms')]);this.groups=groups;this.lecturers=rows(lecturers);this.eofficeRooms=rows(eofficeRooms);await this.load();}catch(e){this.error=e.message;this.loading=false;}},
    async load(){this.loading=true;this.error='';try{const schedules=rows(await api('/admin/sempro/schedules'));this.items=schedules.filter(s=>!this.periodId||String(s.group?.period_id)===String(this.periodId));}catch(e){this.error=e.message;}finally{this.loading=false;}},
    get eligible(){return this.groups.filter(g=>g.status==='READY_FOR_SEMPRO'&&(!this.periodId||String(g.period_id)===String(this.periodId)));},
    edit(item=null,mode=null){this.selected=item;this.mode=mode || (item?'approve':'create');this.errors={};this.form={group_id:item?.group_id||'',date:(item?.date||'').slice(0,10),start_time:(item?.start_time||'').slice(0,5),end_time:(item?.end_time||'').slice(0,5),eoffice_ruangan_id:String(item?.eoffice_ruangan_id||''),examiner_1_id:item?.examiner_1_id||'',examiner_2_id:item?.examiner_2_id||''};dialog('sempro-form').showModal();},
    async save(){const body={...this.form,group_id:Number(this.form.group_id),eoffice_ruangan_id:Number(this.form.eoffice_ruangan_id),examiner_1_id:Number(this.form.examiner_1_id),examiner_2_id:Number(this.form.examiner_2_id)};if(await this.run(()=>api(this.mode==='create'?'/admin/sempro/schedule':`/admin/sempro/schedules/${this.selected.id}`+(this.mode==='approve'?'/approve':''),{method:this.mode==='create'?'POST':'PUT',body}))){dialog('sempro-form').close();await this.load();}},
    cancel(item){this.selected=item;dialog('sempro-cancel').showModal();},
    async cancelSave(){if(await this.run(()=>api(`/admin/sempro/schedules/${this.selected.id}/cancel`,{method:'PUT'}))){dialog('sempro-cancel').close();await this.load();}},
    reject(item){this.selected=item;this.reason='';dialog('sempro-reject').showModal();},
    async rejectSave(){if(!this.reason.trim())return;if(await this.run(()=>api(`/admin/sempro/schedules/${this.selected.id}/reject`,{method:'PUT',body:{rejection_reason:this.reason.trim()}}))){dialog('sempro-reject').close();await this.load();}},
});}
export function finalizationAdmin(){return mergePage(basePage(),{
    tab:'ready',subTab:'no_group',supervisorStatus:'all',memberCount:'all',
    stats:null,flow:null,pagination:{current_page:1,last_page:1,total:0,per_page:20},
    multiplePeriods:false,execConfirm:false,periodFlagConfirm:false,activatePdc1:true,
    lecturers:[],availTitles:[],availGroups:[],
    selectedIds:[],noGroupSelected:[],
    svForm:{group_ids:[],supervisor_1_id:'',supervisor_2_id:'',notes:'',mark_final:false,isReady:false,svDefaultName:''},svError:'',
    titleForm:{group_id:'',group_code:'',title_id:''},
    manualForm:{option:'no_title',title_id:'',newTitle:{title:'',description:'',lecturer_id:''}},
    addForm:{group_id:''},reasonForm:{reason:''},
    rollbackIds:[],cancelTarget:null,forceTarget:null,biddingAction:'',autoFixMode:'safe',
    searchTimer:null,
    get isGroupView(){return this.tab!=='others'||this.subTab!=='no_group';},
    get overloadedLecturers(){return (this.lecturers||[]).filter(l=>l.is_overloaded);},
    get svOverloadWarning(){
        const ids=[this.svForm.supervisor_1_id,this.svForm.supervisor_2_id].filter(Boolean).map(String);
        return ids.some(id=>(this.lecturers||[]).some(l=>String(l.id)===id&&l.is_overloaded));
    },
    async init(){
        try{
            const params=new URLSearchParams(location.search);
            if(params.get('tab'))this.tab=params.get('tab');
            if(params.get('sub_tab'))this.subTab=params.get('sub_tab');
            if(params.get('search'))this.search=params.get('search');
            if(params.get('page'))this.page=Number(params.get('page'))||1;
            if(params.get('per_page'))this.pageSize=Number(params.get('per_page'))||20;
            await this.periodsLoad(false);
            if(params.get('period_id'))this.periodId=params.get('period_id');
            else if(!this.periodId)this.periodId=String(this.periods.find(p=>p.is_active)?.id||this.periods[0]?.id||'');
            if(this.periodId){await this.loadLecturers();await this.load();}
            else this.loading=false;
        }catch(e){this.error=e.message;this.loading=false;}
    },
    syncUrl(){
        const params=new URLSearchParams({period_id:this.periodId,tab:this.tab,sub_tab:this.subTab,search:this.search,page:String(this.page),per_page:String(this.pageSize)});
        history.replaceState(null,'',location.pathname+'?'+params);
    },
    onSearch(){clearTimeout(this.searchTimer);this.searchTimer=setTimeout(()=>{this.page=1;this.load();},500);},
    setTab(tab,sub=null){this.tab=tab;if(sub)this.subTab=sub;this.page=1;this.selectedIds=[];this.noGroupSelected=[];this.load();},
    setSubTab(sub){this.subTab=sub;this.page=1;this.selectedIds=[];this.noGroupSelected=[];this.load();},
    gotoPage(page){page=Math.min(Math.max(1,page),this.pagination.last_page||1);if(page!==this.page){this.page=page;this.load();}},
    async load(){
        this.loading=true;this.error='';
        try{
            if(!this.periodId){this.items=[];return;}
            const suffix=query({period_id:this.periodId,tab:this.tab,sub_tab:this.subTab,search:this.search,supervisor_status:this.supervisorStatus,member_count:this.memberCount,page:this.page,per_page:this.pageSize});
            const body=unwrap(await api('/admin/finalization/dashboard'+suffix));
            this.stats=body.stats||null;this.flow=body.flow||null;
            const paginator=body.data||{};
            this.items=Array.isArray(paginator.data)?paginator.data:[];
            this.pagination={current_page:paginator.current_page||1,last_page:paginator.last_page||1,total:paginator.total||0,per_page:paginator.per_page||this.pageSize};
            this.page=this.pagination.current_page;
            this.selectedIds=[];this.noGroupSelected=[];
            this.syncUrl();
        }catch(e){
            if(String(e.message||'').includes('Multiple active periods')){this.multiplePeriods=true;this.error='';}
            else this.error=e.message;
        }finally{this.loading=false;}
    },
    async loadLecturers(){
        try{
            const body=unwrap(await api('/admin/finalization/lecturers'+query({period_id:this.periodId})));
            this.lecturers=body?.lecturers||[];
        }catch(e){notify(e.message,true);}
    },
    async refreshAll(){await this.loadLecturers();await this.load();},
    memberNames(item){return (item.members||[]).map(m=>m.student?.name||m.student?.user?.name||'').filter(Boolean).slice(0,3).join(', ');},
    readiness(item){
        const min=Number(item.period?.min_group_size ?? 3);
        const max=Number(item.period?.max_group_size ?? 4);
        const n=(item.members||[]).length;
        return [
            {label:'Judul',ok:!!(item.title_id||item.title)},
            {label:`Anggota ${n}/${min}–${max}`,ok:n>=min&&n<=max},
            {label:'SV1',ok:!!item.supervisor_1_id},
            {label:'SV2',ok:!!item.supervisor_2_id},
        ];
    },
    groupStatusClass(status){return ['FORMING','FORMING_SOLO','READY_FOR_BIDDING'].includes(status)?'bg-red-50 text-red-600 border-red-200':['READY_FOR_FINALIZATION','TITLE_APPROVED','TITLE_PROPOSED'].includes(status)?'bg-amber-50 text-amber-700 border-amber-200':'bg-emerald-50 text-emerald-700 border-emerald-200';},
    toggleId(id){this.selectedIds=this.selectedIds.includes(id)?this.selectedIds.filter(i=>i!==id):[...this.selectedIds,id];},
    toggleAll(checked){this.selectedIds=checked?this.items.map(i=>i.id):[];},
    toggleStudent(id){this.noGroupSelected=this.noGroupSelected.includes(id)?this.noGroupSelected.filter(i=>i!==id):[...this.noGroupSelected,id];},
    toggleAllStudents(checked){this.noGroupSelected=checked?this.items.map(i=>i.id):[];},
    svDefaultFor(item){return item.supervisor_1_id?{id:String(item.supervisor_1_id),name:''}:{id:item.suggested_supervisor_1_id?String(item.suggested_supervisor_1_id):'',name:item.suggested_supervisor_1_name||''};},
    openSingleSv(item){this.svError='';const d=this.svDefaultFor(item);this.svForm={group_ids:[item.id],supervisor_1_id:d.id,supervisor_2_id:item.supervisor_2_id?String(item.supervisor_2_id):'',notes:'',mark_final:false,isReady:item.status==='READY_FOR_FINALIZATION',svDefaultName:d.name};dialog('fin-set-sv').showModal();},
    openMarkFinal(item){this.svError='';const d=this.svDefaultFor(item);this.svForm={group_ids:[item.id],supervisor_1_id:d.id,supervisor_2_id:item.supervisor_2_id?String(item.supervisor_2_id):'',notes:'',mark_final:true,isReady:item.status==='READY_FOR_FINALIZATION',svDefaultName:d.name};dialog('fin-set-sv').showModal();},
    openBatchSv(){if(!this.selectedIds.length)return;this.svError='';this.svForm={group_ids:[...this.selectedIds],supervisor_1_id:'',supervisor_2_id:'',notes:'',mark_final:false,isReady:false,svDefaultName:''};dialog('fin-set-sv').showModal();},
    async saveSupervisors(){
        const sv1=this.svForm.supervisor_1_id?Number(this.svForm.supervisor_1_id):null;
        const sv2=this.svForm.supervisor_2_id?Number(this.svForm.supervisor_2_id):null;
        if(!sv1){this.svError='Pembimbing 1 wajib dipilih.';return;}
        if(sv2&&sv2===sv1){this.svError='Pembimbing 1 dan 2 harus berbeda.';return;}
        const markFinal=this.svForm.group_ids.length===1&&!!this.svForm.mark_final;
        if(markFinal&&!sv2){this.svError='Kelompok Final wajib SV1 dan SV2 terisi.';return;}
        this.svError='';
        const body={supervisor_1_id:sv1,supervisor_2_id:sv2,notes:this.svForm.notes||null,mark_final:markFinal};
        const done=await this.run(async()=>{
            if(this.svForm.group_ids.length>1){await api('/admin/finalization/batch-set-supervisor',{method:'POST',body:{...body,group_ids:this.svForm.group_ids}});}
            else{await api('/admin/finalization/set-supervisor',{method:'POST',body:{...body,group_id:this.svForm.group_ids[0]}});}
        },markFinal?'Kelompok ditandai sebagai Kelompok Final.':'Pembimbing ditetapkan.');
        if(done){dialog('fin-set-sv').close();await this.refreshAll();
            if(markFinal){const s=this.stats||{};if((s.total_ready||0)===0&&(s.total_no_title||0)===0&&(s.total_not_ready||0)===0&&(s.total_kelompok_final||0)>0){notify('Semua kelompok sudah Kelompok Final. Klik \u2018Finalisasi Periode\u2019 untuk mengunci periode dan lanjut ke PDC1.');}}
        }
    },
    async openAssignTitle(item){
        this.titleForm={group_id:item.id,group_code:item.code||('Kelompok #'+item.id),title_id:''};
        try{const body=unwrap(await api('/admin/finalization/available-titles'+query({period_id:this.periodId})));this.availTitles=body?.titles||[];}catch(e){notify(e.message,true);return;}
        dialog('fin-assign-title').showModal();
    },
    async saveAssignTitle(){
        if(!this.titleForm.title_id)return;
        if(await this.run(()=>api('/admin/finalization/assign-title',{method:'POST',body:{group_id:Number(this.titleForm.group_id),title_id:Number(this.titleForm.title_id)}}),'Judul ditetapkan.')){dialog('fin-assign-title').close();await this.load();}
    },
    async promote(item){
        if(await this.run(()=>api('/admin/finalization/promote-to-ready',{method:'POST',body:{group_id:item.id}}),'Grup dipromosikan.'))await this.load();
    },
    async openManual(){
        if(!this.noGroupSelected.length)return;
        this.manualForm={option:'no_title',title_id:'',newTitle:{title:'',description:'',lecturer_id:''}};
        try{
            const [titles]=await Promise.all([api('/admin/finalization/available-titles'+query({period_id:this.periodId}))]);
            this.availTitles=unwrap(titles)?.titles||[];
        }catch(e){notify(e.message,true);return;}
        dialog('fin-manual').showModal();
    },
    async saveManual(){
        const body={student_ids:this.noGroupSelected.map(Number),period_id:Number(this.periodId),option:this.manualForm.option};
        if(this.manualForm.option==='assign_title'){if(!this.manualForm.title_id)return;body.title_id=Number(this.manualForm.title_id);}
        if(this.manualForm.option==='add_title'){
            if(!this.manualForm.newTitle.title.trim()||!this.manualForm.newTitle.lecturer_id)return;
            body.new_title={title:this.manualForm.newTitle.title.trim(),description:this.manualForm.newTitle.description||null,specializations:[],lecturer_id:Number(this.manualForm.newTitle.lecturer_id)};
        }
        if(await this.run(()=>api('/admin/finalization/create-manual-group',{method:'POST',body}),'Grup manual dibuat.')){dialog('fin-manual').close();this.noGroupSelected=[];await this.load();}
    },
    async openAddExisting(){
        if(!this.noGroupSelected.length)return;
        this.addForm={group_id:''};
        try{const body=unwrap(await api('/admin/finalization/available-groups'+query({period_id:this.periodId})));this.availGroups=body?.groups||[];}catch(e){notify(e.message,true);return;}
        dialog('fin-add-existing').showModal();
    },
    async saveAddExisting(){
        if(!this.addForm.group_id)return;
        if(await this.run(()=>api('/admin/finalization/add-to-existing-group',{method:'POST',body:{group_id:Number(this.addForm.group_id),student_ids:this.noGroupSelected.map(Number)}}),'Anggota ditambahkan.')){dialog('fin-add-existing').close();this.noGroupSelected=[];await this.load();}
    },
    openPeriodFlag(){this.periodFlagConfirm=false;this.activatePdc1=true;dialog('fin-period-flag').showModal();},
    async doPeriodFlag(){
        if(await this.run(()=>api('/admin/finalization/finalize-period-flag',{method:'POST',body:{period_id:Number(this.periodId),confirmation:true,activate_pdc1:this.activatePdc1}}),'Periode difinalisasi.')){dialog('fin-period-flag').close();this.periodFlagConfirm=false;await this.refreshAll();}
    },
    openRollback(){this.reasonForm={reason:''};this.rollbackIds=[...this.selectedIds];dialog('fin-rollback').showModal();},
    async doRollback(){
        if(!this.reasonForm.reason.trim()||this.reasonForm.reason.trim().length<10)return;
        if(await this.run(()=>api('/admin/finalization/rollback',{method:'POST',body:{period_id:Number(this.periodId),group_ids:this.rollbackIds,reason:this.reasonForm.reason.trim()}}),'Rollback berhasil.')){dialog('fin-rollback').close();await this.load();}
    },
    openCancel(item){this.cancelTarget=item;this.reasonForm={reason:''};dialog('fin-cancel').showModal();},
    async doCancel(){
        if(await this.run(()=>api('/admin/finalization/cancel-kelompok-final',{method:'POST',body:{period_id:Number(this.periodId),group_id:this.cancelTarget.id,reason:this.reasonForm.reason.trim()||null}}),'Kelompok final dibatalkan.')){dialog('fin-cancel').close();await this.load();}
    },
    openReopen(){this.execConfirm=false;dialog('fin-reopen').showModal();},
    async doReopen(){
        if(await this.run(()=>api('/admin/finalization/reopen',{method:'POST',body:{period_id:Number(this.periodId)}}),'Periode dibuka kembali.')){dialog('fin-reopen').close();this.execConfirm=false;await this.refreshAll();}
    },
    openAutoFix(){dialog('fin-autofix').showModal();},
    async doAutoFix(){
        if(await this.run(()=>api('/admin/finalization/auto-fix',{method:'POST',body:{period_id:Number(this.periodId),mode:this.autoFixMode}}),'Auto-fix selesai.')){dialog('fin-autofix').close();await this.load();}
    },
    openForceReady(item){this.forceTarget=item;this.reasonForm={reason:''};dialog('fin-force').showModal();},
    async doForceReady(){
        if(!this.reasonForm.reason.trim()||this.reasonForm.reason.trim().length<10)return;
        if(await this.run(()=>api('/admin/finalization/force-ready',{method:'POST',body:{group_id:this.forceTarget.id,reason:this.reasonForm.reason.trim()||null}}),'Grup dipaksa ready.')){dialog('fin-force').close();await this.load();}
    },
    confirmBidding(action){this.biddingAction=action;dialog('fin-bidding').showModal();},
    async doBidding(){
        if(await this.run(()=>api('/admin/finalization/'+this.biddingAction,{method:'POST',body:{period_id:Number(this.periodId)}}))){dialog('fin-bidding').close();await this.periodsLoad(false);await this.load();}
    },
    async doExport(format){
        try{
            const blob=await api('/admin/finalization/export'+query({period_id:this.periodId,format}),{blob:true});
            const {download}=await import('../../api.js');
            download(blob,`finalisasi_periode_${this.periodId}.${format==='excel'?'csv':'html'}`);
            notify('Laporan diunduh.');
        }catch(e){notify(e.message,true);}
    },
});}
