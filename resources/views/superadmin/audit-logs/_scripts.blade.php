<script>
    const selectedLogs = new Set();

    function updateBulkBar() {
        const checkboxes = document.querySelectorAll('.log-checkbox:checked');
        const bulkBar = document.getElementById('bulkActionBar');
        const selectedCountText = document.getElementById('selectedCount');
        
        selectedLogs.clear();
        checkboxes.forEach(cb => selectedLogs.add(cb.value));
        
        if (selectedCountText) selectedCountText.textContent = selectedLogs.size;
        
        if (selectedLogs.size > 0) {
            bulkBar?.classList.replace('hidden', 'flex');
        } else {
            bulkBar?.classList.replace('flex', 'hidden');
        }
    }
    
    function deselectAll() {
        const selectAll = document.getElementById('selectAllLogs');
        if (selectAll) selectAll.checked = false;
        
        document.querySelectorAll('.log-checkbox').forEach(cb => {
            cb.checked = false;
        });
        
        updateBulkBar();
    }
    
    function openBulkDeleteModal() {
        const selectedIds = Array.from(document.querySelectorAll('.log-checkbox:checked')).map(cb => cb.value);
        const container = document.getElementById('selectedIdsContainer');
        
        container.innerHTML = '';
        selectedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            container.appendChild(input);
        });
        
        const selectedCountSpan = document.getElementById('selectedCountText');
        if (selectedCountSpan) selectedCountSpan.textContent = selectedIds.length;
        
        openModal('modalBulkDeleteAudit');
    }

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

    function openForceLogoutModal(data) {
        document.getElementById('formForceLogout').action = `/superadmin/users/${data.id}/force-logout`;
        document.getElementById('logoutTargetName').textContent = data.name;
        openModal('modalForceLogout');
    }

    function openSuspendModal(data) {
        const form = document.getElementById('formSuspend') || document.querySelector('form[action*="suspend"]');
        if(form) form.action = `/superadmin/users/${data.id}/suspend`;
        const nameEl = document.getElementById('suspendTargetName');
        if(nameEl) nameEl.textContent = data.name;
        openModal('modalSuspend');
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('selectAllLogs')?.addEventListener('change', function() {
            document.querySelectorAll('.log-checkbox').forEach(cb => cb.checked = this.checked);
            updateBulkBar();
        });
        
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('log-checkbox')) updateBulkBar();
        });
        
        document.addEventListener('click', function (e) {
            const link = e.target.closest('#paginationWrapper a');
            if (!link) return;
            e.preventDefault();
            const url = new URL(link.href);
            const page = url.searchParams.get('page') ?? 1;
            const form = document.getElementById('auditFilterForm');
            
            let pageInput = form.querySelector('input[name="page"]');
            if (!pageInput) {
                pageInput = document.createElement('input');
                pageInput.type = 'hidden';
                pageInput.name = 'page';
                form.appendChild(pageInput);
            }
            pageInput.value = page;
            form.submit();
        });
        
        document.addEventListener('keydown', function(e) { 
            if (e.key === 'Escape') {
                ['modalBulkDeleteAudit', 'modalForceLogout', 'modalSuspend'].forEach(closeModal);
            }
        });
    });
</script>