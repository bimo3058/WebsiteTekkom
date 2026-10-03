import {api,unwrap,groupFrom,list,withBase,show,close} from './shared.js';
export function studentBidding(){return withBase({bids:[],order:[],proposals:[],titles:[],lecturers:[],flow:null,form:{title_id:''},
async load(){this.loading=true;this.error='';try{const [g,b,p,t,l]=await Promise.all([api('/mahasiswa/group'),api('/mahasiswa/bids'),api('/mahasiswa/my-proposal'),api('/mahasiswa/titles'),api('/mahasiswa/lecturers')]);this.group=groupFrom(g);this.bids=list(b,'bids');this.order=this.bids.filter(b=>b.status!=='REJECTED').map(b=>({...b})).sort((a,b)=>a.priority-b.priority);this.flow=b.flow??unwrap(b)?.flow;this.proposals=list(p,'proposals').filter(p=>['PENDING','UNDER_REVIEW','APPROVED'].includes(p.supervisor_approval_status));this.titles=list(t,'titles');this.lecturers=list(l,'lecturers');}catch(e){this.error=e.message;}finally{this.loading=false;}},
get active(){return this.bids.filter(b=>b.status!=='REJECTED');},
get rejected(){return this.bids.filter(b=>b.status==='REJECTED');},
get total(){return this.active.length+this.proposals.length;},
get canManage(){return this.isLeader && !this.group?.period?.is_finalized && ['FORMING','FORMING_SOLO','READY_FOR_BIDDING','WAITING_SUPERVISOR_APPROVAL'].includes(this.group?.status) && (!this.group?.is_solo || this.members.length>=this.minMembers);},
get canSubmit(){return this.canManage && (this.flow?.can_submit_bid??(this.total<3 && !this.proposals.length && this.members.length>=this.minMembers));},
get canReorder(){return this.canManage && (this.flow?.can_reorder_bid??true);},get canDelete(){return this.canManage && (this.flow?.can_delete_bid??true);},
get availableTitles(){return this.titles.filter(t=>t.title_source!=='STUDENT' && !this.active.some(b=>String(b.title_id)===String(t.id)));},
get changed(){return this.order.some(b=>this.bids.find(o=>o.id===b.id)?.priority!==b.priority);},
move(index,delta){if(!this.canReorder || this.saving)return;const next=index+delta;if(next<0 || next>=this.order.length)return;const order=[...this.order];[order[index],order[next]]=[order[next],order[index]];this.order=order.map((b,i)=>({...b,priority:i+1}));},
async saveOrder(){if(!this.canReorder || !this.changed)return;if(await this.action('/mahasiswa/bids/reorder','PUT',{bids:this.order.map(b=>({id:b.id,priority:b.priority}))}))await this.load();},
open(){if(!this.canSubmit)return;this.form={title_id:''};this.errors={};show('new-bid');},
async save(){if(!this.canSubmit)return;const f=this.form;if(await this.action('/mahasiswa/bids','POST',{title_id:Number(f.title_id)},'new-bid'))await this.load();},
async remove(b){if(!this.canDelete || !['PENDING','REJECTED'].includes(b.status) || !window.confirm('Are you sure you want to delete this bid?'))return;if(await this.action('/mahasiswa/bids/'+b.id,'DELETE'))await this.load();}
});}
