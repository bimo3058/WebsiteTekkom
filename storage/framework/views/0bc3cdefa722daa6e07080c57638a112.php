<?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => 'manajemenmahasiswa::layouts.mahasiswa'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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

            .jalur-wrapper {
                min-height: 70vh; display: flex; flex-direction: column;
                align-items: center; justify-content: center; padding: 32px 16px;
            }
            .jalur-heading { text-align: center; margin-bottom: 40px; }
            .jalur-heading h2 {
                font-size: 1.75rem; font-weight: 800; color: #1e1b4b;
                margin-bottom: 8px; letter-spacing: -.02em;
            }
            .jalur-heading p { color: #6b7280; font-size: 15px; font-weight: 500; }

            .jalur-grid {
                display: grid; grid-template-columns: 1fr 1fr;
                gap: 20px; max-width: 700px; width: 100%;
            }
            @media (max-width: 600px) { .jalur-grid { grid-template-columns: 1fr; } }

            .jalur-card {
                display: flex; flex-direction: column;
                background: #ffffff; border: 1px solid #DDE1E8;
                border-radius: 12px; padding: 32px 28px;
                text-decoration: none; color: inherit; transition: all 0.22s;
                cursor: pointer; gap: 12px; position: relative; overflow: hidden;
                box-shadow: 0 1px 3px rgba(22,22,43,.06), 0 1px 2px rgba(22,22,43,.04);
            }
            .jalur-card::before {
                content: ''; position: absolute;
                top: 0; left: 0; right: 0; height: 4px;
                opacity: 0; transition: opacity 0.22s;
            }
            .jalur-card.reguler::before {
                background: linear-gradient(135deg, #293C79, #6F7DA4);
            }
            .jalur-card.konfidensial::before {
                background: linear-gradient(135deg, #475569, #94a3b8);
            }
            .jalur-card:hover {
                border-color: transparent; transform: translateY(-4px);
                text-decoration: none; color: inherit;
            }
            .jalur-card.reguler:hover {
                box-shadow: 0 8px 24px rgba(41,60,121,.18);
            }
            .jalur-card.konfidensial:hover {
                box-shadow: 0 8px 24px rgba(22,22,43,.10);
            }
            .jalur-card:hover::before { opacity: 1; }

            .jalur-icon-wrap {
                width: 56px; height: 56px; border-radius: 12px;
                display: flex; align-items: center; justify-content: center;
            }
            .jalur-card.reguler .jalur-icon-wrap { background: #E7E8F0; color: #293C79; }
            .jalur-card.konfidensial .jalur-icon-wrap { background: #f1f5f9; color: #64748b; }

            .jalur-title { font-size: 20px; font-weight: 800; color: #111827; margin: 0; }
            .jalur-desc { font-size: 13px; color: #6b7280; line-height: 1.6; flex: 1; }
            .jalur-cta {
                margin-top: 8px; font-size: 13px; font-weight: 700;
                display: inline-flex; align-items: center; gap: 6px;
            }
            .jalur-card.reguler .jalur-cta { color: #293C79; }
            .jalur-card.konfidensial .jalur-cta { color: #475569; }

            .jalur-pill {
                font-size: 10px; font-weight: 700; padding: 3px 10px;
                border-radius: 20px; letter-spacing: 0.5px; align-self: flex-start;
            }
            .jalur-card.reguler .jalur-pill { background: #E7E8F0; color: #293C79; }
            .jalur-card.konfidensial .jalur-pill { background: #f1f5f9; color: #64748b; }

            .back-link {
                margin-top: 32px; font-size: 13px; font-weight: 600;
                color: #9ca3af; text-decoration: none;
                display: inline-flex; align-items: center; gap: 6px;
                transition: color 0.2s;
            }
            .back-link:hover { color: #374151; }
        </style>
    <?php $__env->stopPush(); ?>

    <div class="jalur-wrapper">
        <div class="jalur-heading">
            <h2>Buat Pengaduan</h2>
            <p>Pilih jalur pelaporan yang sesuai dengan kebutuhan Anda.</p>
        </div>

        <div class="jalur-grid">

            
            <a href="<?php echo e(route('manajemenmahasiswa.pengaduan.create', ['jalur' => 'reguler'])); ?>" class="jalur-card reguler">
                <span class="jalur-pill">STANDAR</span>
                <div class="jalur-icon-wrap">
                    <?php if (isset($component)) { $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.icon','data' => ['name' => 'user-circle','size' => '28']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'user-circle','size' => '28']); ?>
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
                <div class="jalur-title">Reguler</div>
                <div class="jalur-desc">
                    Nama dan identitas Anda terlihat oleh Admin untuk memudahkan tindak lanjut dan koordinasi langsung.
                </div>
                <div class="jalur-cta">
                    Pilih jalur ini
                    <?php if (isset($component)) { $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.icon','data' => ['name' => 'arrow-narrow-right','size' => '16']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-narrow-right','size' => '16']); ?>
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
            </a>

            
            <a href="<?php echo e(route('manajemenmahasiswa.pengaduan.anon.generate')); ?>" class="jalur-card konfidensial">
                <span class="jalur-pill">DILINDUNGI</span>
                <div class="jalur-icon-wrap">
                    <?php if (isset($component)) { $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.icon','data' => ['name' => 'shield-02','size' => '28']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'shield-02','size' => '28']); ?>
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
                <div class="jalur-title">Konfidensial</div>
                <div class="jalur-desc">
                    Identitas Anda tidak ditampilkan di sistem. Data tetap tersimpan secara internal untuk memastikan masalah dapat diselesaikan dengan tepat.
                </div>
                <div class="jalur-cta">
                    Pilih jalur ini
                    <?php if (isset($component)) { $__componentOriginal828fd8b499e764ca5ddb12621f8bfae1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal828fd8b499e764ca5ddb12621f8bfae1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.ui.icon','data' => ['name' => 'arrow-narrow-right','size' => '16']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-narrow-right','size' => '16']); ?>
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
            </a>

        </div>

        <a href="<?php echo e(route('manajemenmahasiswa.pengaduan.index')); ?>" class="back-link">
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
<?php endif; ?>
            Kembali ke Layanan Pengaduan
        </a>
    </div>

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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\ManajemenMahasiswa\resources\views\pengaduan\jalur.blade.php ENDPATH**/ ?>