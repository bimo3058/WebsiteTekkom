{{--
    Kerangka "satu kotak" bab Pengaduan, meniru halaman Detail User & Edit User SITKOM
    (resources/views/superadmin/users/show.blade.php, edit.blade.php):

        .kf-box        kotak putih radius 12
        .kf-toolbar    bilah atas: tombol kembali + judul huruf besar | aksi
        .kf-profile    ikon 56px + judul + badge + grid label–nilai dua kolom
        .kf-split      bagian terbelah: kolom keterangan 240px | isi
        .kf-footer     kaki kotak berlatar abu tipis

    Dipakai halaman Detail, Lacak (magic link), Konfirmasi, dan form Buat Pengaduan.
    Gaya field form (.pgd-field / .pgd-input) mengikuti .input-group/.input-field
    di Edit User SITKOM.
--}}
@include('manajemenmahasiswa::pengaduan.partials.palette')

@once
    <style>
        .kf-box {
            background: #ffffff; border: 1px solid var(--c-border); border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06); overflow: hidden; font-family: 'Inter Tight', sans-serif;
            /* Anak langsung .main-wrapper (flex kolom ber-scroll): tanpa ini kotak ikut
               menyusut setinggi layar dan isi bawahnya terpotong oleh overflow:hidden. */
            flex-shrink: 0;
        }

        /* ── Bilah atas ── */
        .kf-toolbar {
            display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;
            padding: 12px 16px; background: #ffffff; border-bottom: 1px solid var(--c-border);
        }
        .kf-toolbar-lead { display: flex; align-items: center; gap: 12px; min-width: 0; }
        .kf-toolbar-title {
            font-size: 14px; font-weight: 800; color: var(--c-fg); margin: 0;
            text-transform: uppercase; letter-spacing: .02em;
        }
        .kf-toolbar-id { font-family: monospace; font-size: 12px; font-weight: 600; color: var(--c-fg-muted); }
        .kf-toolbar-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .kf-toolbar-actions form { margin: 0; }
        /* Tombol kembali ikon saja 32px, seperti Detail User SITKOM. */
        .kf-back {
            display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;
            width: 32px; height: 32px; border-radius: 8px; background: #ffffff;
            border: 1px solid var(--c-border); color: var(--c-fg-sec);
            box-shadow: 0 1px 2px rgba(0,0,0,.05); text-decoration: none; transition: all .15s;
        }
        .kf-back:hover { background: var(--c-bg); color: var(--c-fg); border-color: var(--c-border-strong); }

        .kf-notice {
            display: flex; align-items: center; gap: 8px; padding: 10px 20px;
            background: var(--c-warning-subtle); color: var(--c-warning);
            font-size: 13px; font-weight: 600; border-bottom: 1px solid #EBCB8B;
        }
        .kf-alerts { padding: 16px 20px 0; }
        .kf-alerts:empty { display: none; }

        /* ── Bagian profil ── */
        .kf-profile { display: flex; gap: 20px; align-items: flex-start; padding: 20px; border-bottom: 1px solid var(--c-border); }
        .kf-icon {
            width: 56px; height: 56px; border-radius: 14px; flex-shrink: 0;
            background: var(--c-primary-subtle); color: var(--c-primary);
            display: flex; align-items: center; justify-content: center;
        }
        .kf-icon.is-konfidensial { background: var(--c-grey-50); color: var(--c-fg-sec); }
        .kf-heading { display: flex; align-items: center; flex-wrap: wrap; gap: 8px 10px; margin-bottom: 4px; }
        .kf-subject { font-size: 20px; font-weight: 800; color: var(--c-fg); margin: 0; letter-spacing: -.02em; overflow-wrap: anywhere; }
        .kf-sub { font-size: 13px; font-weight: 500; color: var(--c-fg-muted); margin: 0 0 16px; }

        .kf-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px 40px; max-width: 900px; }
        .kf-row { display: flex; align-items: baseline; min-width: 0; }
        /* Lebar cukup untuk label pertanyaan terpanjang ("Nama Kegiatan / Organisasi"). */
        .kf-label { width: 180px; padding-right: 12px; box-sizing: border-box; font-size: 13px; line-height: 1.4; color: var(--c-fg-placeholder); flex-shrink: 0; font-weight: 500; }
        .kf-value { font-size: 13px; font-weight: 600; color: var(--c-fg-sec); min-width: 0; overflow-wrap: anywhere; }
        .kf-value.is-empty { color: var(--c-border-strong); }

        /* ── Bagian terbelah ── */
        .kf-split { display: flex; border-bottom: 1px solid var(--c-border); }
        .kf-split:last-child { border-bottom: none; }
        .kf-side { width: 240px; padding: 20px; border-right: 1px solid var(--c-border); background: #ffffff; flex-shrink: 0; }
        .kf-side h3 { font-size: 14px; font-weight: 800; color: var(--c-fg); margin: 0 0 8px; }
        .kf-side p { font-size: 12px; font-weight: 500; color: var(--c-fg-muted); line-height: 1.6; margin: 0; }
        .kf-main {
            flex: 1; padding: 20px; min-width: 0; font-size: 14px; font-weight: 500;
            color: var(--c-fg); line-height: 1.7; white-space: pre-wrap; overflow-wrap: anywhere;
        }
        .kf-main.is-fields { white-space: normal; line-height: normal; }
        .kf-empty { color: var(--c-border-strong); }

        .kf-footer {
            display: flex; justify-content: flex-end; gap: 10px; padding: 14px 20px;
            background: #FAFAFA; border-top: 1px solid var(--c-border);
        }

        /* ── Field form, meniru redesign Edit User SITKOM (superadmin/users/_edit-style):
              label 12px huruf biasa, kotak 38px radius 7px ── */
        .pgd-fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px 24px; }
        .pgd-fields.is-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .pgd-fields + .pgd-fields { margin-top: 16px; }
        .pgd-field.is-wide { grid-column: 1 / -1; }
        .pgd-label {
            display: block; margin-bottom: 6px; font-size: 12px; font-weight: 500; line-height: 1.4;
            color: var(--c-fg-sec); text-transform: none; letter-spacing: normal;
        }
        .pgd-label .is-req { color: var(--c-error); }
        .pgd-label .is-opt { font-weight: 400; color: var(--c-fg-placeholder); }
        .pgd-input {
            display: block; width: 100%; box-sizing: border-box; min-height: 38px; padding: 8px 11px;
            font-family: inherit; font-size: 13px; font-weight: 400; color: var(--c-fg);
            background: #ffffff; border: 1px solid var(--c-border); border-radius: 7px;
            outline: none; transition: border-color .15s, box-shadow .15s;
        }
        .pgd-input::placeholder { color: var(--c-fg-placeholder); font-weight: 400; }
        .pgd-input:focus { border-color: var(--c-primary); box-shadow: 0 0 0 3px rgba(11, 38, 110, .08); }
        /* Dropdown Alpine di form disamakan tinggi & sudutnya dengan kotak isian. */
        .kf-box .mk-select.mk-select--md { --mks-h: 38px; --mks-radius: 7px; --mks-font: 13px; --mks-pad: 11px; }
        .pgd-input:disabled { background: var(--c-bg); color: var(--c-fg-muted); }
        .pgd-input.is-invalid { border-color: var(--c-error); }
        textarea.pgd-input { resize: vertical; line-height: 1.6; }
        .pgd-help { margin-top: 5px; font-size: 11px; color: var(--c-fg-muted); }
        .pgd-help-row { display: flex; justify-content: space-between; gap: 12px; }
        .pgd-help-row [data-pgd-count] { font-variant-numeric: tabular-nums; flex-shrink: 0; }

        /* Set pertanyaan per kategori (Detail Kejadian) */
        .pgd-set { border: 0; margin: 0; padding: 0; min-width: 0; }
        .pgd-set[hidden] { display: none; }
        .pgd-set-empty {
            display: flex; align-items: center; gap: 8px; padding: 14px 16px;
            border: 1px dashed var(--c-border-strong); border-radius: 10px;
            background: var(--c-bg); color: var(--c-fg-muted); font-size: 13px; font-weight: 500;
        }
        .pgd-set-empty[hidden] { display: none; }
        .pgd-sr-only {
            position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
            overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0;
        }
        .pgd-error { margin-top: 5px; font-size: 11px; font-weight: 600; color: var(--c-error); }

        /* Kartu pilihan kategori */
        .pgd-kategori-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .pgd-kategori {
            display: flex; align-items: flex-start; gap: 10px; margin: 0; padding: 12px 14px;
            border: 1px solid var(--c-border); border-radius: 10px; background: #ffffff;
            cursor: pointer; transition: border-color .15s, background .15s;
        }
        .pgd-kategori:hover { border-color: var(--c-border-strong); }
        .pgd-kategori:has(:checked) { border-color: var(--c-primary); background: var(--c-primary-subtle); }
        .pgd-kategori input { margin-top: 2px; width: 16px; height: 16px; flex-shrink: 0; accent-color: var(--c-primary); cursor: pointer; }
        .pgd-kategori-title { display: block; font-size: 13px; font-weight: 700; color: var(--c-fg); }
        .pgd-kategori-desc { display: block; margin-top: 2px; font-size: 11px; line-height: 1.45; color: var(--c-fg-muted); }

        /* Keterangan jalur pelaporan */
        .pgd-jalur { display: flex; align-items: center; gap: 12px; }
        .pgd-jalur-icon {
            width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            background: var(--c-primary-subtle); color: var(--c-primary);
        }
        .pgd-jalur-icon.is-konfidensial { background: var(--c-grey-50); color: var(--c-fg-sec); }
        .pgd-jalur-title { font-size: 14px; font-weight: 700; color: var(--c-fg); }
        .pgd-jalur-desc { font-size: 12px; color: var(--c-fg-muted); }
        .pgd-jalur-desc a { color: var(--c-primary); font-weight: 600; }

        @media (max-width: 768px) {
            .kf-profile { flex-direction: column; }
            .kf-grid { grid-template-columns: 1fr; }
            .kf-split { flex-direction: column; }
            .kf-side { width: auto; border-right: none; border-bottom: 1px solid var(--c-border); padding-bottom: 12px; }
            .pgd-fields, .pgd-fields.is-3, .pgd-kategori-grid { grid-template-columns: 1fr; }
        }
    </style>
@endonce
