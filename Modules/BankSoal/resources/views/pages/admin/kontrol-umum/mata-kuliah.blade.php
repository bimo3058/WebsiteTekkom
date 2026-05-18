<x-banksoal::layouts.admin>
    @section('breadcrumbs')
    <a href="#" class="text-slate-500 hover:text-primary transition-colors">Kontrol Umum</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Manajemen Data</span>
    @endsection

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: rgb(11, 38, 110);
            --primary-hover: rgb(8, 28, 82);
            --danger-red: #ef4444;
            --danger-hover: #dc2626;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
        }

        * {
            box-sizing: border-box;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            gap: 16px;
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
            box-shadow: 0 2px 4px rgba(11, 38, 110, 0.1);
        }

        .btn-add svg {
            width: 18px;
            height: 18px;
            color: #1e293b;
        }

        .bulk-delete-bar {
            display: none;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            background: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 16px;
            animation: slideDown 0.2s ease-out;
        }

        .bulk-delete-bar.show {
            display: flex;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .bulk-delete-info span {
            font-weight: 600;
            color: #92400e;
        }

        .bulk-delete-actions {
            display: flex;
            gap: 8px;
        }

        .btn-bulk-delete {
            background: var(--danger-red);
            color: #fff;
            padding: 8px 14px;
            border-radius: 6px;
            border: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-bulk-delete:hover {
            background: var(--danger-hover);
        }

        .btn-cancel-select {
            background: var(--slate-200);
            color: var(--slate-700);
            padding: 8px 14px;
            border-radius: 6px;
            border: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-cancel-select:hover {
            background: var(--slate-300);
        }

        .controls-section {
            background: #fff;
            border: 1px solid var(--slate-200);
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }

        .search-box {
            flex: 1;
            min-width: 220px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 10px 14px 10px 36px;
            border: 1px solid var(--slate-300);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
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
        }

        .filter-group label {
            font-size: 13px;
            font-weight: 600;
            color: var(--slate-600);
            white-space: nowrap;
        }

        .filter-group select {
            padding: 9px 12px;
            border: 1px solid var(--slate-300);
            border-radius: 8px;
            font-size: 13px;
            background: #fff;
            min-width: 130px;
            cursor: pointer;
        }

        .table-section {
            background: #fff;
            border: 1px solid var(--slate-200);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: var(--slate-100);
            border-bottom: 2px solid var(--slate-200);
        }

        th {
            padding: 14px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            color: var(--slate-700);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--slate-200);
            font-size: 14px;
            color: var(--slate-700);
            vertical-align: middle;
        }

        tbody tr:hover {
            background: var(--slate-50);
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .cb-col { width: 40px; padding: 0 8px 0 16px !important; }
        .cb-col input[type=checkbox] { width: 16px; height: 16px; cursor: pointer; accent-color: var(--primary-blue); margin: 0; }

        /* Toggle Switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .switch input { 
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--slate-300);
            transition: .3s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        input:checked + .slider {
            background-color: var(--primary-blue);
        }

        input:checked + .slider:before {
            transform: translateX(20px);
        }

        input:disabled + .slider {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .status-text {
            font-size: 13px;
            font-weight: 600;
        }
        
        .status-text.active {
            color: var(--primary-blue);
        }
        
        .status-text.inactive {
            color: var(--slate-400);
        }

        tbody tr:hover {
            background: var(--slate-50);
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary-blue);
        }

        /* ── 3-dot dropdown ── */
        .dots-wrap { position: relative; display: inline-block; }
        .btn-dots {
            display: inline-flex; align-items: center; justify-content: center;
            width: 32px; height: 32px; border-radius: 6px;
            border: 1px solid var(--slate-200); background: #fff;
            font-size: 18px; cursor: pointer; color: var(--slate-600);
            transition: all 0.15s; line-height: 1;
        }
        .btn-dots:hover { border-color: var(--primary-blue); color: var(--primary-blue); background: #f0f4ff; }
        .dots-menu {
            display: none; position: absolute; right: 0; top: 38px;
            background: #fff; border: 1px solid var(--slate-200);
            border-radius: 8px; box-shadow: 0 4px 16px rgba(15,23,42,0.1);
            min-width: 140px; z-index: 50; overflow: hidden;
        }
        .dots-menu.open { display: block; }
        .dots-menu button {
            display: flex; align-items: center; gap: 8px;
            width: 100%; padding: 9px 14px;
            background: none; border: none; border-bottom: 1px solid var(--slate-100);
            font-size: 13px; font-weight: 500; color: var(--slate-700);
            cursor: pointer; text-align: left;
        }
        .dots-menu button:last-child { border-bottom: none; }
        .dots-menu button:hover:not(:disabled) { background: var(--slate-50); }
        .dots-menu button:disabled { color: var(--slate-400); cursor: not-allowed; opacity: 0.7; }
        .dots-menu button:disabled svg { opacity: 0.6; }
        .dots-menu .menu-delete:not(:disabled) { color: var(--danger-red); }
        .dots-menu .menu-delete:hover:not(:disabled) { background: #fef2f2; }

        /* ── Table loading ── */
        .tbl-loading {
            display: none; align-items: center; justify-content: center;
            gap: 10px; padding: 48px 20px; color: var(--slate-400);
            font-size: 14px;
        }
        .tbl-loading.show { display: flex; }
        .tbl-spinner {
            width: 24px; height: 24px;
            border: 3px solid var(--slate-200);
            border-top-color: var(--primary-blue);
            border-radius: 50%;
            animation: tbl-spin 0.7s linear infinite; flex-shrink: 0;
        }
        @keyframes tbl-spin { to { transform: rotate(360deg); } }

        .empty-state {
            text-align: center;
            padding: 48px 20px;
        }

        .empty-state svg {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            color: var(--slate-400);
            opacity: 0.4;
        }

        .empty-state p {
            font-size: 16px;
            color: var(--slate-600);
            margin: 0;
        }

        .pagination-section {
            display: flex;
            justify-content: flex-end;
            padding: 14px 16px 18px;
            border-top: 1px solid var(--slate-200);
        }

        .pagination-list {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .pagination-btn {
            min-width: 34px;
            height: 34px;
            padding: 0 12px;
            border: 1px solid var(--slate-200);
            background: #fff;
            color: var(--slate-700);
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
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
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 100;
            justify-content: center;
            align-items: center;
        }

        .modal-overlay.show {
            display: flex;
            animation: fadeIn 0.2s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: #fff;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            padding: 20px;
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
            width: 32px;
            height: 32px;
            border-radius: 6px;
        }

        .modal-close:hover {
            background: var(--slate-100);
            color: var(--slate-600);
        }

        .modal-body {
            padding: 24px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            color: var(--slate-700);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--slate-300);
            border-radius: 8px;
            font-size: 14px;
        }

        .form-group input:focus,
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

        .form-error.show {
            display: block;
        }

        .sks-counter {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sks-counter button {
            width: 36px;
            height: 36px;
            border: 1px solid var(--slate-300);
            background: var(--slate-50);
            border-radius: 6px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            color: var(--slate-600);
        }

        .sks-counter input {
            width: 60px;
            text-align: center;
            font-weight: 600;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--slate-200);
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .btn {
            padding: 10px 18px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--primary-blue);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
        }

        .btn-secondary {
            background: var(--slate-200);
            color: var(--slate-700);
        }

        .btn-secondary:hover {
            background: var(--slate-300);
        }

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-add {
                width: 100%;
                justify-content: center;
            }

            .controls-section {
                flex-direction: column;
            }

            .search-box,
            .filter-group,
            .filter-group select {
                width: 100%;
            }

            .filter-group {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
    @endpush

    <div class="page-header">
        <div class="header-content">
            <h1>Manajemen Data Mata Kuliah</h1>
            <p>Kelola data mata kuliah untuk program studi</p>
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="button" onclick="syncSemester()" class="btn-add" style="background: var(--slate-100); color: var(--slate-700); border-color: var(--slate-200);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Sinkronisasi
            </button>
            <a href="{{ route('banksoal.admin.kontrol-umum.mata-kuliah.create') }}" class="btn-add" style="text-decoration: none;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Mata Kuliah
            </a>
        </div>
    </div>

    <div class="bulk-delete-bar" id="bulkDeleteBar">
        <div class="bulk-delete-info">
            <span id="selectedCount">0 mata kuliah terpilih</span>
        </div>
        <div class="bulk-delete-actions">
            <button type="button" onclick="bulkDelete()" class="btn-bulk-delete">Hapus Terpilih</button>
            <button type="button" onclick="cancelBulkSelect()" class="btn-cancel-select">Batal</button>
        </div>
    </div>

    <div class="controls-section">
        <div class="search-box">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text" id="searchInput" placeholder="Cari kode atau nama..." onkeyup="handleSearch(this.value)">
        </div>

        <div class="filter-group">
            <label for="sortField">Sort By:</label>
            <select id="sortField" onchange="handleSort()">
                <option value="kode">Kode</option>
                <option value="nama">Nama</option>
                <option value="sks">SKS</option>
                <option value="semester">Semester</option>
            </select>
        </div>

        <div class="filter-group">
            <label for="sortDirection">Order By:</label>
            <select id="sortDirection" onchange="handleSort()">
                <option value="asc">Ascending</option>
                <option value="desc">Descending</option>
            </select>
        </div>
    </div>

    <div class="table-section">
        <div class="tbl-loading" id="tblLoading">
            <div class="tbl-spinner"></div>
            Memuat data...
        </div>
        <div class="table-wrapper" id="tblWrapper" style="display:none;">
            <table>
                <thead>
                    <tr>
                        <th class="cb-col">
                            <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                        </th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>SKS</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th style="width:56px;"></th>
                    </tr>
                </thead>
                <tbody id="tableBody"></tbody>
            </table>
        </div>

        <div id="emptyState" class="empty-state" style="display: none;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p>Tidak ada mata kuliah ditemukan</p>
        </div>

        <div class="pagination-section" id="paginationSection" style="display: none;">
            <div class="pagination-list" id="paginationList"></div>
        </div>
    </div>

    <!-- CPL Section -->
    <div class="page-header" style="margin-top: 48px;">
        <div class="header-content">
            <h1>Manajemen Data CPL</h1>
            <p>Kelola data Capaian Pembelajaran Lulusan</p>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('banksoal.admin.kontrol-umum.cpl.create') }}" class="btn-add" style="text-decoration: none;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah CPL
            </a>
        </div>
    </div>

    <div class="controls-section">
        <div class="search-box">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text" id="cplSearch" placeholder="Cari kode atau deskripsi CPL..." onkeyup="handleSearchCpl(this.value)">
        </div>
        <div class="filter-group">
            <label for="cplSortDirection">Order By:</label>
            <select id="cplSortDirection" onchange="handleSortCpl()">
                <option value="asc">Ascending</option>
                <option value="desc">Descending</option>
            </select>
        </div>
    </div>

    <div class="table-section">
        <div class="tbl-loading" id="cplLoading"><div class="tbl-spinner"></div> Memuat data...</div>
        <div class="table-wrapper" id="cplTableWrapper" style="display:none;">
            <table>
                <thead>
                    <tr>
                        <th style="width: 140px;">Kode</th>
                        <th>Deskripsi</th>
                        <th style="width: 56px;"></th>
                    </tr>
                </thead>
                <tbody id="cplTableBody"></tbody>
            </table>
        </div>
        <div id="cplEmptyState" class="empty-state" style="display:none;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p>Tidak ada data CPL</p>
        </div>
        <div class="pagination-section" id="cplPagination" style="display:none;">
            <div class="pagination-list" id="cplPaginationList"></div>
        </div>
    </div>



    <!-- Modals removed as we now use standalone pages -->

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
    <script>
        const API_URL = '{{ url("/bank-soal/admin/api/mata-kuliah") }}';
        const EDIT_MK_URL = '{{ url("/bank-soal/admin/kontrol-umum/mata-kuliah") }}';
        const EDIT_CPL_URL = '{{ url("/bank-soal/admin/kontrol-umum/cpl") }}';
        const csrfToken = '{{ csrf_token() }}';
        const PAGE_SIZE = 10;

        let allMataKuliah = [];
        let filteredMataKuliah = [];
        let searchTimeout;
        let currentPage = 1;

        async function readApiResponse(response) {
            const contentType = response.headers.get('content-type') || '';

            if (contentType.includes('application/json')) {
                return response.json();
            }

            const text = await response.text();
            return {
                success: false,
                message: text || `Request gagal dengan status ${response.status}`,
            };
        }

        function toFriendlyMessage(message, fallback = 'Terjadi kesalahan pada sistem') {
            if (!message || typeof message !== 'string') {
                return fallback;
            }

            const lowered = message.toLowerCase();

            if (lowered.includes('doctype') || lowered.includes('html')) {
                return 'Terjadi kesalahan pada server. Silakan coba lagi.';
            }

            return message;
        }

        function mapFieldError(field, message) {
            if (!message || typeof message !== 'string') {
                return 'Input tidak valid.';
            }

            const lowered = message.toLowerCase();

            if (field === 'kode' && (lowered.includes('taken') || lowered.includes('sudah terdaftar') || lowered.includes('unique'))) {
                return 'Kode mata kuliah sudah terdaftar. Gunakan kode lain.';
            }

            if (field === 'nama' && (lowered.includes('taken') || lowered.includes('sudah terdaftar') || lowered.includes('unique'))) {
                return 'Nama mata kuliah sudah terdaftar. Gunakan nama lain.';
            }

            return message;
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadAllMataKuliah();
            loadAllCpl();
        });

        async function loadAllMataKuliah() {
            document.getElementById('tblLoading').classList.add('show');
            document.getElementById('tblWrapper').style.display = 'none';
            document.getElementById('emptyState').style.display = 'none';
            try {
                const response = await fetch(API_URL, {
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                const result = await readApiResponse(response);
                if (result.success) {
                    allMataKuliah = result.data;
                    filteredMataKuliah = [...allMataKuliah];
                    currentPage = 1;
                    renderTable();
                } else {
                    throw new Error(toFriendlyMessage(result.message, 'Gagal memuat data mata kuliah'));
                }
            } catch (error) {
                showError(toFriendlyMessage(error.message, 'Gagal memuat data mata kuliah'));
            } finally {
                document.getElementById('tblLoading').classList.remove('show');
                document.getElementById('tblWrapper').style.display = 'block';
            }
        }

        function handleSearch(value) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const query = value.toLowerCase().trim();
                if (query === '') {
                    filteredMataKuliah = [...allMataKuliah];
                } else {
                    filteredMataKuliah = allMataKuliah.filter((mk) =>
                        mk.kode.toLowerCase().includes(query) || mk.nama.toLowerCase().includes(query)
                    );
                }

                currentPage = 1;
                document.getElementById('selectAllCheckbox').checked = false;
                updateBulkDeleteUI();
                handleSort();
            }, 300);
        }

        function handleSort() {
            const sortField = document.getElementById('sortField').value;
            const sortDirection = document.getElementById('sortDirection').value;

            filteredMataKuliah.sort((a, b) => {
                let aVal = a[sortField];
                let bVal = b[sortField];

                if (sortField === 'sks' || sortField === 'semester') {
                    aVal = Number(aVal);
                    bVal = Number(bVal);
                } else {
                    aVal = String(aVal).toLowerCase();
                    bVal = String(bVal).toLowerCase();
                }

                if (aVal === bVal) return 0;
                if (sortDirection === 'asc') return aVal > bVal ? 1 : -1;
                return aVal < bVal ? 1 : -1;
            });

            if (currentPage < 1) currentPage = 1;
            renderTable();
        }

        // ── 3-dot dropdown ──────────────────────────────────────────
        function toggleDots(btn) {
            const menu = btn.nextElementSibling;
            const isOpen = menu.classList.contains('open');
            
            // Close all other menus and reset their inline styles
            document.querySelectorAll('.dots-menu.open').forEach(m => {
                m.classList.remove('open');
                m.style.top = '';
                m.style.bottom = '';
                m.style.left = '';
                m.style.right = '';
            });

            if (!isOpen) {
                menu.classList.add('open');
                
                // Collision detection
                const rect = menu.getBoundingClientRect();
                const viewHeight = Math.max(document.documentElement.clientHeight, window.innerHeight);
                const viewWidth = Math.max(document.documentElement.clientWidth, window.innerWidth);
                
                // If it overflows at the bottom, push it upwards
                if (rect.bottom > viewHeight) {
                    menu.style.top = 'auto';
                    menu.style.bottom = '38px';
                }
                
                // If it overflows at the left, push it rightwards
                if (rect.left < 0) {
                    menu.style.right = 'auto';
                    menu.style.left = '0';
                }
            }
        }
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.dots-wrap')) {
                document.querySelectorAll('.dots-menu.open').forEach(m => {
                    m.classList.remove('open');
                    m.style.top = '';
                    m.style.bottom = '';
                    m.style.left = '';
                    m.style.right = '';
                });
            }
        });

        const ICON_EDIT   = `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>`;
        const ICON_DEL    = `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>`;

        function renderTable() {
            const tableBody = document.getElementById('tableBody');
            const emptyState = document.getElementById('emptyState');
            const paginationSection = document.getElementById('paginationSection');

            const totalItems = filteredMataKuliah.length;
            const totalPages = Math.max(1, Math.ceil(totalItems / PAGE_SIZE));
            if (currentPage > totalPages) currentPage = totalPages;

            const startIndex = (currentPage - 1) * PAGE_SIZE;
            const pageItems = filteredMataKuliah.slice(startIndex, startIndex + PAGE_SIZE);

            if (totalItems === 0) {
                tableBody.innerHTML = '';
                emptyState.style.display = 'block';
                paginationSection.style.display = 'none';
                return;
            }

            emptyState.style.display = 'none';
            paginationSection.style.display = totalPages > 1 ? 'flex' : 'none';
            tableBody.innerHTML = pageItems.map((mk) => `
                <tr style="${!mk.is_active ? 'opacity: 0.8; background-color: #fafafa;' : ''}">
                    <td class="cb-col"><input type="checkbox" class="mk-checkbox" value="${mk.id}" onchange="updateBulkDeleteUI()"></td>
                    <td><span style="font-weight:700;color:#1e293b">${escapeHtml(mk.kode)}</span></td>
                    <td>${escapeHtml(mk.nama)}</td>
                    <td>${mk.sks} SKS</td>
                    <td>Semester ${mk.semester}</td>
                    <td>
                        <div class="status-badge">
                            <label class="switch">
                                <input type="checkbox" onchange="toggleActive(${mk.id}, this)" ${mk.is_active ? 'checked' : ''}>
                                <span class="slider"></span>
                            </label>
                            <span class="status-text ${mk.is_active ? 'active' : 'inactive'}">
                                ${mk.is_active ? 'Aktif' : 'Tidak Aktif'}
                            </span>
                        </div>
                    </td>
                    <td style="text-align:center">
                        <div class="dots-wrap">
                            <button class="btn-dots" onclick="toggleDots(this)" title="Aksi">&#8943;</button>
                            <div class="dots-menu">
                                <a href="${EDIT_MK_URL}/${mk.id}/edit" class="dots-menu-link" style="display:flex;align-items:center;gap:8px;padding:9px 14px;font-size:13px;text-decoration:none;color:var(--slate-700);border-bottom:1px solid var(--slate-100);">${ICON_EDIT} Edit</a>
                                <button class="menu-delete" onclick="deleteMataKuliah(${mk.id})">${ICON_DEL} Hapus</button>
                            </div>
                        </div>
                    </td>
                </tr>
            `).join('');

            renderPagination(totalPages);
        }

        function renderPagination(totalPages) {
            const paginationList = document.getElementById('paginationList');
            if (totalPages <= 1) {
                paginationList.innerHTML = '';
                return;
            }

            const buttons = [];
            buttons.push(`<button type="button" class="pagination-btn" onclick="goToPage(${Math.max(1, currentPage - 1)})" ${currentPage === 1 ? 'disabled' : ''}>&lsaquo;</button>`);

            for (let page = 1; page <= totalPages; page++) {
                buttons.push(`<button type="button" class="pagination-btn ${page === currentPage ? 'active' : ''}" onclick="goToPage(${page})">${page}</button>`);
            }

            buttons.push(`<button type="button" class="pagination-btn" onclick="goToPage(${Math.min(totalPages, currentPage + 1)})" ${currentPage === totalPages ? 'disabled' : ''}>&rsaquo;</button>`);
            paginationList.innerHTML = buttons.join('');
        }

        function goToPage(page) {
            currentPage = page;
            document.getElementById('selectAllCheckbox').checked = false;
            updateBulkDeleteUI();
            renderTable();
        }



        function incrementSKS() {
            const input = document.getElementById('sks');
            if (Number(input.value) < 3) {
                input.value = String(Number(input.value) + 1);
            }
        }

        function decrementSKS() {
            const input = document.getElementById('sks');
            if (Number(input.value) > 1) {
                input.value = String(Number(input.value) - 1);
            }
        }


        async function deleteMataKuliah(id) {
            const mk = allMataKuliah.find((item) => Number(item.id) === Number(id));
            if (!mk) {
                showError('Data mata kuliah tidak ditemukan. Silakan muat ulang halaman.');
                return;
            }

            Swal.fire({
                title: 'Hapus Mata Kuliah?',
                text: `Apakah Anda yakin ingin menghapus "${mk.nama}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
            }).then(async (dialogResult) => {
                if (!dialogResult.isConfirmed) return;

                try {
                    const response = await fetch(`${API_URL}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                    });

                    const result = await readApiResponse(response);
                    if (!response.ok) {
                        throw new Error(toFriendlyMessage(result.message, 'Gagal menghapus data'));
                    }

                    allMataKuliah = allMataKuliah.filter((item) => Number(item.id) !== Number(id));
                    const currentSearch = document.getElementById('searchInput').value;
                    if (currentPage > 1 && filteredMataKuliah.length % PAGE_SIZE === 1) {
                        currentPage = Math.max(1, currentPage - 1);
                    }
                    handleSearch(currentSearch);
                    showSuccess('Mata kuliah berhasil dihapus');
                } catch (error) {
                    showError(toFriendlyMessage(error.message, 'Gagal menghapus data'));
                }
            });
        }

        function toggleSelectAll() {
            const checked = document.getElementById('selectAllCheckbox').checked;
            document.querySelectorAll('.mk-checkbox').forEach((cb) => {
                cb.checked = checked;
            });
            updateBulkDeleteUI();
        }

        function updateBulkDeleteUI() {
            const checkedCount = document.querySelectorAll('.mk-checkbox:checked').length;
            const bulkBar = document.getElementById('bulkDeleteBar');
            const selectedCount = document.getElementById('selectedCount');

            if (checkedCount > 0) {
                selectedCount.textContent = `${checkedCount} mata kuliah terpilih`;
                bulkBar.classList.add('show');
            } else {
                bulkBar.classList.remove('show');
                document.getElementById('selectAllCheckbox').checked = false;
            }
        }

        function cancelBulkSelect() {
            document.querySelectorAll('.mk-checkbox').forEach((cb) => {
                cb.checked = false;
            });
            document.getElementById('selectAllCheckbox').checked = false;
            updateBulkDeleteUI();
        }

        async function bulkDelete() {
            const selectedIds = Array.from(document.querySelectorAll('.mk-checkbox:checked')).map((cb) => Number(cb.value));

            if (selectedIds.length === 0) {
                showWarning('Pilih minimal satu mata kuliah');
                return;
            }

            Swal.fire({
                title: `Hapus ${selectedIds.length} Mata Kuliah?`,
                text: 'Data yang dihapus tidak dapat dipulihkan',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus Semua',
                cancelButtonText: 'Batal',
            }).then(async (dialogResult) => {
                if (!dialogResult.isConfirmed) return;

                try {
                    const response = await fetch(`${API_URL}/bulk-delete`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ ids: selectedIds }),
                    });

                    const result = await readApiResponse(response);
                    if (!response.ok) {
                        throw new Error(toFriendlyMessage(result.message, 'Gagal menghapus data terpilih'));
                    }

                    allMataKuliah = allMataKuliah.filter((item) => !selectedIds.includes(Number(item.id)));
                    cancelBulkSelect();
                    const currentSearch = document.getElementById('searchInput').value;
                    currentPage = 1;
                    handleSearch(currentSearch);
                    showSuccess('Mata kuliah terpilih berhasil dihapus');
                } catch (error) {
                    showError(toFriendlyMessage(error.message, 'Gagal menghapus data terpilih'));
                }
            });
        }

        async function toggleActive(id, checkbox) {
            const isActive = checkbox.checked;
            try {
                const response = await fetch(`${API_URL}/${id}/toggle-active`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ is_active: isActive }),
                });

                const result = await readApiResponse(response);
                if (!response.ok) {
                    checkbox.checked = !isActive; // revert
                    throw new Error(toFriendlyMessage(result.message, 'Gagal mengubah status aktif'));
                }

                // Update local data
                const mk = allMataKuliah.find(m => m.id == id);
                if (mk) mk.is_active = isActive;
                
                // Re-render
                renderTable();
                
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Status berhasil diperbarui'
                });
            } catch (error) {
                showError(toFriendlyMessage(error.message, 'Gagal mengubah status aktif'));
            }
        }

        async function syncSemester() {
            Swal.fire({
                title: 'Sinkronisasi Semester',
                text: 'Proses ini akan mengaktifkan Mata Kuliah sesuai dengan semester berjalan dan menonaktifkan yang lainnya. Lanjutkan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Sinkronkan',
                cancelButtonText: 'Batal',
            }).then(async (dialogResult) => {
                if (!dialogResult.isConfirmed) return;

                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    const response = await fetch(`${API_URL}/sync-semester`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        }
                    });

                    const result = await readApiResponse(response);
                    if (!response.ok) {
                        throw new Error(toFriendlyMessage(result.message, 'Gagal sinkronisasi semester'));
                    }

                    await loadAllMataKuliah();
                    showSuccess(result.message);
                } catch (error) {
                    showError(toFriendlyMessage(error.message, 'Gagal sinkronisasi semester'));
                }
            });
        }

        function showSuccess(message) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: message,
                confirmButtonColor: '#3b82f6',
            });
        }

        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                confirmButtonColor: '#ef4444',
            });
        }

        function showWarning(message) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: message,
                confirmButtonColor: '#3b82f6',
            });
        }

        const API_URL_CPL = '{{ url("/bank-soal/admin/api/cpl") }}';
        let allCpl = [];
        let filteredCpl = [];
        let searchTimeoutCpl;
        let currentPageCpl = 1;

        async function loadAllCpl() {
            document.getElementById('cplLoading').classList.add('show');
            document.getElementById('cplTableWrapper').style.display = 'none';
            document.getElementById('cplEmptyState').style.display = 'none';
            try {
                const response = await fetch(API_URL_CPL, {
                    headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
                });
                const result = await readApiResponse(response);
                if (result.success) {
                    allCpl = result.data;
                    filteredCpl = [...allCpl];
                    currentPageCpl = 1;
                    renderTableCpl();
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('loadAllCpl Error:', error);
                showError('Gagal memuat data CPL: ' + error.message);
            } finally {
                document.getElementById('cplLoading').classList.remove('show');
                document.getElementById('cplTableWrapper').style.display = 'block';
            }
        }

        function handleSearchCpl(value) {
            clearTimeout(searchTimeoutCpl);
            searchTimeoutCpl = setTimeout(() => {
                const query = value.toLowerCase().trim();
                if (query === '') {
                    filteredCpl = [...allCpl];
                } else {
                    filteredCpl = allCpl.filter((cpl) =>
                        String(cpl.kode).toLowerCase().includes(query) || String(cpl.deskripsi).toLowerCase().includes(query)
                    );
                }
                currentPageCpl = 1;
                handleSortCpl();
            }, 300);
        }

        function handleSortCpl() {
            const dir = document.getElementById('cplSortDirection').value;
            filteredCpl.sort((a, b) => {
                const aVal = String(a.kode).toLowerCase();
                const bVal = String(b.kode).toLowerCase();
                if (aVal === bVal) return 0;
                if (dir === 'asc') return aVal > bVal ? 1 : -1;
                return aVal < bVal ? 1 : -1;
            });
            if (currentPageCpl < 1) currentPageCpl = 1;
            renderTableCpl();
        }

        function renderTableCpl() {
            const tableBody = document.getElementById('cplTableBody');
            const emptyState = document.getElementById('cplEmptyState');
            const paginationSection = document.getElementById('cplPagination');

            const totalItems = filteredCpl.length;
            const totalPages = Math.max(1, Math.ceil(totalItems / PAGE_SIZE));
            if (currentPageCpl > totalPages) currentPageCpl = totalPages;

            const startIndex = (currentPageCpl - 1) * PAGE_SIZE;
            const pageItems = filteredCpl.slice(startIndex, startIndex + PAGE_SIZE);

            if (totalItems === 0) {
                tableBody.innerHTML = '';
                emptyState.style.display = 'block';
                paginationSection.style.display = 'none';
                return;
            }

            emptyState.style.display = 'none';
            paginationSection.style.display = totalPages > 1 ? 'flex' : 'none';
            tableBody.innerHTML = pageItems.map((cpl) => `
                <tr>
                    <td><span style="font-weight:700;color:#1e293b">${escapeHtml(cpl.kode)}</span></td>
                    <td style="color: #0f172a; line-height: 1.4; max-width: 520px;">${escapeHtml(cpl.deskripsi)}</td>
                    <td style="text-align:center">
                        <div class="dots-wrap">
                            <button class="btn-dots" onclick="toggleDots(this)" title="Aksi">&#8943;</button>
                            <div class="dots-menu">
                                <a href="${EDIT_CPL_URL}/${cpl.id}/edit" class="dots-menu-link" style="display:flex;align-items:center;gap:8px;padding:9px 14px;font-size:13px;text-decoration:none;color:var(--slate-700);border-bottom:1px solid var(--slate-100);">${ICON_EDIT} Edit</a>
                                <button class="menu-delete" onclick="deleteCpl(${cpl.id}, '${escapeHtml(cpl.kode)}')">${ICON_DEL} Hapus</button>
                            </div>
                        </div>
                    </td>
                </tr>
            `).join('');

            renderPaginationCpl(totalPages);
        }

        function renderPaginationCpl(totalPages) {
            const paginationList = document.getElementById('cplPaginationList');
            if (totalPages <= 1) { paginationList.innerHTML = ''; return; }
            const buttons = [];
            buttons.push(`<button type="button" class="pagination-btn" onclick="goToPageCpl(${Math.max(1, currentPageCpl - 1)})" ${currentPageCpl === 1 ? 'disabled' : ''}>&lsaquo;</button>`);
            for (let page = 1; page <= totalPages; page++) {
                buttons.push(`<button type="button" class="pagination-btn ${page === currentPageCpl ? 'active' : ''}" onclick="goToPageCpl(${page})">${page}</button>`);
            }
            buttons.push(`<button type="button" class="pagination-btn" onclick="goToPageCpl(${Math.min(totalPages, currentPageCpl + 1)})" ${currentPageCpl === totalPages ? 'disabled' : ''}>&rsaquo;</button>`);
            paginationList.innerHTML = buttons.join('');
        }

        function goToPageCpl(page) {
            currentPageCpl = page;
            renderTableCpl();
        }


        async function deleteCpl(id, kode) {
            Swal.fire({
                title: 'Hapus CPL?',
                text: `Apakah Anda yakin ingin menghapus "${kode}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`${API_URL_CPL}/${id}`, {
                            method: 'DELETE',
                            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        });
                        const res = await readApiResponse(response);
                        if (!response.ok) throw new Error(res.message || 'Gagal menghapus CPL');
                        allCpl = allCpl.filter((item) => Number(item.id) !== Number(id));
                        const currentSearch = document.getElementById('cplSearch').value;
                        if (currentPageCpl > 1 && filteredCpl.length % PAGE_SIZE === 1) {
                            currentPageCpl = Math.max(1, currentPageCpl - 1);
                        }
                        handleSearchCpl(currentSearch);
                        showSuccess('CPL berhasil dihapus');
                    } catch (error) {
                        showError(error.message);
                    }
                }
            });
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            };
            return String(text).replace(/[&<>"']/g, (m) => map[m]);
        }
    </script>
    @endpush
</x-banksoal::layouts.admin>
