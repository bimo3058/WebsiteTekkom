<script>
(function() {
    // Mencegah duplikasi script jika diload dua kali
    if (window.permissionManagerLoaded) return;
    window.permissionManagerLoaded = true;

    let pendingRoleCheckbox = null;

    const getEl = () => ({
        modal:      document.getElementById('superadminWarningModal'),
        input:      document.getElementById('confirmSuperadminText'),
        btnConfirm: document.getElementById('btnConfirmSuperadmin'),
    });

    // ── 1. Global Helpers ──────────────────────────────────────────
    
    // Fungsi pindah role via dropdown (menggantikan <a href>)
    window.changeRole = function(slug) {
        const baseUrl = window.location.origin + window.location.pathname;
        const urlParams = new URLSearchParams(window.location.search);
        const perPage = urlParams.get('per_page') || '10';
        window.location.href = `${baseUrl}?role=${slug}&per_page=${perPage}`;
    };

    window.openModal = function(id) { 
        const el = document.getElementById(id); 
        if(el){ el.classList.remove('hidden'); document.body.style.overflow='hidden'; } 
    };
    
    window.closeModal = function(id) { 
        const el = document.getElementById(id); 
        if(el){ el.classList.add('hidden'); document.body.style.overflow=''; } 
    };
    
    window.openEditInfo = function(data) { window.location.href = `/superadmin/users/${data.id}/edit`; };
    
    window.openForceLogoutModal = function(data) {
        const form = document.getElementById('formForceLogout');
        const target = document.getElementById('logoutTargetName');
        if(form) form.action = `/superadmin/users/${data.id}/force-logout`;
        if(target) target.textContent = data.name;
        window.openModal('modalForceLogout');
    };
    
    window.openSuspendModal = function(data) {
        const form = document.getElementById('formSuspend');
        const target = document.getElementById('suspendUserName');
        if(form) form.action = `/superadmin/users/${data.id}/suspend`;
        if(target) target.textContent = data.name;
        window.openModal('modalSuspend');
    };
    
    window.openDeleteHybrid = function(data) {
        const form = document.getElementById('formDeleteHybrid');
        const target = document.getElementById('deleteTargetName');
        if(form) form.action = `/superadmin/users/${data.id}/destroy`;
        if(target) target.textContent = data.name;
        window.openModal('modalDeleteHybrid');
    };

    document.addEventListener('keydown', e => {
        if(e.key === 'Escape') {
            ['modalSuspend', 'modalDeleteHybrid', 'modalForceLogout', 'superadminWarningModal'].forEach(window.closeModal);
            window.closeSuperadminWarning();
        }
    });

    // ── 2. Card Toggle & Autopilot ─────────────────────────────────
    window.toggleCard = function(cardKey) {
        const body    = document.getElementById('card-body-' + cardKey);
        const chevron = document.querySelector('.card-chevron-' + cardKey);
        if (!body) return;

        const isHidden = body.classList.contains('hidden');
        body.classList.toggle('hidden');
        if (chevron) chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';

        if (isHidden) {
            const card = body.closest('.user-card');
            if (card) window.executeAutopilot(card);
        }
    };

    window.executeAutopilot = function(card) {
        if (!card) return;
        const activeCheckboxes = Array.from(card.querySelectorAll('.role-checkbox:checked'));
        const activeRoles      = activeCheckboxes.map(cb => cb.dataset.roleName);
        const isFullAccess     = activeCheckboxes.some(cb => cb.dataset.isAcademic === '1' || cb.dataset.roleName === 'superadmin');

        card.querySelectorAll('.module-box').forEach(box => {
            const allowedRoles = JSON.parse(box.dataset.allAllowedRoles || '[]');
            const isAllowed    = isFullAccess || activeRoles.some(r => allowedRoles.includes(r));
            const checkboxes   = box.querySelectorAll('.perm-checkbox');

            if (isAllowed) {
                box.style.opacity       = '1';
                box.style.filter        = 'none';
                box.style.pointerEvents = 'auto';
                if (isFullAccess) {
                    checkboxes.forEach(cb => cb.checked = true);
                } else {
                    checkboxes.forEach(cb => {
                        const name = cb.dataset.perm.toLowerCase();
                        cb.checked = (name.includes('view') || name.includes('index') || name.includes('read') || name.includes('edit') || name.includes('update'));
                    });
                }
            } else {
                box.style.opacity       = '0.35';
                box.style.filter        = 'grayscale(1)';
                box.style.pointerEvents = 'none';
                checkboxes.forEach(cb => cb.checked = false);
            }
        });
    };

    // ── 3. Superadmin Warning Logic ────────────────────────────────
    window.closeSuperadminWarning = function() {
        const el = getEl();
        if (el.modal) { el.modal.style.display = 'none'; el.modal.classList.remove('active'); }
        if (pendingRoleCheckbox) pendingRoleCheckbox.checked = false;
        pendingRoleCheckbox = null;
        if (el.input) el.input.value = '';
        if (el.btnConfirm) {
            el.btnConfirm.disabled = true;
            el.btnConfirm.style.background = 'var(--c-border)';
            el.btnConfirm.style.color = 'var(--c-fg-muted)';
            el.btnConfirm.style.cursor = 'not-allowed';
        }
    };

    document.addEventListener('input', function(e) {
        if (e.target.id !== 'confirmSuperadminText') return;
        const btn = document.getElementById('btnConfirmSuperadmin');
        const isValid = e.target.value.toUpperCase() === 'KONFIRMASI';
        if (!btn) return;
        btn.disabled = !isValid;
        if (isValid) {
            btn.style.background = 'var(--c-error)'; btn.style.color = '#fff'; btn.style.cursor = 'pointer';
        } else {
            btn.style.background = 'var(--c-border)'; btn.style.color = 'var(--c-fg-muted)'; btn.style.cursor = 'not-allowed';
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.id === 'btnConfirmSuperadmin') {
            if (!pendingRoleCheckbox) return;
            pendingRoleCheckbox.checked = true;
            window.executeAutopilot(pendingRoleCheckbox.closest('.user-card'));
            window.closeSuperadminWarning();
        }
    });

    // ── 4. Global Change Listener (Centang Permission) ─────────────
    document.addEventListener('change', function(e) {
        const target = e.target;

        // A. Role Checkbox
        if (target.classList.contains('role-checkbox')) {
            const wrap = target.closest('label')?.querySelector('.dot-indicator-wrap');
            const dot  = target.closest('label')?.querySelector('.dot-indicator');
            if (wrap) {
                if (target.checked) {
                    wrap.style.borderColor = 'var(--c-primary)'; wrap.style.background = 'rgba(11,38,110,0.06)'; wrap.style.color = 'var(--c-primary)';
                } else {
                    wrap.style.borderColor = 'var(--c-border)'; wrap.style.background = '#fff'; wrap.style.color = 'var(--c-fg-muted)';
                }
            }
            if (dot) dot.style.background = target.checked ? 'var(--c-primary)' : 'var(--c-border)';

            if (target.dataset.roleName === 'superadmin' && target.checked) {
                target.checked = false;
                pendingRoleCheckbox = target;
                const el = getEl();
                if (el.modal) {
                    el.modal.style.display = 'flex'; el.modal.classList.add('active');
                    setTimeout(() => el.input?.focus(), 100);
                }
            } else {
                window.executeAutopilot(target.closest('.user-card'));
            }
        }

        // B. Module Select All (Pilih Semua di Card)
        if (target.classList.contains('module-select-all')) {
            const key = target.dataset.moduleTarget;
            const checkboxes = document.querySelectorAll(`.perm-checkbox[data-module-key="${key}"]`);
            checkboxes.forEach(p => {
                p.checked = target.checked;
                p.dispatchEvent(new Event('change', { bubbles: true }));
            });
        }

        // C. Smart Dependency: Auto-check VIEW jika Edit/Delete dicentang
        if (target.classList.contains('perm-checkbox') && target.checked) {
            if (target.dataset.isView === '0') {
                const key = target.dataset.moduleKey;
                const viewCheckbox = document.querySelector(`.perm-checkbox[data-module-key="${key}"][data-is-view="1"]`);
                if (viewCheckbox && !viewCheckbox.checked) {
                    viewCheckbox.checked = true;
                }
            }
        }

        // D. Smart Dependency: Auto-uncheck SEMUA jika VIEW dimatikan
        if (target.classList.contains('perm-checkbox') && !target.checked) {
            const key = target.dataset.moduleKey;
            
            if (target.dataset.isView === '1') {
                const otherCheckboxes = document.querySelectorAll(`.perm-checkbox[data-module-key="${key}"][data-is-view="0"]`);
                otherCheckboxes.forEach(p => p.checked = false);
            }
            
            // Uncheck tombol "Pilih Semua" setiap kali ada yang uncheck
            const selectAllBtn = document.querySelector(`.module-select-all[data-module-target="${key}"]`);
            if (selectAllBtn) selectAllBtn.checked = false;
        }

        // E. Select All Users (Di Header Tabel User)
        if (target.id === 'permSelectAll') {
            document.querySelectorAll('.perm-user-checkbox').forEach(cb => cb.checked = target.checked);
            window.updatePermBulkBar();
        }

        // F. Individual User Checkbox
        if (target.classList.contains('perm-user-checkbox')) {
            window.updatePermBulkBar();
        }
    });

    // ── 5. User Bulk Permissions Helpers ───────────────────────────
    window.updatePermBulkBar = function() {
        const cbs = document.querySelectorAll('.perm-user-checkbox:checked');
        const bar = document.getElementById('permBulkBar');
        const cnt = document.getElementById('permSelectedCount');
        if(cnt) cnt.textContent = cbs.length;
        if(bar) bar.style.display = cbs.length > 0 ? 'flex' : 'none';
    };

    window.deselectAllPerm = function() {
        const selectAll = document.getElementById('permSelectAll');
        if (selectAll) selectAll.checked = false;
        document.querySelectorAll('.perm-user-checkbox').forEach(cb => cb.checked = false);
        window.updatePermBulkBar();
    };

    window.saveBulkPermissions = function() {
        const form = document.getElementById('bulk-save-form');
        const pd = document.getElementById('bulk-perm-inputs');
        const ud = document.getElementById('bulk-user-inputs');
        if(!form || !pd || !ud) return;
        
        pd.innerHTML = ud.innerHTML = '';
        
        document.querySelectorAll('.perm-checkbox:checked').forEach(cb => {
            const i = document.createElement('input'); 
            i.type = 'hidden'; i.name = 'permissions[]'; i.value = cb.value; 
            pd.appendChild(i);
        });
        
        const checked = document.querySelectorAll('.perm-user-checkbox:checked');
        (checked.length ? checked : document.querySelectorAll('.perm-user-checkbox')).forEach(cb => {
            const i = document.createElement('input'); 
            i.type = 'hidden'; i.name = 'user_ids[]'; i.value = cb.value; 
            ud.appendChild(i);
        });
        
        form.submit();
    };

})();
</script>