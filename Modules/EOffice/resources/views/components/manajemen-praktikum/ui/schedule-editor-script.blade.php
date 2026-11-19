@once
<script>
window.practicumScheduleEditor=function(config){return {
    ...config,submitting:false,original:'',originalRows:[],slotOpen:false,calendarSlot:null,slotPracticum:'',slotShift:'',slotStart:'',slotEnd:'',slotError:'',editingMeetingId:null,deleteConfirmOpen:false,
    init(){this.originalRows=JSON.parse(JSON.stringify(this.rows));const saved=this.saved;
        this.plans=this.catalog.map(p=>{const old=(saved.plans||[]).find(r=>r.praktikum_id===p.id);return {...p,praktikum_id:p.id,selected:!!old,modules:old?.modules||p.modules.map(m=>m.id),week_start:old?.week_start||'',slots:p.shifts.map(s=>{const slot=old?.slots?.find(r=>String(r.shift)===String(s.shift));return slot?{...slot}:{shift:s.shift,weekday:'',room_id:'',starts_at:'',ends_at:''}})};});
        this.original=JSON.stringify(this.payload);
        this.$nextTick(()=>this.restoreCalendarPosition());
    },
    rememberCalendarPosition(){
        if(!this.calendarPositionKey)return;
        try{const grid=document.querySelector('.mp-calendar-scroll');sessionStorage.setItem(this.calendarPositionKey,JSON.stringify({left:grid?.scrollLeft||0,top:grid?.scrollTop||0}));}catch{}
    },
    restoreCalendarPosition(){
        if(!this.calendarPositionKey)return;
        try{const stored=sessionStorage.getItem(this.calendarPositionKey);if(!stored)return;sessionStorage.removeItem(this.calendarPositionKey);const position=JSON.parse(stored),grid=document.querySelector('.mp-calendar-scroll');if(grid){grid.scrollLeft=position.left;grid.scrollTop=position.top;if(grid.dataset)grid.dataset.positionRestored='true';}}catch{}
    },
    get visiblePlans(){return this.plans},
    get payload(){const overrides=JSON.parse(JSON.stringify(this.saved.overrides||{}));
        if(this.kind==='new')for(const row of this.rows){const original=this.originalRows.find(r=>r.id===row.id);if(!original)continue;const key=row.praktikum_id+'|'+row.module_id+'|'+row.shift;for(const field of ['date','room_id','starts_at','ends_at'])if(String(row[field]??'')!==String(original[field]??'')){overrides[key]||={};overrides[key][field]=row[field];}}
        const selected=this.plans.filter(p=>p.selected),period=selected.length?this.periods.find(p=>p.year===selected[0].year&&p.semester===selected[0].semester):this.periods.find(p=>p.id===this.periodId);
        return {period_id:period?.id||'',reason:this.reason,...(this.kind==='new'?{plans:selected.map(p=>({praktikum_id:p.id,week_start:p.week_start,modules:p.modules,slots:p.slots})),overrides,excluded_meetings:[...(this.saved.excluded_meetings||[])]}:{meetings:this.rows.map(r=>({id:r.id,date:r.date,room_id:r.room_id,starts_at:r.starts_at,ends_at:r.ends_at}))})};
    },get dirty(){return JSON.stringify(this.payload)!==this.original},
    get editingMeeting(){return this.rows.find(row=>row.id===this.editingMeetingId)},
    get meetingFormUnchanged(){const row=this.editingMeeting;return !!row&&this.slotStart===row.starts_at&&this.slotEnd===row.ends_at},
    editCalendarMeeting(id,availableSlots=[]){
        const row=this.rows.find(row=>row.id===id);if(!row||this.kind!=='new'||this.submitting)return;
        this.editingMeetingId=id;this.deleteConfirmOpen=false;
        this.calendarSlot={date:row.date,roomId:String(row.room_id),roomName:row.room_name||'Ruangan',start:row.starts_at,end:row.ends_at,
            availableSlots:[...availableSlots.filter(slot=>slot.date===row.date&&String(slot.roomId)===String(row.room_id)),{start:row.starts_at,end:row.ends_at}]};
        this.slotPracticum=row.praktikum_id;this.slotShift=String(row.shift);this.slotStart=row.starts_at;this.slotEnd=row.ends_at;this.slotError='';this.slotOpen=true;
    },
    askDeleteDraftMeeting(){if(!this.editingMeeting||this.submitting)return;this.slotOpen=false;this.deleteConfirmOpen=true},
    deleteDraftMeeting(){
        const row=this.editingMeeting;if(!row||!this.deleteConfirmOpen||this.submitting)return;
        const key=row.praktikum_id+'|'+row.module_id+'|'+row.shift;
        this.saved.excluded_meetings=[...new Set([...(this.saved.excluded_meetings||[]),key])];
        this.rows=this.rows.filter(item=>item.id!==row.id);delete (this.saved.overrides||{})[key];
        this.deleteConfirmOpen=false;this.slotOpen=false;this.editingMeetingId=null;
        this.$nextTick(()=>this.$refs?.draftForm?.requestSubmit());
    },
    get slotPlan(){return this.visiblePlans.find(p=>p.id===this.slotPracticum)},
    get calendarWeekStart(){if(!this.calendarSlot)return '';const date=new Date(this.calendarSlot.date+'T00:00:00Z');date.setUTCDate(date.getUTCDate()-(date.getUTCDay()+6)%7);return date.toISOString().slice(0,10)},
    selectCalendarSlot(slot){
        if(this.kind!=='new'||!this.visiblePlans.length)return;
        this.editingMeetingId=null;this.deleteConfirmOpen=false;
        this.calendarSlot=slot;this.slotPracticum='';this.slotShift='';
        this.slotStart=slot.start;this.slotEnd=slot.end;this.slotError='';this.slotOpen=true;
    },
    applyCalendarSlot(){
        if(this.submitting)return;
        if(this.editingMeetingId){
            const row=this.editingMeeting;if(!row)return;
            if(this.meetingFormUnchanged)return;
            if(!this.slotStart||!this.slotEnd||this.slotStart>=this.slotEnd){this.slotError='Jam selesai harus setelah jam mulai.';return;}
            let cursor=this.slotStart;
            for(const slot of [...this.calendarSlot.availableSlots].sort((a,b)=>a.start.localeCompare(b.start))){if(slot.end<=cursor)continue;if(slot.start>cursor)break;cursor=slot.end;if(cursor>=this.slotEnd)break;}
            if(cursor<this.slotEnd){this.slotError='Rentang jam melewati jadwal terpakai atau waktu yang tidak tersedia.';return;}
            Object.assign(row,{starts_at:this.slotStart,ends_at:this.slotEnd});
            this.slotOpen=false;this.$nextTick(()=>this.$refs?.draftForm?.requestSubmit());return;
        }
        const plan=this.slotPlan,shift=plan?.slots.find(s=>String(s.shift)===String(this.slotShift)),period=this.periods.find(p=>p.year===plan?.year&&p.semester===plan?.semester);
        if(!plan){this.slotError='Pilih nama praktikum yang masih aktif.';return;}
        if(!/^[1-9][0-9]*$/.test(String(this.slotShift)) || !shift){this.slotError='Isi angka shift yang sesuai pembagian praktikan pada praktikum ini.';return;}
        if(!period){this.slotError='Pengaturan jadwal praktikum ini belum lengkap. Hubungi Admin Praktikum untuk melengkapi batas pelaksanaan semester.';return;}
        if(this.calendarSlot.date<period.start||this.calendarSlot.date>period.end){this.slotError='Tanggal slot berada di luar periode pelaksanaan. Pilih minggu lain atau sesuaikan periode.';return;}
        if(!this.slotStart||!this.slotEnd||this.slotStart>=this.slotEnd){this.slotError='Jam selesai harus setelah jam mulai.';return;}
        if(this.calendarSlot.availableSlots){let cursor=this.slotStart;for(const slot of [...this.calendarSlot.availableSlots].sort((a,b)=>a.start.localeCompare(b.start))){if(slot.end<=cursor)continue;if(slot.start>cursor)break;cursor=slot.end;if(cursor>=this.slotEnd)break;}if(cursor<this.slotEnd){this.slotError='Rentang jam melewati jadwal terpakai atau waktu yang tidak tersedia. Pilih jam lain.';return;}}
        this.periodId=period.id;plan.selected=true;if(!plan.week_start)plan.week_start=this.calendarWeekStart;
        Object.assign(shift,{weekday:String(this.calendarSlot.weekday),room_id:String(this.calendarSlot.roomId),starts_at:this.slotStart,ends_at:this.slotEnd});
        this.slotOpen=false;this.$nextTick(()=>this.$refs?.draftForm?.requestSubmit());
    }
};};
</script>
@endonce
