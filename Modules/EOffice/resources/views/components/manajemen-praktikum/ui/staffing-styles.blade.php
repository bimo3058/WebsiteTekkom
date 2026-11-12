<x-eoffice::manajemen-praktikum.ui.admin-settings-styles />
<x-eoffice::manajemen-praktikum.ui.select-menu-styles />
@once
<style>
.mp-staffing-actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap}.mp-staffing-table th:nth-child(n+4),.mp-staffing-table td:nth-child(n+4){text-align:center}.mp-staffing-table th:nth-child(3),.mp-staffing-table td:nth-child(3){text-align:left}.mp-staffing-table th:nth-child(2){width:42%}.mp-staffing-table td:last-child form{display:inline-flex}.mp-staffing-danger{color:var(--c-error,#DF1C41)!important}.mp-staffing-danger:hover{background:#FEF1F4!important}.mp-staffing-teachers{display:flex;gap:12px 24px;flex-wrap:wrap}.mp-staffing-teachers>div{width:auto}.mp-staffing-mask{position:fixed;inset:0;z-index:9999;background:rgba(17,24,39,.4);display:flex;align-items:center;justify-content:center;padding:20px}.mp-staffing-modal{width:560px;max-width:100%;max-height:calc(100dvh - 40px);border:1px solid var(--c-border);border-radius:16px;background:white;box-shadow:0 20px 50px rgba(0,0,0,.15);display:flex;flex-direction:column;overflow:hidden}.mp-staffing-modal>header{padding:20px 24px;border-bottom:1px solid var(--c-border);flex-shrink:0}.mp-staffing-modal h2{font-size:16px;font-weight:700;margin:0;color:var(--c-fg)}.mp-staffing-modal>form{display:flex;flex-direction:column;min-height:0}.mp-staffing-modal .mp-settings-body{overflow-y:auto;min-height:300px}.mp-staffing-modal .mp-settings-footer{flex-shrink:0;justify-content:flex-end;border-radius:0}.mp-staffing-lookup{position:relative}.mp-staffing-options{position:relative;top:auto;margin-top:5px;max-height:220px;overflow:hidden;display:flex;flex-direction:column}.mp-staffing-search{padding:8px;border-bottom:1px solid var(--c-border);flex-shrink:0}.mp-staffing-results{overflow-y:auto;min-height:0}.mp-staffing-options .ta-option{white-space:normal;overflow-wrap:anywhere}.mp-staffing-empty{padding:16px;font-size:12px;color:var(--c-fg-muted);text-align:center}.mp-staffing-modal button:disabled{cursor:not-allowed;opacity:.6}.mp-staffing-help{font-size:12px;line-height:18px;color:var(--c-fg-muted);margin:6px 0 0}
.mp-staffing-section-title{font-size:14px;font-weight:600;line-height:20px;color:#353849;margin:0 0 10px}.mp-staffing-member-count{font-size:12px;line-height:18px;color:var(--c-fg-muted);margin:0}.mp-staffing-page .mp-card-header .mp-staffing-section-title{margin-bottom:4px}.mp-staffing-page .mp-page-title,.mp-staffing-page .mp-settings-name,.mp-staffing-page .mp-settings-table td{color:#353849}.mp-staffing-list .mp-settings-table th:first-child{width:30%}.mp-staffing-list .mp-settings-table th:nth-child(2){width:32%}.mp-staffing-list .mp-settings-table th:nth-child(3){width:25%}.mp-staffing-list .mp-settings-table th:last-child{width:13%}.mp-staffing-list-search{position:relative;max-width:100%}.mp-staffing-list-search>.mp-icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#808897;pointer-events:none}.mp-staffing-list-search input.mp-input{padding-left:38px}
@media(max-width:640px){.mp-staffing-actions{width:100%}.mp-staffing-modal>header{padding:16px}.mp-staffing-modal .mp-settings-body{min-height:0}.mp-staffing-table th:nth-child(2){width:auto}}
</style>
<style>
.mp-staffing-modal .mp-settings-body{display:flex;flex-direction:column;gap:20px;justify-content:flex-start;min-height:0;padding:24px;overflow:visible}
.mp-staffing-modal{overflow:visible}.mp-staffing-modal>header{border-radius:16px 16px 0 0}.mp-staffing-modal .mp-settings-footer{border-radius:0 0 16px 16px}
.mp-staffing-modal .mp-staffing-options{position:absolute;top:100%;left:0;right:0;z-index:20;max-height:min(220px,35dvh);background:#fff;border:1px solid #DFE1E7;border-radius:10px;box-shadow:0 8px 24px #0D0D1214}
.mp-staffing-results .ta-option{display:flex;align-items:center;justify-content:space-between;gap:10px;width:100%}.mp-staffing-results .ta-option>span:first-child{min-width:0;overflow-wrap:anywhere}
@media(prefers-reduced-motion:reduce){.mp-staffing-options{transition-duration:.01ms!important}}
</style>
<script>
window.practicumStaffingForm=function(config){return {...config,submitting:false,studentOpen:false,activeRow:0,userIds:config.userIds?.length?config.userIds:[config.userId||''],search:'',
    addStudent(){if(this.userIds.length<5)this.userIds.push('')},
    removeStudent(index){this.userIds.splice(index,1);this.userId=this.userIds[0]||'';this.studentOpen=false},
    labelFor(index){return this.students.find(s=>String(s.value)===String(this.userIds[index]))?.label||'Pilih mahasiswa...'},
    get coordinatorTaken(){return this.coordinatorId||this.assignments.some(a=>a.role==='koor')},
    get candidates(){return this.students},
    get filteredStudents(){const term=this.search.trim().toLocaleLowerCase();return this.candidates.filter(s=>s.label.toLocaleLowerCase().includes(term))},
    get selectedLabel(){return this.candidates.find(s=>String(s.value)===String(this.userId))?.label||'Pilih mahasiswa...'},
    hasSelectedRole(id){return this.assignments.some(a=>String(a.user_id)===String(id)&&a.role===this.role)||(this.role==='koor'&&String(this.coordinatorId)===String(id))},
    studentRoleLabel(id){const roles=this.assignments.filter(a=>String(a.user_id)===String(id)).map(a=>a.role==='koor'?'Koordinator':'Asisten');if(String(this.coordinatorId)===String(id)&&!roles.includes('Koordinator'))roles.push('Koordinator');return roles.length?roles.join(' · '):'Belum memiliki role'},
    get canSubmit(){const ids=this.role==='asprak'?this.userIds:[this.userId];return !this.submitting&&ids.length>=1&&ids.length<=5&&new Set(ids).size===ids.length&&ids.every(id=>!!id&&this.candidates.some(s=>String(s.value)===String(id))&&!this.hasSelectedRole(id))&&!(this.role==='koor'&&this.coordinatorTaken)},
    selectStudent(id,index=0){this.userIds[index]=String(id);if(index===0)this.userId=String(id);this.studentOpen=false;this.search=''},
    closeModal(){if(this.submitting)return;this.roleModalOpen=false;this.studentOpen=false;this.search=''},
    submit(event){if(!this.canSubmit){event.preventDefault();return;}this.submitting=true;}
};};
</script>
@endonce
