<x-app-layout>
<x-sidebar :user="auth()->user()">
    <div class="min-h-screen bg-slate-50 p-8">
        <div class="max-w-7xl mx-auto">

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

        </div>
    </div>

    <script>
        // ============================================
        // VARIABLES & CONSTANTS
        // ============================================
        const DOSEN_ID = {{ $roles->first(fn($r) => strtolower($r->name) === 'dosen')?->id ?? 'null' }};
        const MAHASISWA_ID = {{ $roles->first(fn($r) => strtolower($r->name) === 'mahasiswa')?->id ?? 'null' }};
        const IMPORT_ID = "{{ session('import_id') }}";
        
        let pendingAddSubmit = null;
        let pendingEditSubmit = null;

        // ============================================
        // BULK IMPORT PROGRESS POLLING & CANCEL
        // ============================================
        function initImportPolling() {
            // Ambil ID dari session Laravel atau dari atribut data kontainer
            const container = document.getElementById('importProgressContainer');
            const importId = container ? container.getAttribute('data-import-id') : "{{ session('import_id') }}";
            
            if (!importId || importId === "") return;

            const bar = document.getElementById('importProgressBar');
            const text = document.getElementById('importStatusText');
            const percentText = document.getElementById('importPercentText');
            const btnCancel = document.getElementById('btnCancelImport');

            let timer = setInterval(async () => {
                try {
                    const response = await fetch(`/superadmin/import-status/${importId}`);
                    if (!response.ok) {
                        // Jika 404 atau error, hentikan polling
                        clearInterval(timer);
                        return;
                    }
                    
                    const data = await response.json();
                    const percentage = data.total > 0 ? Math.round((data.processed / data.total) * 100) : 0;

                    if (bar) bar.style.width = percentage + '%';
                    if (percentText) percentText.textContent = percentage + '%';
                    
                    if (data.status === 'processing') {
                        if (text) text.textContent = `Memproses: ${data.processed} / ${data.total} user...`;
                    } else if (data.status === 'completed') {
                        clearInterval(timer);
                        if (text) text.textContent = '✅ Impor Berhasil Selesai!';
                        if (bar) bar.classList.replace('bg-blue-600', 'bg-emerald-500');
                        if (btnCancel) btnCancel.classList.add('hidden');
                        
                        // Beri waktu admin melihat 100% sebelum hilang saat refresh berikutnya
                        setTimeout(() => {
                            if(container) container.classList.add('fade-out');
                            window.location.reload(); 
                        }, 2000);
                    } else if (data.status === 'failed') {
                        clearInterval(timer);
                        if (text) text.textContent = '❌ Impor Dibatalkan/Gagal.';
                        if (bar) bar.classList.replace('bg-blue-600', 'bg-red-500');
                        if (btnCancel) btnCancel.classList.add('hidden');
                    }
                } catch (e) {
                    console.error("Polling error:", e);
                }
            }, 2000);
        }

        async function cancelImport(id) {
            if (!confirm('Batalkan proses impor? Data yang sudah masuk tidak akan dihapus.')) return;
            const btn = document.getElementById('btnCancelImport');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="text-[9px] font-bold">Membatalkan...</span>';
            }
            try {
                await fetch(`/superadmin/import-status/${id}/cancel`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });
            } catch (error) {
                alert('Gagal mengirim perintah pembatalan.');
            }
        }

        // ============================================
        // MODAL CORE FUNCTIONS
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
        
        // Modal Closers
        document.addEventListener('keydown', e => { 
            if (e.key === 'Escape') {
                ['modalAddUser', 'modalEditRoles', 'superadminWarningModal', 'superadminWarningModalEdit', 'modalEditInfo', 'modalSuspend', 'modalImportUser', 'modalDeleteHybrid'].forEach(closeModal); 
            }
        });

        ['modalAddUser', 'modalEditRoles', 'superadminWarningModal', 'superadminWarningModalEdit', 'modalEditInfo', 'modalSuspend', 'modalImportUser', 'modalDeleteHybrid'].forEach(id => {
            document.getElementById(id)?.addEventListener('click', function(e) { 
                if (e.target === this) closeModal(id); 
            });
        });

        // ============================================
        // DELETE & HYBRID FUNCTIONS
        // ============================================
        function openDeleteHybrid(data) {
            const form = document.getElementById('formDeleteHybrid');
            if (form) form.action = `/superadmin/users/${data.id}/destroy`;
            
            const nameEl = document.getElementById('deleteTargetName');
            if (nameEl) nameEl.textContent = data.name;
            
            openModal('modalDeleteHybrid');
        }

        // ============================================
        // BULK DELETE & SELECTION LOGIC
        // ============================================
        const selectAll = document.getElementById('selectAll');
        const userCheckboxes = document.querySelectorAll('.user-checkbox');
        const bulkBar = document.getElementById('bulkActionBar');
        const selectedCountText = document.getElementById('selectedCount');

        function updateBulkBar() {
            const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
            selectedCountText.textContent = checkedCount;
            
            if (checkedCount > 0) {
                bulkBar.classList.remove('hidden');
                bulkBar.classList.add('flex');
            } else {
                bulkBar.classList.add('hidden');
                bulkBar.classList.remove('flex');
            }
        }

        selectAll?.addEventListener('change', function() {
            userCheckboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            updateBulkBar();
        });

        userCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkBar);
        });

        function deselectAll() {
            selectAll.checked = false;
            userCheckboxes.forEach(cb => cb.checked = false);
            updateBulkBar();
        }

        function openBulkDeleteHybrid() {
            const selectedIds = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
            const form = document.getElementById('formDeleteHybrid');
            
            // Reset form action untuk bulk
            form.action = `{{ route('superadmin.users.bulk-destroy') }}`;
            
            // Hapus input hidden lama jika ada
            form.querySelectorAll('.bulk-ids-input').forEach(el => el.remove());
            
            // Tambahkan ID sebagai hidden input
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                input.classList.add('bulk-ids-input');
                form.appendChild(input);
            });

            document.getElementById('deleteTargetName').textContent = selectedIds.length + " user yang dipilih";
            openModal('modalDeleteHybrid');
        }

        // ============================================
        // EDIT FUNCTIONS & LOGIC
        // ============================================
        let _ctx = {};
        
        function openEditRoles(data) {
            _ctx = data;
            const form = document.getElementById('formEditRoles');
            if (form) form.action = `/superadmin/users/${data.id}/roles`;
            document.getElementById('editRolesUserName').textContent = data.name;
            document.querySelectorAll('.edit-role-cb').forEach(cb => {
                cb.checked = data.role_ids.includes(parseInt(cb.value));
                onEditRoleChange(cb);
            });
            setupEditSuperadminWarning();
            openModal('modalEditRoles');
        }

        function onEditRoleChange(cb) {
            const id = parseInt(cb.value);
            if (id === DOSEN_ID) {
                const field = document.getElementById('editFieldDosen');
                const input = document.getElementById('editEmpNumber');
                if (field) field.classList.toggle('hidden', !cb.checked);
                if (input) {
                    input.value = (cb.checked && _ctx.has_lecturer) ? (_ctx.employee_number || '') : '';
                    input.disabled = (cb.checked && _ctx.has_lecturer);
                }
            }
            if (id === MAHASISWA_ID) {
                const field = document.getElementById('editFieldMahasiswa');
                const nimIn = document.getElementById('editStudentNumber');
                const thnIn = document.getElementById('editCohortYear');
                if (field) field.classList.toggle('hidden', !cb.checked);
                if (nimIn && thnIn) {
                    if (cb.checked && _ctx.has_student) {
                        nimIn.value = _ctx.student_number || '';
                        thnIn.value = _ctx.cohort_year || '';
                        nimIn.disabled = thnIn.disabled = true;
                    } else {
                        nimIn.value = '';
                        thnIn.value = new Date().getFullYear();
                        nimIn.disabled = thnIn.disabled = false;
                    }
                }
            }
        }

        // ============================================
        // UTILITY: PAGINATION & FILTERS
        // ============================================
        function submitFilterForm(page) {
            const form = document.querySelector('form[method="GET"]');
            if (form) {
                let input = form.querySelector('input[name="page"]');
                if (!input) {
                    input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'page';
                    form.appendChild(input);
                }
                input.value = page;
                form.submit();
            }
        }

        document.addEventListener('click', e => {
            const link = e.target.closest('#paginationWrapper a');
            if (!link) return;
            e.preventDefault();
            submitFilterForm(new URL(link.href).searchParams.get('page') || 1);
        });

        ['select[name="per_page"]', 'select[name="role"]'].forEach(sel => {
            document.querySelector(sel)?.addEventListener('change', () => submitFilterForm(1));
        });

        // ============================================
        // INITIALIZATION
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            initImportPolling();
            
            // Logic Role Checkboxes (Add Modal)
            document.querySelectorAll('.add-role-cb').forEach(cb => {
                cb.addEventListener('change', function() {
                    const id = parseInt(this.value);
                    if (id === DOSEN_ID) document.getElementById('addFieldDosen')?.classList.toggle('hidden', !this.checked);
                    if (id === MAHASISWA_ID) document.getElementById('addFieldMahasiswa')?.classList.toggle('hidden', !this.checked);
                });
                cb.dispatchEvent(new Event('change'));
            });

            // Admin Module Logic
            const trigger = document.getElementById('logicTriggerAdmin');
            const panel = document.getElementById('adminModulePanel');
            if (trigger && panel) {
                trigger.addEventListener('change', function() {
                    panel.classList.toggle('hidden', !this.checked);
                });
            }
        });

        function openEditInfo(data) {
            document.getElementById('formEditInfo').action = `/superadmin/users/${data.id}/update`;
            document.getElementById('editInfoName').value = data.name;
            document.getElementById('editInfoEmail').value = data.email;
            openModal('modalEditInfo');
        }

        function openSuspendModal(data) {
            document.getElementById('formSuspend').action = `/superadmin/users/${data.id}/suspend`;
            document.getElementById('suspendUserName').textContent = data.name;
            openModal('modalSuspend');
        }

        // ============================================
        // SUPERADMIN SAFETY LOGIC (ADD MODAL)
        // ============================================

        // 1. Deteksi klik pada checkbox Superadmin
        document.addEventListener('change', function(e) {
            const cb = e.target.closest('.add-role-cb');
            if (!cb) return;

            const roleName = cb.getAttribute('data-role-name');

            if (roleName === 'superadmin' && cb.checked) {
                // BATALKAN centang sementara sampai dikonfirmasi di modal kedua
                cb.checked = false;
                
                // Munculkan modal peringatan
                const warningModal = document.getElementById('superadminWarningModal');
                if (warningModal) {
                    warningModal.classList.remove('hidden');
                    warningModal.classList.add('flex'); // Paksa flex agar ke tengah
                    document.body.style.overflow = 'hidden';
                }
            }
        });

        // 2. Logika Pengetikan "SUPERADMIN"
        document.getElementById('confirmSuperadminAddText')?.addEventListener('input', function() {
            const confirmBtn = document.getElementById('confirmSuperadminAdd');
            if (!confirmBtn) return;

            if (this.value.toUpperCase() === 'SUPERADMIN') {
                confirmBtn.disabled = false;
                confirmBtn.classList.remove('bg-slate-200', 'text-slate-400', 'cursor-not-allowed');
                confirmBtn.classList.add('bg-red-600', 'text-white', 'hover:bg-red-700');
            } else {
                confirmBtn.disabled = true;
                confirmBtn.classList.remove('bg-red-600', 'text-white', 'hover:bg-red-700');
                confirmBtn.classList.add('bg-slate-200', 'text-slate-400', 'cursor-not-allowed');
            }
        });

        // 3. Eksekusi centang setelah konfirmasi sukses
        document.getElementById('confirmSuperadminAdd')?.addEventListener('click', function() {
            // Cari checkbox superadmin di modal utama dan centang
            const superadminCb = document.querySelector('.add-role-cb[data-role-name="superadmin"]');
            if (superadminCb) {
                superadminCb.checked = true;
            }
            closeSuperadminWarningModal();
        });

        // 4. Fungsi Tutup & Reset
        function closeSuperadminWarningModal() {
            const modal = document.getElementById('superadminWarningModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
                
                // Reset Input
                const input = document.getElementById('confirmSuperadminAddText');
                const btn = document.getElementById('confirmSuperadminAdd');
                if (input) input.value = '';
                if (btn) {
                    btn.disabled = true;
                    btn.className = "flex-1 bg-slate-200 text-slate-400 cursor-not-allowed text-[10px] font-black uppercase tracking-widest py-3 rounded-xl transition-all shadow-sm";
                }
            }
        }
    document.addEventListener('DOMContentLoaded', function() {
        // Jalankan polling hanya jika elemen progress bar ada di DOM
        if (document.getElementById('importProgressContainer')) {
            initImportPolling();
        }
    });
    </script>
</x-sidebar>
</x-app-layout>