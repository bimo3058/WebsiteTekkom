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
                    <th class="table-header-cell w-[80px]">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-body">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $riwayatMkDisetujui; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mkId => $rpsItems): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $firstItem = $rpsItems->first();
                        $latestItem = $rpsItems->first();
                        $years = $rpsItems->pluck('tahun_ajaran')->filter()->unique()->values()->implode('|');
                    ?>
                    <tr class="table-row cursor-pointer toggle-group" data-group="<?php echo e($mkId); ?>" data-expanded="0" data-mk="<?php echo e($firstItem->mk_nama ?? ''); ?> <?php echo e($firstItem->mk_kode ?? ''); ?>" data-year-latest="<?php echo e($latestItem->tahun_ajaran ?? ''); ?>" data-years="<?php echo e($years); ?>">
                        <td class="px-6 py-4 text-center">
                            <i class="fas fa-chevron-down text-slate-400 transition-transform duration-200" data-icon="<?php echo e($mkId); ?>"></i>
                        </td>
                        <td class="table-cell">
                            <div class="font-medium text-slate-900"><?php echo e($firstItem->mk_nama ?? '-'); ?></div>
                            <div class="text-xs text-slate-500"><?php echo e($firstItem->mk_kode ?? '-'); ?></div>
                        </td>
                        <td class="table-cell text-center">
                            <span class="inline-flex items-center rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-semibold text-primary">
                                <?php echo e($rpsItems->count()); ?> <?php echo e($rpsItems->count() == 1 ? 'versi' : 'versi'); ?>

                            </span>
                        </td>
                        <td class="table-cell"><?php echo e($latestItem->tanggal_disetujui ? \Carbon\Carbon::parse($latestItem->tanggal_disetujui)->format('d M Y H:i') : '-'); ?></td>
                        <td class="table-cell text-center">
                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 border border-emerald-200">Disetujui</span>
                        </td>
                        <td class="table-cell"></td>
                    </tr>

                    <!-- Expanded rows (hidden by default) -->
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $rpsItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="table-row expanded-row" data-group="<?php echo e($mkId); ?>" data-year="<?php echo e($item->tahun_ajaran); ?>" style="display:none; background-color: #f9fafb;">
                            <td class="px-6 py-3"></td>
                            <td colspan="4">
                                <div class="py-2 px-4 bg-slate-50 rounded-lg">
                                    <div class="grid grid-cols-5 gap-4 text-sm">
                                        <div>
                                            <p class="text-xs text-slate-500 font-semibold">Tanggal Disetujui</p>
                                            <p class="text-slate-900"><?php echo e($item->tanggal_disetujui ? \Carbon\Carbon::parse($item->tanggal_disetujui)->format('d M Y H:i') : '-'); ?></p>
                                        </div>
                                        <div>
                                            <?php
                                                $uploaderName = $item->uploader_name ?? DB::table('bs_rps_dosen')->where('rps_id', $item->id)->join('users', 'users.id', '=', 'bs_rps_dosen.dosen_id')->orderBy('bs_rps_dosen.id', 'asc')->value('users.name') ?? 'Tidak diketahui';
                                            ?>
                                            <p class="text-xs text-slate-500 font-semibold">Diunggah Oleh</p>
                                            <p class="text-slate-900"><?php echo e($uploaderName); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500 font-semibold">Tahun/Semester</p>
                                            <p class="text-slate-900"><?php echo e($item->tahun_ajaran); ?> - <?php echo e($item->semester); ?></p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500 font-semibold">Nama File</p>
                                            <p class="text-slate-900 truncate" title="<?php echo e($item->dokumen ? basename((string) $item->dokumen) : '-'); ?>">
                                                <?php echo e($item->dokumen ? basename((string) $item->dokumen) : '-'); ?>

                                            </p>
                                        </div>
                                        <div class="flex items-end justify-end">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->dokumen): ?>
                                                <div class="dots-wrap" id="dots-approved-<?php echo e($item->id); ?>">
                                                    <button type="button" class="btn-dots" onclick="toggleMenu('approved-<?php echo e($item->id); ?>', event)">⋮</button>
                                                    <div class="dots-menu" id="menu-approved-<?php echo e($item->id); ?>">
                                                        <button type="button"
                                                                class="preview-dokumen-btn"
                                                                data-id="<?php echo e($item->id); ?>"
                                                                data-title="<?php echo e(e($firstItem->mk_nama ?? 'Dokumen')); ?>">
                                                            <i class="fas fa-eye w-4"></i> Preview
                                                        </button>
                                                        <a href="<?php echo e(route('banksoal.rps.dosen.download', $item->id)); ?>"
                                                           class="download-rps-btn">
                                                            <i class="fas fa-download w-4"></i> Unduh
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-slate-400 text-sm">-</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr data-empty-state="1">
                        <td colspan="6" class="px-6 py-12 text-center text-slate-600">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-inbox text-4xl text-slate-300 mb-3"></i>
                                <p class="font-medium">Belum ada riwayat RPS disetujui untuk mata kuliah yang Anda ampu saat ini</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\components\ui\riwayat-rps-disetujui.blade.php ENDPATH**/ ?>