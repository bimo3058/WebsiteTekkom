


<?php
    $cardKey      = $categoryKey . '_' . $user->id;
    $userPermCount = $user->getAllPermissions()->count();
    $userModCount  = $user->getAllPermissions()->groupBy(fn($p) => explode('.', $p->name)[0])->count();
    $isActive      = $user->is_active ?? true;

    $allModules = \App\Models\Permission::all()
        ->groupBy(fn($p) => explode('.', $p->name)[0])
        ->keys();

    $moduleIcons = [
        'SIBASO'   => ['bg' => '#FEF3C7', 'color' => '#D97706'],
        'SICATA'   => ['bg' => '#DBEAFE', 'color' => '#3B82F6'],
        'SIMENMA'  => ['bg' => '#D1FAE5', 'color' => '#10B981'],
        'SIPERKOM' => ['bg' => '#FCE7F3', 'color' => '#EC4899'],
    ];
    $defaultIcon = ['bg' => '#EDE9FE', 'color' => '#8B5CF6'];
?>

<div class="user-card bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden" data-user-id="<?php echo e($user->id); ?>">

    
    <div class="flex items-center gap-4 px-5 py-4 cursor-pointer select-none"
         onclick="toggleCard('<?php echo e($cardKey); ?>')">

        
        <?php if (isset($component)) { $__componentOriginal2252ef3298868bc9de4c534a2a83a2a2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2252ef3298868bc9de4c534a2a83a2a2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.user-avatar','data' => ['user' => $user,'size' => 'lg','suspended' => !$isActive]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.user-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user),'size' => 'lg','suspended' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(!$isActive)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2252ef3298868bc9de4c534a2a83a2a2)): ?>
<?php $attributes = $__attributesOriginal2252ef3298868bc9de4c534a2a83a2a2; ?>
<?php unset($__attributesOriginal2252ef3298868bc9de4c534a2a83a2a2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2252ef3298868bc9de4c534a2a83a2a2)): ?>
<?php $component = $__componentOriginal2252ef3298868bc9de4c534a2a83a2a2; ?>
<?php unset($__componentOriginal2252ef3298868bc9de4c534a2a83a2a2); ?>
<?php endif; ?>

        
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-slate-800 truncate"><?php echo e($user->name); ?></p>
            <p class="text-xs text-slate-400 font-medium truncate"><?php echo e($user->email); ?></p>
        </div>

        
        <div class="flex items-center gap-1.5 flex-wrap">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $user->roles->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal018daf1083169982f7dcfc2ce8b4f9aa = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal018daf1083169982f7dcfc2ce8b4f9aa = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.role-badge','data' => ['role' => $role->name,'size' => 'xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.role-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['role' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($role->name),'size' => 'xs']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal018daf1083169982f7dcfc2ce8b4f9aa)): ?>
