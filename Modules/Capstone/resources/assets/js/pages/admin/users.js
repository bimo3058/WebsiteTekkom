import {api,rows,unwrap,url,date,notify,query} from './shared.js';
import {context} from '../../api.js';

export function adminUsers(mode='list') {
    return {
        mode,items:[],user:null,loading:true,error:'',errors:{},saving:false,search:'',role:'all',status:'all',
        page:1,pageSize:10,pagination:{total:0,last_page:1},sortKey:'name',sortDirection:'asc',selectedIds:[],deleting:[],loadVersion:0,
        form:{name:'',email:'',password:'',roles:['mahasiswa'],nim:'',nip:'',cohort_year:''},url,date,
        async init(){await this.load();},
        async load(){
            const version=++this.loadVersion;this.loading=true;this.error='';
            try{
                if(mode==='new')return;
                if(mode==='list'){
                    const result=await api('/admin/user-management'+query({search:this.search,role:this.role,status:this.status,page:this.page,per_page:this.pageSize,sort_by:this.sortKey,sort_order:this.sortDirection}));
                    if(version!==this.loadVersion)return;this.items=rows(result);this.pagination=result.pagination||result;this.selectedIds=[];
                    if(this.page>this.pageCount){this.page=this.pageCount;await this.load();}
                }else{
                    const user=unwrap(await api('/admin/user-management/'+encodeURIComponent(context.params.id)));if(version!==this.loadVersion)return;this.user=user;
                    this.form={name:user.name,email:user.email,password:'',roles:[...user.roles],nim:user.nim||'',nip:user.nip||'',cohort_year:user.cohort_year||''};
                }
            }catch(e){if(version===this.loadVersion)this.error=e.message;}finally{if(version===this.loadVersion)this.loading=false;}
        },
        get pageCount(){return Math.max(1,Number(this.pagination.last_page)||1);},
        get selectedAll(){const selectable=this.items.filter(u=>u.can_delete);return selectable.length>0&&selectable.every(u=>this.selectedIds.includes(u.id));},
        get hasStudent(){return this.form.roles.includes('mahasiswa');},
        get hasLecturer(){return this.form.roles.includes('dosen');},
        get editable(){return mode==='new'||this.user?.can_edit===true;},
        async filter(){this.page=1;await this.load();},
        async sort(key){this.sortDirection=this.sortKey===key&&this.sortDirection==='asc'?'desc':'asc';this.sortKey=key;await this.filter();},
        toggleAll(checked){this.selectedIds=checked?this.items.filter(u=>u.can_delete).map(u=>u.id):[];},
        toggleUser(id,checked){this.selectedIds=checked?[...new Set([...this.selectedIds,id])]:this.selectedIds.filter(item=>item!==id);},
        toggleRole(role,checked){if(!checked){this.form.roles=this.form.roles.filter(r=>r!==role);return;}this.form.roles=role==='mahasiswa'?['mahasiswa']:[...new Set([...this.form.roles.filter(r=>r!=='mahasiswa'),role])];},
        initials(user){return (user?.name||'?').trim().split(/\s+/).slice(0,2).map(s=>s[0]).join('').toUpperCase();},
        userUrl(user,edit=false){return url('/admin/users/'+user.id+(edit?'/edit':''));},
        async save(){
            if(this.saving||!this.editable)return;this.errors={};
            if(!this.form.roles.length){this.errors.roles=['Select at least one role.'];return;}
            const body={name:this.form.name,email:this.form.email,roles:this.form.roles};
            if(this.form.password)body.password=this.form.password;
            if(this.hasStudent){body.nim=this.form.nim;body.cohort_year=this.form.cohort_year;}
            if(this.hasLecturer)body.nip=this.form.nip;
            this.saving=true;
            try{const user=unwrap(await api('/admin/user-management'+(mode==='new'?'':'/'+context.params.id),{method:mode==='new'?'POST':'PUT',body}));this.form.password='';notify('User saved');window.location.assign(this.userUrl(user));}
            catch(e){this.errors={...e.errors,root:e.message};notify(e.message,true);}finally{this.saving=false;}
        },
        confirmDelete(users){this.deleting=users.filter(u=>u.can_delete);this.errors={};if(this.deleting.length)document.getElementById('users-delete').showModal();},
        async remove(){
            if(this.saving||!this.deleting.length)return;this.saving=true;this.errors={};const failed=[];
            try{
                for(const user of this.deleting){try{await api('/admin/user-management/'+user.id,{method:'DELETE'});}catch(e){failed.push({user,message:e.message});}}
                if(failed.length){this.errors.root=failed.map(f=>f.user.name+': '+f.message).join('\n');this.deleting=failed.map(f=>f.user);if(mode==='list')await this.load();return;}
                document.getElementById('users-delete').close();notify('User deleted');if(mode==='detail')window.location.assign(url('/admin/users'));else await this.load();
            }finally{this.saving=false;}
        },
    };
}
