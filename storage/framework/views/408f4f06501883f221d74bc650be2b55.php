<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Tugas Praktikum']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Tugas Praktikum']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$daftarPraktikan): ?>
        <div class="mp-page-header">
            <div>
                <h1 class="mp-page-title">Tugas Praktikum</h1>
            </div>
        </div>
        <div class="mp-alert warning flex-shrink-0">Anda belum terdaftar di praktikum manapun.</div>
    <?php else: ?>
        <?php if (isset($component)) { $__componentOriginaledcee5fc9fded8c7a966e5daae82f7d1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaledcee5fc9fded8c7a966e5daae82f7d1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.mhs-header','data' => ['praktikum' => $daftarPraktikan->praktikum]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.mhs-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['praktikum' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($daftarPraktikan->praktikum)]); ?>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    
    <style>
        .modul-accordion-content {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.15s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.1s ease;
        }
        .modul-accordion-content.is-open {
            max-height: 1500px;
            opacity: 1;
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s ease;
        }
        .tugas-accordion-content {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.15s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.1s ease;
        }
        .tugas-accordion-content.is-open {
            max-height: 1200px; /* Increased for upload form */
            opacity: 1;
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s ease;
        }
    </style>

    <div style="display: flex; flex-direction: column; gap: 16px; padding-top: 4px;">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $modulList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php 
                $modul = $item['modul']; 
                $tugasList = $item['tugas'];
            ?>
            
            <div x-data="{ modulOpen: false }" style="background: #fff; border: 1px solid var(--c-border); border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden;">
                
                
                <div @click="modulOpen = !modulOpen" style="padding: 16px 24px; background: #fff; border-bottom: 1px solid var(--c-border); cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: background 0.15s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#fff'">
                    <div>
                        <h3 style="font-size: 16px; font-weight: 700; color: #111827; margin: 0 0 4px 0;"><?php echo e($modul->nama); ?></h3>
                        <div style="font-size: 13px; color: #6B7280;">
                            Asisten: 
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item['asprak']->isNotEmpty()): ?>
                                <?php echo e($item['asprak']->join(', ')); ?>

                            <?php else: ?>
                                -
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :style="modulOpen ? 'transform: rotate(180deg); transition: transform 0.2s;' : 'transform: rotate(0deg); transition: transform 0.2s;'">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>

                
                <div class="modul-accordion-content" :class="{ 'is-open': modulOpen }">
                    <div style="display: flex; flex-direction: column;">
                    <?php
                        $requiredTypes = [
                            'tugas_pendahuluan' => 'Tugas Pendahuluan',
                            'laporan'           => 'Laporan',
                            'responsi'          => 'Responsi',
                            'tugas_pengganti'   => 'Tugas Pengganti'
                        ];
                    ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $requiredTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $t = $tugasList->firstWhere('jenis_tugas', $type);
                            
                            $dlAC  = $t && $t->deadline ? \Carbon\Carbon::parse($t->deadline) : null;
                            $dlACC = $t && $t->deadline_acc ? \Carbon\Carbon::parse($t->deadline_acc) : null;
                            
                            $lewat = $dlAC && now()->gt($dlAC);
                            $lewatMutlak = ($dlACC ?? $dlAC) && now()->gt($dlACC ?? $dlAC);
                            
                            $sisaAC = $dlAC ? now()->diffInDays($dlAC, false) : null;

                            $pengumpulan = $t ? $t->pengumpulan : null;
                            $sudahKumpul = !is_null($pengumpulan);
                            $statusTugas = $pengumpulan?->status_pengumpulan ?? 'belum_dikumpul';

                            $isLate = false;
                            if ($sudahKumpul && $pengumpulan->riwayat) {
                                $firstSubmission = $pengumpulan->riwayat->sortBy('created_at')->first();
                                if ($firstSubmission && $dlAC && \Carbon\Carbon::parse($firstSubmission->created_at)->gt($dlAC)) {
                                    $isLate = true;
                                }
                            }

                            $oldFiles = [];
                            if ($t && $t->file_path) {
                                $oldFiles = json_decode($t->file_path, true) ?? [];
                                if (!is_array($oldFiles)) $oldFiles = [$t->file_path];
                            }
                        ?>
                        
                        <div x-data="{ open: false, showUpload: false }" style="border-bottom: 1px solid var(--c-border);">
                            
                            
                            <div @click="open = !open" style="padding: 14px 24px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; background: #fff; transition: background 0.15s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#fff'">
                                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                    <div style="font-size: 14px; font-weight: 600; color: #0D0D12;"><?php echo e($label); ?></div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($t): ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statusTugas === 'acc'): ?>
                                            <span class="mp-badge success sm"><span class="dot"></span>ACC<?php echo e($pengumpulan?->nilai ? ' — Nilai: ' . $pengumpulan->nilai : ''); ?></span>
                                        <?php elseif($statusTugas === 'revisi'): ?>
                                            <span class="mp-badge error sm"><span class="dot"></span>Perlu Revisi</span>
                                        <?php elseif($statusTugas === 'belum_dicek'): ?>
                                            <span class="mp-badge warning sm"><span class="dot"></span>Menunggu Penilaian</span>
                                        <?php elseif($lewatMutlak): ?>
                                            <span class="mp-badge error sm"><span class="dot"></span>Waktu Habis</span>
                                        <?php elseif($lewat): ?>
                                            <span class="mp-badge warning sm"><span class="dot"></span>AC Terlewat</span>
                                        <?php elseif($sisaAC !== null && $sisaAC <= 2): ?>
                                            <span class="mp-badge warning sm"><span class="dot"></span>Segera!</span>
                                        <?php else: ?>
                                            <span class="mp-badge neutral sm"><span class="dot"></span>Belum Dikumpul</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isLate): ?>
                                            <span class="mp-badge error sm" style="background:#FFF0F2;color:#DF1C41;border:1px solid #DF1C41;">Terlambat</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div style="display: flex; align-items: center; gap: 16px;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($t): ?>
                                    <div style="font-size: 12px; color: #666D80; display: flex; gap: 8px;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dlAC && $dlACC): ?>
                                            <span style="color:<?php echo e($lewatMutlak ? '#A4ABB8' : '#353849'); ?>;">AC: <?php echo e($dlAC->format('d/m/Y, H:i')); ?> | ACC: <?php echo e($dlACC->format('d/m/Y, H:i')); ?></span>
                                        <?php elseif($dlACC): ?>
                                            <span style="color:<?php echo e($lewatMutlak ? '#A4ABB8' : '#353849'); ?>;">ACC: <?php echo e($dlACC->format('d/m/Y, H:i')); ?></span>
                                        <?php elseif($dlAC): ?>
                                            <span style="color:<?php echo e($lewatMutlak ? '#A4ABB8' : '#353849'); ?>;">AC: <?php echo e($dlAC->format('d/m/Y, H:i')); ?></span>
                                        <?php else: ?>
                                            <span style="color:#A4ABB8;">Tanpa batas waktu</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :style="open ? 'transform: rotate(180deg); transition: transform 0.2s;' : 'transform: rotate(0deg); transition: transform 0.2s;'">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>
                            </div>

                            
                            <div class="tugas-accordion-content" :class="{ 'is-open': open }">
                                <div style="border-top: 1px solid var(--c-border); padding: 16px 24px; background: #FAFBFC;">
                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($t): ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($t->deskripsi): ?>
                                            <div style="font-size: 13px; color: #374151; margin-bottom: 16px; line-height: 1.5; white-space: pre-wrap;"><?php echo e($t->deskripsi); ?></div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        
                                        <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 16px;">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $oldFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                <?php 
                                                    $pathStr = is_array($f) && isset($f['path']) ? $f['path'] : $f;
                                                    $baseName = is_array($f) && isset($f['original_name']) ? $f['original_name'] : pathinfo($pathStr, PATHINFO_BASENAME);
                                                ?>
                                                <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($pathStr, 'eoffice')); ?>"
                                                    target="_blank" title="<?php echo e($baseName); ?>"
                                                    style="display: flex; flex-direction: column; width: 120px; height: 120px; border: 1px solid #DFE1E7; border-radius: 8px; overflow: hidden; text-decoration: none; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.02); transition:transform 0.15s, box-shadow 0.15s;"
                                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)';"
                                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                                    <div style="flex: 1; display: flex; align-items: center; justify-content: center; background: #F9FAFB;">
                                                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                                            stroke="#D1D5DB" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                            <polyline points="13 2 13 9 20 9"></polyline>
                                                        </svg>
                                                    </div>
                                                    <div style="background: #293C79; color: #fff; padding: 8px 10px; font-size: 11px; font-weight: 600; text-align: center; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;" title="<?php echo e($baseName); ?>">
                                                        <?php echo e($baseName); ?>

                                                    </div>
                                                </a>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </div>

                                        <div style="border-top:1px solid #DFE1E7; margin:16px 0;"></div>                                        
                                        <div x-data="{
                                            isEditing: false,
                                            files: [],
                                            init() {
                                                // addInput is the trigger; fileInput is the real named input
                                            },
                                            addFiles(event) {
                                                const newFiles = Array.from(event.target.files);
                                                
                                                if (this.files.length + newFiles.length > 3) {
                                                    alert('Maksimal 3 file diperbolehkan.');
                                                    event.target.value = '';
                                                    return;
                                                }
                                                
                                                const maxSize = 5 * 1024 * 1024;
                                                for(let i=0; i<newFiles.length; i++) {
                                                    if (newFiles[i].size > maxSize) {
                                                        alert('Ukuran file ' + newFiles[i].name + ' melebihi 5MB.');
                                                        event.target.value = '';
                                                        return;
                                                    }
                                                }
                                                
                                                let dt = new DataTransfer();
                                                this.files.forEach(f => dt.items.add(f));
                                                newFiles.forEach(f => dt.items.add(f));
                                                
                                                // Update the real named input
                                                this.$refs.fileInput.files = dt.files;
                                                // Update reactive list
                                                this.files = Array.from(this.$refs.fileInput.files);
                                                // Only reset addInput (the trigger), NOT fileInput
                                                this.$refs.addInput.value = '';
                                            },
                                            removeFile(index) {
                                                let dt = new DataTransfer();
                                                this.files.splice(index, 1);
                                                this.files.forEach(f => dt.items.add(f));
                                                this.$refs.fileInput.files = dt.files;
                                                this.$refs.addInput.value = '';
                                            }
                                        }">
                                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                                                <h3 style="font-size:14px; font-weight:700; color:#353849;">Pengumpulan Tugas</h3>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sudahKumpul && $pengumpulan): ?>
                                                    <span style="font-size:12px; font-weight:500; color:#666D80;">Dikumpulkan pada: <?php echo e(\Carbon\Carbon::parse($pengumpulan->updated_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm')); ?> WIB</span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>

                                            
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sudahKumpul && $pengumpulan?->file_path): ?>
                                                <div x-show="!isEditing" class="mb-4">
                                                    <div style="display:flex;flex-direction:column;gap:8px;">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pengumpulan->files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fileIdx => $pPathObj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                            <?php 
                                                                $pPath = is_array($pPathObj) && isset($pPathObj['path']) ? $pPathObj['path'] : $pPathObj;
                                                                $pName = is_array($pPathObj) && isset($pPathObj['original_name']) ? $pPathObj['original_name'] : pathinfo($pPath, PATHINFO_BASENAME);
                                                            ?>
                                                            <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($pPath, 'eoffice')); ?>"
                                                                target="_blank"
                                                                style="display:flex; align-items:center; padding:10px 14px; border:1px solid #DFE1E7; border-radius:8px; background:#fff; text-decoration:none; transition:border-color 0.2s;"
                                                                onmouseover="this.style.borderColor='#0B266E'" onmouseout="this.style.borderColor='#DFE1E7'">
                                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-right:8px;">
                                                                    <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                                    <polyline points="13 2 13 9 20 9"></polyline>
                                                                </svg>
                                                                <span style="font-size:13px; font-weight:500; color:#0B266E; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?php echo e($pName); ?></span>
                                                            </a>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                    </div>
                                                    
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$lewatMutlak && !in_array($statusTugas, ['acc'])): ?>
                                                        <div style="display:flex; justify-content:flex-end; margin-top:12px;">
                                                            <button type="button" @click="isEditing = true" class="mp-btn secondary" style="width:200px; padding: 8px 16px; font-size:13px; height:30px; border-radius:8px; text-align:center; justify-content:center; color:#DF1C41; border-color:#DF1C41;">
                                                                Batalkan Pengiriman
                                                            </button>
                                                        </div>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>

                                                
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$lewatMutlak && !in_array($statusTugas, ['acc'])): ?>
                                                    <div x-show="isEditing" x-cloak class="mb-4">
                                                        
                                                        <div style="font-size:11px; font-weight:600; color:#666D80; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">File yang sudah dikirim</div>
                                                        <div style="display:flex;flex-direction:column;gap:6px; margin-bottom:16px;">
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pengumpulan->files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pPathObj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                                <?php 
                                                                    $pPath = is_array($pPathObj) && isset($pPathObj['path']) ? $pPathObj['path'] : $pPathObj;
                                                                    $pName = is_array($pPathObj) && isset($pPathObj['original_name']) ? $pPathObj['original_name'] : pathinfo($pPath, PATHINFO_BASENAME);
                                                                ?>
                                                                <div style="display:flex; align-items:center; padding:9px 14px; border:1px solid #EEF0F5; border-radius:8px; background:#F9FAFB;">
                                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-right:8px;">
                                                                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                                        <polyline points="13 2 13 9 20 9"></polyline>
                                                                    </svg>
                                                                    <span style="font-size:12px; color:#808897; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?php echo e($pName); ?></span>
                                                                </div>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                        </div>

                                                        
                                                        <div style="font-size:11px; font-weight:600; color:#666D80; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px;">Unggah file pengganti</div>
                                                        <form method="POST" action="<?php echo e(route('eoffice.manprak.mahasiswa.tugas.kumpul', $t->id)); ?>" enctype="multipart/form-data">
                                                            <?php echo csrf_field(); ?>
                                                            <input type="file" name="file[]" multiple x-ref="fileInput" style="display:none;" required>
                                                            <input type="file" multiple @change="addFiles" x-ref="addInput" style="display:none;">
                                                            
                                                            <button type="button" @click="$refs.addInput.click()"
                                                                style="display:flex; width:100%; align-items:center; justify-content:center; gap:8px; border:1px dashed #A4ABB8; background:#fff; color:#0B266E; padding:12px 20px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; transition:all 0.2s;"
                                                                onmouseover="this.style.background='#F9FAFB'; this.style.borderColor='#0B266E';"
                                                                onmouseout="this.style.background='#fff'; this.style.borderColor='#A4ABB8';">
                                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                                                </svg>
                                                                Tambah Berkas
                                                            </button>

                                                            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px; margin-bottom:12px;">
                                                                <span style="font-size:11px; color:#666D80;">Maksimal 3 file per pengumpulan.</span>
                                                                <span style="font-size:11px; color:#666D80;">Maks. 5MB per file</span>
                                                            </div>

                                                            <div x-show="files.length > 0" class="flex flex-col gap-2" style="margin-bottom:12px;">
                                                                <template x-for="(file, index) in files" :key="index">
                                                                    <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 14px; border:1px solid #DFE1E7; border-radius:8px; background:#fff;">
                                                                        <div style="display:flex; align-items:center; gap:8px; overflow:hidden;">
                                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                                                                                <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                                                <polyline points="13 2 13 9 20 9"></polyline>
                                                                            </svg>
                                                                            <span x-text="file.name" style="font-size:13px; font-weight:500; color:#353849; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"></span>
                                                                        </div>
                                                                        <button type="button" @click="removeFile(index)" style="color:#DF1C41; background:none; border:none; padding:4px; cursor:pointer; display:flex; align-items:center; justify-content:center; border-radius:4px;" onmouseover="this.style.background='#FFF0F2'" onmouseout="this.style.background='none'">
                                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                                                                <line x1="6" y1="6" x2="18" y2="18"></line>
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </template>
                                                            </div>

                                                            
                                                            <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:4px; flex-wrap:wrap;">
                                                                <button type="button" @click="isEditing = false; files = []; $refs.addInput.value = '';" class="mp-btn secondary" style="border-radius:8px; font-size:13px; justify-content:center;">
                                                                    Batalkan Perubahan
                                                                </button>
                                                                <button type="submit" class="mp-btn primary md" x-bind:disabled="files.length === 0" style="width:150px; border-radius:8px; font-size:13px; justify-content:center;">
                                                                    Kumpulkan Kembali
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sudahKumpul && ($pengumpulan?->catatan_revisi || $pengumpulan?->file_revisi_asprak)): ?>
                                                <div class="mb-4 p-3 rounded-[8px]" style="background:#FADAE1;border:1px solid #DF1C41;" x-show="!isEditing">
                                                    <div style="font-size:11px;font-weight:700;color:#7C1028;margin-bottom:2px;">Catatan Revisi dari Asisten:</div>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pengumpulan->catatan_revisi): ?>
                                                        <div style="font-size:12px;color:#7C1028;margin-bottom:6px;"><?php echo e($pengumpulan->catatan_revisi); ?></div>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pengumpulan->file_revisi_asprak): ?>
                                                        <div style="margin-top:6px;">
                                                            <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($pengumpulan->file_revisi_asprak, 'eoffice')); ?>" target="_blank" style="font-size:11px;font-weight:700;color:#95122B;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                                    <polyline points="7 10 12 15 17 10" />
                                                                    <line x1="12" y1="15" x2="12" y2="3" />
                                                                </svg>
                                                                Unduh File Lampiran Revisi
                                                            </a>
                                                        </div>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($daftarPraktikan) && $daftarPraktikan?->praktikum?->is_active && !$lewatMutlak): ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$sudahKumpul || $statusTugas === 'revisi'): ?>
                                                    <div>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statusTugas === 'revisi'): ?>
                                                            <div style="font-size:12px;font-weight:600;color:#D39C3D;margin-bottom:12px;">Kirim ulang file perbaikan:</div>
                                                            <form method="POST" action="<?php echo e(route('eoffice.manprak.mahasiswa.tugas.kirim-ulang', $t->id)); ?>" enctype="multipart/form-data">
                                                                <?php echo csrf_field(); ?>
                                                        <?php else: ?>
                                                            <form method="POST" action="<?php echo e(route('eoffice.manprak.mahasiswa.tugas.kumpul', $t->id)); ?>" enctype="multipart/form-data">
                                                                <?php echo csrf_field(); ?>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                                    
                                                                    <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:4px;">
                                                                        <template x-if="isEditing">
                                                                            <button type="button" @click="isEditing = false; files = []; $refs.fileInput.value = '';" class="mp-btn secondary" style="border-radius:8px; width:200px; text-align:center; font-size:13px; justify-content:center;">
                                                                                Batalkan Perubahan
                                                                            </button>
                                                                        </template>
                                                                        <button type="submit" class="mp-btn primary md" x-bind:disabled="files.length === 0" style="width:150px; border-radius:8px; text-align:center; font-size:13px; justify-content:center;">
                                                                            <span x-text="isEditing ? 'Kumpulkan Kembali' : '<?php echo e($statusTugas === 'revisi' ? 'Kirim Perbaikan' : 'Kumpulkan'); ?>'"></span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                        
                                    <?php else: ?>
                                        <div style="font-size: 13px; color: #6B7280; font-style: italic;"><?php echo e($label); ?> belum diunggah.</div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                </div>
                            </div>

                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>

            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div style="padding: 48px; text-align: center; border: 1px solid var(--c-border); border-radius: 8px; background: #fff;">
                <div style="font-size: 14px; font-weight: 600; color: #111827;">Belum Ada Modul & Tugas</div>
                <div style="font-size: 13px; color: #6B7280; margin-top: 4px;">Belum ada data yang dapat ditampilkan.</div>
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
<?php /**PATH C:\Users\User\manajemen_praktikum_\Modules/EOffice\resources/views/manajemen-praktikum/mahasiswa/tugas.blade.php ENDPATH**/ ?>