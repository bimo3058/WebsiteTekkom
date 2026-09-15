<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Daftar Praktikum']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Daftar Praktikum']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="mp-page-header" style="margin-bottom: 24px;">
        <div>
            <h1 class="mp-page-title">Daftar Praktikum</h1>
            <p class="mp-page-sub">Berikut adalah kelas praktikum yang Anda ikuti</p>
        </div>
    </div>

    <div class="mp-card" style="padding: 24px;">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!isset($daftarPraktikan) || $daftarPraktikan->isEmpty()): ?>
            <div style="padding:48px;text-align:center;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                    stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                </svg>
                <div style="font-size:13px;font-weight:500;color:#666D80;">Anda belum terdaftar di praktikum manapun.</div>
            </div>
        <?php else: ?>
            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:24px;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $daftarPraktikan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php $p = $dp->praktikum; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p): ?>
                    <a href="<?php echo e(route('eoffice.manprak.mahasiswa.pengumuman.index', ['praktikum_id' => $p->id])); ?>" 
                       style="display:flex; flex-direction:column; background:#fff; border:1px solid #E5E7EB; border-radius:12px; overflow:hidden; text-decoration:none; transition:all 0.2s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.05);"
                       onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 20px -5px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05)';"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.05)';">
                        
                        
                        <div style="height:160px; background-color:#F3F4F6; display:flex; align-items:center; justify-content:center; border-bottom:1px solid #E5E7EB;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->cover_path): ?>
                                <img src="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($p->cover_path, 'eoffice')); ?>" alt="<?php echo e($p->nama); ?>" style="width:100%; height:100%; object-fit:cover;">
                            <?php else: ?>
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="#828896">
                                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                                </svg>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <div style="padding:20px; display:flex; flex-direction:column; flex:1;">
                            
                            <div style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:12px;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->status === 'aktif'): ?>
                                    <span style="font-size:11px; font-weight:600; background:#F1F5F9; color:#475569; padding:4px 10px; border-radius:6px;">Aktif</span>
                                <?php else: ?>
                                    <span style="font-size:11px; font-weight:600; background:#F1F5F9; color:#64748B; padding:4px 10px; border-radius:6px;">Tutup</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <span style="font-size:11px; font-weight:600; background:#EFF6FF; color:#2563EB; padding:4px 10px; border-radius:6px;">Semester <?php echo e($p->semester ?? 'Genap'); ?></span>
                            </div>

                            
                            <h3 style="font-size:16px; font-weight:700; color:#111827; margin:0 0 3px 0; line-height:1.4;"><?php echo e($p->nama); ?></h3>

                            
                            <?php
                                $dosenNames = $p->dosens->pluck('name')->implode(', ');
                            ?>
                            <p style="font-size:13px; color:#6B7280; margin:0; line-height:1.5;">
                                Tahun Ajaran: <?php echo e($p->tahun_ajaran ?? '-'); ?>

                                <br>
                                Dosen: <?php echo e($dosenNames ?: 'Belum ditunjuk'); ?>

                                <br>
                                <?php echo e($p->daftar_praktikan_count ?? 0); ?> Praktikan &bull; <?php echo e($p->asprak_praktikum_count ?? 0); ?> Asisten
                            </p>
                        </div>
                    </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules/EOffice\resources/views/manajemen-praktikum/mahasiswa/praktikum.blade.php ENDPATH**/ ?>