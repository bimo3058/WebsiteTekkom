import {workspace,api,rows,unwrap,notify} from './shared.js';
import {context} from '../../api.js';

export const evaluationLabels={BIMBINGAN_SEMPRO:'Bimbingan Sempro',NILAI_DOSEN:'Nilai Dosen',MILESTONE:'Milestone',EXPO:'Expo',BIMBINGAN_TA:'Bimbingan Sidang TA',SEMPRO:'Sempro',SIDANG_TA:'Sidang TA'};
export function lecturerEvaluations(){
    return workspace({items:[],type:'all',status:'all',
        async load(){this.loading=true;this.error='';try{const [result,periods]=await Promise.all([api('/dosen/seminar-schedules/examiner'),api('/periods-list')]);const data=unwrap(result);this.periods=rows(periods);this.items=[];
            for(const [key,type] of [['seminars','SEMINAR'],['ta_defenses','TA_DEFENSE']])for(const schedule of data[key]||[]){const evaluations=schedule.evaluations?.length?schedule.evaluations:[null];for(const evaluation of evaluations)this.items.push({key:`${type}-${schedule.id}-${evaluation?.id||0}`,type,schedule,evaluation,status:['SUBMITTED','COMPLETED'].includes(evaluation?.status)?'COMPLETED':'PENDING'});}
        }catch(e){this.error=e.message;}finally{this.loading=false;}},
        get filtered(){return this.items.filter(item=>this.inPeriod(item.schedule.group?.period_id)&&this.matches(item)&&(this.type==='all'||item.type===this.type)&&(this.status==='all'||item.status===this.status));},
        count(status){return this.items.filter(i=>this.inPeriod(i.schedule.group?.period_id)&&(!status||i.status===status)).length;},
        evaluationUrl(item,view=false){return this.url('/dosen/evaluation/'+item.evaluation.id+'?type='+item.type+(view?'&mode=view':''));},
        late(item){const d=item.schedule?.evaluation_deadline;if(!d)return false;const t=new Date(d).getTime();return Number.isFinite(t)&&t<Date.now();},
    });
}

export function lecturerSupervisorEvaluations(){
    return workspace({items:[],schedules:[],type:'all',status:'all',view:'groups',evaluationLabels,
        async load(){this.loading=true;this.error='';try{const [groups,schedules,periods]=await Promise.all([api('/dosen/supervisor-evaluation/groups'),api('/dosen/supervisor-evaluation/schedules'),api('/periods-list')]);this.items=rows(groups);this.schedules=rows(schedules);this.periods=rows(periods);}catch(e){this.error=e.message;}finally{this.loading=false;}},
        entries(group){return Object.entries(group.evaluations||{}).filter(([type,e])=>(this.type==='all'||type===this.type)&&(this.status==='all'||String(e.status).toUpperCase()===this.status));},
        get filtered(){return this.items.filter(g=>this.inPeriod(g.period?.id)&&this.matches(g)&&this.entries(g).length);},
        get filteredSchedules(){return this.schedules.filter(s=>this.inPeriod(s.period?.id)&&this.matches(s)&&(this.type==='all'||s.evaluation_type===this.type)&&(this.status==='all'||String(s.status).toUpperCase()===this.status));},
        evaluationUrl(groupId,type,view=false,studentId=null){const query=new URLSearchParams({type});if(view)query.set('mode','view');if(studentId)query.set('student_id',studentId);return this.url('/dosen/supervisor-evaluation/'+groupId+'?'+query);},
    });
}

