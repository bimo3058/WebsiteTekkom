<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<title>Dashboard Admin — E-Office SIPERKOM</title>
<?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
<?php echo $__env->make('eoffice::dashboard._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php if (isset($component)) { $__componentOriginal798d336f107c986f84961f0c7e80911e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal798d336f107c986f84961f0c7e80911e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mobile-navigation-assets','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mobile-navigation-assets'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal798d336f107c986f84961f0c7e80911e)): ?>
<?php $attributes = $__attributesOriginal798d336f107c986f84961f0c7e80911e; ?>
<?php unset($__attributesOriginal798d336f107c986f84961f0c7e80911e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal798d336f107c986f84961f0c7e80911e)): ?>
<?php $component = $__componentOriginal798d336f107c986f84961f0c7e80911e; ?>
<?php unset($__componentOriginal798d336f107c986f84961f0c7e80911e); ?>
<?php endif; ?>
</head>
<body class="eo-dashboard h-full overflow-hidden bg-[#F6F8FA] text-[#0D0D12] antialiased" style="font-family:'Inter Tight',system-ui,sans-serif;">

<?php
    $user         = auth()->user();
    $name         = $user->name;
    $initials     = strtoupper(substr($name, 0, 1));
    $sp           = strpos($name, ' ');
    if ($sp !== false) $initials .= strtoupper(substr($name, $sp + 1, 1));
    $currentRoute = request()->route()?->getName() ?? '';

    // SVG path strings — dipakai sidebar & topbar
    $iDashboard = "M4.8787 8.90834L10.5858 3.54999C11.3669 2.81667 12.6332 2.81667 13.4142 3.54999L19.1213 8.90834M4.8787 8.90834C4.31629 9.43653 4.00002 10.1531 4.00002 10.9V18.1833C4.00002 19.7389 5.34317 21 7.00002 21H9V16C9 14.8954 9.89543 14 11 14H13C14.1046 14 15 14.8954 15 16V21H17C18.6569 21 20 19.7389 20 18.1833V10.9C20 10.153 19.684 9.43656 19.1213 8.90834M4.8787 8.90834L3.00031 10.6722M19.1213 8.90834L21 10.6722";
    $iPraktikum  = "M9 21V13H5C3.89543 13 3 13.8954 3 15V19C3 20.1046 3.89543 21 5 21H9ZM9 21H15M9 21V10C9 8.89543 9.89543 8 11 8H15V21M15 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H17C15.8954 3 15 3.89543 15 5V21Z";
    $iKP         = "M22 10V17C22 18.6569 20.6569 20 19 20H5C3.34315 20 2 18.6569 2 17V10M22 10C22 8.34315 20.6569 7 19 7H16M22 10L14.4368 12.917C13.6611 13.2617 12.8306 13.4341 12 13.4341M2 10C2 8.34315 3.34315 7 5 7H8M2 10L9.56317 12.917C10.3389 13.2617 11.1694 13.4341 12 13.4341M8 7V6C8 4.89543 8.89543 4 10 4H14C15.1046 4 16 4.89543 16 6V7M8 7H16M12 13.4341V12M12 13.4341V15";
    $iLogout     = "M13 8.73096V8.14189C13 6.5836 12.1925 5.24194 11.0707 4.93634L7.87068 4.06459C6.38558 3.66002 5 5.20723 5 7.27015V16.7298C5 18.7928 6.38558 20.34 7.87068 19.9354L11.0707 19.0637C12.1925 18.7581 13 17.4164 13 15.8581V15.269M11 11.9996H19M19 11.9996L16.5 9.27539M19 11.9996L16.5 14.7238";
?>

