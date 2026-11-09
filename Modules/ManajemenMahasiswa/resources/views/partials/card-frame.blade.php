{{-- Bingkai kartu putih utama bab Direktori, Verifikasi Data, dan Manajemen Kegiatan.
     Ukurannya disamakan 1:1 dengan kartu Role & Permission SITKOM
     (resources/views/superadmin/permission/index.blade.php → .rp-wrap/.rp-box):
     jarak luar 10px, radius 12px, border + bayangan tipis, isi 20px 24px.
     Layout modul (components/layouts/*) memberi jarak 24px 28px + padding 25px,
     sehingga kartu tampak lebih kecil dan menjorok dibanding halaman global.
     Ditimpa di sini, bukan di layout, supaya bab lain (Forum, Kegiatan, dst.)
     tidak ikut berubah. --}}
<style>
    .content { padding: 10px; }
    .main-wrapper {
        border: 1px solid var(--c-border);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        padding: 20px 24px;
        min-height: calc(100vh - 80px);
    }

    /* Band judul halaman: garis pemisah selebar kotak (seperti .user-box-header /
       .rp-box-header SITKOM). Margin negatif "membatalkan" padding .main-wrapper
       supaya garisnya menyentuh tepi kiri-kanan kotak. Halaman yang tidak memakai
       kelas ini tidak terpengaruh. */
    .mm-frame-header {
        margin: -20px -24px 20px;
        padding: 16px 24px;
        border-bottom: 1px solid var(--c-border);
    }
    /* Area isi di bawah band: kartu-kartu bertumpuk dengan jarak seragam 16px */
    .mm-frame-body {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    /* Jarak antarkartu dipegang gap di atas — margin bawaan anak (alert, .mb-4) dinolkan */
    .mm-frame-body > .alert,
    .mm-frame-body > .mb-4 { margin-bottom: 0 !important; }

    @media (max-width: 767px) {
        .content { padding: 8px 8px 80px; }
        .main-wrapper { padding: 16px 14px; border-radius: 10px; }
        .mm-frame-header { margin: -16px -14px 16px; padding: 12px 14px; }
    }
</style>
