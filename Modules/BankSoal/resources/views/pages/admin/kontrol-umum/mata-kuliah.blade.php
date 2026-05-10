<x-banksoal::layouts.admin>
    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #3b82f6;
            --primary-hover: #2563eb;
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
            box-shadow: 0 2px 4px rgba(15, 23, 42, 0.1);
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

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: var(--primary-blue);
            color: #fff;
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
            transition: all 0.2s;
            background: transparent;
        }

        .btn-icon-edit {
            color: var(--primary-blue);
        }

        .btn-icon-edit:hover {
            background: var(--primary-blue);
            border-color: var(--primary-blue);
            color: #fff;
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
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 50;
            align-items: center;
            justify-content: center;
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
            <h1>Manajemen Mata Kuliah</h1>
            <p>Kelola data mata kuliah untuk program studi</p>
        </div>
        <button type="button" onclick="openAddModal()" class="btn-add">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Mata Kuliah
        </button>
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
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()">
                        </th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>SKS</th>
                        <th>Semester</th>
                        <th>Aksi</th>
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

    <div class="modal-overlay" id="mataKuliahModal" onclick="closeModalOnBackdrop(event)">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Tambah Mata Kuliah</h2>
                <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
            </div>

            <form id="mataKuliahForm" onsubmit="handleFormSubmit(event)">
                <div class="modal-body">
                    <input type="hidden" id="mkId">

                    <div class="form-group">
                        <label for="kode">Kode Mata Kuliah *</label>
                        <input type="text" id="kode" name="kode" placeholder="Contoh: PTSK6103" required maxlength="50">
                        <div class="form-error" id="error-kode"></div>
                    </div>

                    <div class="form-group">
                        <label for="nama">Nama Mata Kuliah *</label>
                        <input type="text" id="nama" name="nama" placeholder="Contoh: Dasar Komputer &amp; Pemrograman" required>
                        <div class="form-error" id="error-nama"></div>
                    </div>

                    <div class="form-group">
                        <label for="sks">Jumlah SKS *</label>
                        <div class="sks-counter">
                            <button type="button" onclick="decrementSKS()">-</button>
                            <input type="number" id="sks" name="sks" value="2" min="1" max="3" readonly>
                            <button type="button" onclick="incrementSKS()">+</button>
                        </div>
                        <div class="form-error" id="error-sks"></div>
                    </div>

                    <div class="form-group">
                        <label for="semester">Semester *</label>
                        <select id="semester" name="semester" required>
                            <option value="">-- Pilih Semester --</option>
                            <option value="1">Semester 1</option>
                            <option value="2">Semester 2</option>
                            <option value="3">Semester 3</option>
                            <option value="4">Semester 4</option>
                            <option value="5">Semester 5</option>
                            <option value="6">Semester 6</option>
                            <option value="7">Semester 7</option>
                            <option value="8">Semester 8</option>
                        </select>
                        <div class="form-error" id="error-semester"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
    <script>
        const API_URL = '{{ url("/bank-soal/admin/api/mata-kuliah") }}';
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

        document.addEventListener('DOMContentLoaded', loadAllMataKuliah);

        async function loadAllMataKuliah() {
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

        function renderTable() {
            const tableBody = document.getElementById('tableBody');
            const emptyState = document.getElementById('emptyState');
            const paginationSection = document.getElementById('paginationSection');

            const totalItems = filteredMataKuliah.length;
            const totalPages = Math.max(1, Math.ceil(totalItems / PAGE_SIZE));
            if (currentPage > totalPages) {
                currentPage = totalPages;
            }

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
            tableBody.innerHTML = pageItems
                .map(
                    (mk) => `
                <tr>
                    <td>
                        <input type="checkbox" class="mk-checkbox" value="${mk.id}" onchange="updateBulkDeleteUI()">
                    </td>
                    <td><span class="badge">${escapeHtml(mk.kode)}</span></td>
                    <td>${escapeHtml(mk.nama)}</td>
                    <td>${mk.sks} SKS</td>
                    <td>Semester ${mk.semester}</td>
                    <td>
                        <div class="action-cell">
                            <button type="button" class="btn-icon btn-icon-edit" title="Edit" onclick="editMataKuliah(${mk.id})">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button type="button" class="btn-icon btn-icon-delete" title="Hapus" onclick="deleteMataKuliah(${mk.id})">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `
                )
                .join('');

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

        function openAddModal() {
            document.getElementById('mkId').value = '';
            document.getElementById('modalTitle').textContent = 'Tambah Mata Kuliah';
            document.getElementById('mataKuliahForm').reset();
            document.getElementById('sks').value = '2';
            clearFormErrors();
            document.getElementById('mataKuliahModal').classList.add('show');
        }

        function closeModal() {
            document.getElementById('mataKuliahModal').classList.remove('show');
            clearFormErrors();
        }

        function closeModalOnBackdrop(e) {
            if (e.target.id === 'mataKuliahModal') {
                closeModal();
            }
        }

        function clearFormErrors() {
            document.querySelectorAll('.form-error').forEach((el) => {
                el.classList.remove('show');
                el.textContent = '';
            });
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

        async function handleFormSubmit(e) {
            e.preventDefault();
            clearFormErrors();

            const mkId = document.getElementById('mkId').value;
            const formData = {
                kode: document.getElementById('kode').value,
                nama: document.getElementById('nama').value,
                sks: document.getElementById('sks').value,
                semester: document.getElementById('semester').value,
            };

            try {
                const url = mkId ? `${API_URL}/${mkId}` : API_URL;
                const method = mkId ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(formData),
                });

                const result = await readApiResponse(response);

                if (!response.ok) {
                    if (result.errors) {
                        Object.entries(result.errors).forEach(([field, messages]) => {
                            const errorEl = document.getElementById(`error-${field}`);
                            if (errorEl) {
                                errorEl.textContent = mapFieldError(field, messages[0]);
                                errorEl.classList.add('show');
                            }
                        });
                    }
                    throw new Error(toFriendlyMessage(result.message, 'Gagal menyimpan data'));
                }

                showSuccess(mkId ? 'Mata kuliah berhasil diperbarui' : 'Mata kuliah berhasil ditambahkan');
                closeModal();

                if (mkId) {
                    const index = allMataKuliah.findIndex((mk) => Number(mk.id) === Number(mkId));
                    if (index !== -1) allMataKuliah[index] = result.data;
                } else {
                    allMataKuliah.push(result.data);
                }

                const currentSearch = document.getElementById('searchInput').value;
                currentPage = 1;
                currentPage = 1;
                handleSearch(currentSearch);
            } catch (error) {
                showError(toFriendlyMessage(error.message, 'Gagal menyimpan data'));
            }
        }

        async function editMataKuliah(id) {
            try {
                const response = await fetch(`${API_URL}/${id}`, {
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                const result = await readApiResponse(response);
                if (!result.success) {
                    throw new Error(toFriendlyMessage(result.message, 'Data tidak ditemukan'));
                }

                const mk = result.data;
                document.getElementById('mkId').value = mk.id;
                document.getElementById('kode').value = mk.kode;
                document.getElementById('nama').value = mk.nama;
                document.getElementById('sks').value = mk.sks;
                document.getElementById('semester').value = mk.semester;
                document.getElementById('modalTitle').textContent = 'Edit Mata Kuliah';

                clearFormErrors();
                document.getElementById('mataKuliahModal').classList.add('show');
            } catch (error) {
                showError(toFriendlyMessage(error.message, 'Gagal memuat data mata kuliah'));
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
