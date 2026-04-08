<x-app-layout>
<x-sidebar :user="auth()->user()">
    <div class="min-h-screen bg-slate-50 p-8">
        <div class="max-w-full mx-auto">

            {{-- Header --}}
            @include('superadmin.users._header', ['total' => $users->total()])

            {{-- Alerts & Progress Bar --}}
            @include('superadmin.users._alerts')

            {{-- Search & Filter --}}
            @include('superadmin.users._search_filter', ['roles' => $roles])

            {{-- Users Table --}}
            @include('superadmin.users._table', ['users' => $users, 'roles' => $roles])

            {{-- Pagination --}}
            @include('superadmin.users._pagination', ['users' => $users])

            {{-- Modals --}}
            @include('superadmin.users._modal_import')
            @include('superadmin.users._modal_add', ['roles' => $roles])
            @include('superadmin.users._modal_edit_info')
            @include('superadmin.users._modal_suspend')
            @include('superadmin.users._modal_delete_hybrid')
            @include('superadmin.users._modal_force_logout')

        </div>
    </div>

    <script>
        // ============================================
        // VARIABLES & CONSTANTS
        // ============================================
        const DOSEN_ID = {{ $roles->first(fn($r) => strtolower($r->name) === 'dosen')?->id ?? 'null' }};
        const MAHASISWA_ID = {{ $roles->first(fn($r) => strtolower($r->name) === 'mahasiswa')?->id ?? 'null' }};
        
        let importTimer = null;
        
        
        // Polling untuk Import Progress
        function stopPolling() {
            if (importTimer) {
                clearInterval(importTimer);
                importTimer = null;
            }
        }

        function startPolling(importId) {
            if (!importId || importId === "null" || importId === "") return;

            if (importTimer) clearInterval(importTimer);

            const bar = document.getElementById('importProgressBar');
            const text = document.getElementById('importStatusText');
            const percentText = document.getElementById('importPercentText');
            const container = document.getElementById('importProgressContainer');

            if (container) {
                container.classList.remove('hidden');
                container.setAttribute('data-import-id', importId);
            }

            importTimer = setInterval(async () => {
                try {
                    const response = await fetch(`/superadmin/import-status/${importId}`);
                    
                    // ✅ Cek jika response bukan JSON (misal redirect ke login)
                    const contentType = response.headers.get("content-type");
                    if (!contentType || !contentType.includes("application/json")) {
                        console.log("Response bukan JSON, menghentikan polling");
                        stopPolling();
                        // Hapus container progress
                        if (container) container.remove();
                        return;
                    }
                    
                    if (!response.ok) {
                        if (response.status === 401 || response.status === 403) {
                            // Session expired, redirect ke login
                            window.location.href = '/login';
                            return;
                        }
                        stopPolling();
                        return;
                    }
                    
                    const data = await response.json();
                    
                    // ✅ Validasi data yang diterima
                    if (!data || typeof data !== 'object') {
                        console.error("Invalid data received");
                        stopPolling();
                        if (container) container.remove();
                        return;
                    }
                    
                    const percentage = data.total > 0 ? Math.round((data.processed / data.total) * 100) : 0;

                    if (bar) bar.style.width = percentage + '%';
                    if (percentText) percentText.textContent = percentage + '%';

                    if (data.status === 'processing') {
                        if (text) text.textContent = `Memproses: ${data.processed} / ${data.total} user...`;
                    } 
                    else if (data.status === 'completed') {
                        stopPolling();
                        if (text) text.innerHTML = '<span class="text-emerald-600 font-bold">Impor Berhasil Selesai!</span>';
                        if (bar) {
                            bar.style.width = '100%';
                            bar.className = "h-full bg-emerald-500 transition-all duration-500";
                        }
                        setTimeout(() => {
                            // Hapus session import_id
                            fetch('/superadmin/clear-import-session', { method: 'POST' });
                            window.location.reload();
                        }, 2000);
                    } 
                    else if (data.status === 'failed') {
                        stopPolling();
                        if (text) {
                            const errorMsg = data.error_message || data.filename || 'Import gagal';
                            text.innerHTML = `<span class="text-red-600 font-bold">❌ Gagal: ${errorMsg}</span>`;
                        }
                        if (bar) bar.className = "h-full bg-red-500 transition-all duration-500";
                        setTimeout(() => {
                            fetch('/superadmin/clear-import-session', { method: 'POST' });
                            window.location.reload();
                        }, 3000);
                    }
                } catch (e) {
                    console.error("Polling error:", e);
                    stopPolling();
                    if (container) container.remove();
                }
            }, 2000);
        }

        // ============================================
        // CANCEL IMPORT FUNCTION
        // ============================================
        async function cancelImport(importId) {
            if (!importId) return;
            
            if (!confirm('Batalkan proses impor? Data yang sudah diproses akan tetap tersimpan.')) {
                return;
            }
            
            try {
                const response = await fetch(`/superadmin/import-status/${importId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    // Hentikan polling
                    if (importTimer) {
                        clearInterval(importTimer);
                        importTimer = null;
                    }
                    
                    // Update tampilan progress bar
                    const bar = document.getElementById('importProgressBar');
                    const text = document.getElementById('importStatusText');
                    
                    if (bar) bar.className = "h-full bg-red-500 transition-all duration-500";
                    if (text) text.innerHTML = '<span class="text-red-600 font-bold">Impor dibatalkan</span>';
                    
                    // Hapus session import_id
                    await fetch('/superadmin/clear-import-session', { method: 'POST' });
                    
                    // Reload setelah 2 detik
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    const error = await response.json();
                    alert('Gagal membatalkan: ' + (error.message || 'Unknown error'));
                }
            } catch (e) {
                console.error("Error cancelling import:", e);
                alert('Gagal membatalkan impor. Silakan refresh halaman.');
            }
        }
        // ============================================
        // 2. MODAL CORE FUNCTIONS
        // ============================================
        function openModal(id) { 
            const modal = document.getElementById(id);
            if(modal) {
                modal.classList.remove('hidden'); 
                document.body.style.overflow = 'hidden'; 
            }
        }
        
        function closeModal(id) { 
            const modal = document.getElementById(id);
            if(modal) {
                modal.classList.add('hidden'); 
                document.body.style.overflow = ''; 
            }
        }
        
        document.addEventListener('keydown', e => { 
            if (e.key === 'Escape') {
                ['modalAddUser', 'modalEditRoles', 'superadminWarningModal', 'modalEditInfo', 'modalSuspend', 'modalImportUser', 'modalDeleteHybrid', 'modalForceLogout'].forEach(closeModal); 
            }
        });

        // ============================================
        // 3. INITIALIZATION & AJAX SUBMIT
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            
            // Cek Impor Aktif (Persistent)
            const container = document.getElementById('importProgressContainer');
            const activeId = container ? container.getAttribute('data-import-id') : "{{ session('import_id') }}";
            if (activeId && activeId !== "null" && activeId !== "") startPolling(activeId);

            // AJAX Form Import
            const importForm = document.getElementById('formImportUser');
                if (importForm) {
                importForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const btn = document.getElementById('btnSubmitImport');
                    const errorContainer = document.getElementById('importErrorContainer');
                    const errorMessage = document.getElementById('importErrorMessage');
                    const formData = new FormData(this);

                    // 1. Reset tampilan error sebelum mulai
                    if (errorContainer) errorContainer.classList.add('hidden');
                    
                    // 2. Beri indikasi loading pada tombol
                    btn.disabled = true;
                    btn.innerHTML = '<span class="animate-spin material-symbols-outlined" style="font-size:18px">sync</span> Memvalidasi...';

                    try {
                        const res = await fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await res.json();

                        // ==========================================
                        // ✅ JIKA ERROR: Jangan Refresh, Munculkan di Modal
                        // ==========================================
                        if (!res.ok) {
                            throw new Error(data.message || "Gagal memproses file.");
                        }

                        // ==========================================
                        // ✅ JIKA SUKSES: Langsung Refresh Halaman
                        // ==========================================
                        if (data.import_id || data.status === 'success') {
                            // Feedback visual sekejap
                            btn.innerHTML = '<span class="material-symbols-outlined">check_circle</span> Berhasil!';
                            
                            // Refresh agar Session Laravel terbaca & Progress Bar muncul otomatis
                            window.location.reload();
                        }

                    } catch (err) {
                        // ==========================================
                        // Tampilkan Error tanpa menutup Modal
                        // ==========================================
                        if (errorMessage) errorMessage.textContent = err.message;
                        if (errorContainer) errorContainer.classList.remove('hidden');
                        
                        // Kembalikan tombol ke kondisi awal agar bisa diperbaiki
                        btn.disabled = false;
                        btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:20px">upload</span> Mulai Impor';
                    }
                });
            }

            const deleteForm = document.getElementById('formDeleteHybrid');
            if (deleteForm) {
                deleteForm.addEventListener('submit', async function(e) {
                    // Kita deteksi apakah ini Bulk Delete dengan mengecek input ids[]
                    const isBulk = this.querySelectorAll('.bulk-ids-input').length > 0;
                    
                    if (isBulk) {
                        e.preventDefault(); 
                        
                        const btn = this.querySelector('button[type="submit"]');
                        const originalContent = btn.innerHTML;
                        
                        btn.disabled = true;
                        btn.innerHTML = '<span class="animate-spin material-symbols-outlined" style="font-size:14px">sync</span> Memproses...';

                        try {
                            const response = await fetch(this.action, {
                                method: 'POST',
                                body: new FormData(this),
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            });

                            if (response.ok) {
                                // Jika berhasil, reload agar tabel sinkron
                                window.location.reload();
                            } else {
                                const data = await response.json();
                                alert('Gagal menghapus: ' + (data.message || 'Terjadi kesalahan'));
                                btn.disabled = false;
                                btn.innerHTML = originalContent;
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan jaringan.');
                            btn.disabled = false;
                            btn.innerHTML = originalContent;
                        }
                    }
                    // Jika bukan bulk (penghapusan satu user), biarkan submit normal (HTML default)
                    // karena Controller biasanya me-return redirect() yang otomatis me-refresh page.
                });
            }

            // --- Checkbox & Selection Logic ---
            const selectAll = document.getElementById('selectAll');
            selectAll?.addEventListener('change', function() {
                document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = this.checked);
                updateBulkBar();
            });

            document.addEventListener('change', e => {
                if (e.target.classList.contains('user-checkbox')) updateBulkBar();
            });

            // Add Modal Role Toggle
            document.querySelectorAll('.add-role-cb').forEach(cb => {
                cb.addEventListener('change', function() {
                    const id = parseInt(this.value);
                    if (id === DOSEN_ID) document.getElementById('addFieldDosen')?.classList.toggle('hidden', !this.checked);
                    if (id === MAHASISWA_ID) document.getElementById('addFieldMahasiswa')?.classList.toggle('hidden', !this.checked);
                });
            });

            // Superadmin Safety Trigger
            document.addEventListener('change', e => {
                const cb = e.target.closest('.add-role-cb');
                if (cb && cb.getAttribute('data-role-name') === 'superadmin' && cb.checked) {
                    cb.checked = false;
                    openModal('superadminWarningModal');
                }
            });
        });
    
        function openBulkDeleteHybrid() {
            const selectedIds = Array.from(document.querySelectorAll('.user-checkbox:checked'))
                                    .map(cb => cb.value);

            if (selectedIds.length === 0) return;

            const form = document.getElementById('formDeleteHybrid');
            
            // Pastikan method spoofing Laravel tetap DELETE
            let methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
            }

            form.action = "{{ route('superadmin.users.bulk-destroy') }}";
            
            // Hapus input ID lama
            form.querySelectorAll('.bulk-ids-input').forEach(el => el.remove());
            
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]'; // Pastikan sesuai dengan $request->ids di Controller
                input.value = id;
                input.classList.add('bulk-ids-input');
                form.appendChild(input);
            });

            document.getElementById('deleteTargetName').textContent = selectedIds.length + " user yang dipilih";
            openModal('modalDeleteHybrid');
        }

        // Tambahkan juga fungsi deselectAll agar tombol Batal di bulk bar berfungsi
        function deselectAll() {
            const selectAll = document.getElementById('selectAll');
            if (selectAll) selectAll.checked = false;
            
            document.querySelectorAll('.user-checkbox').forEach(cb => {
                cb.checked = false;
            });
            
            updateBulkBar();
        }

        // ============================================
        // 4. ACCOUNT ACTIONS (EDIT, DELETE, ETC)
        // ============================================
        function updateBulkBar() {
            const checkboxes = document.querySelectorAll('.user-checkbox:checked');
            const bulkBar = document.getElementById('bulkActionBar');
            const selectedCountText = document.getElementById('selectedCount');
            if (selectedCountText) selectedCountText.textContent = checkboxes.length;
            if (checkboxes.length > 0) bulkBar?.classList.replace('hidden', 'flex');
            else bulkBar?.classList.replace('flex', 'hidden');
        }

        function openEditInfo(data) {
            document.getElementById('formEditInfo').action = `/superadmin/users/${data.id}/update`;
            document.getElementById('editInfoName').value = data.name;
            document.getElementById('editInfoEmail').value = data.email;
            openModal('modalEditInfo');
        }

        function openForceLogoutModal(data) {
            document.getElementById('formForceLogout').action = `/superadmin/users/${data.id}/force-logout`;
            document.getElementById('logoutTargetName').textContent = data.name;
            openModal('modalForceLogout');
        }

        function openDeleteHybrid(data) {
            document.getElementById('formDeleteHybrid').action = `/superadmin/users/${data.id}/destroy`;
            document.getElementById('deleteTargetName').textContent = data.name;
            openModal('modalDeleteHybrid');
        }

        // Superadmin Double Confirmation
        document.getElementById('confirmSuperadminAddText')?.addEventListener('input', function() {
            const btn = document.getElementById('confirmSuperadminAdd');
            btn.disabled = this.value.toUpperCase() !== 'SUPERADMIN';
            btn.className = btn.disabled 
                ? "flex-1 bg-slate-200 text-slate-400 cursor-not-allowed text-[10px] font-black uppercase py-3 rounded-xl"
                : "flex-1 bg-red-600 text-white text-[10px] font-black uppercase py-3 rounded-xl shadow-md";
        });

        document.getElementById('confirmSuperadminAdd')?.addEventListener('click', function() {
            const cb = document.querySelector('.add-role-cb[data-role-name="superadmin"]');
            if (cb) cb.checked = true;
            closeModal('superadminWarningModal');
        });
    </script>
</x-sidebar>
</x-app-layout>