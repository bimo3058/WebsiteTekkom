import {test} from 'node:test';
import assert from 'node:assert/strict';

globalThis.document={getElementById:()=>({textContent:JSON.stringify({role:'mahasiswa',base:'/capstone'})})};
globalThis.window={location:{search:''}};
const factories={};
const Alpine={data:(name,factory)=>factories[name]=factory};
const {registerDashboards}=await import('../resources/assets/js/pages/dashboards.js');
const {registerDocuments}=await import('../resources/assets/js/pages/documents.js');
registerDashboards(Alpine);
registerDocuments(Alpine);

test('dashboard shows submitted work and future requirements without unlocking future uploads',()=>{
    const page=factories.capstoneDashboard();
    page.workflow={phases:[
        {phase:'PDC1',status:'submitted',can_upload:true,documents:[{type:'C100',status:'SUBMITTED',latest_document:{id:12},can_upload:true}]},
        {phase:'SEMPRO',status:'locked',can_upload:false,locked_reason:'Menunggu jadwal',documents:[{type:'PPT Presentasi',status:'missing',can_upload:false}]},
    ]};
    assert.equal(page.dashboardDocuments.length,6);
    assert.equal(page.documentStatus(page.dashboardDocuments[0].status),'Terkirim');
    assert.equal(page.documentStatus(page.dashboardDocuments[1].status),'Belum Upload');
    assert.equal(page.dashboardDocuments[1].can_upload,false);
    assert.equal(page.dashboardDocuments[3].locked_reason,'Menunggu jadwal');
    assert.equal(page.documentHref(page.dashboardDocuments[0]),'/capstone/mahasiswa/documents?phase=PDC1&type=C100&document=12');
});

test('reference order and phase labels stay fixed regardless of workflow order or extra requirements',()=>{
    const page=factories.capstoneDashboard();
    page.workflow={phases:[
        {phase:'PDC2',can_upload:true,documents:[{type:'C500',status:'APPROVED',latest_document:{id:50}},{type:'C400',status:'missing',can_upload:true}]},
        {phase:'PDC1',can_upload:true,documents:[{type:'Extra',status:'SUBMITTED'},{type:'C300',status:'REJECTED',latest_document:{id:30}},{type:'C100',status:'SUBMITTED',latest_document:{id:10}}]},
        {phase:'SEMPRO',can_upload:false,documents:[{type:'PPT Presentasi',status:'missing',can_upload:false}]},
    ]};
    const docs=page.dashboardDocuments;
    assert.deepEqual(docs.map(d=>[d.name,d.phaseLabel]),[['C100','PDC 1'],['C200','PDC 1'],['C300','PDC 1'],['PPT Presentasi','SEMPRO'],['C400','PDC 2'],['C500','PDC 2']]);
    assert.deepEqual(docs.map(d=>d.status),['SUBMITTED','missing','REJECTED','missing','missing','APPROVED']);
    assert.equal(docs[1].can_upload,false);
    assert.equal(docs[4].can_upload,true);
    assert.equal(docs[5].latest_document.id,50);
    assert.equal(page.documentHref(docs[4]),'/capstone/mahasiswa/documents?phase=PDC2&type=C400');
});

test('dashboard links open the matching upload form only when the workflow allows it',async()=>{
    window.location.search='?phase=PDC1&type=C100';
    const page=factories.capstoneDocuments();
    page.workflow={phases:[{phase:'PDC1',can_upload:true,documents:[{type:'C100',can_upload:true}]}]};
    page.load=async()=>{};
    let opened=0;
    page.openUpload=(phase,doc)=>{opened++;assert.equal(phase.phase,'PDC1');assert.equal(doc.type,'C100');};
    await page.init();
    assert.equal(opened,1);
    page.workflow.phases[0].can_upload=false;
    await page.init();
    assert.equal(opened,1);
});

test('view action finds submitted documents without opening the upload form',async()=>{
    window.location.search='?phase=PDC1&type=C100&document=12';
    const page=factories.capstoneDocuments();
    page.workflow={phases:[{phase:'PDC1',can_upload:true,documents:[{type:'C100',can_upload:true}]}]};
    page.load=async()=>{};
    page.openUpload=()=>assert.fail('View must not open the upload form');
    await page.init();
    assert.equal(page.search,'C100');
});
