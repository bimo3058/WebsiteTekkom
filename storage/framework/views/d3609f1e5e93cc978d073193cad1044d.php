<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Absensi &amp; Nilai']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Absensi &amp; Nilai']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>



<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Absensi &amp; Nilai — <?php echo e($modul->nama); ?></h1>
            <span class="mp-badge success sm"><span class="dot"></span>Asprak</span>
        </div>
        <p class="mp-page-sub"><?php echo e($modul->praktikum?->nama); ?> · <?php echo e($praktikans->count()); ?> praktikan</p>
    </div>
    <div class="mp-page-actions">
        <a href="<?php echo e(route('eoffice.manprak.asprak.absensi.index')); ?>" class="mp-btn secondary md" style="text-decoration:none;">Kembali</a>
    </div>
</div>


<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Pilih Modul</span>
    <span class="sec-rule"></span>
</div>

<div class="flex gap-2 flex-wrap flex-shrink-0">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $moduls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
    <a href="<?php echo e(route('eoffice.manprak.asprak.absensi.show', $m->id)); ?>"
       class="<?php echo e($m->id === $modul->id ? 'mp-btn primary md' : 'mp-btn secondary md'); ?>"
       style="text-decoration:none;"><?php echo e($m->nama); ?></a>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
</div>

<?php
    $shiftRowspan = [];
    $kelompokRowspan = [];
    
    $items = $praktikans->all();
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


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
<div class="mp-flash mp-flash-success flex-shrink-0" style="margin-top:24px;">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
    <?php echo e(session('success')); ?>

</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<div class="mp-card flex-shrink-0" style="margin-top:24px;margin-bottom:24px;padding:14px 20px;background:#EFF6FF;border-left:3px solid #3B82F6;">
    <p style="font-size:12px;color:#1E3A8A;margin:0;line-height:1.6;">
        <strong>Informasi:</strong> Kehadiran dan Nilai kini digabung dalam satu tabel untuk mempermudah monitoring. 
        Kolom Nilai (Tugas Pendahuluan, Praktikum, Laporan, Responsi) akan otomatis aktif jika mahasiswa ditandai <strong>Hadir</strong>. 
        Nilai yang diinput di sini akan tersinkronisasi dua arah dengan form tugas di menu <em>Lihat Pengumpulan</em>.
    </p>
</div>


