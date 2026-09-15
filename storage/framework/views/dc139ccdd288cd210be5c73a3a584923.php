<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Data Praktikan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Data Praktikan']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>



<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Data Praktikan</h1>
            <span class="mp-badge" style="background:#E0E7FF;color:#6366F1;border-radius:999px;padding:3px 10px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:5px;"><span class="dot" style="background:#6366F1;"></span>Koordinator</span>
        </div>
        <p class="mp-page-sub"><?php echo e($praktikum?->nama ?? 'Belum ada praktikum aktif'); ?> · <?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?></p>
    </div>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$praktikum): ?>
<div class="mp-alert warning flex-shrink-0">Anda belum memiliki praktikum aktif.</div>
<?php else: ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
<div class="mp-flash mp-flash-error" style="border-radius:10px;border:1px solid #DF1C41;">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <div style="flex:1;">
        <div style="font-weight:700;">Gagal mengimpor:</div>
        <ul style="margin:4px 0 0;padding-left:20px;list-style-type:disc;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><li><?php echo e($error); ?></li><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </ul>
    </div>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>




<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Import Kelompok & Shift</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-shrink-0" style="padding:20px;">
    <div style="display:flex;gap:16px;flex-wrap:wrap;align-items:flex-start;">
        
        <div style="flex:1;min-width:220px;">
            <div style="font-weight:700;font-size:14px;color:#0D0D12;margin-bottom:8px;">Cara Import Kelompok & Shift</div>
            <ol style="font-size:12px;color:#666D80;line-height:1.8;padding-left:18px;margin:0;">
                <li>Export data praktikan terdaftar ke CSV <span style="color:#0B266E;font-weight:600;">(tombol biru di bawah)</span></li>
                <li>Buka file CSV di Excel, isi kolom <code style="background:#F0F1F4;padding:1px 5px;border-radius:4px;">Kelompok</code> dan <code style="background:#F0F1F4;padding:1px 5px;border-radius:4px;">Shift</code></li>
                <li>Simpan kembali sebagai CSV, lalu upload di sini</li>
            </ol>
            <div style="margin-top:10px;font-size:11px;color:#A4ABB8;">
                Format kolom import: <code style="background:#F0F1F4;padding:1px 5px;border-radius:4px;">nim_atau_email, kelompok, shift</code>
                &nbsp;·&nbsp;
                <a href="<?php echo e(route('eoffice.manprak.koor.praktikan.template')); ?>" style="color:#0B266E;font-weight:600;font-size:11px;display:inline-flex;align-items:center;gap:3px;text-decoration:none;">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download Template Kosong
                </a>
            </div>
        </div>
        
        <div style="flex:1;min-width:240px;">
            <form method="POST" action="<?php echo e(route('eoffice.manprak.koor.praktikan.import')); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="praktikum_id" value="<?php echo e($praktikum->id); ?>">
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">File CSV (nim_atau_email, kelompok, shift)</label>
                <input type="file" name="file" accept=".csv,.txt" class="mp-input" style="margin-bottom:10px;">
                <button class="mp-btn primary md" style="width:100%;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/></svg>
                    Import Kelompok & Shift
                </button>
            </form>
        </div>
    </div>
</div>




<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Praktikan Terdaftar</span>
    <span class="sec-rule"></span>
</div>

