import {api, rows, unwrap, notify, url, date, download} from '../../api.js';

export {api, rows, unwrap, notify, url, date, download};
export const specializations = ['Software', 'Embedded', 'Network', 'Multimedia', 'AI', 'Blockchain'];
export const members = group => (group?.members || []).map(m => m.student?.name || m.name).filter(Boolean).join(', ');
export const statusColor = status => ['APPROVED','ACCEPT','COMPLETED','SUBMITTED','open'].includes(status)
    ? 'bg-green-100 text-green-700' : ['REJECTED','REJECT','FAIL','closed'].includes(status)
    ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700';
export function workspace(features = {}) {
    const base = {
        loading:true, error:'', saving:false, errors:{}, periods:[], selectedPeriod:'all', search:'', page:1, pageSize:10,
        date, url, members, statusColor,
        async init(){await this.load();},
        matches(item){return !this.search || JSON.stringify(item).toLowerCase().includes(this.search.toLowerCase());},
        inPeriod(id){return this.selectedPeriod==='all' || String(id)===String(this.selectedPeriod);},
        get pageCount(){return Math.max(1,Math.ceil(this.filtered.length/this.pageSize));},
        get visible(){return this.filtered.slice((Math.min(this.page,this.pageCount)-1)*this.pageSize,Math.min(this.page,this.pageCount)*this.pageSize);},
        open(id){document.getElementById(id).showModal();},
        close(id){document.getElementById(id).close();},
        async mutate(path,body,method='PUT',dialog=null){
            if(this.saving)return false;
            this.saving=true;this.errors={};
            try{const result=await api(path,{method,body});if(dialog)this.close(dialog);notify(result?.message || 'Changes saved');await this.load();return true;}
            catch(e){this.errors={...e.errors,root:e.message};notify(e.message,true);return false;}
            finally{this.saving=false;}
        },
    };
    return Object.defineProperties(base, Object.getOwnPropertyDescriptors(features));
}
