<x-banksoal::layouts.admin>
    @section('breadcrumbs')
    <a href="#" class="text-slate-500 hover:text-primary transition-colors">Bank Soal</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Manajemen RPS</span>
    @endsection

    <link rel="stylesheet" href="{{ asset('css/banksoal-ui.css') }}">
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
            border-color: rgb(11, 38, 110);
            box-shadow: 0 0 0 3px rgba(11, 38, 110, 0.1);
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
            border-color: rgb(11, 38, 110);
            color: rgb(11, 38, 110);
        }

        .pagination-btn.active {
            background: rgb(11, 38, 110);
            border-color: rgb(11, 38, 110);
            color: #fff;
        }

        .pagination-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        /* ── Table loading spinner ── */
        .tbl-loading {
            display: none; align-items: center; justify-content: center;
            gap: 10px; padding: 40px 20px;
            color: #475569; font-size: 13px;
        }
        .tbl-loading.show { display: flex; }
        .tbl-spinner {
            width: 22px; height: 22px;
            border: 3px solid #e2e8f0;
            border-top-color: rgb(11, 38, 110);
            border-radius: 50%;
            animation: tbl-spin 0.7s linear infinite;
            flex-shrink: 0;
        }
        @keyframes tbl-spin { to { transform: rotate(360deg); } }

        /* ── 3-dot dropdown ── */
        .dots-wrap { 
            position: relative; 
            display: inline-block;
            line-height: 1;
        }
        .btn-dots {
            display: inline-flex; 
            align-items: center; 
            justify-content: center;
            width: 32px; 
            height: 32px; 
            border-radius: 8px;
            border: 1px solid #e2e8f0; 
            background: #fff;
            font-size: 18px; 
            cursor: pointer; 
            color: #64748b;
            transition: all 0.2s;
        }
        .btn-dots:hover { 
            border-color: rgb(11, 38, 110); 
            color: rgb(11, 38, 110); 
            background: #f8fafc; 
        }
        .dots-menu {
            display: none; 
            position: absolute; 
            right: 0; 
            top: calc(100% + 5px);
            background: #fff; 
            border: 1px solid #e2e8f0;
            border-radius: 12px; 
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            min-width: 160px; 
            z-index: 100; 
            overflow: hidden;
            padding: 4px;
        }
        .dots-menu.open { 
            display: block; 
            animation: menuFadeIn 0.2s ease-out;
        }
        @keyframes menuFadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .dots-menu button, .dots-menu a {
            display: flex !important; 
            align-items: center !important; 
            justify-content: flex-start !important;
            gap: 10px !important;
            width: 100% !important; 
            padding: 10px 12px !important;
            background: none !important; 
            border: none !important; 
            border-radius: 8px !important;
            font-size: 13px !important; 
            font-weight: 500 !important; 
            color: #334155 !important;
            cursor: pointer !important; 
            text-align: left !important;
            text-decoration: none !important;
            transition: all 0.15s !important;
            margin: 0 !important;
            line-height: 1.2 !important;
        }
        .dots-menu button:hover:not(:disabled), .dots-menu a:hover:not(:disabled) { 
            background: #f1f5f9 !important; 
            color: rgb(11, 38, 110) !important;
        }
        .dots-menu i, .dots-menu svg {
            font-size: 14px !important;
            width: 18px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
            margin: 0 !important;
        }
    </style>

    <div class="mb-6 lg:mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-slate-800 tracking-tight">Kontrol RPS</h1>
            <p class="text-slate-500 text-sm mt-2">Daftar dokumen RPS yang sudah disetujui dan siap di-preview atau diunduh.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 mb-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input
                id="searchInput"
                type="text"
                placeholder="Cari nama mata kuliah, file, atau tahun ajaran..."
                autocomplete="off"
                onkeyup="handleSearch()"
                class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all outline-none"
            >
        </div>

        <div class="flex items-center gap-3">
            <div class="relative" x-data="{ filterOpen: false }" @click.away="filterOpen = false">
                <button @click="filterOpen = !filterOpen" type="button"
                    class="flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 rounded-full text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all shadow-sm">
                    <svg class="w-4 h-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L13 10.414V15a1 1 0 01-.553.894l-4 2A1 1 0 017 17v-6.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                    </svg>
                    Filter
                    <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="filterOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="filterOpen" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     class="absolute right-0 mt-2 w-72 origin-top-right rounded-2xl border border-slate-100 bg-white shadow-xl z-50 p-5 space-y-4">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 block">Tahun Ajaran</label>
                        <div class="space-y-2">
                            <select id="tahunAjaranSelect" onchange="handleFilterChange()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20"></select>
                        </div>
                    </div>
                    <div class="flex gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="filterOpen = false"
                            class="flex-1 py-2 text-xs font-bold text-slate-500 hover:text-slate-700 transition-colors">
                            Tutup
                        </button>
                        <button type="button" @click="filterOpen = false; handleFilterChange();"
                            class="flex-1 py-2 rounded-lg bg-primary text-white text-xs font-bold hover:opacity-90 shadow-md shadow-primary/20 transition-all">
                            Terapkan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div id="rpsLoading" class="tbl-loading show"><div class="tbl-spinner"></div> Memuat data...</div>
        <div class="overflow-x-auto" id="rpsTableContainer" style="opacity: 0.4; transition: opacity 0.2s;">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-primary text-white border-b border-primary/20">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Tanggal Disetujui</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">Nama MK</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider">File</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider w-14"></th>
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

            let pages = [];
            if (totalPages < 10) {
                let start = Math.max(1, page - 2);
                let end = Math.min(totalPages, start + 4);
                start = Math.max(1, end - 4);
                for (let i = start; i <= end; i++) pages.push(i);
            } else {
                pages = [1, 2, 3, '...', totalPages - 1, totalPages];
            }

            pages.forEach(p => {
                if (p === '...') {
                    buttons.push(`<span class="pagination-ellipsis">...</span>`);
                } else {
                    buttons.push(`<button type="button" class="pagination-btn ${p === page ? 'active' : ''}" onclick="goToPage(${p})">${p}</button>`);
                }
            });

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
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4 text-sm text-slate-700 whitespace-nowrap">${escapeHtml(formatDate(item.tanggal_disetujui))}</td>
                    <td class="px-6 py-4 text-sm text-slate-800 font-medium">${escapeHtml(item.mk_nama)}</td>
                    <td class="px-6 py-4 text-sm text-slate-600">${escapeHtml(item.file_name || '-')}</td>
                    <td class="px-6 py-4 text-sm">
                        <div class="dots-wrap">
                            <button type="button" class="btn-dots" onclick="toggleMenu('rps-${item.id}', event)" title="Aksi">&#8942;</button>
                            <div class="dots-menu" id="menu-rps-${item.id}">
                                <a href="${toPreviewUrl(item.id)}">
                                    <i class="fas fa-eye"></i> Preview
                                </a>
                                <a href="${toDownloadUrl(item.id)}">
                                    <i class="fas fa-download"></i> Unduh
                                </a>
                            </div>
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
            const loadingEl = document.getElementById('rpsLoading');
            const tableEl = document.getElementById('rpsTableContainer');
            
            if (loadingEl) loadingEl.classList.add('show');
            if (tableEl) tableEl.style.opacity = '0.4';

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
            } finally {
                if (loadingEl) loadingEl.classList.remove('show');
                if (tableEl) tableEl.style.opacity = '1';
            }
        }

        window.toggleMenu = function(id, event) {
            event.stopPropagation();
            document.querySelectorAll('.dots-menu').forEach(m => {
                if (m.id !== 'menu-' + id) {
                    m.classList.remove('open');
                }
            });
            const menu = document.getElementById('menu-' + id);
            if (menu) menu.classList.toggle('open');
        };

        document.addEventListener('click', function() {
            document.querySelectorAll('.dots-menu').forEach(m => m.classList.remove('open'));
        });

        document.addEventListener('DOMContentLoaded', loadData);

    </script>
</x-banksoal::layouts.admin>