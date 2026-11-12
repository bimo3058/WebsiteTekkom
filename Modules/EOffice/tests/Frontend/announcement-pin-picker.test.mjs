import test from 'node:test';
import assert from 'node:assert/strict';
import {readFileSync} from 'node:fs';
import vm from 'node:vm';

const source=readFileSync(new URL('../../resources/views/components/manajemen-praktikum/ui/datetime-picker-assets.blade.php',import.meta.url),'utf8');
const script=[...source.matchAll(/<script>([\s\S]*?)<\/script>/g)].map(match=>match[1]).join('\n');

function setup(){
    const listeners={};const calls=[];
    const document={body:{},addEventListener:(event,handler)=>listeners[event]=handler,querySelectorAll:()=>[]};
    const window={flatpickr:(input,options)=>{
        const picker={options,updates:[],set(key,value){this.updates.push([key,value]);}};
        input._flatpickr=picker;calls.push(picker);return picker;
    }};
    vm.runInNewContext(script,{window,document,Date});
    return {window,document,listeners,calls};
}

test('pin expiry uses the registration picker with ISO submission and readable display',()=>{
    const {window,document}=setup();const picker=window.mpCreatePinExpiryPicker({});
    assert.equal(picker.options.dateFormat,'Y-m-d\\TH:i');
    assert.equal(picker.options.altFormat,'d/m/Y H:i');
    assert.equal(picker.options.time_24hr,true);
    assert.equal(picker.options.enableTime,true);
    assert.equal(picker.options.minuteIncrement,1);
    assert.equal(picker.options.disableMobile,true);
    assert.equal(picker.options.appendTo,document.body);
    assert.equal(typeof picker.options.onReady,'function');
});

test('reopening the modal reuses the picker and updates its minimum time',()=>{
    const {window,calls}=setup();const input={};
    const first=window.mpCreatePinExpiryPicker(input);const second=window.mpCreatePinExpiryPicker(input);
    assert.equal(first,second);assert.equal(calls.length,1);assert.equal(first.updates.length,2);
    assert.equal(first.updates[1][0],'minDate');assert.ok(first.updates[1][1] instanceof Date);
});

test('registration fields retain their existing format and lazy modal fields are excluded',()=>{
    const {window,document,listeners}=setup();const input={};let selector='';
    document.querySelectorAll=value=>{selector=value;return [input];};
    listeners.DOMContentLoaded();
    assert.equal(selector,'.flatpickr-input:not([data-mp-picker-lazy])');
    assert.equal(input._flatpickr.options.dateFormat,'Y-m-d H:i');
    assert.equal(input._flatpickr.options.monthSelectorType,'static');
    const page=readFileSync(new URL('../../resources/views/manajemen-praktikum/koordinator/pendaftaran-asprak.blade.php',import.meta.url),'utf8');
    assert.ok(page.includes('<x-eoffice::manajemen-praktikum.ui.datetime-picker-assets />'));
});
