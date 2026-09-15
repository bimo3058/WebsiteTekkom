

<div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
    <span style="width:3px;height:14px;border-radius:2px;background:var(--c-primary);"></span>
    <span style="font-size:14px;font-weight:700;color:var(--c-fg);">Aktivitas Terkini</span>
</div>

<?php
    if (isset($new_registrations)) $new_registrations->loadMissing('roles');
    if (isset($recent_logs))       $recent_logs->loadMissing('user.roles');

    $online_users = \App\Models\User::where('is_online', \Illuminate\Support\Facades\DB::raw('true'))
        ->with('roles')->latest('last_login')->take(5)->get();
?>

<div class="dash-activity" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">

    
    <div style="background:#fff;border:1px solid var(--c-border);border-radius:14px;box-shadow:var(--shadow-card);overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-bottom:1px solid var(--c-border);">
            <div style="font-size:13px;font-weight:700;color:var(--c-fg);">Monitoring Online</div>
            <a href="<?php echo e(route('superadmin.users.online')); ?>" style="font-size:12px;font-weight:600;color:var(--c-primary);text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                Lihat Semua
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M20 12H4M20 12L14 6M20 12L14 18"/></svg>
            </a>
        </div>
        <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;min-width:380px;">
            <thead>
                <tr>
                    <th style="text-align:left;font-size:11px;font-weight:500;color:var(--c-fg-muted);padding:10px 18px;border-bottom:1px solid var(--c-border);background:#FBFBFC;">Name</th>
                    <th style="text-align:left;font-size:11px;font-weight:500;color:var(--c-fg-muted);padding:10px 18px;border-bottom:1px solid var(--c-border);background:#FBFBFC;">Role</th>
                    <th style="text-align:left;font-size:11px;font-weight:500;color:var(--c-fg-muted);padding:10px 18px;border-bottom:1px solid var(--c-border);background:#FBFBFC;">Last Online</th>
                </tr>
            </thead>
            <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $online_users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $onlineUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $role = $onlineUser->roles->first()->name ?? 'user';
                ?>
                <tr>
                    <td style="padding:12px 18px;border-bottom:1px solid var(--c-border);font-size:13px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <?php if (isset($component)) { $__componentOriginal2252ef3298868bc9de4c534a2a83a2a2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2252ef3298868bc9de4c534a2a83a2a2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.user-avatar','data' => ['user' => $onlineUser,'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.user-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($onlineUser),'size' => 'sm']); ?>
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
                            <div>
                                <div style="font-size:13px;font-weight:600;color:var(--c-fg);line-height:1.2;"><?php echo e($onlineUser->name); ?></div>
                                <div style="font-size:11px;color:var(--c-fg-muted);margin-top:2px;"><?php echo e($onlineUser->nim ?? $onlineUser->id); ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 18px;border-bottom:1px solid var(--c-border);">
                        <?php if (isset($component)) { $__componentOriginal018daf1083169982f7dcfc2ce8b4f9aa = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal018daf1083169982f7dcfc2ce8b4f9aa = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.role-badge','data' => ['role' => $role,'size' => 'xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.role-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['role' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($role),'size' => 'xs']); ?>
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
                    </td>
                    <td style="padding:12px 18px;border-bottom:1px solid var(--c-border);">
                        <?php if (isset($component)) { $__componentOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldf5a194c1ccdd1698e9a89f0cb5bf2c8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.status-badge','data' => ['status' => 'online']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => 'online']); ?>
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
                    </td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr><td colspan="3" style="padding:32px;text-align:center;color:var(--c-fg-muted);font-size:12px;">Tidak ada user online</td></tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>

    
    <div style="background:#fff;border:1px solid var(--c-border);border-radius:14px;box-shadow:var(--shadow-card);overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-bottom:1px solid var(--c-border);">
            <div style="font-size:13px;font-weight:700;color:var(--c-fg);">User Baru Terdaftar</div>
            <a href="<?php echo e(route('superadmin.users.index')); ?>" style="font-size:12px;font-weight:600;color:var(--c-primary);text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                Detail
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M20 12H4M20 12L14 6M20 12L14 18"/></svg>
            </a>
        </div>
        <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;min-width:380px;">
            <thead>
                <tr>
                    <th style="text-align:left;font-size:11px;font-weight:500;color:var(--c-fg-muted);padding:10px 18px;border-bottom:1px solid var(--c-border);background:#FBFBFC;">Name</th>
                    <th style="text-align:left;font-size:11px;font-weight:500;color:var(--c-fg-muted);padding:10px 18px;border-bottom:1px solid var(--c-border);background:#FBFBFC;">Role</th>
                    <th style="text-align:left;font-size:11px;font-weight:500;color:var(--c-fg-muted);padding:10px 18px;border-bottom:1px solid var(--c-border);background:#FBFBFC;">Tanggal Daftar</th>
                </tr>
            </thead>
            <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $new_registrations->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $newUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $role = $newUser->roles->first()->name ?? 'user';
                ?>
                <tr>
                    <td style="padding:12px 18px;border-bottom:1px solid var(--c-border);">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <?php if (isset($component)) { $__componentOriginal2252ef3298868bc9de4c534a2a83a2a2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2252ef3298868bc9de4c534a2a83a2a2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.user-avatar','data' => ['user' => $newUser,'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.user-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($newUser),'size' => 'sm']); ?>
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
                            <div>
                                <div style="font-size:13px;font-weight:600;color:var(--c-fg);line-height:1.2;"><?php echo e($newUser->name); ?></div>
                                <div style="font-size:11px;color:var(--c-fg-muted);margin-top:2px;"><?php echo e($newUser->nim ?? $newUser->id); ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 18px;border-bottom:1px solid var(--c-border);">
                        <?php if (isset($component)) { $__componentOriginal018daf1083169982f7dcfc2ce8b4f9aa = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal018daf1083169982f7dcfc2ce8b4f9aa = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.role-badge','data' => ['role' => $role,'size' => 'xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.role-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['role' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($role),'size' => 'xs']); ?>
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
                    </td>
                    <td style="padding:12px 18px;border-bottom:1px solid var(--c-border);font-size:12px;color:var(--c-fg-sec);">
                        <?php echo e($newUser->created_at->translatedFormat('F d, Y')); ?>

                    </td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr><td colspan="3" style="padding:32px;text-align:center;color:var(--c-fg-muted);font-size:12px;">Belum ada user baru</td></tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>

    
    <div style="grid-column:1/-1;background:#fff;border:1px solid var(--c-border);border-radius:14px;box-shadow:var(--shadow-card);overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-bottom:1px solid var(--c-border);">
            <div style="font-size:13px;font-weight:700;color:var(--c-fg);">Log Aktivitas Terbaru</div>
            <a href="<?php echo e(route('superadmin.audit-logs')); ?>" style="font-size:12px;font-weight:600;color:var(--c-primary);text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                Lihat Semua
                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M20 12H4M20 12L14 6M20 12L14 18"/></svg>
            </a>
        </div>
        <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;min-width:480px;">
            <thead>
                <tr>
                    <th style="text-align:left;font-size:11px;font-weight:500;color:var(--c-fg-muted);padding:10px 18px;border-bottom:1px solid var(--c-border);background:#FBFBFC;width:32%;">Name</th>
                    <th style="text-align:left;font-size:11px;font-weight:500;color:var(--c-fg-muted);padding:10px 18px;border-bottom:1px solid var(--c-border);background:#FBFBFC;width:18%;">Role</th>
                    <th style="text-align:left;font-size:11px;font-weight:500;color:var(--c-fg-muted);padding:10px 18px;border-bottom:1px solid var(--c-border);background:#FBFBFC;width:18%;">Log</th>
                    <th style="text-align:left;font-size:11px;font-weight:500;color:var(--c-fg-muted);padding:10px 18px;border-bottom:1px solid var(--c-border);background:#FBFBFC;">Value</th>
                </tr>
            </thead>
            <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recent_logs->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $role         = $log->user?->roles->first()->name ?? 'system';
                    $uid          = $log->user?->id ?? 0;
                    $name         = $log->user?->name ?? 'System';
                    $actionVariant = match(strtoupper($log->action)) {
                        'CREATE' => 'action-create',
                        'UPDATE' => 'action-update',
                        'DELETE' => 'action-delete',
                        'LOGIN'  => 'action-login',
                        'LOGOUT' => 'action-logout',
                        default  => 'action-default',
                    };
                ?>
                <tr>
                    <td style="padding:12px 18px;border-bottom:1px solid var(--c-border);">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <?php if (isset($component)) { $__componentOriginal2252ef3298868bc9de4c534a2a83a2a2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2252ef3298868bc9de4c534a2a83a2a2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.user-avatar','data' => ['user' => $log->user,'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.user-avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($log->user),'size' => 'sm']); ?>
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
                            <div>
                                <div style="font-size:13px;font-weight:600;color:var(--c-fg);line-height:1.2;"><?php echo e($name); ?></div>
                                <div style="font-size:11px;color:var(--c-fg-muted);margin-top:2px;"><?php echo e($log->user?->nim ?? $uid); ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 18px;border-bottom:1px solid var(--c-border);">
                        <?php if (isset($component)) { $__componentOriginal018daf1083169982f7dcfc2ce8b4f9aa = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal018daf1083169982f7dcfc2ce8b4f9aa = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.role-badge','data' => ['role' => $role,'size' => 'xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.role-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['role' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($role),'size' => 'xs']); ?>
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
                    </td>
                    <td style="padding:12px 18px;border-bottom:1px solid var(--c-border);">
                        <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $actionVariant,'size' => 'xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($actionVariant),'size' => 'xs']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>
<?php echo e($log->action); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                    </td>
                    <td style="padding:12px 18px;border-bottom:1px solid var(--c-border);">
                        <span style="display:inline-flex;align-items:center;gap:6px;font-size:12px;color:var(--c-fg-muted);">
                            <span style="width:5px;height:5px;border-radius:50%;background:var(--c-fg-placeholder);"></span>
                            <?php echo e($log->created_at->diffForHumans(null, true)); ?>

                        </span>
                    </td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr><td colspan="4" style="padding:32px;text-align:center;color:var(--c-fg-muted);font-size:12px;">Tidak ada log aktivitas</td></tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>

</div>

<style>
@media (max-width: 1024px) { .dash-activity { grid-template-columns: 1fr !important; } }
</style>
<?php /**PATH C:\WebsiteTekkom - Copy\resources\views/superadmin/dashboard/_activity.blade.php ENDPATH**/ ?>