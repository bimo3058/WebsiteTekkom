<!-- RPS Approved Courses Grouped Table Component -->
<div class="card overflow-hidden">
    <div class="card-header">
        <h2 class="text-lg font-semibold text-slate-900">Riwayat RPS Mata Kuliah</h2>
    </div>

    <div class="controls-section mx-4 mt-4">
        <div class="search-box">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
                id="approvedSearchInput"
                type="text"
                placeholder="Cari mata kuliah atau tahun ajaran..."
                autocomplete="off"
                onkeyup="handleApprovedSearch()"
            >
        </div>

        <div class="filter-group">
            <label for="approvedMkSelect">Mata Kuliah:</label>
            <select id="approvedMkSelect" onchange="handleApprovedFilterChange()">
                <option value="">Semua</option>
            </select>
        </div>

        <div class="filter-group">
            <label for="approvedTahunSelect">Tahun Ajaran:</label>
            <select id="approvedTahunSelect" onchange="handleApprovedFilterChange()">
                <option value="">Semua</option>
            </select>
        </div>
    </div>

    <div class="table-wrapper">
        <table class="w-full">
            <thead class="table-header">
                <tr>
                    <th class="table-header-cell w-12"></th>
                    <th class="table-header-cell">Mata Kuliah</th>
                    <th class="table-header-cell">Jumlah Versi</th>
                    <th class="table-header-cell">Tanggal Terbaru</th>
                    <th class="table-header-cell w-16">Status</th>
                </tr>
            </thead>
            <tbody class="table-body">
                @forelse ($riwayatMkDisetujui as $mkId => $rpsItems)
                    @php
                        $firstItem = $rpsItems->first();
                        $latestItem = $rpsItems->first();
                        $years = $rpsItems->pluck('tahun_ajaran')->filter()->unique()->values()->implode('|');
                    @endphp
                    <tr class="table-row cursor-pointer toggle-group" data-group="{{ $mkId }}" data-expanded="0" data-mk="{{ $firstItem->mk_nama ?? '' }} {{ $firstItem->mk_kode ?? '' }}" data-year-latest="{{ $latestItem->tahun_ajaran ?? '' }}" data-years="{{ $years }}">
                        <td class="px-6 py-4 text-center">
                            <i class="fas fa-chevron-down text-slate-400 transition-transform duration-200" data-icon="{{ $mkId }}"></i>
                        </td>
                        <td class="table-cell">
                            <div class="font-medium text-slate-900">{{ $firstItem->mk_nama ?? '-' }}</div>
                            <div class="text-xs text-slate-500">{{ $firstItem->mk_kode ?? '-' }}</div>
                        </td>
                        <td class="table-cell text-center">
                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700">
                                {{ $rpsItems->count() }} {{ $rpsItems->count() == 1 ? 'versi' : 'versi' }}
                            </span>
                        </td>
                        <td class="table-cell">{{ $latestItem->tanggal_disetujui ? \Carbon\Carbon::parse($latestItem->tanggal_disetujui)->format('d M Y H:i') : '-' }}</td>
                        <td class="table-cell text-center">
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 border border-emerald-200">Disetujui</span>
                        </td>
                    </tr>

                    <!-- Expanded rows (hidden by default) -->
                    @foreach ($rpsItems as $item)
                        <tr class="table-row expanded-row" data-group="{{ $mkId }}" data-year="{{ $item->tahun_ajaran }}" style="display:none; background-color: #f9fafb;">
                            <td class="px-6 py-3"></td>
                            <td colspan="4">
                                <div class="py-2 px-4 bg-slate-50 rounded-lg">
                                    <div class="grid grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <p class="text-xs text-slate-500 font-semibold">Tanggal Disetujui</p>
                                            <p class="text-slate-900">{{ $item->tanggal_disetujui ? \Carbon\Carbon::parse($item->tanggal_disetujui)->format('d M Y H:i') : '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500 font-semibold">Tahun/Semester</p>
                                            <p class="text-slate-900">{{ $item->tahun_ajaran }} - {{ $item->semester }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500 font-semibold">Nama File</p>
                                            <p class="text-slate-900 truncate" title="{{ $item->dokumen ? basename((string) $item->dokumen) : '-' }}">
                                                {{ $item->dokumen ? basename((string) $item->dokumen) : '-' }}
                                            </p>
                                        </div>
                                        <div class="flex items-end gap-2">
                                            @if ($item->dokumen)
                                                <button type="button"
                                                        class="preview-dokumen-btn inline-flex items-center gap-1 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100"
                                                        data-id="{{ $item->id }}"
                                                        data-title="{{ e($firstItem->mk_nama ?? 'Dokumen') }}"
                                                        title="Preview dokumen">
                                                    <i class="fas fa-eye"></i> Preview
                                                </button>
                                                <a href="{{ route('banksoal.rps.dosen.download', $item->id) }}"
                                                   class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100"
                                                   title="Unduh RPS">
                                                    <i class="fas fa-download"></i> Unduh
                                                </a>
                                            @else
                                                <span class="text-slate-400 text-sm">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr data-empty-state="1">
                        <td colspan="5" class="px-6 py-12 text-center text-slate-600">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-inbox text-4xl text-slate-300 mb-3"></i>
                                <p class="font-medium">Belum ada riwayat RPS disetujui untuk mata kuliah yang Anda ampu saat ini</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="approvedEmptyState" class="empty-state" style="display:none;">Tidak ada RPS disetujui yang sesuai dengan filter pencarian.</div>
</div>

<script>
(function() {
    document.addEventListener('click', function(event) {
        const toggleRow = event.target.closest('.toggle-group');
        if (!toggleRow) return;

        const groupId = toggleRow.dataset.group;
        const icon = toggleRow.querySelector('[data-icon]');
        const expandedRows = document.querySelectorAll(`tr[data-group="${groupId}"].expanded-row`);
        const isExpanded = toggleRow.dataset.expanded === '1';
        const nextExpanded = isExpanded ? '0' : '1';

        toggleRow.dataset.expanded = nextExpanded;

        expandedRows.forEach(row => {
            row.style.display = nextExpanded === '1' ? '' : 'none';
        });

        if (icon) {
            icon.classList.toggle('rotate-180');
        }

        applyFilter();
    });

    const searchInput = document.getElementById('approvedSearchInput');
    const yearSelect = document.getElementById('approvedTahunSelect');
    const mkSelect = document.getElementById('approvedMkSelect');
    const cardRoot = searchInput?.closest('.card');
    const tableBody = cardRoot?.querySelector('tbody.table-body');
    const emptyState = cardRoot?.querySelector('#approvedEmptyState');

    if (!searchInput || !yearSelect || !tableBody) {
        return;
    }

    const groupRows = Array.from(tableBody.querySelectorAll('tr.toggle-group'));
    const expandedRows = Array.from(tableBody.querySelectorAll('tr.expanded-row'));

    function normalize(value) {
        return String(value ?? '').trim().toLowerCase();
    }

    function syncYearOptions() {
        const currentValue = yearSelect.value;
        const years = [...new Set(groupRows.flatMap((row) => String(row.dataset.years || '').split('|').filter(Boolean)))]
            .sort((a, b) => b.localeCompare(a, 'id'));

        yearSelect.innerHTML = '<option value="">Semua</option>';
        years.forEach((year) => {
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            yearSelect.appendChild(option);
        });

        yearSelect.value = years.includes(currentValue) ? currentValue : '';
    }

    function syncMkOptions() {
        if (!mkSelect) return;

        const currentValue = mkSelect.value;
        const mks = [...new Set(groupRows.map((row) => String(row.dataset.mk || '').trim()).filter(Boolean))]
            .sort((a, b) => a.localeCompare(b, 'id'));

        mkSelect.innerHTML = '<option value="">Semua</option>';
        mks.forEach((mk) => {
            const option = document.createElement('option');
            option.value = mk;
            option.textContent = mk;
            mkSelect.appendChild(option);
        });

        mkSelect.value = mks.includes(currentValue) ? currentValue : '';
    }

    function applyFilter() {
        const query = normalize(searchInput.value);
        const selectedYear = normalize(yearSelect.value);
        const selectedMk = normalize(mkSelect?.value || '');
        let visibleCount = 0;

        groupRows.forEach((groupRow) => {
            const groupId = groupRow.dataset.group;
            const mkText = normalize(groupRow.dataset.mk);
            const years = String(groupRow.dataset.years || '').split('|').map(normalize).filter(Boolean);
            const childRows = expandedRows.filter((row) => row.dataset.group === groupId);

            const groupMatchesSearch = !query || mkText.includes(query) || years.some((year) => year.includes(query));
            const groupMatchesYear = !selectedYear || years.includes(selectedYear);
            const groupMatchesMk = !selectedMk || mkText === selectedMk;
            const groupVisible = groupMatchesSearch && groupMatchesYear && groupMatchesMk;
            const isExpanded = groupRow.dataset.expanded === '1';

            groupRow.style.display = groupVisible ? '' : 'none';

            childRows.forEach((childRow) => {
                const rowYear = normalize(childRow.dataset.year);
                const childMatchesYear = !selectedYear || rowYear === selectedYear;
                const childMatchesSearch = !query || mkText.includes(query) || rowYear.includes(query);
                const childVisible = groupVisible && isExpanded && childMatchesYear && childMatchesSearch;

                childRow.style.display = childVisible ? '' : 'none';
            });

            if (groupVisible) {
                visibleCount += 1;
            }
        });

        if (emptyState) {
            emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    window.handleApprovedSearch = function() {
        applyFilter();
    };

    window.handleApprovedFilterChange = function() {
        applyFilter();
    };

    syncMkOptions();
    syncYearOptions();
    applyFilter();
})();
</script>

<style>
    .controls-section {
        padding: 12px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 16px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        background: #f8fafc;
    }

    .search-box {
        flex: 0 1 260px;
        max-width: 260px;
        min-width: 200px;
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
        margin-left: 0;
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
        min-width: 120px;
    }

    #approvedEmptyState {
        display: none;
        text-align: center;
        padding: 40px 16px;
        color: #64748b;
        font-size: 14px;
    }

    .toggle-group:hover {
        background-color: #f1f5f9;
    }
    
    .expanded-row.hidden {
        display: none;
    }
</style>
