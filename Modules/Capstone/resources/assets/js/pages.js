import {assessmentConfig,gradeConfig} from './pages/admin/configuration.js';
import {adminGroups,expoAdmin,semproAdmin,finalizationAdmin} from './pages/admin/management.js';
import {progressAdmin,peerDashboard,documentUploads} from './pages/admin/monitoring.js';
import {reportsAdmin} from './pages/admin/reports.js';
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
import {lecturerEvaluations,lecturerSupervisorEvaluations,lecturerEvaluationForm} from './pages/dosen/evaluations.js';

export function registerPages(Alpine) {
    Alpine.data('adminUsers',adminUsers);
    for(const [name,factory] of Object.entries({lecturerTitles,lecturerTitleDetail,lecturerApprovals,lecturerBids,lecturerGroups,lecturerDocuments,lecturerRequests,lecturerEvaluations,lecturerSupervisorEvaluations,lecturerEvaluationForm}))Alpine.data(name,factory);
    for(const [name,factory] of Object.entries({adminAssessmentConfig:assessmentConfig,adminGradeConfig:gradeConfig,adminGroups,adminExpo:expoAdmin,adminSempro:semproAdmin,adminFinalization:finalizationAdmin,adminProgress:progressAdmin,adminPeerDashboard:peerDashboard,adminDocumentUploads:documentUploads,adminReports:reportsAdmin}))Alpine.data(name,factory);
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
        get filtered(){let result=this.items.filter(item=>(!this.search || JSON.stringify(item).toLowerCase().includes(this.search.toLowerCase())) && Object.entries(this.filters).every(([key,value])=>!value || value==='all' || String(get(item,key,''))===String(value)));if(this.sortKey)result=[...result].sort((a,b)=>{const left=get(a,this.sortKey,''),right=get(b,this.sortKey,'');return (typeof left==='number' ? left-right : String(left).localeCompare(String(right),'id',{numeric:true}))*this.sortDirection;});return result;},
        get pageCount(){return Math.max(1,Math.ceil(this.filtered.length/this.pageSize));},
        get visible(){const start=(Math.min(this.page,this.pageCount)-1)*this.pageSize;return this.filtered.slice(start,start+Number(this.pageSize));},
        sort(key){this.sortDirection=this.sortKey===key ? -this.sortDirection : 1;this.sortKey=key;},
        value(item,key){return get(item,key);},date,url,
        reset(){this.search='';this.filters={};this.page=1;},
        edit(item=null){this.editing=item;this.errors={};this.form=structuredClone(item || config.defaults || {});document.getElementById(config.dialog || 'record-form').showModal();},
        async save(){this.saving=true;this.errors={};try{await api((config.writeEndpoint || config.endpoint)+(this.editing ? '/'+this.editing.id : ''),{method:this.editing ? 'PUT' : 'POST',body:this.form});document.getElementById(config.dialog || 'record-form').close();notify('Data berhasil disimpan');await this.load();}catch(e){this.errors=e.errors;notify(e.message,true);}finally{this.saving=false;}},
        confirmDelete(item){this.deleting=item;document.getElementById('confirm-delete').showModal();},
        async remove(){if(!this.deleting || this.saving)return;this.saving=true;try{await api((config.writeEndpoint||config.endpoint)+'/'+this.deleting.id,{method:'DELETE'});document.getElementById('confirm-delete').close();notify('Data berhasil dihapus');this.deleting=null;await this.load();}catch(e){notify(e.message,true);}finally{this.saving=false;}},
        async toggle(item){try{await api((config.writeEndpoint||config.endpoint)+'/'+item.id,{method:'PUT',body:{...item,is_active:!item.is_active}});await this.load();}catch(e){notify(e.message,true);}},
    }));
}
