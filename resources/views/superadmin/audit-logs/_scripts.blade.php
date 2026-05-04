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

        if (bulkBar) {
            if (selectedLogs.size > 0) {
                bulkBar.style.display = 'flex';
            } else {
                bulkBar.style.display = 'none';
            }
        }
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
                input.type  = 'hidden';
                input.name  = 'ids[]';
                input.value = id;
                container.appendChild(input);
            });
        }

        const countSpan = document.getElementById('selectedCountText');
        if (countSpan) countSpan.textContent = selectedIds.length;

        openModal('modalBulkDeleteAudit');
    };

    window.openModal = function(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeModal = function(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
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

    document.addEventListener('DOMContentLoaded', function() {
        // Select all
        document.getElementById('selectAllLogs')?.addEventListener('change', function() {
            document.querySelectorAll('.log-checkbox').forEach(cb => cb.checked = this.checked);
            updateBulkBar();
        });

        // Individual checkbox
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('log-checkbox')) updateBulkBar();
        });

        // Pagination intercept — preserve filter params
        document.addEventListener('click', function(e) {
            const link = e.target.closest('#paginationWrapper a');
            if (!link) return;
            e.preventDefault();
            const url  = new URL(link.href);
            const page = url.searchParams.get('page') ?? 1;
            const form = document.getElementById('auditFilterForm');
            if (!form) { window.location = link.href; return; }

            let pageInput = form.querySelector('input[name="page"]');
            if (!pageInput) {
                pageInput      = document.createElement('input');
                pageInput.type = 'hidden';
                pageInput.name = 'page';
                form.appendChild(pageInput);
            }
            pageInput.value = page;
            form.submit();
        });

        // Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                ['modalBulkDeleteAudit', 'modalForceLogout', 'modalSuspend'].forEach(closeModal);
            }
        });
    });
})();
</script>