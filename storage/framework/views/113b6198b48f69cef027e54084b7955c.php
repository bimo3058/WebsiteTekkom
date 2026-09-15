<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Pengumuman Praktikum']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Pengumuman Praktikum']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


    
    <div class="mp-page-header">
        <div>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                <h1 class="mp-page-title">Pengumuman</h1>
                <span class="mp-badge warning sm"><span class="dot"></span>Mahasiswa</span>
            </div>
            <p class="mp-page-sub">Informasi dan pengumuman dari asisten dan koordinator praktikum ·
                <?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?></p>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikumList->isEmpty()): ?>
        <div class="mp-alert warning flex-shrink-0">Anda belum terdaftar di praktikum manapun.</div>
    <?php else: ?>

        
        <div class="sec-head flex-shrink-0">
            <span class="sec-bar" style="background:#D39C3D;"></span>
            <span class="sec-title">Pilih Praktikum</span>
            <span class="sec-rule"></span>
        </div>

        <div class="mp-card flex-shrink-0">
            <div style="padding:14px 18px;">
                <form method="GET" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#808897" stroke-width="2"
                        stroke-linecap="round" style="flex-shrink:0;">
                        <path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2zM22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z" />
                    </svg>
                    <select name="praktikum_id" class="mp-input mp-select" style="max-width:360px;"
                        onchange="this.form.submit()">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $praktikumList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($p->id); ?>" <?php echo e(($praktikum?->id == $p->id) ? 'selected' : ''); ?>>
                                <?php echo e($p->nama); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->kode): ?> [<?php echo e($p->kode); ?>] <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                · <?php echo e($p->semester); ?> <?php echo e($p->tahun_ajaran); ?>

                            </option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikum): ?>
                        <span class="mp-badge warning sm"><span
                                class="dot"></span><?php echo e($pengumumans instanceof \Illuminate\Pagination\LengthAwarePaginator ? $pengumumans->total() : $pengumumans->count()); ?>

                            pengumuman</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </form>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikum): ?>
            
            <div class="mp-stats-grid cols-2 flex-shrink-0" style="grid-template-columns:repeat(2,1fr);">
                <div class="mp-stat" style="display:flex;align-items:center;gap:14px;padding:14px 18px;">
                    <div class="mp-stat-icon" style="background:rgba(211,156,61,0.12);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D39C3D" stroke-width="2"
                            stroke-linecap="round">
                            <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" />
                        </svg>
                    </div>
                    <div>
                        <div class="mp-stat-value" style="font-size:22px;">
                            <?php echo e($pengumumans instanceof \Illuminate\Pagination\LengthAwarePaginator ? $pengumumans->total() : $pengumumans->count()); ?>

                        </div>
                        <div class="mp-stat-label" style="margin:0;">Total Pengumuman</div>
                    </div>
                </div>
                <div class="mp-stat" style="display:flex;align-items:center;gap:14px;padding:14px 18px;">
                    <div class="mp-stat-icon" style="background:rgba(64,196,170,0.12);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#40C4AA" stroke-width="2"
                            stroke-linecap="round">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                    </div>
                    <div>
                        <?php
                            $latest = ($pengumumans instanceof \Illuminate\Pagination\LengthAwarePaginator ? $pengumumans->getCollection() : $pengumumans)->first();
                        ?>
                        <div class="mp-stat-value" style="font-size:14px;font-weight:600;">
                            <?php echo e($latest ? $latest->created_at?->locale('id')->diffForHumans() : '—'); ?></div>
                        <div class="mp-stat-label" style="margin:0;">Pengumuman Terbaru</div>
                    </div>
                </div>
            </div>

            
            <div class="sec-head flex-shrink-0">
                <span class="sec-bar"></span>
                <span class="sec-title">Daftar Pengumuman</span>
                <span class="sec-rule"></span>
                <span
                    class="mp-badge neutral sm"><?php echo e($pengumumans instanceof \Illuminate\Pagination\LengthAwarePaginator ? $pengumumans->total() : $pengumumans->count()); ?>

                    total</span>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pengumumans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $nameParts = explode(' ', $pg->user?->name ?? 'SY');
                    $initials = strtoupper(substr($nameParts[0] ?? 'S', 0, 1) . substr($nameParts[1] ?? $nameParts[0] ?? 'Y', 0, 1));
                    $avColors = ['sky', 'navy', 'green', 'yellow', 'violet'];
                    $avColor = $avColors[crc32($pg->user?->email ?? '') % count($avColors)];
                    $isSistem = $pg->tipe_sistem !== null;
                    $isNew = $pg->created_at && $pg->created_at->gt(now()->subDays(2));
                ?>
                <div class="mp-card flex-shrink-0" style="transition:border-color .2s, box-shadow .2s;"
                    onmouseover="this.style.borderColor='#B7C2DE';this.style.boxShadow='0 4px 14px rgba(11,38,110,.07)'"
                    onmouseout="this.style.borderColor='#DFE1E7';this.style.boxShadow=''">

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isNew): ?>
                        <div style="height:3px;background:linear-gradient(90deg,#D39C3D,#F4C666);border-radius:14px 14px 0 0;"></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div style="padding:20px;">
                        <div style="display:flex;align-items:flex-start;gap:14px;">
                            
                            <div style="flex-shrink:0;margin-top:2px;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isSistem): ?>
                                    <div
                                        style="width:36px;height:36px;border-radius:10px;background:rgba(11,38,110,0.1);display:flex;align-items:center;justify-content:center;">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2"
                                            stroke-linecap="round">
                                            <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" />
                                        </svg>
                                    </div>
                                <?php else: ?>
                                    <?php
                                        $colorMap = ['sky' => '#106A97', 'navy' => '#0B266E', 'green' => '#174E43', 'yellow' => '#5B3D1E', 'violet' => '#5B21B6'];
                                        $bg = $colorMap[$avColor] ?? '#0B266E';
                                    ?>
                                    <div
                                        style="width:36px;height:36px;border-radius:10px;background:<?php echo e($bg); ?>;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;">
                                        <?php echo e($initials); ?>

                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            <div style="flex:1;min-width:0;">
                                
                                <div style="display:flex;align-items:flex-start;gap:8px;flex-wrap:wrap;margin-bottom:4px;">
                                    <div style="font-size:15px;font-weight:700;color:#0D0D12;flex:1;min-width:0;line-height:1.3;">
                                        <?php echo e($pg->judul); ?>

                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isNew): ?>
                                        <span class="mp-badge warning sm" style="animation:pulse-badge 2s infinite;">
                                            <span class="dot"></span>Baru
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isSistem): ?>
                                        <span class="mp-badge sky sm">Sistem</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                
                                <div
                                    style="font-size:11px;color:#808897;margin-bottom:12px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
                                    <span style="display:flex;align-items:center;gap:4px;">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round">
                                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z" />
                                        </svg>
                                        <strong
                                            style="color:#666D80;"><?php echo e($isSistem ? 'Sistem' : ($pg->user?->name ?? '—')); ?></strong>
                                    </span>
                                    <span style="display:flex;align-items:center;gap:4px;">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round">
                                            <circle cx="12" cy="12" r="10" />
                                            <polyline points="12 6 12 12 16 14" />
                                        </svg>
                                        <?php echo e($pg->created_at?->locale('id')->isoFormat('D MMM YYYY, HH:mm')); ?>

                                    </span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pg->praktikum): ?>
                                        <span style="display:flex;align-items:center;gap:4px;">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round">
                                                <path
                                                    d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2zM22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z" />
                                            </svg>
                                            <strong style="color:#666D80;"><?php echo e($pg->praktikum->nama); ?></strong>
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                <div
                                    style="font-size:13px;color:#353849;line-height:1.7;white-space:pre-line;background:#F8FAFC;border:1px solid #EDF0F4;border-radius:8px;padding:14px;">
                                    <?php echo e($pg->konten); ?>

                                </div>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($pg->lampiran)): ?>
                                <div style="margin-top:12px;display:flex;flex-wrap:wrap;gap:8px;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pg->lampiran; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lamp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->getPublicUrl($lamp['path'], 'eoffice')); ?>" target="_blank"
                                       style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:#fff;border:1px solid #DFE1E7;border-radius:6px;font-size:12px;font-weight:500;color:#0B266E;text-decoration:none;transition:all .2s;"
                                       onmouseover="this.style.borderColor='#0B266E';this.style.background='#F0F4FA'" onmouseout="this.style.borderColor='#DFE1E7';this.style.background='#fff'">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                            <path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/>
                                        </svg>
                                        <span style="max-width:150px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="<?php echo e($lamp['name']); ?>">
                                            <?php echo e($lamp['name']); ?>

                                        </span>
                                    </a>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pg->tipe_sistem === 'buka'): ?>
                                    <div style="margin-top:14px;">
                                        <a href="<?php echo e(route('eoffice.manprak.mahasiswa.daftar-asprak.index')); ?>?praktikum_id=<?php echo e($pg->praktikum_id); ?>"
                                            class="mp-btn primary sm" style="text-decoration:none;display:inline-flex;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" style="margin-right:6px;">
                                                <path
                                                    d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8zM14 2v6h6M12 18v-6M9 15h6" />
                                            </svg>
                                            Daftar Sekarang →
                                        </a>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="mp-card flex-1 flex items-center justify-center" style="min-height:280px;">
                    <div style="padding:48px;text-align:center;">
                        <div
                            style="width:56px;height:56px;border-radius:14px;background:#F4F6F8;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                                stroke-linecap="round">
                                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" />
                            </svg>
                        </div>
                        <div style="font-size:15px;font-weight:600;color:#0D0D12;margin-bottom:6px;">Belum Ada Pengumuman</div>
                        <div style="font-size:13px;color:#666D80;max-width:260px;margin:0 auto;line-height:1.6;">
                            Pengumuman dari asisten atau koordinator praktikum Anda akan muncul di sini.
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pengumumans instanceof \Illuminate\Pagination\LengthAwarePaginator && $pengumumans->hasPages()): ?>
                <div class="flex-shrink-0" style="padding:8px 0;"><?php echo e($pengumumans->links()); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php else: ?>
            <div class="mp-alert info flex-shrink-0">Silakan pilih praktikum terlebih dahulu untuk melihat pengumuman.</div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <style>
        @keyframes pulse-badge {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .6;
            }
        }

        .sec-head {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .sec-bar {
            width: 4px;
            height: 18px;
            background: #D39C3D;
            border-radius: 2px;
            flex-shrink: 0;
        }

        .sec-title {
            font-size: 14px;
            font-weight: 700;
            color: #0D0D12;
            white-space: nowrap;
        }

        .sec-rule {
            flex: 1;
            height: 1px;
            background: #ECEFF3;
        }
    </style>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc437526b4c56b06f9c16ab6722a84925)): ?>
<?php $attributes = $__attributesOriginalc437526b4c56b06f9c16ab6722a84925; ?>
<?php unset($__attributesOriginalc437526b4c56b06f9c16ab6722a84925); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc437526b4c56b06f9c16ab6722a84925)): ?>
<?php $component = $__componentOriginalc437526b4c56b06f9c16ab6722a84925; ?>
<?php unset($__componentOriginalc437526b4c56b06f9c16ab6722a84925); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum_backup\mahasiswa\pengumuman.blade.php ENDPATH**/ ?>