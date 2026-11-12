import {assessmentConfig,gradeConfig} from './pages/admin/configuration.js';
import {adminGroups,expoAdmin,semproAdmin,finalizationAdmin} from './pages/admin/management.js';
import {progressAdmin,peerDashboard,documentUploads,auditLogsAdmin} from './pages/admin/monitoring.js';
import {reportsAdmin} from './pages/admin/reports.js';
import {periodRegistrationsAdmin} from './pages/admin/period-registrations.js';
import {taRegistrationsAdmin} from './pages/admin/ta-registrations.js';
import {adminUsers} from './pages/admin/users.js';
import {studentGroup} from './pages/mahasiswa/group.js';
import {studentMarketplace} from './pages/mahasiswa/marketplace.js';
import {studentTitleDetail} from './pages/mahasiswa/title-detail.js';
import {studentProposals} from './pages/mahasiswa/proposals.js';
import {studentBidding} from './pages/mahasiswa/bidding.js';
import {api,context,notify,url,date,get,rows,unwrap,download} from './api.js';
import {registerDashboards} from './pages/dashboards.js';
import {registerRegistration} from './pages/registration.js';
import {registerSchedules} from './pages/schedules.js';
import {registerDocuments} from './pages/documents.js';
import {registerPeerReview} from './pages/peer-review.js';
import {registerExpo} from './pages/expo.js';
import {registerGrades} from './pages/grades.js';
import {registerNotifications} from './pages/notifications.js';
import {registerTaSubmission} from './pages/ta-submission.js';
import {registerAdminTaDefense} from './pages/admin-ta-defense.js';
import {registerDocumentConfiguration} from './pages/document-configuration.js';
import {registerPeriodWizard} from './pages/period-wizard.js';
import {registerAssessmentBank} from './pages/assessment-bank.js';
import {lecturerTitles,lecturerTitleDetail,lecturerApprovals,lecturerBids} from './pages/dosen/titles.js';
import {lecturerGroups,lecturerDocuments,lecturerRequests} from './pages/dosen/mentoring.js';
import {lecturerEvaluations,lecturerSupervisorEvaluations,lecturerEvaluationForm,lecturerExpoReview} from './pages/dosen/evaluations.js';

