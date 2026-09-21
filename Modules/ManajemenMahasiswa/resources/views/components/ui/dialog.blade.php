{{--
    Dialog konfirmasi & pemberitahuan bergaya shell global SITKOM.

    Menggantikan confirm() dan alert() bawaan browser di seluruh modul ini. Bentuknya
    mengikuti modal dashboard superadmin (resources/views/superadmin/users/_modal_suspend.blade.php):
    panel putih radius 16, kepala dengan lencana ikon bulat 40px sesuai jenis pesan,
    badan teks, lalu dua tombol selebar sama di kaki modal.

    Ditulis sebagai CSS biasa (bukan utilitas Tailwind milik versi global) karena layout
    modul ini memuat Bootstrap + CSS kustom, bukan Tailwind. Tiap token warna diberi nilai
    bawaan yang sama dengan palet global, jadi dialog ini tetap benar di halaman yang tidak
    mendeklarasikan token --c-*.

    Komponen ini TUNGGAL: cukup dipasang sekali di tiap layout, sebelum @stack('scripts').
    Semua halaman memakai instance yang sama lewat tiga fungsi global di bawah.

    ── Cara pakai ──────────────────────────────────────────────────────────────────────

    1. Form yang dulu memakai onsubmit="return confirm('...')":

        <form ... onsubmit="return mkConfirmSubmit(this, 'Hapus pengumuman ini?')">

       Varian & label tombol bisa diatur:

        onsubmit="return mkConfirmSubmit(this, 'Hapus thread ini secara permanen?', {
            title: 'Hapus Thread', variant: 'danger', confirmText: 'Ya, Hapus'
        })"

    2. Di dalam JS, menggantikan `if (!confirm(...)) return;` — fungsinya jadi async:

        if (!await mkConfirm({ message: 'Setujui pengumuman ini?' })) return;

    3. Menggantikan alert() — pemberitahuan dengan satu tombol:

        mkNotify({ message: 'Draf berhasil disimpan!', variant: 'success' });

    ── Opsi ────────────────────────────────────────────────────────────────────────────
      message      isi pesan; baris baru (\n) dipertahankan
      title        judul; bawaannya menyesuaikan varian
      subtitle     keterangan kecil di bawah judul (opsional)
      variant      danger (bawaan untuk konfirmasi) | primary | warning | success | info
                   Hanya mewarnai lencana ikon di kepala dialog. Tombolnya selalu
                   navy (utama) dan putih (batal), mengikuti sistem .mk-btn.
      confirmText  label tombol utama; bawaan "Ya, Lanjutkan" / "Mengerti"
      cancelText   label tombol batal; bawaan "Batal"

    mkConfirm() dan mkNotify() mengembalikan Promise — mkConfirm menghasilkan true/false,
    mkNotify menghasilkan true saat ditutup. mkConfirmSubmit() selalu mengembalikan false
    (menahan submit bawaan), lalu mengirim form sendiri lewat form.submit() bila disetujui.
--}}

