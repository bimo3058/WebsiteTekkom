<!-- RPS History Table Component -->
<div class="card overflow-hidden">
    <div class="card-header">
        <h2 class="text-lg font-semibold text-slate-900">Riwayat Pengajuan RPS</h2>
    </div>

    <div class="controls-section mx-4 mt-4">
        <div class="search-box">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
                id="riwayatSearchInput"
                type="text"
                placeholder="Cari nama mata kuliah atau tahun ajaran..."
                autocomplete="off"
                onkeyup="handleRiwayatSearch()"
            >
        </div>

            <div class="filter-group">
                <label for="riwayatStatusSelect">Status:</label>
                <select id="riwayatStatusSelect" onchange="handleRiwayatFilterChange()">
                    <option value="">Semua</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="riwayatMkSelect">Mata Kuliah:</label>
                <select id="riwayatMkSelect" onchange="handleRiwayatFilterChange()">
                    <option value="">Semua</option>
                </select>
            </div>
    </div>

    <div class="table-wrapper">
        <table class="w-full">
            <thead class="table-header">
                <tr>
                    <th class="table-header-cell">Tahun/Semester</th>
                    <th class="table-header-cell">Mata Kuliah</th>
                    <th class="table-header-cell">Tanggal Upload</th>
                    <th class="table-header-cell">Diunggah Oleh</th>
                    <th class="table-header-cell">Status</th>
                    <th class="table-header-cell w-[80px]">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-body">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $riwayat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="table-row" data-mk="<?php echo e($item->mataKuliah?->nama ?? ''); ?> <?php echo e($item->mataKuliah?->kode ?? ''); ?>" data-status="<?php echo e($item->status->value); ?>" data-year="<?php echo e($item->tahun_ajaran); ?>">
                        <td class="table-cell-strong"><?php echo e($item->tahun_ajaran); ?> - <?php echo e($item->semester); ?></td>
                        <td class="table-cell">
                            <?php echo e($item->mataKuliah?->nama ?? 'N/A'); ?> <span class="text-xs text-slate-500">(<?php echo e($item->mataKuliah?->kode ?? 'N/A'); ?>)</span>
                            <div class="mt-1">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->creation_method === 'generator'): ?>
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700 border border-blue-200">Form</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600 border border-slate-200">Upload</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </td>
                        <td class="table-cell"><?php echo e($item->created_at->format('d M Y')); ?></td>
                        <td class="table-cell">
                            <?php
                                $fallbackUser = null;
                                if (!$item->uploader_id) {
                                    $fallbackUser = DB::table('bs_rps_dosen')
                                        ->where('rps_id', $item->id)
                                        ->join('users', 'users.id', '=', 'bs_rps_dosen.dosen_id')
                                        ->orderBy('bs_rps_dosen.id', 'asc')
                                        ->select('users.id', 'users.name')
                                        ->first();
                                }
                                $uploaderId = $item->uploader_id ?? $fallbackUser?->id;
                                $uploaderName = $item->uploader_name ?? $fallbackUser?->name ?? 'Tidak diketahui';
                            ?>
                            <div class="flex items-center gap-2">
                                <div class="h-6 w-6 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600">
                                    <?php echo e(substr($uploaderName, 0, 1)); ?>

                                </div>
                                <span class="text-sm font-medium text-slate-700"><?php echo e($uploaderName); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($uploaderId == Auth::id()): ?>
                                    <span class="rounded-full bg-primary/10 px-2 py-0.5 text-[10px] font-semibold text-primary">Anda</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </td>
                        <td class="table-cell">
                            <?php
                                $status = $item->status->label() ?? 'Unknown';
                                $statusClass = match($item->status->value) {
                                    'disetujui' => 'badge-success',
                                    'revisi' => 'badge-danger',
                                    'diajukan' => 'badge-warning',
                                    default => 'badge-secondary'
                                };
                            ?>
                            <span class="badge <?php echo e($statusClass); ?>"><?php echo e($status); ?></span>
                        </td>
                        <td class="table-cell">
                            <div class="dots-wrap" id="dots-<?php echo e($item->id); ?>">
                                <button type="button" class="btn-dots" onclick="toggleMenu(<?php echo e($item->id); ?>, event)">⋮</button>
                                <div class="dots-menu" id="menu-<?php echo e($item->id); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->dokumen): ?>
                                        <button type="button"
                                                class="preview-dokumen-btn"
                                                data-id="<?php echo e($item->id); ?>"
                                                data-title="<?php echo e(e($item->mataKuliah?->nama ?? 'Dokumen')); ?>">
                                            <i class="fas fa-eye w-4"></i> Preview
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    
                                    <?php
                                        $isUploader = $uploaderId == Auth::id();
                                        $canEdit = $isUploader && in_array($item->status->value, ['diajukan', 'revisi']);
                                        $canDelete = $isUploader && in_array($item->status->value, ['diajukan']);
                                    ?>
                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit): ?>
                                        <a href="<?php echo e(route('banksoal.rps.dosen.edit', $item->id)); ?>"
                                           class="edit-rps-btn">
                                            <i class="fas fa-edit w-4"></i> Edit
                                        </a>
                                    <?php else: ?>
                                        <button type="button" class="cursor-not-allowed text-slate-400" disabled 
                                                title="<?php echo e(!$isUploader ? 'Hanya pengunggah yang dapat mengubah RPS ini' : 'RPS tidak dapat diedit dengan status ' . $item->status->label()); ?>">
                                            <i class="fas fa-edit w-4"></i> Edit
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDelete): ?>
                                        <button type="button"
                                                class="delete-rps-btn menu-delete"
                                                data-id="<?php echo e($item->id); ?>"
                                                data-mk="<?php echo e(e($item->mataKuliah?->nama ?? 'RPS')); ?>"
                                                data-destroy-url="<?php echo e(route('banksoal.rps.dosen.destroy', $item->id)); ?>">
                                            <i class="fas fa-trash w-4"></i> Hapus
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="cursor-not-allowed text-slate-400" disabled
                                                title="<?php echo e(!$isUploader ? 'Hanya pengunggah yang dapat menghapus RPS ini' : 'RPS tidak dapat dihapus dengan status ' . $item->status->label()); ?>">
                                            <i class="fas fa-trash w-4"></i> Hapus
                                        </button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr data-empty-state="1">
                        <td colspan="6" class="px-6 py-12 text-center text-slate-600">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-inbox text-4xl text-slate-300 mb-3"></i>
                                <p class="font-medium">Belum ada riwayat pengunggahan</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(method_exists($riwayat, 'links')): ?>
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            <?php echo e($riwayat->onEachSide(1)->links('banksoal::components.ui.laravel-pagination')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\components\ui\riwayat-rps.blade.php ENDPATH**/ ?>