<?php $attributes = $__attributesOriginal018daf1083169982f7dcfc2ce8b4f9aa; ?>
<?php unset($__attributesOriginal018daf1083169982f7dcfc2ce8b4f9aa); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal018daf1083169982f7dcfc2ce8b4f9aa)): ?>
<?php $component = $__componentOriginal018daf1083169982f7dcfc2ce8b4f9aa; ?>
<?php unset($__componentOriginal018daf1083169982f7dcfc2ce8b4f9aa); ?>
<?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        
        <div class="hidden md:flex items-center gap-4 text-xs text-slate-500 font-semibold">
            <span><?php echo e($userModCount); ?> Modul</span>
            <span class="text-slate-300">|</span>
            <span><?php echo e($userPermCount); ?> Permissions</span>
        </div>

        
        <?php if (isset($component)) { $__componentOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.status-badge','data' => ['status' => $isActive ? 'active' : 'suspended']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isActive ? 'active' : 'suspended')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8)): ?>
<?php $attributes = $__attributesOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8; ?>
<?php unset($__attributesOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8)): ?>
<?php $component = $__componentOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8; ?>
<?php unset($__componentOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8); ?>
<?php endif; ?>

        
        <svg class="card-chevron-<?php echo e($cardKey); ?> w-4 h-4 text-slate-400 shrink-0 transition-transform duration-200"
             fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path d="M6 9l6 6 6-6"/>
        </svg>
    </div>

    
    <div id="card-body-<?php echo e($cardKey); ?>" class="hidden border-t border-slate-100">
        <form id="form-<?php echo e($cardKey); ?>" method="POST" action="<?php echo e(route('superadmin.permissions.update', $user->id)); ?>">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

            <div class="p-5">
                
                <div class="mb-6">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Roles</p>
                    <div class="flex flex-wrap gap-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = \App\Models\Role::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php $hasRole = $user->roles->contains('name', $role->name); ?>
                            <label class="flex items-center gap-2 px-3 py-2 border rounded-xl cursor-pointer transition-all select-none dot-indicator-wrap"
                                   style="<?php echo e($hasRole ? 'border-color: var(--c-primary); background: rgba(11,38,110,0.06); color: var(--c-primary)' : 'border-color: var(--c-border); background: #fff; color: var(--c-fg-muted)'); ?>">
                                <input type="checkbox"
                                       name="roles[]"
                                       value="<?php echo e($role->name); ?>"
                                       class="role-checkbox hidden"
                                       data-role-name="<?php echo e($role->name); ?>"
                                       data-is-academic="<?php echo e(in_array($role->name, ['superadmin', 'admin']) ? '1' : '0'); ?>"
                                       <?php echo e($hasRole ? 'checked' : ''); ?>>
                                <span class="dot-indicator w-2 h-2 rounded-full shrink-0" style="background: <?php echo e($hasRole ? 'var(--c-primary)' : 'var(--c-border)'); ?>"></span>
                                <span class="text-xs font-bold"><?php echo e(Str::title(str_replace('_', ' ', $role->name))); ?></span>
                            </label>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>

                
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Module Permissions</p>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $allModules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $mkey = strtoupper($module);
                                $ico  = $moduleIcons[$mkey] ?? $defaultIcon;
                                $modulePerms = \App\Models\Permission::where('name', 'like', $module . '.%')->get();
                            ?>
                            <div class="module-box border border-slate-200 rounded-xl p-3 transition-all"
                                 data-all-allowed-roles="<?php echo e(json_encode($modulePerms->pluck('name'))); ?>">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0"
                                             style="background:<?php echo e($ico['bg']); ?>">
                                            <div class="w-3 h-3 rounded-sm" style="background:<?php echo e($ico['color']); ?>"></div>
                                        </div>
                                        <span class="text-[10px] font-black text-slate-700 uppercase"><?php echo e($mkey); ?></span>
                                    </div>
                                    <label class="cursor-pointer">
                                        <input type="checkbox"
                                               class="module-select-all w-3 h-3 accent-slate-700"
                                               data-module-target="<?php echo e($cardKey); ?>_<?php echo e($module); ?>">
                                    </label>
                                </div>

                                <div class="flex flex-col gap-1">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $modulePerms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <?php
                                            $hasPerm   = $user->hasPermissionTo($perm->name);
                                            $isView    = str_contains(strtolower($perm->name), 'view') || str_contains(strtolower($perm->name), 'read') || str_contains(strtolower($perm->name), 'index');
                                            $permLabel = Str::title(explode('.', $perm->name)[1] ?? $perm->name);
                                        ?>
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="checkbox"
                                                   name="permissions[]"
                                                   value="<?php echo e($perm->name); ?>"
                                                   class="perm-checkbox w-3.5 h-3.5 accent-slate-700 rounded"
                                                   data-module-key="<?php echo e($cardKey); ?>_<?php echo e($module); ?>"
                                                   data-perm="<?php echo e($perm->name); ?>"
                                                   data-is-view="<?php echo e($isView ? '1' : '0'); ?>"
                                                   <?php echo e($hasPerm ? 'checked' : ''); ?>>
                                            <span class="text-[11px] text-slate-600 font-medium group-hover:text-slate-900 transition-colors"><?php echo e($permLabel); ?></span>
                                        </label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div class="flex items-center justify-end gap-3 px-5 py-3 bg-slate-50 border-t border-slate-100">
                <button type="button" onclick="toggleCard('<?php echo e($cardKey); ?>')"
                        class="px-4 py-2 text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-100 transition-colors">
                    Tutup
                </button>
                <button type="submit"
                        class="px-5 py-2 text-xs font-bold text-white bg-[#1E293B] rounded-xl hover:bg-slate-700 transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div><?php /**PATH C:\WebsiteTekkom - Copy\resources\views\superadmin\permission\_user_card.blade.php ENDPATH**/ ?>