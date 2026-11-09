{{--
    Header modal Bootstrap bab Direktori Mahasiswa, Manajemen Kegiatan, dan
    Verifikasi Data, meniru modal Tambah User SITKOM
    (resources/views/superadmin/users/_modal_add.blade.php): pita navy muda,
    ikon di kotak navy, judul kapital + subjudul navy, tombol tutup berupa ikon.
    Gayanya ada di partials/sitkom-ui.

    Pemakaian (isi slot = judul; boleh markup, mis. span ber-id yang diganti JS):
        <x-manajemenmahasiswa::ui.modal-header subtitle="Kirim prestasi untuk diverifikasi">
            <x-slot:icon><svg ...></svg></x-slot:icon>
            Ajukan Prestasi Lomba
        </x-manajemenmahasiswa::ui.modal-header>
--}}
@props(['subtitle' => null])

<div {{ $attributes->class(['modal-header', 'mm-modal-header']) }}>
    <div class="mm-modal-header__lead">
        @isset($icon)
            <span class="mm-modal-header__icon" aria-hidden="true">{{ $icon }}</span>
        @endisset
        <div style="min-width: 0;">
            <h5 class="modal-title mm-modal-header__title">{{ $slot }}</h5>
            @if($subtitle)
                <p class="mm-modal-header__subtitle">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
    <button type="button" class="mm-modal-header__close" data-bs-dismiss="modal" aria-label="Tutup">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
    </button>
</div>