export function lecturerEvaluationForm(supervisor=false,ta=false){
    return workspace({supervisor,data:null,students:[],components:[],scores:{},notes:{},result:'',resultEditable:true,viewOnly:false,lockReason:'',type:'',evaluationLabels,
        async load(){this.loading=true;this.error='';try{
            const query=new URLSearchParams(window.location.search);this.viewOnly=query.get('mode')==='view';this.type=supervisor?(query.get('type')||'BIMBINGAN_SEMPRO'):(ta?'TA_DEFENSE':query.get('type')||'SEMINAR');
            const endpoint=supervisor?'/dosen/supervisor-evaluation/form/'+encodeURIComponent(context.params.groupId)+'?'+new URLSearchParams({type:this.type,...(query.get('student_id')?{student_id:query.get('student_id')}:{})}):'/dosen/evaluation-context/'+encodeURIComponent(this.type)+'/'+encodeURIComponent(context.params.id);
            this.data=unwrap(await api(endpoint));this.components=this.data.components||[];this.scores={};this.notes={};
            if(supervisor){this.students=this.data.students||[];for(const student of this.students)for(const score of student.scores||[]){const key=score.period_component_id+'_'+student.id;this.scores[key]=score.score??'';this.notes[key]=score.notes||'';}if(this.data.editable===false){this.viewOnly=true;this.lockReason=this.data.editable_reason||'This evaluation is not submittable in the current group status.';}}
            else{const student=this.data.student||this.data.schedule?.student;this.students=this.type==='TA_DEFENSE'&&student?[student]:(this.data.group?.members||[]).map(m=>m.student).filter(Boolean);for(const component of this.components)for(const student of this.students){const key=component.id+'_'+student.id;this.scores[key]=this.data.existing_scores?.[key]?.score??'';this.notes[key]=this.data.existing_scores?.[key]?.notes||'';}this.result=this.data.evaluation?.result||'';this.resultEditable=this.data.result_editable!==false;if(!this.resultEditable)this.lockReason='The PASS/FAIL result is locked because this schedule is completed. Scores can still be edited.';}
        }catch(e){this.error=e.message;}finally{this.loading=false;}},
        get backUrl(){return this.url(supervisor?'/dosen/supervisor-evaluation':'/dosen/evaluation');},
        get minimum(){return supervisor?1:0;},
        get scoreRows(){return this.students.flatMap(student=>this.components.map(component=>{const key=component.id+'_'+student.id;return {[supervisor?'period_component_id':'component_id']:component.id,student_id:student.id,score:this.scores[key]===''?null:Number(this.scores[key]),notes:this.notes[key]||''};}));},
        get complete(){return this.students.length>0&&this.components.length>0&&this.scoreRows.every(s=>s.score!==null&&Number.isFinite(s.score)&&s.score>=this.minimum&&s.score<=100);},
        total(studentId){const totalWeight=this.components.reduce((sum,c)=>sum+Number(c.weight||0),0);if(!totalWeight)return '0.00';return (this.components.reduce((sum,c)=>sum+Number(this.scores[c.id+'_'+studentId]||0)*Number(c.weight||0),0)/totalWeight).toFixed(2);},
        async submit(){
            if(this.saving||this.viewOnly)return;
            if(!this.complete||(!supervisor&&!['PASS','FAIL'].includes(this.result))){this.errors={root:'Complete all scores and select the examination result.'};return;}
            this.saving=true;this.errors={};
            try{
                if(supervisor)await api('/dosen/supervisor-evaluation',{method:'POST',body:{group_id:this.data.group.id,evaluation_type:this.type,scores:this.scoreRows}});
                else{
                    const evaluationType=this.type==='TA_DEFENSE'?'SIDANG_TA':this.data.schedule.type;
                    const endpoint=this.type==='TA_DEFENSE'?'ta-defense':evaluationType==='EXPO'?'expo':'sempro';
                    await api('/dosen/'+endpoint+'/'+this.data.schedule.id+'/evaluate',{method:'POST',body:{rubric_json:{scores:this.scores,notes:this.notes},score:this.students.reduce((sum,s)=>sum+Number(this.total(s.id)),0)/this.students.length,result:this.result}});
                }
                notify('Evaluation saved');await this.load();if(supervisor)this.viewOnly=true;
            }catch(e){this.errors={...e.errors,root:e.message};notify(e.message,true);}finally{this.saving=false;}
        },
    });
}
