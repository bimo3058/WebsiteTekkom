{{--
    Baris filter kategori — satu kategori satu tombol.

    Menggantikan dropdown lama supaya seluruh pilihan langsung terlihat tanpa
    perlu dibuka dulu. Dipakai bersama oleh halaman pengumuman admin, staff,
    dan mahasiswa supaya tampilannya seragam di semua role.

    Nilainya dikirim lewat input hidden, bukan lewat name/value tombol submit:
    kalau tombolnya bertipe submit, menekan Enter di kotak pencarian akan
    memakai tombol pertama sebagai tombol default dan diam-diam mereset
    filter ke "Semua".

    Parameter:
      $selectedKategori  string  kategori yang sedang aktif
      $formId            string  id form filter yang disubmit saat tombol diklik
--}}
@php
    $kategoriFilter = [
        'semua'       => 'Semua',
        'akademik'    => 'Akademik',
        'himpunan'    => 'Himpunan',
        'lowongan'    => 'Lowongan',
        'event_prodi' => 'Event Prodi',
    ];
@endphp

<div class="pg-filter-bar">
    <span class="pg-filter-caption">Kategori</span>
    <div class="pg-filter-chips">
        <input type="hidden" name="kategori" id="pgKategoriInput" value="{{ $selectedKategori }}">
        @foreach($kategoriFilter as $value => $label)
            <button type="button"
                class="pg-chip {{ $selectedKategori === $value ? 'is-active' : '' }}"
                aria-pressed="{{ $selectedKategori === $value ? 'true' : 'false' }}"
                onclick="pgPilihKategori('{{ $value }}', '{{ $formId }}')">
                {{ $label }}
            </button>
        @endforeach
    </div>
</div>

@once
    @push('styles')
        <style>
            /* ── Filter kategori: satu kategori satu tombol ──────────────── */
            .pg-filter-bar {
                display: flex; align-items: center; gap: 10px;
                flex-wrap: wrap; margin-bottom: 10px;
            }
            .pg-filter-caption {
                font-size: 10px; font-weight: 700; letter-spacing: .08em;
                text-transform: uppercase; color: var(--c-fg-muted, #666D80);
            }
            .pg-filter-chips { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }

            .pg-chip {
                height: 32px; padding: 0 14px; border-radius: 8px;
                border: 1px solid var(--c-border, #DFE1E7); background: #fff;
                font-family: inherit; font-size: 12px; font-weight: 600;
                color: var(--c-fg-sec, #353849);
                cursor: pointer; white-space: nowrap; transition: all .15s;
                box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
            }
            .pg-chip:hover {
                background: var(--c-bg, #F6F8FA);
                border-color: var(--c-border-strong, #C1C7CF);
                color: var(--c-fg, #0D0D12);
            }
            .pg-chip:focus-visible {
                outline: none;
                border-color: var(--c-primary, #0B266E);
                box-shadow: 0 0 0 3px var(--c-primary-subtle, rgba(11, 38, 110, .08));
            }
            .pg-chip.is-active {
                background: var(--c-primary, #0B266E);
                border-color: var(--c-primary, #0B266E);
                color: #fff;
                box-shadow: 0 1px 2px rgba(11, 38, 110, .25);
            }
            .pg-chip.is-active:hover {
                background: var(--c-primary-hover, #091958);
                border-color: var(--c-primary-hover, #091958);
                color: #fff;
            }

            /* Di layar sempit tombol digeser horizontal, bukan dibuat menumpuk */
            @media (max-width: 575px) {
                .pg-filter-caption { width: 100%; }
                .pg-filter-chips {
                    width: 100%; flex-wrap: nowrap;
                    overflow-x: auto; padding-bottom: 2px;
                    scrollbar-width: none;
                }
                .pg-filter-chips::-webkit-scrollbar { display: none; }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            /** Pilih kategori lalu kirim ulang form filter. */
            function pgPilihKategori(value, formId) {
                const input = document.getElementById('pgKategoriInput');
                if (!input) return;
                input.value = value;
                document.getElementById(formId)?.submit();
            }
        </script>
    @endpush
@endonce