<form method="POST" action="<?php echo e(route('eoffice.manprak.asprak.absensi.store', $modul->id)); ?>" class="mp-card flex-shrink-0">
    <?php echo csrf_field(); ?>
    <div class="mp-card-header" style="position:sticky;left:0;">
        <div style="display:flex;align-items:center;gap:12px;">
            <label style="font-size:12px;font-weight:600;color:#353849;">Tanggal Praktikum</label>
            <input type="date" name="tanggal" value="<?php echo e(date('Y-m-d')); ?>" required class="mp-input">
        </div>
        <div class="right">
            <button class="mp-btn primary md">Simpan Semua (Absensi &amp; Nilai)</button>
        </div>
    </div>

    <div style="overflow-x:auto; padding-bottom: 24px;">
        <table class="mp-table" style="min-width: 1400px;">
            <thead>
                <tr style="background:#F9FAFB;">
                    <th class="mp-th text-left" style="padding:10px 16px;width:40px;position:sticky;left:0;background:#F9FAFB;z-index:2;">#</th>
                    <th class="mp-th text-left" style="padding:10px 16px;min-width:200px;position:sticky;left:40px;background:#F9FAFB;z-index:2;border-right:1px solid #DFE1E7;">Mahasiswa</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:90px;">Klp</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:70px;border-right:1px solid #DFE1E7;">Shift</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:60px;">Hadir</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:60px;">Izin</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:60px;border-right:1px solid #DFE1E7;">Alpha</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:110px;background:#EEF2FF;">Pendahuluan</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:110px;background:#F0FDF4;">Praktikum</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:110px;background:#FEF9C3;">Laporan</th>
                    <th class="mp-th text-center" style="padding:10px 16px;width:110px;background:#FFF7ED;">Responsi</th>
                    <th class="mp-th text-left" style="padding:10px 16px;min-width:180px;border-left:1px solid #DFE1E7;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $praktikans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $row      = $absensi[$p->id] ?? null;
                    $status   = $row?->status ?? 'hadir';
                    
                    $njMap    = $nilaiJenis[$p->id] ?? collect();
                    $nilaiTP  = $njMap['tugas_pendahuluan']->nilai ?? null;
                    $nilaiPrak = $njMap['praktikum']->nilai ?? null;
                    $nilaiLap = $njMap['laporan']->nilai ?? null;
                    $nilaiResp = $njMap['responsi']->nilai ?? null;
                ?>
                
                
                <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;" x-data="{ status: '<?php echo e($status); ?>' }">
                    <td style="padding:12px 16px;font-size:12px;color:#A4ABB8;font-weight:600;position:sticky;left:0;background:#FFF;z-index:1;">
                        <?php echo e($idx + 1); ?>

                    </td>
                    <td style="padding:12px 16px;position:sticky;left:40px;background:#FFF;z-index:1;border-right:1px solid #DFE1E7;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="mp-av yellow" style="flex-shrink:0;"><?php echo e(strtoupper(substr($p->user?->name ?? 'M', 0, 2))); ?></div>
                            <div style="min-width:0;">
                                <div style="font-weight:600;color:#0D0D12;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo e($p->user?->name ?? '-'); ?></div>
                                <div style="font-size:11px;color:#666D80;font-family:monospace;letter-spacing:0.02em;"><?php echo e($p->user?->student?->student_number ?? '-'); ?></div>
                            </div>
                        </div>
                    </td>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kelompokRowspan[$idx] > 0): ?>
                        <td rowspan="<?php echo e($kelompokRowspan[$idx]); ?>" style="padding:16px;text-align:center;vertical-align:middle;border-right:1px solid #DFE1E7;background:#FFF;font-size:18px;font-weight:700;color:#0D0D12;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->kelompok): ?> <?php echo e($p->kelompok); ?> <?php else: ?> <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">—</span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($shiftRowspan[$idx] > 0): ?>
                        <td rowspan="<?php echo e($shiftRowspan[$idx]); ?>" style="padding:16px;text-align:center;vertical-align:middle;border-right:1px solid #DFE1E7;background:#FFF;font-size:18px;font-weight:700;color:#0D0D12;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->shift): ?> <?php echo e($p->shift); ?> <?php else: ?> <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">—</span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <td style="padding:12px 16px;text-align:center;vertical-align:middle;">
                        <input type="radio" name="absensi[<?php echo e($p->id); ?>][status]" value="hadir" x-model="status" style="accent-color:#10B981;cursor:pointer;width:18px;height:18px;">
                    </td>
                    <td style="padding:12px 16px;text-align:center;vertical-align:middle;">
                        <input type="radio" name="absensi[<?php echo e($p->id); ?>][status]" value="izin" x-model="status" style="accent-color:#3B82F6;cursor:pointer;width:18px;height:18px;">
                    </td>
                    <td style="padding:12px 16px;text-align:center;vertical-align:middle;border-right:1px solid #DFE1E7;">
                        <input type="radio" name="absensi[<?php echo e($p->id); ?>][status]" value="tidak_hadir" x-model="status" style="accent-color:#EF4444;cursor:pointer;width:18px;height:18px;">
                    </td>

                    
                    <td style="padding:8px 12px;text-align:center;vertical-align:middle;" :style="status === 'hadir' ? 'background:#EEF2FF;' : 'background:#F1F5F9;'">
                        <input type="number" name="nilai[<?php echo e($p->id); ?>][tugas_pendahuluan]"
                               value="<?php echo e($nilaiTP !== null ? $nilaiTP : ''); ?>"
                               min="0" max="100" step="0.5"
                               x-bind:disabled="status !== 'hadir'"
                               :placeholder="status === 'hadir' ? '0–100' : '—'"
                               class="mp-input"
                               style="width:80px;text-align:center;font-size:13px;font-weight:600;"
                               :style="status === 'hadir' ? 'color:#4338CA;border-color:#C7D2FE;background:#FFF;' : 'color:#94A3B8;border-color:#E2E8F0;background:#F8FAFC;'">
                    </td>

                    
                    <td style="padding:8px 12px;text-align:center;vertical-align:middle;" :style="status === 'hadir' ? 'background:#F0FDF4;' : 'background:#F1F5F9;'">
                        <input type="number" name="nilai[<?php echo e($p->id); ?>][praktikum]"
                               value="<?php echo e($nilaiPrak !== null ? $nilaiPrak : ''); ?>"
                               min="0" max="100" step="0.5"
                               x-bind:disabled="status !== 'hadir'"
                               :placeholder="status === 'hadir' ? '0–100' : '—'"
                               class="mp-input"
                               style="width:80px;text-align:center;font-size:13px;font-weight:600;"
                               :style="status === 'hadir' ? 'color:#15803D;border-color:#BBF7D0;background:#FFF;' : 'color:#94A3B8;border-color:#E2E8F0;background:#F8FAFC;'">
                    </td>
                    
                    
                    <td style="padding:8px 12px;text-align:center;vertical-align:middle;" :style="status === 'hadir' ? 'background:#FEF9C3;' : 'background:#F1F5F9;'">
                        <input type="number" name="nilai[<?php echo e($p->id); ?>][laporan]"
                               value="<?php echo e($nilaiLap !== null ? $nilaiLap : ''); ?>"
                               min="0" max="100" step="0.5"
                               x-bind:disabled="status !== 'hadir'"
                               :placeholder="status === 'hadir' ? '0–100' : '—'"
                               class="mp-input"
                               style="width:80px;text-align:center;font-size:13px;font-weight:600;"
                               :style="status === 'hadir' ? 'color:#A16207;border-color:#FEF08A;background:#FFF;' : 'color:#94A3B8;border-color:#E2E8F0;background:#F8FAFC;'">
                    </td>

                    
                    <td style="padding:8px 12px;text-align:center;vertical-align:middle;" :style="status === 'hadir' ? 'background:#FFF7ED;' : 'background:#F1F5F9;'">
                        <input type="number" name="nilai[<?php echo e($p->id); ?>][responsi]"
                               value="<?php echo e($nilaiResp !== null ? $nilaiResp : ''); ?>"
                               min="0" max="100" step="0.5"
                               x-bind:disabled="status !== 'hadir'"
                               :placeholder="status === 'hadir' ? '0–100' : '—'"
                               class="mp-input"
                               style="width:80px;text-align:center;font-size:13px;font-weight:600;"
                               :style="status === 'hadir' ? 'color:#C2410C;border-color:#FED7AA;background:#FFF;' : 'color:#94A3B8;border-color:#E2E8F0;background:#F8FAFC;'">
                    </td>
                    
                    
                    <td style="padding:12px 16px;vertical-align:middle;border-left:1px solid #DFE1E7;">
                        <input name="absensi[<?php echo e($p->id); ?>][keterangan]" value="<?php echo e($row?->keterangan); ?>"
                               class="mp-input w-full" style="font-size:12px;padding:6px 8px;min-width:140px;" placeholder="Opsional (Sakit, dsb)">
                    </td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="12">
                        <div style="padding:48px;text-align:center;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                                <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18M8 2v4M16 2v4"/>
                            </svg>
                            <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada praktikan.</div>
                        </div>
                    </td>
                </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
</form>

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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum_backup\asprak\absensi-show.blade.php ENDPATH**/ ?>