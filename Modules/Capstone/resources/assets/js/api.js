export const context = JSON.parse(document.getElementById('capstone-context')?.textContent || '{}');
export const unwrap = response => response?.data ?? response;
export const rows = response => { const data = unwrap(response); return Array.isArray(data) ? data : (data?.data ?? []); };
export const url = path => `${context.base}${path.startsWith('/') ? path : '/'+path}`;
export function notify(message, error = false) { window.dispatchEvent(new CustomEvent('capstone-notice', {detail:{message,error}})); }
const inflight = new Map();
let pendingRequests = 0;
export const getPendingRequests = () => pendingRequests;
function beginActivity() {
    const publish = () => window.dispatchEvent(new CustomEvent('capstone-activity', {detail:{pending:pendingRequests}}));
    pendingRequests++;
    publish();
    return () => { pendingRequests--; publish(); };
}
export async function api(path, options = {}) {
    if (!path.startsWith('/') || path.startsWith('//')) throw new Error('Invalid API path');
    const method = options.method || 'GET';
    const key = `${context.role}:${path}`;
    if (method === 'GET' && inflight.has(key)) return inflight.get(key);
    const {activity = true, ...requestOptions} = options;
    const finishActivity = activity ? beginActivity() : () => {};
    const task = (async () => {
        const headers = {Accept:'application/json', 'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'X-Capstone-Role':context.role || '', ...options.headers};
        let body = options.body;
        if (body && !(body instanceof FormData)) { headers['Content-Type']='application/json'; body=JSON.stringify(body); }
        const response = await fetch(`${context.api}${path}`, {...requestOptions,method,body,headers,credentials:'same-origin'});
        if (options.blob && response.ok) return response.blob();
        const payload = await response.json().catch(()=>({message:`HTTP ${response.status}`}));
        if (!response.ok) { const error = new Error(payload.message || 'Permintaan gagal'); error.status=response.status; error.errors=payload.errors || {}; error.payload=payload; throw error; }
        return payload;
    })();
    if (method === 'GET') inflight.set(key,task);
    try { return await task; } finally { if(method === 'GET') inflight.delete(key); finishActivity(); }
}
export const date = (value, withTime=false) => value ? new Date(value).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric',...(withTime ? {hour:'2-digit',minute:'2-digit'} : {})}) : '—';
export const get = (value,path,fallback='—') => path.split('.').reduce((acc,key)=>acc?.[key],value) ?? fallback;
export function download(blob,filename) { const objectUrl=URL.createObjectURL(blob); const a=document.createElement('a'); a.href=objectUrl; a.download=filename; a.click(); setTimeout(()=>URL.revokeObjectURL(objectUrl),1000); }

export function upload(path,body,onProgress=()=>{}) {
    if(!path.startsWith('/') || path.startsWith('//'))return Promise.reject(new Error('Invalid API path'));
    const finishActivity = beginActivity();
    return new Promise((resolve,reject)=>{
        const xhr=new XMLHttpRequest();
        xhr.open('POST',`${context.api}${path}`);
        xhr.setRequestHeader('Accept','application/json');
        xhr.setRequestHeader('X-Requested-With','XMLHttpRequest');
        xhr.setRequestHeader('X-CSRF-TOKEN',document.querySelector('meta[name="csrf-token"]').content);
        xhr.setRequestHeader('X-Capstone-Role',context.role || '');
        xhr.upload.onprogress=e=>{if(e.lengthComputable)onProgress(Math.round(e.loaded*100/e.total));};
        xhr.onload=()=>{let payload;try{payload=JSON.parse(xhr.responseText);}catch{payload={message:`HTTP ${xhr.status}`};}if(xhr.status>=200&&xhr.status<300){resolve(payload);return;}const error=new Error(payload.message || 'Upload failed');error.status=xhr.status;error.errors=payload.errors || {};reject(error);};
        xhr.onerror=()=>reject(new Error('Upload gagal: koneksi terputus.'));
        xhr.onabort=()=>reject(new Error('Upload dibatalkan.'));
        xhr.ontimeout=()=>reject(new Error('Upload gagal: waktu tunggu habis.'));
        xhr.send(body);
    }).finally(finishActivity);
}
