import {test} from 'node:test';
import assert from 'node:assert/strict';
globalThis.document={getElementById:()=>({textContent:JSON.stringify({role:'admin',base:'/capstone',api:'/capstone/session/capstone'}),close(){}}),querySelector:()=>({content:'csrf'})};
globalThis.window={location:{search:'?period_id=2'},dispatchEvent(){}};
const {documentRequirementsPage,documentTypesPage}=await import('../resources/assets/js/pages/document-configuration.js');
const response=data=>({ok:true,json:async()=>({data})});

test('phase navigation honors the selected period and discards edits when switching periods',async()=>{
    const calls=[];
    globalThis.fetch=async path=>{calls.push(path);return response(path.endsWith('/periods-list') ? [{id:1,is_active:true},{id:2}] : [{id:1,phase:'PDC1',name:'Proposal',is_required:true},{id:2,phase:'EXPO',name:'Poster'}]);};
    const page=documentRequirementsPage('pdc1');await page.init();
    assert.equal(page.selectedPeriod,'2');assert.equal(page.items.length,1);assert.match(page.phaseUrl('TA'),/\/ta\?period_id=2$/);
    page.newName='Local edit';page.add();assert.equal(page.items.length,2);
    page.selectedPeriod='1';await page.load();assert.equal(page.items.length,1);assert.equal(page.newName,'');
    assert.equal(calls.length,3);
});

test('phase save sends only the selected phase, including an intentionally empty list',async()=>{
    const calls=[];globalThis.fetch=async(path,options)=>{calls.push({path,options});return response([]);};
    const page=documentRequirementsPage('TA');page.selectedPeriod='2';page.loading=false;page.load=async()=>{};
    await page.save();
    assert.deepEqual(JSON.parse(calls[0].options.body),{period_id:2,phase:'TA',requirements:[]});
    assert.equal(calls[0].options.headers['X-CSRF-TOKEN'],'csrf');
});

test('finalized period prevents draft changes and all save actions',async()=>{
    let requests=0;globalThis.fetch=async()=>{requests++;return response([]);};
    const page=documentRequirementsPage('TA');page.selectedPeriod='2';page.periods=[{id:2,is_finalized:true}];page.loading=false;page.newName='New';page.items=[{_key:1,name:'Existing',phase:'TA'}];
    page.add();page.remove(page.items[0]);await page.save();await page.save(true);
    assert.equal(page.items.length,1);assert.equal(requests,0);assert.equal(page.editable,false);
});

test('late period response cannot replace the latest selected period',async()=>{
    let resolveOld;globalThis.fetch=path=>path.endsWith('/1') ? new Promise(resolve=>{resolveOld=resolve;}) : Promise.resolve(response([{phase:'TA',name:'Current'}]));
    const page=documentRequirementsPage('TA');page.selectedPeriod='1';const old=page.load();page.selectedPeriod='2';await page.load();resolveOld(response([{phase:'TA',name:'Old'}]));await old;
    assert.equal(page.items[0].name,'Current');assert.equal(page.loading,false);
});

test('all phases document type serializes a null phase supported by Laravel',async()=>{
    let payload;globalThis.fetch=async(path,options)=>{payload=JSON.parse(options.body);return response({});};
    const page=documentTypesPage();page.form={name:'HKI',description:'Copyright',phase:'ALL'};page.load=async()=>{};await page.save();
    assert.equal(payload.phase,null);assert.equal(page.saving,false);
});
