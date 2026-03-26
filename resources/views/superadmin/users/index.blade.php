<x-app-layout>
<x-sidebar :user="auth()->user()">
    <div class="min-h-screen bg-slate-50 p-8">
        <div class="max-w-7xl mx-auto">

            {{-- Header --}}
            @include('superadmin.users._header', ['total' => $users->total()])

            {{-- Alerts --}}
            @include('superadmin.users._alerts')

            {{-- Search & Filter --}}
            @include('superadmin.users._search_filter', ['roles' => $roles])

            {{-- Users Table --}}
            @include('superadmin.users._table', ['users' => $users, 'roles' => $roles])

            {{-- Pagination --}}
            @include('superadmin.users._pagination', ['users' => $users])

            {{-- Modals --}}
            @include('superadmin.users._modal_add', ['roles' => $roles])
            @include('superadmin.users._modal_edit', ['roles' => $roles])

        </div>
    </div>

    <script>
        // ============================================
        // VARIABLES
        // ============================================
        const DOSEN_ID = {{ $roles->first(fn($r) => strtolower($r->name) === 'dosen')?->id ?? 'null' }};
        const MAHASISWA_ID = {{ $roles->first(fn($r) => strtolower($r->name) === 'mahasiswa')?->id ?? 'null' }};
        
        // Superadmin warning state
        let pendingAddSubmit = null;
        let pendingEditSubmit = null;

        // ============================================
        // MODAL FUNCTIONS
        // ============================================
        function openModal(id) { 
            document.getElementById(id).classList.remove('hidden'); 
            document.body.style.overflow = 'hidden'; 
        }
        
        function closeModal(id) { 
            document.getElementById(id).classList.add('hidden'); 
            document.body.style.overflow = ''; 
        }
        
        // Escape key to close modals
        document.addEventListener('keydown', e => { 
            if (e.key === 'Escape') {
                ['modalAddUser', 'modalEditRoles', 'superadminWarningModal', 'superadminWarningModalEdit'].forEach(closeModal); 
            }
        });

        // Click outside to close
        ['modalAddUser', 'modalEditRoles', 'superadminWarningModal', 'superadminWarningModalEdit'].forEach(id => {
            document.getElementById(id)?.addEventListener('click', function(e) { 
                if (e.target === this) closeModal(id); 
            });
        });

        // ============================================
        // ADD USER ROLE CHECKBOX HANDLER (Dosen & Mahasiswa)
        // ============================================
        document.querySelectorAll('.add-role-cb').forEach(cb => {
            cb.addEventListener('change', function() {
                const id = parseInt(this.value);
                if (id === DOSEN_ID) {
                    document.getElementById('addFieldDosen').classList.toggle('hidden', !this.checked);
                }
                if (id === MAHASISWA_ID) {
                    document.getElementById('addFieldMahasiswa').classList.toggle('hidden', !this.checked);
                }
            });
            cb.dispatchEvent(new Event('change'));
        });

        // ============================================
        // SUPERADMIN WARNING HANDLER - ADD MODAL
        // ============================================
        function setupAddSuperadminWarning() {
            const superadminCheckbox = document.querySelector('#modalAddUser .add-role-cb[data-role-name="superadmin"]');
            if (superadminCheckbox && !superadminCheckbox.hasAttribute('data-warning-setup')) {
                superadminCheckbox.setAttribute('data-warning-setup', 'true');
                superadminCheckbox.addEventListener('change', function(e) {
                    if (this.checked) {
                        // Store pending state and show warning
                        this.checked = false;
                        openSuperadminWarningModal();
                    }
                });
            }
        }

        function openSuperadminWarningModal() {
            const modal = document.getElementById('superadminWarningModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeSuperadminWarningModal() {
            const modal = document.getElementById('superadminWarningModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
            pendingAddSubmit = null;
        }

        // Confirm Superadmin in Add Modal
        document.getElementById('confirmSuperadminAdd')?.addEventListener('click', function() {
            const superadminCheckbox = document.querySelector('#modalAddUser .add-role-cb[data-role-name="superadmin"]');
            if (superadminCheckbox) {
                superadminCheckbox.checked = true;
                superadminCheckbox.dispatchEvent(new Event('change'));
            }
            closeSuperadminWarningModal();
            
            if (pendingAddSubmit) {
                pendingAddSubmit();
                pendingAddSubmit = null;
            }
        });

        // Add User Form Submit Handler
        const addUserForm = document.getElementById('addUserForm');
        if (addUserForm) {
            addUserForm.addEventListener('submit', function(e) {
                const superadminCheckbox = document.querySelector('#modalAddUser .add-role-cb[data-role-name="superadmin"]');
                if (superadminCheckbox && superadminCheckbox.checked) {
                    e.preventDefault();
                    pendingAddSubmit = () => {
                        addUserForm.submit();
                    };
                }
            });
        }

        // ============================================
        // EDIT ROLES FUNCTIONS
        // ============================================
        let _ctx = {};
        
        function openEditRoles(data) {
            _ctx = data;
            const form = document.getElementById('formEditRoles');
            if (form) {
                form.action = '/superadmin/users/' + data.id + '/roles';
            }
            document.getElementById('editRolesUserName').textContent = data.name;
            
            // Reset all checkboxes
            document.querySelectorAll('.edit-role-cb').forEach(cb => {
                cb.checked = data.role_ids.includes(parseInt(cb.value));
                onEditRoleChange(cb);
            });
            
            // Setup superadmin warning for edit modal
            setupEditSuperadminWarning();
            
            openModal('modalEditRoles');
        }

        function onEditRoleChange(cb) {
            const id = parseInt(cb.value);
            
            if (id === DOSEN_ID) {
                const dosenField = document.getElementById('editFieldDosen');
                const input = document.getElementById('editEmpNumber');
                if (dosenField) {
                    dosenField.classList.toggle('hidden', !cb.checked);
                }
                if (input) {
                    if (cb.checked && _ctx.has_lecturer) {
                        input.value = _ctx.employee_number || '';
                        input.disabled = true;
                    } else {
                        input.value = '';
                        input.disabled = false;
                    }
                }
            }
            
            if (id === MAHASISWA_ID) {
                const mahasiswaField = document.getElementById('editFieldMahasiswa');
                const nimIn = document.getElementById('editStudentNumber');
                const thnIn = document.getElementById('editCohortYear');
                
                if (mahasiswaField) {
                    mahasiswaField.classList.toggle('hidden', !cb.checked);
                }
                if (nimIn && thnIn) {
                    if (cb.checked && _ctx.has_student) {
                        nimIn.value = _ctx.student_number || '';
                        thnIn.value = _ctx.cohort_year || '';
                        nimIn.disabled = true;
                        thnIn.disabled = true;
                    } else {
                        nimIn.value = '';
                        thnIn.value = {{ date('Y') }};
                        nimIn.disabled = false;
                        thnIn.disabled = false;
                    }
                }
            }
        }

        // ============================================
        // SUPERADMIN WARNING HANDLER - EDIT MODAL
        // ============================================
        function setupEditSuperadminWarning() {
            const superadminCheckbox = document.querySelector('#modalEditRoles .edit-role-cb[data-role-name="superadmin"]');
            if (superadminCheckbox && !superadminCheckbox.hasAttribute('data-warning-setup-edit')) {
                superadminCheckbox.setAttribute('data-warning-setup-edit', 'true');
                superadminCheckbox.addEventListener('change', function(e) {
                    if (this.checked) {
                        this.checked = false;
                        openSuperadminWarningModalEdit();
                    }
                });
            }
        }

        function openSuperadminWarningModalEdit() {
            const modal = document.getElementById('superadminWarningModalEdit');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeSuperadminWarningModalEdit() {
            const modal = document.getElementById('superadminWarningModalEdit');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
            pendingEditSubmit = null;
        }

        // Confirm Superadmin in Edit Modal
        document.getElementById('confirmSuperadminEdit')?.addEventListener('click', function() {
            const superadminCheckbox = document.querySelector('#modalEditRoles .edit-role-cb[data-role-name="superadmin"]');
            if (superadminCheckbox) {
                superadminCheckbox.checked = true;
                onEditRoleChange(superadminCheckbox);
            }
            closeSuperadminWarningModalEdit();
            
            if (pendingEditSubmit) {
                pendingEditSubmit();
                pendingEditSubmit = null;
            }
        });

        // Edit Roles Form Submit Handler
        const editRolesForm = document.getElementById('formEditRoles');
        if (editRolesForm) {
            editRolesForm.addEventListener('submit', function(e) {
                const superadminCheckbox = document.querySelector('#modalEditRoles .edit-role-cb[data-role-name="superadmin"]');
                if (superadminCheckbox && superadminCheckbox.checked) {
                    e.preventDefault();
                    pendingEditSubmit = () => {
                        editRolesForm.submit();
                    };
                }
            });
        }

        // ============================================
        // AUTO OPEN MODAL IF ERRORS
        // ============================================
        @if($errors->any())
            openModal('modalAddUser');
        @endif

        // ============================================
        // PAGINATION HANDLING
        // ============================================
        document.addEventListener('click', function (e) {
            const link = e.target.closest('#paginationWrapper a');
            if (!link) return;

            e.preventDefault();

            const url = new URL(link.href);
            const page = url.searchParams.get('page') ?? 1;
            const form = document.querySelector('form[method="GET"]');

            if (form) {
                let pageInput = form.querySelector('input[name="page"]');
                if (!pageInput) {
                    pageInput = document.createElement('input');
                    pageInput.type = 'hidden';
                    pageInput.name = 'page';
                    form.appendChild(pageInput);
                }
                pageInput.value = page;
                form.submit();
            }
        });

        // ============================================
        // AUTO-SUBMIT ON FILTER CHANGE
        // ============================================
        ['select[name="per_page"]', 'select[name="role"]'].forEach(selector => {
            const element = document.querySelector(selector);
            if (element) {
                element.addEventListener('change', function () {
                    const form = this.closest('form');
                    if (form) {
                        let pageInput = form.querySelector('input[name="page"]');
                        if (!pageInput) {
                            pageInput = document.createElement('input');
                            pageInput.type = 'hidden';
                            pageInput.name = 'page';
                            form.appendChild(pageInput);
                        }
                        pageInput.value = 1;
                        form.submit();
                    }
                });
            }
        });

        // ============================================
        // INITIALIZE SUPERADMIN WARNING ON PAGE LOAD
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            setupAddSuperadminWarning();
            setupEditSuperadminWarning();
        });
    </script>
</x-sidebar>
</x-app-layout>