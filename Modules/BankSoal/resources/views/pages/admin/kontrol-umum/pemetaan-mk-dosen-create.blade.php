<x-banksoal::layouts.admin>
    @section('breadcrumbs')
    <a href="{{ route('banksoal.admin.kontrol-umum.pemetaan') }}" class="text-slate-500 hover:text-primary transition-colors">Kontrol Umum</a>
    <span class="mx-2 text-slate-300">/</span>
    <a href="{{ route('banksoal.admin.kontrol-umum.pemetaan') }}" class="text-slate-500 hover:text-primary transition-colors">Pemetaan</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Tambah Pemetaan MK & Dosen</span>
    @endsection

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .pm-page{--pm:rgb(11,38,110);--pm-hover:rgb(8,28,82);--pm-light:rgba(11,38,110,.08);--s50:#f8fafc;--s100:#f1f5f9;--s200:#e2e8f0;--s300:#cbd5e1;--s400:#94a3b8;--s500:#64748b;--s700:#334155;--s800:#1e293b}
        .pm-page *{box-sizing:border-box}
        .pm-page .page-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;gap:16px;flex-wrap:wrap}
        .pm-page .page-header h1{font-size:24px;font-weight:700;color:var(--s800);margin:0}
        .pm-page .page-header p{font-size:14px;color:var(--s500);margin:6px 0 0}
        .pm-page .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;border:none;transition:all .2s;text-decoration:none}
        .pm-page .btn-primary{background:var(--pm);color:#fff}.pm-page .btn-primary:hover{background:var(--pm-hover);transform:translateY(-1px);box-shadow:0 4px 12px rgba(11,38,110,.2)}
        .pm-page .btn-primary:disabled{opacity:.6;cursor:not-allowed;transform:none}
        .pm-page .btn-secondary{background:#fff;color:var(--s700);border:1px solid var(--s300)}.pm-page .btn-secondary:hover{background:var(--s50);border-color:var(--s400)}
        .pm-page .tabs{display:flex;gap:6px;padding:6px;background:#fff;border:1px solid var(--s200);border-radius:12px;width:fit-content;margin-bottom:20px}
        .pm-page .tab-btn{padding:9px 22px;border-radius:8px;border:none;font-size:14px;font-weight:600;cursor:pointer;color:var(--s500);background:transparent;transition:all .2s}
        .pm-page .tab-btn:hover{color:var(--s800);background:var(--s50)}.pm-page .tab-btn.active{background:var(--pm);color:#fff;box-shadow:0 2px 8px rgba(11,38,110,.2)}
        .pm-page .tab-content{display:none}.pm-page .tab-content.active{display:block}
        .pm-page .mapping-grid{display:grid;grid-template-columns:320px 1fr;gap:20px;align-items:start}
        @media(max-width:900px){.pm-page .mapping-grid{grid-template-columns:1fr}}
        .pm-page .panel{background:#fff;border:1px solid var(--s200);border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.05)}
        .pm-page .panel-header{padding:14px 16px;border-bottom:1px solid var(--s200);background:var(--s50)}
        .pm-page .panel-header h2{font-size:13px;font-weight:700;color:var(--s800);margin:0;text-transform:uppercase;letter-spacing:.4px}
        .pm-page .panel-header p{font-size:12px;color:var(--s500);margin:3px 0 0}
        .pm-page .panel-search{padding:10px 12px;border-bottom:1px solid var(--s200)}
        .pm-page .srch{position:relative}.pm-page .srch svg{position:absolute;left:9px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--s400)}
        .pm-page .srch input{width:100%;padding:8px 10px 8px 30px;border:1px solid var(--s300);border-radius:8px;font-size:13px;color:var(--s800);transition:border-color .2s}
        .pm-page .srch input:focus{outline:none;border-color:var(--pm);box-shadow:0 0 0 3px rgba(59,130,246,.1)}
        .pm-page .items-list{padding:8px;display:flex;flex-direction:column;gap:5px;max-height:400px;overflow-y:auto}
        .pm-page .comp-item{padding:9px 13px;border-radius:8px;border:1.5px solid var(--s200);cursor:pointer;transition:all .18s;font-size:13px;font-weight:600;color:var(--s700);user-select:none}
        .pm-page .comp-item:hover:not(.disabled):not(.selected){border-color:var(--pm);color:var(--pm);background:var(--pm-light)}
        .pm-page .comp-item.selected{background:var(--pm);border-color:var(--pm);color:#fff;box-shadow:0 2px 8px rgba(11,38,110,.18)}
        .pm-page .comp-item.disabled{background:var(--s100);border-color:var(--s200);color:var(--s400);cursor:not-allowed}
        .pm-page .sel-badge{margin:10px 12px;padding:7px 11px;background:var(--pm-light);border:1px solid rgba(11,38,110,.15);border-radius:8px;font-size:12px;color:var(--pm);font-weight:600;display:none}
        .pm-page .sel-badge.show{display:block}
        .pm-page .check-list{max-height:400px;overflow-y:auto}
        .pm-page .check-item{display:flex;align-items:center;gap:10px;padding:9px 14px;border-bottom:1px solid var(--s100);cursor:pointer;transition:background .15s}
        .pm-page .check-item:last-child{border-bottom:none}.pm-page .check-item:hover{background:var(--s50)}
        .pm-page .check-item input[type=checkbox]{width:15px;height:15px;accent-color:var(--pm);cursor:pointer;flex-shrink:0}
        .pm-page .check-item.checked{background:rgba(11,38,110,.04)}.pm-page .check-item.checked span{color:var(--pm) !important}
        .pm-page .r-empty{padding:36px 16px;text-align:center;font-size:13px;color:var(--s400)}
        .pm-page .panel-footer{padding:8px 12px;border-top:1px solid var(--s200);background:var(--s50);display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap}
        .pm-page .pg-info{font-size:12px;color:var(--s500)}
        .pm-page .pagination{display:flex;gap:4px}
        .pm-page .pg-btn{min-width:28px;height:28px;padding:0 6px;border:1px solid var(--s200);background:#fff;color:var(--s700);border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;transition:all .15s}
        .pm-page .pg-btn:hover:not(:disabled){border-color:var(--pm);color:var(--pm)}.pm-page .pg-btn.active{background:var(--pm);border-color:var(--pm);color:#fff}.pm-page .pg-btn:disabled{opacity:.4;cursor:not-allowed}
        .pm-page .sa-row{display:flex;align-items:center;gap:8px;padding:7px 14px;border-bottom:1px solid var(--s200);background:var(--s50)}
        .pm-page .sa-row input{width:15px;height:15px;accent-color:var(--pm);cursor:pointer}
        .pm-page .sa-row label{font-size:12px;font-weight:600;color:var(--s500);cursor:pointer}
        .pm-page .form-actions{display:flex;justify-content:flex-end;gap:12px;margin-top:20px}
    </style>
    @endpush

    <div class="pm-page">
    <div class="page-header">
        <div>
            <h1>Tambah Pemetaan MK & Dosen</h1>
            <p>Pilih arah pemetaan, lalu tentukan komponen terpilih dan pasangannya.</p>
        </div>
        <a href="{{ route('banksoal.admin.kontrol-umum.pemetaan') }}" class="btn btn-secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>

    <div class="tabs">
        <button class="tab-btn active" id="tabBtn0" onclick="switchTab(0)">MK ke Dosen</button>
        <button class="tab-btn" id="tabBtn1" onclick="switchTab(1)">Dosen ke MK</button>
    </div>

    {{-- TAB 0: MK → Dosen --}}
    <div class="tab-content active" id="tab0">
        <div class="mapping-grid">
            <div class="panel">
                <div class="panel-header"><h2>Matkul Terpilih</h2><p>Pilih satu Mata Kuliah</p></div>
                <div class="panel-search"><div class="srch"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg><input type="text" placeholder="Cari MK..." oninput="T[0].lq=this.value;T[0].lp=1;renderLeft(0)"></div></div>
                <div class="sel-badge" id="badge0"></div>
                <div class="items-list" id="leftItems0"></div>
                <div class="panel-footer" id="leftFoot0" style="display:none"><span class="pg-info" id="lpi0"></span><div class="pagination" id="lpg0"></div></div>
            </div>
            <div class="panel">
                <div class="panel-header"><h2>Pilih Dosen</h2><p>Centang satu atau lebih Dosen</p></div>
                <div class="panel-search"><div class="srch"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg><input type="text" placeholder="Cari Dosen..." oninput="T[0].rq=this.value;T[0].rp=1;renderRight(0)"></div></div>
                <div class="sa-row"><input type="checkbox" id="sa0" onchange="toggleAll(0,this.checked)"><label for="sa0">Pilih semua yang tampil</label></div>
                <div class="check-list" id="rightItems0"></div>
                <div class="r-empty" id="rEmpty0" style="display:none">Tidak ada data Dosen</div>
                <div class="panel-footer" id="rightFoot0" style="display:none"><span class="pg-info" id="rpi0"></span><div class="pagination" id="rpg0"></div></div>
            </div>
        </div>
        <div class="form-actions">
            <a href="{{ route('banksoal.admin.kontrol-umum.pemetaan') }}" class="btn btn-secondary">Batal</a>
            <button class="btn btn-primary" id="saveBtn0" onclick="doSubmit(0)" disabled>
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Pemetaan
            </button>
        </div>
    </div>

    {{-- TAB 1: Dosen → MK --}}
    <div class="tab-content" id="tab1">
        <div class="mapping-grid">
            <div class="panel">
                <div class="panel-header"><h2>Dosen Terpilih</h2><p>Pilih satu Dosen</p></div>
                <div class="panel-search"><div class="srch"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg><input type="text" placeholder="Cari Dosen..." oninput="T[1].lq=this.value;T[1].lp=1;renderLeft(1)"></div></div>
                <div class="sel-badge" id="badge1"></div>
                <div class="items-list" id="leftItems1"></div>
                <div class="panel-footer" id="leftFoot1" style="display:none"><span class="pg-info" id="lpi1"></span><div class="pagination" id="lpg1"></div></div>
            </div>
            <div class="panel">
                <div class="panel-header"><h2>Pilih Matkul</h2><p>Centang satu atau lebih Mata Kuliah</p></div>
                <div class="panel-search"><div class="srch"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg><input type="text" placeholder="Cari MK..." oninput="T[1].rq=this.value;T[1].rp=1;renderRight(1)"></div></div>
                <div class="sa-row"><input type="checkbox" id="sa1" onchange="toggleAll(1,this.checked)"><label for="sa1">Pilih semua yang tampil</label></div>
                <div class="check-list" id="rightItems1"></div>
                <div class="r-empty" id="rEmpty1" style="display:none">Tidak ada data MK</div>
                <div class="panel-footer" id="rightFoot1" style="display:none"><span class="pg-info" id="rpi1"></span><div class="pagination" id="rpg1"></div></div>
            </div>
        </div>
        <div class="form-actions">
            <a href="{{ route('banksoal.admin.kontrol-umum.pemetaan') }}" class="btn btn-secondary">Batal</a>
            <button class="btn btn-primary" id="saveBtn1" onclick="doSubmit(1)" disabled>
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Pemetaan
            </button>
        </div>
    </div>{{-- end tab1 --}}
    </div>{{-- end .pm-page --}}

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
    <script>
    const CSRF='{{ csrf_token() }}',BASE='{{ url("/bank-soal/admin/api") }}',BACK='{{ route("banksoal.admin.kontrol-umum.pemetaan") }}';
    const LS=8,RS=10;
    const T=[
        {leftPool:[],rightPool:[],selId:null,selIds:new Set(),lq:'',lp:1,rq:'',rp:1,
         endpoint:BASE+'/pemetaan/dosen-mk/sync',leftKey:'mk_id',rightKey:'user_ids'},
        {leftPool:[],rightPool:[],selId:null,selIds:new Set(),lq:'',lp:1,rq:'',rp:1,
         endpoint:BASE+'/pemetaan/dosen-mk-by-dosen/sync',leftKey:'user_id',rightKey:'mk_ids'},
    ];
    let opt={mk:[],dosen:[]};

    document.addEventListener('DOMContentLoaded',async()=>{
        window.showLoader();
        try{
            const r=await fetch(BASE+'/pemetaan/options',{headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF}});
            const d=await r.json();
            if(d.success){
                opt.mk=d.data.mata_kuliah.sort((a,b)=>(a.kode+a.nama).localeCompare(b.kode+b.nama,undefined,{numeric:true}));
                opt.dosen=d.data.dosen.sort((a,b)=>a.name.localeCompare(b.name,undefined,{sensitivity:'base'}));
            }
        }catch(e){console.error(e);}
        window.hideLoader();
        T[0].leftPool=opt.mk; T[0].rightPool=opt.dosen;
        T[1].leftPool=opt.dosen; T[1].rightPool=opt.mk;
        renderLeft(0);renderRight(0);
        renderLeft(1);renderRight(1);
    });

    function switchTab(i){
        document.querySelectorAll('.tab-btn').forEach((b,j)=>b.classList.toggle('active',i===j));
        document.querySelectorAll('.tab-content').forEach((c,j)=>c.classList.toggle('active',i===j));
    }

    function lbl(item,pool){
        if(pool===opt.mk) return item.kode+' – '+item.nama;
        return item.name; // dosen
    }

    function filtL(i){const q=T[i].lq.toLowerCase();return T[i].leftPool.filter(x=>lbl(x,T[i].leftPool).toLowerCase().includes(q));}
    function filtR(i){const q=T[i].rq.toLowerCase();return T[i].rightPool.filter(x=>lbl(x,T[i].rightPool).toLowerCase().includes(q));}

    function renderLeft(i){
        const all=filtL(i),total=all.length,tp=Math.max(1,Math.ceil(total/LS));
        if(T[i].lp>tp)T[i].lp=1;
        const paged=all.slice((T[i].lp-1)*LS,T[i].lp*LS);
        const el=document.getElementById('leftItems'+i);
        const rows=paged.map(x=>{
            const sel=T[i].selId===x.id;
            return `<div class="comp-item ${sel?'selected':''}" onclick="selL(${i},${x.id})">${lbl(x,T[i].leftPool)}</div>`;
        }).join('');
        const ghosts='<div class="comp-item" style="visibility:hidden;pointer-events:none">&nbsp;</div>'
            .repeat(Math.max(0,LS-paged.length));
        el.innerHTML=rows?rows+ghosts:'<div style="padding:20px;text-align:center;font-size:13px;color:#94a3b8">Tidak ada data</div>';
        const badge=document.getElementById('badge'+i);
        if(T[i].selId!==null){const f=T[i].leftPool.find(x=>x.id===T[i].selId);badge.textContent='✓ Terpilih: '+(f?lbl(f,T[i].leftPool):'');badge.classList.add('show');}
        else badge.classList.remove('show');
        const foot=document.getElementById('leftFoot'+i);
        foot.style.display=total>LS?'flex':'none';
        document.getElementById('lpi'+i).textContent=`Hal. ${T[i].lp}/${tp}`;
        const wS=Math.max(1,Math.min(T[i].lp-1,tp-2)),wE=Math.min(tp,wS+2);
        let lb=`<button class="pg-btn" onclick="setLP(${i},${T[i].lp-1})" ${T[i].lp<=1?'disabled':''}>‹</button>`;
        for(let p=wS;p<=wE;p++)lb+=`<button class="pg-btn ${p===T[i].lp?'active':''}" onclick="setLP(${i},${p})">${p}</button>`;
        lb+=`<button class="pg-btn" onclick="setLP(${i},${T[i].lp+1})" ${T[i].lp>=tp?'disabled':''}>›</button>`;
        document.getElementById('lpg'+i).innerHTML=lb;
        updBtn(i);
    }
    function setLP(i,p){T[i].lp=p;renderLeft(i);}
    async function selL(i,id){
        if (T[i].selId === id) {
            T[i].selId = null;
            T[i].selIds.clear();
            renderLeft(i);
            renderRight(i);
        } else {
            T[i].selId = id;
            T[i].selIds.clear();
            renderLeft(i);
            
            window.showLoader();
            try {
                const type = i === 0 ? 'dosen-mk' : 'dosen-by-dosen';
                const response = await fetch(`${BASE}/pemetaan/existing?type=${type}&id=${id}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
                });
                const result = await response.json();
                if (result.success && Array.isArray(result.data)) {
                    T[i].selIds = new Set(result.data);
                } else {
                    T[i].selIds.clear();
                }
            } catch (err) {
                console.error("Error fetching existing mapping:", err);
                T[i].selIds.clear();
            } finally {
                window.hideLoader();
                renderRight(i);
            }
        }
    }

    function renderRight(i){
        const all=filtR(i),total=all.length,tp=Math.max(1,Math.ceil(total/RS));
        if(T[i].rp>tp)T[i].rp=1;
        const paged=all.slice((T[i].rp-1)*RS,T[i].rp*RS);
        const el=document.getElementById('rightItems'+i),em=document.getElementById('rEmpty'+i);
        if(!paged.length){el.innerHTML='';em.style.display='block';}
        else{
            em.style.display='none';
            const rows=paged.map(x=>{
                const chk=T[i].selIds.has(x.id);
                return `<label class="check-item ${chk?'checked':''}" onclick="event.preventDefault();togR(${i},${x.id},this)">
                    <input type="checkbox" ${chk?'checked':''} onchange="togR(${i},${x.id},this.closest('.check-item'))">
                    <span style="font-size:13px;font-weight:600;color:#334155">${lbl(x,T[i].rightPool)}</span></label>`;
            }).join('');
            const ghosts='<div class="check-item" style="visibility:hidden;pointer-events:none"><input type="checkbox" disabled><span>&nbsp;</span></div>'
                .repeat(Math.max(0,RS-paged.length));
            el.innerHTML=rows+ghosts;
        }
        const foot=document.getElementById('rightFoot'+i);
        foot.style.display=total>RS?'flex':'none';
        if(total>RS){
            document.getElementById('rpi'+i).textContent=`${Math.min((T[i].rp-1)*RS+1,total)}–${Math.min(T[i].rp*RS,total)} / ${total}`;
            let b=`<button class="pg-btn" onclick="setRP(${i},${T[i].rp-1})" ${T[i].rp<=1?'disabled':''}>‹</button>`;
            for(let p=1;p<=tp;p++){
                if(tp<=6||p===1||p===tp||Math.abs(p-T[i].rp)<=1)b+=`<button class="pg-btn ${p===T[i].rp?'active':''}" onclick="setRP(${i},${p})">${p}</button>`;
                else if(Math.abs(p-T[i].rp)===2)b+=`<span style="padding:0 3px;color:var(--s400)">…</span>`;
            }
            b+=`<button class="pg-btn" onclick="setRP(${i},${T[i].rp+1})" ${T[i].rp>=tp?'disabled':''}>›</button>`;
            document.getElementById('rpg'+i).innerHTML=b;
        }
        const sa=document.getElementById('sa'+i),cc=paged.filter(x=>T[i].selIds.has(x.id)).length;
        sa.checked=paged.length>0&&cc===paged.length;sa.indeterminate=cc>0&&cc<paged.length;
        updBtn(i);
    }
    function setRP(i,p){T[i].rp=p;renderRight(i);}
    function togR(i,id,row){
        T[i].selIds.has(id)?T[i].selIds.delete(id):T[i].selIds.add(id);
        row.classList.toggle('checked',T[i].selIds.has(id));
        const cb=row.querySelector('input[type=checkbox]');if(cb)cb.checked=T[i].selIds.has(id);
        const paged=filtR(i).slice((T[i].rp-1)*RS,T[i].rp*RS);
        const sa=document.getElementById('sa'+i),cc=paged.filter(x=>T[i].selIds.has(x.id)).length;
        sa.checked=paged.length>0&&cc===paged.length;sa.indeterminate=cc>0&&cc<paged.length;
        updBtn(i);
    }
    function toggleAll(i,chk){filtR(i).slice((T[i].rp-1)*RS,T[i].rp*RS).forEach(x=>{chk?T[i].selIds.add(x.id):T[i].selIds.delete(x.id);});renderRight(i);}
    function updBtn(i){document.getElementById('saveBtn'+i).disabled=!(T[i].selId!==null&&T[i].selIds.size>0);}

    async function doSubmit(i){
        const t=T[i];if(t.selId===null||t.selIds.size===0)return;
        const btn=document.getElementById('saveBtn'+i);btn.disabled=true;btn.textContent='Menyimpan...';
        window.showLoader();
        try{
            const r=await fetch(t.endpoint,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF},
                body:JSON.stringify({[t.leftKey]:t.selId,[t.rightKey]:[...t.selIds]})});
            const d=await r.json();
            if(r.ok&&d.success){await Swal.fire({icon:'success',title:'Berhasil',text:d.message,timer:1600,showConfirmButton:false});window.location.href=BACK;}
            else{Swal.fire({icon:'error',title:'Gagal',text:d.message||'Terjadi kesalahan'});btn.disabled=false;btn.textContent='Simpan Pemetaan';}
        }catch(e){Swal.fire({icon:'error',title:'Error',text:e.message});btn.disabled=false;btn.textContent='Simpan Pemetaan';}
        finally { window.hideLoader(); }
    }
    </script>
    @endpush
</x-banksoal::layouts.admin>
