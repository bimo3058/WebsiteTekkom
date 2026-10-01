import {test} from 'node:test';
import assert from 'node:assert/strict';

globalThis.document={getElementById:()=>({textContent:JSON.stringify({role:'mahasiswa',base:'/capstone'})})};
globalThis.window={location:{search:''}};
const factories={};
const Alpine={data:(name,factory)=>factories[name]=factory};
const {registerDashboards}=await import('../resources/assets/js/pages/dashboards.js');
registerDashboards(Alpine);

test('dashboard phaseLabel matches every workflow phase exactly once',()=>{
    const page=factories.capstoneDashboard();
    // Must stay 1:1 with DocumentController::PHASES.
    assert.deepEqual(
        ['PDC1','SEMPRO','PDC2','TA','EXPO','SIDANG'].map(c=>page.phaseLabel(c)),
        ['PDC 1','Seminar Proposal','PDC 2','TA Draft','Expo','Sidang TA'],
    );
});

test('dashboard phaseLabel never renders the TA phase as Sidang TA',()=>{
    const page=factories.capstoneDashboard();
    assert.equal(page.phaseLabel('TA'),'TA Draft');
    assert.notEqual(page.phaseLabel('TA'),page.phaseLabel('SIDANG'));
});
