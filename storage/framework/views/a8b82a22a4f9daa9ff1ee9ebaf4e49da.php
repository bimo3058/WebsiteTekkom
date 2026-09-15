<?php if (isset($component)) { $__componentOriginaleb091a39d43027241cd802ab8434d071 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaleb091a39d43027241cd802ab8434d071 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.layouts.mahasiswa','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::layouts.mahasiswa'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


<style>
    .filter-chip { padding:7px 16px;border-radius:8px;border:1px solid #DFE1E7;background:#fff;color:#666D80;font-size:13px;font-weight:600;cursor:pointer;transition:all 0.15s;text-decoration:none !important;display:inline-block; }
    .filter-chip:hover { border-color:#0B266E;color:#0B266E;background:rgba(11,38,110,0.06); }
    .filter-chip.active { background:#0B266E;color:#fff !important;border-color:#0B266E; }
    .filter-select-custom { padding:0 14px;border-radius:8px;border:1px solid #DFE1E7;background:#fff;color:#374151;font-size:13px;font-weight:600;outline:none;height:38px;transition:all 0.15s; }
    .filter-select-custom:focus { border-color:#0B266E;box-shadow:0 0 0 3px rgba(11,38,110,0.1); }
    .search-wrapper { position:relative;flex-grow:1; }
    .search-icon { position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#666D80; }
    .search-input { background:#fff;border:1px solid #DFE1E7;border-radius:8px;height:38px;padding-left:36px;font-size:13px;width:100%;color:#374151; }
    .search-input:focus { background:#fff;border-color:#0B266E;box-shadow:0 0 0 3px rgba(11,38,110,0.1);outline:none; }
    .filter-section { display:flex;flex-wrap:wrap;gap:8px;margin-bottom:20px;align-items:center; }

    /* Cards */
    .pelaksanaan-card { background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 2px rgba(0,0,0,0.04);transition:all 0.2s;text-decoration:none !important;display:flex;flex-direction:column;border:1px solid #DFE1E7; }
    .pelaksanaan-card:hover { transform:translateY(-3px);box-shadow:0 12px 24px -4px rgba(11,38,110,0.12);border-color:rgba(11,38,110,0.25); }
    .card-banner { width:100%;aspect-ratio:16/9;background:linear-gradient(135deg,rgba(11,38,110,0.06),rgba(11,38,110,0.12));display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative; }
    .card-banner img { width:100%;height:100%;object-fit:cover; }
    .card-body { padding:16px 18px 18px;display:flex;flex-direction:column;flex:1; }
    .badge-bidang { font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:#eef2ff;color:#0B266E; }
    .card-title { font-weight:700;font-size:15px;color:#0D0D12;margin:8px 0 10px;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden; }
    .card-meta { display:flex;flex-wrap:wrap;gap:10px;font-size:12px;color:#666D80;font-weight:500;padding-top:10px;border-top:1px solid #f3f4f6;margin-top:auto; }
    .card-meta span { display:inline-flex;align-items:center;gap:4px; }

    /* Status */
    .status-badge { display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700; }
    .status-disetujui { background:#ECFDF5;color:#059669; }
    .status-berlangsung { background:#dbeafe;color:#1d4ed8; }
    .status-selesai { background:#f3f4f6;color:#374151; }
    .empty-state { text-align:center;padding:50px 20px;color:#666D80; }
    .empty-state h5 { color:#666D80;font-weight:600;margin-bottom:4px; }
</style>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;border:none;background:#ECFDF5;color:#059669;font-weight:500;font-size:14px;">
        <?php echo e(session('success')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h3 class="fw-bold mb-1" style="font-size:1.45rem;color:#0D0D12;letter-spacing:-.02em;">Pelaksanaan Kegiatan</h3>
        <p class="mb-0" style="font-size:.82rem;color:#666D80;font-weight:500;">Proker yang sudah disetujui — lengkapi data pelaksanaan di sini</p>
    </div>
</div>



<form method="GET" action="<?php echo e(route('manajemenmahasiswa.pelaksanaan.index')); ?>" id="filterForm">
    <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center mb-3">
        <div class="search-wrapper w-100 me-0 me-md-2">
            <span class="search-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
            <input type="text" name="search" class="form-control search-input"
                   placeholder="Cari kegiatan..." value="<?php echo e(request('search')); ?>">
        </div>
        <div class="d-flex gap-2">
            <select name="tahun" class="filter-select-custom" style="min-width:130px;" onchange="document.getElementById('filterForm').submit()">
                <option value="semua">Semua Tahun</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tahunList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <option value="<?php echo e($t); ?>" <?php echo e(request('tahun')==$t?'selected':''); ?>><?php echo e($t); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
        </div>
    </div>
    <div class="filter-section">
        <a href="<?php echo e(route('manajemenmahasiswa.pelaksanaan.index', request()->except(['bidang','page']))); ?>"
           class="filter-chip <?php echo e(!request('bidang')||request('bidang')==='semua'?'active':''); ?>">Semua</a>
        <a href="<?php echo e(route('manajemenmahasiswa.pelaksanaan.index', array_merge(request()->except('page'),['bidang'=>'prodi']))); ?>"
           class="filter-chip <?php echo e(request('bidang')==='prodi'?'active':''); ?>"
           style="<?php echo e(request('bidang')==='prodi'?'background:#0B266E;border-color:#0B266E;':''); ?>">Prodi</a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $bidangList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bidang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <a href="<?php echo e(route('manajemenmahasiswa.pelaksanaan.index', array_merge(request()->except('page'),['bidang'=>$bidang->id]))); ?>"
               class="filter-chip <?php echo e(request('bidang')==$bidang->id?'active':''); ?>"><?php echo e($bidang->nama_bidang); ?></a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
</form>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pelaksanaanList->count() > 0): ?>
    <div class="row g-4">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pelaksanaanList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="col-md-6 col-lg-4">
                <a href="<?php echo e(route('manajemenmahasiswa.pelaksanaan.show', $item->id)); ?>" class="pelaksanaan-card">
                    <div class="card-banner">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->banner): ?>
                            <img src="<?php echo e($item->banner_url); ?>" alt="<?php echo e($item->judul); ?>">
                        <?php else: ?>
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#5C78B8" stroke-width="1.5"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->bidangs && $item->bidangs->count() > 0): ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $item->bidangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <span class="badge-bidang"><?php echo e($b->nama_bidang); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <?php else: ?>
                                <span class="badge-bidang" style="background:#eef2ff;color:#0B266E;">Prodi</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="card-title"><?php echo e($item->judul); ?></div>

                        <div class="card-meta">
                            <span><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg> <?php echo e($item->tanggal_mulai ? $item->tanggal_mulai->translatedFormat('d M Y') : 'Belum ditentukan'); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->jam_mulai): ?>
                                <span><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> <?php echo e($item->jam_mulai_formatted); ?><?php echo e($item->jam_selesai_formatted ? ' - ' . $item->jam_selesai_formatted : ''); ?> WIB</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->lokasi): ?>
                                <span><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> <?php echo e($item->lokasi); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pelaksanaanList->hasPages()): ?>
        <div class="mt-4 d-flex justify-content-center"><?php echo e($pelaksanaanList->withQueryString()->links()); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php else: ?>
    <div class="empty-state">
        <div style="font-size:48px;margin-bottom:12px;opacity:0.5;">&#127939;</div>
        <h5>Belum ada proker yang siap dilaksanakan</h5>
        <p>Proker yang sudah disetujui admin akan muncul di sini</p>
        <a href="<?php echo e(route('manajemenmahasiswa.proker.index')); ?>" class="btn mt-2"
           style="background:#0B266E;color:#fff;border-radius:8px;font-weight:600;font-size:14px;">
            Lihat Rencana Proker
        </a>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaleb091a39d43027241cd802ab8434d071)): ?>
<?php $attributes = $__attributesOriginaleb091a39d43027241cd802ab8434d071; ?>
<?php unset($__attributesOriginaleb091a39d43027241cd802ab8434d071); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaleb091a39d43027241cd802ab8434d071)): ?>
<?php $component = $__componentOriginaleb091a39d43027241cd802ab8434d071; ?>
<?php unset($__componentOriginaleb091a39d43027241cd802ab8434d071); ?>
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\ManajemenMahasiswa\resources\views\pelaksanaan\index.blade.php ENDPATH**/ ?>