<x-banksoal::layouts.admin>
    @section('breadcrumbs')
    <a href="{{ route('banksoal.admin.kontrol-umum.pemetaan') }}" class="text-slate-500 hover:text-primary transition-colors">Kontrol Umum</a>
    <span class="mx-2 text-slate-300">/</span>
    <a href="{{ route('banksoal.admin.kontrol-umum.pemetaan') }}" class="text-slate-500 hover:text-primary transition-colors">Pemetaan</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Edit Pemetaan MK ke Dosen</span>
    @endsection

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .pm-page{--pm:rgb(11,38,110);--pm-hover:rgb(8,28,82);--pm-light:rgba(11,38,110,.08);--s50:#f8fafc;--s100:#f1f5f9;--s200:#e2e8f0;--s300:#cbd5e1;--s400:#94a3b8;--s500:#64748b;--s700:#334155;--s800:#1e293b}
        .pm-page *{box-sizing:border-box}
        .pm-page .page-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;gap:16px;flex-wrap:wrap}
        .pm-page .page-header h1{font-size:24px;font-weight:700;color:var(--s800);margin:0}
        .pm-page .page-header p{font-size:14px;color:var(--s500);margin:6px 0 0}
        .pm-page .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;border:none;transition:all .2s;text-decoration:none}
        .pm-page .btn-primary{background:var(--pm);color:#fff}.pm-page .btn-primary:hover{background:var(--pm-hover)}
        .pm-page .btn-secondary{background:#fff;color:var(--s700);border:1px solid var(--s300)}.pm-page .btn-secondary:hover{background:var(--s50)}
        .pm-page .mapping-grid{display:grid;grid-template-columns:320px 1fr;gap:20px;align-items:start}
        @media(max-width:900px){.pm-page .mapping-grid{grid-template-columns:1fr}}
        .pm-page .panel{background:#fff;border:1px solid var(--s200);border-radius:12px;overflow:hidden}
        .pm-page .panel-header{padding:16px 18px;border-bottom:1px solid var(--s200);background:var(--s50)}
        .pm-page .panel-header h2{font-size:13px;font-weight:700;color:var(--s800);margin:0;text-transform:uppercase;letter-spacing:.4px}
        .pm-page .panel-header p{font-size:12px;color:var(--s500);margin:4px 0 0}
        .pm-page .panel-locked{padding:16px 18px}
        .pm-page .locked-item{display:flex;align-items:center;gap:10px;padding:12px 16px;background:var(--pm);border-radius:8px;color:#fff;font-weight:700;font-size:14px}
        .pm-page .lock-note{margin-top:10px;font-size:12px;color:var(--s400);display:flex;align-items:center;gap:5px}
        .pm-page .panel-search{padding:12px 14px;border-bottom:1px solid var(--s200)}
        .pm-page .srch{position:relative}.pm-page .srch svg{position:absolute;left:10px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:var(--s400)}
        .pm-page .srch input{width:100%;padding:9px 12px 9px 32px;border:1px solid var(--s300);border-radius:8px;font-size:13px}
        .pm-page .srch input:focus{outline:none;border-color:var(--pm);box-shadow:0 0 0 3px rgba(59,130,246,.1)}
        .pm-page .check-list{max-height:420px;overflow-y:auto}
        .pm-page .check-item{display:flex;align-items:center;gap:12px;padding:10px 16px;border-bottom:1px solid var(--s100);cursor:pointer;transition:background .15s}
        .pm-page .check-item:last-child{border-bottom:none}.pm-page .check-item:hover{background:var(--s50)}
        .pm-page .check-item input[type=checkbox]{width:16px;height:16px;accent-color:var(--pm);cursor:pointer;flex-shrink:0}
        .pm-page .check-item.checked{background:rgba(11,38,110,.04)}.pm-page .check-item.checked span{color:var(--pm) !important}
        .pm-page .r-empty{padding:40px 16px;text-align:center;font-size:13px;color:var(--s400)}
        .pm-page .panel-footer{padding:10px 14px;border-top:1px solid var(--s200);background:var(--s50);display:flex;align-items:center;justify-content:space-between;gap:8px}
        .pm-page .pg-info{font-size:12px;color:var(--s500)}
        .pm-page .pagination{display:flex;gap:4px;align-items:center}
        .pm-page .pg-btn{min-width:30px;height:30px;padding:0 8px;border:1px solid var(--s200);background:#fff;color:var(--s700);border-radius:6px;font-size:12px;font-weight:600;cursor:pointer}
        .pm-page .pg-btn:hover:not(:disabled){border-color:var(--pm);color:var(--pm)}.pm-page .pg-btn.active{background:var(--pm);border-color:var(--pm);color:#fff}.pm-page .pg-btn:disabled{opacity:.4;cursor:not-allowed}
        .pm-page .sa-row{display:flex;align-items:center;gap:8px;padding:8px 16px;border-bottom:1px solid var(--s200);background:var(--s50)}
        .pm-page .sa-row input{width:16px;height:16px;accent-color:var(--pm);cursor:pointer}
        .pm-page .sa-row label{font-size:12px;font-weight:600;color:var(--s500);cursor:pointer}
        .pm-page .form-actions{display:flex;justify-content:flex-end;gap:12px;margin-top:24px}
    </style>
    @endpush
    <div class="pm-page">
    <div class="page-header">
        <div>
            <h1>Edit Pemetaan MK ke Dosen</h1>
            <p>Mata Kuliah sudah terkunci. Ubah centang Dosen yang mengampu MK ini.</p>
        </div>
        <a href="{{ route('banksoal.admin.kontrol-umum.pemetaan') }}" class="btn btn-secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>
    <div class="mapping-grid">
        <div class="panel">
            <div class="panel-header"><h2>Mata Kuliah Terpilih</h2><p>MK tidak dapat diubah di sini</p></div>
            <div class="panel-locked">
                <div class="locked-item">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    {{ $mk->kode }} – {{ $mk->nama }}
                </div>
                <div class="lock-note">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    MK dikunci saat mode edit
                </div>
            </div>
        </div>
        <div class="panel">
            <div class="panel-header"><h2>Pilih Dosen Pengampu</h2><p>Centang satu atau lebih Dosen</p></div>
            <div class="panel-search">
                <div class="srch">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" id="rightSearch" placeholder="Cari Dosen..." oninput="onSearch(this.value)">
                </div>
            </div>
            <div class="sa-row">
                <input type="checkbox" id="selectAllRight" onchange="toggleSelectAll(this.checked)">
                <label for="selectAllRight">Pilih semua yang tampil</label>
            </div>
            <div class="check-list" id="rightItems"></div>
            <div class="r-empty" id="rightEmpty" style="display:none;">Tidak ada data Dosen</div>
            <div class="panel-footer" id="rightFooter" style="display:none;">
                <span class="pg-info" id="rightPageInfo"></span>
                <div class="pagination" id="rightPagination"></div>
            </div>
        </div>
    </div>
    <div class="form-actions">
        <a href="{{ route('banksoal.admin.kontrol-umum.pemetaan') }}" class="btn btn-secondary">Batal</a>
        <button type="button" class="btn btn-primary" id="btnSimpan" onclick="submitMapping()">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Simpan Perubahan
        </button>
    </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
    <script>
    const CSRF='{{ csrf_token() }}',BASE_API='{{ url("/bank-soal/admin/api") }}',BACK_URL='{{ route("banksoal.admin.kontrol-umum.pemetaan") }}';
    const MK_ID={{ $mk->id }},RS=10;
    const s={pool:[],sel:new Set({{ json_encode($selectedIds) }}),q:'',p:1};
    document.addEventListener('DOMContentLoaded',async()=>{
        window.showLoader();
        try{const r=await fetch(BASE_API+'/pemetaan/options',{headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF}});const d=await r.json();if(d.success)s.pool=d.data.dosen.sort((a,b)=>a.name.localeCompare(b.name,undefined,{numeric:true}));}catch(e){console.error(e);}
        window.hideLoader();render();
    });
    function onSearch(v){s.q=v;s.p=1;render();}
    function filt(){return s.pool.filter(c=>c.name.toLowerCase().includes(s.q.toLowerCase()));}
    function render(){
        const all=filt(),total=all.length,tp=Math.max(1,Math.ceil(total/RS));
        if(s.p>tp)s.p=1;const paged=all.slice((s.p-1)*RS,s.p*RS);
        const el=document.getElementById('rightItems'),em=document.getElementById('rightEmpty');
        if(!paged.length){el.innerHTML='';em.style.display='block';}
        else{em.style.display='none';el.innerHTML=paged.map(c=>`<label class="check-item ${s.sel.has(c.id)?'checked':''}" onclick="event.preventDefault();tog(${c.id},this)"><input type="checkbox" ${s.sel.has(c.id)?'checked':''} onchange="tog(${c.id},this.closest('.check-item'))"><span style="font-size:13px;font-weight:600;color:#334155">${c.name}</span></label>`).join('')+'<div class="check-item" style="visibility:hidden;pointer-events:none"><input type="checkbox" disabled><span>&nbsp;</span></div>'.repeat(Math.max(0,RS-paged.length));}
        document.getElementById('rightFooter').style.display=total>RS?'flex':'none';
        document.getElementById('rightPageInfo').textContent=`${Math.min((s.p-1)*RS+1,total)}–${Math.min(s.p*RS,total)} dari ${total}`;
        let b=`<button class="pg-btn" onclick="setP(${s.p-1})" ${s.p<=1?'disabled':''}>‹</button>`;
        for(let i=1;i<=tp;i++){if(tp<=7||i===1||i===tp||Math.abs(i-s.p)<=1)b+=`<button class="pg-btn ${i===s.p?'active':''}" onclick="setP(${i})">${i}</button>`;else if(Math.abs(i-s.p)===2)b+=`<span style="padding:0 4px;color:#94a3b8">…</span>`;}
        b+=`<button class="pg-btn" onclick="setP(${s.p+1})" ${s.p>=tp?'disabled':''}>›</button>`;
        document.getElementById('rightPagination').innerHTML=b;
        const saEl=document.getElementById('selectAllRight'),cc=paged.filter(c=>s.sel.has(c.id)).length;
        saEl.checked=paged.length>0&&cc===paged.length;saEl.indeterminate=cc>0&&cc<paged.length;
    }
    function tog(id,row){s.sel.has(id)?s.sel.delete(id):s.sel.add(id);row.classList.toggle('checked',s.sel.has(id));const cb=row.querySelector('input[type=checkbox]');if(cb)cb.checked=s.sel.has(id);render();}
    function toggleSelectAll(chk){const paged=filt().slice((s.p-1)*RS,s.p*RS);paged.forEach(c=>chk?s.sel.add(c.id):s.sel.delete(c.id));render();}
    function setP(p){s.p=p;render();}
    async function submitMapping(){
        const btn=document.getElementById('btnSimpan');btn.disabled=true;btn.innerHTML='Simpan Perubahan';
        window.showLoader();
        try{const r=await fetch(BASE_API+'/pemetaan/dosen-mk/sync',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify({mk_id:MK_ID,user_ids:[...s.sel]})});const d=await r.json();
        if(r.ok&&d.success){await Swal.fire({icon:'success',title:'Berhasil',text:d.message,timer:1600,showConfirmButton:false});window.location.href=BACK_URL;}
        else{Swal.fire({icon:'error',title:'Gagal',text:d.message||'Terjadi kesalahan'});btn.disabled=false;btn.innerHTML='Simpan Perubahan';}
        }catch(e){Swal.fire({icon:'error',title:'Error',text:e.message});btn.disabled=false;btn.innerHTML='Simpan Perubahan';}
        finally { window.hideLoader(); }
    }
    </script>
    @endpush
</x-banksoal::layouts.admin>
