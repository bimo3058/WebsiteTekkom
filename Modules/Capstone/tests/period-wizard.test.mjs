import {test} from 'node:test';
import assert from 'node:assert/strict';
globalThis.document={getElementById:()=>({textContent:JSON.stringify({role:'admin',base:'/capstone',api:'/capstone/session/capstone'})}),querySelector:()=>({content:'csrf'})};
globalThis.window={dispatchEvent(){},location:{assign(){}}};
const {periodWizard}=await import('../resources/assets/js/pages/period-wizard.js');
function ready(){const page=periodWizard();page.loading=false;page.templates=[{id:1,code:'CPL-1',name:'A',description:'Desc A',is_active:true,weight:100}];page.peerTemplates=[{id:9,code:'PR-1',name:'P',description:'Desc P',is_active:true,weight:100}];page.form={...page.form,name:'New Period',start_date:'2026-01-01',end_date:'2026-12-31'};return page;}
test('wizard cannot skip required fields or missing period setup',()=>{
    const page=periodWizard();page.go(1);assert.equal(page.step,0);assert.ok(page.errors.name);
    const valid=ready();for(let i=1;i<=5;i++)valid.go(i);assert.equal(valid.step,5);
    valid.step=1;valid.syncTab();valid.templates=[];valid.go(2);assert.equal(valid.step,1);assert.ok(valid.errors.assessments);
});
test('wizard validates selected weights and group limits',()=>{
    const page=ready();page.templates[0].weight=50;page.assessments.EXPO=[1];assert.equal(page.validate(1),false);
    page.templates[0].weight=100;assert.equal(page.validate(1),true);
    page.form.min_group_size=4;page.form.max_group_size=3;assert.equal(page.validate(4),false);
});
test('failed save preserves wizard input and does not navigate',async()=>{
    let navigation=0;window.location.assign=()=>navigation++;
    globalThis.fetch=async()=>({ok:false,status:422,json:async()=>({message:'Invalid config',errors:{assessments:['Invalid config']}})});
    const page=ready();page.step=5;page.assessments.EXPO=[1];await page.save();
    assert.equal(navigation,0);assert.equal(page.form.name,'New Period');assert.deepEqual(page.assessments.EXPO,[1]);assert.equal(page.saving,false);assert.ok(page.errors.assessments);
});
test('save uses the session endpoint with all selected configuration and typed limits',async()=>{
    let sent,navigation;window.location.assign=path=>navigation=path;
    globalThis.fetch=async(path,options)=>{sent={path,options};return {ok:true,json:async()=>({period:{id:4}})};};
    const page=ready();page.step=5;page.form.min_group_size='3';page.assessments.EXPO=[1];await page.save();
    assert.equal(sent.path,'/capstone/session/capstone/admin/period-wizard');assert.equal(sent.options.method,'POST');assert.equal(sent.options.headers['X-CSRF-TOKEN'],'csrf');
    const body=JSON.parse(sent.options.body);assert.equal(body.min_group_size,3);assert.deepEqual(body.assessments.EXPO,[1]);assert.deepEqual(body.assessments.SEMPRO,[]);assert.equal(body.expo_date,null);assert.equal(navigation,'/capstone/admin/periods');
});
test('refreshing templates after returning from the bank preserves the period draft',async()=>{
    const page=ready();page.assessments.EXPO=[1];page.step=1;
    globalThis.fetch=async()=>({ok:true,json:async()=>({templates:[{id:1,weight:100,is_active:true},{id:2,weight:50,is_active:true}],peer_templates:[],periods:[]})});
    await page.refreshTemplates();assert.equal(page.templates.length,2);assert.equal(page.form.name,'New Period');assert.deepEqual(page.assessments.EXPO,[1]);assert.equal(page.step,1);
});
test('wizard covers all seven evaluation types starting with SEMPRO',()=>{
    const page=periodWizard();
    assert.deepEqual(page.types,['SEMPRO','SIDANG_TA','EXPO','BIMBINGAN_SEMPRO','BIMBINGAN_TA','NILAI_DOSEN','MILESTONE']);
    assert.equal(page.evaluationType,'SEMPRO');
    assert.deepEqual(page.assessments.SEMPRO,[]);
});
test('component picker supports search, filter, sort and pagination',()=>{
    const page=ready();
    page.templates=Array.from({length:25},(_,i)=>({id:i+1,code:'CPL-'+(i+1),name:'Komponen '+(i+1),description:'Deskripsi '+(i+1),is_active:true,weight:i===0?50:10}));
    assert.equal(page.totalResults,25);
    assert.equal(page.pagedChoices.length,10);
    assert.deepEqual(page.pageNumbers,[1,2,3]);
    page.goPage(3);assert.equal(page.pagedChoices.length,5);
    page.goPage(1);page.filter='high';assert.equal(page.totalResults,1);assert.equal(page.pagedChoices[0].weight,50);
    page.filter='all';page.sortBy='bobot_desc';assert.equal(page.pagedChoices[0].weight,50);
    page.search='cpl-2';assert.equal(page.totalResults,7);
});
test('simpan konfigurasi validates total weight before marking a draft',()=>{
    const page=ready();
    page.step=1;page.syncTab();
    assert.equal(page.simpanDisabled,true);
    page.templates[0].weight=50;page.assessments.SEMPRO=[1];assert.equal(page.simpanDisabled,true);page.simpanKonfigurasi();assert.equal(page.configuredAssessment.SEMPRO,undefined);assert.ok(page.errors['assessments.SEMPRO']);
    page.templates[0].weight=100;page.simpanKonfigurasi();assert.equal(page.configuredAssessment.SEMPRO,true);
    page.step=2;page.syncTab();page.peerIds=[9];page.simpanKonfigurasi();assert.equal(page.configuredPeer,true);
});
test('peer review weight must total 100 percent',()=>{
    const page=ready();page.peerTemplates[0].weight=50;page.peerIds=[9];assert.equal(page.validate(2),false);
    page.peerTemplates[0].weight=100;assert.equal(page.validate(2),true);
});
test('step navigation syncs assessment and peer contexts',()=>{
    const page=ready();page.go(1);assert.equal(page.tab,'assessment');
    page.go(2);assert.equal(page.step,2);assert.equal(page.tab,'peer');
    page.back();assert.equal(page.step,1);assert.equal(page.tab,'assessment');
});
test('bottom navigation steps through each evaluation type',()=>{
    const page=ready();page.go(1);page.syncTab();
    const order=['SEMPRO','SIDANG_TA','EXPO','BIMBINGAN_SEMPRO','BIMBINGAN_TA','NILAI_DOSEN','MILESTONE'];
    for(const expected of order.slice(1)){page.search='cpl';page.nextType();assert.equal(page.evaluationType,expected);assert.equal(page.search,'');}
    assert.equal(page.step,1);
    page.nextType();assert.equal(page.step,2);assert.equal(page.tab,'peer');
    page.back();assert.equal(page.step,1);
    for(const expected of [...order].reverse().slice(1)){page.prevType();assert.equal(page.evaluationType,expected);}
    page.prevType();assert.equal(page.step,0);
});
