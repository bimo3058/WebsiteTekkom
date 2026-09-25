(() => {
    const root = document.getElementById('approvedRps');
    if (!root) return;
    const byId = id => root.querySelector(`#${id}`);
    const state = { all: [], filtered: [], page: 1, timer: null, loading: false, error: false };
    const pageSize = 10;
    const escape = value => String(value ?? '').replace(/[&<>"']/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[char]));
    const icon = path => `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="${path}" /></svg>`;
    const date = value => {
        if (!value || Number.isNaN(new Date(value).getTime())) return '—';
        return new Date(value).toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    };
    function updateYearOptions(selected) {
        const years = [...new Set(state.all.map(row => String(row.tahun_ajaran || '').trim()).filter(Boolean))].sort().reverse();
        byId('tahunAjaranSelect').value = selected;
        window.dispatchEvent(new CustomEvent('banksoal-filter-options', { detail: {
            name: 'tahun_ajaran', selected,
            options: [{ value: '', label: 'Semua tahun ajaran' }, ...years.map(year => ({ value: year, label: year }))],
        } }));
    }
    function filter() {
        state.page = 1;
        const search = byId('searchInput').value.trim().toLowerCase();
        const year = byId('tahunAjaranSelect').value;
        state.filtered = state.all.filter(row => (!year || String(row.tahun_ajaran || '') === year)
            && (!search || [row.mk_nama, row.file_name, row.tahun_ajaran].some(value => String(value || '').toLowerCase().includes(search))));
        render();
    }
    function render() {
        const total = state.filtered.length;
        const pages = Math.max(1, Math.ceil(total / pageSize));
        state.page = Math.min(state.page, pages);
        const start = (state.page - 1) * pageSize;
        byId('rpsLoading').hidden = !state.loading;
        byId('rpsError').hidden = !state.error;
        byId('rpsTableContainer').hidden = state.loading || state.error || !total;
        byId('rpsEmptyState').hidden = state.loading || state.error || !!total;
        byId('rpsPagination').hidden = state.loading || state.error || !total;
        byId('rpsCount').textContent = state.loading || state.error ? '—' : `${total} dokumen`;
        root.setAttribute('aria-busy', String(state.loading));
        if (state.loading || state.error) return;
        byId('rpsEmptyTitle').textContent = state.all.length ? 'Tidak ada dokumen yang cocok' : 'Belum ada dokumen RPS';
        byId('rpsEmptyDescription').textContent = state.all.length ? 'Coba kata kunci lain atau reset filter yang digunakan.' : 'RPS yang telah disetujui akan muncul di sini.';
        byId('rpsTableBody').innerHTML = state.filtered.slice(start, start + pageSize).map(row => {
            const id = encodeURIComponent(String(row.id));
            return `<tr>
                <td><div class="control-title">${escape(row.mk_nama || 'Mata kuliah tidak tersedia')}</div><span class="control-meta">Tahun ajaran ${escape(row.tahun_ajaran || '—')}</span></td>
                <td><div class="control-file">${icon('M14 2H6v20h12V6zM14 2v5h5M9 12h6M9 16h4')}<span>${escape(row.file_name || 'Nama file tidak tersedia')}</span></div></td>
                <td class="control-date">${escape(date(row.tanggal_disetujui))}</td>
                <td><div class="control-actions">
                    <a class="dosen-management-btn" href="${escape(root.dataset.preview.replace('__ID__', id))}" aria-label="Preview RPS ${escape(row.mk_nama)}">${icon('M2 12s3-7 10-7 10 7 10 7-3 7-10 7S2 12 2 12Zm13 0a3 3 0 1 1-6 0 3 3 0 0 1 6 0')}Preview</a>
                    <a class="dosen-management-btn" href="${escape(root.dataset.download.replace('__ID__', id))}" aria-label="Unduh RPS ${escape(row.mk_nama)}">${icon('M12 3v12m-4-4 4 4 4-4M4 16v5h16v-5')}Unduh</a>
                </div></td>
            </tr>`;
        }).join('');
        byId('rpsSummary').textContent = `Menampilkan ${total ? start + 1 : 0}–${Math.min(start + pageSize, total)} dari ${total} dokumen`;
        const selectedPages = [...new Set([1, pages, state.page - 1, state.page, state.page + 1])].filter(page => page >= 1 && page <= pages).sort((a, b) => a - b);
        const button = (page, label, disabled, current = false, ariaLabel = '') => `<button type="button" class="pagination-btn${current ? ' active' : ''}" data-page="${page}" ${disabled ? 'disabled' : ''} ${current ? 'aria-current="page"' : ''} aria-label="${ariaLabel || `Halaman ${page}`}">${label}</button>`;
        let html = button(state.page - 1, '&lsaquo;', state.page === 1, false, 'Halaman sebelumnya');
        selectedPages.forEach((page, index) => {
            if (index && page - selectedPages[index - 1] > 1) html += '<span class="pagination-ellipsis" aria-hidden="true">…</span>';
            html += button(page, page, false, page === state.page);
        });
        html += button(state.page + 1, '&rsaquo;', state.page === pages, false, 'Halaman berikutnya');
        byId('rpsPaginationList').innerHTML = html;
    }
    async function load() {
        if (state.loading) return;
        state.loading = true;
        state.error = false;
        render();
        try {
            const response = await fetch(root.dataset.api, { headers: { Accept: 'application/json' } });
            if (!response.ok) throw new Error('Unable to load approved RPS');
            const result = await response.json();
            if (!result.success || !Array.isArray(result.data)) throw new Error('Invalid approved RPS response');
            state.all = result.data;
            const latestYear = [...new Set(state.all.map(row => String(row.tahun_ajaran || '').trim()).filter(Boolean))].sort().reverse()[0] || '';
            updateYearOptions(latestYear);
        } catch {
            state.error = true;
        } finally {
            state.loading = false;
            filter();
        }
    }
    byId('searchInput').addEventListener('input', () => {
        clearTimeout(state.timer);
        state.timer = setTimeout(filter, 250);
    });
    byId('resetRpsFilter').addEventListener('click', () => {
        byId('searchInput').value = '';
        updateYearOptions('');
        filter();
    });
    byId('retryRps').addEventListener('click', load);
    byId('rpsPaginationList').addEventListener('click', event => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.disabled) return;
        state.page = Number(button.dataset.page);
        render();
    });
    window.ApprovedRps = { filter };
    if (window.Alpine) load();
    else document.addEventListener('alpine:initialized', load, { once: true });
})();