export function registerPages(Alpine) {
    Alpine.data('adminUsers',adminUsers);
    for(const [name,factory] of Object.entries({lecturerTitles,lecturerTitleDetail,lecturerApprovals,lecturerBids,lecturerGroups,lecturerDocuments,lecturerRequests,lecturerEvaluations,lecturerSupervisorEvaluations,lecturerEvaluationForm,lecturerExpoReview}))Alpine.data(name,factory);
    for(const [name,factory] of Object.entries({adminAssessmentConfig:assessmentConfig,adminGradeConfig:gradeConfig,adminGroups,adminExpo:expoAdmin,adminSempro:semproAdmin,adminFinalization:finalizationAdmin,adminProgress:progressAdmin,adminPeerDashboard:peerDashboard,adminDocumentUploads:documentUploads,adminAuditLogs:auditLogsAdmin,adminReports:reportsAdmin,adminPeriodRegistrations:periodRegistrationsAdmin,adminTaRegistrations:taRegistrationsAdmin}))Alpine.data(name,factory);
    Alpine.data('studentGroup', studentGroup);
    Alpine.data('studentMarketplace', studentMarketplace);
    Alpine.data('studentTitleDetail', studentTitleDetail);
    Alpine.data('studentProposals', studentProposals);
    Alpine.data('studentBidding', studentBidding);
    registerDashboards(Alpine);
    registerRegistration(Alpine);
    registerSchedules(Alpine);
    registerDocuments(Alpine);
    registerPeerReview(Alpine);
    registerExpo(Alpine);
    registerGrades(Alpine);
    registerNotifications(Alpine);
    registerTaSubmission(Alpine);
    registerAdminTaDefense(Alpine);
    registerDocumentConfiguration(Alpine);
    registerPeriodWizard(Alpine);
    registerAssessmentBank(Alpine);
    Alpine.data('capstoneTable',(config)=>({
        config,items:[],loading:true,error:'',search:'',filters:{},sortKey:'',sortDirection:1,page:1,pageSize:10,editing:null,form:{},errors:{},saving:false,deleting:null,expanded:null,
        async init(){await this.load();},
        async load(){this.loading=true;this.error='';try{this.items=rows(await api(config.endpoint));}catch(e){this.error=e.message;}finally{this.loading=false;}},
        get filtered(){const fixed=config.fixedFilter || {};let result=this.items.filter(item=>(!this.search || JSON.stringify(item).toLowerCase().includes(this.search.toLowerCase())) && Object.entries({...fixed,...this.filters}).every(([key,value])=>!value || value==='all' || String(get(item,key,''))===String(value)));if(this.sortKey)result=[...result].sort((a,b)=>{const left=get(a,this.sortKey,''),right=get(b,this.sortKey,'');return (typeof left==='number' ? left-right : String(left).localeCompare(String(right),'id',{numeric:true}))*this.sortDirection;});return result;},
        get pageCount(){return Math.max(1,Math.ceil(this.filtered.length/Number(this.pageSize)));},
        get visible(){const start=(Math.min(this.page,this.pageCount)-1)*Number(this.pageSize);return this.filtered.slice(start,start+Number(this.pageSize));},
        get showingFrom(){return this.filtered.length ? (Math.min(this.page,this.pageCount)-1)*Number(this.pageSize)+1 : 0;},
        get showingTo(){return Math.min(Math.min(this.page,this.pageCount)*Number(this.pageSize),this.filtered.length);},
        get pageList(){const total=this.pageCount,current=Math.min(this.page,total);if(total<=7)return Array.from({length:total},(_,i)=>i+1);const keep=new Set([1,2,current-1,current,current+1,total-1,total].filter(p=>p>=1&&p<=total));const sorted=[...keep].sort((a,b)=>a-b);const out=[];let prev=0;for(const p of sorted){if(p-prev>1)out.push('…');out.push(p);prev=p;}return out;},
        sort(key){this.sortDirection=this.sortKey===key ? -this.sortDirection : 1;this.sortKey=key;},
        goToPage(target){const count=this.pageCount;this.page=Math.min(Math.max(1,Number(target)||1),count);},
        firstPage(){this.goToPage(1);},
        prevPage(){this.goToPage(this.page-1);},
        nextPage(){this.goToPage(this.page+1);},
        lastPage(){this.goToPage(this.pageCount);},
        resetSearch(){this.search='';this.goToPage(1);},
        value(item,key){return get(item,key);},date,url,
        reset(){this.search='';this.filters={};this.page=1;},
        edit(item=null){this.editing=item;this.errors={};this.form=structuredClone(item || config.defaults || {});const d=document.getElementById(config.dialog || 'record-form');if(d&&!d.open)d.showModal();},
        async save(){this.saving=true;this.errors={};try{await api((config.writeEndpoint || config.endpoint)+(this.editing ? '/'+this.editing.id : ''),{method:this.editing ? 'PUT' : 'POST',body:this.form});const d=document.getElementById(config.dialog || 'record-form');if(d&&d.open)d.close();notify('Data berhasil disimpan');await this.load();}catch(e){this.errors=e.errors;notify(e.message,true);}finally{this.saving=false;}},
        confirmDelete(item){this.deleting=item;const d=document.getElementById('confirm-delete');if(d&&!d.open)d.showModal();},
        async remove(){if(!this.deleting || this.saving)return;this.saving=true;try{await api((config.writeEndpoint||config.endpoint)+'/'+this.deleting.id,{method:'DELETE'});const d=document.getElementById('confirm-delete');if(d&&d.open)d.close();notify('Data berhasil dihapus');this.deleting=null;await this.load();}catch(e){notify(e.message,true);}finally{this.saving=false;}},
        async toggle(item){try{await api((config.writeEndpoint||config.endpoint)+'/'+item.id,{method:'PUT',body:{...item,is_active:!item.is_active}});await this.load();}catch(e){notify(e.message,true);}},
    }));
}
