<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Kelola Tugas']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Kelola Tugas']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>



<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Kelola Tugas Praktikum</h1>
            <span class="mp-badge success sm"><span class="dot"></span>Asisten Praktikum</span>
        </div>
        <p class="mp-page-sub">Buat tugas dan nilai pengumpulan mahasiswa · <?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?></p>
    </div>
    <div class="mp-page-actions">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($praktikum) && $praktikum->is_active): ?>
        <a href="<?php echo e(route('eoffice.manprak.asprak.tugas.create')); ?>" class="mp-btn primary md" style="text-decoration:none;display:flex;align-items:center;gap:6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Tugas Baru
        </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>


<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Tugas</span>
    <span class="sec-rule"></span>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tugasList ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tugas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
<?php
    $dl    = $tugas->deadline ? \Carbon\Carbon::parse($tugas->deadline) : null;
    $lewat = $dl && now()->gt($dl);
    $totalKumpul = $tugas->pengumpulan_count ?? 0;
    $acc         = $tugas->pengumpulan_acc_count ?? 0;
    $revisi      = $tugas->pengumpulan_revisi_count ?? 0;
    $belumDicek  = $totalKumpul - $acc - $revisi;
?>
<div class="mp-card" style="padding:24px;margin-bottom:4px;width:100%;min-width:0;overflow:hidden;"
     onmouseover="this.style.boxShadow='0 4px 16px rgba(11,38,110,.08)';this.style.borderColor='#B7C2DE';"
     onmouseout="this.style.boxShadow='';this.style.borderColor='#DFE1E7';"
     x-data="{ open: false }">

    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:24px;">
        
        <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;flex-wrap:wrap;">
                <div style="font-size:15px;font-weight:700;color:#0D0D12;"><?php echo e($tugas->judul); ?></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lewat): ?>
                <span class="mp-badge neutral sm">Berakhir</span>
                <?php else: ?>
                <span class="mp-badge success sm"><span class="dot"></span>Aktif</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$tugas->is_published): ?>
                <span class="mp-badge warning sm">Draft</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div style="font-size:13px;color:#666D80;margin-bottom:10px;">
                <?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.icon','data' => ['name' => 'book']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'book']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $attributes = $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $component = $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?> Modul: <span style="font-weight:600;color:#353849;"><?php echo e($tugas->modul?->nama ?? '—'); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dl): ?>
                <br><?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.icon','data' => ['name' => 'clock']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'clock']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $attributes = $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $component = $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?> Deadline AC: <span style="font-weight:600;color:<?php echo e($lewat ? '#999' : '#353849'); ?>;"><?php echo e($dl->locale('id')->format('d M Y, H:i')); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tugas->deadline_acc): ?>
                <br><?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.icon','data' => ['name' => 'clock']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'clock']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $attributes = $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $component = $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?> Deadline ACC: <span style="font-weight:600;color:<?php echo e(now()->gt($tugas->deadline_acc) ? '#DF1C41' : '#353849'); ?>;"><?php echo e(\Carbon\Carbon::parse($tugas->deadline_acc)->locale('id')->format('d M Y, H:i')); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tugas->deskripsi): ?>
            <div style="font-size:12px;color:#666D80;line-height:1.5;margin-bottom:10px;"><?php echo e(Str::limit($tugas->deskripsi, 150)); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tugas->file_path): ?>
            <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($tugas->file_path, 'eoffice')); ?>"
               target="_blank"
               style="display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:7px;background:#FADAE1;text-decoration:none;font-size:11px;font-weight:600;color:#95122B;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                Lampiran PDF
            </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div style="display:flex;flex-direction:column;gap:12px;align-items:flex-end;flex-shrink:0;">
            
            <div style="display:grid;grid-template-columns:repeat(4, 1fr);gap:10px;">
                <div style="text-align:center;padding:10px 14px;border-radius:8px;background:#F9FAFB;border:1px solid #E4E6EB;">
                    <div style="font-size:18px;font-weight:700;color:#0D0D12;"><?php echo e($totalKumpul); ?></div>
                    <div style="font-size:10px;font-weight:600;color:#666D80;margin-top:4px;">Dikumpul</div>
                </div>
                <div style="text-align:center;padding:10px 14px;border-radius:8px;background:#FFFBF0;border:1px solid #FFE8C8;">
                    <div style="font-size:18px;font-weight:700;color:#D39C3D;"><?php echo e(max(0, $belumDicek)); ?></div>
                    <div style="font-size:10px;font-weight:600;color:#854F0B;margin-top:4px;">Menunggu</div>
                </div>
                <div style="text-align:center;padding:10px 14px;border-radius:8px;background:#FFF3F3;border:1px solid #FFD9E1;">
                    <div style="font-size:18px;font-weight:700;color:#DF1C41;"><?php echo e($revisi); ?></div>
                    <div style="font-size:10px;font-weight:600;color:#A51330;margin-top:4px;">Revisi</div>
                </div>
                <div style="text-align:center;padding:10px 14px;border-radius:8px;background:#E8EEFF;border:1px solid #D4DBFF;">
                    <div style="font-size:18px;font-weight:700;color:#0B266E;"><?php echo e($acc); ?></div>
                    <div style="font-size:10px;font-weight:600;color:#185FA5;margin-top:4px;">ACC</div>
                </div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($praktikum) && $praktikum->is_active): ?>
            <div style="display:flex;gap:8px;align-items:center;">
                <a href="<?php echo e(route('eoffice.manprak.asprak.tugas.edit', $tugas->id)); ?>"
                   class="mp-btn secondary sm"
                   style="text-decoration:none;display:flex;align-items:center;gap:5px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <form method="POST" action="<?php echo e(route('eoffice.manprak.asprak.tugas.destroy', $tugas->id)); ?>"
                      onsubmit="return confirm('Hapus tugas ini? Semua pengumpulan ikut terhapus.')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="mp-btn destructive sm"
                            style="display:flex;align-items:center;gap:5px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>
                        </svg>
                        Hapus
                    </button>
                </form>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div style="margin-top:20px;padding-top:20px;border-top:1px solid #DFE1E7;">
        <button @click="open = !open" type="button"
                style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:#6366F1;cursor:pointer;padding:8px 0;border:none;background:none;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                 :class="open ? 'rotate-90' : ''" style="transition:transform .2s;">
                <polyline points="9 18 15 12 9 6"/>
            </svg>
            <span x-text="open ? 'Sembunyikan Pengumpulan' : 'Lihat Pengumpulan (' + <?php echo e($totalKumpul); ?> + ')'"></span>
        </button>

        <div x-show="open" x-transition style="margin-top:16px;">
            <?php
                $praktikans = $tugas->praktikans ?? collect();
                $pengumpulanMapped = $tugas->pengumpulan_mapped ?? collect();
                
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
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikans->isEmpty()): ?>
            <div style="padding:40px;text-align:center;background:#F9FAFB;border-radius:8px;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <div style="font-size:13px;font-weight:600;color:#666D80;">Belum ada praktikan terdaftar di praktikum ini.</div>
            </div>
            <?php else: ?>
            <div style="border:1px solid #DFE1E7;border-radius:10px;overflow:hidden;">
                <div style="overflow-x:auto;overflow-y:auto;width:100%;max-height:450px;">
                    <table class="mp-table" style="width:100%;border-collapse:collapse;min-width:1250px;font-size:13px;text-align:left;">
                        <thead>
                            <tr style="background:#F9FAFB;border-bottom:1px solid #DFE1E7;position:sticky;top:0;z-index:10;">
                                <th class="mp-th text-left" style="padding:12px 16px;width:40px;background:#F9FAFB;">#</th>
                                <th class="mp-th text-left" style="padding:12px 16px;width:200px;background:#F9FAFB;">Mahasiswa</th>
                                <th class="mp-th text-left" style="padding:12px 16px;width:120px;background:#F9FAFB;">NIM</th>
                                <th class="mp-th text-center" style="padding:12px 16px;width:100px;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;background:#F9FAFB;">Kelompok</th>
                                <th class="mp-th text-center" style="padding:12px 16px;width:100px;border-right:1px solid #DFE1E7;background:#F9FAFB;">Shift</th>
                                <th class="mp-th text-left" style="padding:12px 16px;width:150px;background:#F9FAFB;">Dokumen</th>
                                <th class="mp-th text-left" style="padding:12px 16px;width:140px;background:#F9FAFB;">Waktu Submit</th>
                                <th class="mp-th text-left" style="padding:12px 16px;width:110px;background:#F9FAFB;">Nilai</th>
                                <th class="mp-th text-left" style="padding:12px 16px;width:90px;background:#F9FAFB;">Status</th>
                                <th class="mp-th text-left" style="padding:12px 16px;width:90px;background:#F9FAFB;">Kirim Revisi</th>
                                <th class="mp-th text-left" style="padding:12px 16px;width:150px;background:#F9FAFB;">Dokumen Revisi</th>
                                <th class="mp-th text-left" style="padding:12px 16px;width:140px;background:#F9FAFB;">Waktu Submit Revisi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $praktikans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $pr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $peng = $pengumpulanMapped[$pr->id] ?? null;
                                $st = $peng ? ($peng->status_pengumpulan ?? 'belum_dicek') : 'belum_kumpul';
                                $firstSub = $peng ? ($peng->riwayat->where('is_revision', false)->sortBy('created_at')->first() ?? $peng->riwayat->sortBy('created_at')->first()) : null;
                                $waktuSubmit = $firstSub ? $firstSub->created_at : ($peng ? $peng->created_at : null);
                                $latestRevision = $peng ? $peng->riwayat->where('is_revision', true)->sortByDesc('created_at')->first() : null;
                            ?>
                            <tr style="border-bottom:1px solid #EEF0F5;transition:background .1s;" onmouseover="this.style.background='#FAFBFC'" onmouseout="this.style.background=''">
                                <td style="padding:12px 16px;vertical-align:middle;color:#808897;font-size:12px;"><?php echo e($idx + 1); ?></td>
                                
                                
                                <td style="padding:12px 16px;vertical-align:middle;">
                                    <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                                        <div class="mp-av yellow" style="width:34px;height:34px;flex-shrink:0;"><?php echo e(strtoupper(substr($pr->user?->name ?? 'M', 0, 2))); ?></div>
                                        <div style="min-width:0;">
                                            <div style="font-size:13px;font-weight:600;color:#0D0D12;"><?php echo e($pr->user?->name ?? '—'); ?></div>
                                            <div style="font-size:11px;color:#666D80;"><?php echo e($pr->user?->email); ?></div>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($peng && $peng->catatan): ?>
                                            <div style="font-size:11px;color:#666D80;margin-top:2px;font-style:italic;" title="<?php echo e($peng->catatan); ?>"><?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.icon','data' => ['name' => 'message']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'message']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $attributes = $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $component = $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?> Mhs: <?php echo e(Str::limit($peng->catatan, 20)); ?></div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </td>

                                
                                <td style="padding:12px 16px;vertical-align:middle;font-size:12px;font-family:monospace;font-weight:600;color:#353849;letter-spacing:.03em;">
                                    <?php echo e($pr->user?->student?->student_number ?? '-'); ?>

                                </td>

                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kelompokRowspan[$idx] > 0): ?>
                                    <td rowspan="<?php echo e($kelompokRowspan[$idx]); ?>" style="padding:16px;text-align:center;vertical-align:middle;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;background:#FFF;font-size:18px;font-weight:700;color:#0D0D12;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pr->kelompok): ?>
                                            <?php echo e($pr->kelompok); ?>

                                        <?php else: ?>
                                            <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">—</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                
                                
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($shiftRowspan[$idx] > 0): ?>
                                    <td rowspan="<?php echo e($shiftRowspan[$idx]); ?>" style="padding:16px;text-align:center;vertical-align:middle;border-right:1px solid #DFE1E7;background:#FFF;font-size:18px;font-weight:700;color:#0D0D12;">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pr->shift): ?>
                                            <?php echo e($pr->shift); ?>

                                        <?php else: ?>
                                            <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">—</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                
                                <td style="padding:12px 16px;vertical-align:middle;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($peng && $firstSub && $firstSub->file_path): ?>
                                    <div style="display:flex;flex-direction:column;gap:4px;">
                                        <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($firstSub->file_path, 'eoffice')); ?>" target="_blank" style="font-size:12px;font-weight:600;color:#0B266E;text-decoration:none;" title="<?php echo e(basename($firstSub->file_path)); ?>">
                                            <?php echo e(Str::limit(basename($firstSub->file_path), 15)); ?>

                                        </a>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($firstSub->catatan): ?>
                                        <div style="font-size:11px;color:#666D80;margin-top:2px;font-style:italic;" title="<?php echo e($firstSub->catatan); ?>"><?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.icon','data' => ['name' => 'message']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'message']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $attributes = $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $component = $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?> Mhs: <?php echo e(Str::limit($firstSub->catatan, 20)); ?></div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($peng->riwayat->isNotEmpty()): ?>
                                        <div x-data="{ openRiwayat: false }" style="position:relative;">
                                            <button type="button" @click="openRiwayat = !openRiwayat" style="background:none;border:none;padding:0;font-size:10px;color:#6366F1;cursor:pointer;font-weight:600;display:inline-flex;align-items:center;gap:1px;">
                                                Riwayat (<?php echo e($peng->riwayat->count()); ?>)
                                                <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                                            </button>
                                            <div x-show="openRiwayat" @click.away="openRiwayat = false" style="position:absolute;top:100%;left:0;background:#fff;border:1px solid #DFE1E7;border-radius:8px;padding:8px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);z-index:100;min-width:220px;display:flex;flex-direction:column;gap:6px;margin-top:4px;">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $peng->riwayat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($r->file_path, 'eoffice')); ?>" target="_blank" style="font-size:11px;color:#353849;text-decoration:none;display:flex;flex-direction:column;padding:6px;border-radius:6px;transition:background .1s;" onmouseover="this.style.background='#F3F4F6'" onmouseout="this.style.background=''">
                                                    <div style="display:flex;justify-content:space-between;align-items:center;">
                                                        <span style="font-weight:700;color:#0B266E;">#<?php echo e($peng->riwayat->count() - $index); ?> <?php echo e($r->is_revision ? 'Revisi' : 'Pertama'); ?></span>
                                                        <span style="font-size:9px;color:#888;"><?php echo e($r->created_at->format('H:i')); ?></span>
                                                    </div>
                                                    <span style="font-size:9px;color:#A4ABB8;margin-top:2px;"><?php echo e($r->created_at->locale('id')->format('d M Y')); ?></span>
                                                </a>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <?php elseif($peng && $peng->file_path): ?>
                                    <div style="display:flex;flex-direction:column;gap:4px;">
                                        <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($peng->file_path, 'eoffice')); ?>" target="_blank" style="font-size:12px;font-weight:600;color:#0B266E;text-decoration:none;" title="<?php echo e(basename($peng->file_path)); ?>">
                                            <?php echo e(Str::limit(basename($peng->file_path), 15)); ?>

                                        </a>
                                    </div>
                                    <?php else: ?>
                                    <span style="font-size:12px;color:#A4ABB8;">Belum mengumpulkan</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                
                                <td style="padding:12px 16px;vertical-align:middle;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($waktuSubmit): ?>
                                    <span style="color:#0F6E56;font-weight:600;font-size:12px;">
                                        <?php echo e(\Carbon\Carbon::parse($waktuSubmit)->locale('id')->isoFormat('D MMM YYYY, HH:mm')); ?> WIB
                                    </span>
                                    <?php else: ?>
                                    <span style="font-size:12px;color:#999;">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                
                                <td style="padding:12px 16px;vertical-align:middle;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($peng): ?>
                                    <form method="POST" action="<?php echo e(route('eoffice.manprak.asprak.tugas.nilai', $peng->id)); ?>" style="display:flex;gap:4px;align-items:center;margin:0;">
                                        <?php echo csrf_field(); ?>
                                        <input type="number" name="nilai" min="0" max="100" step="1" placeholder="0-100"
                                               value="<?php echo e($peng->nilai ?? ''); ?>" class="mp-input" style="width:60px;font-size:12px;padding:4px 6px;">
                                        <button type="submit" class="mp-btn ghost sm" style="white-space:nowrap;padding:4px 6px;font-size:11px;"><?php echo e($st === 'acc' ? 'Edit' : 'ACC'); ?></button>
                                    </form>
                                    <?php else: ?>
                                    <span style="font-size:12px;color:#999;">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                
                                <td style="padding:12px 16px;vertical-align:middle;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($st === 'acc'): ?>
                                    <span class="mp-badge success sm" style="padding:2px 6px;font-size:10px;"><span class="dot"></span>ACC</span>
                                    <?php elseif($st === 'revisi'): ?>
                                    <span class="mp-badge error sm" style="padding:2px 6px;font-size:10px;"><span class="dot"></span>Revisi</span>
                                    <?php elseif($st === 'belum_kumpul'): ?>
                                    <span class="mp-badge neutral sm" style="padding:2px 6px;font-size:10px;"><span class="dot"></span>Belum Kumpul</span>
                                    <?php else: ?>
                                    <span class="mp-badge warning sm" style="padding:2px 6px;font-size:10px;"><span class="dot"></span>Menunggu</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                
                                <td style="padding:12px 16px;vertical-align:middle;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($st === 'acc' || $st === 'belum_kumpul'): ?>
                                    <span style="font-size:12px;color:#999;">—</span>
                                    <?php else: ?>
                                    <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-start;">
                                        <div x-data="{ showRevisiModal: false }">
                                            <button type="button" @click="showRevisiModal = true" class="mp-btn secondary sm" style="white-space:nowrap;padding:4px 8px;font-size:11px;">Revisi</button>
                
                                            <!-- Modal Overlay -->
                                            <div x-show="showRevisiModal" 
                                                 x-transition:enter="transition ease-out duration-200"
                                                 x-transition:enter-start="opacity-0"
                                                 x-transition:enter-end="opacity-100"
                                                 x-transition:leave="transition ease-in duration-150"
                                                 x-transition:leave-start="opacity-100"
                                                 x-transition:leave-end="opacity-0"
                                                 style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(15, 23, 42, 0.45);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;z-index:9999;padding:16px;"
                                                 @click.self="showRevisiModal = false">
                                                
                                                <!-- Modal Content -->
                                                <div style="background:#fff;border-radius:12px;width:100%;max-width:480px;box-shadow:0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);border:1px solid #DFE1E7;display:flex;flex-direction:column;overflow:hidden;">
                                                    
                                                    <!-- Modal Header -->
                                                    <div style="padding:16px 20px;border-bottom:1px solid #EEF0F5;display:flex;align-items:center;justify-content:between;">
                                                        <div style="font-size:15px;font-weight:700;color:#0D0D12;">Beri Catatan & Lampiran Revisi</div>
                                                        <button type="button" @click="showRevisiModal = false" style="background:none;border:none;cursor:pointer;color:#666D80;padding:4px;margin-left:auto;">
                                                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </div>
                
                                                    <!-- Form -->
                                                    <form method="POST" action="<?php echo e(route('eoffice.manprak.asprak.tugas.revisi', $peng->id)); ?>" enctype="multipart/form-data" style="margin:0;">
                                                        <?php echo csrf_field(); ?>
                                                        <div style="padding:20px;display:flex;flex-direction:column;gap:16px;text-align:left;">
                                                            <div>
                                                                <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">Catatan Revisi <span style="color:#DF1C41;">*</span></label>
                                                                <textarea name="catatan_revisi" required rows="4" placeholder="Tuliskan catatan perbaikan untuk mahasiswa..." class="mp-input" style="width:100%;resize:none;font-size:13px;"></textarea>
                                                            </div>
                                                            <div>
                                                                <label style="display:block;font-size:12px;font-weight:600;color:#353849;margin-bottom:6px;">Lampiran File Pendukung (Opsional)</label>
                                                                <input type="file" name="file_revisi" class="mp-input" style="width:100%;font-size:12px;">
                                                                <span style="font-size:11px;color:#666D80;display:block;margin-top:4px;">Format: PDF, DOCX, ZIP, RAR (maks. 10MB)</span>
                                                            </div>
                                                        </div>
                
                                                        <!-- Modal Footer -->
                                                        <div style="padding:16px 20px;background:#F9FAFB;border-top:1px solid #EEF0F5;display:flex;justify-content:end;gap:8px;">
                                                            <button type="button" @click="showRevisiModal = false" class="mp-btn secondary md">Batal</button>
                                                            <button type="submit" class="mp-btn primary md">Kirim Revisi</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($peng && ($peng->file_revisi_asprak || $peng->catatan_revisi)): ?>
                                        <div style="padding:6px 8px;background:#FEF2F2;border:1px solid #FEE2E2;border-radius:6px;font-size:11px;width:100%;box-sizing:border-box;">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($peng->file_revisi_asprak): ?>
                                            <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($peng->file_revisi_asprak, 'eoffice')); ?>" target="_blank" style="font-weight:600;color:#95122B;text-decoration:none;display:block;word-break:break-all;" title="<?php echo e(basename($peng->file_revisi_asprak)); ?>">
                                                <?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.icon','data' => ['name' => 'file']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'file']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $attributes = $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $component = $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?> <?php echo e(Str::limit(basename($peng->file_revisi_asprak), 12)); ?>

                                            </a>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($peng->catatan_revisi): ?>
                                            <div style="color:#7C1028;font-style:italic;margin-top:2px;word-break:break-word;" title="<?php echo e($peng->catatan_revisi); ?>"><?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.icon','data' => ['name' => 'message']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'message']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $attributes = $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $component = $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?>: <?php echo e(Str::limit($peng->catatan_revisi, 25)); ?></div>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                
                                <td style="padding:12px 16px;vertical-align:middle;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($latestRevision && $latestRevision->file_path): ?>
                                    <div style="display:flex;flex-direction:column;gap:2px;">
                                        <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($latestRevision->file_path, 'eoffice')); ?>" target="_blank" style="font-size:12px;font-weight:600;color:#0F6E56;text-decoration:none;" title="<?php echo e(basename($latestRevision->file_path)); ?>">
                                            <?php echo e(Str::limit(basename($latestRevision->file_path), 15)); ?>

                                        </a>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($latestRevision->catatan): ?>
                                        <div style="font-size:10px;color:#353849;font-style:italic;" title="<?php echo e($latestRevision->catatan); ?>"><?php if (isset($component)) { $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.ui.icon','data' => ['name' => 'message']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.ui.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'message']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $attributes = $__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__attributesOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995)): ?>
<?php $component = $__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995; ?>
<?php unset($__componentOriginal22b2b62d2a6bf157ddbb8ecb4421b995); ?>
<?php endif; ?> Mhs: <?php echo e(Str::limit($latestRevision->catatan, 25)); ?></div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <?php else: ?>
                                    <span style="font-size:12px;color:#999;">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>

                                
                                <td style="padding:12px 16px;vertical-align:middle;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($latestRevision): ?>
                                    <span style="color:#0F6E56;font-weight:600;font-size:12px;">
                                        <?php echo e(\Carbon\Carbon::parse($latestRevision->created_at)->locale('id')->isoFormat('D MMM YYYY, HH:mm')); ?> WIB
                                    </span>
                                    <?php else: ?>
                                    <span style="font-size:12px;color:#999;">—</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                            </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
<div class="mp-card" style="display:flex;align-items:center;justify-content:center;min-height:240px;padding:48px;">
    <div style="text-align:center;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 16px;display:block;opacity:0.6;">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <div style="font-size:14px;font-weight:600;color:#666D80;">Belum ada tugas. Buat tugas baru sekarang!</div>
        <a href="<?php echo e(route('eoffice.manprak.asprak.tugas.create')); ?>" class="mp-btn primary md" style="text-decoration:none;margin-top:16px;display:inline-flex;align-items:center;gap:6px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Tugas Baru
        </a>
    </div>
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum\asprak\tugas.blade.php ENDPATH**/ ?>