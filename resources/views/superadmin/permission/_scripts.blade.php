<script>
(function() {
    if (window.permissionManagerLoaded) return;
    window.permissionManagerLoaded = true;

    let pendingRoleCheckbox = null;

    const getEl = () => ({
        modal:      document.getElementById('superadminWarningModal'),
        input:      document.getElementById('confirmSuperadminText'),
        btnConfirm: document.getElementById('btnConfirmSuperadmin'),
    });

    // ── Toggle card expand/collapse ────────────────────────────
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

    // ── Close modal ────────────────────────────────────────────
    window.closeSuperadminWarning = function() {
        const el = getEl();
        if (el.modal) {
            el.modal.style.display = 'none';
            el.modal.classList.remove('active');
        }
        if (pendingRoleCheckbox) pendingRoleCheckbox.checked = false;
        pendingRoleCheckbox = null;
        if (el.input) el.input.value = '';
        // Reset confirm button
        if (el.btnConfirm) {
            el.btnConfirm.disabled = true;
            el.btnConfirm.style.background = 'var(--c-border)';
            el.btnConfirm.style.color = 'var(--c-fg-muted)';
            el.btnConfirm.style.cursor = 'not-allowed';
        }
    };

    // ── Confirm input validation ───────────────────────────────
    document.addEventListener('input', function(e) {
        if (e.target.id !== 'confirmSuperadminText') return;
        const btn = document.getElementById('btnConfirmSuperadmin');
        const isValid = e.target.value.toUpperCase() === 'KONFIRMASI';
        if (!btn) return;
        btn.disabled = !isValid;
        if (isValid) {
            btn.style.background = 'var(--c-error)';
            btn.style.color = '#fff';
            btn.style.cursor = 'pointer';
        } else {
            btn.style.background = 'var(--c-border)';
            btn.style.color = 'var(--c-fg-muted)';
            btn.style.cursor = 'not-allowed';
        }
    });

    // ── Confirm superadmin click ───────────────────────────────
    document.addEventListener('click', function(e) {
        if (e.target.id !== 'btnConfirmSuperadmin') return;
        if (!pendingRoleCheckbox) return;
        pendingRoleCheckbox.checked = true;
        window.executeAutopilot(pendingRoleCheckbox.closest('.user-card'));
        window.closeSuperadminWarning();
    });

    // ── Role & permission change handlers ──────────────────────
    document.addEventListener('change', function(e) {
        const target = e.target;

        // Role checkbox
        if (target.classList.contains('role-checkbox')) {
            // Update pill style
            const wrap = target.closest('label')?.querySelector('.dot-indicator-wrap');
            const dot  = target.closest('label')?.querySelector('.dot-indicator');
            if (wrap) {
                if (target.checked) {
                    wrap.style.borderColor = 'var(--c-primary)';
                    wrap.style.background  = 'rgba(11,38,110,0.06)';
                    wrap.style.color       = 'var(--c-primary)';
                } else {
                    wrap.style.borderColor = 'var(--c-border)';
                    wrap.style.background  = '#fff';
                    wrap.style.color       = 'var(--c-fg-muted)';
                }
            }
            if (dot) dot.style.background = target.checked ? 'var(--c-primary)' : 'var(--c-border)';

            // Superadmin guard
            if (target.dataset.roleName === 'superadmin' && target.checked) {
                target.checked = false;
                pendingRoleCheckbox = target;
                const el = getEl();
                if (el.modal) {
                    el.modal.style.display = 'flex';
                    el.modal.classList.add('active');
                    setTimeout(() => el.input?.focus(), 100);
                }
            } else {
                window.executeAutopilot(target.closest('.user-card'));
            }
        }

        // Module select-all
        if (target.classList.contains('module-select-all')) {
            const key = target.dataset.moduleTarget;
            document.querySelectorAll(`.perm-checkbox[data-module-key="${key}"]`).forEach(p => p.checked = target.checked);
        }

        // Master view unchecked → uncheck children
        if (target.classList.contains('master-view-cb') && !target.checked) {
            const key = target.dataset.moduleKey;
            document.querySelectorAll(`.perm-checkbox[data-module-key="${key}"][data-is-view="0"]`).forEach(p => p.checked = false);
        }
    });

    // ── Autopilot: auto-assign permissions based on roles ─────
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
})();
</script>