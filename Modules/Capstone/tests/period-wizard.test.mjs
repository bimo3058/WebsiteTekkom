import {test} from 'node:test';
import assert from 'node:assert/strict';
globalThis.document={getElementById:()=>({textContent:JSON.stringify({role:'admin',base:'/capstone',api:'/capstone/session/capstone'})}),querySelector:()=>({content:'csrf'})};
globalThis.window={dispatchEvent(){},location:{assign(){}}};
const {periodWizard}=await import('../resources/assets/js/pages/period-wizard.js');
function ready(){const page=periodWizard();page.loading=false;page.templates=[{id:1,name:'A',is_active:true,weight:100}];page.form={...page.form,name:'New Period',start_date:'2026-01-01',end_date:'2026-12-31'};return page;}
test('wizard cannot skip required fields or missing period setup',()=>{
    const page=periodWizard();page.go(1);assert.equal(page.step,0);assert.ok(page.errors.name);
    const valid=ready();valid.go(4);assert.equal(valid.step,0);valid.go(1);assert.equal(valid.step,1);
    valid.templates=[];valid.go(2);assert.equal(valid.step,1);assert.ok(valid.errors.assessments);
});
test('wizard validates selected weights and group limits',()=>{
    const page=ready();page.templates[0].weight=50;page.assessments.EXPO=[1];assert.equal(page.validate(1),false);
    page.templates[0].weight=100;assert.equal(page.validate(1),true);
    page.form.min_group_size=4;page.form.max_group_size=3;assert.equal(page.validate(3),false);
});
test('failed save preserves wizard input and does not navigate',async()=>{
    let navigation=0;window.location.assign=()=>navigation++;
    globalThis.fetch=async()=>({ok:false,status:422,json:async()=>({message:'Invalid config',errors:{assessments:['Invalid config']}})});
    const page=ready();page.step=4;page.assessments.EXPO=[1];await page.save();
    assert.equal(navigation,0);assert.equal(page.form.name,'New Period');assert.deepEqual(page.assessments.EXPO,[1]);assert.equal(page.saving,false);assert.ok(page.errors.assessments);
});
test('save uses the session endpoint with all selected configuration and typed limits',async()=>{
    let sent,navigation;window.location.assign=path=>navigation=path;
    globalThis.fetch=async(path,options)=>{sent={path,options};return {ok:true,json:async()=>({period:{id:4}})};};
    const page=ready();page.step=4;page.form.min_group_size='3';page.assessments.EXPO=[1];await page.save();
    assert.equal(sent.path,'/capstone/session/capstone/admin/period-wizard');assert.equal(sent.options.method,'POST');assert.equal(sent.options.headers['X-CSRF-TOKEN'],'csrf');
    const body=JSON.parse(sent.options.body);assert.equal(body.min_group_size,3);assert.deepEqual(body.assessments.EXPO,[1]);assert.equal(body.expo_date,null);assert.equal(navigation,'/capstone/admin/periods');
});
test('refreshing templates after returning from the bank preserves the period draft',async()=>{
    const page=ready();page.assessments.EXPO=[1];page.step=1;
    globalThis.fetch=async()=>({ok:true,json:async()=>({templates:[{id:1,weight:100,is_active:true},{id:2,weight:50,is_active:true}],peer_templates:[],periods:[]})});
    await page.refreshTemplates();assert.equal(page.templates.length,2);assert.equal(page.form.name,'New Period');assert.deepEqual(page.assessments.EXPO,[1]);assert.equal(page.step,1);
});
