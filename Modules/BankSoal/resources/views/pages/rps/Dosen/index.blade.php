<x-banksoal::layouts.dosen-admin>
    @section('breadcrumbs')
        <span class="text-slate-800 font-semibold">Manajemen RPS</span>
    @endsection
    <style>
        .controls-section {
            padding: 12px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 16px;
            display: flex;
            flex-wrap: nowrap;
            gap: 6px;
            align-items: center;
            background: #f8fafc;
            overflow-x: auto;
        }

        .search-box {
            flex: 0 0 220px;
            max-width: 220px;
            min-width: 220px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 8px 10px 8px 32px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 12px;
            background: #fff;
        }

        .search-box input:focus {
            outline: none;
            border-color: rgb(11, 38, 110);
            box-shadow: 0 0 0 3px rgba(11, 38, 110, 0.1);
        }

        .search-box svg {
            position: absolute;
            left: 9px;
            top: 50%;
            transform: translateY(-50%);
            width: 14px;
            height: 14px;
            color: #94a3b8;
        }

        .filter-group {
            display: flex;
            gap: 6px;
            align-items: center;
            flex: 0 0 auto;
            white-space: nowrap;
        }

        .filter-group label {
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            white-space: nowrap;
        }

        .filter-group select {
            padding: 7px 9px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 11px;
            background: #fff;
            min-width: 110px;
            max-width: 110px;
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
        .dots-menu button:disabled, .dots-menu a:disabled {
            color: #94a3b8 !important;
            cursor: not-allowed !important;
            opacity: 0.7;
        }
        .dots-menu button:disabled svg, .dots-menu a:disabled svg, .dots-menu button:disabled i, .dots-menu a:disabled i {
            opacity: 0.6;
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
        .dots-menu .menu-delete:not(:disabled) { 
            color: #ef4444 !important; 
        }
        .dots-menu .menu-delete:hover:not(:disabled) { 
            background: #fef2f2 !important; 
            color: #dc2626 !important;
        }
    </style>

    <x-banksoal::notification.alerts />

    <x-banksoal::ui.page-header title="Manajemen RPS" subtitle="Lengkapi data rencana pembelajaran semester dan unggah dokumen pendukung." />

    <x-banksoal::ui.status-banner
        :activePeriode="$activePeriode ?? null"
        :isUploadOpen="$isUploadOpen ?? false"
        :tenggatH7="$tenggatH7 ?? false"
        :unsubmittedMk="$unsubmittedMk ?? []"
        :daysLeft="$daysLeft ?? 0"
        :isHourFormat="$isHourFormat ?? false"
    />

    <div class="mb-6 flex items-center justify-end">
        @if($isUploadOpen ?? false)
            <a href="{{ route('banksoal.rps.dosen.create') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/40"
                title="Ajukan RPS baru"
            >
                <i class="fas fa-plus"></i> Ajukan RPS
            </a>
        @else
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-500"
                disabled
                title="Periode upload RPS saat ini tidak aktif"
            >
                <i class="fas fa-plus"></i> Ajukan RPS
            </button>
        @endif
    </div>

    <div id="riwayatPengajuanContainer">
        <x-banksoal::ui.riwayat-rps :riwayat="$riwayat" />
    </div>

    <div id="rpsRiwayatEmptyState" class="empty-state" style="display:none;">
        Tidak ada riwayat pengajuan RPS yang sesuai dengan pencarian mata kuliah.
    </div>

    <div id="rpsPagination" class="pagination-section" style="display:none;">
        <div id="rpsPaginationList" class="pagination-list"></div>
    </div>

    <div class="mt-8">
        <x-banksoal::ui.riwayat-rps-disetujui :riwayatMkDisetujui="$riwayatMkDisetujui" />
    </div>



    <x-banksoal::ui.dokumen-rps-modal />

    <x-banksoal::ui.hapus-rps-modal />

    <x-banksoal::ui.rps-script />

    <script>
        (function() {
            // Klien-side pagination untuk Riwayat Pengajuan RPS
            initClientSidePagination();

            // Menu toggle logic
            window.toggleMenu = function(id, event) {
                event.stopPropagation();
                const menus = document.querySelectorAll('.dots-menu');
                const menu = document.getElementById(`menu-${id}`);
                
                menus.forEach(m => {
                    if (m.id !== `menu-${id}`) {
                        m.classList.remove('open');
                        m.style.top = '';
                        m.style.bottom = '';
                        m.style.left = '';
                        m.style.right = '';
                    }
                });
                
                if (menu) {
                    menu.classList.toggle('open');
                    
                    if (menu.classList.contains('open')) {
                        const rect = menu.getBoundingClientRect();
                        const viewHeight = Math.max(document.documentElement.clientHeight, window.innerHeight);
                        const viewWidth = Math.max(document.documentElement.clientWidth, window.innerWidth);
                        
                        if (rect.bottom > viewHeight) {
                            menu.style.top = 'auto';
                            menu.style.bottom = '100%';
                            menu.style.marginBottom = '5px';
                        } else {
                            menu.style.top = '100%';
                            menu.style.bottom = 'auto';
                            menu.style.marginBottom = '0';
                            menu.style.marginTop = '5px';
                        }
                        
                        if (rect.left < 0) {
                            menu.style.right = 'auto';
                            menu.style.left = '0';
                        }
                    } else {
                        menu.style.top = '';
                        menu.style.bottom = '';
                        menu.style.left = '';
                        menu.style.right = '';
                    }
                }
            };

            document.addEventListener('click', function() {
                document.querySelectorAll('.dots-menu').forEach(m => {
                    m.classList.remove('open');
                    m.style.top = '';
                    m.style.bottom = '';
                    m.style.left = '';
                    m.style.right = '';
                });
            });
        })();

        function initClientSidePagination() {
            const itemsPerPage = 5;
            const container = document.getElementById('riwayatPengajuanContainer');
            const pagination = document.getElementById('rpsPagination');
            const paginationList = document.getElementById('rpsPaginationList');
            const emptyState = document.getElementById('rpsRiwayatEmptyState');
            const searchInput = document.getElementById('riwayatSearchInput');
            const statusSelect = document.getElementById('riwayatStatusSelect');
            const mkSelect = document.getElementById('riwayatMkSelect');
            if (!container) return;

            const tableBody = container.querySelector('tbody');
            if (!tableBody) return;

            const allRows = Array.from(tableBody.querySelectorAll('tr'));
            const realRows = allRows.filter((row) => row.dataset.emptyState !== '1');
            let currentPage = 1;
            let searchQuery = '';
            let selectedStatus = '';
            let selectedMk = '';
            let filteredRows = [...realRows];

            function normalize(value) {
                return String(value ?? '').trim().toLowerCase();
            }

            function getFilteredRows() {
                const query = normalize(searchQuery);
                const status = normalize(selectedStatus);
                const mk = normalize(selectedMk);

                return realRows.filter((row) => {
                    const mkText = normalize(row.dataset.mk);
                    const rowStatus = normalize(row.dataset.status);
                    const rowYear = normalize(row.dataset.year);

                    const matchesSearch = !query || mkText.includes(query) || rowYear.includes(query) || rowStatus.includes(query);
                    const matchesStatus = !status || rowStatus === status;
                    const matchesMk = !mk || mkText === mk;

                    return matchesSearch && matchesStatus && matchesMk;
                }).sort((a, b) => {
                    const mkCompare = normalize(a.dataset.mk).localeCompare(normalize(b.dataset.mk), 'id');
                    if (mkCompare !== 0) return mkCompare;

                    return normalize(b.dataset.year).localeCompare(normalize(a.dataset.year), 'id');
                });
            }

            function updateEmptyState(totalItems) {
                if (!emptyState) return;

                if (totalItems === 0) {
                    emptyState.style.display = 'block';
                } else {
                    emptyState.style.display = 'none';
                }
            }

            function showPage(page) {
                const totalItems = filteredRows.length;
                const totalPages = Math.max(1, Math.ceil(totalItems / itemsPerPage));
                currentPage = Math.max(1, Math.min(page, totalPages));
                const start = (currentPage - 1) * itemsPerPage;
                const end = start + itemsPerPage;
                const pageRows = filteredRows.slice(start, end);

                realRows.forEach((row, index) => {
                    if (pageRows.includes(row)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                updateEmptyState(totalItems);
                renderPagination();
            }

            function renderPagination() {
                const totalItems = filteredRows.length;
                const totalPages = Math.max(1, Math.ceil(totalItems / itemsPerPage));

                if (totalItems === 0 || totalPages <= 1) {
                    if (pagination) {
                        pagination.style.display = 'none';
                    }
                    if (paginationList) {
                        paginationList.innerHTML = '';
                    }
                    return;
                }

                const buttons = [];
                buttons.push(`<button type="button" class="pagination-btn" onclick="window.rpsTablePageChanger(${Math.max(1, currentPage - 1)})" ${currentPage === 1 ? 'disabled' : ''}>&lsaquo;</button>`);

                for (let page = 1; page <= totalPages; page++) {
                    buttons.push(`<button type="button" class="pagination-btn ${page === currentPage ? 'active' : ''}" onclick="window.rpsTablePageChanger(${page})">${page}</button>`);
                }

                buttons.push(`<button type="button" class="pagination-btn" onclick="window.rpsTablePageChanger(${Math.min(totalPages, currentPage + 1)})" ${currentPage === totalPages ? 'disabled' : ''}>&rsaquo;</button>`);

                if (paginationList) {
                    paginationList.innerHTML = buttons.join('');
                }

                if (pagination) {
                    pagination.style.display = 'flex';
                }
            }

            window.handleRiwayatSearch = function() {
                searchQuery = searchInput?.value || '';
                selectedStatus = statusSelect?.value || '';
                selectedMk = mkSelect?.value || '';
                currentPage = 1;
                filteredRows = getFilteredRows();
                syncStatusOptions();
                syncMkOptions();
                showPage(1);
            };

            function syncStatusOptions() {
                if (!statusSelect) return;

                const currentValue = statusSelect.value;
                const statuses = [...new Set(realRows.map((row) => String(row.dataset.status || '').trim()).filter(Boolean))]
                    .sort((a, b) => a.localeCompare(b, 'id'));

                statusSelect.innerHTML = '<option value="">Semua</option>';
                statuses.forEach((status) => {
                    const option = document.createElement('option');
                    option.value = status;
                    option.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                    statusSelect.appendChild(option);
                });

                statusSelect.value = statuses.includes(currentValue) ? currentValue : '';
                selectedStatus = statusSelect.value;
            }

            function syncMkOptions() {
                if (!mkSelect) return;

                const currentValue = mkSelect.value;
                const mks = [...new Set(realRows.map((row) => String(row.dataset.mk || '').trim()).filter(Boolean))]
                    .sort((a, b) => a.localeCompare(b, 'id'));

                mkSelect.innerHTML = '<option value="">Semua</option>';
                mks.forEach((mk) => {
                    const option = document.createElement('option');
                    option.value = mk;
                    option.textContent = mk;
                    mkSelect.appendChild(option);
                });

                mkSelect.value = mks.includes(currentValue) ? currentValue : '';
                selectedMk = mkSelect.value;
            }

            window.handleRiwayatFilterChange = function() {
                selectedStatus = statusSelect?.value || '';
                selectedMk = mkSelect?.value || '';
                currentPage = 1;
                filteredRows = getFilteredRows();
                showPage(1);
            };

            window.handleSearch = window.handleRiwayatSearch;

            window.rpsTablePageChanger = function(page) {
                showPage(page);
            };

            window.handleRiwayatSearch();
        }
    </script>
</x-banksoal::layouts.dosen-admin>
