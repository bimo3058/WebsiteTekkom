import {calendarDays,localDateKey} from '../calendar.js';
import {api,context,notify,url,rows,unwrap,date} from '../api.js';
export function registerDashboards(Alpine) {
    Alpine.data('capstoneDashboard',()=>({
        loading:true,error:'',data:{},groups:[],periods:[],selectedPeriod:'all',pending:0,group:null,workflow:{},schedules:[],date,url,
        get progress(){const phases=this.workflow?.phases || [];return phases.length ? Math.round(phases.filter(p=>p.status==='completed').length/phases.length*100) : 0;},
        get dashboardDocuments(){
            const phases=this.workflow?.phases || [];
            if(!phases.length)return [];
            const reference=[['C100','PDC1','PDC 1'],['C200','PDC1','PDC 1'],['C300','PDC1','PDC 1'],['PPT Presentasi','SEMPRO','SEMPRO'],['C400','PDC2','PDC 2'],['C500','PDC2','PDC 2']];
            return reference.map(([name,phase,phaseLabel])=>{
                const workflowPhase=phases.find(p=>p.phase===phase);
                const doc=(workflowPhase?.documents || []).find(d=>String(d.type).trim().toUpperCase()===name.toUpperCase());
                return {...doc,name,type:doc?.type || name,phase,phaseLabel,status:doc?.status || 'missing',
                    can_upload:workflowPhase?.can_upload === true && doc?.can_upload === true,
                    locked_reason:doc?.locked_reason || workflowPhase?.locked_reason || (!doc ? 'Dokumen belum dikonfigurasi untuk periode ini' : null)};
            });
        },
        documentStatus(status){return {missing:'Belum Upload',SUBMITTED:'Terkirim',APPROVED:'Disetujui',REJECTED:'Perlu Revisi',DRAFT:'Draft'}[status] || status;},
        documentStatusClass(status){return {missing:'border-red-200 bg-red-50 text-red-600',SUBMITTED:'border-blue-200 bg-blue-50 text-blue-600',APPROVED:'border-emerald-200 bg-emerald-50 text-emerald-700',REJECTED:'border-amber-200 bg-amber-50 text-amber-700'}[status] || 'border-slate-200 bg-slate-50 text-slate-600';},
        documentHref(doc){return url('/mahasiswa/documents')+'?phase='+encodeURIComponent(doc.phase)+'&type='+encodeURIComponent(doc.type)+(doc.latest_document?.id ? '&document='+encodeURIComponent(doc.latest_document.id) : '');},
        async init(){await this.load();},
        async load(){this.loading=true;this.error='';try{
            const role=context.role;
            if(role==='admin'){this.data=unwrap(await api('/admin/dashboard'));this.groups=this.data.recent_groups || [];}
            else if(role==='dosen'){
                const query=this.selectedPeriod==='all' ? '' : '?period_id='+encodeURIComponent(this.selectedPeriod);
                const result=await Promise.all([api('/dosen/dashboard'+query),api('/dosen/groups/supervised'+query),api('/dosen/supervisor-evaluation/pending-count')]);
                this.data=unwrap(result[0]);this.groups=rows(result[1]);this.pending=unwrap(result[2])?.count || 0;this.periods=this.data.available_periods || [];
            }else{
                const period=unwrap(await api('/mahasiswa/my-period'));
                if(!period?.period){location.replace(url('/mahasiswa/registration'));return;}
                const result=await Promise.allSettled([api('/mahasiswa/dashboard'),api('/mahasiswa/group'),api('/mahasiswa/all-schedules'),api('/mahasiswa/workflow')]);
                if(result[0].status==='rejected')throw result[0].reason;
                this.data=unwrap(result[0].value);
                const group=result[1].status==='fulfilled' ? unwrap(result[1].value) : null;
                this.group=group?.group || group;
                this.schedules=result[2].status==='fulfilled' ? rows(result[2].value) : [];
                this.workflow=result[3].status==='fulfilled' ? unwrap(result[3].value) : this.data.workflow || {};
            }
        }catch(e){this.error=e.message;}finally{this.loading=false;}}
    }));
    Alpine.data('capstoneCalendar',()=>({
        month:new Date().getMonth(),year:new Date().getFullYear(),
        get monthLabel(){return new Date(this.year,this.month,1).toLocaleDateString('id-ID',{month:'long',year:'numeric'});},
        get days(){return calendarDays(this.year,this.month,1);},
        move(direction){const next=new Date(this.year,this.month+direction,1);this.month=next.getMonth();this.year=next.getFullYear();},
        eventsFor(day,events){return (events || []).filter(e=>localDateKey(e.date || e.scheduled_at)===day.key);},
        eventColor(type){return {BIMBINGAN:'bg-blue-500',SEMPRO:'bg-amber-500',SIDANG:'bg-primary-500',EXPO:'bg-emerald-500',TA_DEFENSE:'bg-rose-500',PDC1:'bg-sky-500',PDC2:'bg-violet-500'}[type] || 'bg-gray-400';}
    }));
}
