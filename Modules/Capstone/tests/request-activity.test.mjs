import {test} from 'node:test';
import assert from 'node:assert/strict';

const events = [];
globalThis.document = {
    getElementById: () => ({textContent: JSON.stringify({role:'mahasiswa', api:'/capstone/session/capstone'})}),
    querySelector: () => ({content:'csrf'}),
};
globalThis.window = {dispatchEvent: event => events.push(event)};
const {api, upload, getPendingRequests} = await import('../resources/assets/js/api.js');
const deferred = () => { let resolve, reject; const promise = new Promise((yes, no) => { resolve=yes; reject=no; }); return {promise, resolve, reject}; };
const response = {ok:true, json:async () => ({data:[]})};

test('concurrent requests keep loading active and duplicate GETs share one request', async () => {
    events.length = 0;
    const first = deferred(), second = deferred();
    let calls = 0;
    globalThis.fetch = () => (++calls === 1 ? first.promise : second.promise);
    const a = api('/first'), duplicate = api('/first'), b = api('/second');
    assert.equal(calls, 2);
    assert.equal(getPendingRequests(), 2);
    first.resolve(response);
    await Promise.all([a, duplicate]);
    assert.equal(getPendingRequests(), 1);
    second.resolve(response);
    await b;
    assert.equal(getPendingRequests(), 0);
    assert.deepEqual(events.map(event => event.detail.pending), [1,2,1,0]);
    assert.ok(events.every(event => event.type === 'capstone-activity'));
});

test('HTTP and network failures stop loading without emitting success notices', async () => {
    events.length = 0;
    globalThis.fetch = async () => ({ok:false, status:422, json:async () => ({message:'Validation failed'})});
    await assert.rejects(api('/save', {method:'POST', body:{title:''}}), /Validation failed/);
    assert.equal(getPendingRequests(), 0);
    globalThis.fetch = async () => { throw new Error('Offline'); };
    await assert.rejects(api('/offline'), /Offline/);
    assert.equal(getPendingRequests(), 0);
    assert.ok(events.every(event => event.type === 'capstone-activity'));
});

test('downloads stay busy until body resolves and silent background checks do not start loading', async () => {
    const body = deferred();
    globalThis.fetch = async () => ({ok:true, blob:() => body.promise});
    const request = api('/download', {blob:true});
    await Promise.resolve();
    assert.equal(getPendingRequests(), 1);
    body.resolve(new Blob(['test']));
    await request;
    assert.equal(getPendingRequests(), 0);
    events.length = 0;
    globalThis.fetch = async (_path, options) => { assert.equal('activity' in options, false); return response; };
    await api('/notifications/unread-count', {activity:false});
    assert.equal(events.length, 0);
});

test('uploads clear loading on success, HTTP failure, network failure, cancellation and timeout', async () => {
    let xhr;
    globalThis.XMLHttpRequest = class {
        constructor() { xhr=this; this.upload={}; }
        open() {}
        setRequestHeader() {}
        send() {}
    };
    for (const outcome of ['success','http','error','abort','timeout']) {
        const request = upload('/documents', new FormData());
        assert.equal(getPendingRequests(), 1);
        if (outcome === 'success' || outcome === 'http') {
            xhr.status = outcome === 'success' ? 201 : 422;
            xhr.responseText = JSON.stringify({message:outcome});
            xhr.onload();
        } else xhr[`on${outcome}`]();
        if (outcome === 'success') await request;
        else await assert.rejects(request);
        assert.equal(getPendingRequests(), 0);
    }
    globalThis.XMLHttpRequest = class { constructor() { throw new Error('Unavailable'); } };
    await assert.rejects(upload('/documents', new FormData()), /Unavailable/);
    assert.equal(getPendingRequests(), 0);
});
