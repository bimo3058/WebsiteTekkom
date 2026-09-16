import Alpine from 'alpinejs';
import {api, context, notify, url, date, get, rows, unwrap, download, getPendingRequests} from './api.js';
import {registerPages} from './pages.js';
window.Capstone = {api,context,notify,url,date,get,rows,unwrap,download};
Alpine.data('capstoneShell',()=>({
    collapsed:false,mobileSidebar:false,unread:0,notices:[],pendingRequests:0,
    init(){
        this.pendingRequests=getPendingRequests();
        window.addEventListener('capstone-activity',event=>{this.pendingRequests=event.detail.pending;});
        try { this.collapsed=localStorage.getItem('capstone.sidebar.collapsed')==='true'; } catch {}
        window.addEventListener('capstone-notice',event=>{ const notice={...event.detail,id:Date.now()+Math.random()}; this.notices.push(notice); setTimeout(()=>{this.notices=this.notices.filter(n=>n.id!==notice.id);},6000); });
        const refreshUnread=()=>{if(context.actor)api('/notifications/unread-count',{activity:false}).then(data=>{this.unread=unwrap(data)?.count || 0;}).catch(()=>{});};
        refreshUnread();
        window.addEventListener('capstone-notifications-updated',refreshUnread);
    },
    toggleSidebar(){this.collapsed=!this.collapsed;try{localStorage.setItem('capstone.sidebar.collapsed',String(this.collapsed));}catch{}}
}));
Alpine.magic('capstone',()=>window.Capstone);
registerPages(Alpine);
window.Alpine = Alpine;
Alpine.start();
