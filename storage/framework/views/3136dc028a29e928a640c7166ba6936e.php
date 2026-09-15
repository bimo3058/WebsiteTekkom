<?php if (isset($component)) { $__componentOriginal5f8703e57583ba4eb55a214d5c40ce87 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5f8703e57583ba4eb55a214d5c40ce87 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.layouts.admin','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


    <?php $__env->startPush('styles'); ?>
        <style>
            .main-wrapper {
                background: transparent !important;
                box-shadow: none !important;
                padding: 0 !important;
            }

            .verif-header {
                margin-bottom: 28px;
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 12px;
            }
            .verif-header-text h4 {
                font-size: 1.5rem;
                font-weight: 700;
                color: #1e1b4b;
                margin-bottom: 4px;
            }
            .verif-header-text p {
                font-size: 0.95rem;
                color: #6b7280;
                margin-bottom: 0;
            }

            .pending-counter {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 10px 20px;
                background: linear-gradient(135deg, #fef3c7 0%, #fff7ed 100%);
                border: 1px solid #fde68a;
                border-radius: 50px;
                font-size: 0.9rem;
                font-weight: 700;
                color: #d97706;
            }
            .pending-counter .counter-num {
                background: #f59e0b;
                color: #fff;
                width: 28px;
                height: 28px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.85rem;
                font-weight: 800;
            }

            /* Tab Filter */
            .verif-tabs {
                display: flex;
                gap: 6px;
                margin-bottom: 24px;
                border-bottom: 2px solid #f3f4f6;
                padding-bottom: 0;
            }
            .verif-tab {
                padding: 10px 20px;
                font-size: 0.88rem;
                font-weight: 600;
                color: #6b7280;
                text-decoration: none;
                border-bottom: 3px solid transparent;
                margin-bottom: -2px;
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }
            .verif-tab:hover {
                color: #6B4FF4;
            }
            .verif-tab.active {
                color: #6B4FF4;
                border-bottom-color: #6B4FF4;
            }
            .tab-count {
                background: #f3f4f6;
                color: #6b7280;
                padding: 2px 8px;
                border-radius: 50px;
                font-size: 0.75rem;
                font-weight: 700;
            }
            .verif-tab.active .tab-count {
                background: #F5F3FF;
                color: #6B4FF4;
            }

            /* Flash Messages */
            .alert-success {
                background: #d1fae5;
                color: #065f46;
                border: 1px solid #a7f3d0;
                border-radius: 12px;
                padding: 14px 18px;
                font-size: 0.9rem;
                font-weight: 500;
                margin-bottom: 20px;
            }
            .alert-error {
                background: #fee2e2;
                color: #991b1b;
                border: 1px solid #fca5a5;
                border-radius: 12px;
                padding: 14px 18px;
                font-size: 0.9rem;
                font-weight: 500;
                margin-bottom: 20px;
            }

            /* Request Card */
            .request-card {
                background: #fff;
                border-radius: 16px;
                border: 1px solid #e5e7eb;
                padding: 24px;
                margin-bottom: 16px;
                transition: all 0.2s ease;
            }
            .request-card:hover {
                border-color: #d1d5db;
                box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            }
            .request-card-header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 16px;
                margin-bottom: 16px;
            }
            .request-card-title {
                font-size: 1.05rem;
                font-weight: 700;
                color: #1e1b4b;
                margin-bottom: 6px;
                line-height: 1.4;
            }
            .request-card-title a {
                color: inherit;
                text-decoration: none;
                transition: color 0.15s;
            }
            .request-card-title a:hover {
                color: #6B4FF4;
            }

            .request-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                font-size: 0.85rem;
                color: #6b7280;
            }
            .request-meta-item {
                display: flex;
                align-items: center;
                gap: 5px;
            }
            .request-meta-item svg {
                color: #9ca3af;
                flex-shrink: 0;
            }

            .request-pesan {
                background: #f9fafb;
                border: 1px solid #f3f4f6;
                border-radius: 10px;
                padding: 12px 16px;
                font-size: 0.88rem;
                color: #4b5563;
                margin-bottom: 16px;
                line-height: 1.5;
            }
            .request-pesan .pesan-label {
                font-weight: 700;
                color: #374151;
                font-size: 0.82rem;
                margin-bottom: 4px;
                display: flex;
                align-items: center;
                gap: 5px;
            }

            /* Status badges */
            .status-badge {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 5px 12px;
                border-radius: 50px;
                font-size: 0.78rem;
                font-weight: 700;
                letter-spacing: 0.02em;
                text-transform: uppercase;
                flex-shrink: 0;
            }
            .badge-pending {
                background: #fef3c7;
                color: #d97706;
                border: 1px solid #fde68a;
            }
            .badge-approved {
                background: #d1fae5;
                color: #059669;
                border: 1px solid #a7f3d0;
            }
            .badge-rejected {
                background: #fee2e2;
                color: #dc2626;
                border: 1px solid #fca5a5;
            }
            .badge-cancelled {
                background: #f3f4f6;
                color: #6b7280;
                border: 1px solid #e5e7eb;
            }

            /* Action area */
            .request-actions {
                display: flex;
                align-items: flex-end;
                gap: 12px;
                padding-top: 16px;
                border-top: 1px solid #f3f4f6;
            }
            .catatan-input-group {
                flex: 1;
            }
            .catatan-input-group label {
                font-size: 0.82rem;
                font-weight: 600;
                color: #374151;
                display: block;
                margin-bottom: 6px;
            }
            .catatan-input {
                width: 100%;
                padding: 10px 14px;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                font-size: 0.88rem;
                color: #374151;
                background: #fafafa;
                outline: none;
                font-family: inherit;
                resize: none;
                min-height: 44px;
                transition: all 0.2s;
            }
            .catatan-input:focus {
                border-color: #6B4FF4;
                background: #fff;
                box-shadow: 0 0 0 3px rgba(107, 79, 244, 0.12);
            }

            .action-buttons {
                display: flex;
                gap: 8px;
                flex-shrink: 0;
            }
            .btn-approve {
                padding: 10px 20px;
                border: none;
                border-radius: 10px;
                background: #059669;
                color: #fff;
                font-size: 0.88rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }
            .btn-approve:hover {
                background: #047857;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
            }
            .btn-reject {
                padding: 10px 20px;
                border: 1px solid #fca5a5;
                border-radius: 10px;
                background: #fff;
                color: #dc2626;
                font-size: 0.88rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }
            .btn-reject:hover {
                background: #fef2f2;
                border-color: #ef4444;
            }

            /* Catatan verifikator (untuk history) */
            .verifikator-catatan {
                background: #fffbeb;
                border: 1px solid #fde68a;
                border-radius: 10px;
                padding: 12px 16px;
                font-size: 0.88rem;
                color: #92400e;
                margin-top: 12px;
            }

            /* Empty state */
            .empty-state {
                text-align: center;
                padding: 60px 20px;
                color: #9ca3af;
            }
            .empty-state-icon {
                width: 64px;
                height: 64px;
                background: #f3f4f6;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 16px;
                color: #d1d5db;
            }
            .empty-state h6 {
                font-size: 1rem;
                font-weight: 600;
                color: #6b7280;
                margin-bottom: 4px;
            }
            .empty-state p {
                font-size: 0.88rem;
                margin-bottom: 0;
            }

            @media (max-width: 640px) {
                .request-card-header { flex-direction: column; }
                .request-actions { flex-direction: column; align-items: stretch; }
                .action-buttons { justify-content: flex-end; }
                .verif-header { flex-direction: column; }
            }
        </style>
    <?php $__env->stopPush(); ?>

    <!-- Header -->
    <div class="verif-header">
        <div class="verif-header-text">
            <h4>Verifikasi Pengumuman</h4>
            <p>Kelola pengajuan verifikasi pengumuman dari staff himpunan</p>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendingCount > 0): ?>
            <div class="pending-counter">
                <div class="counter-num"><?php echo e($pendingCount); ?></div>
                Menunggu Verifikasi
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <!-- Flash Messages -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="alert-error"><?php echo e(session('error')); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Tab Filter -->
    <div class="verif-tabs">
        <a href="<?php echo e(route('manajemenmahasiswa.pengumuman.verifikasi.index', ['status' => 'pending'])); ?>"
           class="verif-tab <?php echo e($statusFilter === 'pending' ? 'active' : ''); ?>">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Menunggu
        </a>
        <a href="<?php echo e(route('manajemenmahasiswa.pengumuman.verifikasi.index', ['status' => 'approved'])); ?>"
           class="verif-tab <?php echo e($statusFilter === 'approved' ? 'active' : ''); ?>">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            Disetujui
        </a>
        <a href="<?php echo e(route('manajemenmahasiswa.pengumuman.verifikasi.index', ['status' => 'rejected'])); ?>"
           class="verif-tab <?php echo e($statusFilter === 'rejected' ? 'active' : ''); ?>">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            Ditolak
        </a>
        <a href="<?php echo e(route('manajemenmahasiswa.pengumuman.verifikasi.index', ['status' => 'all'])); ?>"
           class="verif-tab <?php echo e($statusFilter === 'all' ? 'active' : ''); ?>">
            Semua
        </a>
    </div>

    <!-- Request List -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <?php $isNewRequest = $req->created_at?->gt(now()->subHours(24)); ?>
        <div class="request-card">
            <div class="request-card-header">
                <div>
                    <div class="request-card-title">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($req->pengumuman): ?>
                        <a href="<?php echo e(route('manajemenmahasiswa.pengumuman.show', $req->pengumuman_id)); ?>">
                            📢 <?php echo e($req->pengumuman->judul); ?>

                        </a>
                        <?php else: ?>
                            <span style="color:#9ca3af;">📢 (Pengumuman telah dihapus)</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isNewRequest && $req->status === 'pending'): ?>
                            <span style="display:inline-flex;align-items:center;padding:2px 8px;border-radius:50px;font-size:10px;font-weight:800;background:#dcfce7;color:#15803d;letter-spacing:.04em;margin-left:6px;vertical-align:middle;">BARU</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="request-meta">
                        <span class="request-meta-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <?php echo e($req->requester?->name ?? 'Unknown'); ?>

                        </span>
                        <span class="request-meta-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <?php echo e($req->created_at->diffForHumans()); ?>

                        </span>
                    </div>
                </div>

                <span class="status-badge
                    <?php if($req->status === 'pending'): ?> badge-pending
                    <?php elseif($req->status === 'approved'): ?> badge-approved
                    <?php elseif($req->status === 'rejected'): ?> badge-rejected
                    <?php else: ?> badge-cancelled <?php endif; ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($req->status === 'pending'): ?> ⏳ Menunggu
                    <?php elseif($req->status === 'approved'): ?> ✅ Disetujui
                    <?php elseif($req->status === 'rejected'): ?> ❌ Ditolak
                    <?php else: ?> 🚫 Dibatalkan <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
            </div>

            <!-- Pesan dari staff -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($req->pesan_pengaju): ?>
                <div class="request-pesan">
                    <div class="pesan-label">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        Pesan dari pengaju:
                    </div>
                    <?php echo e($req->pesan_pengaju); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- Catatan verifikator (jika sudah diproses) -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($req->catatan_verifikator): ?>
                <div class="verifikator-catatan">
                    <strong>Catatan Anda:</strong> <?php echo e($req->catatan_verifikator); ?>

                    <br><small>Diproses: <?php echo e($req->verified_at?->diffForHumans()); ?></small>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <!-- Action Area (hanya untuk pending) -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($req->isPending()): ?>
                <div class="request-actions">
                    <div class="catatan-input-group">
                        <label for="catatan-<?php echo e($req->id); ?>">Catatan <span style="color: #9ca3af; font-weight: 400;">(wajib jika menolak)</span></label>
                        <textarea class="catatan-input" id="catatan-<?php echo e($req->id); ?>" placeholder="Tulis catatan verifikasi..." rows="1"></textarea>
                    </div>
                    <div class="action-buttons">
                        <form action="<?php echo e(route('manajemenmahasiswa.pengumuman.verifikasi.reject', $req->id)); ?>" method="POST" class="d-inline"
                            onsubmit="return handleReject(this, <?php echo e($req->id); ?>)">
                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                            <input type="hidden" name="catatan" class="catatan-hidden-<?php echo e($req->id); ?>">
                            <button type="submit" class="btn-reject">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                Tolak
                            </button>
                        </form>
                        <form action="<?php echo e(route('manajemenmahasiswa.pengumuman.verifikasi.approve', $req->id)); ?>" method="POST" class="d-inline"
                            onsubmit="return handleApprove(this, <?php echo e($req->id); ?>)">
                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                            <input type="hidden" name="catatan" class="catatan-hidden-<?php echo e($req->id); ?>">
                            <button type="submit" class="btn-approve">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                Setujui
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <h6>Tidak ada pengajuan</h6>
            <p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statusFilter === 'pending'): ?>
                    Belum ada pengumuman yang menunggu verifikasi Anda.
                <?php elseif($statusFilter === 'approved'): ?>
                    Belum ada pengumuman yang Anda setujui.
                <?php elseif($statusFilter === 'rejected'): ?>
                    Belum ada pengumuman yang Anda tolak.
                <?php else: ?>
                    Belum ada pengajuan verifikasi yang masuk.
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </p>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAdmin && $requests->hasPages()): ?>
        <div class="d-flex justify-content-center mt-4">
            <?php echo e($requests->appends(request()->query())->links('pagination::bootstrap-5')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php $__env->startPush('scripts'); ?>
    <script>
        /**
         * Sync textarea value to hidden input before approve/reject submit.
         */
        function syncCatatan(reqId) {
            const textarea = document.getElementById('catatan-' + reqId);
            const hiddenInputs = document.querySelectorAll('.catatan-hidden-' + reqId);
            hiddenInputs.forEach(input => {
                input.value = textarea ? textarea.value : '';
            });
        }

        function handleApprove(form, reqId) {
            syncCatatan(reqId);
            if (!confirm('Setujui pengumuman ini? Pengumuman akan langsung dipublikasikan.')) {
                return false;
            }
            return true;
        }

        function handleReject(form, reqId) {
            syncCatatan(reqId);
            const textarea = document.getElementById('catatan-' + reqId);
            if (!textarea || !textarea.value.trim()) {
                textarea.focus();
                textarea.style.borderColor = '#ef4444';
                textarea.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.12)';
                alert('Catatan wajib diisi saat menolak pengumuman.');
                return false;
            }
            if (!confirm('Tolak pengumuman ini? Pengumuman akan dikembalikan ke status draft.')) {
                return false;
            }
            return true;
        }
    </script>
    <?php $__env->stopPush(); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5f8703e57583ba4eb55a214d5c40ce87)): ?>
<?php $attributes = $__attributesOriginal5f8703e57583ba4eb55a214d5c40ce87; ?>
<?php unset($__attributesOriginal5f8703e57583ba4eb55a214d5c40ce87); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5f8703e57583ba4eb55a214d5c40ce87)): ?>
<?php $component = $__componentOriginal5f8703e57583ba4eb55a214d5c40ce87; ?>
<?php unset($__componentOriginal5f8703e57583ba4eb55a214d5c40ce87); ?>
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\ManajemenMahasiswa\resources\views\pengumuman\pengumuman-verifikasi.blade.php ENDPATH**/ ?>