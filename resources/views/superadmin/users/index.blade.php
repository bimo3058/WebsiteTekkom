<x-app-layout>
<x-sidebar :user="auth()->user()">

    {{-- Tambahkan Style Box Wrap khas SITKOM --}}
    <style>
        /* Hilangkan padding default agar wrap bisa full 100vh */
        .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }
        
        /* Container luar */
        .user-wrap { 
            display: flex; flex-direction: column; height: calc(100vh - 60px); 
            padding: 10px; box-sizing: border-box; font-family: 'Inter Tight', sans-serif; 
        }
        
        /* Kotak utama (Box) */
        .user-box { 
            display: flex; flex-direction: column; flex: 1; min-height: 0; 
            background: #fff; border: 1px solid var(--c-border); 
            border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); 
            overflow: hidden; /* Ini memastikan tidak ada konten yang bisa tembus keluar kotak */
            width: 100%;
            box-sizing: border-box;
        }

        /* Area Header Box (Fixed di atas kotak) */
        .user-box-header {
            background: #fff; 
            border-bottom: 1px solid var(--c-border); 
            flex-shrink: 0;
            width: 100%;
            box-sizing: border-box; /* Kunci agar padding tidak menambah lebar kotak */
            padding: 16px 24px; /* Ini kuncinya! Memberi jarak aman agar tombol tidak menabrak garis */
        }

        /* Area Konten Box (Scrollable) */
        .user-box-body {
            flex: 1; overflow-y: auto; padding: 20px 24px; display: flex; flex-direction: column; gap: 16px;
        }
    </style>

    <div class="user-wrap">
        <div class="user-box">
            
            {{-- Area Header (Diam) --}}
            <div class="user-box-header">
                @include('superadmin.users._header', ['total' => $users->total()])
            </div>

            {{-- Area Konten (Bisa di-scroll) --}}
            <div class="user-box-body">
                {{-- Alerts & Progress Bar --}}
                @include('superadmin.users._alerts')

                {{-- Users Table (search/filter inline) --}}
                @include('superadmin.users._table', ['users' => $users, 'roles' => $roles])
            </div>

        </div>
    </div>

    {{-- Modals diletakkan di luar wrap agar overlay-nya menutupi layar penuh --}}
    @include('superadmin.users._modal_import')
    @include('superadmin.users._modal_add', ['roles' => $roles])
    @include('superadmin.users._modal_suspend')
    @include('superadmin.users._modal_delete_hybrid')
    @include('superadmin.users._modal_force_logout')

        </div>
    </div>

    <script>
        const DOSEN_ID     = {{ $roles->first(fn($r) => strtolower($r->name) === 'dosen')?->id ?? 'null' }};
        const MAHASISWA_ID = {{ $roles->first(fn($r) => strtolower($r->name) === 'mahasiswa')?->id ?? 'null' }};

        let importTimer = null;

        // ── Polling ──────────────────────────────────────────────────────────
        function stopPolling() {
            if (importTimer) { clearInterval(importTimer); importTimer = null; }
        }

        function startPolling(importId) {
            if (!importId || importId === 'null' || importId === '') return;
            if (importTimer) clearInterval(importTimer);

            const container = document.getElementById('importProgressContainer');
            if (container) {
                container.setAttribute('data-import-id', importId);
                container.style.display = '';
                container.classList.remove('hidden');
            }

            const bar         = document.getElementById('importProgressBar');
            const text        = document.getElementById('importStatusText');
            const percentText = document.getElementById('importPercentText');

            importTimer = setInterval(async () => {
                try {
                    const response = await fetch(`/superadmin/import-status/${importId}`);
                    const ct = response.headers.get('content-type');
                    if (!ct?.includes('application/json')) { stopPolling(); return; }
                    if (response.status === 401 || response.status === 403) { window.location.href = '/login'; return; }
                    if (!response.ok) { stopPolling(); return; }

                    const data = await response.json();
                    if (!data || typeof data !== 'object') { stopPolling(); return; }

                    const pct = data.total > 0 ? Math.round((data.processed / data.total) * 100) : 0;
                    if (bar)         bar.style.width      = pct + '%';
                    if (percentText) percentText.textContent = pct + '%';

                    if (data.status === 'processing') {
                        if (text) text.textContent = `Memproses: ${data.processed} / ${data.total} user...`;
                    } else if (data.status === 'completed') {
                        stopPolling();
                        if (text) text.innerHTML = '<span style="color:#059669;font-weight:700;">✓ Impor Berhasil Selesai!</span>';
                        if (bar)  { bar.style.width = '100%'; bar.style.background = '#22C55E'; }
                        setTimeout(async () => {
                            await fetch('/superadmin/clear-import-session', { method: 'POST' });
                            await fetch('/superadmin/bust-stats-cache', {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                            });
                            window.location.reload();
                        }, 1500);
                    } else if (data.status === 'failed') {
                        stopPolling();
                        if (text) text.innerHTML = `<span style="color:#DC2626;font-weight:700;">✕ Gagal: ${data.error_message || 'Import gagal'}</span>`;
                        if (bar)  bar.style.background = '#EF4444';
                        setTimeout(async () => {
                            await fetch('/superadmin/clear-import-session', { method: 'POST' });
                            window.location.reload();
                        }, 3000);
                    }
                } catch (e) { console.error('Polling error:', e); stopPolling(); }
            }, 2000);
        }

        async function cancelImport(importId) {
            if (!importId) return;
            try {
                const res = await fetch(`/superadmin/import-status/${importId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) {
                    stopPolling();
                    const bar  = document.getElementById('importProgressBar');
                    const text = document.getElementById('importStatusText');
                    if (bar)  bar.style.background = '#EF4444';
                    if (text) text.innerHTML = '<span style="color:#DC2626;font-weight:700;">Impor dibatalkan</span>';
                    setTimeout(async () => {
                        await fetch('/superadmin/clear-import-session', { method: 'POST' });
                        await fetch('/superadmin/bust-stats-cache', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                        window.location.reload();
                    }, 1500);
                }
            } catch (e) { console.error('Cancel error:', e); }
        }

        // ── Modal helpers ─────────────────────────────────────────────────────
        function openModal(id) {
            const el = document.getElementById(id);
            if (el) { el.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
        }
        function closeModal(id) {
            const el = document.getElementById(id);
            if (el) { el.classList.add('hidden'); document.body.style.overflow = ''; }
        }
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                ['modalAddUser','modalEditInfo','superadminWarningModal','modalSuspend',
                 'modalImportUser','modalDeleteHybrid','modalForceLogout'].forEach(closeModal);
            }
        });

        // ── Init ──────────────────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function () {

            // Cek import aktif
            const container = document.getElementById('importProgressContainer');
            const activeId  = container?.getAttribute('data-import-id') || '';
            if (activeId && activeId !== 'null' && activeId !== '') startPolling(activeId);

            // AJAX import form
            const importForm = document.getElementById('formImportUser');
            if (importForm) {
                let isSubmitting = false;
                importForm.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    if (isSubmitting) return;
                    isSubmitting = true;
                    const btn            = document.getElementById('btnSubmitImport');
                    const errorContainer = document.getElementById('importErrorContainer');
                    const dupeContainer  = document.getElementById('importDuplicateContainer');
                    if (errorContainer) errorContainer.classList.add('hidden');
                    if (dupeContainer)  dupeContainer.classList.add('hidden');
                    btn.disabled  = true;
                    btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:16px;animation:spin 1s linear infinite;">sync</span> Memvalidasi...';
                    try {
                        const res  = await fetch(this.action, {
                            method: 'POST',
                            body: new FormData(this),
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                        });
                        const data = await res.json();
                        if (data.status === 'duplicate') {
                            handleImportDuplicateResponse(data);
                            isSubmitting = false;
                            return;
                        }
                        if (!res.ok) throw new Error(data.message || 'Gagal memproses file.');
                        if (data.import_id) {
                            btn.innerHTML = '✓ Berhasil!';
                            closeModal('modalImportUser');
                            startPolling(data.import_id);
                        } else if (data.status === 'success') {
                            btn.innerHTML = '✓ Berhasil!';
                            window.location.reload();
                        }
                    } catch (err) {
                        const errorMessage = document.getElementById('importErrorMessage');
                        if (errorMessage) errorMessage.textContent = err.message;
                        if (errorContainer) errorContainer.classList.remove('hidden');
                        btn.disabled  = false;
                        btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:16px">upload</span> Mulai Impor';
                        isSubmitting  = false;
                    }
                });
            }

            // Bulk delete form
            const deleteForm = document.getElementById('formDeleteHybrid');
            if (deleteForm) {
                deleteForm.addEventListener('submit', async function (e) {
                    const isBulk = this.querySelectorAll('.bulk-ids-input').length > 0;
                    if (isBulk) {
                        e.preventDefault();
                        const btn = this.querySelector('button[type="submit"]');
                        const orig = btn.innerHTML;
                        btn.disabled = true;
                        btn.innerHTML = 'Memproses...';
                        try {
                            const res = await fetch(this.action, {
                                method: 'POST',
                                body: new FormData(this),
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                            });
                            if (res.ok) window.location.reload();
                            else {
                                const data = await res.json();
                                alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
                                btn.disabled = false;
                                btn.innerHTML = orig;
                            }
                        } catch (err) {
                            alert('Terjadi kesalahan jaringan.');
                            btn.disabled = false;
                            btn.innerHTML = orig;
                        }
                    }
                });
            }

            // Checkbox select all
            const selectAll = document.getElementById('selectAll');
            selectAll?.addEventListener('change', function () {
                document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = this.checked);
                updateBulkBar();
            });
            document.addEventListener('change', e => {
                if (e.target.classList.contains('user-checkbox')) updateBulkBar();
            });

            // Add modal role toggle
            document.querySelectorAll('.add-role-cb').forEach(cb => {
                cb.addEventListener('change', function () {
                    const id = parseInt(this.value);
                    if (id === DOSEN_ID)     document.getElementById('addFieldDosen')?.classList.toggle('hidden', !this.checked);
                    if (id === MAHASISWA_ID) document.getElementById('addFieldMahasiswa')?.classList.toggle('hidden', !this.checked);
                });
            });

            // Superadmin safety
            document.addEventListener('change', e => {
                const cb = e.target.closest('.add-role-cb');
                if (cb && cb.getAttribute('data-role-name') === 'superadmin' && cb.checked) {
                    cb.checked = false;
                    openModal('superadminWarningModal');
                }
            });
        });

        // ── Bulk actions ──────────────────────────────────────────────────────
        function updateBulkBar() {
            const cbs = document.querySelectorAll('.user-checkbox:checked');
            const bar = document.getElementById('bulkActionBar');
            const cnt = document.getElementById('selectedCount');
            if (cnt) cnt.textContent = cbs.length;
            if (cbs.length > 0) { bar.style.display = 'flex'; }
            else { bar.style.display = 'none'; }
        }

        function deselectAll() {
            const selectAll = document.getElementById('selectAll');
            if (selectAll) selectAll.checked = false;
            document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = false);
            updateBulkBar();
        }

        function openBulkDeleteHybrid() {
            const selectedIds = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
            if (!selectedIds.length) return;
            const form = document.getElementById('formDeleteHybrid');
            form.action = '{{ route("superadmin.users.bulk-destroy") }}';
            form.querySelectorAll('.bulk-ids-input').forEach(el => el.remove());
            selectedIds.forEach(id => {
                const inp = document.createElement('input');
                inp.type  = 'hidden';
                inp.name  = 'ids[]';
                inp.value = id;
                inp.classList.add('bulk-ids-input');
                form.appendChild(inp);
            });
            document.getElementById('deleteTargetName').textContent = selectedIds.length + ' user yang dipilih';
            openModal('modalDeleteHybrid');
        }

        // ── Account action helpers ────────────────────────────────────────────
        function openEditInfo(data) {
            window.location.href = `/superadmin/users/${data.id}/edit`;
        }

        function openForceLogoutModal(data) {
            document.getElementById('formForceLogout').action        = `/superadmin/users/${data.id}/force-logout`;
            document.getElementById('logoutTargetName').textContent  = data.name;
            openModal('modalForceLogout');
        }

        function openDeleteHybrid(data) {
            document.getElementById('formDeleteHybrid').action       = `/superadmin/users/${data.id}/destroy`;
            document.getElementById('deleteTargetName').textContent  = data.name;
            openModal('modalDeleteHybrid');
        }

        // Superadmin confirm
        document.getElementById('confirmSuperadminAddText')?.addEventListener('input', function () {
            const btn     = document.getElementById('confirmSuperadminAdd');
            btn.disabled  = this.value.toUpperCase() !== 'SUPERADMIN';
            btn.className = btn.disabled
                ? 'flex-1 bg-slate-200 text-slate-400 cursor-not-allowed text-[10px] font-black uppercase py-3 rounded-xl'
                : 'flex-1 bg-red-600 text-white text-[10px] font-black uppercase py-3 rounded-xl shadow-md';
        });
        document.getElementById('confirmSuperadminAdd')?.addEventListener('click', function () {
            const cb = document.querySelector('.add-role-cb[data-role-name="superadmin"]');
            if (cb) cb.checked = true;
            closeModal('superadminWarningModal');
        });
    </script>
</x-sidebar>
</x-app-layout>