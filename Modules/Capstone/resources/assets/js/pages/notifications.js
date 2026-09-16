import {api,notify,url} from '../api.js';
export function registerNotifications(Alpine){
    Alpine.data('capstoneNotifications',()=>({
        loading:true,error:'',items:[],page:1,lastPage:1,total:0,busy:false,invitationActions:{},
        get unreadCount(){return this.items.filter(n=>!n.is_read).length;},
        async init(){await this.load(1);},
        async load(page=this.page){this.loading=true;this.error='';try{const data=await api(`/notifications?page=${page}&per_page=10`);this.items=data.data || [];this.page=data.current_page;this.lastPage=data.last_page;this.total=data.total;}catch(e){this.error=e.message;}finally{this.loading=false;}},
        palette(type){return {PROPOSAL_SUBMITTED:'bg-blue-50 text-blue-600',PROPOSAL_APPROVED:'bg-green-50 text-green-600',PROPOSAL_REJECTED:'bg-red-50 text-red-600',PROPOSAL_RESUBMITTED:'bg-amber-50 text-amber-600',EXPO_REGISTRATION:'bg-primary-50 text-primary-500',SCHEDULE_APPROVED:'bg-emerald-50 text-emerald-600',SCHEDULE_REJECTED:'bg-rose-50 text-rose-600',GROUP_INVITATION:'bg-indigo-50 text-indigo-600'}[type] || 'bg-gray-50 text-gray-500';},
        relative(value){const seconds=(new Date(value)-Date.now())/1000;if(!Number.isFinite(seconds))return '';const units=[['year',31536000],['month',2592000],['day',86400],['hour',3600],['minute',60],['second',1]];const [unit,amount]=units.find(([,n])=>Math.abs(seconds)>=n) || units.at(-1);return new Intl.RelativeTimeFormat('id',{numeric:'auto'}).format(Math.round(seconds/amount),unit);},
        async read(item){if(item.is_read)return;await api(`/notifications/${item.id}/read`,{method:'PUT'});item.is_read=true;window.dispatchEvent(new CustomEvent('capstone-notifications-updated'));},
        async open(item){try{await this.read(item);const path=item.action_url;if(typeof path==='string' && /^\/(admin|dosen|mahasiswa|profile|notifications)(\/|$)/.test(path))window.location.assign(url(path));}catch(e){notify(e.message,true);}},
        async readAll(){if(this.busy)return;this.busy=true;try{await api('/notifications/read-all',{method:'PUT'});this.items.forEach(n=>n.is_read=true);window.dispatchEvent(new CustomEvent('capstone-notifications-updated'));notify('All notifications marked as read');}catch(e){notify(e.message,true);}finally{this.busy=false;}},
        async invitation(item,action){if(this.busy||item.invitation_status!=='PENDING')return;this.busy=true;try{const response=await api(`/mahasiswa/group-invitations/${item.related_id}/${action}`,{method:'POST'});this.invitationActions[item.id]=action;item.invitation_status=action==='accept'?'ACCEPTED':'REJECTED';await this.read(item);notify(response.message || `Invitation ${action}ed successfully`);}catch(e){notify(e.message,true);}finally{this.busy=false;}}
    }));
}
