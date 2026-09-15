<?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => $isStaff ? 'manajemenmahasiswa::layouts.admin' : 'manajemenmahasiswa::layouts.mahasiswa'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


    <?php $__env->startPush('styles'); ?>
        <style>
            .main-wrapper { background: transparent !important; box-shadow: none !important; padding: 0 !important; }

            /* ── Card (Forum pattern) ── */
            .form-card {
                background: #ffffff; border-radius: 12px; padding: 32px;
                box-shadow: 0 1px 3px rgba(22,22,43,.06), 0 1px 2px rgba(22,22,43,.04);
                border: 1px solid #DDE1E8;
            }

            /* ── Page Title ── */
            .page-title h4 {
                font-size: 1.5rem; font-weight: 700; color: #1e1b4b;
                margin: 0 0 4px; letter-spacing: -.02em;
            }
            .page-title p { font-size: .95rem; color: #6b7280; margin: 0; }

            /* ── Buttons (Forum pattern) ── */
            .btn-post {
                display: inline-flex; align-items: center; gap: 8px;
                background-color: #293C79; color: white; border: none;
                border-radius: 12px; padding: 10px 24px;
                font-weight: 600; transition: all 0.2s; text-decoration: none;
            }
            .btn-post:hover {
                background-color: #415086; color: white;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(41,60,121,.3);
            }
            .btn-back {
                font-weight: 600; font-size: 13px; text-decoration: none;
                border-radius: 12px; padding: 8px 16px;
                display: inline-flex; align-items: center; gap: 6px;
                transition: all 0.2s; background: transparent;
                border: 1px solid #DDE1E8; color: #6b7280;
            }
            .btn-back:hover { background: #E7E8F0; color: #374151; border-color: #293C79; }

            /* ── Form Controls ── */
            .form-control-custom, .form-select-custom {
                background-color: #f9fafb; border: 1px solid #DDE1E8;
                border-radius: 12px; padding: 12px 16px;
                font-size: 14px; transition: all 0.2s; font-weight: 500;
            }
            .form-control-custom:focus, .form-select-custom:focus {
                background-color: #ffffff; border-color: #293C79;
                box-shadow: 0 0 0 3px rgba(41,60,121,.12); outline: none;
            }
            .form-label-custom {
                font-size: 13px; font-weight: 600; color: #4b5563;
                text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;
            }

            /* ── Section Title ── */
            .section-title {
                display: flex; align-items: center; gap: 10px;
                font-weight: 700; color: #111827; font-size: 16px;
                margin-bottom: 24px; padding-bottom: 12px;
                border-bottom: 1px solid #DDE1E8;
            }

            /* ── Kategori Radio ── */
            .kategori-option {
                border: 2px solid #f3f4f6; border-radius: 10px;
                background: #f9fafb; padding: 14px 16px; cursor: pointer;
                transition: all .2s;
            }
            .kategori-option:has(:checked) {
                border-color: #293C79; background: #f0f2ff;
            }
            .kategori-option:hover { border-color: #c7d2fe; }
        </style>
    <?php $__env->stopPush(); ?>

    <?php
        $jalur = request()->query('jalur', old('jalur_query'));
        if (!in_array($jalur, ['reguler', 'konfidensial'])) {
            // Redirect ke halaman pilihan jalur jika tidak ada param valid
        }
        $isAnonim = $jalur === 'konfidensial' ? '1' : '0';
    ?>

    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title">
            <h4>Buat Pengaduan</h4>
            <p>Isi form di bawah ini dengan detail yang jelas dan valid.</p>
        </div>
        <a href="<?php echo e(route('manajemenmahasiswa.pengaduan.jalur')); ?>" class="btn-back">
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
<?php endif; ?> Kembali
        </a>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($jalur === 'konfidensial'): ?>
        <div class="d-flex align-items-center gap-3 mb-4 p-3 px-4 rounded-3" style="background: #f8fafc; border: 1.5px solid #e2e8f0;">
            <div style="width: 36px; height: 36px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #64748b;">
                <?php if (isset($component)) { $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.icon','data' => ['name' => 'shield-02','size' => '20']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shield-02','size' => '20']); ?>
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
                <div class="fw-bold text-dark" style="font-size: 14px;">Jalur Konfidensial</div>
                <div class="text-muted" style="font-size: 12px;">Identitas Anda tidak akan ditampilkan di sistem. <a href="<?php echo e(route('manajemenmahasiswa.pengaduan.jalur')); ?>" style="color: #6b7280;">Ganti jalur</a></div>
            </div>
        </div>
    <?php else: ?>
        <div class="d-flex align-items-center gap-3 mb-4 p-3 px-4 rounded-3" style="background: #f5f5ff; border: 1.5px solid #c7d2fe;">
            <div style="width: 36px; height: 36px; border-radius: 10px; background: #eef2ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #4f46e5;">
                <?php if (isset($component)) { $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.icon','data' => ['name' => 'user-circle','size' => '20']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'user-circle','size' => '20']); ?>
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
                <div class="fw-bold text-dark" style="font-size: 14px;">Jalur Reguler</div>
                <div class="text-muted" style="font-size: 12px;">Identitas Anda terlihat oleh Admin. <a href="<?php echo e(route('manajemenmahasiswa.pengaduan.jalur')); ?>" style="color: #6b7280;">Ganti jalur</a></div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="alert alert-danger border-0" style="background-color: #fee2e2; color: #dc2626; border-radius: 12px;">
            <div class="fw-bold mb-2 d-flex align-items-center gap-2">
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
<?php endif; ?> Terdapat kesalahan pada input:
            </div>
            <ul class="mb-0 fw-medium" style="font-size: 14px;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <li><?php echo e($error); ?></li>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </ul>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <form method="POST" action="<?php echo e(route('manajemenmahasiswa.pengaduan.confirm')); ?>" class="form-card">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="is_anonim" value="<?php echo e($isAnonim); ?>">
        <input type="hidden" name="jalur_query" value="<?php echo e($jalur); ?>">

        <div class="mb-4">
            <label class="form-label-custom d-block">Kategori Pengaduan <span class="text-danger">*</span></label>
            <div class="d-flex flex-column gap-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $kategoriList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <label class="kategori-option d-flex align-items-start gap-3">
                        <input class="form-check-input mt-1" type="radio" name="kategori"
                            value="<?php echo e($value); ?>" <?php echo e(old('kategori') === $value ? 'checked' : ''); ?>

                            <?php echo e($loop->first ? 'required' : ''); ?>

                            style="width: 18px; height: 18px; cursor: pointer; flex-shrink: 0;">
                        <span style="min-width: 0;">
                            <span class="fw-bold text-dark d-block" style="font-size: 14px;"><?php echo e($meta['label']); ?></span>
                            <span class="text-muted d-block" style="font-size: 12px; line-height: 1.4;">Contoh: <?php echo e($meta['example']); ?></span>
                        </span>
                    </label>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <label class="form-label-custom d-block">Nama Mahasiswa</label>
                <input type="text" class="form-control form-control-custom" value="<?php echo e(auth()->user()->name ?? '-'); ?>" disabled>
                <div class="form-text mt-2 fw-medium" style="color: #9ca3af; font-size: 13px;">
                    Identitas tersimpan di database. Jika memilih jalur <strong>Konfidensial</strong>, identitas tidak ditampilkan di sistem.
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label-custom d-block">Angkatan <span class="text-muted fw-normal text-lowercase">(Opsional)</span></label>
                <input type="text" class="form-control form-control-custom" name="template[angkatan]"
                    value="<?php echo e(old('template.angkatan')); ?>" placeholder="Contoh: 2022">
            </div>
        </div>

        <div class="mt-5">
            <h6 class="section-title">
                <?php if (isset($component)) { $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.icon','data' => ['name' => 'file-01','size' => '18']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'file-01','size' => '18']); ?>
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
                Detail Pengaduan
            </h6>

            <div class="mb-4">
                <label class="form-label-custom d-block">Judul <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-custom" name="template[judul]" value="<?php echo e(old('template.judul')); ?>"
                    placeholder="Contoh: AC Ruang 3.12 tidak berfungsi" required>
            </div>

            <div class="mb-4">
                <label class="form-label-custom d-block">Hal Aduan <span class="text-danger">*</span></label>
                <textarea class="form-control form-control-custom" name="template[hal_aduan]" rows="3"
                    placeholder="Tuliskan ringkas inti aduan Anda…" required><?php echo e(old('template.hal_aduan')); ?></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label-custom d-block">Kronologi / Isi Pengaduan <span class="text-danger">*</span></label>
                <textarea class="form-control form-control-custom" name="template[kronologi]" rows="6"
                    placeholder="Ceritakan dengan jelas masalah yang Anda hadapi…"
                    required><?php echo e(old('template.kronologi')); ?></textarea>
                <div class="form-text mt-2 fw-medium" style="color: #9ca3af; font-size: 13px;">Minimal 20 karakter untuk memberikan konteks yang jelas.</div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="form-label-custom d-block">Lokasi Kejadian <span class="text-muted fw-normal text-lowercase">(Opsional)</span></label>
                    <input type="text" class="form-control form-control-custom" name="template[lokasi]" value="<?php echo e(old('template.lokasi')); ?>"
                        placeholder="Contoh: Lab Komputer">
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom d-block">Waktu Kejadian <span class="text-muted fw-normal text-lowercase">(Opsional)</span></label>
                    <input type="datetime-local" class="form-control form-control-custom" name="template[waktu_kejadian]"
                        value="<?php echo e(old('template.waktu_kejadian') ?? old('template.tanggal_kejadian')); ?>">
                </div>
            </div>

            <div class="row g-4 mt-0">
                <div class="col-md-4">
                    <label class="form-label-custom d-block">Mata Kuliah <span class="text-muted fw-normal text-lowercase">(Opsional)</span></label>
                    <input type="text" class="form-control form-control-custom" name="template[mata_kuliah]"
                        value="<?php echo e(old('template.mata_kuliah')); ?>" placeholder="Contoh: Basis Data">
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom d-block">Dosen Terkait <span class="text-muted fw-normal text-lowercase">(Opsional)</span></label>
                    <select class="form-select form-control-custom" name="template[nama_dosen]">
                        <option value="" <?php echo e(old('template.nama_dosen') ? '' : 'selected'); ?>>Pilih dosen…</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ($dosenList ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $namaDosen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($namaDosen); ?>" <?php echo e(old('template.nama_dosen') === $namaDosen ? 'selected' : ''); ?>>
                                <?php echo e($namaDosen); ?>

                            </option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom d-block">Tendik Terkait <span class="text-muted fw-normal text-lowercase">(Opsional)</span></label>
                    <input type="text" class="form-control form-control-custom" name="template[nama_tendik]"
                        value="<?php echo e(old('template.nama_tendik')); ?>" placeholder="Contoh: Bu Siti">
                </div>
            </div>

            <div class="row g-4 mt-0 mb-1 pt-4">
                <div class="col-md-6">
                    <label class="form-label-custom d-block">Seberapa Sering Terjadi <span class="text-muted fw-normal text-lowercase">(Opsional)</span></label>
                    <select class="form-select form-control-custom" name="template[frekuensi]">
                        <option value="" <?php echo e(old('template.frekuensi') ? '' : 'selected'); ?>>Pilih frekuensi…</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ($frekuensiList ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($value); ?>" <?php echo e(old('template.frekuensi') === $value ? 'selected' : ''); ?>>
                                <?php echo e($label); ?>

                            </option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom d-block">Link Bukti Dukung <span class="text-muted fw-normal text-lowercase">(Opsional)</span></label>
                    <input type="url" class="form-control form-control-custom" name="template[link_bukti]"
                        value="<?php echo e(old('template.link_bukti')); ?>"
                        placeholder="Contoh: https://drive.google.com/...">
                    <div class="form-text mt-2 fw-medium" style="color: #9ca3af; font-size: 13px;">
                        Bukti berupa screenshot/foto/dokumen, simpan di drive yang bisa diakses.
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mt-5 pt-4" style="border-top: 1px solid #f3f4f6;">
            <a href="<?php echo e(route('manajemenmahasiswa.pengaduan.index')); ?>" class="btn-back text-decoration-none">Batal</a>
            <button type="submit" class="btn-post">Lanjut Konfirmasi</button>
        </div>
    </form>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\ManajemenMahasiswa\resources\views\pengaduan\create.blade.php ENDPATH**/ ?>