<div class="eo-dashboard-shell flex h-screen overflow-hidden" x-data="{ sidebarOpen: false, isMobile: window.innerWidth < 768 }"
     :class="{ 'eo-sidebar-open': sidebarOpen }"
     x-init="(() => { try { sidebarOpen = window.innerWidth >= 768 &amp;&amp; localStorage.getItem('eo_sb') !== '0'; } catch { sidebarOpen = window.innerWidth >= 768; } $watch('sidebarOpen', v => { if (window.innerWidth >= 768) { try { localStorage.setItem('eo_sb', v ? '1' : '0'); } catch {} } }); })()"
     @resize.window.debounce.150ms="if (isMobile !== (window.innerWidth < 768)) { isMobile = window.innerWidth < 768; try { sidebarOpen = !isMobile &amp;&amp; localStorage.getItem('eo_sb') !== '0'; } catch { sidebarOpen = !isMobile; } }"
     @keydown.escape.window="if (isMobile &amp;&amp; sidebarOpen) { sidebarOpen = false; $refs.eoMenuButton.focus(); }">
    <button type="button" class="eo-sidebar-backdrop" x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" aria-label="Tutup menu navigasi"></button>
    <?php echo $__env->make('eoffice::dashboard._sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <main class="eo-dashboard-main" :inert="isMobile &amp;&amp; sidebarOpen">

        <?php echo $__env->make('eoffice::dashboard._topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div class="eo-dashboard-wrap">
            <section class="eo-dashboard-box" aria-labelledby="eo-dashboard-title">
                <?php echo $__env->make('eoffice::dashboard._header', ['dashboardRole' => 'admin'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <div class="eo-dashboard-content">
                    <?php echo $__env->make('eoffice::dashboard._summary', ['dashboardRole' => 'admin'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php echo $__env->make('eoffice::dashboard._services', ['dashboardRole' => 'admin'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            
            <div class="eo-detail-grid">

                
                <div style="background: #fff; border: 1px solid var(--c-border, #DFE1E7); border-radius: 14px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.04); display: flex; flex-direction: column; flex: 2; min-width: 0;">
                    
                    
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border-bottom: 1px solid var(--c-border, #DFE1E7); gap: 10px; flex-wrap: wrap; flex-shrink: 0;">
                        <div>
                            <h2 style="font-size: 14px; font-weight: 700; color: var(--c-fg, #0D0D12); margin: 0;">Daftar Praktikum Aktif</h2>
                            <div style="font-size: 11px; color: var(--c-fg-muted, #666D80); margin-top: 2px;"><?php echo e($semesterLabel ?? 'Semester Genap 2025/2026'); ?></div>
                        </div>
                        <a href="<?php echo e(route('eoffice.manprak.admin.praktikum.index')); ?>"
                        class="mp-btn secondary sm" 
                        style="font-size: 12px; padding: 6px 12px; border-radius: 8px; text-decoration: none;">
                            Lihat Semua
                        </a>
                    </div>

                    
                    <div style="overflow-x: auto; flex: 1;">
                        <table style="width: 100%; border-collapse: collapse; min-width: 500px;">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--c-border, #DFE1E7); background: #FAFAFA;">
                                    <th style="padding: 11px 16px; text-align: left; font-size: 11px; font-weight: 600; color: var(--c-fg-muted, #666D80); white-space: nowrap; width: 90px;">Kode</th>
                                    <th style="padding: 11px 16px; text-align: left; font-size: 11px; font-weight: 600; color: var(--c-fg-muted, #666D80); white-space: nowrap;">Nama Praktikum</th>
                                    <th style="padding: 11px 16px; text-align: left; font-size: 11px; font-weight: 600; color: var(--c-fg-muted, #666D80); white-space: nowrap; width: 170px;">Dosen Pengampu</th>
                                    <th style="padding: 11px 16px; text-align: center; font-size: 11px; font-weight: 600; color: var(--c-fg-muted, #666D80); white-space: nowrap; width: 75px;">Peserta</th>
                                    <th style="padding: 11px 16px; text-align: left; font-size: 11px; font-weight: 600; color: var(--c-fg-muted, #666D80); white-space: nowrap; width: 90px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $praktikums ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <tr style="border-bottom: 1px solid #F3F4F6; transition: background .12s; cursor: pointer;"
                                    onmouseover="this.style.background='#FAFAFA'" 
                                    onmouseout="this.style.background='transparent'"
                                    onclick="window.location='<?php echo e(route('eoffice.manprak.admin.praktikum.show', $p->id)); ?>'">
                                    
                                    
                                    <td style="padding: 12px 16px; font-size: 12px; font-weight: 700; color: #0B266E; font-family: monospace; white-space: nowrap;">
                                        <?php echo e($p->kode ?? '—'); ?>

                                    </td>
                                    
                                    
                                    <td style="padding: 12px 16px;">
                                        <div style="font-size: 13px; font-weight: 600; color: var(--c-fg, #0D0D12); max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo e($p->nama ?? ''); ?>">
                                            <?php echo e($p->nama ?? '—'); ?>

                                        </div>
                                    </td>
                                    
                                    
                                    <td style="padding: 12px 16px;">
                                        <div style="font-size: 12px; color: var(--c-fg-muted, #666D80); max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo e($p->dosens->pluck('name')->join(', ') ?? '—'); ?>">
                                            <?php echo e($p->dosens->pluck('name')->join(', ') ?? '—'); ?>

                                        </div>
                                    </td>
                                    
                                    
                                    <td style="padding: 12px 16px; text-align: center; font-size: 13px; font-weight: 700; color: var(--c-fg, #0D0D12);">
                                        <?php echo e(($p->status ?? '') === 'aktif' ? ($p->daftar_praktikan_count ?? 0) : '—'); ?>

                                    </td>
                                    
                                    
                                    <td style="padding: 12px 16px; white-space: nowrap;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($p->status ?? '') === 'aktif'): ?>
                                            <span class="mp-badge success sm"><span class="dot"></span>Aktif</span>
                                        <?php else: ?>
                                            <span class="mp-badge neutral sm"><span class="dot"></span>Nonaktif</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr>
                                    <td colspan="5" style="padding: 40px; text-align: center;">
                                        <svg width="36" height="36" fill="none" stroke="var(--c-fg-placeholder, #A4ABB8)" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" style="margin: 0 auto 10px; display: block;">
                                            <rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>
                                        </svg>
                                        <p style="font-size: 12px; font-weight: 600; color: var(--c-fg-muted, #666D80); margin: 0;">Belum ada praktikum aktif.</p>
                                    </td>
                                </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                
                <div class="flex flex-col bg-white border border-[#DFE1E7] rounded-[14px] overflow-hidden shadow-[0_1px_2px_rgba(228,229,231,.24)] min-w-0 flex-1">
                    <div class="px-5 py-4 border-b border-[#DFE1E7] flex-shrink-0">
                        <div class="font-bold text-[15px] text-[#0D0D12]">Aktivitas Terbaru</div>
                    </div>
                    <div class="overflow-y-auto flex-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recentActivities ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $act): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $cm = ['blue'=>['#0B266E','rgba(11,38,110,0.08)'],'success'=>['#40C4AA','#DDF2EE'],'sky'=>['#106A97','#D1F0F9'],'warning'=>['#D39C3D','#F9ECCB'],'error'=>['#DF1C41','#FADAE1']];
                            [$dc,$db] = $cm[$act['type']??'blue'] ?? $cm['blue'];
                        ?>
                        <div class="flex gap-3 items-start px-5 py-[10px] border-b border-[#F8F9FB] last:border-0">
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg flex-shrink-0 mt-[1px]"
                                 style="background:<?php echo e($db); ?>;">
                                <div class="w-2 h-2 rounded-full" style="background:<?php echo e($dc); ?>;"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-[13px] font-medium text-[#353849] leading-[1.4]"><?php echo $act['text']; ?></div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($act['desc'])): ?><div class="text-[12px] text-[#666D80] mt-[1px] leading-[1.3]"><?php echo e($act['desc']); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div class="text-[11px] text-[#A4ABB8] mt-[2px]"><?php echo e($act['time']); ?></div>
                            </div>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="py-6 text-center text-[13px] text-[#666D80]">Belum ada aktivitas.</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

            </div>

                </div>
            </section>
        </div>
    </main>
</div>

    <?php if (isset($component)) { $__componentOriginal0d3b2b7a1a27f99acddd84d2b2599baa = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0d3b2b7a1a27f99acddd84d2b2599baa = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mobile-navigation','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mobile-navigation'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0d3b2b7a1a27f99acddd84d2b2599baa)): ?>
<?php $attributes = $__attributesOriginal0d3b2b7a1a27f99acddd84d2b2599baa; ?>
<?php unset($__attributesOriginal0d3b2b7a1a27f99acddd84d2b2599baa); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0d3b2b7a1a27f99acddd84d2b2599baa)): ?>
<?php $component = $__componentOriginal0d3b2b7a1a27f99acddd84d2b2599baa; ?>
<?php unset($__componentOriginal0d3b2b7a1a27f99acddd84d2b2599baa); ?>
<?php endif; ?>
</body>
</html>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\dashboard\admin.blade.php ENDPATH**/ ?>