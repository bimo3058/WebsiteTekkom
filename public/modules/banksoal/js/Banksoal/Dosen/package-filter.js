(() => {
    const form = document.getElementById('packageFilterForm');
    const results = document.getElementById('packageResults');
    if (!form || !results) return;

    const search = form.querySelector('[name="searchPackages"]');
    const reset = document.getElementById('resetPackageFilter');
    const status = document.getElementById('packageFilterStatus');
    let debounceTimer;
    let pendingRequest;

    function searchUrl() {
        const url = new URL(window.location.href);
        const term = search.value.trim();
        if (term) url.searchParams.set('searchPackages', term);
        else url.searchParams.delete('searchPackages');
        url.searchParams.delete('pkg_page');
        return url;
    }

    async function updatePackages(url) {
        clearTimeout(debounceTimer);
        pendingRequest?.abort();
        const request = new AbortController();
        pendingRequest = request;
        results.setAttribute('aria-busy', 'true');
        status.textContent = 'Memuat paket soal...';
        status.hidden = false;

        try {
            const response = await fetch(url, {
                headers: { Accept: 'text/html', 'X-Requested-With': 'XMLHttpRequest' },
                signal: request.signal,
            });
            if (!response.ok || response.redirected) throw new Error('Request failed');
            const html = await response.text();
            if (request.signal.aborted) return;
            const page = new DOMParser().parseFromString(html, 'text/html');
            const updatedResults = page.getElementById('packageResults');
            if (!updatedResults) throw new Error('Missing package results');

            // Only replace the results; retain focus, modal state and the question table.
            results.innerHTML = updatedResults.innerHTML;
            reset.hidden = !url.searchParams.has('searchPackages');
            window.history.replaceState(window.history.state, '', url);
            status.textContent = 'Daftar paket soal diperbarui.';
        } catch (error) {
            if (request.signal.aborted) return;
            status.textContent = 'Paket soal gagal dimuat. Klik Filter untuk mencoba kembali.';
        } finally {
            if (pendingRequest === request) {
                results.setAttribute('aria-busy', 'false');
                pendingRequest = null;
            }
        }
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        updatePackages(searchUrl());
    });

    search.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        // Discard responses for the previous term as soon as the user types again.
        pendingRequest?.abort();
        reset.hidden = !search.value.trim();
        debounceTimer = setTimeout(() => updatePackages(searchUrl()), 600);
    });

    reset.addEventListener('click', () => {
        search.value = '';
        updatePackages(searchUrl());
        search.focus();
    });

    results.addEventListener('click', (event) => {
        const link = event.target.closest('.pagination-list a');
        if (!link || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        if (link.classList.contains('pointer-events-none')) return;
        // Keep the question-table filters from the current URL intact.
        const url = new URL(window.location.href);
        const page = new URL(link.href).searchParams.get('pkg_page');
        if (page) url.searchParams.set('pkg_page', page);
        else url.searchParams.delete('pkg_page');
        updatePackages(url);
    });
})();
