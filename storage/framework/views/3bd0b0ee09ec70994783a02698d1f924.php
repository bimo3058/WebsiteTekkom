
<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php if (isset($component)) { $__componentOriginal2880b66d47486b4bfeaf519598a469d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2880b66d47486b4bfeaf519598a469d6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sidebar','data' => ['user' => auth()->user()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(auth()->user())]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


        <?php
            $user = auth()->user();
            $namaDepan = explode(' ', $user->name)[0];
            $nim = $user->student?->student_number ?? '';
            $isDefaultPw = $user->password &&
                \Illuminate\Support\Facades\Hash::check($namaDepan . $nim, $user->password);
        ?>

        <style>
            .sitkom-content {
                padding: 0 !important;
                display: flex;
                flex-direction: column;
                flex: 1;
                overflow: hidden;
            }

            .settings-wrap {
                display: flex;
                flex-direction: column;
                height: calc(100vh - 60px);
                padding: 10px;
                box-sizing: border-box;
                font-family: 'Inter Tight', sans-serif;
            }

            .settings-box {
                display: flex;
                flex-direction: column;
                flex: 1;
                min-height: 0;
                background: #fff;
                border: 1px solid var(--c-border);
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
                overflow: hidden;
                width: 100%;
                box-sizing: border-box;
            }

            .settings-box-header {
                background: #fff;
                border-bottom: 1px solid var(--c-border);
                flex-shrink: 0;
                width: 100%;
                box-sizing: border-box;
                padding: 14px 24px;
            }

            .settings-box-body {
                flex: 1;
                overflow-y: auto;
                display: flex;
                min-height: 0;
            }

            /* ── Mobile: scroll natively ── */
            @media (max-width: 767px) {
                .sitkom-content {
                    padding: 8px 8px 80px !important;
                    display: block !important;
                    overflow: visible !important;
                }
                .settings-wrap {
                    height: auto !important;
                    min-height: 0 !important;
                    padding: 0;
                }
                .settings-box {
                    flex: none !important;
                    min-height: 0 !important;
                    overflow: visible !important;
                    border-radius: 10px;
                }
                .settings-box-header {
                    padding: 10px 14px;
                    position: sticky;
                    top: 52px;
                    z-index: 10;
                }
                .settings-box-body {
                    overflow-y: visible !important;
                    flex-direction: column !important;
                    display: block !important;
                }
            }

            .settings-nav {
                width: 200px;
                flex-shrink: 0;
                border-right: 1px solid var(--c-border);
                padding: 16px 8px;
                display: flex;
                flex-direction: column;
                gap: 2px;
                background: #fff;
            }

            .settings-nav-btn {
                display: flex;
                align-items: center;
                gap: 10px;
                width: 100%;
                padding: 9px 12px;
                border-radius: 10px;
                font-size: 13px;
                font-weight: 500;
                color: #64748b;
                background: transparent;
                border: none;
                cursor: pointer;
                text-align: left;
                transition: background .15s, color .15s;
            }

            .settings-nav-btn:hover {
                background: #f8fafc;
                color: #1e293b;
            }

            .settings-nav-btn.active {
                background: #f1f5f9;
                color: #0f172a;
                font-weight: 600;
            }

            .settings-nav-btn .material-symbols-outlined {
                font-size: 18px;
            }

            .settings-content {
                flex: 1;
                overflow-y: auto;
                padding: 28px 32px;
                box-sizing: border-box;
            }

            .panel-title {
                font-size: 15px;
                font-weight: 700;
                color: #0f172a;
                margin-bottom: 2px;
            }

            .panel-sub {
                font-size: 13px;
                color: #64748b;
                margin-bottom: 24px;
            }

            .field-label {
                display: block;
                font-size: 13px;
                font-weight: 500;
                color: #374151;
                margin-bottom: 6px;
            }

            .field-input {
                width: 100%;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                padding: 9px 12px;
                font-size: 13px;
                color: #0f172a;
                background: #fff;
                outline: none;
                box-sizing: border-box;
                transition: border-color .15s, box-shadow .15s;
            }

            .field-input:focus {
                border-color: #5E53F4;
                box-shadow: 0 0 0 3px rgba(94, 83, 244, .08);
            }

            .field-input.readonly {
                background: #f8fafc;
                color: #94a3b8;
                cursor: not-allowed;
            }

            .toggle-track {
                width: 40px;
                height: 22px;
                border-radius: 99px;
                display: flex;
                align-items: center;
                padding: 2px;
            }

            .toggle-track.on {
                background: #1E1B4B;
            }

            .toggle-track.off {
                background: #cbd5e1;
            }

            .toggle-thumb {
                width: 18px;
                height: 18px;
                border-radius: 50%;
                background: #fff;
                box-shadow: 0 1px 3px rgba(0, 0, 0, .2);
                transition: transform .2s;
            }

            .toggle-track.on .toggle-thumb {
                transform: translateX(18px);
            }

            .str-bar {
                height: 4px;
                flex: 1;
                border-radius: 99px;
                background: #e2e8f0;
                transition: background .3s;
            }
        </style>

        <div class="settings-wrap" id="settings-root"
            x-data="{ tab: '<?php echo e(session('status') === 'password-updated' ? 'password' : 'general'); ?>' }">
            <div class="settings-box">

                
                <div class="settings-box-header">
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <span class="material-symbols-outlined"
                                style="font-size:20px;color:#5E53F4;">settings</span>
                            <span style="font-size:15px;font-weight:700;color:#0f172a;">Settings</span>
                        </div>
                        <button type="button" onclick="submitActiveForm()"
                            style="padding:7px 18px;background:#1E1B4B;color:#fff;font-size:13px;font-weight:600;border-radius:8px;border:none;cursor:pointer;transition:background .15s;"
                            onmouseover="this.style.background='#2d2a5e'" onmouseout="this.style.background='#1E1B4B'">
                            Save Changes
                        </button>
                    </div>
                </div>

                
                <div class="settings-box-body">

                    
                    <nav class="settings-nav">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                                        ['key' => 'general', 'label' => 'Umum', 'icon' => 'person'],
                                        ['key' => 'password', 'label' => 'Password', 'icon' => 'lock'],
                                        ['key' => 'tema', 'label' => 'Tema & Bahasa', 'icon' => 'palette'],
                                        ['key' => 'notifikasi', 'label' => 'Notifikasi', 'icon' => 'notifications'],
                                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <button type="button"
                                class="settings-nav-btn"
                                :class="tab==='<?php echo e($t['key']); ?>' ? 'active' : ''"
                                @click="tab='<?php echo e($t['key']); ?>'">
                                <span class="material-symbols-outlined"><?php echo e($t['icon']); ?></span>
                                <?php echo e($t['label']); ?>

                            </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </nav>
      
                      
            <div class="settings-content">

                          <div       x-show="tab==='general'" x-transition.opacity.duration.150ms>
                            <?php echo $__env->make('profile.partials.settings-panel-general', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
      
                  <div         x-show="tab==='password'" x-transition.opacity.duration.150ms>
                            <?php echo $__env->make('profile.partials.settings-panel-password', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                        <div         x-show="tab==='tema'" x-transition.opacity.duration.150ms>
                            <?php echo $__env->make('profile.partials.settings-panel-tema', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                                <div x-show="tab==='notifikasi'" x-transition.opacity.duration.150ms>
                            <?php echo $__env->make('profile.partials.settings-panel-notifikasi', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>

                    <div x-show="tab==='notifikasi'" x-transition.opacity.duration.150ms>
                        <?php echo $__env->make('profile.partials.settings-panel-notifikasi', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
        </div>
            </div>
            </div>
            
            <?php echo $__env->make('profile.partials.settings-modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            
            <script>
        function submitActiveForm() {
            // Target wrapper settings secara spesifik by ID (bukan querySelector('[x-data]')
            // yang bisa menangkap elemen Alpine lain seperti dropdown phone code)
            const el = document.getElementById('settings-root');
            const tab = (el && window.Alpine) ? Alpine.$data(el).tab : 'general';

            if (tab === 'general')  document.getElementById('form-general').submit();
            if (tab === 'password') document.getElementById('form-password').submit();
        }
</script>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2880b66d47486b4bfeaf519598a469d6)): ?>
<?php $attributes = $__attributesOriginal2880b66d47486b4bfeaf519598a469d6; ?>
<?php unset($__attributesOriginal2880b66d47486b4bfeaf519598a469d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2880b66d47486b4bfeaf519598a469d6)): ?>
<?php $component = $__componentOriginal2880b66d47486b4bfeaf519598a469d6; ?>
<?php unset($__componentOriginal2880b66d47486b4bfeaf519598a469d6); ?>
<?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\resources\views\profile\edit.blade.php ENDPATH**/ ?>