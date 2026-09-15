<?php $__env->startSection('title', 'Lacak Pengaduan Konfidensial'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        /* ── Card ── */
        .detail-card {
            background: #ffffff; border-radius: 12px; padding: 32px;
            border: 1px solid #DDE1E8;
            box-shadow: 0 1px 3px rgba(22,22,43,.06), 0 1px 2px rgba(22,22,43,.04);
        }

        /* ── Page Title ── */
        .page-title h3 {
            font-size: 1.5rem; font-weight: 700; color: #1e1b4b;
            margin: 0 0 4px; letter-spacing: -.02em;
        }
        .page-title p { font-size: .95rem; color: #6b7280; margin: 0; }

        /* ── Tags ── */
        .tags-row { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 12px; }
        .tag-label {
            font-size: 11px; font-weight: 600; padding: 4px 12px;
            border-radius: 20px; display: inline-flex;
            align-items: center; gap: 4px; white-space: nowrap;
        }
        .tag-kategori { background: #e0e7ff; color: #4f46e5; }
        .tag-baru { background: #f3f4f6; color: #4b5563; }
        .tag-dibaca { background: #e0f2fe; color: #0284c7; }
        .tag-didelegasikan { background: #ffedd5; color: #ea580c; }
        .tag-selesai { background: #bbf7d0; color: #15803d; }
        .tag-anonim { background: #111827; color: #fff; }

        /* ── Section Labels ── */
        .section-label {
            font-size: 11px; font-weight: 700; color: #94a3b8;
            text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 8px;
        }
        .section-value { font-size: 15px; font-weight: 600; color: #111827; }



        /* ── Section Divider ── */
        .section-divider { display: flex; align-items: center; gap: 12px; margin: 24px 0 16px; }
        .section-divider span { font-size: 12px; font-weight: 800; color: #374151; text-transform: uppercase; letter-spacing: 0.8px; white-space: nowrap; }
        .section-divider::after { content: ''; flex: 1; height: 1px; background: #DDE1E8; }

        /* ── Info Grid ── */
        .info-grid {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px 20px;
        }
        @media (max-width: 992px) { .info-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 768px) { .info-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 480px) { .info-grid { grid-template-columns: 1fr; } }
        .info-item-label {
            font-size: 11px; font-weight: 700; color: #94a3b8;
            text-transform: uppercase; letter-spacing: .04em; margin-bottom: 2px;
        }
        .info-item-value { font-size: 14px; font-weight: 600; color: #1e293b; }



        /* ── Process Info Card ── */
        .process-info-card {
            background: #fffaf0; border: 1px solid #fde68a;
            border-top: 3px solid #f59e0b; border-radius: 12px; padding: 24px 28px;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-title mb-4">
        <h3>Status Pengaduan</h3>
        <p>Pantau progress tindak lanjut aduan konfidensial Anda di sini.</p>
    </div>

    <?php
        $status = strtolower($pengaduan->status);
        $waktuKejadian = data_get($pengaduan, 'data_template.waktu_kejadian')
            ?? data_get($pengaduan, 'data_template.tanggal_kejadian');
        $linkBukti = data_get($pengaduan, 'data_template.link_bukti');
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert"
            style="background-color: #dcfce7; color: #16a34a; border-radius: 12px; border: none; font-weight: 600;">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="alert alert-danger border-0 mb-4" style="background-color: #fee2e2; color: #b91c1c; border-radius: 12px; font-weight: 500; font-size: 14px;">
            <ul class="mb-0 ps-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <li><?php echo e($error); ?></li>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </ul>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="detail-card mb-4">

        
        <div class="tags-row">
            <?php
                $kategoriRaw = (string) $pengaduan->kategori;
                $kategori = \Modules\ManajemenMahasiswa\Models\Pengaduan::normalizeKategori($kategoriRaw);
                $statusStyle = match($status) {
                    'selesai' => 'tag-selesai',
                    'dibaca' => 'tag-dibaca',
                    'didelegasikan' => 'tag-didelegasikan',
                    default => 'tag-baru',
                };
            ?>
            <span class="tag-label tag-kategori"><?php echo e(ucwords(str_replace('_', ' ', $kategori))); ?></span>
            <span class="tag-label <?php echo e($statusStyle); ?>"><?php echo e($status === 'dibaca' ? 'Diproses' : ucfirst($pengaduan->status)); ?></span>
            <span class="tag-label tag-anonim">
                <?php if (isset($component)) { $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.icon','data' => ['name' => 'locked-01','size' => '11']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'locked-01','size' => '11']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $attributes = $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $component = $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?> Konfidensial
            </span>
        </div>

        
        <h4 class="fw-bold text-dark mb-2" style="font-size: 20px; line-height: 1.4;">
            <?php echo e(data_get($pengaduan, 'data_template.judul', '-')); ?>

        </h4>
        <div style="font-size: 13px; color: #9ca3af; margin-bottom: 24px;">
            Diajukan <?php echo e(optional($pengaduan->created_at)->translatedFormat('d F Y, H:i')); ?> WIB
        </div>

        <div class="section-divider" style="margin-top: 0;">
            <span>Detail Pengaduan</span>
        </div>

        
        <div class="mb-4">
            <div class="section-label">Hal Aduan</div>
            <div class="section-value" style="white-space: pre-wrap; line-height: 1.7;"><?php echo e(data_get($pengaduan, 'data_template.hal_aduan', '—') ?: '—'); ?></div>
        </div>

        
        <div class="mb-4">
            <div class="section-label">Kronologi / Isi Pengaduan</div>
            <div class="section-value" style="white-space: pre-wrap; line-height: 1.7;"><?php echo e(data_get($pengaduan, 'data_template.kronologi', '-')); ?></div>
        </div>

        
        <?php
            $infoItems = collect([
                ['label' => 'Lokasi', 'value' => data_get($pengaduan, 'data_template.lokasi')],
                ['label' => 'Waktu Kejadian', 'value' => $waktuKejadian ? \Carbon\Carbon::parse($waktuKejadian)->translatedFormat('d F Y, H:i') : null],
                ['label' => 'Angkatan', 'value' => data_get($pengaduan, 'data_template.angkatan')],
                ['label' => 'Mata Kuliah', 'value' => data_get($pengaduan, 'data_template.mata_kuliah')],
                ['label' => 'Dosen', 'value' => data_get($pengaduan, 'data_template.nama_dosen')],
                ['label' => 'Tendik', 'value' => data_get($pengaduan, 'data_template.nama_tendik')],
                ['label' => 'Frekuensi', 'value' => data_get($pengaduan, 'data_template.frekuensi')],
            ]);
        ?>

        <div class="section-divider">
            <span>Informasi Tambahan</span>
        </div>
        <div class="info-grid">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $infoItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div>
                    <div class="info-item-label"><?php echo e($info['label']); ?></div>
                    <div class="info-item-value" style="<?php echo e(empty($info['value']) ? 'color: #cbd5e1;' : ''); ?>"><?php echo e($info['value'] ?: '—'); ?></div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div>
                <div class="info-item-label">Bukti Dukung</div>
                <div class="info-item-value">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($linkBukti): ?>
                        <a href="<?php echo e($linkBukti); ?>" target="_blank" rel="noopener noreferrer"
                           style="color: #293C79; text-decoration: none;">Lihat Bukti ↗</a>
                    <?php else: ?>
                        <span style="color: #cbd5e1;">—</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>


    </div>

    
    <?php $delegasiInfo = $pengaduan->delegasiAktif ?? $pengaduan->delegasiTerakhir; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($delegasiInfo && $pengaduan->status === \Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_DIDELEGASIKAN): ?>
        <div class="process-info-card mb-4">
            <div class="d-flex align-items-start gap-3">
                <div style="background: #fef3c7; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #d97706;">
                    <?php if (isset($component)) { $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.icon','data' => ['name' => 'clock-02','size' => '20']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'clock-02','size' => '20']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $attributes = $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $component = $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?>
                </div>
                <div>
                    <h6 class="fw-bold mb-1" style="color: #92400e; font-size: 15px;">Pengaduan Sedang Ditinjau Pihak Berwenang</h6>
                    <p class="mb-0 text-muted" style="font-size: 13px; line-height: 1.6;">
                        Pengaduan Anda telah diteruskan ke pihak yang berwenang untuk ditindaklanjuti.
                        Proses ini membutuhkan waktu.
                    </p>
                    <div class="mt-2 d-flex align-items-center gap-1" style="font-size: 12px; color: #b45309;">
                        <?php if (isset($component)) { $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.icon','data' => ['name' => 'clock-02','size' => '13']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'clock-02','size' => '13']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $attributes = $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $component = $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?>
                        Didelegasikan sejak: <?php echo e($delegasiInfo->delegated_at->translatedFormat('d F Y, H:i')); ?> WIB
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($delegasiInfo && $delegasiInfo->status === 'ditolak' && !in_array($pengaduan->status, [
        \Modules\ManajemenMahasiswa\Models\Pengaduan::STATUS_SELESAI,
    ])): ?>
        <div class="process-info-card mb-4" style="background: #fefce8; border-color: #fde68a;">
            <div class="d-flex align-items-start gap-3">
                <div style="background: #e0f2fe; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #0284c7;">
                    <?php if (isset($component)) { $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.icon','data' => ['name' => 'arrow-circle-left','size' => '20']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-circle-left','size' => '20']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $attributes = $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1)): ?>
<?php $component = $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1; ?>
<?php unset($__componentOriginal828fd8b499e764ca5ddb12621f8bfae1); ?>
<?php endif; ?>
                </div>
                <div>
                    <h6 class="fw-bold mb-1" style="color: #92400e; font-size: 15px;">Pengaduan Dikembalikan ke Admin</h6>
                    <p class="mb-0 text-muted" style="font-size: 13px; line-height: 1.6;">
                        Pihak yang ditunjuk mengembalikan pengaduan ini ke Admin untuk ditinjau ulang.
                        Admin akan menindaklanjuti sendiri atau meneruskan ke pihak lain yang lebih tepat.
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('manajemenmahasiswa::pengaduan.anon.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\ManajemenMahasiswa\resources\views\pengaduan\anon\track.blade.php ENDPATH**/ ?>