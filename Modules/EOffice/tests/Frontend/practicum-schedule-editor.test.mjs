import {readFileSync} from 'node:fs';
import vm from 'node:vm';
import test from 'node:test';
import assert from 'node:assert/strict';

const source=readFileSync(new URL('../../resources/views/components/manajemen-praktikum/ui/schedule-editor-script.blade.php',import.meta.url),'utf8').match(/<script>([\s\S]*?)<\/script>/)[1];
function editor(){
    const context=vm.createContext({window:{},document:{getElementById:()=>({scrollIntoView(){}})}});vm.runInContext(source,context);
    const value=context.window.practicumScheduleEditor({kind:'new',periodId:'period',reason:'',saved:{},rows:[],plans:[],periods:[{id:'period',year:'2026/2027',semester:'Ganjil',start:'2026-11-09',end:'2027-01-31'}],catalog:[{id:'practicum',name:'Jaringan',year:'2026/2027',semester:'Ganjil',modules:[{id:'1',name:'Modul 1'}],shifts:[{shift:'1',participants:2},{shift:'2',participants:2}]}]});
    value.$nextTick=callback=>callback();value.init();return value;
}
const slot={date:'2026-11-18',weekday:3,roomId:'2',roomName:'Lab B',start:'09:00',end:'10:00'};
test('minute precision is preserved when saving a calendar selection',()=>{
    const value=editor();choose(value,{...slot,availableSlots:[{...slot,start:'09:00',end:'11:00'}]});value.slotStart='09:13';value.slotEnd='10:47';value.applyCalendarSlot();
    assert.equal(value.slotOpen,false);assert.equal(value.payload.plans[0].slots[0].starts_at,'09:13');assert.equal(value.payload.plans[0].slots[0].ends_at,'10:47');
});
test('time selector offers every minute and clamps the campus opening and closing bounds',()=>{
    const template=readFileSync(new URL('../../resources/views/components/manajemen-praktikum/ui/time-select.blade.php',import.meta.url),'utf8');
    const expression=template.match(/x-data="([\s\S]*?)" x-modelable/)[1].replace('@js($min)',"'07:13'").replace('@js($max)',"'23:47'");
    const value=vm.runInNewContext('('+expression+')');value.timeValue='09:13';
    assert.equal(Object.keys(value.timeMinuteOptions).length,60);value.timeMinute='17';assert.equal(value.timeValue,'09:17');
    value.timeHour='07';assert.equal(value.timeValue,'07:17');assert.equal(value.timeMinuteOptions['12'],undefined);
    value.timeValue='09:59';value.timeHour='23';assert.equal(value.timeValue,'23:47');assert.equal(value.timeMinuteOptions['48'],undefined);
});
test('searchable dropdown filters names and minute entries stay in numeric order',()=>{
    const template=readFileSync(new URL('../../resources/views/components/manajemen-praktikum/ui/ta-select.blade.php',import.meta.url),'utf8');
    const expression=template.match(/x-data="([\s\S]*?)" x-modelable/)[1].replace('{{ json_encode($options) }}',"{'01':'Jaringan','02':'Sistem Digital'}");
    const value=vm.runInNewContext('('+expression+')');value.search='  jaringan ';assert.equal(value.filteredEntries.length,1);assert.equal(value.filteredEntries[0][1],'Jaringan');
    value.search='';value.options={'00':'00','09':'09','10':'10','59':'59'};assert.deepEqual(Array.from(value.filteredEntries,x=>x[0]),['00','09','10','59']);
});
function editableMeeting(value){
    const row={id:'meeting-1',praktikum_id:'practicum',module_id:'1',module_name:'Modul 1',practicum_name:'Jaringan',shift:'1',date:'2026-11-18',room_id:'2',room_name:'Lab B',starts_at:'09:00',ends_at:'10:00',note:'Catatan lama'};
    value.rows=[{...row},{...row,id:'meeting-2',shift:'2'}];value.originalRows=JSON.parse(JSON.stringify(value.rows));
    value.plans[0].selected=true;value.plans[0].week_start='2026-11-16';return row;
}
test('clicking a saved draft opens a prefilled single-meeting form',()=>{
    const value=editor(),row=editableMeeting(value);value.editCalendarMeeting(row.id,[slot]);
    assert.equal(value.slotOpen,true);assert.equal(value.slotPracticum,'practicum');assert.equal(value.slotShift,'1');assert.equal(value.slotStart,'09:00');assert.equal(value.slotNote,undefined);assert.equal(value.editingMeeting.module_name,'Modul 1');
});
test('draft deletion requires confirmation and removes only the selected occurrence',()=>{
    const value=editor(),row=editableMeeting(value);let saves=0;value.$refs={draftForm:{requestSubmit(){saves++}}};
    value.editCalendarMeeting(row.id,[slot]);value.deleteDraftMeeting();assert.equal(value.rows.length,2);assert.equal(saves,0);
    value.askDeleteDraftMeeting();assert.equal(value.deleteConfirmOpen,true);assert.equal(value.rows.length,2);
    value.deleteDraftMeeting();assert.equal(saves,1);assert.equal(value.rows.length,1);assert.equal(value.rows[0].id,'meeting-2');
    assert.deepEqual(Array.from(value.payload.excluded_meetings),['practicum|1|1']);assert.equal(value.payload.plans.length,1);
});
test('editing one occurrence changes its override rather than the weekly shift plan',()=>{
    const value=editor(),row=editableMeeting(value);value.editCalendarMeeting(row.id,[slot]);value.slotEnd='09:45';value.slotNote='Baru';value.applyCalendarSlot();
    assert.equal(value.payload.overrides['practicum|1|1'].ends_at,'09:45');assert.equal(value.payload.overrides['practicum|1|1'].note,undefined);assert.equal(value.rows[0].note,'Catatan lama');assert.equal(value.rows[1].ends_at,'10:00');
    assert.equal(value.payload.plans[0].slots[0].ends_at,'');
});
test('reopened payload retains exclusions and stale clicks cannot remove other entries',()=>{
    const value=editor();editableMeeting(value);value.saved.excluded_meetings=['other|1|2'];value.editCalendarMeeting('missing',[slot]);value.askDeleteDraftMeeting();value.deleteDraftMeeting();
    assert.equal(value.rows.length,2);assert.deepEqual(Array.from(value.payload.excluded_meetings),['other|1|2']);
});
test('saving and reopening a draft restores its horizontal and vertical calendar scroll once',()=>{
    const grid={scrollLeft:830,scrollTop:245},storage=new Map();
    const context=vm.createContext({window:{},document:{querySelector:()=>grid},sessionStorage:{setItem:(key,value)=>storage.set(key,value),getItem:key=>storage.get(key),removeItem:key=>storage.delete(key)}});
    vm.runInContext(source,context);const value=context.window.practicumScheduleEditor({calendarPositionKey:'draft-1'});
    value.rememberCalendarPosition();grid.scrollLeft=0;grid.scrollTop=0;value.restoreCalendarPosition();assert.equal(grid.scrollLeft,830);assert.equal(grid.scrollTop,245);assert.equal(storage.size,0);
});
function choose(value,range=slot){value.selectCalendarSlot(range);value.slotPracticum='practicum';value.slotShift='1';}

