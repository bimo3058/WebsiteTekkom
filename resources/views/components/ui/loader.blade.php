{{-- resources/views/components/ui/loader.blade.php --}}

{{-- Thin navigation progress bar (link clicks, full-page navigations) --}}
<style>
    #nav-progress {
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: linear-gradient(90deg, #0B266E, #3C518B);
        z-index: 10000;
        transition: width 0.2s ease, opacity 0.3s ease;
        opacity: 0;
        pointer-events: none;
    }
    #nav-progress.active {
        opacity: 1;
    }
</style>
<div id="nav-progress"></div>

{{-- Full-screen overlay loader (forms & AJAX) --}}
<div id="global-loader" class="fixed inset-0 z-[9999] hidden flex-col items-center justify-center bg-slate-900/40 backdrop-blur-sm transition-opacity duration-300">
    <div class="flex flex-col items-center gap-4 rounded-2xl bg-white p-8 shadow-2xl transform scale-95 transition-transform duration-300 loader-content">
        <div class="relative flex h-12 w-12 items-center justify-center">
            <svg class="h-12 w-12 animate-spin text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <div class="text-center">
            <h3 class="text-base font-semibold text-slate-800">Memproses...</h3>
            <p class="text-sm text-slate-500 mt-1">Mohon tunggu sebentar.</p>
        </div>
    </div>
</div>

<script>
(function () {
    var loader        = document.getElementById('global-loader');
    var loaderContent = loader.querySelector('.loader-content');
    var navBar        = document.getElementById('nav-progress');
    var navTimer      = null;
    var safetyTimer   = null;
    var navWidth      = 0;

    // ─── Full-screen overlay (forms + AJAX) ──────────────────────────────────

    loader.classList.add('opacity-0');

    window.showGlobalLoader = function () {
        clearTimeout(safetyTimer);
        loader.classList.remove('hidden');
        setTimeout(function () {
            loader.classList.remove('opacity-0');
            loaderContent.classList.remove('scale-95');
            loaderContent.classList.add('scale-100');
        }, 10);
        // Safety: auto-hide after 30 s to prevent stuck loader
        safetyTimer = setTimeout(window.hideGlobalLoader, 30000);
    };

    window.hideGlobalLoader = function () {
        clearTimeout(safetyTimer);
        loader.classList.add('opacity-0');
        loaderContent.classList.remove('scale-100');
        loaderContent.classList.add('scale-95');
        setTimeout(function () { loader.classList.add('hidden'); }, 300);
    };

    // Show on form submits (skip data-no-loader forms)
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form:not([data-no-loader])').forEach(function (form) {
            form.addEventListener('submit', function () {
                if (!this.checkValidity || this.checkValidity()) {
                    showGlobalLoader();
                }
            });
        });
    });

    // Intercept fetch
    var originalFetch = window.fetch;
    window.fetch = function () {
        var args    = Array.prototype.slice.call(arguments);
        var options = args[1] || {};
        if (options.headers && options.headers['X-No-Loader']) {
            return originalFetch.apply(this, args);
        }
        showGlobalLoader();
        return originalFetch.apply(this, args).finally(hideGlobalLoader);
    };

    // Axios interceptors
    if (window.axios) {
        window.axios.interceptors.request.use(function (config) {
            if (!config.headers['X-No-Loader']) showGlobalLoader();
            return config;
        }, function (error) {
            hideGlobalLoader();
            return Promise.reject(error);
        });
        window.axios.interceptors.response.use(function (response) {
            hideGlobalLoader();
            return response;
        }, function (error) {
            hideGlobalLoader();
            return Promise.reject(error);
        });
    }

    // ─── Thin progress bar (link/page navigations) ───────────────────────────

    function navStart() {
        clearInterval(navTimer);
        navWidth = 0;
        navBar.style.width = '0%';
        navBar.classList.add('active');
        // Creep slowly to 85 % while page loads
        navTimer = setInterval(function () {
            if (navWidth < 85) {
                navWidth += (85 - navWidth) * 0.08;
                navBar.style.width = navWidth + '%';
            }
        }, 120);
    }

    function navDone() {
        clearInterval(navTimer);
        navWidth = 100;
        navBar.style.width = '100%';
        setTimeout(function () {
            navBar.classList.remove('active');
            setTimeout(function () { navBar.style.width = '0%'; }, 300);
        }, 200);
    }

    // Show nav bar on any <a> click that would cause full-page navigation
    document.addEventListener('click', function (e) {
        var anchor = e.target.closest('a[href]');
        if (!anchor) return;
        var href = anchor.getAttribute('href');
        // Skip: blank targets, external links, hash-only links, mailto/tel
        if (!href
            || anchor.target === '_blank'
            || href.startsWith('#')
            || href.startsWith('mailto:')
            || href.startsWith('tel:')
            || anchor.hasAttribute('data-no-loader')) return;

        // Skip external URLs
        try {
            var url = new URL(href, window.location.href);
            if (url.hostname !== window.location.hostname) return;
        } catch (_) { return; }

        navStart();
    });

    // Hide nav bar when page is loaded
    window.addEventListener('pageshow', navDone);
    window.addEventListener('load', navDone);
}());
</script>
