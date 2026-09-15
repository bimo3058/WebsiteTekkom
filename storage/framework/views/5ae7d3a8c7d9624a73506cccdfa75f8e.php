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
    /* ── Status Badges ── */
    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 12px; border-radius: 50px;
        font-size: 11px; font-weight: 700; letter-spacing: 0.3px;
    }
    .status-draft     { background: #f3f4f6; color: #666D80; }
    .status-diajukan  { background: #FFFBEB; color: #d97706; }
    .status-disetujui { background: #ECFDF5; color: #059669; }
    .status-ditolak   { background: #fef2f2; color: #dc2626; }



    /* ── Filter Bar ── */
    .filter-section { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px; align-items: center; }
    .filter-chip {
        padding: 7px 16px; border-radius: 8px; border: 1px solid #DFE1E7;
        background: #fff; color: #666D80; font-size: 13px; font-weight: 600;
        cursor: pointer; transition: all 0.15s; text-decoration: none !important;
        display: inline-block;
    }
    .filter-chip:hover { border-color: #0B266E; color: #0B266E; background: rgba(11,38,110,0.06); }
    .filter-chip.active { background: #0B266E; color: #fff !important; border-color: #0B266E; }

    .search-wrapper { position: relative; flex-grow: 1; }
    .search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #666D80; }
    .search-input {
        background: #fff; border: 1px solid #DFE1E7; border-radius: 8px;
        height: 38px; padding-left: 36px; font-size: 13px; width: 100%; color: #374151;
    }
    .search-input:focus { background: #fff; border-color: #0B266E; box-shadow: 0 0 0 3px rgba(11,38,110,0.1); outline: none; }

    /* ── Proker Cards ── */
    .proker-card {
        background: #fff; border-radius: 12px; overflow: hidden;
        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        transition: all 0.2s ease; text-decoration: none !important;
        display: flex; flex-direction: column; border: 1px solid #DFE1E7;
    }
    .proker-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px -4px rgba(11,38,110,0.12);
        border-color: rgba(11,38,110,0.25);
    }
    .proker-card-image {
        width: 100%; aspect-ratio: 16/9;
        background: linear-gradient(135deg, rgba(11,38,110,0.06) 0%, rgba(11,38,110,0.12) 100%);
        display: flex; align-items: center; justify-content: center; overflow: hidden;
    }
    .proker-card-image img { width: 100%; height: 100%; object-fit: cover; }
    .proker-card-body { padding: 16px 18px 18px; display: flex; flex-direction: column; flex: 1; }
    .badge-bidang {
        font-size: 11px; font-weight: 700; padding: 3px 10px;
        border-radius: 50px; background: #eef2ff; color: #0B266E;
    }
    .proker-card-title {
        font-weight: 700; font-size: 15px; color: #0D0D12;
        margin: 8px 0 6px; line-height: 1.4;
        display: -webkit-box; -webkit-line-clamp: 2;
        -webkit-box-orient: vertical; overflow: hidden;
    }
    .proker-card-meta {
        display: flex; flex-wrap: wrap; gap: 10px;
        font-size: 12px; color: #666D80; font-weight: 500;
        padding-top: 10px; border-top: 1px solid #f3f4f6; margin-top: auto;
    }
    .proker-card-meta span { display: inline-flex; align-items: center; gap: 4px; }
    .empty-state { text-align: center; padding: 50px 20px; color: #666D80; }
    .empty-state h5 { color: #666D80; font-weight: 600; margin-bottom: 4px; }
</style>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius:10px;border:none;background:#ECFDF5;color:#059669;font-weight:500;font-size:14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert"
         style="border-radius:10px;border:none;background:#fef2f2;color:#dc2626;font-weight:500;font-size:14px;">
        <?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h3 class="fw-bold mb-1" style="font-size:1.45rem;color:#0D0D12;letter-spacing:-.02em;">Rencana Program Kerja</h3>
        <p class="mb-0" style="font-size:.82rem;color:#666D80;font-weight:500;">Daftar rencana proker dari 8 bidang himpunan &amp; prodi</p>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManage): ?>
        <a href="<?php echo e(route('manajemenmahasiswa.proker.create')); ?>" class="btn d-flex align-items-center gap-2"
           style="background:#0B266E;color:#fff;font-weight:600;font-size:14px;padding:9px 18px;border-radius:8px;transition:all .15s;"
           onmouseover="this.style.background='#091958'" onmouseout="this.style.background='#0B266E'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Proker
        </a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>



<form method="GET" action="<?php echo e(route('manajemenmahasiswa.proker.index')); ?>" id="filterForm">
    <div class="d-flex gap-3 align-items-center mb-3">
        <div class="search-wrapper w-100">
            <span class="search-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
            <input type="text" name="search" class="form-control search-input"
                   placeholder="Cari rencana proker..." value="<?php echo e(request('search')); ?>">
        </div>
    </div>
    <div class="filter-section">
        <a href="<?php echo e(route('manajemenmahasiswa.proker.index', request()->except(['bidang','page']))); ?>"
           class="filter-chip <?php echo e(!request('bidang')||request('bidang')==='semua'?'active':''); ?>">Semua</a>
        <a href="<?php echo e(route('manajemenmahasiswa.proker.index', array_merge(request()->except('page'),['bidang'=>'prodi']))); ?>"
           class="filter-chip <?php echo e(request('bidang')==='prodi'?'active':''); ?>"
           style="<?php echo e(request('bidang')==='prodi'?'background:#0B266E;border-color:#0B266E;':''); ?>">Prodi</a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $bidangList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bidang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <a href="<?php echo e(route('manajemenmahasiswa.proker.index', array_merge(request()->except('page'),['bidang'=>$bidang->id]))); ?>"
               class="filter-chip <?php echo e(request('bidang')==$bidang->id?'active':''); ?>"><?php echo e($bidang->nama_bidang); ?></a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
</form>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prokerList->count() > 0): ?>
    <div class="row g-4">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $prokerList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proker): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="col-md-6 col-lg-4 col-xxl-3">
                <a href="<?php echo e(route('manajemenmahasiswa.proker.show', $proker->id)); ?>" class="proker-card">
                    <div class="proker-card-image">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($proker->banner): ?>
                            <img src="<?php echo e($proker->banner_url); ?>" alt="<?php echo e($proker->judul); ?>">
                        <?php else: ?>
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#5C78B8" stroke-width="1.5"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="proker-card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <span class="status-badge status-<?php echo e($proker->status); ?>"><?php echo e($proker->status_label); ?></span>
                            <?php
                                $hasProdi = $proker->kategoris && $proker->kategoris->contains(fn($k) => stripos($k->nama_kategori, 'Prodi') !== false);
                                $hasBidang = $proker->bidangs && $proker->bidangs->count() > 0;
                            ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasProdi || !$hasBidang): ?>
                                <span class="badge-bidang" style="background:#eef2ff;color:#0B266E;">Prodi</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasBidang): ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $proker->bidangs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <span class="badge-bidang"><?php echo e($b->nama_bidang); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="proker-card-title"><?php echo e($proker->judul); ?></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($proker->deskripsi): ?>
                            <div style="font-size:12px;color:#666D80;line-height:1.55;margin-bottom:8px;
                                        display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                <?php echo e($proker->deskripsi); ?>

                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="proker-card-meta">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($proker->tanggal_mulai): ?>
                                <span>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                    <?php echo e($proker->tanggal_mulai->translatedFormat('d M Y')); ?>

                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($proker->tahun): ?><span><?php echo e($proker->tahun); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($proker->ketuaPelaksana && $proker->ketuaPelaksana->user): ?>
                                <span>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    <?php echo e(Str::limit($proker->ketuaPelaksana->user->name, 18)); ?>

                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prokerList->hasPages()): ?>
        <div class="mt-4 d-flex justify-content-center"><?php echo e($prokerList->withQueryString()->links()); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php else: ?>
    <div class="empty-state">
        <div style="font-size:48px;margin-bottom:12px;opacity:0.5;">&#128203;</div>
        <h5>Belum ada rencana proker</h5>
        <p>Rencana program kerja yang dibuat akan muncul di sini</p>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManage): ?>
            <a href="<?php echo e(route('manajemenmahasiswa.proker.create')); ?>" class="btn mt-2"
               style="background:#0B266E;color:#fff;border-radius:8px;font-weight:600;font-size:14px;">+ Buat Proker Pertama</a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\ManajemenMahasiswa\resources\views\proker\index.blade.php ENDPATH**/ ?>