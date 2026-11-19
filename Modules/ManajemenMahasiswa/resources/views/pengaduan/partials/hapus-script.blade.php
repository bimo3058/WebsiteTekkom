{{--
    Konfirmasi hapus bab Pengaduan lewat dialog modul (ui.dialog, varian danger).
    Tombolnya tetap navy/putih sesuai sistem .mk-btn; hanya lencana ikonnya merah.

        <form ... data-judul="{{ $judul }}" onsubmit="return pgdKonfirmasiHapus(this)">

    Judul dibaca dari data-judul dan ditampilkan lewat x-text, jadi aman dari XSS.
--}}
@once
    <script>
        window.pgdKonfirmasiHapus = function (form) {
            return mkConfirmSubmit(form, 'Pengaduan "' + (form.dataset.judul || '-') + '" akan dihapus dari daftar pengaduan.', {
                title: 'Hapus Pengaduan?', variant: 'danger', confirmText: 'Ya, Hapus'
            });
        };
    </script>
@endonce
