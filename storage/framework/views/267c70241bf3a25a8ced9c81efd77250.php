<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Pengumpulan Tugas']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Pengumpulan Tugas']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>



<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title"><?php echo e($tugas->judul); ?></h1>
            <span class="mp-badge success sm"><span class="dot"></span>Asisten Praktikum</span>
        </div>
        <p class="mp-page-sub"><?php echo e($tugas->modul?->nama); ?> · <?php echo e($tugas->modul?->praktikum?->nama); ?></p>
    </div>
    <div class="mp-page-actions">
        <a href="<?php echo e(route('eoffice.manprak.asprak.tugas.index')); ?>" class="mp-btn secondary md" style="text-decoration:none;">Kembali</a>
    </div>
</div>


<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:12px;margin-bottom:24px;">
    <?php
        $dl = $tugas->deadline ? \Carbon\Carbon::parse($tugas->deadline) : null;
        $lewat = $dl && now()->gt($dl);
    ?>
    <div class="mp-card" style="padding:16px;border:1px solid #DFE1E7;">
        <div style="font-size:11px;font-weight:700;color:#666D80;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Deadline</div>
        <div style="font-size:14px;font-weight:700;color:<?php echo e($lewat ? '#999' : '#0D0D12'); ?>;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dl): ?>
                <?php echo e($dl->locale('id')->format('d M Y, H:i')); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lewat): ?>
                    <span style="font-size:11px;color:#666D80;display:block;margin-top:4px;">(Berakhir)</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php else: ?>
                <span style="color:#999;">Tidak ada deadline</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <div class="mp-card" style="padding:16px;border:1px solid #E8F5E9;background:linear-gradient(135deg, #F1F8F5 0%, #E8F5E9 100%);">
        <div style="font-size:11px;font-weight:700;color:#0F6E56;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Sudah Dikumpul</div>
        <div style="font-size:18px;font-weight:700;color:#0F6E56;"><?php echo e($pengumpulan->count()); ?></div>
    </div>

    <div class="mp-card" style="padding:16px;border:1px solid #FFE0B2;background:linear-gradient(135deg, #FFF8E8 0%, #FFF3E0 100%);">
        <div style="font-size:11px;font-weight:700;color:#854F0B;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Menunggu Penilaian</div>
        <div style="font-size:18px;font-weight:700;color:#D39C3D;"><?php echo e($pengumpulan->where('status_pengumpulan', '!=', 'acc')->where('status_pengumpulan', '!=', 'revisi')->count()); ?></div>
    </div>

    <div class="mp-card" style="padding:16px;border:1px solid #D4DBFF;background:linear-gradient(135deg, #F0F4FF 0%, #E8EEFF 100%);">
        <div style="font-size:11px;font-weight:700;color:#185FA5;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Sudah Dinilai</div>
        <div style="font-size:18px;font-weight:700;color:#0B266E;"><?php echo e($pengumpulan->where('nilai', '!=', null)->count()); ?></div>
    </div>
</div>


<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Pengumpulan</span>
    <span class="sec-rule"></span>
    <span class="mp-badge neutral sm"><?php echo e($pengumpulan->count()); ?> / <?php echo e($praktikans->count()); ?> dikumpulkan</span>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikans->count() > 0): ?>
