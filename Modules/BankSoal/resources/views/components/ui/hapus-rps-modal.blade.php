<!-- Konfirmasi Hapus Draft RPS menggunakan SweetAlert2 (matches admin style) -->
<script>
(function() {
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.delete-draft-btn');
        if (!btn) return;

        const mkName = btn.dataset.mk || 'data ini';
        const destroyUrl = btn.dataset.destroyUrl;
        if (!destroyUrl) return;

        const title = 'Hapus Draft RPS?';
        const text = `Apakah Anda yakin ingin menghapus draft RPS untuk "${mkName}"? Data yang dihapus tidak dapat dikembalikan.`;

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: async () => {
                    try {
                        const response = await fetch(destroyUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (!response.ok || !data.success) {
                            throw new Error(data.message || 'Gagal menghapus data');
                        }
                        return data;
                    } catch (error) {
                        Swal.showValidationMessage(error.message || 'Terjadi kesalahan saat menghapus data');
                    }
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: result.value?.message || 'Draft RPS berhasil dihapus',
                        timer: 1500,
                        showConfirmButton: false,
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        } else if (confirm(text)) {
            fetch(destroyUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Berhasil dihapus');
                    window.location.reload();
                } else {
                    alert(data.message || 'Gagal menghapus');
                }
            })
            .catch(err => alert(err.message || 'Terjadi kesalahan'));
        }
    });
})();
</script>
