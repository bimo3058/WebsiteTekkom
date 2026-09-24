import {api,context,rows,notify,url,date,download} from '../api.js';
import {calendarDays,localDateKey,eventColors,eventLabels,normalizeSchedule} from '../calendar.js';

export function schedulePage() {
    return {
        loading:true,error:'',schedules:[],periods:[],groups:[],locations:[],selectedPeriod:'all',view:'calendar',
        month:new Date().getMonth(),year:new Date().getFullYear(),selectedDate:localDateKey(new Date()),
        selected:null,editing:null,deleting:null,rejecting:null,form:{},errors:{},saving:false,reason:'',
        search:'',typeFilter:'all',statusFilter:'all',page:1,perPage:10,sortDirection:1,date,url,
        async init(){await this.load();},
        async load(){
            this.loading=true;this.error='';
            try {
                const role=context.role;
                const quiet=promise=>promise.catch(()=>null);
                const result=await Promise.all([
                    api(`/${role}/all-schedules`),
                    role==='mahasiswa' ? [] : quiet(api(role==='admin' ? '/admin/periods' : '/periods-list')),
                    role==='dosen' ? quiet(api('/dosen/groups/supervised')) : [],
                    role==='dosen' ? quiet(api('/locations/eoffice-rooms')) : [],
                ]);
                this.schedules=rows(result[0]).map(item=>normalizeSchedule(item,item.type)).filter(item=>localDateKey(item.date));
                // Bimbingan stays free-text: map EOffice rooms to {id,name} suggestions.
                // Aux calls fail soft so one bad picker feed can never blank the page.
                this.periods=rows(result[1]);this.groups=rows(result[2]);this.locations=rows(result[3]).map(r=>r.nama!==undefined?{id:r.id,name:r.nama,type:'offline'}:r);
            } catch(e){this.error=e.message;} finally{this.loading=false;}
        },
        get filtered(){return this.schedules.filter(s=>this.selectedPeriod==='all' || String(s.period_id || s.group?.period_id)===String(this.selectedPeriod));},
        get tableRows(){
            const term=this.search.toLocaleLowerCase();
            return this.filtered.filter(s=>(this.typeFilter==='all'||s.type===this.typeFilter) && (this.statusFilter==='all'||s.status===this.statusFilter)
                && [s.group?.title?.title,s.student_name,s.room,s.type,s.period_name].some(v=>String(v||'').toLocaleLowerCase().includes(term)))
                .sort((a,b)=>((localDateKey(a.date)+(a.start_time||'')).localeCompare(localDateKey(b.date)+(b.start_time||'')))*this.sortDirection);
        },
        get totalPages(){return Math.max(1,Math.ceil(this.tableRows.length/this.perPage));},
        get currentPage(){return Math.min(this.page,this.totalPages);},
        get visibleRows(){return this.tableRows.slice((this.currentPage-1)*this.perPage,this.currentPage*this.perPage);},
        get days(){return calendarDays(this.year,this.month);},
        get monthLabel(){return new Date(this.year,this.month,1).toLocaleDateString('en-US',{month:'long',year:'numeric'});},
        get selectedLabel(){return new Date(this.selectedDate+'T12:00:00').toLocaleDateString('en-US',{weekday:'long',month:'long',day:'numeric',year:'numeric'});},
        get eventsByDate(){
            const grouped={};
            for(const event of this.filtered){const key=localDateKey(event.date);(grouped[key]??=[]).push(event);}
            for(const events of Object.values(grouped))events.sort((a,b)=>(a.start_time||'').localeCompare(b.start_time||''));
            return grouped;
        },
        get dayEvents(){return this.eventsFor(this.selectedDate);},
        eventsFor(day){return this.eventsByDate[day] || [];},
        count(type){return this.filtered.filter(s=>s.type===type).length;},
        color(type){return eventColors[type] || eventColors.BIMBINGAN;},
        label(type){return eventLabels[type] || type;},
        statusColor(status){return ['APPROVED','SCHEDULED'].includes(status) ? 'bg-green-100 text-green-700' : ['COMPLETED','DONE'].includes(status) ? 'bg-blue-100 text-blue-700' : ['REJECTED','CANCELLED'].includes(status) ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700';},
        time(event){const start=(event.start_time||'').slice(0,5),end=(event.end_time||'').slice(0,5);return start ? start+(end?' — '+end:'') : 'Time not set';},
        title(event){return event.student_name || event.group?.title?.title || this.label(event.type);},
        students(event){return event.students?.map(s=>s.name).join(', ') || event.student_name || '';},
        examiners(event){return event.examiners?.map(e=>e.name || e.examiner?.name).filter(Boolean).join(', ') || [event.examiner1?.name,event.examiner2?.name].filter(Boolean).join(', ');},
        canApprove(event){return context.role==='admin' && ['SEMPRO','EXPO','TA_DEFENSE'].includes(event?.type) && ['PENDING','PENDING_APPROVAL'].includes(event?.status);},
        canEdit(event){return context.role==='dosen' && event?.type==='BIMBINGAN';},
        move(direction){const next=new Date(this.year,this.month+direction,1);this.year=next.getFullYear();this.month=next.getMonth();},
        today(){const now=new Date();this.year=now.getFullYear();this.month=now.getMonth();this.selectedDate=localDateKey(now);},
        detail(event){this.selected=event;document.getElementById('schedule-detail').showModal();},
        edit(event=null){
            if(context.role!=='dosen'||(event&&!this.canEdit(event)))return;
            this.editing=event;this.errors={};
            this.form={group_id:String(event?.group_id||''),type:'BIMBINGAN',date:event?localDateKey(event.date):this.selectedDate,
                start_time:(event?.start_time||'').slice(0,5),end_time:(event?.end_time||'').slice(0,5),room:event?.room||'',mode:event?.mode||'offline',notes:event?.notes||''};
            document.getElementById('schedule-form').showModal();
        },
        async save(){
            if(this.saving)return;
            this.saving=true;this.errors={};
            try{await api('/schedules'+(this.editing?'/'+this.editing._id:''),{method:this.editing?'PUT':'POST',body:this.form});
                document.getElementById('schedule-form').close();notify('Schedule saved');await this.load();
            }catch(e){this.errors=e.errors||{};notify(e.message,true);}finally{this.saving=false;}
        },
        confirmDelete(event){if(!this.canEdit(event))return;this.deleting=event;document.getElementById('schedule-delete').showModal();},
        async remove(){
            if(!this.deleting||this.saving)return;
            this.saving=true;
            try{await api('/schedules/'+this.deleting._id,{method:'DELETE'});document.getElementById('schedule-delete').close();this.deleting=null;notify('Schedule deleted');await this.load();}
            catch(e){notify(e.message,true);}finally{this.saving=false;}
        },
        async approve(event){
            if(!this.canApprove(event)||this.saving)return;
            this.saving=true;
            try{
                const body={date:localDateKey(event.date),start_time:event.start_time,end_time:event.end_time,room:event.room,eoffice_ruangan_id:event.eoffice_ruangan_id || null,
                    examiner_1_id:event.examiner_1_id||event.examiner1?.id,examiner_2_id:event.examiner_2_id||event.examiner2?.id};
                const endpoint=event.type==='TA_DEFENSE'?`/admin/ta-defense-schedules/${event._id}`:`/admin/${event.type.toLowerCase()}/schedules/${event._id}/approve`;
                if(event.type==='TA_DEFENSE')body.status='SCHEDULED';
                await api(endpoint,{method:'PUT',body});notify('Schedule approved!');document.getElementById('schedule-detail').close();await this.load();
            }catch(e){notify(e.message,true);}finally{this.saving=false;}
        },
        reject(event){if(!this.canApprove(event))return;this.rejecting=event;this.reason='';document.getElementById('schedule-reject').showModal();},
        async submitRejection(){
            if(!this.reason.trim()||this.saving||!this.rejecting)return;
            this.saving=true;
            try{
                const event=this.rejecting;
                const endpoint=event.type==='TA_DEFENSE'?`/admin/ta-defense-schedules/${event._id}/cancel`:`/admin/${event.type.toLowerCase()}/schedules/${event._id}/reject`;
                await api(endpoint,{method:'PUT',body:{rejection_reason:this.reason.trim()}});
                document.getElementById('schedule-reject').close();document.getElementById('schedule-detail').close();notify('Schedule request rejected.');await this.load();
            }catch(e){notify(e.message,true);}finally{this.saving=false;}
        },
        exportCsv(all=false){
            const quote=value=>'"'+String(value??'').replace(/^[=+@\-]/,"'$&").replaceAll('"','""')+'"';
            const content=[['ID','Type','Date','Start Time','End Time','Room','Group Title','Student Name','Period','Status','Examiner 1','Examiner 2','Mode','Notes'],
                ...(all?this.schedules:this.filtered).map(s=>[s.id,s.type,localDateKey(s.date),s.start_time,s.end_time,s.room,s.group?.title?.title,s.student_name,s.period_name,s.status,s.examiner1?.name,s.examiner2?.name,s.mode,s.notes])]
                .map(row=>row.map(quote).join(',')).join('\r\n');
            download(new Blob(['\uFEFF'+content],{type:'text/csv;charset=utf-8'}),`schedules_${all?'all':'filtered'}_${localDateKey(new Date())}.csv`);
        },
        safeLink(link){try{const u=new URL(link);return ['http:','https:'].includes(u.protocol)?u.href:'#';}catch{return '#';}}
    };
}
export function registerSchedules(Alpine){Alpine.data('capstoneSchedules',schedulePage);}
