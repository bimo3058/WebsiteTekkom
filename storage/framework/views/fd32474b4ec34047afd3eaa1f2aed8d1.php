<?php
    $pageTitle = ($praktikum->nama ?? 'Praktikum') . ' / Modul';
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

    <div x-data="modulManager()">

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="mp-flash mp-flash-success flex-shrink-0 mb-[16px]">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
            <div class="mp-flash mp-flash-error flex-shrink-0 mb-[16px]">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="15" y1="9" x2="9" y2="15" />
                    <line x1="9" y1="9" x2="15" y2="15" />
                </svg>
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($praktikum) && $praktikum): ?>
            <?php if (isset($component)) { $__componentOriginalc4328e4d8a5660f77eb1acfc1689aa68 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc4328e4d8a5660f77eb1acfc1689aa68 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.asprak-header','data' => ['praktikum' => $praktikum,'activeTab' => 'modul']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.asprak-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['praktikum' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($praktikum),'activeTab' => 'modul']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc4328e4d8a5660f77eb1acfc1689aa68)): ?>
<?php $attributes = $__attributesOriginalc4328e4d8a5660f77eb1acfc1689aa68; ?>
<?php unset($__attributesOriginalc4328e4d8a5660f77eb1acfc1689aa68); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc4328e4d8a5660f77eb1acfc1689aa68)): ?>
<?php $component = $__componentOriginalc4328e4d8a5660f77eb1acfc1689aa68; ?>
<?php unset($__componentOriginalc4328e4d8a5660f77eb1acfc1689aa68); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikumList->isEmpty()): ?>
            <div class="mp-alert warning flex-shrink-0">Anda belum terdaftar sebagai asisten praktikum di praktikum manapun.
                Hubungi koordinator untuk aktivasi.</div>
        <?php else: ?>


            <div style="display:flex; flex-direction:column; gap:24px;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $moduls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modul): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php $isMine = $assignedModulIds->contains($modul->id); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isMine): ?>
                        <div
                            style="border:1px solid #DFE1E7; border-radius:12px; background:#fff; padding:24px; position:relative;">

                            
                            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                <div style="flex:1; min-width:0;">
                                    <div style="font-size:16px; font-weight:700; color:#111827; margin:0 0 4px 0;">
                                        <?php echo e($modul->nama); ?></div>
                                    <div style="font-size:12px; color:#374151; font-weight:400; margin-bottom:4px;">
                                        Asisten:
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_2 = true; $__currentLoopData = $modul->modulAsprak; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ma): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <span><?php echo e($ma->asprak?->user?->name ?? '—'); ?></span><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$loop->last): ?>, <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            <span>—</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div style="font-size:12px; color:#6B7280; font-weight:400;">
                                        <?php $firstMateri = $modul->materi->sortBy('created_at')->first(); ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($firstMateri): ?>
                                            <?php echo e(\Carbon\Carbon::parse($firstMateri->created_at)->locale('id')->translatedFormat('d M Y, H:i')); ?>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($modul->updated_at && $modul->updated_at->diffInSeconds($firstMateri->created_at) > 5): ?>
                                                <span style="font-style:italic; color:#9CA3AF;">(Diedit <?php echo e(\Carbon\Carbon::parse($modul->updated_at)->locale('id')->translatedFormat('d M Y, H:i')); ?>)</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isMine): ?>
                                    <button type="button" data-modul-id="<?php echo e($modul->id); ?>" data-modul-nama="<?php echo e($modul->nama); ?>"
                                        data-modul-deskripsi="<?php echo e($modul->deskripsi ?? ''); ?>"
                                        @click="openEditModal($el.dataset.modulId, $el.dataset.modulNama, $el.dataset.modulDeskripsi, <?php echo e(json_encode($modul->materi->map(function ($m) {
                                    return ['id' => $m->id, 'name' => basename($m->file_path ?? 'File'), 'url' => app(\App\Services\SupabaseStorage::class)->publicUrl($m->file_path, 'eoffice')]; }))); ?>)"
                                        title="Edit Modul"
                                        style="background:#EEF2FF; border:none; padding:10px; border-radius:10px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background 0.2s; flex-shrink:0; margin-left:12px; width:40px; height:40px;"
                                        onmouseover="this.style.background='#E0E7FF'" onmouseout="this.style.background='#EEF2FF'">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#293C79" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($modul->deskripsi): ?>
                                <div style="margin:20px 0 0 0; padding-top:20px; border-top:1px solid #DFE1E7;">
                                    <p style="font-size:13px; color:#374151; margin:0; padding:0; line-height:1.6;">
                                        <?php echo e($modul->deskripsi); ?></p>
                                </div>
                            <?php else: ?>
                                <div style="margin:20px 0 0 0; padding-top:20px; border-top:1px solid #DFE1E7;"></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($modul->materi->isNotEmpty()): ?>
                                <div style="display:flex; gap:16px; flex-wrap:wrap; margin-top:20px;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $modul->materi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $materi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <a href="<?php echo e($materi->file_path ? app(\App\Services\SupabaseStorage::class)->publicUrl($materi->file_path, 'eoffice') : '#'); ?>"
                                            target="_blank"
                                            style="display:flex; flex-direction:column; width:180px; height:160px; border:1px solid #DFE1E7; border-radius:8px; overflow:hidden; text-decoration:none; background:#fff; transition:transform 0.15s, box-shadow 0.15s;"
                                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)';"
                                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">

                                            
                                            <div
                                                style="flex:1; display:flex; align-items:center; justify-content:center; background:#FAFAFA;">
                                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB"
                                                    stroke-width="1.5" stroke-linecap="round">
                                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                                                    <polyline points="14 2 14 8 20 8" />
                                                </svg>
                                            </div>

                                            
                                            <div style="background:#293C79; padding:12px 14px; display:flex; align-items:center;">
                                                <span
                                                    style="color:#FFF; font-size:12px; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; width:100%;">
                                                    <?php echo e($materi->judul ?? basename($materi->file_path ?? 'File')); ?>

                                                </span>
                                            </div>
                                        </a>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div
                        style="min-height:180px;display:flex;align-items:center;justify-content:center;border:1px dashed #DFE1E7;border-radius:12px;">
                        <div style="padding:36px;text-align:center;">
                            <div
                                style="width:48px;height:48px;border-radius:12px;background:#FAFAFA;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border:1px solid #DFE1E7;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="1.5"
                                    stroke-linecap="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" />
                                    <path d="M3 9h18M9 21V9" />
                                </svg>
                            </div>
                            <div style="font-size:14px;font-weight:600;color:#111827;margin-bottom:4px;">Belum Ada Modul</div>
                            <div style="font-size:12px;color:#6B7280;">Belum ada modul yang di-assign ke Anda.</div>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>



            
            
            
            <div x-show="editModal.open" style="display:none;"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm transition-all duration-300"
                x-cloak>

                <div class="relative bg-white rounded-xl shadow-2xl w-[600px] max-w-[90%] max-h-[90vh] overflow-y-auto"
                    style="border:1px solid #E5E7EB;" @click.away="editModal.open = false" x-show="editModal.open"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                    <div
                        style="padding:20px 24px; border-bottom:1px solid #E5E7EB; display:flex; justify-content:space-between; align-items:center;">
                        <h2 style="font-size:18px; font-weight:700; color:#111827; margin:0;">Edit Modul</h2>
                        <button type="button" @click="editModal.open = false"
                            style="background:none; border:none; cursor:pointer; color:#6B7280;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round">
                                <line x1="18" y1="6" x2="6" y2="18" />
                                <line x1="6" y1="6" x2="18" y2="18" />
                            </svg>
                        </button>
                    </div>

                    <div style="padding:24px;">
                        <form :action="`/eoffice/manprak/asprak/modul/${editModal.id}`" method="POST"
                            enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:16px;"
                            @submit="injectDeletedFiles($event)">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>

                            
                            <div>
                                <label
                                    style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#374151;">
                                    Nama Modul <span style="color:#DF1C41;">*</span>
                                </label>
                                <select name="id" required x-model="editModal.id" disabled
                                    style="width:100%; padding:10px 14px; border:1px solid #D1D5DB; border-radius:8px; font-size:14px; color:#111827; background:#F9FAFB; appearance:none; background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%236B7280%22 stroke-width=%222%22><polyline points=%226 9 12 15 18 9%22/></svg>'); background-repeat:no-repeat; background-position:right 12px center; background-size:16px; cursor:not-allowed;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $moduls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modul): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($assignedModulIds->contains($modul->id)): ?>
                                            <option value="<?php echo e($modul->id); ?>"><?php echo e($modul->nama); ?></option>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </select>
                                <input type="hidden" name="nama" x-model="editModal.nama">
                            </div>

                            
                            <div>
                                <label
                                    style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#374151;">
                                    Deskripsi <span style="font-weight:400; color:#9CA3AF;">(Opsional, maks. 500
                                        karakter)</span>
                                </label>
                                <textarea name="deskripsi" rows="3" maxlength="500" placeholder="Deskripsi singkat..."
                                    x-model="editModal.deskripsi"
                                    style="width:100%; padding:10px 14px; border:1px solid #D1D5DB; border-radius:8px; font-size:14px; color:#111827; resize:vertical; min-height:80px; max-height:120px; box-sizing:border-box;"></textarea>
                            </div>

                            
                            <div>
                                <label
                                    style="display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#374151;">
                                    Berkas Lampiran <span style="color:#DF1C41;">*</span>
                                </label>

                                <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:8px;">
                                    <button type="button" @click="$refs.fileInputEdit.click()"
                                        style="width:100%; padding:10px; border:1px dashed #D1D5DB; border-radius:8px; background:#F9FAFB; color:#293C79; font-size:13px; font-weight:600; cursor:pointer; display:flex; justify-content:center; align-items:center; gap:8px; transition:all 0.2s;"
                                        onmouseover="this.style.background='#F3F4F6'; this.style.borderColor='#9CA3AF';"
                                        onmouseout="this.style.background='#F9FAFB'; this.style.borderColor='#D1D5DB';">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round">
                                            <line x1="12" y1="5" x2="12" y2="19"></line>
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                        Tambah Berkas
                                    </button>
                                    <div
                                        style="display:flex; justify-content:space-between; align-items:center; font-size:11px; color:#9CA3AF;">
                                        <span x-text="editFiles.length + ' / 3 file terpilih'"></span>
                                        <span>Maks. 5MB per file</span>
                                    </div>
                                </div>
                                <input type="file" x-ref="fileInputEdit" style="display:none" multiple accept="*/*"
                                    @change="addFiles($event, 'edit')">
                                <input type="file" id="hidden-lampiran-edit" name="lampiran[]" multiple
                                    style="display:none">

                                
                                <div style="display:flex;flex-direction:column;gap:6px;">
                                    <template x-for="(file, index) in editFiles" :key="index">
                                        <div
                                            style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:#F9FAFB;border:1px solid #E5E7EB;border-radius:6px;">
                                            <div style="display:flex;align-items:center;gap:8px;overflow:hidden;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6B7280"
                                                    stroke-width="2" stroke-linecap="round">
                                                    <path
                                                        d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48" />
                                                </svg>
                                                <span x-text="file.name"
                                                    style="font-size:12px;color:#374151;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:300px;"></span>
                                            </div>
                                            <button type="button" @click="removeFile(index, 'edit')" title="Hapus"
                                                style="color:#DC2626;background:none;border:none;cursor:pointer;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                                    <line x1="18" y1="6" x2="6" y2="18" />
                                                    <line x1="6" y1="6" x2="18" y2="18" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>

                                
                                <template x-if="editModal.existingFiles.filter(f => !f.deleted).length > 0">
                                    <div style="margin-top:10px;">
                                        <div style="font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;">
                                            Berkas Saat Ini:</div>
                                        <div style="display:flex;flex-direction:column;gap:6px;">
                                            <template x-for="(file, index) in editModal.existingFiles" :key="index">
                                                <div x-show="!file.deleted">
                                                    <div
                                                        style="display:flex;flex-direction:row;align-items:center;padding:5px 12px;background:#FAFAFA;border:1px solid #E5E7EB;border-radius:6px;gap:8px;">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                                            stroke="#6B7280" stroke-width="2" stroke-linecap="round"
                                                            style="flex-shrink:0;">
                                                            <path
                                                                d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48" />
                                                        </svg>
                                                        <a :href="file.url" target="_blank"
                                                            style="font-size:12px;color:#293C79;text-decoration:none;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1;min-width:0;"
                                                            x-text="file.name"></a>
                                                        <button type="button" @click="removeExistingFile(index)"
                                                            title="Hapus Berkas"
                                                            style="color:#9CA3AF;background:none;border:none;cursor:pointer;flex-shrink:0;display:flex;align-items:center;padding:0;"
                                                            onmouseover="this.style.color='#DC2626'"
                                                            onmouseout="this.style.color='#9CA3AF'">
                                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2.5"
                                                                stroke-linecap="round">
                                                                <line x1="18" y1="6" x2="6" y2="18" />
                                                                <line x1="6" y1="6" x2="18" y2="18" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div style="display:flex;justify-content:flex-end; gap:12px; margin-top:8px;">
                                <button type="submit"
                                    style="padding:10px 20px; font-size:14px; font-weight:600; color:white; background:#293C79; border:none; border-radius:8px; cursor:pointer;"
                                    onmouseover="this.style.background='#1F2D59'"
                                    onmouseout="this.style.background='#293C79'">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> 
    </div>

    <script>
        function modulManager() {
            return {
                createModal: { open: false, modulId: '' },
                editModal: { open: false, id: null, nama: '', deskripsi: '', existingFiles: [] },
                createFiles: [],
                editFiles: [],

                openEditModal(id, nama, deskripsi, existingFiles) {
                    this.editModal.id = id;
                    this.editModal.nama = nama;
                    this.editModal.deskripsi = deskripsi;
                    this.editModal.existingFiles = existingFiles ? existingFiles.map(f => ({ ...f, deleted: false })) : [];
                    this.editFiles = [];
                    this.syncInput('edit');
                    this.editModal.open = true;
                },

                removeExistingFile(index) {
                    this.editModal.existingFiles[index].deleted = true;
                },

                addFiles(e, type) {
                    let selectedFiles = Array.from(e.target.files);
                    let currentFiles = type === 'edit' ? this.editFiles : this.createFiles;
                    let existingFileCount = type === 'edit' ? this.editModal.existingFiles.filter(f => !f.deleted).length : 0;
                    let totalFiles = currentFiles.length + selectedFiles.length + existingFileCount;

                    if (totalFiles > 3) {
                        alert('Maksimal 3 file yang dapat dilampirkan!');
                        let available = 3 - (currentFiles.length + existingFileCount);
                        selectedFiles = available > 0 ? selectedFiles.slice(0, available) : [];
                    }

                    if (type === 'edit') {
                        this.editFiles = [...this.editFiles, ...selectedFiles];
                    } else {
                        this.createFiles = [...this.createFiles, ...selectedFiles];
                    }

                    this.syncInput(type);
                    e.target.value = ''; // reset so same file can be picked again
                },

                removeFile(index, type) {
                    if (type === 'edit') {
                        this.editFiles.splice(index, 1);
                    } else {
                        this.createFiles.splice(index, 1);
                    }
                    this.syncInput(type);
                },

                injectDeletedFiles(e) {
                    const form = e.target;
                    // Hapus input deleted_files lama jika ada
                    form.querySelectorAll('input[data-deleted-file]').forEach(el => el.remove());
                    // Inject hanya file yang ditandai deleted
                    this.editModal.existingFiles.forEach(f => {
                        if (f.deleted) {
                            const inp = document.createElement('input');
                            inp.type = 'hidden';
                            inp.name = 'deleted_files[]';
                            inp.value = f.id;
                            inp.setAttribute('data-deleted-file', '1');
                            form.appendChild(inp);
                        }
                    });
                    // Biarkan form submit secara normal
                },

                syncInput(type) {
                    let dt = new DataTransfer();
                    let currentFiles = type === 'edit' ? this.editFiles : this.createFiles;

                    currentFiles.forEach(file => dt.items.add(file));

                    if (type === 'edit') {
                        document.getElementById('hidden-lampiran-edit').files = dt.files;
                    } else {
                        document.getElementById('hidden-lampiran-create').files = dt.files;
                    }
                }
            };
        }
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc437526b4c56b06f9c16ab6722a84925)): ?>
<?php $attributes = $__attributesOriginalc437526b4c56b06f9c16ab6722a84925; ?>
<?php unset($__attributesOriginalc437526b4c56b06f9c16ab6722a84925); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc437526b4c56b06f9c16ab6722a84925)): ?>
<?php $component = $__componentOriginalc437526b4c56b06f9c16ab6722a84925; ?>
<?php unset($__componentOriginalc437526b4c56b06f9c16ab6722a84925); ?>
<?php endif; ?><?php /**PATH C:\Users\User\manajemen_praktikum_\Modules/EOffice\resources/views/manajemen-praktikum/asprak/modul.blade.php ENDPATH**/ ?>