test('calendar selection updates only the chosen shift and aligns the first week to Monday',()=>{
    const value=editor();choose(value);assert.equal(value.slotOpen,true);value.slotShift='2';value.slotEnd='11:00';value.slotNote=' Modul 1 ';value.applyCalendarSlot();
    assert.equal(value.slotOpen,false);assert.equal(value.dirty,true);const plan=value.payload.plans[0];assert.equal(plan.week_start,'2026-11-16');
    assert.equal(plan.slots[1].weekday,'3');assert.equal(plan.slots[1].room_id,'2');assert.equal(plan.slots[1].ends_at,'11:00');assert.equal(plan.slots[1].note,undefined);assert.equal(plan.slots[0].room_id,'');assert.equal(plan.modules[0],'1');
});
test('dates outside the semester cannot modify the draft',()=>{
    const value=editor();choose(value,{...slot,date:'2027-02-03'});value.applyCalendarSlot();assert.equal(value.dirty,false);assert.match(value.slotError,/di luar periode/);assert.equal(value.slotOpen,true);
});
test('invalid duration and empty shift leave the draft unchanged',()=>{
    const value=editor();choose(value);value.slotEnd='08:00';value.applyCalendarSlot();assert.match(value.slotError,/Jam selesai/);assert.equal(value.dirty,false);
    value.slotShift='';value.applyCalendarSlot();assert.match(value.slotError,/Isi angka shift/);assert.equal(value.dirty,false);
});
test('edited hours cannot cross an occupied gap or exceed available calendar hours',()=>{
    const value=editor();choose(value,{...slot,availableSlots:[slot,{...slot,start:'11:00',end:'12:00'}]});value.slotEnd='12:00';value.applyCalendarSlot();assert.match(value.slotError,/tidak tersedia/);assert.equal(value.dirty,false);
    value.slotEnd='09:30';value.applyCalendarSlot();assert.equal(value.slotOpen,false);assert.equal(value.payload.plans[0].slots[0].ends_at,'09:30');
});
test('semester is resolved from the selected practicum without a period choice',()=>{
    const value=editor();value.periodId='other';choose(value);value.applyCalendarSlot();assert.equal(value.slotOpen,false);assert.equal(value.periodId,'period');assert.equal(value.payload.period_id,'period');assert.equal(value.payload.plans.length,1);
});
test('missing admin semester boundaries give a clear error without selecting a plan',()=>{
    const value=editor();value.periods=[];choose(value);value.applyCalendarSlot();assert.equal(value.slotOpen,true);assert.match(value.slotError,/Admin Praktikum/);assert.equal(value.payload.plans.length,0);
});
test('practicum and numeric existing shift are required and new slots omit notes',()=>{
    const value=editor();value.selectCalendarSlot(slot);value.applyCalendarSlot();assert.match(value.slotError,/Pilih nama praktikum/);assert.equal(value.dirty,false);
    value.slotPracticum='practicum';for(const invalid of ['0','1.5','A','99']){value.slotShift=invalid;value.applyCalendarSlot();assert.match(value.slotError,/Isi angka shift/);assert.equal(value.dirty,false);}
    value.slotShift='1';value.applyCalendarSlot();assert.equal(value.payload.plans[0].slots[0].note,undefined);
});
test('confirmation saves the draft once and permits another shift selection',()=>{
    const value=editor();let saves=0;value.$refs={draftForm:{requestSubmit(){saves++}}};
    choose(value);value.applyCalendarSlot();assert.equal(saves,1);assert.equal(value.slotOpen,false);
    choose(value,{...slot,start:'11:00',end:'12:00'});value.slotShift='2';value.applyCalendarSlot();assert.equal(saves,2);assert.equal(value.payload.plans[0].slots[0].starts_at,'09:00');assert.equal(value.payload.plans[0].slots[1].starts_at,'11:00');
});
test('a second active practicum remains selectable and is appended to the same package',()=>{
    const value=editor();value.plans.push({...value.plans[0],id:'second',praktikum_id:'second',name:'Praktikum Kedua',semester:'Genap',selected:false,slots:[{shift:'1',weekday:'',room_id:'',starts_at:'',ends_at:''}]});
    value.periods.push({...value.periods[0],id:'second-period',semester:'Genap'});
    choose(value);value.applyCalendarSlot();assert.equal(value.visiblePlans.length,2);
    value.selectCalendarSlot({...slot,start:'11:00',end:'12:00'});value.slotPracticum='second';value.slotShift='1';value.applyCalendarSlot();
    assert.equal(value.payload.plans.length,2);assert.equal(value.payload.plans[0].praktikum_id,'practicum');assert.equal(value.payload.plans[0].slots[0].starts_at,'09:00');assert.equal(value.payload.plans[1].praktikum_id,'second');
});

