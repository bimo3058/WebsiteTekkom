import {test} from 'node:test';
import assert from 'node:assert/strict';
globalThis.document={getElementById:id=>id==='capstone-context'?{textContent:JSON.stringify({role:'admin',base:'/capstone',api:'/capstone/session/capstone',params:{}})}:{showModal(){},close(){}},querySelector:()=>({content:'csrf'})};
globalThis.window={dispatchEvent(){},location:{assign(){}}};
const {adminUsers}=await import('../resources/assets/js/pages/admin/users.js');
const reply=data=>({ok:true,json:async()=>data});
const users=[
    {id:1,name:'Ali Rahman',email:'ali@example.test',roles:['mahasiswa'],status:'active',nim:'20240001',nip:null,created_at:'2024-01-10T00:00:00Z',can_delete:true},
    {id:2,name:'Siti Dosen',email:'siti@example.test',roles:['dosen'],status:'active',nim:null,nip:'NIP-001',created_at:'2023-05-01T00:00:00Z',can_delete:true},
    {id:3,name:'Budi Admin',email:'budi@example.test',roles:['admin'],status:'suspended',nim:null,nip:null,created_at:'2022-01-01T00:00:00Z',can_delete:false},
];
async function loadedPage(){
    let calls=0;
    globalThis.fetch=async path=>{
        calls++;
        const page=Number(new URL(path,'http://x').searchParams.get('page')||1);
        return reply(page>=2?{data:[users[2]],pagination:{last_page:2}}:{data:users.slice(0,2),pagination:{last_page:2}});
    };
    const page=adminUsers();
    await page.init();
    return {page,calls:()=>calls};
}
test('loadAll fetches every page once and shows the table without loading',async()=>{
    const {page,calls}=await loadedPage();
    assert.equal(calls(),2);
    assert.equal(page.items.length,3);
    assert.equal(page.loading,false);
    assert.equal(page.error,'');
    assert.equal(page.userTotal,3);
});
test('typing search filters client-side with no extra request',async()=>{
    const {page,calls}=await loadedPage();
    page.search='ali';page.filter();
    assert.equal(calls(),2);
    assert.equal(page.page,1);
    assert.deepEqual(page.pagedUsers.map(u=>u.id),[1]);
    page.search='NIP-001';page.filter();
    assert.deepEqual(page.pagedUsers.map(u=>u.id),[2]);
    page.search='20240001';page.filter();
    assert.deepEqual(page.pagedUsers.map(u=>u.id),[1]);
    page.search='no-match-xyz';page.filter();
    assert.deepEqual(page.pagedUsers,[]);
    assert.equal(calls(),2);
});
test('role and status filters apply client-side with no extra request',async()=>{
    const {page,calls}=await loadedPage();
    page.role='admin';page.filter();
    assert.deepEqual(page.pagedUsers.map(u=>u.id),[3]);
    page.role='all';page.status='suspended';page.filter();
    assert.deepEqual(page.pagedUsers.map(u=>u.id),[3]);
    page.status='active';page.filter();
    assert.deepEqual(page.pagedUsers.map(u=>u.id),[1,2]);
    assert.equal(calls(),2);
});
test('sort and pagination slice the filtered list',async()=>{
    const {page}=await loadedPage();
    page.applySort('date');
    assert.deepEqual(page.pagedUsers.map(u=>u.id),[1,2,3]);
    page.applySort('name');
    assert.deepEqual(page.pagedUsers.map(u=>u.id),[1,3,2]);
    page.applySort('name');
    page.pageSize=2;page.filter();
    assert.equal(page.userTotal,3);
    assert.equal(page.userLastPage,2);
    assert.deepEqual(page.pagedUsers.map(u=>u.id),[1,3]);
    assert.equal(page.userFrom,1);
    assert.equal(page.userTo,2);
    page.page=2;
    assert.deepEqual(page.pagedUsers.map(u=>u.id),[2]);
    assert.deepEqual(page.userPageList,[1,2]);
});
test('select-all covers the visible page and remove reloads the list',async()=>{
    const {page,calls}=await loadedPage();
    page.pageSize=2;page.filter();
    page.toggleAll(true);
    assert.deepEqual(page.selectedIds,[1]);
    assert.equal(page.selectedAll,true);
    page.toggleAll(false);
    assert.equal(page.selectedAll,false);
    let reloads=0;
    globalThis.fetch=async (path,options)=>{if(options?.method==='DELETE')return reply({});reloads++;return reply({data:[],pagination:{last_page:1}});};
    page.deleting=[users[0]];
    await page.remove();
    assert.ok(reloads>0);
    assert.equal(page.items.length,0);
});