<style>
    .mkd-overlay {
        position: fixed;
        inset: 0;
        z-index: 2000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background: rgba(13, 13, 18, 0.45);
    }

    .mkd-panel {
        width: 100%;
        max-width: 440px;
        background: #ffffff;
        border: 1px solid var(--c-border, #DFE1E7);
        border-radius: 16px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.18);
        overflow: hidden;
        font-family: inherit;
    }

    .mkd-head {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 18px 20px;
        border-bottom: 1px solid var(--c-border, #DFE1E7);
    }

    .mkd-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .mkd-titles { min-width: 0; flex: 1; }

    .mkd-title {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.35;
        color: var(--c-fg, #0D0D12);
    }

    .mkd-sub {
        margin: 2px 0 0;
        font-size: 12px;
        color: var(--c-fg-muted, #666D80);
    }

    .mkd-close {
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        padding: 0;
        background: none;
        border: none;
        border-radius: 8px;
        color: var(--c-fg-muted, #666D80);
        cursor: pointer;
        transition: background 0.15s, color 0.15s;
    }
    .mkd-close:hover { background: var(--c-bg, #F6F8FA); color: var(--c-fg, #0D0D12); }

    .mkd-body {
        padding: 18px 20px;
        font-size: 13.5px;
        line-height: 1.6;
        color: var(--c-fg-sec, #353849);
        /* Pesan lama memakai \n untuk memisah paragraf — dipertahankan apa adanya. */
        white-space: pre-line;
        word-break: break-word;
        max-height: 50vh;
        overflow-y: auto;
    }

    .mkd-foot {
        display: flex;
        gap: 10px;
        padding: 14px 20px 18px;
        border-top: 1px solid var(--c-border, #DFE1E7);
    }

    /* Tombol dialog memakai sistem .mk-btn milik modul (partials/button-theme).
       Kelas ini tinggal mengatur tata letaknya saja: dua tombol sama lebar. */
    .mkd-btn { flex: 1; }

    /* Warna per jenis pesan hanya untuk lencana ikon — TOMBOLNYA tetap navy/putih,
       mengikuti keputusan bahwa modul ini cuma punya dua warna tombol. */
    .mkd--danger  .mkd-icon { background: var(--c-error-subtle, #FADAE1);   color: var(--c-error, #DF1C41); }
    .mkd--warning .mkd-icon { background: var(--c-warning-subtle, #F9ECCB); color: var(--c-warning, #956321); }
    .mkd--success .mkd-icon { background: var(--c-success-subtle, #DDF2EE); color: var(--c-success, #287F6E); }
    .mkd--info    .mkd-icon { background: var(--c-sky-subtle, #D1F0F9);     color: var(--c-sky, #0C4D6E); }
    .mkd--primary .mkd-icon { background: var(--c-primary-subtle, rgba(11, 38, 110, 0.08)); color: var(--c-primary, #0B266E); }

    @media (max-width: 480px) {
        .mkd-foot { flex-direction: column-reverse; }
    }
</style>

<div x-data="mkDialog"
     x-show="open"
     x-cloak
     style="display: none;"
     class="mkd-overlay"
     :class="'mkd--' + variant"
     role="dialog"
     aria-modal="true"
     :aria-label="title"
     x-transition:enter="transition ease-out duration-150"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     @click.self="dismiss()"
     @keydown.escape.window="dismiss()">

    <div class="mkd-panel"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">

        <div class="mkd-head">
            <div class="mkd-icon">
                {{-- Ikon mengikuti jenis pesan; hanya satu yang tampil. --}}
                <svg x-show="variant === 'danger' || variant === 'warning'" width="20" height="20" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                <svg x-show="variant === 'success'" width="20" height="20" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6L9 17l-5-5"/>
                </svg>
                <svg x-show="variant === 'info' || variant === 'primary'" width="20" height="20" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
            </div>

            <div class="mkd-titles">
                <p class="mkd-title" x-text="title"></p>
                <p class="mkd-sub" x-show="subtitle" x-text="subtitle"></p>
            </div>

            <button type="button" class="mkd-close" @click="dismiss()" aria-label="Tutup">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     stroke-width="2" stroke-linecap="round">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="mkd-body" x-text="message"></div>

        <div class="mkd-foot">
            <button type="button" class="mkd-btn mk-btn mk-btn--secondary" x-show="mode === 'confirm'"
                    @click="decide(false)" x-text="cancelText"></button>

            <button type="button" class="mkd-btn mk-btn mk-btn--primary" x-ref="confirmBtn"
                    @click="decide(true)" x-text="confirmText"></button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', function () {
        /**
         * Dialog tunggal pengganti confirm()/alert() bawaan browser.
         *
         * Instance-nya didaftarkan ke window saat init supaya bisa dipanggil dari JS biasa
         * di halaman mana pun lewat mkConfirm/mkNotify/mkConfirmSubmit.
         */
        Alpine.data('mkDialog', function () {
            return {
                open: false,
                mode: 'confirm',       // 'confirm' (dua tombol) | 'notify' (satu tombol)
                variant: 'danger',
                title: '',
                subtitle: '',
                message: '',
                confirmText: '',
                cancelText: '',
                resolve: null,

                init() {
                    window.__mkDialog = this;
                },

                /** Menampilkan dialog dan menunggu jawaban pengguna. */
                ask(options) {
                    // Permintaan baru saat dialog lama masih terbuka: tutup yang lama dulu
                    // supaya promise-nya tidak menggantung selamanya.
                    if (this.resolve) this.decide(false);

                    const opts = options || {};

                    this.mode = opts.mode === 'notify' ? 'notify' : 'confirm';
                    this.variant = opts.variant || (this.mode === 'notify' ? 'info' : 'danger');
                    this.message = opts.message || '';
                    this.subtitle = opts.subtitle || '';
                    this.title = opts.title || (this.mode === 'notify' ? 'Pemberitahuan' : 'Konfirmasi');
                    this.confirmText = opts.confirmText || (this.mode === 'notify' ? 'Mengerti' : 'Ya, Lanjutkan');
                    this.cancelText = opts.cancelText || 'Batal';
                    this.open = true;

                    // Tombol utama difokuskan supaya Enter/Esc langsung bisa dipakai.
                    this.$nextTick(() => this.$refs.confirmBtn && this.$refs.confirmBtn.focus());

                    return new Promise((resolve) => { this.resolve = resolve; });
                },

                /** Menutup dialog sambil mengirim jawaban ke pemanggil. */
                decide(answer) {
                    this.open = false;

                    const done = this.resolve;
                    this.resolve = null;

                    if (done) done(answer);
                },

                /** Esc, klik latar, atau tombol tutup: dianggap membatalkan. */
                dismiss() {
                    // Pada mode pemberitahuan tidak ada yang dibatalkan, jadi tetap true.
                    this.decide(this.mode === 'notify');
                },
            };
        });
    });

    /**
     * Konfirmasi bergaya SITKOM; pengganti confirm().
     *
     * @param {Object|string} options Pesan, atau objek opsi (message, title, variant, dll.)
     * @returns {Promise<boolean>} true bila pengguna menyetujui
     */
    window.mkConfirm = function (options) {
        const opts = typeof options === 'string' ? { message: options } : (options || {});

        // Jaring pengaman bila Alpine belum sempat jalan (mis. skrip halaman yang
        // dieksekusi lebih awal): jangan sampai aksinya lolos tanpa konfirmasi.
        if (!window.__mkDialog) return Promise.resolve(window.confirm(opts.message || ''));

        return window.__mkDialog.ask(Object.assign({ mode: 'confirm' }, opts));
    };

    /**
     * Pemberitahuan satu tombol; pengganti alert().
     *
     * @param {Object|string} options Pesan, atau objek opsi (message, title, variant, dll.)
     * @returns {Promise<boolean>}
     */
    window.mkNotify = function (options) {
        const opts = typeof options === 'string' ? { message: options } : (options || {});

        if (!window.__mkDialog) { window.alert(opts.message || ''); return Promise.resolve(true); }

        return window.__mkDialog.ask(Object.assign({ mode: 'notify' }, opts));
    };

    /**
     * Pengganti onsubmit="return confirm('...')".
     *
     * Selalu mengembalikan false untuk menahan submit bawaan, lalu mengirim form sendiri
     * memakai form.submit() bila pengguna menyetujui — form.submit() tidak memicu ulang
     * onsubmit, jadi tidak ada perulangan.
     *
     * @param {HTMLFormElement} form Form yang akan dikirim
     * @param {string} message Pesan konfirmasi
     * @param {Object} [options] Opsi tambahan (title, variant, confirmText, ...)
     * @returns {boolean} selalu false
     */
    window.mkConfirmSubmit = function (form, message, options) {
        window.mkConfirm(Object.assign({ message: message }, options || {}))
            .then(function (ok) { if (ok) form.submit(); });

        return false;
    };
</script>
