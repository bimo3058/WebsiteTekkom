import {basePage,api,rows,unwrap,query,download,allRows} from './shared.js';
import {context} from '../../api.js';
export const reportKinds={assessments:'student-evaluations-summary','final-grades':'final-grades','peer-reviews':'peer-reviews',groups:'groups','grade-consistency':'grade-consistency',pdc1:'phase-evaluations',pdc2:'phase-evaluations',ta:'phase-evaluations'};
export const reportColumns={
    assessments:[['group_name','Kelompok'],['student_name','Mahasiswa'],['student_nim','NIM']],
    'final-grades':[['group_title','Judul'],['student_name','Mahasiswa'],['student_nim','NIM'],['pdc1_score','PDC 1'],['pdc2_score','PDC 2'],['ta_score','TA']],
    'peer-reviews':[['group.code','Kelompok'],['reviewer.name','Reviewer'],['reviewee.name','Dinilai'],['score','Nilai'],['comment','Komentar']],
    groups:[['code','Kelompok'],['title.title','Judul'],['status','Status'],['members_count','Anggota']],
    'grade-consistency':[['student_name','Mahasiswa'],['student_nim','NIM'],['pdc1_score','PDC 1'],['pdc2_score','PDC 2'],['deviation','Deviasi']],
    phase:[['group_name','Kelompok'],['student_name','Mahasiswa'],['student_nim','NIM'],['overall_status','Status']],
};
export function reportsAdmin(kind='summary'){return {...basePage(),kind,summary:{},pagination:{last_page:1,total:0},data:{},
    async init(){try{this.periodId=new URLSearchParams(window.location.search).get('period_id')||'';await this.periodsLoad();await this.load();}catch(e){this.error=e.message;this.loading=false;}},
    get columns(){return reportColumns[this.kind]||reportColumns.phase;},
    value(item,key){if(key==='code')return this.groupName(item);return key.split('.').reduce((value,k)=>value?.[k],item)??'-';},
    get detail(){return ['student','evaluation','evaluator','schedule'].includes(this.kind);},
    endpoint(){const p=context.params||{};if(this.kind==='summary')return '/admin/reports/summary';if(this.kind==='evaluation')return `/admin/reports/student-evaluations/${p.studentId}/${p.evaluationType}`;if(this.kind==='evaluator')return `/admin/reports/evaluator-detail/${p.studentId}/${p.evaluationType}/${p.evaluatorId}`;if(this.kind==='schedule')return `/admin/supervisor-evaluation/schedules/${p.scheduleId}/summary`;return '/admin/reports/'+(reportKinds[this.kind]||'student-evaluations-summary');},
    params(){return {period_id:this.periodId,schedule_source:this.kind==='schedule'?(new URLSearchParams(window.location.search).get('schedule_source')||undefined):undefined,phase:['pdc1','pdc2','ta'].includes(this.kind)?this.kind:undefined,student_search:this.search,search:this.search,page:this.page,per_page:this.pageSize};},
    async load(){this.loading=true;this.error='';try{if(!this.periodId&&this.kind!=='schedule'){this.items=[];return;}const response=this.kind==='student'?{data:await allRows(this.endpoint(),this.params())}:await api(this.endpoint()+query(this.params()));this.data=unwrap(response);if(this.kind==='summary')this.summary=this.data;else if(this.kind==='student'){this.items=rows(response).filter(r=>String(r.student_id)===String(context.params.studentId));}else if(!this.detail){this.items=rows(response);this.pagination=this.data.pagination||response.pagination||response.meta||this.data.meta||{};}}catch(e){this.error=e.message;}finally{this.loading=false;}},
    link(path){return this.url(path)+query({period_id:this.periodId});},
    evaluationUrl(item,type){return type==='PEER_REVIEW'?this.link('/admin/reports/peer-reviews'):this.link(`/admin/reports/assessments/student/${item.student_id}/evaluation/${type}`);},
    async exportReport(){if(!this.periodId)return;await this.run(async()=>{
        let blob;
        if(['pdc1','pdc2','ta','student'].includes(this.kind)){
            let items=await allRows(this.endpoint(),this.params());
            if(this.kind==='student')items=items.filter(i=>String(i.student_id)===String(context.params.studentId));
            const types=[...new Set(items.flatMap(i=>Object.keys(i.evaluations||{})))];
            const values=[['Kelompok','Mahasiswa','NIM',...types],...items.map(i=>[i.group_name,i.student_name,i.student_nim,...types.map(type=>i.evaluations?.[type]?.score ?? '')])];
            const quote=value=>{let text=String(value??'');if(/^[=+@-]/.test(text))text="'"+text;return '"'+text.replaceAll('"','""')+'"';};
            blob=new Blob(['\uFEFF'+values.map(row=>row.map(quote).join(',')).join('\r\n')],{type:'text/csv'});
        }else{
            const endpoint=this.kind==='assessments'?'/admin/reports/student-evaluations-summary/export':this.endpoint();
            blob=await api(endpoint+query({...this.params(),format:'csv'}),{blob:true});
        }
        download(blob,this.kind+'-'+this.periodId+'.csv');
    },'Laporan diunduh');},
};}
