<x-banksoal::layouts.admin>
    <style>
        .controls-section {
            padding: 14px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            background: #f8fafc;
        }

        .search-box {
            flex: 0 1 420px;
            max-width: 420px;
            min-width: 240px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 10px 12px 10px 36px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 13px;
            background: #fff;
        }

        .search-box input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .search-box svg {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: #94a3b8;
        }

        .filter-group {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-left: auto;
        }

        .filter-group label {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            white-space: nowrap;
        }

        .filter-group select {
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 12px;
            background: #fff;
            min-width: 140px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 16px;
            color: #64748b;
            font-size: 14px;
        }

        .pagination-section {
            display: flex;
            justify-content: flex-end;
            padding: 12px 14px 16px;
            border-top: 1px solid #e2e8f0;
            background: #fff;
        }

        .pagination-list {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .pagination-btn {
            min-width: 32px;
            height: 32px;
            padding: 0 10px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #334155;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .pagination-btn:hover:not(:disabled) {
            border-color: #3b82f6;
            color: #3b82f6;
        }

        .pagination-btn.active {
            background: #3b82f6;
            border-color: #3b82f6;
            color: #fff;
        }

        .pagination-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }
    </style>

    <div class="mb-6 lg:mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-slate-800 tracking-tight">Kontrol RPS</h1>
            <p class="text-slate-500 text-sm mt-2">Daftar dokumen RPS yang sudah disetujui dan siap di-preview atau diunduh.</p>
        </div>
    </div>

    <div class="controls-section">
        <div class="search-box">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
                id="searchInput"
                type="text"
                placeholder="Cari nama mata kuliah, file, atau tahun ajaran..."
                autocomplete="off"
                onkeyup="handleSearch()"
            >
        </div>
        <div class="filter-group">
            <label for="tahunAjaranSelect">Tahun Ajaran:</label>
            <select id="tahunAjaranSelect" onchange="handleFilterChange()"></select>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal Disetujui</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama MK</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">File</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="rpsTableBody" class="divide-y divide-slate-100 bg-white"></tbody>
            </table>
        </div>

        <div id="rpsEmptyState" class="empty-state" style="display:none;">Belum ada RPS berstatus disetujui.</div>
        <div id="rpsPagination" class="pagination-section" style="display:none;">
            <div id="rpsPaginationList" class="pagination-list"></div>
        </div>
    </div>

    <script>
        const PAGE_SIZE = 10;
        const csrfToken = '{{ csrf_token() }}';
        const approvedRpsApi = "{{ route('banksoal.api.v1.admin.rps.approved.index') }}";
        const previewRouteTemplate = "{{ route('banksoal.admin.kontrol-banksoal.rps.preview', ['rpsId' => '__ID__']) }}";
        const downloadRouteTemplate = "{{ route('banksoal.admin.kontrol-banksoal.rps.download', ['rpsId' => '__ID__']) }}";

        const state = {
            all: [],
            filtered: [],
            currentPage: 1,
            timer: null,
        };

        function toPreviewUrl(id) {
            return previewRouteTemplate.replace('__ID__', encodeURIComponent(String(id)));
        }

        function toDownloadUrl(id) {
            return downloadRouteTemplate.replace('__ID__', encodeURIComponent(String(id)));
        }

        function formatDate(value) {
            if (!value) return '-';
            const d = new Date(value);
            if (Number.isNaN(d.getTime())) return '-';

            return d.toLocaleString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        async function readApiResponse(response) {
            const contentType = response.headers.get('content-type') || '';
            if (contentType.includes('application/json')) {
                return response.json();
            }

            const text = await response.text();
            return { success: false, message: text || `Request gagal dengan status ${response.status}` };
        }

        function fillTahunAjaranOptions() {
            const select = document.getElementById('tahunAjaranSelect');
            if (!select) return;

            const unique = [...new Set(state.all.map((row) => String(row.tahun_ajaran || '').trim()).filter(Boolean))];
            unique.sort((a, b) => (a < b ? 1 : -1));

            select.innerHTML = '';
            if (unique.length === 0) {
                select.innerHTML = '<option value="">-</option>';
                return;
            }

            unique.forEach((ta) => {
                const option = document.createElement('option');
                option.value = ta;
                option.textContent = ta;
                select.appendChild(option);
            });

            select.value = unique[0];
        }

        function handleSearch() {
            clearTimeout(state.timer);
            state.timer = setTimeout(function () {
                state.currentPage = 1;
                applyFilter();
            }, 300);
        }

        function handleFilterChange() {
            state.currentPage = 1;
            applyFilter();
        }

        function applyFilter() {
            const search = document.getElementById('searchInput').value.trim().toLowerCase();
            const ta = document.getElementById('tahunAjaranSelect').value;

            let data = [...state.all];
            if (ta) {
                data = data.filter((item) => String(item.tahun_ajaran || '') === ta);
            }

            if (search) {
                data = data.filter((item) => {
                    const mk = String(item.mk_nama || '').toLowerCase();
                    const file = String(item.file_name || '').toLowerCase();
                    const tahunAjaran = String(item.tahun_ajaran || '').toLowerCase();
                    return mk.includes(search) || file.includes(search) || tahunAjaran.includes(search);
                });
            }

            state.filtered = data;
            renderTable();
        }

        function renderPagination(totalPages) {
            const page = state.currentPage;
            const list = document.getElementById('rpsPaginationList');
            const buttons = [];

            buttons.push(`<button type="button" class="pagination-btn" onclick="goToPage(${Math.max(1, page - 1)})" ${page === 1 ? 'disabled' : ''}>&lsaquo;</button>`);
            for (let i = 1; i <= totalPages; i++) {
                buttons.push(`<button type="button" class="pagination-btn ${i === page ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`);
            }
            buttons.push(`<button type="button" class="pagination-btn" onclick="goToPage(${Math.min(totalPages, page + 1)})" ${page === totalPages ? 'disabled' : ''}>&rsaquo;</button>`);

            list.innerHTML = buttons.join('');
        }

        function renderTable() {
            const body = document.getElementById('rpsTableBody');
            const emptyState = document.getElementById('rpsEmptyState');
            const pagination = document.getElementById('rpsPagination');

            const totalItems = state.filtered.length;
            const totalPages = Math.max(1, Math.ceil(totalItems / PAGE_SIZE));
            if (state.currentPage > totalPages) {
                state.currentPage = totalPages;
            }

            if (totalItems === 0) {
                body.innerHTML = '';
                emptyState.style.display = 'block';
                pagination.style.display = 'none';
                return;
            }

            emptyState.style.display = 'none';

            const start = (state.currentPage - 1) * PAGE_SIZE;
            const pageItems = state.filtered.slice(start, start + PAGE_SIZE);

            body.innerHTML = pageItems.map((item) => `
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="px-6 py-4 text-sm text-slate-700 whitespace-nowrap">${escapeHtml(formatDate(item.tanggal_disetujui))}</td>
                    <td class="px-6 py-4 text-sm text-slate-800 font-medium">${escapeHtml(item.mk_nama)}</td>
                    <td class="px-6 py-4 text-sm text-slate-600">${escapeHtml(item.file_name || '-')}</td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center gap-2">
                            <a href="${toPreviewUrl(item.id)}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">Preview</a>
                            <a href="${toDownloadUrl(item.id)}" class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100">Unduh</a>
                        </div>
                    </td>
                </tr>
            `).join('');

            pagination.style.display = totalPages > 1 ? 'flex' : 'none';
            renderPagination(totalPages);
        }

        function goToPage(page) {
            state.currentPage = page;
            renderTable();
        }

        async function loadData() {
            try {
                const response = await fetch(approvedRpsApi, {
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                const result = await readApiResponse(response);
                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Gagal memuat data RPS disetujui');
                }

                state.all = result.data || [];
                fillTahunAjaranOptions();
                applyFilter();
            } catch (error) {
                console.error(error);
                state.all = [];
                state.filtered = [];
                renderTable();
            }
        }

        document.addEventListener('DOMContentLoaded', loadData);

    </script>
</x-banksoal::layouts.admin>