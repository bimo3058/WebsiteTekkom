import {calendarDays,localDateKey,eventColors,eventLabels,normalizeSchedule} from '../calendar.js';
import {api,context,notify,url,rows,unwrap,date} from '../api.js';
export function registerDashboards(Alpine) {
    Alpine.data('capstoneDashboard',()=>({
        loading:true,error:'',data:{},groups:[],periods:[],selectedPeriod:'all',pending:0,group:null,workflow:{},schedules:[],date,url,
        jadwalView:'calendar',jadwalSearch:'',typeFilter:'all',statusFilter:'all',tablePage:1,tablePerPage:10,sortDirection:1,
        month:new Date().getMonth(),year:new Date().getFullYear(),selectedDate:localDateKey(new Date()),
        selected:null,rejecting:null,reason:'',saving:false,
        allGroups:[],groupSearch:'',groupStatus:'',groupSort:'newest',groupPage:1,groupPerPage:3,groupLoading:false,groupSelected:[],
        docPage:1,docPerPage:5,
        activityRange:'30',hoverIdx:-1,        get progress(){const phases=this.workflow?.phases || [];return phases.length ? Math.round(phases.filter(p=>p.status==='completed').length/phases.length*100) : 0;},
        get dashboardDocuments(){
            const rows=[];
            for(const phase of this.workflow?.phases || []){
                for(const doc of phase.documents || []){
                    rows.push({name:doc.type,type:doc.type,phase:phase.phase,status:doc.status || 'missing',
                        can_upload:doc.can_upload === true,
                        locked_reason:doc.locked_reason || null,
                        latest_document:doc.latest_document || null});
                }
            }
            return rows;
        },
        get docTotal(){return this.dashboardDocuments.length;},
        get docTotalPages(){return Math.max(1,Math.ceil(this.docTotal/this.docPerPage));},
        get docCurrentPage(){return Math.min(this.docPage,this.docTotalPages);},
        get pagedDocuments(){return this.dashboardDocuments.slice((this.docCurrentPage-1)*this.docPerPage,this.docCurrentPage*this.docPerPage);},
        get docFrom(){return this.docTotal ? (this.docCurrentPage-1)*this.docPerPage+1 : 0;},
        get docTo(){return Math.min(this.docCurrentPage*this.docPerPage,this.docTotal);},
        get docPageList(){
            const last=this.docTotalPages,current=Math.min(this.docPage,last);
            if(last<=5)return Array.from({length:last},(_,i)=>i+1);
            if(current<=2)return [1,2,3,'…',last];
            if(current>=last-1)return [1,'…',last-2,last-1,last];
            return [1,'…',current,'…',last];
        },
        documentStatus(status){return {missing:'Belum Upload',SUBMITTED:'Terkirim',APPROVED:'Disetujui',REJECTED:'Perlu Revisi',DRAFT:'Draft'}[status] || status;},
        documentStatusClass(status){return {missing:'border-red-200 bg-red-50 text-red-600',SUBMITTED:'border-blue-200 bg-blue-50 text-blue-600',APPROVED:'border-emerald-200 bg-emerald-50 text-emerald-700',REJECTED:'border-amber-200 bg-amber-50 text-amber-700'}[status] || 'border-slate-200 bg-slate-50 text-slate-600';},
        documentHref(doc){return url('/mahasiswa/documents')+'?phase='+encodeURIComponent(doc.phase)+'&type='+encodeURIComponent(doc.type)+(doc.latest_document?.id ? '&document='+encodeURIComponent(doc.latest_document.id) : '');},
        get activePeriodsCount(){return this.data.active_periods_count ?? (this.data.active_periods || []).length ?? 0;},
        get pendingApproval(){return this.data.pending_approval ?? this.data.pending_finalization ?? 0;},
        get filteredSchedules(){return this.schedules.filter(s=>this.selectedPeriod==='all' || String(s.period_id || s.group?.period_id)===String(this.selectedPeriod));},
        get jadwalFiltered(){
            const term=this.jadwalSearch.toLocaleLowerCase();
            return this.filteredSchedules.filter(s=>(this.typeFilter==='all'||s.type===this.typeFilter) && (this.statusFilter==='all'||s.status===this.statusFilter)
                && (!term || [s.group?.title?.title,s.student_name,s.room,s.type,s.period_name].some(v=>String(v||'').toLocaleLowerCase().includes(term))));
        },
        get jadwalRows(){return [...this.jadwalFiltered].sort((a,b)=>((localDateKey(a.date)+(a.start_time||'')).localeCompare(localDateKey(b.date)+(b.start_time||'')))*this.sortDirection);},
        get jadwalTotalPages(){return Math.max(1,Math.ceil(this.jadwalRows.length/this.tablePerPage));},
        get jadwalCurrentPage(){return Math.min(this.tablePage,this.jadwalTotalPages);},
        get jadwalVisible(){return this.jadwalRows.slice((this.jadwalCurrentPage-1)*this.tablePerPage,this.jadwalCurrentPage*this.tablePerPage);},
        get kanbanGroups(){
            const order=['SEMPRO','TA_DEFENSE','EXPO','BIMBINGAN'];
            return order.map(type=>({type,items:this.jadwalFiltered.filter(s=>s.type===type)}));
        },
        get days(){return calendarDays(this.year,this.month,1);},
        get monthLabel(){return new Date(this.year,this.month,1).toLocaleDateString('id-ID',{month:'long',year:'numeric'});},
        get eventsByDate(){
            const grouped={};
            for(const event of this.jadwalFiltered){const key=localDateKey(event.date);if(!key)continue;(grouped[key]??=[]).push(event);}
            for(const events of Object.values(grouped))events.sort((a,b)=>(a.start_time||'').localeCompare(b.start_time||''));
            return grouped;
        },
        get dayEvents(){return this.eventsByDate[this.selectedDate] || [];},
        get selectedLabel(){return new Date(this.selectedDate+'T12:00:00').toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});},
        eventsFor(day){return this.eventsByDate[typeof day === 'string' ? day : day.key] || [];},
        dashboardEventPill(type){return ['SEMPRO','TA_DEFENSE','SIDANG'].includes(type) ? 'border-l-[#2f3d8a] bg-[#eef0f7] text-slate-700' : 'border-l-amber-400 bg-[#fef8e2] text-slate-700';},
        color(type){return eventColors[type] || eventColors.BIMBINGAN;},
        label(type){return eventLabels[type] || type;},
        statusColor(status){return ['APPROVED','SCHEDULED'].includes(status) ? 'bg-green-100 text-green-700' : ['COMPLETED','DONE'].includes(status) ? 'bg-blue-100 text-blue-700' : ['REJECTED','CANCELLED'].includes(status) ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700';},
        time(event){const start=(event.start_time||'').slice(0,5),end=(event.end_time||'').slice(0,5);return start ? start+(end?' — '+end:'') : 'Time not set';},
        title(event){return event.student_name || event.group?.title?.title || this.label(event.type);},
        students(event){return event.students?.map(s=>s.name).join(', ') || event.student_name || '';},
        examiners(event){return event.examiners?.map(e=>e.name || e.examiner?.name).filter(Boolean).join(', ') || [event.examiner1?.name,event.examiner2?.name].filter(Boolean).join(', ');},
        safeLink(link){try{const u=new URL(link);return ['http:','https:'].includes(u.protocol)?u.href:'#';}catch{return '#';}},
        canApprove(event){return context.role==='admin' && ['SEMPRO','EXPO','TA_DEFENSE'].includes(event?.type) && ['PENDING','PENDING_APPROVAL'].includes(event?.status);},
        move(direction){const next=new Date(this.year,this.month+direction,1);this.year=next.getFullYear();this.month=next.getMonth();},
        today(){const now=new Date();this.year=now.getFullYear();this.month=now.getMonth();this.selectedDate=localDateKey(now);},
        detail(event){this.selected=event;document.getElementById('schedule-detail')?.showModal();},
        openDay(day){this.selectedDate=typeof day === 'string' ? day : day.key;document.getElementById('schedule-day')?.showModal();},
        async approve(event){
            if(!this.canApprove(event)||this.saving)return;
            this.saving=true;
            try{
                const body={date:localDateKey(event.date),start_time:event.start_time,end_time:event.end_time,room:event.room,
                    examiner_1_id:event.examiner_1_id||event.examiner1?.id,examiner_2_id:event.examiner_2_id||event.examiner2?.id};
                const endpoint=event.type==='TA_DEFENSE'?`/admin/ta-defense-schedules/${event._id}`:`/admin/${event.type.toLowerCase()}/schedules/${event._id}/approve`;
                if(event.type==='TA_DEFENSE')body.status='SCHEDULED';
                await api(endpoint,{method:'PUT',body});notify('Schedule approved!');document.getElementById('schedule-detail')?.close();await this.loadAdminSchedules();
            }catch(e){notify(e.message,true);}finally{this.saving=false;}
        },
        reject(event){if(!this.canApprove(event))return;this.rejecting=event;this.reason='';document.getElementById('schedule-reject')?.showModal();},
        async submitRejection(){
            if(!this.reason.trim()||this.saving||!this.rejecting)return;
            this.saving=true;
            try{
                const event=this.rejecting;
                const endpoint=event.type==='TA_DEFENSE'?`/admin/ta-defense-schedules/${event._id}/cancel`:`/admin/${event.type.toLowerCase()}/schedules/${event._id}/reject`;
                await api(endpoint,{method:'PUT',body:{rejection_reason:this.reason.trim()}});
                document.getElementById('schedule-reject')?.close();document.getElementById('schedule-detail')?.close();notify('Schedule request rejected.');await this.loadAdminSchedules();
            }catch(e){notify(e.message,true);}finally{this.saving=false;}
        },
        ketuaName(item){const members=item.members || [];const leader=members.find(m=>m.is_leader) || members[0];return leader?.student?.name || leader?.student?.user?.name || '—';},
        supervisorNames(item){
            const names=[];
            const push=name=>{if(name && !names.includes(name))names.push(name);};
            push(item.supervisor1?.name || item.supervisor1?.user?.name);
            push(item.supervisor2?.name || item.supervisor2?.user?.name);
            for(const s of item.supervisions || [])push(s.supervisor?.name || s.supervisor?.user?.name);
            return names.filter(Boolean);
        },
        groupStatusClass(status){return ['FORMING','FORMING_SOLO','READY_FOR_BIDDING'].includes(status) ? 'bg-red-50 text-red-600 border border-red-200' : ['READY_FOR_FINALIZATION','TITLE_APPROVED'].includes(status) ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200';},
        groupStatusLabel(item){return item.status_label || (item.status || '').charAt(0)+(item.status || '').slice(1).toLowerCase().replace(/_/g,' ') || 'Unknown';},
        initials(name){return String(name || '?').trim().split(/\s+/).slice(0,2).map(w=>w.charAt(0).toUpperCase()).join('');},
        phaseLabel(code){return {PDC1:'PDC 1',SEMPRO:'Seminar Proposal',PDC2:'PDC 2',TA_DRAFT:'TA Draft',TA:'Sidang TA',EXPO:'Expo',SIDANG:'Sidang TA'}[code] || code || '—';},
        memberDisplayName(member){return member?.student?.name || member?.student?.user?.name || '—';},
        async refreshMahasiswa(){this.loading=true;this.error='';try{
            const result=await Promise.allSettled([api('/mahasiswa/dashboard'),api('/mahasiswa/group'),api('/mahasiswa/all-schedules'),api('/mahasiswa/workflow')]);
            if(result[0].status==='rejected')throw result[0].reason;
            this.data=unwrap(result[0].value);
            const group=result[1].status==='fulfilled' ? unwrap(result[1].value) : null;
            this.group=group?.group || group;
            this.docPage=1;
            this.schedules=result[2].status==='fulfilled' ? rows(result[2].value).map(item=>normalizeSchedule(item,item.type)) : [];
            this.workflow=result[3].status==='fulfilled' ? unwrap(result[3].value) : this.data.workflow || {};
        }catch(e){this.error=e.message;}finally{this.loading=false;}},
        get filteredGroupItems(){
            const term=this.groupSearch.trim().toLocaleLowerCase();
            let items=this.allGroups;
            if(this.groupStatus)items=items.filter(i=>i.status===this.groupStatus);
            if(term)items=items.filter(i=>{
                const members=i.members || [];
                const haystack=[i.code,i.title?.title,
                    members.map(m=>m.student?.nim || m.student?.student_number).join(' '),
                    members.map(m=>m.student?.name || m.student?.user?.name).join(' '),
                    members.map(m=>m.student?.user?.email).join(' ')];
                return haystack.some(v=>String(v||'').toLocaleLowerCase().includes(term));
            });
            if(this.groupSort==='code')items=[...items].sort((a,b)=>String(a.code||'').localeCompare(String(b.code||'')));
            else if(this.groupSort==='status')items=[...items].sort((a,b)=>String(a.status||'').localeCompare(String(b.status||'')));
            return items;
        },
        get pagedGroups(){
            if(this.groupPage>this.groupLastPage)this.groupPage=this.groupLastPage;
            return this.filteredGroupItems.slice((this.groupPage-1)*this.groupPerPage,this.groupPage*this.groupPerPage);
        },
        get groupTotal(){return this.filteredGroupItems.length;},
        get groupFrom(){return this.groupTotal ? (this.groupPage-1)*this.groupPerPage+1 : 0;},
        get groupTo(){return Math.min(this.groupPage*this.groupPerPage,this.groupTotal);},
        get groupLastPage(){return Math.max(1,Math.ceil(this.groupTotal/this.groupPerPage));},
        get groupPageList(){
            const last=this.groupLastPage,current=Math.min(this.groupPage,last);
            if(last<=5)return Array.from({length:last},(_,i)=>i+1);
            if(current<=2)return [1,2,3,'…',last];
            if(current>=last-1)return [1,'…',last-2,last-1,last];
            return [1,'…',current,'…',last];
        },
        dosenSupervisors(item){const names=[item.dosbing_1_name,item.dosbing_2_name].filter(Boolean);return names.length?names:this.supervisorNames(item);},
        get activityDays(){const n=Number(this.activityRange)||30;const base=new Date();base.setHours(0,0,0,0);const days=[];for(let i=n-1;i>=0;i--){const d=new Date(base);d.setDate(d.getDate()-i);days.push(d);}return days;},
        get activityCounts(){const map={};for(const row of (this.data.activity_series||[])){map[String(row.day).slice(0,10)]=Number(row.count)||0;}return this.activityDays.map(d=>map[localDateKey(d)]||0);},
        get activityMax(){return Math.max(1,...this.activityCounts);},
        get activityAvg(){const c=this.activityCounts;return c.length?c.reduce((a,b)=>a+b,0)/c.length:0;},
        get activityPoints(){const W=600,H=200,P=10;const n=this.activityCounts.length;return this.activityCounts.map((c,i)=>{const x=n<2?W/2:P+(W-2*P)*i/(n-1);const y=H-P-(H-2*P)*(c/this.activityMax);return [x,y];});},
        get activityLine(){return this.activityPoints.map((p,i)=>(i?'L':'M')+p[0].toFixed(1)+' '+p[1].toFixed(1)).join(' ');},
        get activityArea(){return this.activityLine+' L590 200 L10 200 Z';},
        get activityAvgY(){const H=200,P=10;return H-P-(H-2*P)*(Math.min(this.activityAvg,this.activityMax)/this.activityMax);},
        get activityTicks(){const m=this.activityMax;return [4,3,2,1,0].map(k=>Math.round(m*k/4));},
        get activityFirstLabel(){const d=this.activityDays[0];return d?d.toLocaleDateString('en-US',{month:'short',day:'2-digit',year:'numeric'}):'';},
        get activityLastLabel(){const d=this.activityDays[this.activityDays.length-1];return d?d.toLocaleDateString('en-US',{month:'short',day:'2-digit',year:'numeric'}):'';},
        chartHover(event){const r=event.currentTarget.getBoundingClientRect();const n=this.activityCounts.length;if(!n)return;const i=Math.round(((event.clientX-r.left)/r.width)*(n-1));this.hoverIdx=Math.max(0,Math.min(n-1,i));},
        get activityHover(){const i=this.hoverIdx;if(i<0||!this.activityCounts.length||!this.activityPoints[i])return null;const d=this.activityDays[i];return {label:d.toLocaleDateString('en-US',{month:'short',day:'2-digit',year:'numeric'}),value:this.activityCounts[i],x:this.activityPoints[i][0]/600*100,y:this.activityPoints[i][1]/200*100};},
        get donutTotal(){return (Number(this.data.titles_available)||0)+(Number(this.data.titles_full)||0);},
        get donutPct(){return this.donutTotal?Math.round((Number(this.data.titles_available)||0)/this.donutTotal*100):0;},
        get donutStyle(){return this.donutTotal ? `background: conic-gradient(#8b8bd4 0 ${this.donutPct}%, #f0b429 ${this.donutPct}% 100)` : 'background: #e2e8f0';},
        get donutDash(){const C=2*Math.PI*70;return `${(C*this.donutPct/100).toFixed(1)} ${C.toFixed(1)}`;},
        async init(){await this.load();},
        async load(){this.loading=true;this.error='';try{
            const role=context.role;
            if(role==='admin'){await this.loadAdminAll();}
            else if(role==='dosen'){
                const query=this.selectedPeriod==='all' ? '' : '?period_id='+encodeURIComponent(this.selectedPeriod);
                const result=await Promise.all([api('/dosen/dashboard'+query),api('/dosen/groups/supervised'+query),api('/dosen/supervisor-evaluation/pending-count'),api('/dosen/all-schedules'+query)]);
                this.data=unwrap(result[0]);this.groups=rows(result[1]);this.pending=unwrap(result[2])?.count || 0;this.periods=this.data.available_periods || [];
                this.schedules=rows(result[3]).map(item=>normalizeSchedule(item,item.type)).filter(item=>localDateKey(item.date));
                this.allGroups=this.groups;this.groupPage=1;this.groupPerPage=2;this.groupSelected=[];this.hoverIdx=-1;
            }else{
                const period=unwrap(await api('/mahasiswa/my-period'));
                if(!period?.period){location.replace(url('/mahasiswa/registration'));return;}
                const result=await Promise.allSettled([api('/mahasiswa/dashboard'),api('/mahasiswa/group'),api('/mahasiswa/all-schedules'),api('/mahasiswa/workflow')]);
                if(result[0].status==='rejected')throw result[0].reason;
                this.data=unwrap(result[0].value);
                const group=result[1].status==='fulfilled' ? unwrap(result[1].value) : null;
                this.group=group?.group || group;
                this.schedules=result[2].status==='fulfilled' ? rows(result[2].value).map(item=>normalizeSchedule(item,item.type)) : [];
                this.workflow=result[3].status==='fulfilled' ? unwrap(result[3].value) : this.data.workflow || {};
            }
        }catch(e){this.error=e.message;}finally{this.loading=false;}},
        async loadAdminAll(){
            const query=this.selectedPeriod==='all' ? '' : '?period_id='+encodeURIComponent(this.selectedPeriod);
            const result=await Promise.all([api('/admin/dashboard'+query),api('/admin/all-schedules'),api('/admin/periods')]);
            this.data=unwrap(result[0]);
            this.groups=this.data.recent_groups || [];
            this.periods=rows(result[2]).length ? rows(result[2]) : (this.data.periods || []);
            this.schedules=rows(result[1]).map(item=>normalizeSchedule(item,item.type)).filter(item=>localDateKey(item.date));
            await this.loadAdminGroups();
        },
        async reloadAdmin(){
            this.loading=true;this.error='';
            try{
                const query=this.selectedPeriod==='all' ? '' : '?period_id='+encodeURIComponent(this.selectedPeriod);
                this.data=unwrap(await api('/admin/dashboard'+query));
                await Promise.all([this.loadAdminSchedules(),this.loadAdminGroups(true)]);
            }catch(e){this.error=e.message;}finally{this.loading=false;}
        },
        async loadAdminSchedules(){
            const list=rows(await api('/admin/all-schedules'));
            this.schedules=list.map(item=>normalizeSchedule(item,item.type)).filter(item=>localDateKey(item.date));
            this.tablePage=1;
        },
        async loadAdminGroups(refetch=false){
            if(!refetch && this.allGroups.length){this.groupPage=1;this.groupSelected=[];return;}
            this.groupLoading=true;
            try{
                const params=new URLSearchParams({per_page:'all'});
                if(this.selectedPeriod!=='all')params.set('period_id',this.selectedPeriod);
                const payload=unwrap(await api('/admin/groups?'+params.toString()));
                this.allGroups=rows(payload);
                this.groupPage=1;
                this.groupSelected=[];
            }catch(e){notify(e.message,true);}finally{this.groupLoading=false;}
        },
    }));
    Alpine.data('capstoneCalendar',()=>({
        month:new Date().getMonth(),year:new Date().getFullYear(),selectedDate:localDateKey(new Date()),
        get monthLabel(){return new Date(this.year,this.month,1).toLocaleDateString('id-ID',{month:'long',year:'numeric'});},
        get days(){return calendarDays(this.year,this.month,1);},
        move(direction){const next=new Date(this.year,this.month+direction,1);this.month=next.getMonth();this.year=next.getFullYear();},
        eventsFor(day,events){return (events || []).filter(e=>localDateKey(e.date || e.scheduled_at)===day.key);},
        openDay(day){this.selectedDate=day.key;document.getElementById('schedule-day')?.showModal();},
        dayList(){return this.eventsFor({key:this.selectedDate},this.schedules || []).sort((a,b)=>(a.start_time||'').localeCompare(b.start_time||''));},
        dayTitle(){return new Date(this.selectedDate+'T12:00:00').toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});},
        typeLabel(type){return eventLabels[type] || type;},
        dayTime(event){const start=(event.start_time||'').slice(0,5),end=(event.end_time||'').slice(0,5);return start ? start+(end?' — '+end:'') : 'Waktu belum ditentukan';},
        dayName(event){return event.student_name || event.group?.title?.title || this.typeLabel(event.type);},
        eventColor(type){return {BIMBINGAN:'bg-blue-500',SEMPRO:'bg-amber-500',SIDANG:'bg-primary-500',EXPO:'bg-emerald-500',TA_DEFENSE:'bg-rose-500',PDC1:'bg-sky-500',PDC2:'bg-violet-500'}[type] || 'bg-gray-400';}
    }));
}