<div class="mp-card flex-1 min-h-0">
    <div class="mp-card-header">
        <span class="mp-card-title">
            Praktikan Terdaftar
            <span class="mp-badge neutral sm" style="margin-left:6px;"><?php echo e($praktikans->total()); ?></span>
        </span>
        <div class="right" style="gap:8px;display:flex;align-items:center;flex-wrap:wrap;">
            
            <a href="<?php echo e(route('eoffice.manprak.koor.praktikan.export')); ?>"
               class="mp-btn secondary sm" style="display:inline-flex;align-items:center;gap:5px;text-decoration:none;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export CSV
            </a>
            
            <form method="GET" style="display:flex;gap:4px;">
                <input name="search" value="<?php echo e($search); ?>" placeholder="Cari nama / NIM..." class="mp-input" style="width:180px;">
                <button type="submit" class="mp-btn primary sm">Cari</button>
            </form>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <?php
            $shiftRowspan = [];
            $kelompokRowspan = [];
            
            $items = $praktikans->items();
            $totalItems = count($items);

            // Calculate rowspan for shift
            $i = 0;
            while ($i < $totalItems) {
                $val = $items[$i]->shift;
                if (empty($val)) {
                    $shiftRowspan[$i] = 1;
                    $i++;
                    continue;
                }
                $count = 1;
                while ($i + $count < $totalItems && $items[$i + $count]->shift === $val) {
                    $count++;
                }
                $shiftRowspan[$i] = $count;
                for ($j = 1; $j < $count; $j++) {
                    $shiftRowspan[$i + $j] = 0;
                }
                $i += $count;
            }

            // Calculate rowspan for kelompok (must match same kelompok AND same shift)
            $i = 0;
            while ($i < $totalItems) {
                $valK = $items[$i]->kelompok;
                $valS = $items[$i]->shift;
                if (empty($valK)) {
                    $kelompokRowspan[$i] = 1;
                    $i++;
                    continue;
                }
                $count = 1;
                while (
                    $i + $count < $totalItems && 
                    $items[$i + $count]->kelompok === $valK && 
                    $items[$i + $count]->shift === $valS
                ) {
                    $count++;
                }
                $kelompokRowspan[$i] = $count;
                for ($j = 1; $j < $count; $j++) {
                    $kelompokRowspan[$i + $j] = 0;
                }
                $i += $count;
            }
        ?>

        <table class="mp-table" style="min-width:700px;">
            <thead>
                <tr style="background:#F9FAFB;">
                    <th class="mp-th text-left" style="padding:10px 16px;width:40px;">#</th>
                    <th class="mp-th text-left" style="padding:10px 16px;">Mahasiswa</th>
                    <th class="mp-th text-left" style="padding:10px 16px;width:140px;">NIM</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:120px;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;">Kelompok</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:120px;border-right:1px solid #DFE1E7;">Shift</th>
                    <th class="mp-th text-left" style="padding:10px 16px;width:100px;">Status</th>
                    <th class="mp-th text-left" style="padding:10px 16px;width:130px;">Tanggal Masuk</th>
                </tr>
            </thead>
            <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $praktikans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                    <td style="padding:12px 16px;font-size:12px;color:#A4ABB8;font-weight:600;">
                        <?php echo e($praktikans->firstItem() + $idx); ?>

                    </td>
                    <td style="padding:12px 16px;">
                        <div class="flex items-center gap-[10px]">
                            <div class="mp-av yellow"><?php echo e(strtoupper(substr($p->user?->name ?? 'M', 0, 2))); ?></div>
                            <div>
                                <div style="font-weight:600;color:#0D0D12;"><?php echo e($p->user?->name ?? '-'); ?></div>
                                <div style="font-size:11px;color:#666D80;"><?php echo e($p->user?->email); ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 16px;font-size:12px;font-family:monospace;font-weight:600;color:#353849;letter-spacing:.03em;">
                        <?php echo e($p->user?->student?->student_number ?? '-'); ?>

                    </td>
                    
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kelompokRowspan[$idx] > 0): ?>
                        <td rowspan="<?php echo e($kelompokRowspan[$idx]); ?>" style="padding:16px;text-align:center;vertical-align:middle;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;background:#FFF;font-size:18px;font-weight:700;color:#0D0D12;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->kelompok): ?>
                                <?php echo e($p->kelompok); ?>

                            <?php else: ?>
                                <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">—</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($shiftRowspan[$idx] > 0): ?>
                        <td rowspan="<?php echo e($shiftRowspan[$idx]); ?>" style="padding:16px;text-align:center;vertical-align:middle;border-right:1px solid #DFE1E7;background:#FFF;font-size:18px;font-weight:700;color:#0D0D12;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->shift): ?>
                                <?php echo e($p->shift); ?>

                            <?php else: ?>
                                <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">—</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <td style="padding:12px 16px;">
                        <span class="mp-badge success sm"><span class="dot"></span><?php echo e($p->status ?? 'terdaftar'); ?></span>
                    </td>
                    <td style="padding:12px 16px;font-size:12px;color:#666D80;">
                        <?php echo e($p->created_at?->format('d M Y H:i')); ?>

                    </td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="7">
                        <div style="padding:48px;text-align:center;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada praktikan yang terdaftar.</div>
                            <div style="font-size:11px;color:#A4ABB8;margin-top:4px;">Praktikan akan muncul di sini setelah Anda menyetujui pendaftaran mereka.</div>
                        </div>
                    </td>
                </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikans->hasPages()): ?>
    <div style="padding:12px 16px;border-top:1px solid #DFE1E7;flex-shrink:0;"><?php echo e($praktikans->links()); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
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
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum_backup\koordinator\daftar-praktikan.blade.php ENDPATH**/ ?>