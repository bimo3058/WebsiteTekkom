{{--
    Kolom Aksi tabel Verifikasi Data — tombol "⋯" + panel menu, polanya sama
    persis dengan kolom Aksi Direktori Mahasiswa (direktori/mahasiswa-index):
    .mk-btn--icon + .mk-menu milik modul, dibuka lewat x-data="mmAksiMenu"
    (partials/sitkom-ui). Panelnya berposisi fixed, jadi tidak terpotong
    pembungkus tabel yang overflow-x.

    Props:
      $items — daftar butir menu, tiap butir:
        'label'    teks butir
        'fn'       nama fungsi JS global yang dipanggil (openTinjau, openTinjauReward,
                   openAjukanReward) — nilai tetap dari view, bukan input pengguna
        'payload'  argumen fungsi itu (array → JSON lewat @js)
        'icon'     'eye' (default) | 'gift'
        'disabled' (opsional) butir tampil redup & tidak bisa diklik
        'title'    (opsional) keterangan saat disabled
--}}
@php
    $ikonAksi = [
        'eye'  => '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>',
        'gift' => '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="8" width="18" height="4" rx="1"></rect><path d="M12 8v13M5 12v9h14v-9"></path><path d="M12 8C12 8 11 3 8 3a2.5 2.5 0 0 0 0 5h4zM12 8s1-5 4-5a2.5 2.5 0 0 1 0 5h-4z"></path></svg>',
    ];
@endphp
<div style="position: relative; display: inline-block;" x-data="mmAksiMenu"
     @scroll.window.capture="open = false" @resize.window="open = false">
    <button type="button" @click="toggle($el)" @click.outside="open = false" class="mk-btn mk-btn--secondary mk-btn--icon mk-btn--sm" aria-label="Aksi">
        <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg>
    </button>
    <div x-show="open" x-cloak :style="posisi"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="mk-menu mm-menu-fixed" style="display: none;">
        @foreach($items as $item)
            @if($item['disabled'] ?? false)
                <button type="button" class="mk-menu-item" role="menuitem" disabled aria-disabled="true"
                        title="{{ $item['title'] ?? '' }}" style="opacity: .5; cursor: not-allowed;">
                    {!! $ikonAksi[$item['icon'] ?? 'eye'] !!}
                    {{ $item['label'] }}
                </button>
            @else
                <button type="button" class="mk-menu-item" role="menuitem"
                        @click="open = false; {{ $item['fn'] }}(@js($item['payload']))">
                    {!! $ikonAksi[$item['icon'] ?? 'eye'] !!}
                    {{ $item['label'] }}
                </button>
            @endif
        @endforeach
    </div>
</div>
