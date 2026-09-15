<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Pembagian Modul ke Asisten']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Pembagian Modul ke Asisten']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


<?php
    /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\AsistenPraktikum[] $asistenList */
    /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\Modul[] $modulList */
    /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\ModulAsprak[] $distribusiList */
?>

<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Pembagian Modul ke Asisten Praktikum</h1>
            <span class="mp-badge" style="background:#E0E7FF;color:#6366F1;border-radius:999px;padding:3px 10px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:5px;"><span class="dot" style="background:#6366F1;"></span>Koordinator</span>
        </div>
        <p class="mp-page-sub">Tentukan asisten mana yang mengelola modul mana · <?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?></p>
    </div>
</div>

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Distribusi Asisten & Modul</span>
    <span class="sec-rule"></span>
</div>

<div class="flex gap-[14px] flex-1 min-h-0">

    
    <div class="mp-card flex-shrink-0" style="width:280px;">
        <div class="mp-card-header">
            <div>
                <span class="mp-card-title">Asisten Praktikum</span>
                <div style="font-size:12px;color:#666D80;margin-top:2px;"><?php echo e($asistenList->count()); ?> asisten praktikum terdaftar</div>
            </div>
        </div>
        <div class="overflow-y-auto flex-1">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $asistenList ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div style="padding:10px 20px;border-bottom:1px solid #DFE1E7;">
                <div class="flex items-center gap-[10px]">
                    <div class="mp-av green"><?php echo e(strtoupper(substr($a->user?->name ?? 'A', 0, 2))); ?></div>
                    <div class="min-w-0">
                        <div style="font-size:12px;font-weight:600;color:#0D0D12;" class="truncate"><?php echo e($a->user?->name ?? '—'); ?></div>
                        <div style="font-size:10px;color:#666D80;"><?php echo e($a->modulAsprak->count()); ?> modul</div>
                    </div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($a->modulAsprak->isNotEmpty()): ?>
                <div class="flex flex-wrap gap-1 mt-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $a->modulAsprak; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ma): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <span class="mp-badge sky sm"><?php echo e($ma->modul?->nama); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div style="padding:48px;text-align:center;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada asprak.</div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div class="flex flex-col gap-[14px] flex-1 min-w-0 overflow-y-auto">

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($praktikum) && $praktikum->is_active): ?>
        <div class="mp-card flex-shrink-0" style="padding:20px;">
            <div style="font-weight:700;font-size:14px;color:#0D0D12;margin-bottom:16px;">Assign Asisten Praktikum ke Modul</div>
            <form method="POST" action="<?php echo e(route('eoffice.manprak.koor.bagi-modul.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Pilih Asisten <span class="text-red-500">*</span></label>
                        <select name="asprak_id" required class="mp-input mp-select w-full">
                            <option value="">— Pilih Asisten Praktikum —</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $asistenList ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($a->id); ?>"><?php echo e($a->user?->name); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Pilih Modul <span class="text-red-500">*</span></label>
                        <?php
                $modulOptions = [];
                if(isset($modulList)) {
                    foreach($modulList as $m) {
                        $modulOptions[] = ['value' => (string)$m->id, 'label' => $m->judul];
                    }
                }
            ?>
            <?php if (isset($component)) { $__componentOriginalaa793a071b61f8aedcefc5af7e38176e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaa793a071b61f8aedcefc5af7e38176e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.select','data' => ['name' => 'modul_id','options' => $modulOptions,'selected' => (string)request('modul_id', (isset($modul) ? $modul?->id : '')),'placeholder' => 'Pilih Modul...','onChange' => '$event.target.form.submit()','minWidth' => '200px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'modul_id','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($modulOptions),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((string)request('modul_id', (isset($modul) ? $modul?->id : ''))),'placeholder' => 'Pilih Modul...','onChange' => '$event.target.form.submit()','minWidth' => '200px']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalaa793a071b61f8aedcefc5af7e38176e)): ?>
<?php $attributes = $__attributesOriginalaa793a071b61f8aedcefc5af7e38176e; ?>
<?php unset($__attributesOriginalaa793a071b61f8aedcefc5af7e38176e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalaa793a071b61f8aedcefc5af7e38176e)): ?>
<?php $component = $__componentOriginalaa793a071b61f8aedcefc5af7e38176e; ?>
<?php unset($__componentOriginalaa793a071b61f8aedcefc5af7e38176e); ?>
<?php endif; ?>
                    </div>
                </div>
                <button type="submit" class="mp-btn primary md">Assign Modul</button>
            </form>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="mp-card flex-1 min-h-0">
            <div class="mp-card-header" style="flex-shrink:0;">
                <span class="mp-card-title">Distribusi Saat Ini</span>
                <div class="right">
                    <span class="mp-badge neutral sm"><?php echo e(count($distribusiList ?? [])); ?> entri</span>
                </div>
            </div>
            <div style="flex-shrink:0;">
                <table class="mp-table" style="width:100%;">
                    <thead>
                        <tr style="background:#F9FAFB;">
                            <th class="mp-th text-left" style="padding:10px 16px;">Modul</th>
                            <th class="mp-th text-left" style="padding:10px 16px;width:200px;">Asisten Praktikum</th>
                            <th class="mp-th text-left" style="padding:10px 16px;width:80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $distribusiList ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                            <td style="padding:12px 16px;">
                                <div style="font-size:13px;font-weight:500;color:#0D0D12;"><?php echo e($d->modul?->nama ?? '—'); ?></div>
                            </td>
                            <td style="padding:12px 16px;">
                                <div class="flex items-center gap-2">
                                    <div class="mp-av green" style="width:24px;height:24px;font-size:9px;"><?php echo e(strtoupper(substr($d->asprak?->user?->name ?? 'A', 0, 2))); ?></div>
                                    <span style="font-size:12px;color:#353849;"><?php echo e($d->asprak?->user?->name ?? '—'); ?></span>
                                </div>
                            </td>
                            <td style="padding:12px 16px;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($praktikum) && $praktikum->is_active): ?>
                                <form method="POST" action="<?php echo e(route('eoffice.manprak.koor.bagi-modul.destroy', $d->id)); ?>"
                                      onsubmit="return confirm('Hapus distribusi ini?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="mp-btn destructive sm">Hapus</button>
                                </form>
                                <?php else: ?>
                                <span style="font-size:11px;color:#808897;">Read-only</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                        </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="3">
                                <div style="padding:48px;text-align:center;">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                                    <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada distribusi modul.</div>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc437526b4c56b06f9c16ab6722a84925)): ?>
<?php $attributes = $__attributesOriginalc437526b4c56b06f9c16ab6722a84925; ?>
<?php unset($__attributesOriginalc437526b4c56b06f9c16ab6722a84925); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc437526b4c56b06f9c16ab6722a84925)): ?>
<?php $component = $__componentOriginalc437526b4c56b06f9c16ab6722a84925; ?>
<?php unset($__componentOriginalc437526b4c56b06f9c16ab6722a84925); ?>
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\bagi-modul.blade.php ENDPATH**/ ?>