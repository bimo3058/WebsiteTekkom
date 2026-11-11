import test from 'node:test';
import assert from 'node:assert/strict';
import {readFileSync} from 'node:fs';
import vm from 'node:vm';

const source = readFileSync(new URL('../../../../public/js/practicum-feedback.js', import.meta.url), 'utf8');
test('score input immediately clears letters, negative and excessive values',()=>{
    const listeners={};
    vm.runInNewContext(source,{document:{addEventListener:(name,fn)=>listeners[name]=fn,querySelectorAll:()=>[]}});
    for(const value of ['gyhjio','101','-1','1e2','NaN']){
        const input={value,disabled:false,dataset:{gradeInteger:'1'},matches:()=>true};
        listeners.input({target:input});assert.equal(input.value,'');
    }
    for(const value of ['0','50','100']){
        const input={value,disabled:false,dataset:{gradeInteger:'1'},matches:()=>true};
        listeners.input({target:input});assert.equal(input.value,value);
    }
});
test('confirmation does not submit before approval and preserves submitter', async () => {
    const listeners = {};
    let dialog;
    vm.runInNewContext(source, {
        document:{addEventListener:(name, fn)=>{listeners[name]=fn;},querySelectorAll:()=>[]},
        window:{dispatchEvent:event=>{dialog=event.detail;}},
        CustomEvent:class {constructor(name, options){this.detail=options.detail;}},
    });
    let submitted=0;
    const attrs={onsubmit:"return confirm('Hapus pengumuman ini?')"};
    const form={dataset:{},isConnected:true,getAttribute:key=>attrs[key],removeAttribute:key=>delete attrs[key],setAttribute:(key,v)=>attrs[key]=v,requestSubmit:()=>{submitted++;assert.equal(attrs.onsubmit,undefined);}};
    const event={target:form,preventDefault(){},stopImmediatePropagation(){}};
    const cancelled=listeners.submit(event);
    assert.equal(submitted,0);
    dialog.resolve(false);
    await cancelled;
    assert.equal(submitted,0);
    const approved=listeners.submit(event);
    assert.equal(dialog.message,'Hapus pengumuman ini?');
    dialog.resolve(true);
    await approved;
    assert.equal(submitted,1);
    assert.ok(attrs.onsubmit);
});
