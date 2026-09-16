import {api,rows,unwrap,url,date,notify,download} from '../../api.js';
export {api,rows,unwrap,url,date,notify,download};
export const query=params=>{const q=new URLSearchParams(Object.entries(params).filter(([,v])=>v!==''&&v!==null&&v!==undefined&&v!=='all'));return q.size?'?'+q:'';};
export const person=p=>p?.name || p?.user?.name || '-';
export const groupName=g=>g?.code || g?.group_code || (g?.id?'Group '+g.id:'-');
export const dialog=id=>document.getElementById(id);
export function basePage(){return {
    loading:true,error:'',saving:false,errors:{},periods:[],periodId:'',search:'',page:1,pageSize:10,items:[],selected:null,sortKey:'',sortDirection:1,url,date,person,groupName,
    async periodsLoad(select=true){this.periods=rows(await api('/periods-list'));if(select&&!this.periodId)this.periodId=String(this.periods.find(p=>p.is_active)?.id || this.periods[0]?.id || '');},
    get period(){return this.periods.find(p=>String(p.id)===String(this.periodId));},
    get filtered(){let list=this.items.filter(i=>JSON.stringify(i).toLowerCase().includes(this.search.toLowerCase()));if(this.sortKey)list=[...list].sort((a,b)=>String(a[this.sortKey]??'').localeCompare(String(b[this.sortKey]??''),undefined,{numeric:true})*this.sortDirection);return list;},
    get pageCount(){return Math.max(1,Math.ceil(this.filtered.length/Number(this.pageSize)));},
    get visible(){const page=Math.min(this.page,this.pageCount);return this.filtered.slice((page-1)*this.pageSize,page*this.pageSize);},
    sort(key){this.sortDirection=this.sortKey===key?-this.sortDirection:1;this.sortKey=key;},
    async run(action,message='Perubahan disimpan'){if(this.saving)return false;this.saving=true;this.errors={};try{await action();notify(message);return true;}catch(e){this.errors=e.errors||{};notify(e.message,true);return false;}finally{this.saving=false;}},
};}
export function mergePage(base,extra){const target=Object.assign(base,extra);for(const key of Object.getOwnPropertyNames(extra)){const desc=Object.getOwnPropertyDescriptor(extra,key);if(desc&&typeof desc.get==='function')Object.defineProperty(target,key,desc);}return target;}
export async function allRows(endpoint,params={}){let data=[],page=1,last=1;do{const suffix=query({...params,per_page:100,page});const response=await api(endpoint+(endpoint.includes('?')?suffix.replace('?','&'):suffix));data.push(...rows(response));const body=unwrap(response);last=Number(response.pagination?.last_page || response.meta?.last_page || body?.pagination?.last_page || body?.last_page || body?.meta?.last_page || 1);page++;}while(page<=last);return data;}
