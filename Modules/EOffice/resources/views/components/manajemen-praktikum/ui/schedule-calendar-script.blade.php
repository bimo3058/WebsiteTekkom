@once
<script>
window.practicumCalendarPicker=function(config){return {
    ...config,range:null,anchor:null,selecting:false,selectionError:'',hoveredColumn:'',
    init(){this.$nextTick(()=>{const grid=this.$el?.querySelector('.mp-calendar-scroll'),today=grid?.querySelector('[data-calendar-today]');if(grid&&today&&!grid.dataset.positionRestored)grid.scrollLeft=Math.max(0,today.offsetLeft-72);if(this.initial)this.pickCalendarRange(this.initial)})},
    rangeAvailable(range){
        const slots=this.slots.filter(slot=>slot.date===range.date&&String(slot.roomId)===String(range.roomId)&&slot.start<range.end&&slot.end>range.start).sort((a,b)=>a.start.localeCompare(b.start));
        let cursor=range.start;
        for(const slot of slots){if(slot.start>cursor)return false;if(slot.end>cursor)cursor=slot.end;}
        return range.start<range.end&&cursor>=range.end;
    },
    startCalendarRange(slot,event){
        if(event.button!==undefined&&event.button!==0)return;
        if(event.pointerType==='mouse')event.preventDefault();
        if(!this.rangeAvailable(slot))return;
        this.anchor={...slot};this.range={...slot};this.selecting=true;this.selectionError='';
    },
    extendCalendarRange(slot){
        if(!this.selecting)return;
        if(slot.date!==this.anchor.date||String(slot.roomId)!==String(this.anchor.roomId)){this.selectionError='Pilih jam pada ruangan dan tanggal yang sama.';return;}
        const range={...this.anchor,start:slot.start<this.anchor.start?slot.start:this.anchor.start,end:slot.end>this.anchor.end?slot.end:this.anchor.end};
        if(!this.rangeAvailable(range)){this.selectionError='Rentang pilihan melewati jadwal terpakai atau waktu yang tidak tersedia.';return;}
        this.range=range;this.selectionError='';
    },
    finishCalendarRange(){
        if(!this.selecting)return;this.selecting=false;
        if(this.selectionError){this.range=null;return;}
        this.pickCalendarRange(this.range);
    },
    pickCalendarRange(range){
        if(!this.rangeAvailable(range)){this.selectionError='Slot sudah tidak tersedia. Pilih rentang jam lain.';this.range=null;return;}
        this.range={...range};this.selectionError='';
        if(this.redirectUrl){
            const url=new URL(this.redirectUrl,window.location.href);
            for(const [key,value] of Object.entries({week:range.date,calendar_room:range.roomId,slot_date:range.date,slot_room:range.roomId,slot_start:range.start,slot_end:range.end}))url.searchParams.set(key,value);
            window.location.assign(url.toString());
        }else this.selectCalendarSlot({...range,availableSlots:this.slots.filter(slot=>slot.date===range.date&&String(slot.roomId)===String(range.roomId))});
    },
    calendarRangeContains(slot){return !!this.range&&this.range.date===slot.date&&String(this.range.roomId)===String(slot.roomId)&&slot.start<this.range.end&&slot.end>this.range.start},
    calendarRangeClasses(slot){const selected=this.calendarRangeContains(slot);return {'mp-calendar-selected':selected,'mp-calendar-selected-start':selected&&slot.start===this.range.start,'mp-calendar-selected-end':selected&&slot.end===this.range.end,'mp-calendar-hover':this.hoveredColumn===slot.date+'|'+slot.roomId}},
    cancelCalendarRange(){this.selecting=false;this.range=null;this.selectionError=''}
};};
</script>
@endonce
