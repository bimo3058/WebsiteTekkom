import {api,rows,unwrap,notify,url,date} from '../api.js';
export function registerRegistration(Alpine){
    Alpine.data('capstoneRegistration',()=>({
        periods:[],loading:true,error:'',registered:null,myRequest:null,saving:null,cancelling:false,date,
        get isPending(){return !this.registered && this.myRequest?.status==='PENDING';},
        get isRejected(){return !this.registered && this.myRequest?.status==='REJECTED';},
        get requestedPeriodName(){return this.myRequest?.period?.name || 'the selected period';},
        async init(){await this.load();},
        async load(){this.loading=true;this.error='';try{const result=await Promise.all([api('/periods-list'),api('/mahasiswa/my-period')]);this.periods=rows(result[0]).filter(p=>p.is_active && !p.is_finalized);const mine=unwrap(result[1]);this.registered=mine?.period || null;this.myRequest=mine?.registration || null;}catch(e){this.error=e.message;}finally{this.loading=false;}},
        async register(period){if(this.saving || this.cancelling)return;this.saving=period.id;try{const res=unwrap(await api('/mahasiswa/periods/register',{method:'POST',body:{period_id:period.id}}));this.myRequest=res?.registration || {status:'PENDING',period};notify('Join request sent. Please wait for admin approval.');}catch(e){notify(e.message,true);await this.load();}finally{this.saving=null;}},
        async cancelRequest(){if(this.cancelling)return;this.cancelling=true;try{await api('/mahasiswa/periods/registration',{method:'DELETE'});this.myRequest=null;notify('Join request cancelled. You can now join another period.');await this.load();}catch(e){notify(e.message,true);}finally{this.cancelling=false;}}
    }));
}
