<?php $__env->startSection('title', 'Konfirmasi Pengaduan Konfidensial'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .detail-card {
            background: #ffffff; border-radius: 12px; padding: 24px 28px;
            box-shadow: 0 1px 3px rgba(22,22,43,.06), 0 1px 2px rgba(22,22,43,.04);
            border: 1px solid #DDE1E8; margin-bottom: 24px;
        }
        .page-title h4 {
            font-size: 1.5rem; font-weight: 700; color: #1e1b4b;
            margin: 0 0 4px; letter-spacing: -.02em;
        }
        .page-title p { font-size: .95rem; color: #6b7280; margin: 0; }
        .btn-post {
            display: inline-flex; align-items: center; gap: 8px;
            background-color: #293C79; color: white; border: none;
            border-radius: 12px; padding: 10px 24px; font-weight: 600;
            transition: all 0.2s; text-decoration: none;
        }
        .btn-post:hover {
            background-color: #415086; color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(41,60,121,.3);
        }
        .btn-back {
            font-weight: 600; font-size: 13px; text-decoration: none;
            border-radius: 12px; padding: 8px 20px;
            display: inline-flex; align-items: center; gap: 6px;
            transition: all 0.2s; background: transparent;
            border: 1px solid #DDE1E8; color: #6b7280;
        }
        .btn-back:hover { background: #E7E8F0; color: #374151; border-color: #293C79; }

        /* ── Tags ── */
        .tags-row { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 10px; }
        .tag-label {
            font-size: 11px; font-weight: 600; padding: 4px 12px;
            border-radius: 20px; display: inline-flex;
            align-items: center; gap: 4px; white-space: nowrap;
        }
        .tag-kategori { background: #e0e7ff; color: #4f46e5; }
        .tag-anonim { background: #111827; color: #fff; }

        /* ── Section Labels ── */
        .section-label {
            font-size: 11px; font-weight: 700; color: #94a3b8;
            text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 4px;
        }
        .section-value { font-size: 15px; font-weight: 600; color: #111827; }



        /* ── Section Divider ── */
        .section-divider {
            display: flex; align-items: center; gap: 12px; margin: 16px 0 12px;
        }
        .section-divider span {
            font-size: 12px; font-weight: 800; color: #374151;
            text-transform: uppercase; letter-spacing: 0.8px; white-space: nowrap;
        }
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
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $kategoriLabel = ucwords(str_replace('_', ' ', $payload['kategori']));
        $backUrl = route('manajemenmahasiswa.pengaduan.track', ['token' => $token]);

        $infoItems = collect([
            ['label' => 'Lokasi', 'value' => data_get($payload, 'template.lokasi')],
            ['label' => 'Waktu Kejadian', 'value' => data_get($payload, 'template.waktu_kejadian')],
            ['label' => 'Angkatan', 'value' => data_get($payload, 'template.angkatan')],
            ['label' => 'Mata Kuliah', 'value' => data_get($payload, 'template.mata_kuliah')],
            ['label' => 'Dosen', 'value' => data_get($payload, 'template.nama_dosen')],
            ['label' => 'Tendik', 'value' => data_get($payload, 'template.nama_tendik')],
            ['label' => 'Frekuensi', 'value' => data_get($payload, 'template.frekuensi')],
        ]);

        $linkBukti = data_get($payload, 'template.link_bukti');
    ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h4>Konfirmasi Pengaduan</h4>
            <p>Periksa kembali data sebelum dikirim.</p>
        </div>
        <a href="<?php echo e($backUrl); ?>" class="btn-back">
            <?php if (isset($component)) { $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.icon','data' => ['name' => 'chevron-left','size' => '14']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-left','size' => '14']); ?>
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
<?php endif; ?> Ubah
        </a>
    </div>

    <div class="alert border-0 mb-4" style="background-color: #fef3c7; color: #92400e; border-radius: 12px; font-weight: 600; font-size: 14px;">
        <div class="d-flex align-items-center gap-2">
            <?php if (isset($component)) { $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.icon','data' => ['name' => 'alert-triangle','size' => '16']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'alert-triangle','size' => '16']); ?>
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
            Pastikan data sudah benar. Setelah dikirim, pengaduan tidak akan bisa diedit lagi.
        </div>
    </div>

    <div class="detail-card">

        
        <div class="tags-row">
            <span class="tag-label tag-kategori"><?php echo e($kategoriLabel); ?></span>
            <span class="tag-label tag-anonim">
                <?php if (isset($component)) { $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.icon','data' => ['name' => 'shield-02','size' => '11']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shield-02','size' => '11']); ?>
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
            <?php echo e(data_get($payload, 'template.judul', '-')); ?>

        </h4>
        <div style="font-size: 13px; color: #9ca3af; margin-bottom: 16px;">
            Pengaduan konfidensial · <?php echo e(now()->translatedFormat('d F Y, H:i')); ?> WIB
        </div>

        <div class="section-divider" style="margin-top: 0;">
            <span>Detail Pengaduan</span>
        </div>

        
        <div class="mb-3">
            <div class="section-label">Hal Aduan</div>
            <div class="section-value" style="white-space: pre-wrap; line-height: 1.7;"><?php echo e(data_get($payload, 'template.hal_aduan', '—') ?: '—'); ?></div>
        </div>

        
        <div class="mb-3">
            <div class="section-label">Kronologi / Isi Pengaduan</div>
            <div class="section-value" style="white-space: pre-wrap; line-height: 1.7;"><?php echo e(data_get($payload, 'template.kronologi', '-')); ?></div>
        </div>

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

        
        <form method="POST" action="<?php echo e(route('manajemenmahasiswa.pengaduan.anon.store', ['token' => $token])); ?>" class="mt-3">
            <?php echo csrf_field(); ?>

            <input type="hidden" name="kategori" value="<?php echo e($payload['kategori']); ?>">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ($payload['template'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_array($value)): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php continue; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <input type="hidden" name="template[<?php echo e($key); ?>]" value="<?php echo e((string) $value); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

            <div class="d-flex justify-content-end gap-3 mt-3 pt-3" style="border-top: 1px solid #f3f4f6;">
                <a href="<?php echo e($backUrl); ?>" class="btn-back">Kembali</a>
                <button type="submit" class="btn-post">
                    <?php if (isset($component)) { $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.icon','data' => ['name' => 'check','size' => '16']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check','size' => '16']); ?>
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
<?php endif; ?> Kirim Pengaduan
                </button>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('manajemenmahasiswa::pengaduan.anon.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\ManajemenMahasiswa\resources\views\pengaduan\anon\confirm.blade.php ENDPATH**/ ?>