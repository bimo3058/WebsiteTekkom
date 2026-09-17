import {api,context,unwrap,groupFrom,list,withBase} from './shared.js';
export function studentTitleDetail(){return withBase({title:null,bidFlow:null,bids:[],
async load(){this.loading=true;this.error='';try{const [t,g,b]=await Promise.all([api('/mahasiswa/titles/'+context.params.id),api('/mahasiswa/group'),api('/mahasiswa/bids')]);this.title=unwrap(t);this.group=groupFrom(g);this.bidFlow=b.flow??unwrap(b)?.flow;this.bids=list(b,'bids');}catch(e){this.error=e.message;}finally{this.loading=false;}},
get slots(){return Math.max(0,(this.title?.quota??0)-(this.title?.active_groups_count??this.title?.groups?.filter(g=>g.status!=='REJECTED').length??0));},
get canBid(){return this.isLeader && !this.group?.title_id && ['READY_FOR_BIDDING','FORMING_SOLO','FORMING','WAITING_SUPERVISOR_APPROVAL'].includes(this.group?.status) && (!this.group?.is_solo || this.members.length>=this.minMembers) && this.members.length>=this.minMembers && !this.group?.period?.is_finalized && this.slots>0 && (this.bidFlow?.can_submit_bid??true) && !this.bids.some(b=>String(b.title_id)===String(this.title?.id));},
async bid(){if(!this.canBid || !this.title?.lecturer?.id)return;if(await this.action('/mahasiswa/bids','POST',{title_id:this.title.id,proposed_supervisor_1_id:this.title.lecturer.id,proposed_supervisor_2_id:null}))window.location.assign(this.url('/mahasiswa/group'));}
});}
