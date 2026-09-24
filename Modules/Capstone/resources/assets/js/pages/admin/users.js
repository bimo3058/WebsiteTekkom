import {api,unwrap,url,date,notify,allRows} from './shared.js';
import {context} from '../../api.js';

export function adminUsers(mode='list') {
    return {
        mode,items:[],allItems:[],user:null,loading:true,error:'',errors:{},saving:false,search:'',role:'all',status:'all',
        page:1,pageSize:10,sortKey:'name',sortDirection:'asc',selectedIds:[],deleting:[],loadVersion:0,
        url,date,
        async init(){if(mode==='list')await this.loadAll();else await this.load();},
        async loadAll(){
            this.loading=true;this.error='';
            try{this.allItems=await allRows('/admin/user-management');this.items=this.allItems;this.selectedIds=[];this.page=1;}
            catch(e){this.error=e.message;}finally{this.loading=false;}
        },
        async load(){
            const version=++this.loadVersion;this.loading=true;this.error='';
            try{
                const user=unwrap(await api('/admin/user-management/'+encodeURIComponent(context.params.id)));if(version!==this.loadVersion)return;this.user=user;
            }catch(e){if(version===this.loadVersion)this.error=e.message;}finally{if(version===this.loadVersion)this.loading=false;}
        },
        get filtered(){
            const term=this.search.trim().toLowerCase();
            let list=this.allItems.filter(u=>(this.role==='all'||(u.roles||[]).includes(this.role))&&(this.status==='all'||u.status===this.status));
            if(term)list=list.filter(u=>[u.name,u.email,u.nim,u.nip].filter(Boolean).some(v=>String(v).toLowerCase().includes(term)));
            const dir=this.sortDirection==='desc'?-1:1;
            return [...list].sort((a,b)=>this.sortKey==='created_at'?(new Date(a.created_at)-new Date(b.created_at))*dir:String(a[this.sortKey]??'').localeCompare(String(b[this.sortKey]??''),undefined,{numeric:true})*dir);
        },
        get pagedUsers(){return this.filtered.slice((this.userPage-1)*Number(this.pageSize),this.userPage*Number(this.pageSize));},
        get userTotal(){return this.filtered.length;},
        get userLastPage(){return Math.max(1,Math.ceil(this.userTotal/Number(this.pageSize)));},
        get userPage(){return Math.min(this.page,this.userLastPage);},
        get userFrom(){return this.userTotal?(this.userPage-1)*Number(this.pageSize)+1:0;},
        get userTo(){return Math.min(this.userPage*Number(this.pageSize),this.userTotal);},
        get userPageList(){const last=this.userLastPage,current=this.userPage;if(last<=5)return Array.from({length:last},(_,i)=>i+1);if(current<=2)return [1,2,3,'…',last];if(current>=last-1)return [1,'…',last-2,last-1,last];return [1,'…',current,'…',last];},
        usDate(value){if(!value)return '—';const d=value instanceof Date?value:new Date(value);return Number.isNaN(d.getTime())?'—':`${d.getMonth()+1}/${d.getDate()}/${d.getFullYear()}`;},
        longDate(value){if(!value)return '—';const d=value instanceof Date?value:new Date(value);return Number.isNaN(d.getTime())?'—':d.toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric'});},
        timeAgo(value){if(!value)return '—';const d=value instanceof Date?value:new Date(value);if(Number.isNaN(d.getTime()))return '—';const s=Math.max(0,Math.floor((Date.now()-d.getTime())/1000));if(s<60)return 'baru saja';const m=Math.floor(s/60);if(m<60)return m+' menit yang lalu';const h=Math.floor(m/60);if(h<24)return h+' jam yang lalu';const days=Math.floor(h/24);if(days<30)return days+' hari yang lalu';const months=Math.floor(days/30);if(months<12)return months+' bulan yang lalu';return Math.floor(months/12)+' tahun yang lalu';},
        rolePillClass(slug){return slug==='admin'?'bg-red-50 text-red-600':slug==='dosen'?'bg-teal-50 text-teal-600':slug==='mahasiswa'?'bg-amber-50 text-amber-600':'bg-slate-100 text-slate-500';},
        roleLabel(slug){return String(slug||'').charAt(0).toUpperCase()+String(slug||'').slice(1);},
        applySort(value){if(value==='date'){this.sortKey='created_at';this.sortDirection='desc';}else{this.sortKey='name';this.sortDirection='asc';}this.filter();},
        get selectedAll(){const selectable=this.pagedUsers.filter(u=>u.can_delete);return selectable.length>0&&selectable.every(u=>this.selectedIds.includes(u.id));},
        filter(){this.page=1;},
        async sort(key){this.sortDirection=this.sortKey===key&&this.sortDirection==='asc'?'desc':'asc';this.sortKey=key;this.filter();},
        toggleAll(checked){this.selectedIds=checked?this.pagedUsers.filter(u=>u.can_delete).map(u=>u.id):[];},
        toggleUser(id,checked){this.selectedIds=checked?[...new Set([...this.selectedIds,id])]:this.selectedIds.filter(item=>item!==id);},
        initials(user){return (user?.name||'?').trim().split(/\s+/).slice(0,2).map(s=>s[0]).join('').toUpperCase();},
        userUrl(user){return url('/admin/users/'+user.id);},
        confirmDelete(users){this.deleting=users.filter(u=>u.can_delete);this.errors={};if(this.deleting.length)document.getElementById('users-delete').showModal();},
        async remove(){
            if(this.saving||!this.deleting.length)return;this.saving=true;this.errors={};const failed=[];
            try{
                for(const user of this.deleting){try{await api('/admin/user-management/'+user.id,{method:'DELETE'});}catch(e){failed.push({user,message:e.message});}}
                if(failed.length){this.errors.root=failed.map(f=>f.user.name+': '+f.message).join('\n');this.deleting=failed.map(f=>f.user);if(mode==='list')await this.loadAll();return;}
                document.getElementById('users-delete').close();notify('User deleted');if(mode==='detail')window.location.assign(url('/admin/users'));else await this.loadAll();
            }finally{this.saving=false;}
        },
    };
}
