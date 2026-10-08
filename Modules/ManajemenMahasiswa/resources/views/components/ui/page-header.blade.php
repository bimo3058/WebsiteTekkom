{{--
    Page header baku modul SIMENMA.

    Sebelumnya tiap halaman menulis sendiri blok judul + subjudul + badge, sehingga
    ukurannya berbeda-beda (22px/18px/1.45rem, bobot 700/800, jarak dan padding
    tidak seragam). Komponen ini menjadikan header "Pengumuman & Informasi"
    (Modules/ManajemenMahasiswa/resources/views/pengumuman/pengumuman-admin.blade.php)
    sebagai satu-satunya acuan:

        judul    22px / 700 / -0.02em / line-height 1.2
        badge    10px / 600 / pill primary-subtle
        subjudul 12px / --c-fg-muted
        jarak    judul→subjudul 3px, baris judul→badge 8px

    ── Cara pakai ──────────────────────────────────────────────────────────────
    Di dalam .dash-box-header (garis pemisah sudah disediakan kotaknya):

        <div class="dash-box-header">
            <x-manajemenmahasiswa::ui.page-header
                title="Pengumuman & Informasi"
                badge="Modul Mahasiswa"
                subtitle="Wadah informasi untuk mahasiswa dan alumni">
                <x-slot:actions>
                    <a href="..." class="mk-btn mk-btn--primary">Buat Post</a>
                </x-slot:actions>
            </x-manajemenmahasiswa::ui.page-header>
        </div>

    Di halaman tanpa .dash-box-header (konten langsung di .main-wrapper), pakai
    `bordered` supaya komponen menggambar padding + garis pemisahnya sendiri,
    full-bleed sampai tepi kotak persis seperti .dash-box-header:

        <x-manajemenmahasiswa::ui.page-header bordered title="Manajemen Kegiatan" ... />

    Subjudul yang butuh markup (angka tebal, tanggal, badge kecil) ditulis sebagai
    isi komponen, menggantikan atribut `subtitle`:

        <x-manajemenmahasiswa::ui.page-header title="Dashboard Analitik" badge="Modul Mahasiswa">
            Selamat datang kembali, <strong>{{ $nama }}</strong>
        </x-manajemenmahasiswa::ui.page-header>
--}}

@props([
    'title',
    'badge'    => null,
    'subtitle' => null,
    'bordered' => false,
])

@once
    @push('styles')
        <style>
            .mm-page-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                flex-wrap: wrap;
            }

            /* Varian untuk halaman yang tidak memakai .dash-box-header. Margin
               negatif membatalkan padding .main-wrapper (20px 24px) supaya header
               rapat ke tepi atas kotak dan garis pemisahnya full-bleed, persis
               seperti .dash-box-header.

               Syaratnya: header harus jadi elemen TERLIHAT pertama di dalam
               .main-wrapper — flash message ditaruh di bawahnya, mengikuti
               halaman acuan Pengumuman & Informasi. Tag style dan include CSS
               tidak masalah karena display:none dan tidak memakan ruang.

               Halaman yang padding kotaknya berbeda cukup menimpa --mm-ph-bleed
               dan --mm-ph-bleed-top di .main-wrapper miliknya (lihat Pengaduan). */
            .mm-page-header--bordered {
                margin: calc(var(--mm-ph-bleed-top, 20px) * -1)
                        calc(var(--mm-ph-bleed, 24px) * -1)
                        20px;
                padding: 16px var(--mm-ph-bleed, 24px);
                border-bottom: 1px solid var(--c-border, #DFE1E7);
            }

            /* Judul + elemen pendahulunya (mis. tombol kembali) tetap satu kelompok
               di kiri, supaya space-between hanya memisahkan teks dari tombol aksi. */
            .mm-page-header__lead {
                display: flex;
                align-items: center;
                gap: 12px;
                min-width: 0;
            }

            .mm-page-header__leading {
                display: flex;
                align-items: center;
                flex-shrink: 0;
            }

            .mm-page-header__titlerow {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
                margin-bottom: 3px;
            }

            .mm-page-header__title {
                font-size: 22px;
                font-weight: 700;
                color: var(--c-fg, #0D0D12);
                letter-spacing: -0.02em;
                line-height: 1.2;
                margin: 0;
            }

            .mm-page-header__badge {
                font-size: 10px;
                font-weight: 600;
                color: var(--c-primary, #0B266E);
                background: rgba(11, 38, 110, 0.09);
                border: 1px solid rgba(11, 38, 110, 0.18);
                padding: 2px 8px;
                border-radius: 9999px;
                letter-spacing: 0.03em;
                white-space: nowrap;
            }

            .mm-page-header__subtitle {
                font-size: 12px;
                color: var(--c-fg-muted, #666D80);
                line-height: 1.5;
                margin: 0;
            }

            .mm-page-header__actions {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
                flex-shrink: 0;
            }

            @media (max-width: 767px) {
                .mm-page-header {
                    gap: 12px;
                }

                .mm-page-header__title {
                    font-size: 18px;
                }

                .mm-page-header--bordered {
                    margin: calc(var(--mm-ph-bleed-top-sm, 12px) * -1)
                            calc(var(--mm-ph-bleed-sm, 14px) * -1)
                            14px;
                    padding: 12px var(--mm-ph-bleed-sm, 14px);
                }
            }
        </style>
    @endpush
@endonce

<div {{ $attributes->class(['mm-page-header', 'mm-page-header--bordered' => $bordered]) }}>
    <div class="mm-page-header__lead">
        @isset($leading)
            <div class="mm-page-header__leading">{{ $leading }}</div>
        @endisset

        <div class="mm-page-header__text">
            <div class="mm-page-header__titlerow">
                <h1 class="mm-page-header__title">{{ $title }}</h1>
                @if($badge)
                    <span class="mm-page-header__badge">{{ $badge }}</span>
                @endif
            </div>

            @if($subtitle)
                <p class="mm-page-header__subtitle">{{ $subtitle }}</p>
            @elseif($slot->isNotEmpty())
                <p class="mm-page-header__subtitle">{{ $slot }}</p>
            @endif
        </div>
    </div>

    @isset($actions)
        <div class="mm-page-header__actions">{{ $actions }}</div>
    @endisset
</div>
