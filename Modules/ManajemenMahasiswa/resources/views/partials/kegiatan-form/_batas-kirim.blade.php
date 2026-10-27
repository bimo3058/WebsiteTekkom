{{--
    Penjaga total ukuran kiriman form kegiatan (Rencana Proker, Pelaksanaan,
    Laporan & Arsip). `post_max_size` PHP membatasi seluruh isi satu kiriman;
    bila terlewati, server menolak sebelum validasi dan pengguna mendarat di
    halaman error. Di sini kiriman ditahan dulu dengan pesan untuk mencicil.
--}}
@php
    $batasKirim = \Modules\ManajemenMahasiswa\Support\BatasUnggah::totalKirimByte();
@endphp
@if($batasKirim)
<script>
(function () {
    // Sisakan ruang untuk isian teks & token form.
    const BATAS_KIRIM = Math.floor({{ $batasKirim }} * 0.95);
    const keMB = bytes => (bytes / 1048576).toFixed(1).replace('.', ',');

    document.querySelectorAll('form[enctype="multipart/form-data"]').forEach(form => {
        form.addEventListener('submit', event => {
            let total = 0;
            form.querySelectorAll('input[type="file"]').forEach(input => {
                Array.from(input.files || []).forEach(file => { total += file.size; });
            });

            if (total <= BATAS_KIRIM) return;

            event.preventDefault();
            event.stopImmediatePropagation();
            const pesan = `Total berkas yang dipilih ${keMB(total)} MB, melebihi batas satu kali simpan (${keMB(BATAS_KIRIM)} MB). `
                + 'Simpan sebagian berkas dulu, lalu buka Edit untuk mengunggah sisanya.';
            if (window.mkNotify) {
                window.mkNotify({ title: 'Unggah dicicil', message: pesan, variant: 'warning' });
            } else {
                window.alert(pesan);
            }
        }, true);
    });
})();
</script>
@endif
