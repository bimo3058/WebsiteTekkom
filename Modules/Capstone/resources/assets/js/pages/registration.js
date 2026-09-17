import {api,rows,unwrap,notify,url,date} from '../api.js';
export function registerRegistration(Alpine){
    Alpine.data('capstoneRegistration',()=>({
        periods:[],loading:true,error:'',registered:null,saving:null,date,
        async init(){await this.load();},
        async load(){this.loading=true;this.error='';try{const result=await Promise.all([api('/periods-list'),api('/mahasiswa/my-period')]);this.periods=rows(result[0]).filter(p=>p.is_active && !p.is_finalized);this.registered=unwrap(result[1])?.period || null;}catch(e){this.error=e.message;}finally{this.loading=false;}},
        async register(period){if(this.saving)return;this.saving=period.id;try{await api('/mahasiswa/periods/register',{method:'POST',body:{period_id:period.id}});this.registered=period;notify('Successfully registered for period');setTimeout(()=>location.assign(url('/mahasiswa/dashboard')),1500);}catch(e){notify(e.message,true);}finally{this.saving=null;}}
    }));
}
