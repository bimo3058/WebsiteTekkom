<script>
(function() {
    if (window.permissionManagerLoaded) return;
    window.permissionManagerLoaded = true;

    let pendingRoleCheckbox = null;

    const getElements = () => ({
        modal: document.getElementById('superadminWarningModal'),
        input: document.getElementById('confirmSuperadminText'),
        btnConfirm: document.getElementById('btnConfirmSuperadmin')
    });

    window.toggleCard = function(userId) {
        const body = document.getElementById('card-body-' + userId);
        const chevron = document.querySelector('.card-chevron-' + userId);
        if (!body) return;

        const isHidden = body.classList.contains('hidden');
        body.classList.toggle('hidden');
        if (chevron) chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';

        if (isHidden) {
            const card = body.closest('.user-card');
            if (card) window.executeAutopilot(card);
        }
    };

    window.closeSuperadminWarning = function() {
        const el = getElements();
        if (el.modal) el.modal.classList.add('hidden');
        if (pendingRoleCheckbox) pendingRoleCheckbox.checked = false;
        pendingRoleCheckbox = null;
        if (el.input) el.input.value = '';
    };

    // Logic input modal: "KONFIRMASI"
    document.addEventListener('input', function(e) {
        if (e.target.id === 'confirmSuperadminText') {
            const btn = document.getElementById('btnConfirmSuperadmin');
            const isValid = e.target.value.toUpperCase() === 'KONFIRMASI';
            if (btn) {
                btn.disabled = !isValid;
                btn.className = isValid 
                    ? "flex-1 bg-rose-600 text-white text-[10px] font-black uppercase tracking-widest py-3 rounded-xl shadow-md transition-all active:scale-95"
                    : "flex-1 bg-slate-200 text-slate-400 cursor-not-allowed text-[10px] font-black uppercase tracking-widest py-3 rounded-xl transition-all";
            }
        }
    });

    // Konfirmasi Superadmin
    document.addEventListener('click', function(e) {
        if (e.target.id === 'btnConfirmSuperadmin') {
            if (pendingRoleCheckbox) {
                pendingRoleCheckbox.checked = true;
                window.executeAutopilot(pendingRoleCheckbox.closest('.user-card'));
                const el = getElements();
                if (el.modal) el.modal.classList.add('hidden');
                pendingRoleCheckbox = null;
            }
        }
    });

    // Role & Module Logic
    document.addEventListener('change', function(e) {
        const target = e.target;

        if (target.classList.contains('role-checkbox')) {

            const dot = target.closest('label')?.querySelector('.dot-indicator');
            if (dot) {
                dot.classList.toggle('bg-[#5E53F4]', target.checked);
                dot.classList.toggle('bg-[#DEE2E6]', !target.checked);
            }

            if (target.dataset.roleName === 'superadmin' && target.checked) {
                target.checked = false; 
                pendingRoleCheckbox = target;
                const el = getElements();
                if (el.modal) {
                    el.modal.classList.remove('hidden');
                    setTimeout(() => el.input?.focus(), 100);
                }
            } else {
                window.executeAutopilot(target.closest('.user-card'));
            }
        }

        if (target.classList.contains('module-select-all')) {
            const key = target.dataset.moduleTarget;
            document.querySelectorAll(`.perm-checkbox[data-module-key="${key}"]`).forEach(p => p.checked = target.checked);
        }

        if (target.classList.contains('master-view-cb') && !target.checked) {
            const key = target.dataset.moduleKey;
            document.querySelectorAll(`.perm-checkbox[data-module-key="${key}"][data-is-view="0"]`).forEach(p => p.checked = false);
        }
    });

    window.executeAutopilot = function(card) {
        if (!card) return;
        const activeCheckboxes = Array.from(card.querySelectorAll('.role-checkbox:checked'));
        const activeRoles = activeCheckboxes.map(cb => cb.dataset.roleName);
        const isFullAccess = activeCheckboxes.some(cb => cb.dataset.isAcademic === '1' || cb.dataset.roleName === 'superadmin');

        card.querySelectorAll('.module-box').forEach(box => {
            const allowedRoles = JSON.parse(box.dataset.allAllowedRoles || '[]');
            const isAllowed = isFullAccess || activeRoles.some(r => allowedRoles.includes(r));
            const checkboxes = box.querySelectorAll('.perm-checkbox');

            if (isAllowed) {
                box.classList.remove('opacity-40', 'grayscale', 'pointer-events-none');
                if (isFullAccess) {
                    checkboxes.forEach(cb => cb.checked = true);
                } else {
                    checkboxes.forEach(cb => {
                        const name = cb.dataset.perm.toLowerCase();
                        cb.checked = (name.includes('view') || name.includes('index') || name.includes('read') || name.includes('edit') || name.includes('update'));
                    });
                }
            } else {
                box.classList.add('opacity-40', 'grayscale', 'pointer-events-none');
                checkboxes.forEach(cb => cb.checked = false);
            }
        });
    };
})();
</script>