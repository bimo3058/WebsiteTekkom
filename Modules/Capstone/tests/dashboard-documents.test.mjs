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

test('dashboard flattens every configured workflow document with per-document permissions',()=>{
    const page=factories.capstoneDashboard();
    page.workflow={phases:[
        {phase:'PDC1',status:'submitted',documents:[{type:'C100',status:'SUBMITTED',latest_document:{id:12},can_upload:true,locked_reason:null}]},
        {phase:'SEMPRO',status:'locked',documents:[{type:'PPT Presentasi',status:'missing',can_upload:false,locked_reason:'Menunggu jadwal',latest_document:null}]},
    ]};
    assert.equal(page.dashboardDocuments.length,2);
    assert.equal(page.documentStatus(page.dashboardDocuments[0].status),'Terkirim');
    assert.equal(page.documentStatus(page.dashboardDocuments[1].status),'Belum Upload');
    assert.equal(page.dashboardDocuments[1].can_upload,false);
    assert.equal(page.dashboardDocuments[1].locked_reason,'Menunggu jadwal');
    assert.equal(page.documentHref(page.dashboardDocuments[0]),'/capstone/mahasiswa/documents?phase=PDC1&type=C100&document=12');
});

test('dashboard lists period-specific requirements with raw phase codes and paginates by five',()=>{
    const page=factories.capstoneDashboard();
    page.workflow={phases:[
        {phase:'PDC2',documents:[{type:'C400',status:'SUBMITTED',latest_document:{id:50},can_upload:false},{type:'C500',status:'missing',can_upload:true},{type:'HKI',status:'missing',can_upload:true}]},
        {phase:'TA_DRAFT',documents:[{type:'GENERAL',status:'missing',can_upload:false}]},
        {phase:'EXPO',documents:[{type:'Absen Kehadiran Pengunjung',status:'missing',can_upload:false},{type:'Nilai Pengunjung',status:'missing',can_upload:false}]},
    ]};
    const docs=page.dashboardDocuments;
    assert.deepEqual(docs.map(d=>d.name),['C400','C500','HKI','GENERAL','Absen Kehadiran Pengunjung','Nilai Pengunjung']);
    assert.deepEqual(docs.map(d=>d.phase),['PDC2','PDC2','PDC2','TA_DRAFT','EXPO','EXPO']);
    assert.equal(page.documentStatus(docs[0].status),'Terkirim');
    assert.equal(page.documentHref(docs[0]),'/capstone/mahasiswa/documents?phase=PDC2&type=C400&document=50');
    assert.equal(page.docTotal,6);
    assert.equal(page.docTotalPages,2);
    assert.deepEqual(page.docPageList,[1,2]);
    assert.equal(page.pagedDocuments.length,5);
    assert.equal(page.docFrom,1);
    assert.equal(page.docTo,5);
    page.docPage=2;
    assert.equal(page.pagedDocuments.length,1);
    assert.equal(page.docFrom,6);
    assert.equal(page.docTo,6);
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
