{{--
    Tombol Hapus di halaman detail Rencana Proker, Pelaksanaan, dan Laporan & Arsip.

    Konfirmasinya memakai dialog SITKOM bersama (components/ui/dialog →
    mkConfirmSubmit), sama dengan tombol hapus di Direktori Alumni, Konseling, dan
    Pengaduan. Dulu tiap subbab punya kotak konfirmasi sendiri dengan judul, teks
    tombol, dan cara menutup yang berbeda-beda; kini kalimatnya disusun di satu
    tempat dan dialog bisa ditutup dengan Esc, klik latar, atau tombol ×.

    Variabel: $action (URL destroy), $jenis ('Rencana Proker' | 'Kegiatan'),
    $judul (judul kegiatan), $lampiran (file yang ikut terhapus, mis. 'banner-nya').
    Opsional: $catatan — kalimat tambahan, mis. "rencana awalnya tetap tersimpan" saat
    menghapus salinan Pelaksanaan/Arsip (S-49).
--}}
@php
    $pesanHapus = $jenis . ' "' . $judul . '" akan dihapus permanen beserta ' . $lampiran
        . '. Tindakan ini tidak dapat dibatalkan.' . (!empty($catatan) ? ' ' . $catatan : '');
@endphp
<form action="{{ $action }}" method="POST" class="m-0"
      onsubmit="return mkConfirmSubmit(this, @js($pesanHapus), { title: @js('Hapus ' . $jenis), confirmText: 'Ya, Hapus' })">
    @csrf
    @method('DELETE')
    <button type="submit" class="mk-btn mk-btn--secondary mk-btn--sm">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="3 6 5 6 21 6"></polyline>
            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
        </svg>
        Hapus
    </button>
</form>
