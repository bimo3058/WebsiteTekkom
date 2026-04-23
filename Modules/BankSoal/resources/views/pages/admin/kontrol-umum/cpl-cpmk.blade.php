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

        .desc-cell {
            color: #0f172a;
            line-height: 1.4;
            max-width: 520px;
        }

        .muted-date {
            font-size: 12px;
            color: var(--slate-600);
            white-space: nowrap;
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

        .btn-icon-edit { color: var(--primary-blue); }
        .btn-icon-edit:hover {
            background: var(--primary-blue);
            border-color: var(--primary-blue);
            color: #fff;
        }
        .btn-icon-delete { color: var(--danger-red); }
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

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--slate-300);
            border-radius: 8px;
            font-size: 13px;
            background: #fff;
        }

        .form-group textarea {
            min-height: 96px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group textarea:focus {
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
        .btn-primary:hover { background: var(--primary-hover); }
        .btn-secondary { background: var(--slate-200); color: var(--slate-700); }
        .btn-secondary:hover { background: var(--slate-300); }

        @media (max-width: 900px) {
            .controls-section { flex-direction: column; align-items: stretch; }
            .search-box { min-width: 100%; }
            .filter-group { width: 100%; margin-left: 0; }
            .filter-group select { flex: 1; }
        }
    </style>
    @endpush

    <div class="page-header">
        <div class="header-content">
            <h1>Manajemen CPL & CPMK</h1>
            <p>Kelola data CPL dan CPMK. Masing-masing tabel menampilkan 5 item per halaman.</p>
        </div>
    </div>

    <section class="feature-card" id="section-cpl">
        <div class="feature-head">
            <div>
                <h2 class="feature-title">CPL</h2>
                <p class="feature-subtitle">Format kode: CPL-1, CPL-2</p>
            </div>
            <button type="button" class="btn-add" onclick="openAddModal('cpl')">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah CPL
            </button>
        </div>

        <div class="controls-section">
            <div class="search-box">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" id="cplSearch" placeholder="Cari kode atau deskripsi CPL..." onkeyup="handleSearch('cpl', this.value)">
            </div>
            <div class="filter-group">
                <label for="cplSortDirection">Order By:</label>
                <select id="cplSortDirection" onchange="handleSort('cpl')">
                    <option value="asc">Ascending</option>
                    <option value="desc">Descending</option>
                </select>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 140px;">Kode</th>
                        <th>Deskripsi</th>
                        <th style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="cplTableBody"></tbody>
            </table>
        </div>
        <div id="cplEmptyState" class="empty-state" style="display:none;">Tidak ada data CPL</div>
        <div id="cplPagination" class="pagination-section" style="display:none;">
            <div class="pagination-list" id="cplPaginationList"></div>
        </div>
    </section>

    <section class="feature-card" id="section-cpmk">
        <div class="feature-head">
            <div>
                <h2 class="feature-title">CPMK</h2>
                <p class="feature-subtitle">Format kode: CPMK-1.1, CPMK 10.3</p>
            </div>
            <button type="button" class="btn-add" onclick="openAddModal('cpmk')">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah CPMK
            </button>
        </div>

        <div class="controls-section">
            <div class="search-box">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" id="cpmkSearch" placeholder="Cari kode atau deskripsi CPMK..." onkeyup="handleSearch('cpmk', this.value)">
            </div>
            <div class="filter-group">
                <label for="cpmkSortDirection">Order By:</label>
                <select id="cpmkSortDirection" onchange="handleSort('cpmk')">
                    <option value="asc">Ascending</option>
                    <option value="desc">Descending</option>
                </select>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 140px;">Kode</th>
                        <th>Deskripsi</th>
                        <th style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="cpmkTableBody"></tbody>
            </table>
        </div>
        <div id="cpmkEmptyState" class="empty-state" style="display:none;">Tidak ada data CPMK</div>
        <div id="cpmkPagination" class="pagination-section" style="display:none;">
            <div class="pagination-list" id="cpmkPaginationList"></div>
        </div>
    </section>

    <div class="modal-overlay" id="entityModal" onclick="closeModalOnBackdrop(event)">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Tambah Data</h2>
                <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="entityForm" onsubmit="handleFormSubmit(event)">
                <div class="modal-body">
                    <input type="hidden" id="entityType">
                    <input type="hidden" id="entityId">

                    <div class="form-group">
                        <label for="kode">Kode *</label>
                        <input type="text" id="kode" name="kode" maxlength="50" placeholder="Contoh: CPL-001 / 11.1" required>
                        <div class="form-error" id="error-kode"></div>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi *</label>
                        <textarea id="deskripsi" name="deskripsi" placeholder="Masukkan deskripsi" required></textarea>
                        <div class="form-error" id="error-deskripsi"></div>
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
        const PAGE_SIZE = 5;
        const csrfToken = '{{ csrf_token() }}';

        const ENTITY = {
            cpl: {
                label: 'CPL',
                api: '{{ url("/bank-soal/admin/api/cpl") }}',
                nextCode: '{{ url("/bank-soal/admin/api/cpl/next-code") }}',
            },
            cpmk: {
                label: 'CPMK',
                api: '{{ url("/bank-soal/admin/api/cpmk") }}',
                nextCode: '{{ url("/bank-soal/admin/api/cpmk/next-code") }}',
            },
        };

        const state = {
            cpl: { all: [], filtered: [], currentPage: 1, timer: null },
            cpmk: { all: [], filtered: [], currentPage: 1, timer: null },
        };

        document.addEventListener('DOMContentLoaded', async () => {
            await Promise.all([loadData('cpl'), loadData('cpmk')]);
        });

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
            if (!message || typeof message !== 'string') return fallback;
            const lowered = message.toLowerCase();
            if (lowered.includes('doctype') || lowered.includes('<html')) {
                return 'Terjadi kesalahan pada server. Silakan coba lagi.';
            }
            return message;
        }

        function clearFormErrors() {
            document.querySelectorAll('.form-error').forEach((el) => {
                el.classList.remove('show');
                el.textContent = '';
            });
        }

        function showValidationErrors(errors = {}) {
            Object.entries(errors).forEach(([field, messages]) => {
                const el = document.getElementById(`error-${field}`);
                if (!el) return;
                el.textContent = Array.isArray(messages) ? messages[0] : String(messages);
                el.classList.add('show');
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

        function formatDate(dateValue) {
            if (!dateValue) return '-';
            const d = new Date(dateValue);
            if (Number.isNaN(d.getTime())) return '-';
            return d.toLocaleString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        }

        async function loadData(type) {
            try {
                const response = await fetch(ENTITY[type].api, {
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                const result = await readApiResponse(response);
                if (!response.ok || !result.success) {
                    throw new Error(toFriendlyMessage(result.message, `Gagal memuat data ${ENTITY[type].label}`));
                }

                state[type].all = result.data || [];
                applyFilterAndSort(type, false);
            } catch (error) {
                showError(toFriendlyMessage(error.message, `Gagal memuat data ${ENTITY[type].label}`));
            }
        }

        function handleSearch(type, value) {
            clearTimeout(state[type].timer);
            state[type].timer = setTimeout(() => {
                state[type].currentPage = 1;
                applyFilterAndSort(type, false);
            }, 300);
        }

        function handleSort(type) {
            state[type].currentPage = 1;
            applyFilterAndSort(type, false);
        }

        function applyFilterAndSort(type, keepPage = false) {
            const searchValue = document.getElementById(`${type}Search`).value.trim().toLowerCase();
            const sortDirection = document.getElementById(`${type}SortDirection`).value;

            let data = [...state[type].all];

            if (searchValue) {
                data = data.filter((item) => {
                    const kode = String(item.kode || '').toLowerCase();
                    const deskripsi = String(item.deskripsi || '').toLowerCase();

                    return kode.includes(searchValue) || deskripsi.includes(searchValue);
                });
            }

            data.sort((a, b) => {
                const left = String(a.kode || '').toLowerCase();
                const right = String(b.kode || '').toLowerCase();

                if (left === right) return 0;
                if (sortDirection === 'asc') return left > right ? 1 : -1;
                return left < right ? 1 : -1;
            });

            state[type].filtered = data;
            if (!keepPage) {
                state[type].currentPage = 1;
            }

            renderTable(type);
        }

        function renderTable(type) {
            const tableBody = document.getElementById(`${type}TableBody`);
            const emptyState = document.getElementById(`${type}EmptyState`);
            const pagination = document.getElementById(`${type}Pagination`);
            const paginationList = document.getElementById(`${type}PaginationList`);

            const totalItems = state[type].filtered.length;
            const totalPages = Math.max(1, Math.ceil(totalItems / PAGE_SIZE));

            if (state[type].currentPage > totalPages) {
                state[type].currentPage = totalPages;
            }

            const start = (state[type].currentPage - 1) * PAGE_SIZE;
            const pageItems = state[type].filtered.slice(start, start + PAGE_SIZE);

            if (totalItems === 0) {
                tableBody.innerHTML = '';
                emptyState.style.display = 'block';
                pagination.style.display = 'none';
                return;
            }

            emptyState.style.display = 'none';
            pagination.style.display = totalPages > 1 ? 'flex' : 'none';

            tableBody.innerHTML = pageItems.map((item) => `
                <tr>
                    <td><span class="kode-badge">${escapeHtml(item.kode)}</span></td>
                    <td class="desc-cell">${escapeHtml(item.deskripsi)}</td>
                    <td>
                        <div class="action-cell">
                            <button type="button" class="btn-icon btn-icon-edit" title="Edit" onclick="openEditModal('${type}', ${item.id})">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button type="button" class="btn-icon btn-icon-delete" title="Hapus" onclick="deleteEntity('${type}', ${item.id})">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');

            const buttons = [];
            buttons.push(`<button type="button" class="pagination-btn" onclick="goToPage('${type}', ${Math.max(1, state[type].currentPage - 1)})" ${state[type].currentPage === 1 ? 'disabled' : ''}>&lsaquo;</button>`);
            for (let page = 1; page <= totalPages; page++) {
                buttons.push(`<button type="button" class="pagination-btn ${page === state[type].currentPage ? 'active' : ''}" onclick="goToPage('${type}', ${page})">${page}</button>`);
            }
            buttons.push(`<button type="button" class="pagination-btn" onclick="goToPage('${type}', ${Math.min(totalPages, state[type].currentPage + 1)})" ${state[type].currentPage === totalPages ? 'disabled' : ''}>&rsaquo;</button>`);
            paginationList.innerHTML = buttons.join('');
        }

        function goToPage(type, page) {
            state[type].currentPage = page;
            renderTable(type);
        }

        async function openAddModal(type) {
            document.getElementById('entityType').value = type;
            document.getElementById('entityId').value = '';
            document.getElementById('modalTitle').textContent = `Tambah ${ENTITY[type].label}`;
            document.getElementById('deskripsi').value = '';
            document.getElementById('kode').placeholder = type === 'cpmk' ? 'Contoh: 11.1' : 'Contoh: CPL-001';
            clearFormErrors();

            if (type === 'cpl') {
                try {
                    const response = await fetch(ENTITY[type].nextCode, {
                        headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    });
                    const result = await readApiResponse(response);
                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'Gagal mengambil kode otomatis');
                    }
                    document.getElementById('kode').value = result.data.kode;
                } catch (error) {
                    document.getElementById('kode').value = '';
                    showWarning(toFriendlyMessage(error.message, 'Kode otomatis tidak tersedia, isi manual sesuai format.'));
                }
            }

            document.getElementById('entityModal').classList.add('show');
        }

        async function openEditModal(type, id) {
            clearFormErrors();
            try {
                const response = await fetch(`${ENTITY[type].api}/${id}`, {
                    headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
                });
                const result = await readApiResponse(response);

                if (!response.ok || !result.success) {
                    throw new Error(result.message || `Data ${ENTITY[type].label} tidak ditemukan`);
                }

                const data = result.data;
                document.getElementById('entityType').value = type;
                document.getElementById('entityId').value = String(data.id);
                document.getElementById('modalTitle').textContent = `Edit ${ENTITY[type].label}`;
                document.getElementById('kode').value = data.kode || '';
                document.getElementById('deskripsi').value = data.deskripsi || '';

                document.getElementById('entityModal').classList.add('show');
            } catch (error) {
                showError(toFriendlyMessage(error.message, `Gagal memuat data ${ENTITY[type].label}`));
            }
        }

        function closeModal() {
            document.getElementById('entityModal').classList.remove('show');
            clearFormErrors();
        }

        function closeModalOnBackdrop(event) {
            if (event.target.id === 'entityModal') {
                closeModal();
            }
        }

        async function handleFormSubmit(event) {
            event.preventDefault();
            clearFormErrors();

            const type = document.getElementById('entityType').value;
            const id = document.getElementById('entityId').value;
            const payload = {
                kode: document.getElementById('kode').value.trim(),
                deskripsi: document.getElementById('deskripsi').value.trim(),
            };

            try {
                const response = await fetch(id ? `${ENTITY[type].api}/${id}` : ENTITY[type].api, {
                    method: id ? 'PUT' : 'POST',
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
                        showValidationErrors(result.errors);
                    }
                    throw new Error(result.message || 'Gagal menyimpan data');
                }

                closeModal();
                await loadData(type);
                showSuccess(id ? `${ENTITY[type].label} berhasil diperbarui` : `${ENTITY[type].label} berhasil ditambahkan`);
            } catch (error) {
                showError(toFriendlyMessage(error.message, 'Gagal menyimpan data'));
            }
        }

        async function deleteEntity(type, id) {
            const found = state[type].all.find((item) => Number(item.id) === Number(id));
            if (!found) {
                showError(`Data ${ENTITY[type].label} tidak ditemukan`);
                return;
            }

            const confirm = await Swal.fire({
                title: `Hapus ${ENTITY[type].label}?`,
                text: `Data dengan kode "${found.kode}" akan dihapus permanen`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
            });

            if (!confirm.isConfirmed) return;

            try {
                const response = await fetch(`${ENTITY[type].api}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                const result = await readApiResponse(response);
                if (!response.ok) {
                    throw new Error(result.message || `Gagal menghapus ${ENTITY[type].label}`);
                }

                await loadData(type);
                showSuccess(`${ENTITY[type].label} berhasil dihapus`);
            } catch (error) {
                showError(toFriendlyMessage(error.message, `Gagal menghapus ${ENTITY[type].label}`));
            }
        }

        function showSuccess(message) {
            Swal.fire({ icon: 'success', title: 'Berhasil', text: message, confirmButtonColor: '#3b82f6' });
        }

        function showError(message) {
            Swal.fire({ icon: 'error', title: 'Error', text: message, confirmButtonColor: '#ef4444' });
        }

        function showWarning(message) {
            Swal.fire({ icon: 'warning', title: 'Perhatian', text: message, confirmButtonColor: '#3b82f6' });
        }
    </script>
    @endpush
</x-banksoal::layouts.admin>
