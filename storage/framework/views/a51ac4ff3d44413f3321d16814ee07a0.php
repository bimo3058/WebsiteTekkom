
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


    <div style="min-height:100vh; background:var(--c-bg); font-family:var(--font-sans);">
        <div style="max-width:100%; padding:24px 24px 56px;">

            
            <nav style="display:flex; align-items:center; gap:6px; font-size:11px; color:var(--c-fg-muted); margin-bottom:16px;">
                <a href="<?php echo e(route('superadmin.dashboard')); ?>" style="color:var(--c-fg-muted); text-decoration:none;" onmouseover="this.style.color='var(--c-fg)'" onmouseout="this.style.color='var(--c-fg-muted)'">Dashboard</a>
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                <a href="<?php echo e(route('superadmin.permissions')); ?>" style="color:var(--c-fg-muted); text-decoration:none;" onmouseover="this.style.color='var(--c-fg)'" onmouseout="this.style.color='var(--c-fg-muted)'">Access Control</a>
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
                <span style="color:var(--c-fg); font-weight:500;"><?php echo e($category); ?></span>
            </nav>

            
            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:24px; flex-wrap:wrap;">
                <div>
                    <h1 style="font-size:20px; font-weight:700; color:var(--c-fg); letter-spacing:-0.01em; line-height:1.2;">
                        <?php echo e($category); ?>

                        <span style="font-size:13px; font-weight:500; color:var(--c-fg-muted); margin-left:6px;">
                            (<?php echo e($users->total()); ?> users)
                        </span>
                    </h1>
                    <p style="font-size:12px; color:var(--c-fg-muted); margin-top:3px;">Menampilkan semua pengguna dalam grup ini</p>
                </div>

                <form action="<?php echo e(url()->current()); ?>" method="GET" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">

                    
                    <div x-data="{
                            open: false,
                            selected: '<?php echo e(request('per_page') ?: (session('cat_per_page') ?? '10')); ?>',
                            options: ['10','25','50','100'],
                        }"
                         style="position:relative; width:110px;">
                        <input type="hidden" name="per_page" :value="selected">
                        <button type="button" @click="open = !open" @click.away="open = false"
                                style="width:100%; display:flex; align-items:center; justify-content:space-between; padding:7px 10px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-family:inherit; color:var(--c-fg-sec); cursor:pointer; transition:border-color .15s;"
                                onmouseover="this.style.borderColor='var(--c-border-strong)'" onmouseout="this.style.borderColor='var(--c-border)'">
                            <span x-text="selected + ' baris'" style="font-weight:500;"></span>
                            <svg :style="open ? 'transform:rotate(180deg)' : ''" width="12" height="12" fill="none" stroke="var(--c-fg-placeholder)" viewBox="0 0 24 24" stroke-width="2" style="transition:transform .2s; flex-shrink:0;"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div x-show="open" x-transition.opacity.duration.150ms
                             style="display:none; position:absolute; top:calc(100% + 4px); left:0; width:100%; background:#fff; border:1px solid var(--c-border); border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,.08); z-index:50; overflow:hidden; padding:4px 0;">
                            <template x-for="opt in options" :key="opt">
                                <button type="button" @click="selected = opt; open = false"
                                        :style="selected == opt ? 'color:var(--c-primary); background:rgba(11,38,110,0.04); font-weight:600;' : 'color:var(--c-fg-sec);'"
                                        style="width:100%; text-align:left; padding:6px 12px; font-size:12px; font-family:inherit; background:none; border:none; cursor:pointer; transition:background .12s;"
                                        onmouseover="this.style.background='var(--c-bg)'" onmouseout="if(this.getAttribute('data-active')!='1') this.style.background='none'">
                                    <span x-text="opt + ' baris'"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($category === 'Admins'): ?>
                    <div x-data="{
                            open: false,
                            selected: '<?php echo e(request('role', 'all')); ?>',
                            roles: [
                                { name: 'all', label: 'Semua Role' },
                                { name: 'superadmin', label: 'Superadmin' },
                                { name: 'admin_banksoal', label: 'Admin Bank Soal' },
                                { name: 'admin_capstone', label: 'Admin Capstone' },
                                { name: 'admin_eoffice', label: 'Admin E-Office' },
                                { name: 'admin_kemahasiswaan', label: 'Admin Kemahasiswaan' },
                            ],
                            get currentLabel() { return this.roles.find(r => r.name === this.selected)?.label || 'Semua Role'; }
                        }"
                         style="position:relative; width:160px;">
                        <input type="hidden" name="role" :value="selected">
                        <button type="button" @click="open = !open" @click.away="open = false"
                                style="width:100%; display:flex; align-items:center; justify-content:space-between; padding:7px 10px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-family:inherit; color:var(--c-fg-sec); cursor:pointer; transition:border-color .15s;"
                                onmouseover="this.style.borderColor='var(--c-border-strong)'" onmouseout="this.style.borderColor='var(--c-border)'">
                            <span x-text="currentLabel" style="font-weight:500; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"></span>
                            <svg :style="open ? 'transform:rotate(180deg)' : ''" width="12" height="12" fill="none" stroke="var(--c-fg-placeholder)" viewBox="0 0 24 24" stroke-width="2" style="transition:transform .2s; flex-shrink:0; margin-left:4px;"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div x-show="open" x-transition.opacity.duration.150ms
                             style="display:none; position:absolute; top:calc(100% + 4px); left:0; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid var(--c-border); border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,.08); z-index:50; padding:4px 0;">
                            <template x-for="r in roles" :key="r.name">
                                <button type="button" @click="selected = r.name; open = false"
                                        :style="selected === r.name ? 'color:var(--c-primary); background:rgba(11,38,110,0.04); font-weight:600;' : 'color:var(--c-fg-sec);'"
                                        style="width:100%; text-align:left; padding:6px 12px; font-size:12px; font-family:inherit; background:none; border:none; cursor:pointer; transition:background .12s;"
                                        onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='none'">
                                    <span x-text="r.label"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <div style="position:relative;">
                        <svg width="14" height="14" fill="none" stroke="var(--c-fg-placeholder)" viewBox="0 0 24 24" stroke-width="2" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); pointer-events:none;">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama atau email..."
                               style="padding:7px 12px 7px 32px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-family:inherit; color:var(--c-fg); outline:none; width:220px; transition:border-color .15s, box-shadow .15s;"
                               onfocus="this.style.borderColor='var(--c-primary)'; this.style.boxShadow='0 0 0 3px var(--c-primary-subtle)'"
                               onblur="this.style.borderColor='var(--c-border)'; this.style.boxShadow='none'">
                    </div>

                    
                    <button type="submit"
                            style="padding:7px 14px; background:var(--c-primary); border:none; border-radius:8px; color:#fff; font-size:12px; font-weight:600; cursor:pointer; font-family:inherit; transition:background .15s;"
                            onmouseover="this.style.background='var(--c-primary-hover)'" onmouseout="this.style.background='var(--c-primary)'">
                        Filter
                    </button>

                    
                    <a href="<?php echo e(route('superadmin.permissions')); ?>"
                       style="display:inline-flex; align-items:center; gap:5px; padding:7px 13px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-weight:600; color:var(--c-fg-sec); text-decoration:none; transition:background .15s;"
                       onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M19 12H5M5 12l7 7M5 12l7-7"/></svg>
                        Kembali
                    </a>
                </form>
            </div>

            
            <div style="display:flex; flex-direction:column; gap:6px;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php echo $__env->make('superadmin.permission._user_card', ['user' => $user, 'categoryKey' => strtolower(str_replace(' ', '_', $category))], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div style="background:#fff; border:1px dashed var(--c-border); border-radius:12px; padding:48px; text-align:center;">
                        <p style="font-size:12px; color:var(--c-fg-placeholder); text-transform:uppercase; letter-spacing:0.08em; font-weight:500;">
                            User tidak ditemukan dalam kategori ini
                        </p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($users->hasPages()): ?>
            <div style="margin-top:24px;">
                <?php echo e($users->appends(request()->query())->links()); ?>

            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        </div>
    </div>

    <?php echo $__env->make('superadmin.permission._scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\resources\views\superadmin\permission\category.blade.php ENDPATH**/ ?>