const pickerSource=readFileSync(new URL('../../resources/views/components/manajemen-praktikum/ui/schedule-calendar-script.blade.php',import.meta.url),'utf8').match(/<script>([\s\S]*?)<\/script>/)[1];
function picker(slots,redirectUrl=null){
    const navigations=[];const context=vm.createContext({window:{location:{href:'http://localhost/',assign:url=>navigations.push(url)}},URL});vm.runInContext(pickerSource,context);
    const value=context.window.practicumCalendarPicker({slots,redirectUrl,initial:null});value.opened=[];value.selectCalendarSlot=range=>value.opened.push({...range});value.navigations=navigations;return value;
}
const pointer={button:0,pointerType:'mouse',preventDefault(){}};
test('calendar initially scrolls to today while preserving an explicitly restored position',()=>{
    const value=picker([]),grid={dataset:{},scrollLeft:0,querySelector:()=>({offsetLeft:740})};value.$el={querySelector:()=>grid};value.$nextTick=callback=>callback();
    value.init();assert.equal(grid.scrollLeft,668);grid.dataset.positionRestored='true';grid.scrollLeft=430;value.init();assert.equal(grid.scrollLeft,430);
});
const later={...slot,start:'10:00',end:'11:00'};
test('dragging adjacent free hours selects the complete range and opens one form',()=>{
    const value=picker([slot,later]);value.startCalendarRange(slot,pointer);value.extendCalendarRange(later);assert.equal(value.calendarRangeContains(slot),true);assert.equal(value.calendarRangeContains(later),true);value.finishCalendarRange();
    assert.equal(value.opened.length,1);assert.equal(value.opened[0].start,'09:00');assert.equal(value.opened[0].end,'11:00');value.finishCalendarRange();assert.equal(value.opened.length,1);
});
test('reverse dragging chooses the same contiguous interval',()=>{
    const value=picker([slot,later]);value.startCalendarRange(later,pointer);value.extendCalendarRange(slot);value.finishCalendarRange();assert.equal(value.opened[0].start,'09:00');assert.equal(value.opened[0].end,'11:00');
});
test('range corners belong only to the first and last selected cells',()=>{
    const value=picker([slot,later]);value.startCalendarRange(slot,pointer);value.extendCalendarRange(later);
    assert.equal(value.calendarRangeClasses(slot)['mp-calendar-selected-start'],true);assert.equal(value.calendarRangeClasses(slot)['mp-calendar-selected-end'],false);
    assert.equal(value.calendarRangeClasses(later)['mp-calendar-selected-start'],false);assert.equal(value.calendarRangeClasses(later)['mp-calendar-selected-end'],true);
    value.hoveredColumn=slot.date+'|'+slot.roomId;assert.equal(value.calendarRangeClasses(slot)['mp-calendar-hover'],true);
});
test('a blocked hour between endpoints prevents selection even if the pointer skips it',()=>{
    const afterBlocked={...slot,start:'11:00',end:'12:00'};const value=picker([slot,afterBlocked]);value.startCalendarRange(slot,pointer);value.extendCalendarRange(afterBlocked);value.finishCalendarRange();assert.equal(value.opened.length,0);assert.match(value.selectionError,/melewati jadwal/);assert.equal(value.range,null);
});
test('selection cannot cross rooms or dates and cancellation clears the highlight',()=>{
    const value=picker([slot,later]);value.startCalendarRange(slot,pointer);value.extendCalendarRange({...later,roomId:'3'});value.finishCalendarRange();assert.equal(value.opened.length,0);
    value.startCalendarRange(slot,pointer);value.cancelCalendarRange();assert.equal(value.calendarRangeContains(slot),false);assert.equal(value.selecting,false);
});
test('main calendar transfers the chosen range to the draft form without writing a booking',()=>{
    const value=picker([slot,later],'http://localhost/eoffice/manprak/cerc/peminjaman/buat');value.startCalendarRange(slot,pointer);value.extendCalendarRange(later);value.finishCalendarRange();const url=new URL(value.navigations[0]);assert.equal(url.searchParams.get('slot_start'),'09:00');assert.equal(url.searchParams.get('slot_end'),'11:00');assert.equal(url.searchParams.get('slot_room'),'2');
});
