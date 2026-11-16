import test from 'node:test';
import assert from 'node:assert/strict';
import {readFileSync} from 'node:fs';
import vm from 'node:vm';

const page=readFileSync(new URL('../../resources/views/manajemen-praktikum/asprak/pengumuman.blade.php',import.meta.url),'utf8');
const script=[...page.matchAll(/<script>([\s\S]*?)<\/script>/g)].map(match=>match[1]).find(value=>value.includes('function pengumumanManager'));
function manager(){
    const inputs={};const warnings=[];
    class Transfer{constructor(){this.files=[];this.items={add:file=>this.files.push(file)};}}
    const context={DataTransfer:Transfer,alert:message=>warnings.push(message),document:{getElementById:id=>inputs[id]??=( {files:[]} )}};
    vm.runInNewContext(script,context);
    return {state:context.pengumumanManager(),warnings,inputs};
}

test('old attachments can be removed without changing the original snapshot and reopen resets edits',()=>{
    const {state,inputs}=manager();const original=[{path:'old/a.pdf',name:'A.pdf'},{path:'old/b.pdf',name:'B.pdf'}];
    state.openEditModal(1,'Judul','Isi',original);state.retainedFiles.splice(0,1);
    assert.equal(state.retainedFiles[0].path,'old/b.pdf');assert.equal(original.length,2);
    state.openEditModal(1,'Judul','Isi',original);
    assert.equal(state.retainedFiles.length,2);assert.equal(state.editFiles.length,0);
    assert.equal(inputs['hidden-lampiran-edit'].files.length,0);
});

test('combined old and new attachment count cannot exceed three',()=>{
    const {state,warnings}=manager();state.openEditModal(1,'Judul','Isi',[{path:'a'},{path:'b'}]);
    state.addFiles({target:{files:[{name:'c.pdf',size:100},{name:'d.pdf',size:100}],value:'selected'}},'edit');
    assert.equal(state.editFiles.length,1);assert.equal(state.retainedFiles.length+state.editFiles.length,3);
    assert.equal(warnings.length,1);
    state.addFiles({target:{files:[{name:'e.pdf',size:100}],value:'selected'}},'edit');
    assert.equal(state.editFiles.length,1);
});

test('highlight dismisses on the next pointer interaction without changing the destination hash',()=>{
    const source=readFileSync(new URL('../../resources/views/manajemen-praktikum/mahasiswa/pengumuman.blade.php',import.meta.url),'utf8');
    const script=[...source.matchAll(/<script>([\s\S]*?)<\/script>/g)].map(match=>match[1]).find(value=>value.includes('pointerdown'));
    const events={};let dismissed=false;
    vm.runInNewContext(script,{document:{addEventListener:(name,handler)=>events[name]=handler,querySelector:()=>({classList:{add:name=>{dismissed=name==='mp-highlight-dismissed';}}})}});
    events.pointerdown();assert.equal(dismissed,true);
    assert.ok(source.includes(':target:not(.mp-highlight-dismissed)'));
});
