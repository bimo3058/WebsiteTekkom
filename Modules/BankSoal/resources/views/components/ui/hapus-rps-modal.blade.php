<!-- RPS Delete Confirmation Modal Component -->
<div id="deleteConfirmationModal" class="fixed inset-0 bg-black/70 z-50 items-center justify-center hidden">
    <div class="bg-white rounded-lg w-11/12 max-w-md shadow-2xl">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-semibold text-slate-900">Konfirmasi Penghapusan</h3>
        </div>

        <!-- Modal Content -->
        <div class="px-6 py-4 space-y-3">
            <p class="text-slate-700">Apakah Anda yakin ingin menghapus RPS untuk:</p>
            <p id="deleteConfirmationMkName" class="font-semibold text-slate-900 text-lg"></p>
            <p class="text-sm text-red-600 mt-4">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Tindakan ini tidak dapat dibatalkan!
            </p>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex justify-end gap-3">
            <button type="button" id="deleteConfirmationBatal" class="btn-secondary">
                Batal
            </button>
            <button type="button" id="deleteConfirmationSetuju" class="btn-danger" style="background-color: #dc2626; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 500; display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    const modal = document.getElementById('deleteConfirmationModal');
    const mkNameEl = document.getElementById('deleteConfirmationMkName');
    const batalBtn = document.getElementById('deleteConfirmationBatal');
    const setujuBtn = document.getElementById('deleteConfirmationSetuju');
    
    let pendingDeleteUrl = null;

    // Open modal when delete button clicked
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-rps-btn')) {
            const btn = e.target.closest('.delete-rps-btn');
            const mkName = btn.dataset.mk;
            const destroyUrl = btn.dataset.destroyUrl;

            mkNameEl.textContent = mkName;
            pendingDeleteUrl = destroyUrl;
            
            // Show modal
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
        }
    });

    // Close modal when batal clicked
    batalBtn.addEventListener('click', function() {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        pendingDeleteUrl = null;
    });

    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
            pendingDeleteUrl = null;
        }
    });

    // Submit delete when setuju clicked
    setujuBtn.addEventListener('click', function() {
        if (!pendingDeleteUrl) return;

        // Show loading state
        setujuBtn.disabled = true;
        setujuBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';

        // Send AJAX DELETE request
        fetch(pendingDeleteUrl, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message
                showNotification('success', 'RPS berhasil dihapus');
                
                // Reload page after short delay
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Gagal menghapus RPS');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', error.message || 'Terjadi kesalahan saat menghapus RPS');
            
            // Reset button state
            setujuBtn.disabled = false;
            setujuBtn.innerHTML = '<i class="fas fa-trash"></i> Hapus';
        });
    });

    // Helper function to show notification
    function showNotification(type, message) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-60 shadow-lg ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
})();
</script>

<style>
    .btn-danger:hover {
        background-color: #b91c1c !important;
    }

    .btn-danger:disabled {
        background-color: #9ca3af !important;
        cursor: not-allowed;
    }
</style>
