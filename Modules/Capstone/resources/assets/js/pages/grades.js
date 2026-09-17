import {api,unwrap} from '../api.js';

const componentLabels={SEMPRO:'Seminar Proposal',BIMBINGAN_SEMPRO:'Bimbingan Sempro',NILAI_DOSEN:'Nilai Dosen',MILESTONE:'Milestone',EXPO:'Expo',PEER_REVIEW:'Peer Review',BIMBINGAN_TA:'Bimbingan TA',SIDANG_TA:'Sidang TA'};
const roleLabels={SUPERVISOR_1:'Pembimbing 1',SUPERVISOR_2:'Pembimbing 2',EXAMINER:'Penguji',EXAMINER_1:'Penguji 1',EXAMINER_2:'Penguji 2',STUDENT:'Rekan',UNKNOWN:'Evaluator'};
export function gradePalette(score){
    if(score>=85)return {letter:'A',text:'text-emerald-700',bg:'bg-emerald-100',bar:'bg-emerald-500'};
    if(score>=70)return {letter:'B',text:'text-sky-700',bg:'bg-sky-100',bar:'bg-sky-500'};
    if(score>=60)return {letter:'C',text:'text-amber-700',bg:'bg-amber-100',bar:'bg-amber-500'};
    if(score>=50)return {letter:'D',text:'text-orange-700',bg:'bg-orange-100',bar:'bg-orange-500'};
    return {letter:'E',text:'text-rose-700',bg:'bg-rose-100',bar:'bg-rose-500'};
}
export function registerGrades(Alpine){
    Alpine.data('capstoneGrades',()=>({
        loading:true,error:'',result:null,tab:'pdc1',palette:gradePalette,
        componentLabel:type=>componentLabels[type] || type,roleLabel:role=>roleLabels[role] || role,
        score:value=>value==null?'—':Number(value).toFixed(1),
        get sections(){return [['pdc1','PDC 1','Seminar & Bimbingan'],['pdc2','PDC 2','Expo, Milestone & Peer Review'],['ta','TA','Bimbingan & Sidang TA']].filter(([key])=>this.result?.grades?.[key]).map(([key,label,subtitle])=>({key,label,subtitle,section:this.result.grades[key]}));},
        get components(){return Object.entries(this.sections.find(s=>s.key===this.tab)?.section.components || {}).map(([type,detail])=>({type,...detail}));},
        async init(){await this.load();},
        async load(){this.loading=true;this.error='';try{this.result=unwrap(await api('/mahasiswa/my-grades'));if(!this.sections.some(s=>s.key===this.tab))this.tab=this.sections[0]?.key || 'pdc1';}catch(e){this.error=e.message;}finally{this.loading=false;}}
    }));
}
