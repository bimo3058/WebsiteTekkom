import {test} from 'node:test';
import assert from 'node:assert/strict';
import {calendarDays,localDateKey,normalizeSchedule} from '../resources/assets/js/calendar.js';

// Page code is tested with a minimal DOM boundary and mocked HTTP only.
globalThis.document={getElementById:()=>({textContent:JSON.stringify({role:'admin',base:'/capstone',api:'/capstone/session/capstone'}),showModal(){},close(){}}),querySelector:()=>({content:'test-csrf'})};
globalThis.window={dispatchEvent(){}};
const {schedulePage}=await import('../resources/assets/js/pages/schedules.js');
const {adminTaDefensePage}=await import('../resources/assets/js/pages/admin-ta-defense.js');

test('calendar follows the Sunday-first Next.js layout, including leap years and year boundaries',()=>{
    const leap=calendarDays(2024,1);
    assert.equal(leap[0].key,'2024-01-28');assert.equal(leap.at(-1).key,'2024-03-02');
    assert.equal(leap.filter(d=>d.current).length,29);
    const january=calendarDays(2026,0);
    assert.equal(january[0].key,'2025-12-28');assert.equal(january.at(-1).key,'2026-01-31');
    assert.equal(calendarDays(2026,1).length,28);
});
test('database calendar dates retain their date without UTC day drift',()=>{
    for(const date of ['2026-09-09','2026-09-09T00:00:00.000Z','2026-09-09T00:00:00+07:00'])assert.equal(localDateKey(date),'2026-09-09');
    assert.equal(localDateKey('invalid'),'');
});
test('distinct schedule types never collide and prefixed ids resolve to the original record',()=>{
    const a=normalizeSchedule({id:1,date:'2026-09-09'},'SEMPRO');
    const b=normalizeSchedule({id:1,date:'2026-09-09'},'EXPO');
    assert.notEqual(a._key,b._key);
    assert.equal(normalizeSchedule({id:'ta_12'},'TA_DEFENSE')._id,'12');
    assert.equal(normalizeSchedule({id:'bim_3'},'BIMBINGAN')._id,'3');
});
test('calendar filters by period id even when period names are identical, sorts by time and paginates',()=>{
    const page=schedulePage();
    page.schedules=[{id:1,date:'2026-09-09',start_time:'11:00',group:{period_id:1},period_name:'Same'},
        {id:2,date:'2026-09-09',start_time:'08:00',group:{period_id:1},period_name:'Same'},
        {id:3,date:'2026-09-09',start_time:'09:00',group:{period_id:2},period_name:'Same'}].map(e=>normalizeSchedule(e,'BIMBINGAN'));
    page.selectedPeriod='1';page.selectedDate='2026-09-09';
    assert.deepEqual(page.dayEvents.map(e=>e.id),[2,1]);
    page.perPage=1;page.page=10;assert.equal(page.currentPage,2);assert.equal(page.visibleRows[0].id,1);
    page.search='missing';assert.equal(page.visibleRows.length,0);assert.equal(page.totalPages,1);
});
test('calendar navigates across years and approval actions match type and status',()=>{
    const page=schedulePage();page.month=11;page.year=2026;page.move(1);assert.equal(page.month,0);assert.equal(page.year,2027);
    page.move(-1);assert.equal(page.month,11);assert.equal(page.year,2026);
    assert.equal(page.canApprove({type:'TA_DEFENSE',status:'PENDING'}),true);
    assert.equal(page.canApprove({type:'BIMBINGAN',status:'PENDING'}),false);
    assert.equal(page.canApprove({type:'SEMPRO',status:'COMPLETED'}),false);
    assert.equal(page.safeLink('javascript:alert(1)'),'#');
});
test('calendar loads a single role-scoped feed and surfaces failures without silently returning an empty calendar',async()=>{
    const calls=[];globalThis.fetch=async(path)=>{calls.push(path);return {ok:true,json:async()=>({data:[]})};};
    const page=schedulePage();await page.load();
    assert.deepEqual(calls,['/capstone/session/capstone/admin/all-schedules','/capstone/session/capstone/admin/periods']);
    assert.equal(page.error,'');assert.equal(page.loading,false);
    globalThis.fetch=async()=>({ok:false,status:403,json:async()=>({message:'Locked'})});
    await page.load();assert.equal(page.error,'Locked');assert.equal(page.loading,false);
});
test('TA approvals and rejections use the dedicated defense endpoint and preserve schedule fields',async()=>{
    const calls=[];globalThis.fetch=async(path,options)=>{calls.push({path,options});return {ok:true,json:async()=>({data:[]})};};
    const page=schedulePage();page.load=async()=>{};
    const event=normalizeSchedule({id:'ta_9',date:'2026-09-09',status:'PENDING',start_time:'09:00',end_time:'10:00',room:'Lab',examiner1:{id:2},examiner2:{id:3}},'TA_DEFENSE');
    await page.approve(event);
    assert.equal(calls[0].path,'/capstone/session/capstone/admin/ta-defense-schedules/9');
    assert.deepEqual(JSON.parse(calls[0].options.body),{date:'2026-09-09',start_time:'09:00',end_time:'10:00',room:'Lab',examiner_1_id:2,examiner_2_id:3,status:'SCHEDULED'});
    page.rejecting=event;page.reason='Conflict';await page.submitRejection();
    assert.equal(calls[1].path,'/capstone/session/capstone/admin/ta-defense-schedules/9/cancel');
    assert.equal(calls[1].options.headers['X-CSRF-TOKEN'],'test-csrf');
    assert.equal(calls[1].options.credentials,'same-origin');
});

test('admin TA editor rejects duplicate and supervisor examiners before sending a request',async()=>{
    let requests=0;globalThis.fetch=async()=>{requests++;return {ok:true,json:async()=>({data:[]})};};
    const page=adminTaDefensePage();page.form={group_id:'1',student_ids:['8'],examiner_1_id:'2',examiner_2_id:'2'};
    await page.save();assert.match(page.formError,/cannot be the same/);assert.equal(requests,0);
    page.form.examiner_2_id='3';page.eligible=[{id:1,supervisors:[{id:2}]}];
    await page.save();assert.match(page.formError,/cannot be supervisors/);assert.equal(requests,0);
});

test('admin TA edits preserve the student selection and submit examiner changes to Laravel',async()=>{
    const calls=[];globalThis.fetch=async(path,options)=>{calls.push({path,options});return {ok:true,json:async()=>({data:[]})};};
    const page=adminTaDefensePage();page.load=async()=>{};page.editing={id:9};
    page.form={group_id:'1',period_id:'4',student_ids:['8','10'],examiner_1_id:'2',examiner_2_id:'3',date:'2027-01-15',start_time:'09:00',end_time:'10:00',location_id:'7',notes:'Updated'};
    await page.save();
    assert.equal(calls.length,1);assert.equal(calls[0].path,'/capstone/session/capstone/admin/ta-defense-schedules/9');
    const body=JSON.parse(calls[0].options.body);
    assert.deepEqual(body.student_ids,[8,10]);assert.equal(body.examiner_1_id,2);assert.equal(body.examiner_2_id,3);assert.equal(body.location_id,7);
    assert.equal(body.group_id,undefined);assert.equal(body.period_id,undefined);assert.equal(page.saving,false);
});
