{{--
    Pesan hasil aksi (berhasil / gagal / peringatan / info) bab Direktori
    Mahasiswa, Manajemen Kegiatan, dan Verifikasi Data.

    Bentuknya meniru pesan di User Management SITKOM
    (resources/views/superadmin/users/_alerts.blade.php): kartu putih, garis kiri
    3px berwarna, ikon di kotak kecil, teks 11px, tombol tutup abu. Daftar
    kesalahan validasi memakai varian bertajuk seperti blok "Validation Error"
    di sana.

    Pemakaian:
        <x-manajemenmahasiswa::ui.flash type="success" :message="session('success')" />
        <x-manajemenmahasiswa::ui.flash type="error" :messages="$errors->all()" />
        <x-manajemenmahasiswa::ui.flash type="warning">Isi dengan <b>markup</b></x-manajemenmahasiswa::ui.flash>

    Tombol tutup memakai Alpine (sudah dimuat semua layout modul), jadi tidak
    bergantung pada Bootstrap.
--}}
@props([
    'type'        => 'success', // success | error | warning | info
    'message'     => null,
    'messages'    => [],        // daftar pesan → tampil sebagai varian bertajuk
    'title'       => null,      // tajuk varian daftar; bawaan per jenis
    'dismissible' => true,
])

@php
    $tone = match ($type) {
        'error'   => ['fg' => 'var(--c-error, #DF1C41)',   'subtle' => 'var(--c-error-subtle, #FADAE1)',   'border' => 'var(--c-error-subtle, #FADAE1)', 'title' => 'Periksa Kembali Isian'],
        'warning' => ['fg' => 'var(--c-warning, #956321)', 'subtle' => 'var(--c-warning-subtle, #F9ECCB)', 'border' => '#FBD982',                         'title' => 'Perhatian'],
        'info'    => ['fg' => 'var(--c-sky, #0C4D6E)',     'subtle' => 'var(--c-sky-subtle, #D1F0F9)',     'border' => '#7EDCF1',                         'title' => 'Informasi'],
        default   => ['fg' => 'var(--c-success, #287F6E)', 'subtle' => 'var(--c-success-subtle, #DDF2EE)', 'border' => '#9DE0D3',                         'title' => 'Berhasil'],
    };

    $messages = collect($messages)->filter()->values();
    $isList   = $messages->count() > 1;
    // Satu pesan dalam array tetap tampil sebagai kartu biasa
    $single   = $message ?? ($messages->count() === 1 ? $messages->first() : null);
@endphp

@if($isList || filled($single) || $slot->isNotEmpty())
    <div {{ $attributes->merge(['class' => 'mm-flash']) }}
         role="{{ $type === 'error' ? 'alert' : 'status' }}"
         @if($dismissible) x-data="{ show: true }" x-show="show" @endif
         {{-- flex-shrink 0: .main-wrapper adalah kolom flex yang bisa digulir; tanpa ini
              kartu ber-overflow:hidden menyusut sampai tinggal garis tepinya. --}}
         style="flex-shrink: 0; background: #ffffff; border: 1px solid {{ $tone['border'] }}; border-left: 3px solid {{ $tone['fg'] }}; border-radius: 10px; overflow: hidden;">

        @if($isList)
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 8px 14px; background: {{ $tone['subtle'] }};">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" style="color: {{ $tone['fg'] }}; flex-shrink: 0;">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: {{ $tone['fg'] }};">{{ $title ?? $tone['title'] }}</span>
                </div>
                @if($dismissible)
                    <button type="button" @click="show = false" aria-label="Tutup pesan"
                            style="background: none; border: none; cursor: pointer; color: var(--c-fg-muted, #666D80); padding: 2px; display: flex;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
                    </button>
                @endif
            </div>
            <ul style="padding: 10px 14px 10px 28px; list-style: disc; margin: 0;">
                @foreach($messages as $pesan)
                    <li style="font-size: 11px; color: {{ $tone['fg'] }}; font-weight: 500; margin-bottom: 2px; line-height: 1.5;">{{ $pesan }}</li>
                @endforeach
            </ul>
        @else
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 10px 14px;">
                <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                    <div style="width: 26px; height: 26px; border-radius: 7px; background: {{ $tone['subtle'] }}; color: {{ $tone['fg'] }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        @if($type === 'success')
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                        @else
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        @endif
                    </div>
                    <span style="font-size: 11px; font-weight: 600; color: {{ $tone['fg'] }}; line-height: 1.5;">
                        @if(filled($single))
                            {{ $single }}
                        @else
                            {{ $slot }}
                        @endif
                    </span>
                </div>
                @if($dismissible)
                    <button type="button" @click="show = false" aria-label="Tutup pesan"
                            style="background: none; border: none; cursor: pointer; color: var(--c-fg-muted, #666D80); padding: 2px; display: flex; flex-shrink: 0;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
                    </button>
                @endif
            </div>
        @endif
    </div>
@endif
