<x-banksoal::layouts.admin>
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #3b82f6;
            --danger-red: #ef4444;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            gap: 12px;
            flex-wrap: wrap;
        }

        .header-content h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--slate-800);
            margin: 0;
            letter-spacing: -0.5px;
        }

        .header-content p {
            font-size: 14px;
            color: #64748b;
            margin: 8px 0 0 0;
        }

        .feature-card {
            border: 1px solid var(--slate-200);
            border-radius: 12px;
            background: #fff;
            margin-bottom: 18px;
            overflow: hidden;
        }

        .feature-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 16px;
            border-bottom: 1px solid var(--slate-200);
            flex-wrap: wrap;
        }

        .feature-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--slate-800);
            margin: 0;
        }

        .feature-subtitle {
            font-size: 13px;
            color: var(--slate-600);
            margin: 4px 0 0 0;
        }

        .feature-hint {
            font-size: 12px;
            color: var(--slate-400);
            margin: 4px 0 0 0;
        }

        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #f8fafc;
            color: #1e293b;
            padding: 12px 22px;
            border-radius: 16px;
            border: 1px solid #cbd5e1;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
        }

        .btn-add:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }

        .btn-add svg {
            width: 18px;
            height: 18px;
            color: #1e293b;
        }

        .controls-section {
            padding: 14px 16px;
            border-bottom: 1px solid var(--slate-200);
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            background: var(--slate-50);
        }

        .search-box {
            flex: 0 1 320px;
            max-width: 320px;
            min-width: 220px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 10px 12px 10px 36px;
            border: 1px solid var(--slate-300);
            border-radius: 8px;
            font-size: 13px;
            background: #fff;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .search-box svg {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: var(--slate-400);
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
            color: var(--slate-600);
            white-space: nowrap;
        }

        .filter-group select {
            padding: 8px 10px;
            border: 1px solid var(--slate-300);
            border-radius: 8px;
            font-size: 12px;
            background: #fff;
            min-width: 120px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #fff;
            border-bottom: 1px solid var(--slate-200);
        }

        th {
            padding: 12px 14px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            color: var(--slate-700);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--slate-200);
            font-size: 13px;
            color: var(--slate-700);
            vertical-align: top;
        }

        tbody tr:hover {
            background: var(--slate-50);
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .kode-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            background: #dbeafe;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            white-space: nowrap;
        }

        .mapping-pill {
            display: inline-flex;
            align-items: center;
            padding: 5px 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            background: #dbeafe;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            max-width: 100%;
        }

        .desc-cell {
            color: #0f172a;
            line-height: 1.4;
            max-width: 520px;
        }

        .action-cell {
            display: flex;
            gap: 8px;
        }

        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border: 1px solid currentColor;
            border-radius: 6px;
            cursor: pointer;
            background: transparent;
            transition: all 0.2s;
        }

        .btn-icon-delete {
            color: var(--danger-red);
        }

        .btn-icon-delete:hover {
            background: var(--danger-red);
            border-color: var(--danger-red);
            color: #fff;
        }

        .empty-state {
            text-align: center;
            padding: 36px 16px;
            color: var(--slate-600);
            font-size: 14px;
        }

        .pagination-section {
            display: flex;
            justify-content: flex-end;
            padding: 12px 14px 16px;
            border-top: 1px solid var(--slate-200);
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
            border: 1px solid var(--slate-200);
            background: #fff;
            color: var(--slate-700);
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .pagination-btn:hover:not(:disabled) {
            border-color: var(--primary-blue);
            color: var(--primary-blue);
        }

        .pagination-btn.active {
            background: var(--primary-blue);
            border-color: var(--primary-blue);
            color: #fff;
        }

        .pagination-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.45);
            z-index: 60;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .modal-overlay.show { display: flex; }

        .modal-content {
            background: #fff;
            border-radius: 12px;
            width: 100%;
            max-width: 560px;
            border: 1px solid var(--slate-200);
            overflow: hidden;
        }

        .modal-header {
            padding: 16px 18px;
            border-bottom: 1px solid var(--slate-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: var(--slate-800);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            color: var(--slate-400);
            cursor: pointer;
            width: 30px;
            height: 30px;
            border-radius: 6px;
        }

        .modal-close:hover {
            background: var(--slate-100);
            color: var(--slate-700);
        }

        .modal-body {
            padding: 16px 18px;
        }

        .form-group { margin-bottom: 14px; }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 600;
            color: var(--slate-700);
        }

        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--slate-300);
            border-radius: 8px;
            font-size: 13px;
            background: #fff;
        }

        .form-group select[multiple] {
            min-height: 140px;
        }

        .form-group select:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-error {
            display: none;
            margin-top: 6px;
            font-size: 12px;
            color: var(--danger-red);
        }

        .form-error.show { display: block; }

        .modal-footer {
            padding: 12px 18px 16px;
            border-top: 1px solid var(--slate-200);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            padding: 9px 14px;
            border-radius: 8px;
            border: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-primary { background: var(--primary-blue); color: #fff; }
        .btn-secondary { background: var(--slate-200); color: var(--slate-700); }

        .page-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 16px;
        }

        @media (max-width: 900px) {
            .controls-section { flex-direction: column; align-items: stretch; }
            .search-box { min-width: 100%; }
            .filter-group { width: 100%; margin-left: 0; }
            .filter-group select { flex: 1; }
        }
    </style>
    @endpush

    <div class="page-container">
        <div class="page-header">
        <div class="header-content">
            <h1>Pemetaan</h1>
            <p>Kelola pemetaan CPMK ke CPL, Mata Kuliah ke CPL, dan Dosen ke Mata Kuliah.</p>
        </div>
    </div>

    <section class="feature-card" id="section-cpmk-cpl">
        <div class="feature-head">
            <div>
                <h2 class="feature-title">Pemetaan CPMK ke CPL</h2>
            </div>
            <button type="button" class="btn-add" onclick="openModal('cpmkCpl')">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Pemetaan
            </button>
        </div>

        <div class="controls-section">
            <div class="search-box">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" id="cpmkCplSearch" placeholder="Cari CPMK atau CPL..." onkeyup="handleSearch('cpmkCpl')">
            </div>
            <div class="filter-group">
                <label for="cpmkCplSortDirection">Order By:</label>
                <select id="cpmkCplSortDirection" onchange="handleSort('cpmkCpl')">
                    <option value="asc">Ascending</option>
                    <option value="desc">Descending</option>
                </select>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 180px;">CPL</th>
                        <th>CPMK</th>
                    </tr>
                </thead>
                <tbody id="cpmkCplTableBody"></tbody>
            </table>
        </div>
        <div id="cpmkCplEmptyState" class="empty-state" style="display:none;">Tidak ada data pemetaan CPMK ke CPL</div>
        <div id="cpmkCplPagination" class="pagination-section" style="display:none;">
            <div class="pagination-list" id="cpmkCplPaginationList"></div>
        </div>
    </section>

    <section class="feature-card" id="section-mk-cpl">
        <div class="feature-head">
            <div>
                <h2 class="feature-title">Pemetaan Mata Kuliah ke CPL</h2>
            </div>
            <button type="button" class="btn-add" onclick="openModal('mkCpl')">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Pemetaan
            </button>
        </div>

        <div class="controls-section">
            <div class="search-box">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" id="mkCplSearch" placeholder="Cari mata kuliah atau CPL..." onkeyup="handleSearch('mkCpl')">
            </div>
            <div class="filter-group">
                <label for="mkCplSortDirection">Order By:</label>
                <select id="mkCplSortDirection" onchange="handleSort('mkCpl')">
                    <option value="asc">Ascending</option>
                    <option value="desc">Descending</option>
                </select>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 240px;">Mata Kuliah</th>
                        <th>CPL</th>
                    </tr>
                </thead>
                <tbody id="mkCplTableBody"></tbody>
            </table>
        </div>
        <div id="mkCplEmptyState" class="empty-state" style="display:none;">Tidak ada data pemetaan Mata Kuliah ke CPL</div>
        <div id="mkCplPagination" class="pagination-section" style="display:none;">
            <div class="pagination-list" id="mkCplPaginationList"></div>
        </div>
    </section>

    <section class="feature-card" id="section-dosen-mk">
        <div class="feature-head">
            <div>
                <h2 class="feature-title">Pemetaan Dosen ke Mata Kuliah</h2>
            </div>
            <button type="button" class="btn-add" onclick="openModal('dosenMk')">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Pemetaan
            </button>
        </div>

        <div class="controls-section">
            <div class="search-box">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" id="dosenMkSearch" placeholder="Cari dosen atau mata kuliah..." onkeyup="handleSearch('dosenMk')">
            </div>
            <div class="filter-group">
                <label for="dosenMkSortDirection">Order By:</label>
                <select id="dosenMkSortDirection" onchange="handleSort('dosenMk')">
                    <option value="asc">Ascending</option>
                    <option value="desc">Descending</option>
                </select>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 240px;">Mata Kuliah</th>
                        <th>Dosen Pengampu</th>
                    </tr>
                </thead>
                <tbody id="dosenMkTableBody"></tbody>
            </table>
        </div>
        <div id="dosenMkEmptyState" class="empty-state" style="display:none;">Tidak ada data pemetaan Dosen ke Mata Kuliah</div>
        <div id="dosenMkPagination" class="pagination-section" style="display:none;">
            <div class="pagination-list" id="dosenMkPaginationList"></div>
        </div>
    </section>

    <div class="modal-overlay" id="modalCpmkCpl" onclick="closeModalOnBackdrop(event, 'modalCpmkCpl')">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h2 class="modal-title">Tambah Pemetaan CPMK ke CPL</h2>
                <button type="button" class="modal-close" onclick="closeModal('modalCpmkCpl')">&times;</button>
            </div>
            <form onsubmit="submitCpmkCpl(event)">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="mapCplIdForCpmk">CPL *</label>
                        <select id="mapCplIdForCpmk" required></select>
                        <div class="form-error" id="error-mapCplIdForCpmk"></div>
                    </div>
                    <div class="form-group">
                        <label for="mapCpmkIdsForCpl">CPMK * (boleh pilih banyak)</label>
                        <select id="mapCpmkIdsForCpl" multiple required></select>
                        <div class="form-error" id="error-mapCpmkIdsForCpl"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalCpmkCpl')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="modalMkCpl" onclick="closeModalOnBackdrop(event, 'modalMkCpl')">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h2 class="modal-title">Tambah Pemetaan Mata Kuliah ke CPL</h2>
                <button type="button" class="modal-close" onclick="closeModal('modalMkCpl')">&times;</button>
            </div>
            <form onsubmit="submitMkCpl(event)">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="mapMkIdForCpl">Mata Kuliah *</label>
                        <select id="mapMkIdForCpl" required></select>
                        <div class="form-error" id="error-mapMkIdForCpl"></div>
                    </div>
                    <div class="form-group">
                        <label for="mapCplIdsForMk">CPL * (boleh pilih banyak)</label>
                        <select id="mapCplIdsForMk" multiple required></select>
                        <div class="form-error" id="error-mapCplIdsForMk"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalMkCpl')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="modalDosenMk" onclick="closeModalOnBackdrop(event, 'modalDosenMk')">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h2 class="modal-title">Tambah Pemetaan Dosen ke Mata Kuliah</h2>
                <button type="button" class="modal-close" onclick="closeModal('modalDosenMk')">&times;</button>
            </div>
            <form onsubmit="submitDosenMk(event)">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="mapMkIdForDosen">Mata Kuliah *</label>
                        <select id="mapMkIdForDosen" required></select>
                        <div class="form-error" id="error-mapMkIdForDosen"></div>
                    </div>
                    <div class="form-group">
                        <label for="mapDosenIdsForMk">Dosen * (boleh pilih banyak)</label>
                        <select id="mapDosenIdsForMk" multiple required></select>
                        <div class="form-error" id="error-mapDosenIdsForMk"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalDosenMk')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
    <script>
        const PAGE_SIZE = 5;
        const csrfToken = '{{ csrf_token() }}';
        const BASE_API = '{{ url("/bank-soal/admin/api/pemetaan") }}';
        let cpmkCplTomSelect = null;
        let mkCplTomSelect = null;
        let dosenMkTomSelect = null;

        const mappingConfig = {
            cpmkCpl: {
                listApi: `${BASE_API}/cpmk-cpl`,
                searchId: 'cpmkCplSearch',
                sortId: 'cpmkCplSortDirection',
                tableBodyId: 'cpmkCplTableBody',
                emptyId: 'cpmkCplEmptyState',
                paginationId: 'cpmkCplPagination',
                paginationListId: 'cpmkCplPaginationList',
                sortText: (item) => `${item.cpl_kode} ${(item.cpmk_codes || []).join(' ')}`,
                filterText: (item) => `${item.cpl_kode} ${(item.cpmk_codes || []).join(' ')}`,
            },
            mkCpl: {
                listApi: `${BASE_API}/mk-cpl`,
                searchId: 'mkCplSearch',
                sortId: 'mkCplSortDirection',
                tableBodyId: 'mkCplTableBody',
                emptyId: 'mkCplEmptyState',
                paginationId: 'mkCplPagination',
                paginationListId: 'mkCplPaginationList',
                sortText: (item) => `${item.mk_nama} ${item.mk_kode} ${(item.cpl_codes || []).join(' ')}`,
                filterText: (item) => `${item.mk_nama} ${item.mk_kode} ${(item.cpl_codes || []).join(' ')}`,
            },
            dosenMk: {
                listApi: `${BASE_API}/dosen-mk`,
                searchId: 'dosenMkSearch',
                sortId: 'dosenMkSortDirection',
                tableBodyId: 'dosenMkTableBody',
                emptyId: 'dosenMkEmptyState',
                paginationId: 'dosenMkPagination',
                paginationListId: 'dosenMkPaginationList',
                sortText: (item) => `${item.mk_nama} ${(item.dosen_names || []).join(' ')}`,
                filterText: (item) => `${item.mk_nama} ${item.mk_kode} ${(item.dosen_names || []).join(' ')}`,
            },
        };

        const state = {
            options: { cpl: [], cpmk: [], mata_kuliah: [], dosen: [] },
            cpmkCpl: { all: [], filtered: [], currentPage: 1, timer: null },
            mkCpl: { all: [], filtered: [], currentPage: 1, timer: null },
            dosenMk: { all: [], filtered: [], currentPage: 1, timer: null },
        };

        document.addEventListener('DOMContentLoaded', async () => {
            await loadOptions();
            initCpmkCplMultiselect();
            initMkCplMultiselect();
            initDosenMkMultiselect();
            await Promise.all([loadList('cpmkCpl'), loadList('mkCpl'), loadList('dosenMk')]);
        });

        function initCpmkCplMultiselect() {
            const selectEl = document.getElementById('mapCpmkIdsForCpl');
            if (!selectEl || typeof TomSelect === 'undefined') return;

            if (cpmkCplTomSelect) {
                cpmkCplTomSelect.destroy();
                cpmkCplTomSelect = null;
            }

            cpmkCplTomSelect = new TomSelect(selectEl, {
                plugins: { remove_button: { title: 'Hapus CPMK ini' } },
                create: false,
                maxOptions: 300,
                placeholder: 'Pilih satu atau lebih CPMK',
                searchField: ['text'],
                hideSelected: true,
            });
        }

        function initMkCplMultiselect() {
            const selectEl = document.getElementById('mapCplIdsForMk');
            if (!selectEl || typeof TomSelect === 'undefined') return;

            if (mkCplTomSelect) {
                mkCplTomSelect.destroy();
                mkCplTomSelect = null;
            }

            mkCplTomSelect = new TomSelect(selectEl, {
                plugins: { remove_button: { title: 'Hapus CPL ini' } },
                create: false,
                maxOptions: 300,
                placeholder: 'Pilih satu atau lebih CPL',
                searchField: ['text'],
                hideSelected: true,
            });
        }

        function initDosenMkMultiselect() {
            const selectEl = document.getElementById('mapDosenIdsForMk');
            if (!selectEl || typeof TomSelect === 'undefined') return;

            if (dosenMkTomSelect) {
                dosenMkTomSelect.destroy();
                dosenMkTomSelect = null;
            }

            dosenMkTomSelect = new TomSelect(selectEl, {
                plugins: {
                    remove_button: {
                        title: 'Hapus dosen ini',
                    },
                },
                create: false,
                maxOptions: 300,
                placeholder: 'Pilih satu atau lebih dosen',
                searchField: ['text'],
                hideSelected: true,
            });
        }

        async function readApiResponse(response) {
            const contentType = response.headers.get('content-type') || '';
            if (contentType.includes('application/json')) {
                return response.json();
            }
            const text = await response.text();
            return { success: false, message: text || `Request gagal dengan status ${response.status}` };
        }

        function toFriendlyMessage(message, fallback = 'Terjadi kesalahan pada sistem') {
            if (!message || typeof message !== 'string') return fallback;
            const lowered = message.toLowerCase();
            if (lowered.includes('doctype') || lowered.includes('<html')) {
                return 'Terjadi kesalahan pada server. Silakan coba lagi.';
            }
            return message;
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function clearErrors() {
            document.querySelectorAll('.form-error').forEach((el) => {
                el.classList.remove('show');
                el.textContent = '';
            });
        }

        async function loadOptions() {
            try {
                const response = await fetch(`${BASE_API}/options`, {
                    headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
                });
                const result = await readApiResponse(response);
                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Gagal memuat opsi pemetaan');
                }

                state.options = result.data || state.options;
                fillSelectOptions();
            } catch (error) {
                showError(toFriendlyMessage(error.message, 'Gagal memuat opsi pemetaan'));
            }
        }

        function fillSelectOptions() {
            fillSelect('mapCpmkIdsForCpl', state.options.cpmk, (row) => `${row.kode}`);
            fillSelect('mapCplIdForCpmk', state.options.cpl, (row) => `${row.kode}`);
            fillSelect('mapMkIdForCpl', state.options.mata_kuliah, (row) => `${row.kode} - ${row.nama}`);
            fillSelect('mapCplIdsForMk', state.options.cpl, (row) => `${row.kode}`);
            fillSelect('mapMkIdForDosen', state.options.mata_kuliah, (row) => `${row.kode} - ${row.nama}`);
            fillSelect('mapDosenIdsForMk', state.options.dosen, (row) => `${row.name}`);
        }

        function fillSelect(id, list, labelResolver) {
            const select = document.getElementById(id);
            if (!select) return;

            const tomSelectMap = {
                mapCpmkIdsForCpl: cpmkCplTomSelect,
                mapCplIdsForMk: mkCplTomSelect,
                mapDosenIdsForMk: dosenMkTomSelect,
            };
            const currentTomSelect = tomSelectMap[id] || null;

            select.innerHTML = '';
            if (!select.multiple) {
                select.innerHTML = '<option value="">Pilih data</option>';
            }

            (list || []).forEach((row) => {
                const option = document.createElement('option');
                option.value = String(row.id);
                option.textContent = labelResolver(row);
                select.appendChild(option);
            });

            if (currentTomSelect) {
                currentTomSelect.clear(true);
                currentTomSelect.sync();
            }
        }

        async function loadList(key) {
            try {
                const response = await fetch(mappingConfig[key].listApi, {
                    headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
                });
                const result = await readApiResponse(response);
                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Gagal memuat data pemetaan');
                }

                state[key].all = result.data || [];
                applyFilterSort(key, false);
            } catch (error) {
                showError(toFriendlyMessage(error.message, 'Gagal memuat data pemetaan'));
            }
        }

        function handleSearch(key) {
            clearTimeout(state[key].timer);
            state[key].timer = setTimeout(() => {
                state[key].currentPage = 1;
                applyFilterSort(key, false);
            }, 300);
        }

        function handleSort(key) {
            state[key].currentPage = 1;
            applyFilterSort(key, false);
        }

        function applyFilterSort(key, keepPage = false) {
            const cfg = mappingConfig[key];
            const searchValue = document.getElementById(cfg.searchId).value.trim().toLowerCase();
            const sortDirection = document.getElementById(cfg.sortId).value;

            let data = [...state[key].all];
            if (searchValue) {
                data = data.filter((item) => cfg.filterText(item).toLowerCase().includes(searchValue));
            }

            data.sort((a, b) => {
                const left = cfg.sortText(a).toLowerCase();
                const right = cfg.sortText(b).toLowerCase();

                if (left === right) return 0;
                if (sortDirection === 'asc') return left > right ? 1 : -1;
                return left < right ? 1 : -1;
            });

            state[key].filtered = data;
            if (!keepPage) {
                state[key].currentPage = 1;
            }

            renderTable(key);
        }

        function renderTable(key) {
            const cfg = mappingConfig[key];
            const body = document.getElementById(cfg.tableBodyId);
            const empty = document.getElementById(cfg.emptyId);
            const pagination = document.getElementById(cfg.paginationId);
            const paginationList = document.getElementById(cfg.paginationListId);

            const totalItems = state[key].filtered.length;
            const totalPages = Math.max(1, Math.ceil(totalItems / PAGE_SIZE));

            if (state[key].currentPage > totalPages) {
                state[key].currentPage = totalPages;
            }

            const start = (state[key].currentPage - 1) * PAGE_SIZE;
            const pageItems = state[key].filtered.slice(start, start + PAGE_SIZE);

            if (totalItems === 0) {
                body.innerHTML = '';
                empty.style.display = 'block';
                pagination.style.display = 'none';
                return;
            }

            empty.style.display = 'none';
            pagination.style.display = totalPages > 1 ? 'flex' : 'none';
            body.innerHTML = renderRows(key, pageItems);
            paginationList.innerHTML = renderPaginationButtons(key, totalPages);
        }

        function renderRows(key, pageItems) {
            if (key === 'cpmkCpl') {
                return pageItems.map((item) => `
                    <tr>
                        <td><span class="mapping-pill">${escapeHtml(item.cpl_kode)}</span></td>
                        <td class="desc-cell">
                            ${(item.cpmk_items || []).map((cpmk) => `<span class="kode-badge" style="margin-right:6px;margin-bottom:6px;display:inline-flex;align-items:center;gap:6px;">${escapeHtml(cpmk.cpmk_kode)} <button type="button" class="btn-icon btn-icon-delete" style="width:20px;height:20px;border-radius:999px;padding:0;" title="Hapus CPMK dari CPL" onclick="deleteCpmkCpl(${item.cpl_id}, ${cpmk.cpmk_id})"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></span>`).join('')}
                        </td>
                    </tr>
                `).join('');
            }

            if (key === 'mkCpl') {
                return pageItems.map((item) => `
                    <tr>
                        <td class="desc-cell">${escapeHtml(item.mk_nama)}</td>
                        <td class="desc-cell">
                            ${(item.cpl_items || []).map((cpl) => `<span class="kode-badge" style="margin-right:6px;margin-bottom:6px;display:inline-flex;align-items:center;gap:6px;">${escapeHtml(cpl.cpl_kode)} <button type="button" class="btn-icon btn-icon-delete" style="width:20px;height:20px;border-radius:999px;padding:0;" title="Hapus CPL dari MK" onclick="deleteMkCpl(${item.mk_id}, ${cpl.cpl_id})"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></span>`).join('')}
                        </td>
                    </tr>
                `).join('');
            }

            return pageItems.map((item) => `
                <tr>
                    <td class="desc-cell">${escapeHtml(item.mk_nama)}</td>
                    <td class="desc-cell">
                        ${(item.dosen_items || []).map((dosen) => `<span class="kode-badge" style="margin-right:6px;margin-bottom:6px;display:inline-flex;align-items:center;gap:6px;">${escapeHtml(dosen.dosen_nama)} <button type="button" class="btn-icon btn-icon-delete" style="width:20px;height:20px;border-radius:999px;padding:0;" title="Hapus dosen dari MK" onclick="deleteDosenMk(${dosen.id})"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></span>`).join('')}
                    </td>
                </tr>
            `).join('');
        }

        function renderPaginationButtons(key, totalPages) {
            const page = state[key].currentPage;
            const buttons = [];
            buttons.push(`<button type="button" class="pagination-btn" onclick="goToPage('${key}', ${Math.max(1, page - 1)})" ${page === 1 ? 'disabled' : ''}>&lsaquo;</button>`);
            for (let i = 1; i <= totalPages; i++) {
                buttons.push(`<button type="button" class="pagination-btn ${i === page ? 'active' : ''}" onclick="goToPage('${key}', ${i})">${i}</button>`);
            }
            buttons.push(`<button type="button" class="pagination-btn" onclick="goToPage('${key}', ${Math.min(totalPages, page + 1)})" ${page === totalPages ? 'disabled' : ''}>&rsaquo;</button>`);
            return buttons.join('');
        }

        function goToPage(key, page) {
            state[key].currentPage = page;
            renderTable(key);
        }

        function openModal(type) {
            clearErrors();
            if (type === 'cpmkCpl') {
                document.getElementById('mapCplIdForCpmk').value = '';
                if (cpmkCplTomSelect) {
                    cpmkCplTomSelect.clear(true);
                }
                document.getElementById('modalCpmkCpl').classList.add('show');
                return;
            }
            if (type === 'mkCpl') {
                document.getElementById('mapMkIdForCpl').value = '';
                if (mkCplTomSelect) {
                    mkCplTomSelect.clear(true);
                }
                document.getElementById('modalMkCpl').classList.add('show');
                return;
            }

            document.getElementById('mapMkIdForDosen').value = '';
            if (dosenMkTomSelect) {
                dosenMkTomSelect.clear(true);
            } else {
                const dosenSelect = document.getElementById('mapDosenIdsForMk');
                Array.from(dosenSelect.options).forEach((option) => {
                    option.selected = false;
                });
            }
            document.getElementById('modalDosenMk').classList.add('show');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('show');
            clearErrors();
        }

        function closeModalOnBackdrop(event, id) {
            if (event.target.id === id) {
                closeModal(id);
            }
        }

        function showValidationErrors(errors = {}, fieldMap = {}) {
            Object.entries(errors).forEach(([field, messages]) => {
                const normalizedField = field.replace(/\.\d+$/, '');
                const targetId = fieldMap[field] || fieldMap[normalizedField] || field;
                const el = document.getElementById(`error-${targetId}`);
                if (!el) return;
                el.textContent = Array.isArray(messages) ? messages[0] : String(messages);
                el.classList.add('show');
            });
        }

        async function submitCpmkCpl(event) {
            event.preventDefault();
            clearErrors();

            const cpmkIds = Array.from(document.getElementById('mapCpmkIdsForCpl').selectedOptions)
                .map((option) => Number(option.value))
                .filter((value) => Number.isInteger(value) && value > 0);
            const payload = {
                cpl_id: Number(document.getElementById('mapCplIdForCpmk').value),
                cpmk_ids: cpmkIds,
            };

            await createMapping(`${BASE_API}/cpmk-cpl`, payload, 'modalCpmkCpl', 'cpmkCpl', {
                cpl_id: 'mapCplIdForCpmk',
                cpmk_ids: 'mapCpmkIdsForCpl',
                'cpmk_ids.0': 'mapCpmkIdsForCpl',
            });
        }

        async function submitMkCpl(event) {
            event.preventDefault();
            clearErrors();

            const cplIds = Array.from(document.getElementById('mapCplIdsForMk').selectedOptions)
                .map((option) => Number(option.value))
                .filter((value) => Number.isInteger(value) && value > 0);
            const payload = {
                mk_id: Number(document.getElementById('mapMkIdForCpl').value),
                cpl_ids: cplIds,
            };

            await createMapping(`${BASE_API}/mk-cpl`, payload, 'modalMkCpl', 'mkCpl', {
                mk_id: 'mapMkIdForCpl',
                cpl_ids: 'mapCplIdsForMk',
                'cpl_ids.0': 'mapCplIdsForMk',
            });
        }

        async function submitDosenMk(event) {
            event.preventDefault();
            clearErrors();

            const userIds = Array.from(document.getElementById('mapDosenIdsForMk').selectedOptions)
                .map((option) => Number(option.value))
                .filter((value) => Number.isInteger(value) && value > 0);
            const payload = {
                mk_id: Number(document.getElementById('mapMkIdForDosen').value),
                user_ids: userIds,
            };

            await createMapping(`${BASE_API}/dosen-mk`, payload, 'modalDosenMk', 'dosenMk', {
                mk_id: 'mapMkIdForDosen',
                user_ids: 'mapDosenIdsForMk',
                'user_ids.0': 'mapDosenIdsForMk',
            });
        }

        async function createMapping(url, payload, modalId, listKey, fieldMap = {}) {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                });

                const result = await readApiResponse(response);
                if (!response.ok) {
                    if (result.errors) {
                        showValidationErrors(result.errors, fieldMap);
                    }
                    throw new Error(result.message || 'Gagal menyimpan pemetaan');
                }

                closeModal(modalId);
                await loadList(listKey);
                showSuccess(result.message || 'Pemetaan berhasil disimpan');
            } catch (error) {
                showError(toFriendlyMessage(error.message, 'Gagal menyimpan pemetaan'));
            }
        }

        async function deleteCpmkCpl(cplId, cpmkId) {
            await deleteMapping(`${BASE_API}/cpmk-cpl`, { cpl_id: cplId, cpmk_id: cpmkId }, 'cpmkCpl', 'Pemetaan CPMK ke CPL');
        }

        async function deleteMkCpl(mkId, cplId) {
            await deleteMapping(`${BASE_API}/mk-cpl`, { mk_id: mkId, cpl_id: cplId }, 'mkCpl', 'Pemetaan MK ke CPL');
        }

        async function deleteDosenMk(id) {
            await deleteMapping(`${BASE_API}/dosen-mk/${id}`, null, 'dosenMk', 'Pemetaan Dosen ke MK');
        }

        async function deleteMapping(url, payload, listKey, label) {
            const confirm = await Swal.fire({
                title: `Hapus ${label}?`,
                text: 'Data pemetaan yang dihapus tidak dapat dikembalikan',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
            });

            if (!confirm.isConfirmed) return;

            try {
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: payload ? JSON.stringify(payload) : null,
                });

                const result = await readApiResponse(response);
                if (!response.ok) {
                    throw new Error(result.message || 'Gagal menghapus pemetaan');
                }

                await loadList(listKey);
                showSuccess(result.message || 'Pemetaan berhasil dihapus');
            } catch (error) {
                showError(toFriendlyMessage(error.message, 'Gagal menghapus pemetaan'));
            }
        }

        function showSuccess(message) {
            Swal.fire({ icon: 'success', title: 'Berhasil', text: message, confirmButtonColor: '#3b82f6' });
        }

        function showError(message) {
            Swal.fire({ icon: 'error', title: 'Error', text: message, confirmButtonColor: '#ef4444' });
        }
    </script>
    @endpush
    </div><!-- end page-container -->
</x-banksoal::layouts.admin>