import {workspace,api,rows,unwrap,specializations} from './shared.js';
import {context} from '../../api.js';

export function lecturerTitles(){
    return workspace({items:[],specializations,filterSpecs:[],sortKey:'title',sortDirection:1,editing:null,deleting:null,
        form:{title:'',description:'',problem_statement:'',scope:'',specializations:[],quota:1,status:'open'},
        async load(){this.loading=true;this.error='';try{const [titles,periods]=await Promise.all([api('/dosen/titles'),api('/periods-list')]);this.items=rows(titles);this.periods=rows(periods);}catch(e){this.error=e.message;}finally{this.loading=false;}},
        get filtered(){return this.items.filter(t=>this.matches(t)&&(this.inPeriod(t.period_id)||!t.period_id)&&(!this.filterSpecs.length||this.filterSpecs.some(s=>(t.specializations||[]).includes(s)))).sort((a,b)=>String(a[this.sortKey]??'').localeCompare(String(b[this.sortKey]??''),'en',{numeric:true})*this.sortDirection);},
        sort(key){this.sortDirection=this.sortKey===key?-this.sortDirection:1;this.sortKey=key;},
        edit(title=null){this.editing=title;this.errors={};this.form={title:title?.title||'',description:title?.description||'',problem_statement:title?.problem_statement||'',scope:title?.scope||'',specializations:[...(title?.specializations||[])],quota:title?.quota||1,status:title?.status||'open'};this.open('title-form');},
        async save(){if(!this.form.specializations.length){this.errors={specializations:['Select at least one specialization.']};return;}await this.mutate('/dosen/titles'+(this.editing?'/'+this.editing.id:''),this.form,this.editing?'PUT':'POST','title-form');},
        confirmDelete(title){this.deleting=title;this.errors={};this.open('title-delete');},
        async remove(){if(this.deleting)await this.mutate('/dosen/titles/'+this.deleting.id,undefined,'DELETE','title-delete');},
    });
}

export function lecturerTitleDetail(){
    return workspace({title:null,
        async load(){this.loading=true;this.error='';try{this.title=unwrap(await api('/dosen/titles/'+encodeURIComponent(context.params.id)));}catch(e){this.error=e.message;}finally{this.loading=false;}},
    });
}

export function lecturerApprovals(){
    return workspace({items:[],selected:null,decision:'approve',reason:'',flow:null,
        async load(){this.loading=true;this.error='';try{const [proposals,periods]=await Promise.all([api('/dosen/title-approvals'),api('/periods-list')]);this.items=rows(proposals);this.flow=proposals.flow||null;this.periods=rows(periods);}catch(e){this.error=e.message;}finally{this.loading=false;}},
        get filtered(){return this.items.filter(p=>this.matches(p)&&this.inPeriod(p.period_id||p.proposed_by_group?.period_id));},
        canReview(p){return p.supervisor_approval_status==='PENDING' && !p.proposed_by_group?.period?.is_finalized && p.allowed_actions?.can_approve!==false;},
        review(p,decision){this.selected=p;this.decision=decision;this.reason='';this.errors={};this.open('proposal-review');},
        async submit(){if(!this.selected||this.saving||(this.decision==='reject'&&!this.reason.trim()))return;await this.mutate('/dosen/title-approvals/'+this.selected.id+'/'+this.decision,this.decision==='reject'?{rejection_reason:this.reason.trim()}:{},'PUT','proposal-review');},
    });
}

export function lecturerBids(){
    return workspace({items:[],flow:null,
        async load(){this.loading=true;this.error='';try{const [bids,periods]=await Promise.all([api('/dosen/bids'),api('/periods-list')]);this.items=rows(bids);this.flow=bids.flow||null;this.periods=rows(periods);}catch(e){this.error=e.message;}finally{this.loading=false;}},
        get filtered(){return this.items.filter(b=>this.matches(b)&&this.inPeriod(b.group?.period_id));},
        get byTitle(){const grouped=new Map();for(const bid of this.filtered){if(!grouped.has(bid.title_id))grouped.set(bid.title_id,{title:bid.title,bids:[]});grouped.get(bid.title_id).bids.push(bid);}return [...grouped.values()];},
        locked(bid){const p=bid.group?.period || this.periods.find(p=>String(p.id)===String(bid.group?.period_id));return !!(p?.is_finalized||p?.bidding_locked||p?.bidding_locked_at||this.flow?.can_recommend===false);},
        canRecommend(bid,value){return !this.locked(bid)&&bid.lecturer_recommendation!==value&&bid.allowed_actions?.[value==='ACCEPT'?'can_accept':(bid.lecturer_recommendation==='ACCEPT'?'can_cancel_accept':'can_reject')]!==false;},
        async recommend(bid,value){if(this.canRecommend(bid,value))await this.mutate('/dosen/bids/'+bid.id+'/recommend',{recommendation:value});},
    });
}
