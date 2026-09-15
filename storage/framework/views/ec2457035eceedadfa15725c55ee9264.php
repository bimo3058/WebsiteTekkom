<?php
    $pageTitle = ($praktikum?->nama ?? 'Praktikum') . ' / Pengumuman';
?>
<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => $pageTitle]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pageTitle)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikumList->isEmpty()): ?>
        <div class="mp-page-header">
            <div>
                <h1 class="mp-page-title">Pengumuman</h1>
            </div>
        </div>
        <div class="mp-alert warning flex-shrink-0">Anda belum terdaftar di praktikum manapun.</div>
    <?php else: ?>
        <?php if (isset($component)) { $__componentOriginaledcee5fc9fded8c7a966e5daae82f7d1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaledcee5fc9fded8c7a966e5daae82f7d1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.mhs-header','data' => ['praktikum' => $praktikum]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.mhs-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['praktikum' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($praktikum)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaledcee5fc9fded8c7a966e5daae82f7d1)): ?>
<?php $attributes = $__attributesOriginaledcee5fc9fded8c7a966e5daae82f7d1; ?>
<?php unset($__attributesOriginaledcee5fc9fded8c7a966e5daae82f7d1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaledcee5fc9fded8c7a966e5daae82f7d1)): ?>
<?php $component = $__componentOriginaledcee5fc9fded8c7a966e5daae82f7d1; ?>
<?php unset($__componentOriginaledcee5fc9fded8c7a966e5daae82f7d1); ?>
<?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikum): ?>

            
            <div style="display:flex; flex-direction:column; gap:16px;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pengumumans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $nameParts = explode(' ', $pg->user?->name ?? 'SY');
                        $initials = strtoupper(substr($nameParts[0] ?? 'S', 0, 1) . substr($nameParts[1] ?? $nameParts[0] ?? 'Y', 0, 1));
                        $avColors = ['sky', 'navy', 'green', 'yellow', 'violet'];
                        $avColor = $avColors[crc32($pg->user?->email ?? '') % count($avColors)];
                    ?>
                    <div style="border:1px solid #DFE1E7; background:#fff; border-radius:14px; padding:15px 24px; position:relative; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition:all 0.2s ease;"
                        onmouseover="this.style.boxShadow='0 4px 14px rgba(17,24,39,0.05)';"
                        onmouseout="this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)';">

                        
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
                            <div style="display:flex; align-items:center; gap:12px;">
                                
                                <div class="mp-av lg <?php echo e($avColor); ?>" style="flex-shrink:0;">
                                    <?php echo e($initials); ?>

                                </div>
                                <div>
                                    <div style="font-weight:700; font-size:14px; color:#111827;">
                                        <?php echo e($pg->user?->name ?? '—'); ?>

                                    </div>
                                    <div style="font-size:12px; color:#6B7280;">
                                        <?php echo e($pg->created_at?->format('d M Y, H:i') ?? '-'); ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pg->updated_at && $pg->updated_at->gt($pg->created_at)): ?>
                                            <span style="font-style:italic; margin-left:4px;">(Diedit
                                                <?php echo e($pg->updated_at->format('d M Y, H:i')); ?>)</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div style="margin:10px 0 0 0;">
                            <h3 style="font-size:15px; font-weight:700; color:#111827; margin:0 0 4px 0; line-height:1.4; padding:0;">
                                <?php echo e($pg->judul); ?>

                            </h3>
                            <p style="font-size:13px; color:#374151; margin:0; padding:0; line-height:1.6; white-space:pre-line;"><?php echo e($pg->konten); ?></p>
                        </div>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($pg->lampiran)): ?>
                            <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:14px;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pg->lampiran; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lamp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <?php
                                        $pfPath = isset($lamp['path']) ? $lamp['path'] : $lamp;
                                        $pfName = isset($lamp['name']) ? $lamp['name'] : basename($pfPath);
                                    ?>
                                    <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($pfPath, 'eoffice')); ?>"
                                        target="_blank" title="<?php echo e($pfName); ?>"
                                        style="display: flex; flex-direction: column; width: 140px; height: 140px; border: 1px solid #DFE1E7; border-radius: 8px; overflow: hidden; text-decoration: none; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.02); transition:transform 0.15s, box-shadow 0.15s;"
                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)';"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                        <div style="flex: 1; display: flex; align-items: center; justify-content: center; background: #F9FAFB;">
                                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB"
                                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                <polyline points="13 2 13 9 20 9"></polyline>
                                            </svg>
                                        </div>
                                        <div style="background: #293C79; color: #fff; padding: 10px 12px; font-size: 13px; font-weight: 600; text-align: center; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                            <?php echo e($pfName); ?>

                                        </div>
                                    </a>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="mp-card flex-shrink-0" style="min-height:180px;display:flex;align-items:center;justify-content:center;">
                        <div style="padding:36px;text-align:center;">
                            <div style="width:48px;height:48px;border-radius:12px;background:#F4F6F8;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                                    stroke-linecap="round">
                                    <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" />
                                </svg>
                            </div>
                            <div style="font-size:14px;font-weight:600;color:#0D0D12;margin-bottom:4px;">Belum Ada Pengumuman</div>
                            <div style="font-size:12px;color:#666D80;">Belum ada pengumuman yang diterbitkan pada praktikum ini.</div>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pengumumans instanceof \Illuminate\Pagination\LengthAwarePaginator && $pengumumans->hasPages()): ?>
                <div class="flex-shrink-0" style="padding:8px 0;"><?php echo e($pengumumans->links()); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php else: ?>
            <div class="mp-alert info flex-shrink-0">Silakan pilih praktikum terlebih dahulu untuk melihat pengumuman.</div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc437526b4c56b06f9c16ab6722a84925)): ?>
<?php $attributes = $__attributesOriginalc437526b4c56b06f9c16ab6722a84925; ?>
<?php unset($__attributesOriginalc437526b4c56b06f9c16ab6722a84925); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc437526b4c56b06f9c16ab6722a84925)): ?>
<?php $component = $__componentOriginalc437526b4c56b06f9c16ab6722a84925; ?>
<?php unset($__componentOriginalc437526b4c56b06f9c16ab6722a84925); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules/EOffice\resources/views/manajemen-praktikum/mahasiswa/pengumuman.blade.php ENDPATH**/ ?>