<x-banksoal::layouts.dosen-admin>
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
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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
        <button
            type="button"
            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/40 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-500 disabled:hover:bg-slate-300"
            onclick="openRpsUploadModal()"
            {{ !($isUploadOpen ?? false) ? 'disabled' : '' }}
            title="{{ !($isUploadOpen ?? false) ? 'Periode upload RPS saat ini tidak aktif' : 'Ajukan RPS baru' }}"
        >
            <i class="fas fa-plus"></i> Ajukan RPS
        </button>
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

    <!-- Modal Ajukan RPS Baru -->
    <div id="rpsUploadModal" data-has-validation-errors="{{ $errors->any() ? '1' : '0' }}" class="fixed inset-0 bg-black/70 z-50 items-start justify-center overflow-y-auto hidden">
        <div class="relative w-full max-w-5xl mx-4 my-8">
            <button type="button" onclick="closeRpsUploadModal()" class="absolute right-3 top-3 z-10 h-9 w-9 rounded-full bg-white text-slate-500 shadow hover:text-slate-700" aria-label="Tutup modal">
                ×
            </button>

            <x-banksoal::ui.rps-form
                :mataKuliahs="$mataKuliahs"
                :tahunAjarans="$tahunAjarans"
                :isUploadOpen="$isUploadOpen ?? false"
                :semester="$semester ?? 'Genap'"
                :academicYear="$academicYear ?? date('Y') . '/' . (date('Y') + 1)"
            />
        </div>
    </div>

    <!-- Modal Edit RPS (Revisi) -->
    <div id="rpsEditModal" class="fixed inset-0 bg-black/70 z-50 items-start justify-center overflow-y-auto hidden">
        <div class="relative w-full max-w-5xl mx-4 my-8">
            <button type="button" onclick="closeRpsEditModal()" class="absolute right-3 top-3 z-10 h-9 w-9 rounded-full bg-white text-slate-500 shadow hover:text-slate-700" aria-label="Tutup modal">
                ×
            </button>
            <div id="editModalContent"></div>
        </div>
    </div>

    <x-banksoal::ui.dokumen-rps-modal />

    <x-banksoal::ui.hapus-rps-modal />

    <x-banksoal::ui.rps-script />

    <script>
        (function() {
            const uploadModal = document.getElementById('rpsUploadModal');
            const editModal = document.getElementById('rpsEditModal');
            const hasValidationErrors = uploadModal?.dataset.hasValidationErrors === '1';

            function parseJsonArray(value) {
                if (!value) return [];
                try {
                    const parsed = JSON.parse(value);
                    return Array.isArray(parsed) ? parsed : [];
                } catch (error) {
                    console.warn('Gagal parse data multiselect edit modal:', error);
                    return [];
                }
            }

            function initializeEditModalForm(contentDiv) {
                const form = contentDiv?.querySelector('form[data-edit-mode="1"]');
                if (!form || typeof RpsMultiselectHandler !== 'function') return;

                const rpsMultiselect = new RpsMultiselectHandler({
                    rootElement: form,
                    routeSourceElement: form,
                    isEditForm: true,
                    rpsId: Number(form.dataset.rpsId || 0),
                    selectedDosenIds: parseJsonArray(form.dataset.selectedDosenIds),
                    selectedCplIds: parseJsonArray(form.dataset.selectedCplIds),
                    selectedCpmkIds: parseJsonArray(form.dataset.selectedCpmkIds),
                });

                rpsMultiselect.init();

                const mkSelect = form.querySelector('#mkSelect');
                if (mkSelect && mkSelect.value) {
                    mkSelect.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }

            function resetUploadModalForm() {
                const form = uploadModal?.querySelector('form');
                if (!form) return;

                form.reset();

                const syncSingleSelect = (selector) => {
                    const element = form.querySelector(selector);
                    if (!element?.tomselect) return;

                    const ts = element.tomselect;
                    ts.clear(true);
                    ts.setValue(element.value || '', true);
                    if (typeof ts.setTextboxValue === 'function') {
                        ts.setTextboxValue('');
                    }
                    if (typeof ts.refreshOptions === 'function') {
                        ts.refreshOptions(false);
                    }
                    if (typeof ts.close === 'function') {
                        ts.close();
                    }
                };

                const resetMultiSelect = (selector, placeholder) => {
                    const element = form.querySelector(selector);
                    if (!element?.tomselect) return;

                    const ts = element.tomselect;
                    ts.clear(true);
                    ts.clearOptions();
                    if (typeof ts.close === 'function') {
                        ts.close();
                    }
                    ts.disable();

                    if (ts.input) {
                        ts.input.placeholder = placeholder;
                    }
                };

                syncSingleSelect('#mkSelect');
                syncSingleSelect('#semester');
                syncSingleSelect('#tahun_ajaran');

                resetMultiSelect('#dosenSelect', 'Pilih Mata Kuliah Terlebih Dahulu');
                resetMultiSelect('#cplSelect', 'Pilih Mata Kuliah Terlebih Dahulu');
                resetMultiSelect('#cpmkSelect', 'Pilih CPL Terlebih Dahulu');

                const uploadText = form.querySelector('#uploadText');
                const uploadSub = form.querySelector('#uploadSub');
                const fileInput = form.querySelector('#fileInput');
                if (fileInput) {
                    fileInput.value = '';
                }
                if (uploadText) {
                    uploadText.textContent = 'Klik untuk unggah atau seret file ke sini';
                }
                if (uploadSub) {
                    uploadSub.textContent = 'PDF (Maks. 1MB)';
                }
            }

            window.openRpsUploadModal = function() {
                if (!uploadModal) return;
                uploadModal.classList.remove('hidden');
                uploadModal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
            };

            window.closeRpsUploadModal = function() {
                if (!uploadModal) return;
                uploadModal.classList.remove('flex');
                uploadModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                resetUploadModalForm();
            };

            window.openRpsEditModal = function(rpsId) {
                if (!editModal) return;
                const editLink = document.querySelector(`a[href*="/rps/dosen/${rpsId}/edit"]`);
                const editModalUrl = '{{ route("banksoal.rps.dosen.edit-modal", ":rpsId") }}'.replace(':rpsId', rpsId);
                
                // Load edit form via AJAX dari endpoint edit-modal
                fetch(editModalUrl)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.text();
                    })
                    .then(html => {
                        const contentDiv = document.getElementById('editModalContent');
                        if (contentDiv) {
                            contentDiv.innerHTML = html;
                            initializeEditModalForm(contentDiv);
                            editModal.classList.remove('hidden');
                            editModal.classList.add('flex');
                            document.body.classList.add('overflow-hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading edit form:', error);
                        alert('Gagal memuat form edit. Silakan coba lagi.');
                    });
            };

            window.closeRpsEditModal = function() {
                if (!editModal) return;
                editModal.classList.remove('flex');
                editModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                document.getElementById('editModalContent').innerHTML = '';
            };

            uploadModal?.addEventListener('click', function(event) {
                if (event.target === uploadModal) {
                    window.closeRpsUploadModal();
                }
            });

            editModal?.addEventListener('click', function(event) {
                if (event.target === editModal) {
                    window.closeRpsEditModal();
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    if (uploadModal && !uploadModal.classList.contains('hidden')) {
                        window.closeRpsUploadModal();
                    }
                    if (editModal && !editModal.classList.contains('hidden')) {
                        window.closeRpsEditModal();
                    }
                }
            });

            if (hasValidationErrors) {
                window.openRpsUploadModal();
            }

            // Event listener untuk tombol Edit RPS
            document.addEventListener('click', function(event) {
                const editBtn = event.target.closest('.edit-rps-btn');
                if (editBtn) {
                    const rpsId = editBtn.dataset.rpsid;
                    if (rpsId) {
                        window.openRpsEditModal(rpsId);
                    }
                }
            });

            // Klien-side pagination untuk Riwayat Pengajuan RPS
            initClientSidePagination();
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
