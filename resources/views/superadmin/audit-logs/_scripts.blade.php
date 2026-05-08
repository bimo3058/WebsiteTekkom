<script>
(function() {
    const selectedLogs = new Set();

    function updateBulkBar() {
        const checkboxes = document.querySelectorAll('.log-checkbox:checked');
        const bulkBar    = document.getElementById('bulkActionBar');
        const countEl    = document.getElementById('selectedCount');
        selectedLogs.clear();
        checkboxes.forEach(cb => selectedLogs.add(cb.value));
        if (countEl) countEl.textContent = selectedLogs.size;
        if (bulkBar) bulkBar.style.display = selectedLogs.size > 0 ? 'flex' : 'none';
    }

    window.deselectAll = function() {
        const selectAll = document.getElementById('selectAllLogs');
        if (selectAll) selectAll.checked = false;
        document.querySelectorAll('.log-checkbox').forEach(cb => cb.checked = false);
        updateBulkBar();
    };

    window.openBulkDeleteModal = function() {
        const selectedIds = Array.from(document.querySelectorAll('.log-checkbox:checked')).map(cb => cb.value);
        const container   = document.getElementById('selectedIdsContainer');
        if (container) {
            container.innerHTML = '';
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden'; input.name = 'ids[]'; input.value = id;
                container.appendChild(input);
            });
        }
        const countSpan = document.getElementById('selectedCountText');
        if (countSpan) countSpan.textContent = selectedIds.length;
        openModal('modalBulkDeleteAudit');
    };

    window.openModal = function(id) {
        const modal = document.getElementById(id);
        if (modal) { modal.style.display = 'flex'; modal.classList.add('active'); document.body.style.overflow = 'hidden'; }
    };

    window.closeModal = function(id) {
        const modal = document.getElementById(id);
        if (modal) { modal.style.display = 'none'; modal.classList.remove('active'); document.body.style.overflow = ''; }
    };

    window.openForceLogoutModal = function(data) {
        const form = document.getElementById('formForceLogout');
        if (form) form.action = `/superadmin/users/${data.id}/force-logout`;
        const nameEl = document.getElementById('logoutTargetName');
        if (nameEl) nameEl.textContent = data.name;
        openModal('modalForceLogout');
    };

    window.openSuspendModal = function(data) {
        const form = document.getElementById('formSuspend') || document.querySelector('form[action*="suspend"]');
        if (form) form.action = `/superadmin/users/${data.id}/suspend`;
        const nameEl = document.getElementById('suspendTargetName');
        if (nameEl) nameEl.textContent = data.name;
        openModal('modalSuspend');
    };

    // ── Pagination & sort helpers ──────────────────────────────────────────

    function getForm() { return document.getElementById('auditFilterForm'); }

    function setHidden(form, name, value) {
        let el = form.querySelector(`input[name="${name}"]`);
        if (!el) { el = document.createElement('input'); el.type = 'hidden'; el.name = name; form.appendChild(el); }
        el.value = value;
    }

    window.goToPage = function(page) {
        const form = getForm(); if (!form) return;
        setHidden(form, 'page', page);
        form.submit();
    };

    window.setPerPage = function(perPage) {
        const form = getForm(); if (!form) return;
        setHidden(form, 'per_page', perPage);
        setHidden(form, 'page', 1);
        form.submit();
    };

    // Sort by column — toggles direction if same column is clicked again
    window.setSortBy = function(col) {
        const form = getForm(); if (!form) return;
        const currentCol = form.querySelector('input[name="sort_by"]')?.value || 'created_at';
        const currentDir = form.querySelector('input[name="sort_dir"]')?.value || 'desc';
        // If same column, flip direction; otherwise default to desc
        const newDir = (col === currentCol && currentDir === 'desc') ? 'asc' : 'desc';
        setHidden(form, 'sort_by', col);
        setHidden(form, 'sort_dir', newDir);
        setHidden(form, 'page', 1);
        form.submit();
    };

    window.setSortDir = function(dir) {
        const form = getForm(); if (!form) return;
        setHidden(form, 'sort_dir', dir);
        setHidden(form, 'page', 1);
        form.submit();
    };

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('selectAllLogs')?.addEventListener('change', function() {
            document.querySelectorAll('.log-checkbox').forEach(cb => cb.checked = this.checked);
            updateBulkBar();
        });
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('log-checkbox')) updateBulkBar();
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') ['modalBulkDeleteAudit', 'modalForceLogout', 'modalSuspend'].forEach(closeModal);
        });
    });
})();
</script>