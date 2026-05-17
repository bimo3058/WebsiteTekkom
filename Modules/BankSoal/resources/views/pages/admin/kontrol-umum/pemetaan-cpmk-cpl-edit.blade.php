<x-banksoal::layouts.admin>
    @section('breadcrumbs')
    <a href="{{ route('banksoal.admin.kontrol-umum.pemetaan') }}" class="text-slate-500 hover:text-primary transition-colors">Kontrol Umum</a>
    <span class="mx-2 text-slate-300">/</span>
    <a href="{{ route('banksoal.admin.kontrol-umum.pemetaan') }}" class="text-slate-500 hover:text-primary transition-colors">Pemetaan</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Edit Pemetaan CPMK ke CPL</span>
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
        .pm-page .btn-primary{background:var(--pm);color:#fff}.pm-page .btn-primary:hover{background:var(--pm-hover);transform:translateY(-1px);box-shadow:0 4px 12px rgba(11,38,110,.2)}
        .pm-page .btn-primary:disabled{opacity:.6;cursor:not-allowed;transform:none}
        .pm-page .btn-secondary{background:#fff;color:var(--s700);border:1px solid var(--s300)}.pm-page .btn-secondary:hover{background:var(--s50);border-color:var(--s400)}
        .pm-page .mapping-grid{display:grid;grid-template-columns:320px 1fr;gap:20px;align-items:start}
        @media(max-width:900px){.pm-page .mapping-grid{grid-template-columns:1fr}}
        .pm-page .panel{background:#fff;border:1px solid var(--s200);border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.05)}
        .pm-page .panel-header{padding:16px 18px;border-bottom:1px solid var(--s200);background:var(--s50)}
        .pm-page .panel-header h2{font-size:13px;font-weight:700;color:var(--s800);margin:0;text-transform:uppercase;letter-spacing:.4px}
        .pm-page .panel-header p{font-size:12px;color:var(--s500);margin:4px 0 0}
        /* Left panel locked */
        .pm-page .panel-locked{padding:16px 18px}
        .pm-page .locked-item{display:flex;align-items:center;gap:10px;padding:12px 16px;background:var(--pm);border-radius:8px;color:#fff;font-weight:700;font-size:14px}
        .pm-page .locked-item svg{flex-shrink:0}
        .pm-page .lock-note{margin-top:10px;font-size:12px;color:var(--s400);display:flex;align-items:center;gap:5px}
        /* Right panel */
        .pm-page .panel-search{padding:12px 14px;border-bottom:1px solid var(--s200)}
        .pm-page .srch{position:relative}.pm-page .srch svg{position:absolute;left:10px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:var(--s400)}
        .pm-page .srch input{width:100%;padding:9px 12px 9px 32px;border:1px solid var(--s300);border-radius:8px;font-size:13px;color:var(--s800);background:#fff;transition:border-color .2s}
        .pm-page .srch input:focus{outline:none;border-color:var(--pm);box-shadow:0 0 0 3px rgba(59,130,246,.1)}
        .pm-page .check-list{max-height:420px;overflow-y:auto}
        .pm-page .check-item{display:flex;align-items:center;gap:12px;padding:10px 16px;border-bottom:1px solid var(--s100);cursor:pointer;transition:background .15s}
        .pm-page .check-item:last-child{border-bottom:none}.pm-page .check-item:hover{background:var(--s50)}
        .pm-page .check-item input[type=checkbox]{width:16px;height:16px;accent-color:var(--pm);cursor:pointer;flex-shrink:0}
        .pm-page .check-item.checked{background:rgba(11,38,110,.04)}.pm-page .check-item.checked span{color:var(--pm) !important}
        .pm-page .r-empty{padding:40px 16px;text-align:center;font-size:13px;color:var(--s400)}
        .pm-page .panel-footer{padding:10px 14px;border-top:1px solid var(--s200);background:var(--s50);display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap}
        .pm-page .pg-info{font-size:12px;color:var(--s500)}
        .pm-page .pagination{display:flex;gap:4px;align-items:center}
        .pm-page .pg-btn{min-width:30px;height:30px;padding:0 8px;border:1px solid var(--s200);background:#fff;color:var(--s700);border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;transition:all .15s}
        .pm-page .pg-btn:hover:not(:disabled){border-color:var(--pm);color:var(--pm)}.pm-page .pg-btn.active{background:var(--pm);border-color:var(--pm);color:#fff}.pm-page .pg-btn:disabled{opacity:.4;cursor:not-allowed}
        .pm-page .sa-row{display:flex;align-items:center;gap:8px;padding:8px 16px;border-bottom:1px solid var(--s200);background:var(--s50)}
        .pm-page .sa-row input{width:16px;height:16px;accent-color:var(--pm);cursor:pointer}
        .pm-page .sa-row label{font-size:12px;font-weight:600;color:var(--s500);cursor:pointer}
        .pm-page .form-actions{display:flex;justify-content:flex-end;gap:12px;margin-top:24px}
        .pm-loader{position:fixed;inset:0;background:rgba(255,255,255,.7);display:none;align-items:center;justify-content:center;z-index:50}
        .pm-loader.show{display:flex}
        .pm-spinner{width:36px;height:36px;border:3px solid #e2e8f0;border-top-color:rgb(11,38,110);border-radius:50%;animation:pm-spin .7s linear infinite}
        @keyframes pm-spin{to{transform:rotate(360deg)}}
    </style>
    @endpush

    <div class="pm-loader" id="loaderOverlay"><div class="pm-spinner"></div></div>

    <div class="pm-page">
    <div class="page-header">
        <div>
            <h1>Edit Pemetaan CPMK ke CPL</h1>
            <p>CPL sudah terkunci. Ubah centang CPMK yang dipetakan ke CPL ini.</p>
        </div>
        <a href="{{ route('banksoal.admin.kontrol-umum.pemetaan') }}" class="btn btn-secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>

    <div class="mapping-grid">
        {{-- LEFT: CPL Terkunci --}}
        <div class="panel" id="panelLeft">
            <div class="panel-header">
                <h2>CPL Terpilih</h2>
                <p>CPL tidak dapat diubah di sini</p>
            </div>
            <div class="panel-locked">
                <div class="locked-item">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    {{ $cpl->kode }}
                </div>
                <div class="lock-note">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    CPL dikunci saat mode edit
                </div>
            </div>
        </div>

        {{-- RIGHT: Pilih CPMK --}}
        <div class="panel" id="panelRight">
            <div class="panel-header">
                <h2>Pilih CPMK</h2>
                <p>Centang satu atau lebih CPMK</p>
            </div>
            <div class="panel-search">
                <div class="srch">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" id="rightSearch" placeholder="Cari CPMK..." oninput="onRightSearch(this.value)">
                </div>
            </div>
            <div class="sa-row">
                <input type="checkbox" id="selectAllRight" onchange="toggleSelectAll(this.checked)">
                <label for="selectAllRight">Pilih semua yang tampil</label>
            </div>
            <div class="check-list" id="rightItems"></div>
            <div class="r-empty" id="rightEmpty" style="display:none;">Tidak ada data CPMK</div>
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

    </div>{{-- end .pm-page --}}

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
    <script>
    const CSRF     = '{{ csrf_token() }}';
    const BASE_API = '{{ url("/bank-soal/admin/api") }}';
    const BACK_URL = '{{ route("banksoal.admin.kontrol-umum.pemetaan") }}';
    const CPL_ID   = {{ $cpl->id }};
    const RIGHT_SIZE = 10;

    const s = {
        cpmk: [],
        selectedRightIds: new Set({{ json_encode($selectedIds) }}),
        rightSearch: '', rightPage: 1,
    };

    document.addEventListener('DOMContentLoaded', async () => {
        document.getElementById('loaderOverlay').classList.add('show');
        try {
            const r = await fetch(BASE_API + '/pemetaan/options', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } });
            const d = await r.json();
            if (d.success) s.cpmk = d.data.cpmk.sort((a,b) => a.kode.localeCompare(b.kode, undefined, {numeric:true}));
        } catch (e) { console.error(e); }
        document.getElementById('loaderOverlay').classList.remove('show');
        renderRight();
    });

    function onRightSearch(v) { s.rightSearch = v; s.rightPage = 1; renderRight(); }

    function filteredRight() {
        const q = s.rightSearch.toLowerCase();
        return s.cpmk.filter(c => c.kode.toLowerCase().includes(q));
    }

    function renderRight() {
        const all = filteredRight();
        const total = all.length;
        const totalPages = Math.max(1, Math.ceil(total / RIGHT_SIZE));
        if (s.rightPage > totalPages) s.rightPage = 1;
        const paged = all.slice((s.rightPage - 1) * RIGHT_SIZE, s.rightPage * RIGHT_SIZE);

        const container = document.getElementById('rightItems');
        const empty = document.getElementById('rightEmpty');

        if (!paged.length) {
            container.innerHTML = '';
            empty.style.display = 'block';
        } else {
            empty.style.display = 'none';
            const rows = paged.map(c => {
                const chk = s.selectedRightIds.has(c.id);
                return `<label class="check-item ${chk?'checked':''}" onclick="event.preventDefault(); toggleRight(${c.id}, this)">
                    <input type="checkbox" ${chk?'checked':''} onchange="toggleRight(${c.id}, this.closest('.check-item'))">
                    <span style="font-size:13px;font-weight:600;color:#334155">${c.kode}</span>
                </label>`;
            }).join('');
            const ghosts = '<div class="check-item" style="visibility:hidden;pointer-events:none"><input type="checkbox" disabled><span>&nbsp;</span></div>'
                .repeat(Math.max(0, RIGHT_SIZE - paged.length));
            container.innerHTML = rows + ghosts;
        }

        renderRightPagination(total, totalPages);
        updateSelectAllState(paged);
    }

    function toggleRight(id, row) {
        if (s.selectedRightIds.has(id)) { s.selectedRightIds.delete(id); row.classList.remove('checked'); }
        else { s.selectedRightIds.add(id); row.classList.add('checked'); }
        const cb = row.querySelector('input[type="checkbox"]');
        if (cb) cb.checked = s.selectedRightIds.has(id);
        updateSelectAllState(filteredRight().slice((s.rightPage-1)*RIGHT_SIZE, s.rightPage*RIGHT_SIZE));
    }

    function toggleSelectAll(checked) {
        const paged = filteredRight().slice((s.rightPage-1)*RIGHT_SIZE, s.rightPage*RIGHT_SIZE);
        paged.forEach(c => { if (checked) s.selectedRightIds.add(c.id); else s.selectedRightIds.delete(c.id); });
        renderRight();
    }

    function updateSelectAllState(paged) {
        const saEl = document.getElementById('selectAllRight');
        if (!paged.length) { saEl.checked = false; saEl.indeterminate = false; return; }
        const checkedCount = paged.filter(c => s.selectedRightIds.has(c.id)).length;
        saEl.checked = checkedCount === paged.length;
        saEl.indeterminate = checkedCount > 0 && checkedCount < paged.length;
    }

    function renderRightPagination(total, totalPages) {
        const footer = document.getElementById('rightFooter');
        footer.style.display = total > RIGHT_SIZE ? 'flex' : 'none';
        document.getElementById('rightPageInfo').textContent = `${Math.min((s.rightPage-1)*RIGHT_SIZE+1, total)}–${Math.min(s.rightPage*RIGHT_SIZE, total)} dari ${total}`;
        const pg = document.getElementById('rightPagination');
        let btns = `<button class="pg-btn" onclick="setRightPage(${s.rightPage-1})" ${s.rightPage<=1?'disabled':''}>‹</button>`;
        for (let i = 1; i <= totalPages; i++) {
            if (totalPages <= 7 || i === 1 || i === totalPages || Math.abs(i - s.rightPage) <= 1) {
                btns += `<button class="pg-btn ${i===s.rightPage?'active':''}" onclick="setRightPage(${i})">${i}</button>`;
            } else if (Math.abs(i - s.rightPage) === 2) { btns += `<span style="padding:0 4px;color:#94a3b8">…</span>`; }
        }
        btns += `<button class="pg-btn" onclick="setRightPage(${s.rightPage+1})" ${s.rightPage>=totalPages?'disabled':''}>›</button>`;
        pg.innerHTML = btns;
    }

    function setRightPage(p) { s.rightPage = p; renderRight(); }

    async function submitMapping() {
        const btn = document.getElementById('btnSimpan');
        btn.disabled = true;
        btn.innerHTML = '<div class="pm-spinner" style="width:16px;height:16px;border-width:2px;"></div> Menyimpan...';

        try {
            const r = await fetch(BASE_API + '/pemetaan/cpmk-cpl/sync', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ cpl_id: CPL_ID, cpmk_ids: [...s.selectedRightIds] })
            });
            const d = await r.json();
            if (r.ok && d.success) {
                await Swal.fire({ icon: 'success', title: 'Berhasil', text: d.message, timer: 1600, showConfirmButton: false });
                window.location.href = BACK_URL;
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: d.message || 'Terjadi kesalahan' });
                btn.disabled = false;
                btn.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Simpan Perubahan';
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Error', text: e.message });
            btn.disabled = false;
            btn.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Simpan Perubahan';
        }
    }
    </script>
    @endpush
</x-banksoal::layouts.admin>
