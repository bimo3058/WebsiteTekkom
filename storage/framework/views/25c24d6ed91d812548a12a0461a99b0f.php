<?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => $layout] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


<style>
    /* ── Stat Cards (Admin KPI) ── */
    .admin-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 22px; }
    .admin-stat-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
        padding: 16px 18px; display: flex; align-items: center; gap: 14px;
        cursor: pointer; transition: all .18s; text-decoration: none !important;
        position: relative; overflow: hidden;
    }
    .admin-stat-card:hover { border-color: rgba(11,38,110,0.18); box-shadow: 0 4px 14px rgba(0,0,0,.06); transform: translateY(-1px); }
    .admin-stat-card.active { border-color: var(--c-primary); box-shadow: 0 0 0 2px rgba(11,38,110,.12); }
    .admin-stat-card .stat-icon {
        width: 42px; height: 42px; border-radius: 11px; display: flex;
        align-items: center; justify-content: center; flex-shrink: 0;
    }
    .admin-stat-card .stat-num { font-size: 1.5rem; font-weight: 800; line-height: 1; margin-bottom: 1px; }
    .admin-stat-card .stat-lbl { font-size: .78rem; color: var(--c-fg-muted); font-weight: 500; }
    .admin-stat-card.pending .stat-icon { background: var(--c-warning-subtle); color: #d97706; }
    .admin-stat-card.pending .stat-num { color: #d97706; }
    .admin-stat-card.approved .stat-icon { background: var(--c-success-subtle); color: var(--c-success); }
    .admin-stat-card.approved .stat-num { color: var(--c-success); }
    .admin-stat-card.rejected .stat-icon { background: var(--c-error-subtle); color: var(--c-error); }
    .admin-stat-card.rejected .stat-num { color: var(--c-error); }

    /* ── Status & Buttons ── */
    .status-verif {
        display: inline-flex; align-items: center; padding: 3px 9px;
        border-radius: 50px; font-size: .73rem; font-weight: 600;
    }
    .status-verif.pending { background: #FFFBEB; color: #d97706; }
    .status-verif.approved { background: #ECFDF5; color: #059669; }
    .status-verif.rejected { background: var(--c-error-subtle); color: var(--c-error); }

    .btn-approve {
        width: 30px; height: 30px; border-radius: 8px; border: 1px solid #bbf7d0;
        background: #ECFDF5; color: #059669; cursor: pointer; transition: all .15s;
        display: inline-flex; align-items: center; justify-content: center; padding: 0;
    }
    .btn-approve:hover { background: #bbf7d0; border-color: #86efac; }
    .btn-reject {
        width: 30px; height: 30px; border-radius: 8px; border: 1px solid #fecaca;
        background: var(--c-error-subtle); color: var(--c-error); cursor: pointer; transition: all .15s;
        display: inline-flex; align-items: center; justify-content: center; padding: 0;
    }
    .btn-reject:hover { background: #fee2e2; border-color: #fca5a5; }

    /* ── Empty State ── */
    .empty-state { text-align: center; padding: 60px 24px; color: var(--c-fg-muted); }
    .empty-state .empty-icon { display: flex; justify-content: center; margin-bottom: 12px; color: #E5E7EB; }

    .modal-content { border-radius: 18px; border: none; box-shadow: 0 24px 60px rgba(0,0,0,.18); }
    .modal-header { border-bottom: 1px solid #f3f4f6; padding: 18px 22px; }
    .modal-header .modal-title { font-size: 1rem; font-weight: 700; color: var(--c-fg); }
    .modal-body { padding: 22px; }
    .modal-footer { border-top: 1px solid #f3f4f6; padding: 14px 22px; }

    .tingkat-badge {
        display: inline-flex; align-items: center; padding: 2px 8px;
        border-radius: 50px; font-size: .73rem; font-weight: 600; text-transform: uppercase;
    }
    .tingkat-badge.internasional { background: #FFFBEB; color: #92400e; }
    .tingkat-badge.nasional { background: #dbeafe; color: #1e40af; }
    .tingkat-badge.regional { background: #f3e8ff; color: #7c3aed; }
    .tingkat-badge.universitas { background: #ECFDF5; color: #059669; }
    .tingkat-badge.prodi { background: #eef2ff; color: var(--c-primary); }

    /* ── Reward Badge & Aksi ── */
    .claim-badge { font-size: .73rem; font-weight: 600; padding: 3px 9px; border-radius: 50px; display: inline-flex; align-items: center; }
    .claim-badge.belum     { background: #f3f4f6; color: var(--c-fg-muted); }
    .claim-badge.diajukan  { background: #dbeafe; color: #1e40af; }
    .claim-badge.disetujui { background: #ECFDF5; color: #059669; }
    .claim-badge.ditolak   { background: var(--c-error-subtle); color: var(--c-error); }

    .reward-mini { font-size: .72rem; color: var(--c-fg-muted); margin-top: 4px; max-width: 200px; line-height: 1.4; }

    .btn-tinjau {
        background: var(--c-primary-subtle); color: var(--c-primary); border: 1px solid rgba(11,38,110,0.18); padding: 5px 14px;
        border-radius: 8px; font-size: .8rem; font-weight: 600; cursor: pointer; transition: all .15s;
    }
    .btn-tinjau:hover { background: rgba(11,38,110,0.12); border-color: var(--c-primary-border); }

    .tinjau-info {
        font-size: .87rem; color: var(--c-fg-sec); background: #fafafa; border: 1px solid var(--c-border);
        border-radius: 10px; padding: 12px 14px; line-height: 1.7;
    }
    .tinjau-info .lbl { color: var(--c-fg-muted); }
    .kuota-pill {
        display: inline-block; margin-top: 10px; font-size: .8rem; font-weight: 600;
        padding: 5px 12px; border-radius: 50px; background: var(--c-primary-subtle); color: var(--c-primary);
    }
    .kuota-pill.penuh { background: var(--c-error-subtle); color: var(--c-error); }

    /* ── Pagination (global style) ── */
    .verif-pagination {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 16px; background: #fff; border-top: 1px solid #e5e7eb;
        flex-wrap: wrap; gap: 10px;
    }

    /* ── Enhanced Table Visibility ── */
    table thead tr {
        background: #eef0f4 !important;
        border-bottom: 2px solid #d1d5db !important;
    }
    table thead th {
        font-size: 11.5px !important;
        font-weight: 700 !important;
        color: #374151 !important;
        text-transform: uppercase;
        letter-spacing: .03em;
        padding-top: 13px !important;
        padding-bottom: 13px !important;
    }
    table tbody tr {
        border-bottom: 1px solid #e5e7eb !important;
    }
    table tbody tr:nth-child(even) {
        background: #f9fafb;
    }
    table tbody tr:hover {
        background: #eef2ff !important;
        box-shadow: inset 3px 0 0 0 #0B266E;
    }
    table tbody td {
        font-size: 13px;
        padding-top: 15px !important;
        padding-bottom: 15px !important;
    }
</style>

<!-- Flash Messages -->
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: #ECFDF5; color: #059669; font-weight: 500; font-size: 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: #fef2f2; color: #dc2626; font-weight: 500; font-size: 14px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        <?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert"
         style="border-radius: 10px; border: none; background: #fef2f2; color: #dc2626; font-weight: 500; font-size: 14px;">
        <ul style="margin: 0; padding-left: 18px;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <li><?php echo e($err); ?></li>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<!-- Page Header -->
<div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:24px;">
    <div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'prestasi'): ?>
            <h4 style="font-size:1.45rem; font-weight:700; color:var(--c-fg); margin-bottom:2px; letter-spacing:-.02em;">Verifikasi Prestasi</h4>
            <p style="font-size:.82rem; color:var(--c-fg-muted); margin:0;">Review & verifikasi prestasi lomba yang diajukan mahasiswa</p>
        <?php else: ?>
            <h4 style="font-size:1.45rem; font-weight:700; color:var(--c-fg); margin-bottom:2px; letter-spacing:-.02em;">Verifikasi Riwayat Kegiatan</h4>
            <p style="font-size:.82rem; color:var(--c-fg-muted); margin:0;">Review & verifikasi riwayat keikutsertaan kegiatan yang diajukan mahasiswa</p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'prestasi'): ?>
        <a href="<?php echo e(route('manajemenmahasiswa.verifikasi.reward.index')); ?>"
           style="background:#0B266E; color:#fff; font-weight:600; font-size:.85rem; padding:9px 18px; border-radius:8px; text-decoration:none; white-space:nowrap; display:inline-flex; align-items:center; gap:8px; transition:all .15s; border:none;"
           onmouseover="this.style.background='#091958'" onmouseout="this.style.background='#0B266E'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h6"/><path d="M9 12l2 2 4-4"/><path d="M16 5h6M19 2v6"/></svg>
            Klaim Reward
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendingPrestasiReward > 0): ?>
                <span style="background:#fff; color:#0B266E; font-size:.72rem; font-weight:700; padding:2px 8px; border-radius:50px;"><?php echo e($pendingPrestasiReward); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<!-- Admin Stat Cards -->
<div class="admin-stats">
    <a href="<?php echo e(route('manajemenmahasiswa.verifikasi.index', ['tab' => $tab, 'status' => 'pending'])); ?>"
       class="admin-stat-card pending <?php echo e($status === 'pending' ? 'active' : ''); ?>">
        <div class="stat-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
            <div class="stat-num"><?php echo e($adminStats['pending']); ?></div>
            <div class="stat-lbl">Menunggu Verifikasi</div>
        </div>
    </a>
    <a href="<?php echo e(route('manajemenmahasiswa.verifikasi.index', ['tab' => $tab, 'status' => 'approved'])); ?>"
       class="admin-stat-card approved <?php echo e($status === 'approved' ? 'active' : ''); ?>">
        <div class="stat-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div>
            <div class="stat-num"><?php echo e($adminStats['approved']); ?></div>
            <div class="stat-lbl">Disetujui</div>
        </div>
    </a>
    <a href="<?php echo e(route('manajemenmahasiswa.verifikasi.index', ['tab' => $tab, 'status' => 'rejected'])); ?>"
       class="admin-stat-card rejected <?php echo e($status === 'rejected' ? 'active' : ''); ?>">
        <div class="stat-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        </div>
        <div>
            <div class="stat-num"><?php echo e($adminStats['rejected']); ?></div>
            <div class="stat-lbl">Ditolak</div>
        </div>
    </a>
</div>

<!-- Main Table Card (Global Style) -->
<div style="background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column;">

    <!-- Table Toolbar -->
    <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid #e5e7eb; gap:10px; flex-wrap:wrap;">
        <div style="display:flex; align-items:center; gap:12px; flex-shrink:0;">
            <h2 style="font-size:14px; font-weight:700; color:var(--c-fg); margin:0;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'prestasi'): ?> Verifikasi Prestasi <?php else: ?> Verifikasi Riwayat Kegiatan <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </h2>
        </div>

        <form method="GET" action="<?php echo e(route('manajemenmahasiswa.verifikasi.index')); ?>" id="filterForm"
              style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin:0;">
            <input type="hidden" name="tab" value="<?php echo e($tab); ?>">

            <!-- Search -->
            <div style="position:relative; width:min(220px, calc(100vw - 200px)); min-width:120px;">
                <svg style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--c-fg-placeholder); pointer-events:none;"
                     width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                       placeholder="Cari nama / NIM..."
                       style="width:100%; height:34px; padding:0 12px 0 34px; border:1px solid var(--c-border); border-radius:8px; font-size:12.5px; color:var(--c-fg); font-family:inherit; outline:none; transition:all .15s; box-sizing:border-box; background:#fff;"
                       onfocus="this.style.borderColor='var(--c-primary)'; this.style.boxShadow='0 0 0 3px rgba(11,38,110,0.08)'"
                       onblur="this.style.borderColor='var(--c-border)'; this.style.boxShadow='none'">
            </div>


            <!-- Angkatan Filter -->
            <select name="angkatan"
                    style="height:34px; padding:0 10px; border:1px solid var(--c-border); border-radius:8px; font-size:12.5px; font-weight:600; font-family:inherit; color:var(--c-fg-sec); outline:none; background:#fff; cursor:pointer;"
                    onchange="document.getElementById('filterForm').submit()">
                <option value="semua">Semua Angkatan</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $angkatanList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <option value="<?php echo e($a); ?>" <?php echo e($angkatan == $a ? 'selected' : ''); ?>><?php echo e($a); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
        </form>
    </div>

<!-- Tab Content: Riwayat Kegiatan -->
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'riwayat'): ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($riwayatData->count() > 0): ?>
        <!-- Table -->
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; min-width:780px;">
                <thead>
                    <tr style="border-bottom:1px solid #e5e7eb; background:#FAFAFA;">
                        <th style="padding:11px 12px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:48px;">No</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; min-width:160px;">Mahasiswa</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">NIM</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; min-width:180px;">Nama Kegiatan</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Peran</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Tanggal</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Bukti</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Status</th>
                        <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $riwayatData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $rw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr style="border-bottom:1px solid #e5e7eb; transition:background .12s;"
                            onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                            <td style="padding:14px 12px; font-size:13px; font-weight:400; color:var(--c-fg-muted); width:48px;"><?php echo e(($riwayatData->currentPage() - 1) * $riwayatData->perPage() + $i + 1); ?></td>
                            <td style="padding:14px 16px; min-width:160px;">
                                <p style="font-size:13px; font-weight:600; color:var(--c-fg); margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:160px;"><?php echo e($rw->student->user->name ?? '-'); ?></p>
                            </td>
                            <td style="padding:14px 16px;">
                                <span style="font-family:monospace; font-size:12px; font-weight:600; color:var(--c-primary);"><?php echo e($rw->student->student_number ?? '-'); ?></span>
                            </td>
                            <td style="padding:14px 16px; min-width:180px;">
                                <p style="font-size:13px; font-weight:600; color:var(--c-fg); margin:0; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; line-height:1.5; max-width:220px;"><?php echo e($rw->nama_kegiatan_manual ?? 'Kegiatan tidak diketahui'); ?></p>
                            </td>
                            <td style="padding:14px 16px; font-size:13px; color:var(--c-fg-sec);"><?php echo e($rw->peran_manual ?? ucfirst($rw->peran ?? '')); ?></td>
                            <td style="padding:14px 16px; white-space:nowrap;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rw->tanggal_kegiatan): ?>
                                    <span style="font-size:12px; font-weight:500; color:var(--c-fg-sec);"><?php echo e($rw->tanggal_kegiatan->translatedFormat('d M Y')); ?></span>
                                <?php else: ?>
                                    <span style="font-size:12px; color:var(--c-fg-muted);">-</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td style="padding:14px 16px;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rw->buktiFiles && $rw->buktiFiles->count() > 0): ?>
                                    <div style="display:flex; gap:4px; flex-wrap:wrap;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $rw->buktiFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bukti): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <a href="<?php echo e($bukti->public_url); ?>" target="_blank" title="<?php echo e($bukti->nama_file); ?>" style="text-decoration: none;">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bukti->isImage()): ?>
                                                    <img src="<?php echo e($bukti->public_url); ?>" style="width: 36px; height: 36px; border-radius: 6px; object-fit: cover; border: 1px solid var(--c-border); cursor: pointer; transition: transform 0.15s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                                                <?php else: ?>
                                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 6px; background: #eef2ff; border: 1px solid var(--c-border); font-size: 16px; cursor: pointer;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--c-primary)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg></span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </a>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span style="font-size: 11px; color: var(--c-fg-muted);">—</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td style="padding:14px 16px;">
                                <span class="status-verif <?php echo e($rw->verification_status); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rw->verification_status === 'pending'): ?> Pending
                                    <?php elseif($rw->verification_status === 'approved'): ?> Disetujui
                                    <?php else: ?> Ditolak
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rw->verification_status === 'rejected' && $rw->verification_note): ?>
                                    <p style="font-size:11px; color:var(--c-error); margin:4px 0 0 0; max-width:160px; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; line-height:1.4;" title="<?php echo e($rw->verification_note); ?>">
                                        "<?php echo e(Str::limit($rw->verification_note, 40)); ?>"
                                    </p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rw->verification_status === 'approved' && $rw->verification_note): ?>
                                    <p style="font-size:11px; color:var(--c-success); margin:4px 0 0 0; max-width:160px; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; line-height:1.4;" title="<?php echo e($rw->verification_note); ?>">
                                        "<?php echo e(Str::limit($rw->verification_note, 40)); ?>"
                                    </p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td style="padding:14px 16px; text-align:center;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rw->verification_status === 'pending'): ?>
                                    <div style="display:flex; gap:6px; justify-content:center;">
                                        <button type="button" class="btn-approve" title="Setujui"
                                                onclick="openApproveModal('riwayat', <?php echo e($rw->id); ?>, '<?php echo e(addslashes($rw->nama_kegiatan_manual ?? '')); ?>')">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        </button>
                                        <button type="button" class="btn-reject" title="Tolak"
                                                onclick="openRejectModal('riwayat', <?php echo e($rw->id); ?>, '<?php echo e(addslashes($rw->nama_kegiatan_manual ?? '')); ?>')">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <span style="font-size: 11px; color: var(--c-fg-muted);">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rw->verifiedBy): ?> <?php echo e($rw->verifiedBy->name); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination (Global Style) -->
        <?php
            $rFrom = $riwayatData->firstItem() ?? 0;
            $rTo   = $riwayatData->lastItem() ?? 0;
            $rTotal = $riwayatData->total();
            $rCurrentPage = $riwayatData->currentPage();
            $rLastPage = $riwayatData->lastPage();
        ?>
        <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; background:#fff; border-top:1px solid #e5e7eb; flex-wrap:wrap; gap:10px;">
            <span style="font-size:12px; color:var(--c-fg-sec);">
                Showing <strong style="color:var(--c-fg); font-weight:700;"><?php echo e($rFrom); ?></strong>
                to <strong style="color:var(--c-fg); font-weight:700;"><?php echo e($rTo); ?></strong>
                of <strong style="color:var(--c-fg); font-weight:700;"><?php echo e(number_format($rTotal)); ?></strong> results
            </span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rLastPage > 1): ?>
            <div style="display:flex; align-items:center; gap:4px;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rCurrentPage > 1): ?>
                <a href="<?php echo e($riwayatData->appends(request()->query())->previousPageUrl()); ?>" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;" onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
                </a>
                <?php else: ?>
                <span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid #F3F4F6; border-radius:6px; background:#FAFAFA; color:#D1D5DB; cursor:not-allowed;"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php $range = 2; $start = max(1, $rCurrentPage - $range); $end = min($rLastPage, $rCurrentPage + $range); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($start > 1): ?>
                    <a href="<?php echo e($riwayatData->appends(request()->query())->url(1)); ?>" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; font-size:12px; font-weight:500; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;" onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">1</a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($start > 2): ?><span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; font-size:12px; color:var(--c-fg-muted);">…</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($p = $start; $p <= $end; $p++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <a href="<?php echo e($riwayatData->appends(request()->query())->url($p)); ?>" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border-radius:6px; font-size:12px; font-weight:<?php echo e($p === $rCurrentPage ? '700' : '500'); ?>; text-decoration:none; transition:all .15s; <?php echo e($p === $rCurrentPage ? 'background:var(--c-primary); color:#fff; border:1px solid var(--c-primary); box-shadow:0 2px 6px rgba(11,38,110,0.25);' : 'border:1px solid var(--c-border); background:#fff; color:var(--c-fg-sec);'); ?>"><?php echo e($p); ?></a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($end < $rLastPage): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($end < $rLastPage - 1): ?><span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; font-size:12px; color:var(--c-fg-muted);">…</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <a href="<?php echo e($riwayatData->appends(request()->query())->url($rLastPage)); ?>" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; font-size:12px; font-weight:500; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;" onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'"><?php echo e($rLastPage); ?></a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rCurrentPage < $rLastPage): ?>
                <a href="<?php echo e($riwayatData->appends(request()->query())->nextPageUrl()); ?>" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;" onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg>
                </a>
                <?php else: ?>
                <span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid #F3F4F6; border-radius:6px; background:#FAFAFA; color:#D1D5DB; cursor:not-allowed;"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">
                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" style="color:#E5E7EB;" stroke="currentColor" stroke-width="1.5">
                    <rect x="8" y="2" width="8" height="4" rx="1"></rect><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><path d="M9 12h6M9 16h6"></path>
                </svg>
            </div>
            <p style="font-size:12px; font-weight:600; color:var(--c-fg-muted); text-transform:uppercase; letter-spacing:0.06em; margin:0;">Tidak ada data riwayat kegiatan</p>
            <p style="font-size:11px; color:var(--c-fg-placeholder); margin:4px 0 0 0;">Belum ada pengajuan riwayat kegiatan yang sesuai filter</p>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<!-- Tab Content: Prestasi Lomba -->
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tab === 'prestasi'): ?>
    <?php $P = \Modules\ManajemenMahasiswa\Models\Prestasi::class; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($prestasiData->count() > 0): ?>
        <!-- Table -->
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; min-width:960px;">
                <thead>
                    <tr style="border-bottom:1px solid #e5e7eb; background:#FAFAFA;">
                        <th style="padding:11px 12px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:48px;">No</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; min-width:160px;">Mahasiswa</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">NIM</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; min-width:180px;">Nama Prestasi</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Tingkat</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Tanggal</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Bukti</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Status</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; min-width:140px;">Reward</th>
                        <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $prestasiData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr style="border-bottom:1px solid #e5e7eb; transition:background .12s;"
                            onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                            <td style="padding:14px 12px; font-size:13px; font-weight:400; color:var(--c-fg-muted); width:48px;"><?php echo e(($prestasiData->currentPage() - 1) * $prestasiData->perPage() + $i + 1); ?></td>
                            <td style="padding:14px 16px; min-width:160px;">
                                <p style="font-size:13px; font-weight:600; color:var(--c-fg); margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:160px;"><?php echo e($p->kemahasiswaan->nama ?? '-'); ?></p>
                            </td>
                            <td style="padding:14px 16px;">
                                <span style="font-family:monospace; font-size:12px; font-weight:600; color:var(--c-primary);"><?php echo e($p->kemahasiswaan->nim ?? '-'); ?></span>
                            </td>
                            <td style="padding:14px 16px; min-width:180px;">
                                <p style="font-size:13px; font-weight:600; color:var(--c-fg); margin:0; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; line-height:1.5; max-width:220px;"><?php echo e($p->nama_prestasi); ?></p>
                            </td>
                            <td style="padding:14px 16px;"><span class="tingkat-badge <?php echo e($p->tingkat); ?>"><?php echo e(ucfirst($p->tingkat)); ?></span></td>
                            <td style="padding:14px 16px; white-space:nowrap;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->tanggal): ?>
                                    <span style="font-size:12px; font-weight:500; color:var(--c-fg-sec);"><?php echo e(\Carbon\Carbon::parse($p->tanggal)->translatedFormat('d M Y')); ?></span>
                                <?php else: ?>
                                    <span style="font-size:12px; color:var(--c-fg-muted);">-</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td style="padding:14px 16px;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->buktiFiles && $p->buktiFiles->count() > 0): ?>
                                    <div style="display:flex; gap:4px; flex-wrap:wrap;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $p->buktiFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bukti): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <a href="<?php echo e($bukti->public_url); ?>" target="_blank" title="<?php echo e($bukti->nama_file); ?>" style="text-decoration: none;">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bukti->isImage()): ?>
                                                    <img src="<?php echo e($bukti->public_url); ?>" style="width: 36px; height: 36px; border-radius: 6px; object-fit: cover; border: 1px solid var(--c-border); cursor: pointer; transition: transform 0.15s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                                                <?php else: ?>
                                                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 6px; background: #eef2ff; border: 1px solid var(--c-border); font-size: 16px; cursor: pointer;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--c-primary)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg></span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </a>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span style="font-size: 11px; color: var(--c-fg-muted);">—</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td style="padding:14px 16px;">
                                <span class="status-verif <?php echo e($p->verification_status); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->verification_status === 'pending'): ?> Pending
                                    <?php elseif($p->verification_status === 'approved'): ?> Disetujui
                                    <?php else: ?> Ditolak
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->verification_status === 'rejected' && $p->verification_note): ?>
                                    <p style="font-size:11px; color:var(--c-error); margin:4px 0 0 0; max-width:160px; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; line-height:1.4;" title="<?php echo e($p->verification_note); ?>">
                                        "<?php echo e(Str::limit($p->verification_note, 40)); ?>"
                                    </p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->verification_status === 'approved' && $p->verification_note): ?>
                                    <p style="font-size:11px; color:var(--c-success); margin:4px 0 0 0; max-width:160px; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; line-height:1.4;" title="<?php echo e($p->verification_note); ?>">
                                        "<?php echo e(Str::limit($p->verification_note, 40)); ?>"
                                    </p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td style="padding:14px 16px;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->verification_status === 'approved'): ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->reward_status === $P::CLAIM_DIAJUKAN): ?>
                                        <span class="claim-badge diajukan">Menunggu</span>
                                    <?php elseif($p->reward_status === $P::CLAIM_DISETUJUI): ?>
                                        <span class="claim-badge disetujui">Disetujui</span>
                                    <?php elseif($p->reward_status === $P::CLAIM_DITOLAK): ?>
                                        <span class="claim-badge ditolak">Ditolak</span>
                                    <?php else: ?>
                                        <span style="font-size:11px; color:var(--c-fg-muted);">Belum diajukan</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <span style="color:#d1d5db;">—</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td style="padding:14px 16px; text-align:center;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->verification_status === 'pending'): ?>
                                    <div style="display:flex; gap:6px; justify-content:center;">
                                        <button type="button" class="btn-approve" title="Setujui"
                                                onclick="openApproveModal('prestasi', <?php echo e($p->id); ?>, '<?php echo e(addslashes($p->nama_prestasi)); ?>')">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        </button>
                                        <button type="button" class="btn-reject" title="Tolak"
                                                onclick="openRejectModal('prestasi', <?php echo e($p->id); ?>, '<?php echo e(addslashes($p->nama_prestasi)); ?>')">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <span style="font-size: 11px; color: var(--c-fg-muted);">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->verifiedBy): ?> <?php echo e($p->verifiedBy->name); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination (Global Style) -->
        <?php
            $pFrom = $prestasiData->firstItem() ?? 0;
            $pTo   = $prestasiData->lastItem() ?? 0;
            $pTotal = $prestasiData->total();
            $pCurrentPage = $prestasiData->currentPage();
            $pLastPage = $prestasiData->lastPage();
        ?>
        <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; background:#fff; border-top:1px solid #e5e7eb; flex-wrap:wrap; gap:10px;">
            <span style="font-size:12px; color:var(--c-fg-sec);">
                Showing <strong style="color:var(--c-fg); font-weight:700;"><?php echo e($pFrom); ?></strong>
                to <strong style="color:var(--c-fg); font-weight:700;"><?php echo e($pTo); ?></strong>
                of <strong style="color:var(--c-fg); font-weight:700;"><?php echo e(number_format($pTotal)); ?></strong> results
            </span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pLastPage > 1): ?>
            <div style="display:flex; align-items:center; gap:4px;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pCurrentPage > 1): ?>
                <a href="<?php echo e($prestasiData->appends(request()->query())->previousPageUrl()); ?>" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;" onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
                </a>
                <?php else: ?>
                <span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid #F3F4F6; border-radius:6px; background:#FAFAFA; color:#D1D5DB; cursor:not-allowed;"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php $range = 2; $start = max(1, $pCurrentPage - $range); $end = min($pLastPage, $pCurrentPage + $range); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($start > 1): ?>
                    <a href="<?php echo e($prestasiData->appends(request()->query())->url(1)); ?>" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; font-size:12px; font-weight:500; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;" onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">1</a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($start > 2): ?><span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; font-size:12px; color:var(--c-fg-muted);">…</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($p_page = $start; $p_page <= $end; $p_page++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <a href="<?php echo e($prestasiData->appends(request()->query())->url($p_page)); ?>" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border-radius:6px; font-size:12px; font-weight:<?php echo e($p_page === $pCurrentPage ? '700' : '500'); ?>; text-decoration:none; transition:all .15s; <?php echo e($p_page === $pCurrentPage ? 'background:var(--c-primary); color:#fff; border:1px solid var(--c-primary); box-shadow:0 2px 6px rgba(11,38,110,0.25);' : 'border:1px solid var(--c-border); background:#fff; color:var(--c-fg-sec);'); ?>"><?php echo e($p_page); ?></a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($end < $pLastPage): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($end < $pLastPage - 1): ?><span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; font-size:12px; color:var(--c-fg-muted);">…</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <a href="<?php echo e($prestasiData->appends(request()->query())->url($pLastPage)); ?>" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; font-size:12px; font-weight:500; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;" onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'"><?php echo e($pLastPage); ?></a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pCurrentPage < $pLastPage): ?>
                <a href="<?php echo e($prestasiData->appends(request()->query())->nextPageUrl()); ?>" style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;" onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg>
                </a>
                <?php else: ?>
                <span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid #F3F4F6; border-radius:6px; background:#FAFAFA; color:#D1D5DB; cursor:not-allowed;"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">
                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" style="color:#E5E7EB;" stroke="currentColor" stroke-width="1.5">
                    <path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0V4z"></path><path d="M5 4H3v2a3 3 0 0 0 3 3M19 4h2v2a3 3 0 0 1-3 3"></path>
                </svg>
            </div>
            <p style="font-size:12px; font-weight:600; color:var(--c-fg-muted); text-transform:uppercase; letter-spacing:0.06em; margin:0;">Tidak ada data prestasi</p>
            <p style="font-size:11px; color:var(--c-fg-placeholder); margin:4px 0 0 0;">Belum ada pengajuan prestasi yang sesuai filter</p>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div> 

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="color: #0D0D12;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -3px;"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                        Tolak Pengajuan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p style="font-size: 14px; color: #666D80; margin-bottom: 16px;">
                        Anda akan menolak: <strong id="rejectItemName" style="color: #1f2937;"></strong>
                    </p>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-bold mb-0" style="font-size: 13px;">Alasan Penolakan <span style="color: #dc2626;">*</span></label>
                        <span class="text-muted" style="font-size: 11px;" id="charCount_reject">0 / 200 huruf</span>
                    </div>
                    <textarea name="verification_note" class="form-control" rows="3" required maxlength="200"
                              placeholder="Contoh: Data kurang lengkap, bukti tidak valid, dll."
                              style="border-radius: 10px; font-size: 14px;" oninput="document.getElementById('charCount_reject').innerText = this.value.length + ' / 200 huruf'"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" style="padding: 8px 20px; border-radius: 10px; border: none; background: #dc2626; color: #fff; font-weight: 600; font-size: 14px; cursor: pointer;">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="approveForm" method="POST">
                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="color: #0D0D12;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -3px;"><circle cx="12" cy="12" r="10"></circle><polyline points="9 12 11 14 15 10"></polyline></svg>
                        Setujui Pengajuan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p style="font-size: 14px; color: #666D80; margin-bottom: 16px;">
                        Anda akan menyetujui: <strong id="approveItemName" style="color: #1f2937;"></strong>
                    </p>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-bold mb-0" style="font-size: 13px;">Catatan Persetujuan <span style="font-weight: 400; color: #666D80;">(opsional)</span></label>
                        <span class="text-muted" style="font-size: 11px;" id="charCount_approve">0 / 200 huruf</span>
                    </div>
                    <textarea name="verification_note" class="form-control" rows="3" maxlength="200"
                              placeholder="Contoh: Dokumen lengkap dan valid. Disetujui."
                              style="border-radius: 10px; font-size: 14px; border-color: #bbf7d0;"
                              onfocus="this.style.borderColor='#16a34a';this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.1)'"
                              onblur="this.style.borderColor='#bbf7d0';this.style.boxShadow='none'" oninput="document.getElementById('charCount_approve').innerText = this.value.length + ' / 200 huruf'"></textarea>
                    <p style="display: none;" aria-hidden="true">Catatan akan terlihat di halaman status mahasiswa.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" style="padding: 8px 20px; border-radius: 10px; border: none; background: #16a34a; color: #fff; font-weight: 600; font-size: 14px; cursor: pointer;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 4px;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRejectModal(type, id, itemName) {
    const baseUrl = '<?php echo e(url("manajemen-mahasiswa/verifikasi")); ?>';
    document.getElementById('rejectForm').action = `${baseUrl}/${type}/${id}/reject`;
    document.getElementById('rejectItemName').textContent = itemName;
    document.getElementById('rejectForm').querySelector('textarea').value = '';
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
function openApproveModal(type, id, itemName) {
    const baseUrl = '<?php echo e(url("manajemen-mahasiswa/verifikasi")); ?>';
    document.getElementById('approveForm').action = `${baseUrl}/${type}/${id}/approve`;
    document.getElementById('approveItemName').textContent = itemName;
    document.getElementById('approveForm').querySelector('textarea').value = '';
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}
</script>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\ManajemenMahasiswa\resources\views\verifikasi\admin.blade.php ENDPATH**/ ?>