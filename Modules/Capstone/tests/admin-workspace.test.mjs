import {test} from 'node:test';
import assert from 'node:assert/strict';
let closed=0;
globalThis.document={getElementById:id=>id==='capstone-context'?{textContent:JSON.stringify({role:'admin',base:'/capstone',api:'/capstone/session/capstone',params:{id:'SEMPRO'}})}:{showModal(){},close(){closed++;}},querySelector:()=>({content:'csrf'}),createElement:()=>({click(){}})};
globalThis.window={dispatchEvent(){},location:{search:''}};
const {assessmentConfig,gradeConfig}=await import('../resources/assets/js/pages/admin/configuration.js');
const {allRows}=await import('../resources/assets/js/pages/admin/shared.js');
const {expoAdmin,semproAdmin,finalizationAdmin}=await import('../resources/assets/js/pages/admin/management.js');
const {documentUploads}=await import('../resources/assets/js/pages/admin/monitoring.js');
const {reportsAdmin}=await import('../resources/assets/js/pages/admin/reports.js');
const reply=data=>({ok:true,json:async()=>data});

test('allRows handles nested pagination and preserves existing endpoint parameters',async()=>{
    const calls=[];globalThis.fetch=async path=>{calls.push(path);return reply({data:{data:[{id:calls.length}],pagination:{last_page:2}}});};
    assert.deepEqual(await allRows('/admin/users?role=dosen'),[{id:1},{id:2}]);
    assert.equal(calls[1],'/capstone/session/capstone/admin/users?role=dosen&per_page=100&page=2');
});
test('period changes ignore an older assessment response',async()=>{
    let finish;globalThis.fetch=path=>path.includes('/periods/1/')?new Promise(resolve=>finish=()=>resolve(reply({data:{all_templates:[{id:1}],selected_components:[]}}))):Promise.resolve(reply({data:{all_templates:[{id:2}],selected_components:[{template_id:2}]}}));
    const page=assessmentConfig(false,true);page.periodId='1';const pending=page.load();page.periodId='2';await page.load();finish();await pending;
    assert.deepEqual(page.templates,[{id:2}]);assert.deepEqual(page.selectedIds,[2]);assert.equal(page.loading,false);
});
test('assessment saves canonical ordered template ids and keeps drafts after a failed save',async()=>{
    let payload;globalThis.fetch=async(path,options)=>{payload=JSON.parse(options.body);return {ok:false,status:422,json:async()=>({message:'Invalid configuration',errors:{template_ids:['Invalid']}})};};
    const page=assessmentConfig(false,true);page.periodId='3';page.selectedIds=[7,2];await page.save();
    assert.deepEqual(payload,{type:'SEMPRO',template_ids:[7,2]});assert.deepEqual(page.selectedIds,[7,2]);assert.deepEqual(page.errors,{template_ids:['Invalid']});
    page.periods=[{id:3,is_finalized:true}];globalThis.fetch=()=>assert.fail('Finalized config must not be written');await page.save();
});
test('grade configuration validates totals and posts numeric weights per phase',async()=>{
    const page=gradeConfig();page.periodId='2';page.weights={pdc1:{SEMPRO:'50',BIMBINGAN_SEMPRO:'50'},pdc2:{EXPO:100},ta:{SIDANG_TA:100}};
    let payload;globalThis.fetch=async(path,options)=>{payload=JSON.parse(options.body);return reply({});};await page.save();
    assert.equal(payload.pdc1_weights.SEMPRO,50);page.weights.pdc1.SEMPRO=49;assert.equal(page.valid,false);globalThis.fetch=()=>assert.fail('Invalid total');await page.save();
});
test('expo validation failures preserve input and keep the modal open',async()=>{
    closed=0;globalThis.fetch=async()=>({ok:false,status:422,json:async()=>({message:'Invalid date',errors:{date:['Invalid date']}})});
    const page=expoAdmin();page.edit();page.form={period_id:'2',name:'Expo 2026',date:'2026-11-01',capacity:20};await page.save();
    assert.equal(closed,0);assert.equal(page.form.name,'Expo 2026');assert.equal(page.errors.date[0],'Invalid date');
});
test('sempro edit uses the update endpoint and academic examiner ids',async()=>{
    let call;globalThis.fetch=async(path,options)=>{call={path,options};return reply({});};
    const page=semproAdmin();page.load=async()=>{};page.edit({id:5,group_id:4,examiner_1_id:11,examiner_2_id:12},'edit');await page.save();
    assert.equal(call.path,'/capstone/session/capstone/admin/sempro/schedules/5');assert.equal(call.options.method,'PUT');assert.equal(JSON.parse(call.options.body).examiner_1_id,11);
});
test('allocation preserves group and title selection and does not stringify academic ids',async()=>{
    let payload;globalThis.fetch=async(path,options)=>{payload=JSON.parse(options.body);return reply({});};
    const page=finalizationAdmin();page.load=async()=>{};page.allocate({id:8,lecturer_id:11},{id:9,group_id:4});await page.save();
    assert.deepEqual(payload,{bid_id:9,group_id:4,title_id:8,supervisor_1_id:11,supervisor_2_id:null});
});
test('document downloads include their source to avoid collisions between tables',async()=>{
    let target;globalThis.fetch=async path=>{target=path;return {ok:true,blob:async()=>new Blob(['document'])};};
    await documentUploads().downloadDoc({id:7,source:'expo_documents',original_name:'poster.pdf'});
    assert.equal(target,'/capstone/session/capstone/admin/document-uploads/7/download?source=expo_documents');
});
test('report export requests CSV explicitly and preserves period filters',async()=>{
    let target;globalThis.fetch=async path=>{target=path;return {ok:true,blob:async()=>new Blob(['report'])};};
    const page=reportsAdmin('final-grades');page.periodId='2';await page.exportReport();assert.match(target,/period_id=2/);assert.match(target,/format=csv/);
});
