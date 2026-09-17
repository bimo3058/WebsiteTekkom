import {api,unwrap,notify,url} from '../api.js';

export function registerPeerReview(Alpine){
    Alpine.data('capstonePeerReview',()=>({
        loading:true,error:'',saving:false,group:null,members:[],indicators:[],currentUser:null,indicatorKey:"indicator_id",isLocked:true,hasSubmitted:false,status:null,scores:{},url,
        async init(){await this.load();},
        async load(){this.loading=true;this.error='';try{
            const [form,status]=await Promise.all([api('/mahasiswa/peer-review'),api('/mahasiswa/ta-status')]);
            const data=unwrap(form);this.group=data.group;this.members=data.members || [];this.indicators=data.indicators || [];
            this.indicatorKey=data.indicator_key || "indicator_id";this.currentUser=data.current_user_id;this.isLocked=data.is_locked;this.hasSubmitted=data.has_submitted;this.status=unwrap(status);
            this.scores={};
            for(const member of this.reviewable){this.scores[member.student.id]={};for(const ind of this.indicators){
                const existing=(data.existing_reviews || []).find(r=>r.reviewee_id===member.student.id && r[this.indicatorKey]===ind.id);
                this.scores[member.student.id][ind.id]={score:existing ? Number(existing.raw_score ?? Number(existing.score)/25) : 0,comment:existing?.comment || ''};
            }}
        }catch(e){this.error=e.message;}finally{this.loading=false;}},
        get reviewable(){return this.members.filter(m=>m.student?.id!==this.currentUser);},
        get canSubmit(){return !this.isLocked&&!this.hasSubmitted&&this.reviewable.length>0&&this.indicators.length>0;},
        get complete(){return this.reviewable.every(m=>this.indicators.every(i=>[1,2,3,4].includes(Number(this.scores[m.student.id]?.[i.id]?.score))));},
        average(id){let total=0,weight=0;for(const indicator of this.indicators){const score=Number(this.scores[id]?.[indicator.id]?.score || 0);if(score>0){total+=score*25*Number(indicator.weight);weight+=Number(indicator.weight);}}return (weight?total/weight:0).toFixed(1);},
        get overall(){return (this.reviewable.length ? this.reviewable.reduce((sum,m)=>sum+Number(this.average(m.student.id)),0)/this.reviewable.length : 0).toFixed(1);},
        confirm(){if(!this.canSubmit)return;if(!this.complete){notify('Please enter a score (1–4) for every member and indicator.',true);return;}document.getElementById('peer-confirm').showModal();},
        async submit(){
            if(!this.canSubmit||!this.complete||this.saving)return;
            this.saving=true;
            try{const reviews=[];for(const member of this.reviewable)for(const indicator of this.indicators){const value=this.scores[member.student.id][indicator.id];reviews.push({reviewee_id:member.student.id,[this.indicatorKey]:indicator.id,score:Number(value.score)*(this.indicatorKey==="period_indicator_id"?1:25),comment:value.comment});}
                await api('/mahasiswa/peer-review',{method:'POST',body:{reviews}});document.getElementById('peer-confirm').close();this.hasSubmitted=true;notify('Peer review submitted successfully!');await this.load();
            }catch(e){notify(e.message,true);if(e.status===403){document.getElementById('peer-confirm').close();await this.load();}}finally{this.saving=false;}
        }
    }));
}
