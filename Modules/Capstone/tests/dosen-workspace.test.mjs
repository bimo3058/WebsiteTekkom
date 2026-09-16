import {test} from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';

globalThis.document={getElementById:id=>id==='capstone-context'?{textContent:JSON.stringify({role:'dosen',base:'/capstone',api:'/capstone/session/capstone',params:{id:81,groupId:4}})}:{showModal(){},close(){}},querySelector:()=>({content:'csrf'})};
globalThis.window={dispatchEvent(){},location:{search:''}};
const {lecturerTitles,lecturerTitleDetail,lecturerApprovals,lecturerBids}=await import('../resources/assets/js/pages/dosen/titles.js');
const {lecturerGroups,lecturerDocuments,lecturerRequests}=await import('../resources/assets/js/pages/dosen/mentoring.js');
const {lecturerEvaluations,lecturerSupervisorEvaluations,lecturerEvaluationForm}=await import('../resources/assets/js/pages/dosen/evaluations.js');
const reply=data=>({ok:true,json:async()=>data});
const failed=()=>({ok:false,status:422,json:async()=>({message:'Validation failed',errors:{title:['Title required']}})});

test('every registered lecturer route has a Blade entry point',()=>{
    const pages=JSON.parse(fs.readFileSync(new URL('../resources/reference/pages.json',import.meta.url)));
    for(const page of pages.filter(p=>p.route.startsWith('/dosen/'))){const route=page.route.replace(/\[([^\]]+)\]/g,'_$1_');assert.ok(fs.existsSync(new URL('../resources/views/pages'+route+'.blade.php',import.meta.url)),route);}
});
test('page factories initialize lazy pagination without reading missing data',()=>{
    for(const factory of [lecturerTitles,lecturerApprovals,lecturerBids,lecturerGroups,lecturerDocuments,lecturerRequests,lecturerEvaluations,lecturerSupervisorEvaluations]){const page=factory();assert.equal(page.pageCount,1);assert.deepEqual(page.visible,[]);}
    assert.equal(lecturerTitleDetail().title,null);assert.equal(lecturerEvaluationForm().complete,false);
});
test('title filters and sorting preserve originals, failed edits preserve user input',async()=>{
    const page=lecturerTitles();page.items=[{id:1,title:'B',quota:10,period_id:2,specializations:['AI']},{id:2,title:'A',quota:2,period_id:1,specializations:['Network']}];page.selectedPeriod='2';page.filterSpecs=['AI'];assert.deepEqual(page.filtered.map(t=>t.id),[1]);page.selectedPeriod='all';page.filterSpecs=[];page.sort('quota');assert.deepEqual(page.filtered.map(t=>t.id),[2,1]);assert.equal(page.items[0].id,1);
    page.edit(page.items[0]);globalThis.fetch=async()=>failed();await page.save();assert.equal(page.form.title,'B');assert.equal(page.errors.root,'Validation failed');assert.equal(page.saving,false);
});
test('locked periods and explicit allowed actions prevent bid recommendations',async()=>{
    const page=lecturerBids();const bid={id:1,group:{period_id:2}};page.periods=[{id:2,bidding_locked_at:'2026-09-01'}];let writes=0;globalThis.fetch=async()=>{writes++;return reply({});};await page.recommend(bid,'ACCEPT');assert.equal(writes,0);page.periods=[];bid.allowed_actions={can_accept:false};assert.equal(page.canRecommend(bid,'ACCEPT'),false);
});
test('proposal rejection requires a reason and uses the active API contract',async()=>{
    const page=lecturerApprovals();page.review({id:6},'reject');let sent;globalThis.fetch=async(path,options)=>{sent={path,body:JSON.parse(options.body)};return reply({});};page.load=async()=>{};await page.submit();assert.equal(sent,undefined);page.reason='Needs a clearer scope';await page.submit();assert.ok(sent.path.endsWith('/title-approvals/6/reject'));assert.equal(sent.body.rejection_reason,page.reason);
});
test('document filters combine period, group, phase and canonical individual TA phase',()=>{
    const page=lecturerDocuments(true);page.groups=[{id:1,period_id:3},{id:2,period_id:4}];page.items=[{id:11,group_id:1,phase:'TA_INDIVIDUAL',status:'SUBMITTED'},{id:12,group_id:1,phase:'PDC1',status:'SUBMITTED'},{id:13,group_id:2,phase:'TA_INDIVIDUAL',status:'SUBMITTED'}];page.selectedPeriod='3';page.selectedGroup='1';assert.deepEqual(page.filtered.map(d=>d.id),[11]);page.status='APPROVED';assert.equal(page.filtered.length,0);
});
test('examiner links use evaluation IDs rather than schedule IDs',async()=>{
    globalThis.fetch=async path=>reply(path.endsWith('periods-list')?[]:{data:{seminars:[{id:20,type:'SEMPRO',group:{period_id:1},evaluations:[{id:81,status:'PENDING'}]}],ta_defenses:[]}});
    const page=lecturerEvaluations();await page.load();assert.equal(page.items[0].evaluation.id,81);assert.equal(page.evaluationUrl(page.items[0]),'/capstone/dosen/evaluation/81?type=SEMINAR');
});
const evaluationData=()=>({group:{id:4,members:[{student:{id:7,name:'Student'}}]},schedule:{id:20,type:'SEMPRO'},evaluation:{id:81,status:'PENDING'},components:[{id:2,weight:25},{id:3,weight:75}],existing_scores:{}});
test('examiner scores require complete input and submit component IDs, weighted total and result',async()=>{
    globalThis.window.location.search='?type=SEMINAR';const data=evaluationData();const calls=[];globalThis.fetch=async(path,options)=>{if(options.method==='GET')return reply(data);calls.push({path,body:JSON.parse(options.body)});return reply({});};const page=lecturerEvaluationForm();await page.load();assert.equal(page.complete,false);page.scores={'2_7':'80','3_7':'100'};page.result='PASS';assert.equal(page.total(7),'95.00');assert.equal(page.complete,true);await page.submit();assert.equal(calls.length,2);assert.equal(calls[0].body.scores[0].component_id,2);assert.equal(calls[0].body.evaluation_type,'SEMPRO');assert.ok(calls[1].path.endsWith('/sempro/20/evaluate'));assert.equal(calls[1].body.score,95);assert.equal(calls[1].body.result,'PASS');assert.equal(page.viewOnly,true);
});
test('failed finalization preserves scores for retry and does not mark the form submitted',async()=>{
    globalThis.window.location.search='?type=SEMINAR';globalThis.fetch=async(path,options)=>options.method==='GET'?reply(evaluationData()):path.includes('/sempro/')?failed():reply({});const page=lecturerEvaluationForm();await page.load();page.scores={'2_7':'80','3_7':'90'};page.result='PASS';await page.submit();assert.equal(page.viewOnly,false);assert.equal(page.scores['2_7'],'80');assert.equal(page.errors.root,'Validation failed');
});
test('supervisor evaluation preserves period component IDs and blocks zero scores',async()=>{
    window.location.search='?type=BIMBINGAN_TA&student_id=7';let sent;globalThis.fetch=async(path,options)=>{if(options.method==='POST'){sent=JSON.parse(options.body);return reply({});}return reply({data:{group:{id:4},components:[{id:22,weight:100}],students:[{id:7,scores:[{period_component_id:22,score:85,notes:'Good'}]}]}});};const page=lecturerEvaluationForm(true);await page.load();assert.equal(page.complete,true);page.scores['22_7']='0';assert.equal(page.complete,false);page.scores['22_7']='95';await page.submit();assert.equal(sent.evaluation_type,'BIMBINGAN_TA');assert.deepEqual(sent.scores,[{period_component_id:22,student_id:7,score:95,notes:'Good'}]);
});
test('submitted examiner forms cannot send mutations',async()=>{
    window.location.search='?type=SEMINAR';globalThis.fetch=async()=>reply({...evaluationData(),evaluation:{status:'SUBMITTED'}});const page=lecturerEvaluationForm();await page.load();let writes=0;globalThis.fetch=async()=>{writes++;return reply({});};await page.submit();assert.equal(writes,0);
});
