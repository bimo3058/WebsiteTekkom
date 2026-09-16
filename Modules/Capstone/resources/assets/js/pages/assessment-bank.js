import {api,rows,unwrap,notify,url} from '../api.js';
export function assessmentBank(){return {
    loading:true,error:'',items:[],search:'',status:'all',sortKey:'code',direction:1,page:1,pageSize:10,selected:[],saving:false,deleting:null,deleteIds:[],url,
    async init(){this.$watch?.('search',()=>{this.page=1;this.selected=[];});this.$watch?.('status',()=>{this.page=1;this.selected=[];});await this.load();},
    async load(){this.loading=true;this.error='';try{this.items=rows(await api('/admin/assessment-templates'));}catch(e){this.error=e.message;}finally{this.loading=false;}},
    get filtered(){const q=this.search.trim().toLowerCase();return this.items.filter(t=>(this.status==='all'||String(t.is_active)===this.status)&&[t.code,t.description].some(v=>String(v || '').toLowerCase().includes(q))).sort((a,b)=>{const left=a[this.sortKey],right=b[this.sortKey];return (this.sortKey==='weight' ? Number(left)-Number(right) : String(left).localeCompare(String(right),'id',{numeric:true}))*this.direction;});},
    get pageCount(){return Math.max(1,Math.ceil(this.filtered.length/this.pageSize));},get visible(){const p=Math.min(this.page,this.pageCount);return this.filtered.slice((p-1)*this.pageSize,p*this.pageSize);},
    sort(key){this.direction=this.sortKey===key?-this.direction:1;this.sortKey=key;},
    selectPage(checked){this.selected=checked?[...new Set([...this.selected,...this.visible.map(t=>t.id)])]:this.selected.filter(id=>!this.visible.some(t=>t.id===id));},
    weightColor(weight){return weight>50?'bg-red-50 text-red-700 border-red-200':weight>=25?'bg-amber-50 text-amber-700 border-amber-200':'bg-green-50 text-green-700 border-green-200';},
    confirmDelete(item=null){this.deleting=item;this.deleteIds=item?[item.id]:[...this.selected];document.getElementById('bank-delete').showModal();},
    async mutate(ids,action){if(this.saving||!ids.length)return;this.saving=true;const failed=[];try{for(const id of ids){try{await api('/admin/assessment-templates/'+id,{method:action==='delete'?'DELETE':'PUT',...(action==='delete'?{}:{body:{is_active:action==='activate'}})});}catch(e){failed.push(id);notify(e.message,true);}}this.selected=failed;const done=ids.length-failed.length;if(done)notify(done+' komponen berhasil diperbarui');if(action==='delete')document.getElementById('bank-delete').close();await this.load();}finally{this.saving=false;}},
};}
export function assessmentTemplateForm(id=null){return {
    id,loading:true,error:'',saving:false,errors:{},form:{code:'',description:'',weight:0,is_active:true},original:'',unload:null,url,
    get dirty(){return !!this.original && JSON.stringify(this.form)!==this.original;},
    async init(){await this.load();this.unload=e=>{if(this.dirty){e.preventDefault();e.returnValue='';}};window.addEventListener('beforeunload',this.unload);},
    destroy(){if(this.unload)window.removeEventListener('beforeunload',this.unload);},
    async load(){this.loading=true;this.error='';try{if(this.id){const data=unwrap(await api('/admin/assessment-templates/'+this.id));this.form={code:data.code,description:data.description || '',weight:Number(data.weight),is_active:!!data.is_active};}this.original=JSON.stringify(this.form);}catch(e){this.error=e.message;}finally{this.loading=false;}},
    back(){if(this.dirty)document.getElementById('bank-unsaved').showModal();else this.leave();},
    leave(){this.original=JSON.stringify(this.form);window.location.assign(url('/admin/assessment-bank'));},
    async save(){if(this.saving||this.loading||this.error)return;this.saving=true;this.errors={};try{await api('/admin/assessment-templates'+(this.id?'/'+this.id:''),{method:this.id?'PUT':'POST',body:{...this.form,name:this.form.code,weight:Number(this.form.weight)}});notify('Komponen penilaian berhasil disimpan');this.leave();}catch(e){this.errors=e.errors || {};notify(e.message,true);}finally{this.saving=false;}},
};}
export function registerAssessmentBank(Alpine){Alpine.data('assessmentBank',assessmentBank);Alpine.data('assessmentTemplateForm',assessmentTemplateForm);}
