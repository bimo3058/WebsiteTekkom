{{--
    Palet & badge bab Layanan Pengaduan — satu sumber warna untuk daftar, detail,
    konfirmasi, form, dan halaman magic link, supaya status yang sama tidak lagi tampil
    dengan warna berbeda di tiap halaman.

    Token --c-* diambil dari palet Direktori (1:1 dengan SITKOM); layout mahasiswa dan
    layout magic link tidak mendeklarasikannya sendiri.

      .pgd-status.{baru|tercatat}         badge garis tepi + titik, meniru
                                          components/ui/status-badge SITKOM.
                                          Nada diambil dari Pengaduan::statusBadge();
                                          warnanya sama dengan partials/sitkom-ui
                                          (Baru = kuning "Menunggu", Tercatat = hijau).
      .pgd-pill.kategori                  pill terisi navy muda
      .pgd-pill.konfidensial              pill hitam
      .pgd-pill.jalur                     pill abu penanda jalur Reguler (bukan status)
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
        /* Nilai garis & titik = --c-warning-50/100 dan --c-success-50/100 di sitkom-ui. */
        .pgd-status.baru             { color: var(--c-warning); border-color: #FBD982; }
        .pgd-status.baru::before     { background: #FFBD4C; }
        .pgd-status.tercatat         { color: var(--c-success); border-color: #9DE0D3; }
        .pgd-status.tercatat::before { background: #40C4AA; }

        .pgd-pill {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 9999px;
            font-size: 11px; font-weight: 600; line-height: 1.4; white-space: nowrap;
        }
        .pgd-pill.kategori     { background: var(--c-primary-subtle); color: var(--c-primary); }
        .pgd-pill.konfidensial { background: #111827; color: #ffffff; }
        .pgd-pill.jalur        { background: var(--c-grey-50); color: var(--c-fg-sec); }
    </style>
@endonce
