{{--
    Palet & badge bab Layanan Pengaduan — satu sumber warna untuk daftar, detail,
    konfirmasi, form, dan halaman magic link, supaya status yang sama tidak lagi tampil
    dengan warna berbeda di tiap halaman.

    Token --c-* diambil dari palet Direktori (1:1 dengan SITKOM); layout mahasiswa dan
    layout magic link tidak mendeklarasikannya sendiri.

      .pgd-status.{baru|dibaca|tercatat}  badge garis tepi + titik, meniru
                                          components/ui/status-badge SITKOM.
                                          Nada diambil dari Pengaduan::statusBadge().
      .pgd-pill.kategori                  pill terisi navy muda
      .pgd-pill.konfidensial              pill hitam
--}}
@once
    @include('manajemenmahasiswa::direktori.partials.palette')

    <style>
        .pgd-status {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 12px; border: 1px solid; border-radius: 9999px;
            font-size: 11px; font-weight: 500; line-height: 1.4; white-space: nowrap;
            background: #ffffff;
        }
        .pgd-status::before {
            content: ''; width: 6px; height: 6px; border-radius: 50%;
            background: currentColor; flex-shrink: 0;
        }
        .pgd-status.baru     { color: var(--c-warning); border-color: #EBCB8B; }
        .pgd-status.dibaca   { color: var(--c-fg-muted); border-color: var(--c-border); }
        .pgd-status.dibaca::before { background: var(--c-fg-placeholder); }
        .pgd-status.tercatat { color: var(--c-success); border-color: #9DE0D3; }

        .pgd-pill {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 9999px;
            font-size: 11px; font-weight: 600; line-height: 1.4; white-space: nowrap;
        }
        .pgd-pill.kategori     { background: var(--c-primary-subtle); color: var(--c-primary); }
        .pgd-pill.konfidensial { background: #111827; color: #ffffff; }
    </style>
@endonce