<div class="mp-card">
    <div class="mp-card-header">
        <span class="mp-card-title">Pengumpulan Tugas</span>
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

    <div style="overflow-x:auto;">
        <table class="mp-table" style="width:100%;border-collapse:collapse;min-width:1250px;font-size:13px;text-align:left;">
            <thead>
                <tr style="background:#F9FAFB;border-bottom:1px solid #DFE1E7;">
                    <th class="mp-th text-left" style="padding:14px 16px;width:40px;">#</th>
                    <th class="mp-th text-left" style="padding:14px 16px;width:200px;">Mahasiswa</th>
                    <th class="mp-th text-left" style="padding:14px 16px;width:120px;">NIM</th>
                    <th class="mp-th text-center" style="padding:14px 16px;width:100px;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;">Kelompok</th>
                    <th class="mp-th text-center" style="padding:14px 16px;width:100px;border-right:1px solid #DFE1E7;">Shift</th>
                    <th class="mp-th text-left" style="padding:14px 16px;width:150px;">Dokumen</th>
                    <th class="mp-th text-left" style="padding:14px 16px;width:140px;">Waktu Submit</th>
                    <th class="mp-th text-left" style="padding:14px 16px;width:110px;">Nilai</th>
                    <th class="mp-th text-left" style="padding:14px 16px;width:90px;">Status</th>
                    <th class="mp-th text-left" style="padding:14px 16px;width:90px;">Kirim Revisi</th>
                    <th class="mp-th text-left" style="padding:14px 16px;width:150px;">Dokumen Revisi</th>
                    <th class="mp-th text-left" style="padding:14px 16px;width:140px;">Waktu Submit Revisi</th>
                </tr>
            </thead>
            <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $praktikans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $pr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $p = $pengumpulan[$pr->id] ?? null;
                    $st = $p ? ($p->status_pengumpulan ?? 'belum_dicek') : 'belum_kumpul';
                    $waktuSubmit = null;
                    $latestRevision = null;
                    if ($p) {
                        $firstSub = $p->riwayat->where('is_revision', false)->sortBy('created_at')->first() ?? $p->riwayat->sortBy('created_at')->first();
                        $waktuSubmit = $firstSub ? $firstSub->created_at : $p->created_at;
                        $latestRevision = $p->riwayat->where('is_revision', true)->sortByDesc('created_at')->first();
                    }
                ?>
                <tr style="border-bottom:1px solid #EEF0F5;transition:background .1s;" onmouseover="this.style.background='#FAFBFC'" onmouseout="this.style.background=''">
                    <td style="padding:14px 16px;vertical-align:middle;color:#808897;font-size:12px;"><?php echo e($idx + 1); ?></td>
                    
                    
                    <td style="padding:14px 16px;vertical-align:middle;">
                        <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                            <div class="mp-av yellow" style="width:34px;height:34px;flex-shrink:0;"><?php echo e(strtoupper(substr($pr->user?->name ?? 'M', 0, 2))); ?></div>
                            <div style="min-width:0;">
                                <div style="font-size:13px;font-weight:600;color:#0D0D12;"><?php echo e($pr->user?->name ?? '—'); ?></div>
                                <div style="font-size:11px;color:#666D80;"><?php echo e($pr->user?->email); ?></div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p && $p->catatan): ?>
                                <div style="font-size:11px;color:#666D80;margin-top:2px;font-style:italic;" title="<?php echo e($p->catatan); ?>">💬 Mhs: <?php echo e(Str::limit($p->catatan, 20)); ?></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </td>

                    
                    <td style="padding:14px 16px;vertical-align:middle;font-size:12px;font-family:monospace;font-weight:600;color:#353849;letter-spacing:.03em;">
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

                    
                    <td style="padding:14px 16px;vertical-align:middle;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p && $firstSub && $firstSub->file_path): ?>
                        <div style="display:flex;flex-direction:column;gap:4px;">
                            <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($firstSub->file_path, 'eoffice')); ?>" target="_blank" style="font-size:12px;font-weight:600;color:#0B266E;text-decoration:none;" title="<?php echo e(basename($firstSub->file_path)); ?>">
                                <?php echo e(Str::limit(basename($firstSub->file_path), 15)); ?>

                            </a>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($firstSub->catatan): ?>
                            <div style="font-size:11px;color:#666D80;margin-top:2px;font-style:italic;" title="<?php echo e($firstSub->catatan); ?>">💬 Mhs: <?php echo e(Str::limit($firstSub->catatan, 20)); ?></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->riwayat->isNotEmpty()): ?>
                            <div x-data="{ openRiwayat: false }" style="position:relative;">
                                <button type="button" @click="openRiwayat = !openRiwayat" style="background:none;border:none;padding:0;font-size:10px;color:#6366F1;cursor:pointer;font-weight:600;display:inline-flex;align-items:center;gap:1px;">
                                    Riwayat (<?php echo e($p->riwayat->count()); ?>)
                                    <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                                </button>
                                <div x-show="openRiwayat" @click.away="openRiwayat = false" style="position:absolute;top:100%;left:0;background:#fff;border:1px solid #DFE1E7;border-radius:8px;padding:8px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);z-index:100;min-width:220px;display:flex;flex-direction:column;gap:6px;margin-top:4px;">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $p->riwayat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($r->file_path, 'eoffice')); ?>" target="_blank" style="font-size:11px;color:#353849;text-decoration:none;display:flex;flex-direction:column;padding:6px;border-radius:6px;transition:background .1s;" onmouseover="this.style.background='#F3F4F6'" onmouseout="this.style.background=''">
                                        <div style="display:flex;justify-content:space-between;align-items:center;">
                                            <span style="font-weight:700;color:#0B266E;">#<?php echo e($p->riwayat->count() - $index); ?> <?php echo e($r->is_revision ? 'Revisi' : 'Pertama'); ?></span>
                                            <span style="font-size:9px;color:#888;"><?php echo e($r->created_at->format('H:i')); ?></span>
                                        </div>
                                        <span style="font-size:9px;color:#A4ABB8;margin-top:2px;"><?php echo e($r->created_at->locale('id')->format('d M Y')); ?></span>
                                    </a>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <?php elseif($p && $p->file_path): ?>
                        <div style="display:flex;flex-direction:column;gap:4px;">
                            <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($p->file_path, 'eoffice')); ?>" target="_blank" style="font-size:12px;font-weight:600;color:#0B266E;text-decoration:none;" title="<?php echo e(basename($p->file_path)); ?>">
                                <?php echo e(Str::limit(basename($p->file_path), 15)); ?>

                            </a>
                        </div>
                        <?php else: ?>
                        <span style="font-size:12px;color:#A4ABB8;">Belum mengumpulkan</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>

                    
                    <td style="padding:14px 16px;vertical-align:middle;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($waktuSubmit): ?>
                        <span style="color:#0F6E56;font-weight:600;font-size:12px;">
                            <?php echo e(\Carbon\Carbon::parse($waktuSubmit)->locale('id')->isoFormat('D MMMM YYYY, HH:mm')); ?> WIB
                        </span>
                        <?php else: ?>
                        <span style="font-size:12px;color:#999;">—</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>

                    
                    <td style="padding:14px 16px;vertical-align:middle;">
                        <?php
                            $njValue = $nilaiJenis[$pr->id]?->nilai ?? null;
                            $currentNilai = $p ? $p->nilai : null;
                            $displayNilai = $currentNilai ?? $njValue ?? '';
                        ?>
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p): ?>
                        <form method="POST" action="<?php echo e(route('eoffice.manprak.asprak.tugas.nilai', $p->id)); ?>" style="display:flex;gap:4px;align-items:center;margin:0;">
                            <?php echo csrf_field(); ?>
                            <input type="number" name="nilai" min="0" max="100" step="0.5" placeholder="0-100"
                                   value="<?php echo e($displayNilai); ?>" class="mp-input" style="width:60px;font-size:12px;padding:4px 6px;">
                            <button type="submit" class="mp-btn ghost sm" style="white-space:nowrap;padding:4px 6px;font-size:11px;"><?php echo e($st === 'acc' ? 'Edit' : 'ACC'); ?></button>
                        </form>
                        <?php else: ?>
                        <form method="POST" action="<?php echo e(route('eoffice.manprak.asprak.tugas.nilai-jenis', $tugas->id)); ?>" style="display:flex;gap:4px;align-items:center;margin:0;">
                            <?php echo csrf_field(); ?>
                            <input type="number" name="nilai[<?php echo e($pr->id); ?>][nilai_jenis]" min="0" max="100" step="0.5" placeholder="0-100"
                                   value="<?php echo e($displayNilai); ?>" class="mp-input" style="width:60px;font-size:12px;padding:4px 6px;">
                            <button type="submit" class="mp-btn ghost sm" style="white-space:nowrap;padding:4px 6px;font-size:11px;color:#0B266E;">Simpan</button>
                        </form>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>

                    
                    <td style="padding:14px 16px;vertical-align:middle;">
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

                    
                    <td style="padding:14px 16px;vertical-align:middle;">
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
                                        <form method="POST" action="<?php echo e(route('eoffice.manprak.asprak.tugas.revisi', $p->id)); ?>" enctype="multipart/form-data" style="margin:0;">
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

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p && ($p->file_revisi_asprak || $p->catatan_revisi)): ?>
                            <div style="padding:6px 8px;background:#FEF2F2;border:1px solid #FEE2E2;border-radius:6px;font-size:11px;width:100%;box-sizing:border-box;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->file_revisi_asprak): ?>
                                <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($p->file_revisi_asprak, 'eoffice')); ?>" target="_blank" style="font-weight:600;color:#95122B;text-decoration:none;display:block;word-break:break-all;" title="<?php echo e(basename($p->file_revisi_asprak)); ?>">
                                    📄 <?php echo e(Str::limit(basename($p->file_revisi_asprak), 12)); ?>

                                </a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->catatan_revisi): ?>
                                <div style="color:#7C1028;font-style:italic;margin-top:2px;word-break:break-word;" title="<?php echo e($p->catatan_revisi); ?>">💬: <?php echo e(Str::limit($p->catatan_revisi, 25)); ?></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>

                    
                    <td style="padding:14px 16px;vertical-align:middle;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($latestRevision && $latestRevision->file_path): ?>
                        <div style="display:flex;flex-direction:column;gap:2px;">
                            <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($latestRevision->file_path, 'eoffice')); ?>" target="_blank" style="font-size:12px;font-weight:600;color:#0F6E56;text-decoration:none;" title="<?php echo e(basename($latestRevision->file_path)); ?>">
                                <?php echo e(Str::limit(basename($latestRevision->file_path), 15)); ?>

                            </a>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($latestRevision->catatan): ?>
                            <div style="font-size:10px;color:#353849;font-style:italic;" title="<?php echo e($latestRevision->catatan); ?>">💬 Mhs: <?php echo e(Str::limit($latestRevision->catatan, 25)); ?></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <?php else: ?>
                        <span style="font-size:12px;color:#999;">—</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>

                    
                    <td style="padding:14px 16px;vertical-align:middle;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($latestRevision): ?>
                        <span style="color:#0F6E56;font-weight:600;font-size:12px;">
                            <?php echo e(\Carbon\Carbon::parse($latestRevision->created_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm')); ?> WIB
                        </span>
                        <?php else: ?>
                        <span style="font-size:12px;color:#999;">—</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="12" style="padding:48px;text-align:center;color:#666D80;">
                        Belum ada praktikan terdaftar.
                    </td>
                </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>
<div class="mp-card" style="display:flex;align-items:center;justify-content:center;min-height:240px;padding:48px;">
    <div style="text-align:center;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 16px;display:block;opacity:0.6;">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <div style="font-size:14px;font-weight:600;color:#666D80;">Belum ada praktikan terdaftar.</div>
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
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum_backup_kode\asprak\tugas-pengumpulan.blade.php ENDPATH**/ ?>