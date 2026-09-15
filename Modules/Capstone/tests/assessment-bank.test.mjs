import {test} from 'node:test';
import assert from 'node:assert/strict';
globalThis.document={getElementById:()=>({textContent:JSON.stringify({role:'admin',base:'/capstone',api:'/capstone/session/capstone'}),close(){}}),querySelector:()=>({content:'csrf'})};
globalThis.window={dispatchEvent(){},location:{assign(){}}};
const {assessmentBank,assessmentTemplateForm}=await import('../resources/assets/js/pages/assessment-bank.js');
test('bulk actions retain failed IDs and refresh the list after partial success',async()=>{
    const paths=[];globalThis.fetch=async(path,options)=>{paths.push(path);return {ok:!path.endsWith('/2'),status:403,json:async()=>({message:'Locked'})};};
    const page=assessmentBank();page.selected=[1,2];let reloads=0;page.load=async()=>reloads++;
    await page.mutate([1,2],'deactivate');assert.deepEqual(page.selected,[2]);assert.equal(reloads,1);assert.equal(page.saving,false);assert.equal(paths.length,2);
});
test('template form sends the original name/code convention and tracks unsaved changes',async()=>{
    let payload,navigation;globalThis.fetch=async(path,options)=>{payload=JSON.parse(options.body);return {ok:true,json:async()=>({data:{id:1}})};};window.location.assign=path=>navigation=path;
    const page=assessmentTemplateForm();page.loading=false;page.original=JSON.stringify(page.form);page.form={code:'CPL-01',description:'Description',weight:'100',is_active:false};
    assert.equal(page.dirty,true);await page.save();assert.equal(payload.name,'CPL-01');assert.equal(payload.weight,100);assert.equal(payload.is_active,false);assert.equal(page.dirty,false);assert.equal(navigation,'/capstone/admin/assessment-bank');
});
