{{--
    Gaya daftar berkas bukti (ikon jenis + nama + ukuran), mengikuti daftar berkas
    "Ajukan Riwayat Kegiatan" di Verifikasi Kegiatan, ditambah pop-up penampil berkas.
    Dipakai bersama oleh input form (pratinjau), konfirmasi, dan halaman detail;
    @once agar tidak tercetak ganda.

    Pop-up: elemen apa pun ber-atribut data-bk-view="<url>" (+ data-bk-title) yang diklik
    membuka penampil; window.bkOpenViewer(url, title) dipakai untuk berkas yang belum diunggah.
--}}
@once
    <style>
        /* Input berkas bawaan disembunyikan tapi tetap bisa difokus keyboard. */
        .bk-file-native {
            position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
            overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; border: 0;
        }
        .bk-picker {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 18px; border-radius: 12px;
            border: 1px dashed #c7cbd4; background: #f9fafb;
            color: #4b5563; font-size: 13px; font-weight: 600;
            cursor: pointer; transition: all .2s; margin: 0;
        }
        .bk-picker:hover { background: #fff; border-color: var(--c-primary, #0B266E); color: var(--c-primary, #0B266E); }
        .bk-file-native:focus-visible + .bk-picker { outline: 2px solid var(--c-primary, #0B266E); outline-offset: 2px; }

        .bk-list { display: flex; flex-direction: column; gap: 6px; margin-top: 10px; }
        .bk-list:empty { display: none; }
        .bk-row {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 12px; background: #fafafa;
            border: 1px solid var(--c-border, #DFE1E7); border-radius: 10px;
            font-size: .87rem; text-decoration: none; color: inherit;
        }
        a.bk-row, .bk-row[data-bk-view] { cursor: pointer; transition: border-color .15s, background .15s; }
        a.bk-row:hover, .bk-row[data-bk-view]:hover { background: #fff; border-color: var(--c-primary, #0B266E); }
        .bk-icon {
            width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: .68rem; color: #fff; background: #6b7280;
        }
        .bk-icon.pdf { background: #dc2626; }
        .bk-icon.image { background: #2563eb; }
        .bk-text { flex: 1; min-width: 0; line-height: 1.3; }
        .bk-name { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #374151; font-weight: 600; }
        .bk-sub { display: block; font-size: 11.5px; color: #9ca3af; }
        .bk-open { flex-shrink: 0; color: #9ca3af; display: flex; }
        a.bk-row:hover .bk-open, .bk-row[data-bk-view]:hover .bk-open { color: var(--c-primary, #0B266E); }
        .bk-remove {
            width: 20px; height: 20px; border-radius: 50%; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;
            font-size: .8rem; font-weight: 700; cursor: pointer; padding: 0; line-height: 1;
        }
        .bk-remove:hover { background: #fee2e2; }
        .bk-error { margin-top: 8px; font-size: 12.5px; font-weight: 600; color: #dc2626; }
        .bk-error:empty { display: none; }

        /* ── Pop-up penampil berkas ── */
        .bk-viewer {
            position: fixed; inset: 0; z-index: 2000;
            display: none; align-items: center; justify-content: center;
            padding: 24px; background: rgba(15, 23, 42, .6);
        }
        .bk-viewer.is-open { display: flex; }
        .bk-viewer-dialog {
            display: flex; flex-direction: column;
            width: min(960px, 100%); height: min(90vh, 900px);
            background: #fff; border-radius: 14px; overflow: hidden;
            box-shadow: 0 20px 50px rgba(15, 23, 42, .35);
        }
        .bk-viewer-head {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 16px; border-bottom: 1px solid var(--c-border, #DFE1E7);
        }
        .bk-viewer-title { flex: 1; min-width: 0; margin: 0; font-size: 15px; font-weight: 700; color: #111827; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .bk-viewer-close {
            width: 32px; height: 32px; flex-shrink: 0; border-radius: 8px;
            border: 1px solid #DFE1E7; background: #fff; color: #353849;
            font-size: 20px; line-height: 1; cursor: pointer;
        }
        .bk-viewer-close:hover { background: #F6F8FA; }
        .bk-viewer-frame { flex: 1; width: 100%; border: 0; background: #F1F5F9; }
        .bk-viewer-foot { padding: 8px 16px; border-top: 1px solid var(--c-border, #DFE1E7); font-size: 12px; color: #6b7280; }
        .bk-viewer-foot a { color: var(--c-primary, #0B266E); font-weight: 600; }
        @media (max-width: 576px) { .bk-viewer { padding: 0; } .bk-viewer-dialog { height: 100%; border-radius: 0; } }
    </style>

    <div class="bk-viewer" id="bkViewer" role="dialog" aria-modal="true" aria-labelledby="bkViewerTitle" hidden>
        <div class="bk-viewer-dialog">
            <div class="bk-viewer-head">
                <h5 class="bk-viewer-title" id="bkViewerTitle">Bukti</h5>
                <button type="button" class="bk-viewer-close" id="bkViewerClose" aria-label="Tutup">&times;</button>
            </div>
            <iframe class="bk-viewer-frame" id="bkViewerFrame" title="Pratinjau bukti"></iframe>
            <div class="bk-viewer-foot">
                PDF tidak tampil di perangkat Anda? <a href="#" id="bkViewerFallback" target="_blank" rel="noopener noreferrer">Buka di tab baru</a>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var viewer = document.getElementById('bkViewer');
            var frame = document.getElementById('bkViewerFrame');
            var title = document.getElementById('bkViewerTitle');
            var fallback = document.getElementById('bkViewerFallback');
            if (!viewer) return;

            // Pindahkan ke <body> agar tidak terjebak di dalam <form>/kontainer bertransform.
            document.body.appendChild(viewer);

            var current = null;   // URL yang sedang tampil
            var lastFocus = null; // elemen pemicu, agar fokus kembali setelah ditutup

            function open(url, label) {
                release();
                current = url;
                lastFocus = document.activeElement;
                title.textContent = label || 'Bukti';
                frame.src = url;
                fallback.href = url;
                viewer.hidden = false;
                viewer.classList.add('is-open');
                document.body.style.overflow = 'hidden';
                document.getElementById('bkViewerClose').focus();
            }

            // Berkas yang belum diunggah ditampilkan lewat blob URL; tanpa dilepas,
            // isinya tetap tertahan di memori peramban selama halaman terbuka.
            function release() {
                if (current && current.indexOf('blob:') === 0) URL.revokeObjectURL(current);
                current = null;
            }

            function close() {
                viewer.classList.remove('is-open');
                viewer.hidden = true;
                frame.src = 'about:blank';
                release();
                document.body.style.overflow = '';
                if (lastFocus && lastFocus.focus) lastFocus.focus();
                lastFocus = null;
            }

            window.bkOpenViewer = open;

            document.addEventListener('click', function (e) {
                var trigger = e.target.closest('[data-bk-view]');
                if (trigger && !e.target.closest('.bk-remove')) {
                    e.preventDefault();
                    open(trigger.getAttribute('data-bk-view'), trigger.getAttribute('data-bk-title'));
                }
            });
            viewer.addEventListener('click', function (e) { if (e.target === viewer) close(); });
            document.getElementById('bkViewerClose').addEventListener('click', close);
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && viewer.classList.contains('is-open')) close(); });
        })();
    </script>
@endonce
