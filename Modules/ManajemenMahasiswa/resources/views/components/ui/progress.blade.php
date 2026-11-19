{{--
    Progress bar tipis di tepi atas layar — meniru indikator loading SICATA
    (Modules/Capstone: .capstone-progress). Muncul saat:
      - klik tautan ke halaman lain di origin yang sama,
      - submit form (yang tidak dibatalkan, mis. oleh mkConfirmSubmit),
      - fetch() yang dipicu user masih berjalan.
    Polling notifikasi topbar & autosave draf sengaja tidak dihitung (aktivitas latar).
    Tautan/form bisa dikecualikan manual dengan atribut data-no-progress.

    Komponen TUNGGAL: pasang sekali per layout, sebelum @stack('scripts').
--}}
<div id="mk-progress" class="mk-progress" role="status" aria-label="Sedang memuat" hidden>
    <span class="mk-progress-bar" aria-hidden="true"></span>
</div>

<style>
    .mk-progress { position: fixed; inset: 0 0 auto; height: 3px; z-index: 2000; overflow: hidden; pointer-events: none; background: rgba(11, 38, 110, .12); }
    .mk-progress[hidden] { display: none; }
    .mk-progress-bar { display: block; width: 40%; height: 100%; background: var(--c-primary, #0B266E); animation: mk-progress 1.2s ease-in-out infinite; }
    @keyframes mk-progress { from { transform: translateX(-100%); } to { transform: translateX(350%); } }
    @media (prefers-reduced-motion: reduce) {
        .mk-progress-bar { animation: none; width: 100%; opacity: .65; }
    }
</style>

<script>
(() => {
    const bar = document.getElementById('mk-progress');
    if (!bar || window.mkProgress) return;

    let pending = 0, navigating = false, navTimer = null;
    const render = () => { bar.hidden = !(navigating || pending > 0); };

    // shortcut: unduhan (CV, lampiran) tidak meninggalkan halaman sehingga tak ada event selesai;
    // bar disembunyikan otomatis setelah 10 dtk. Tandai tautannya dengan data-no-progress bila mengganggu.
    const startNav = () => {
        navigating = true; render();
        clearTimeout(navTimer);
        navTimer = setTimeout(() => { navigating = false; render(); }, 10000);
    };

    // Kembali lewat tombol Back (bfcache) memulihkan halaman lama beserta bar yang masih tampil.
    window.addEventListener('pageshow', () => { navigating = false; pending = 0; render(); });

    document.addEventListener('click', (e) => {
        if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        const a = e.target.closest('a[href]');
        if (!a || a.hasAttribute('download') || a.hasAttribute('data-no-progress') || a.hasAttribute('data-bs-toggle')) return;
        if (a.target && a.target !== '_self') return;
        const url = new URL(a.href, location.href);
        if (url.origin !== location.origin || !/^https?:$/.test(url.protocol)) return;
        if (url.pathname === location.pathname && url.search === location.search && url.hash) return; // anchor di halaman yang sama
        startNav();
    });

    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (e.defaultPrevented || form.hasAttribute('data-no-progress')) return;
        if (form.target && form.target !== '_self') return;
        startNav();
    });

    const BACKGROUND = /\/(notifications|drafts)(\/|\?|$)/;
    const nativeFetch = window.fetch.bind(window);
    window.fetch = (input, init) => {
        const url = typeof input === 'string' ? input : (input && input.url) || '';
        if (BACKGROUND.test(url)) return nativeFetch(input, init);
        pending++; render();
        return nativeFetch(input, init).finally(() => { pending = Math.max(0, pending - 1); render(); });
    };
    window.mkProgress = true;
})();
</script>
