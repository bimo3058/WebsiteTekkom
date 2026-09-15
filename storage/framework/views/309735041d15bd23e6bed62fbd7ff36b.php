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


<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Tugas Praktikum</h1>
        <p class="mp-page-sub">
            Lihat dan kumpulkan tugas praktikum Anda
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($daftarPraktikan): ?> · <?php echo e($daftarPraktikan->praktikum?->nama); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </p>
    </div>
</div>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($semuaPraktikan) && $semuaPraktikan->count() > 1): ?>
<div style="display:flex;flex-wrap:wrap;gap:8px;" class="flex-shrink-0">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $semuaPraktikan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
    <a href="<?php echo e(route('eoffice.manprak.mahasiswa.tugas.index')); ?>?praktikum_id=<?php echo e($dp->praktikum_id); ?>"
       class="<?php echo e($dp->praktikum_id === $daftarPraktikan?->praktikum_id ? 'mp-btn primary sm' : 'mp-btn secondary sm'); ?>"
       style="text-decoration:none;">
        <?php echo e($dp->praktikum?->nama ?? 'Praktikum'); ?>

    </a>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Tugas</span>
    <span class="sec-rule"></span>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tugasList->isEmpty()): ?>
<div class="mp-card flex-shrink-0" style="padding:40px;text-align:center;">
    <svg class="mx-auto mb-3" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round">
        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
    </svg>
    <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada tugas yang diberikan.</div>
</div>
<?php else: ?>
<div class="flex flex-col gap-3 flex-1">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tugasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tugas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
    <?php
        $dl             = $tugas->deadline ? \Carbon\Carbon::parse($tugas->deadline) : null;
        $dlAcc          = $tugas->deadline_acc ? \Carbon\Carbon::parse($tugas->deadline_acc) : null;
        $lewat          = $dl && now()->gt($dl);
        $lewatAcc       = $dlAcc && now()->gt($dlAcc);
        
        $sisa           = $dl ? now()->diffInDays($dl, false) : null;
        $sisaAcc        = $dlAcc ? now()->diffInDays($dlAcc, false) : null;
        
        $pengumpulan    = $tugas->pengumpulan;
        $sudahKumpul    = !is_null($pengumpulan);
        $statusTugas    = $tugas->status_tugas ?? ($sudahKumpul ? $pengumpulan->status_pengumpulan : 'belum_dikumpul');
        
        // Cek keterlambatan pengumpulan pertama
        $firstSubmission = $sudahKumpul && $pengumpulan->riwayat ? $pengumpulan->riwayat->sortBy('created_at')->first() : null;
        $isLate          = $firstSubmission && $dl && \Carbon\Carbon::parse($firstSubmission->created_at)->gt($dl);
        
        // Batas pengumpulan mutlak
        $tenggatMutlak  = $dlAcc ?? $dl;
        $lewatMutlak    = $tenggatMutlak && now()->gt($tenggatMutlak);
        
        $deadlineColor  = ($lewat || ($sisa !== null && $sisa <= 2)) ? '#DF1C41' : '#666D80';
    ?>
    <div class="mp-card flex-shrink-0" style="padding:20px;" x-data="{ open: false }">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <div style="font-size:14px;font-weight:700;color:#0D0D12;"><?php echo e($tugas->judul); ?></div>
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statusTugas === 'acc'): ?>
                    <span class="mp-badge success sm"><span class="dot"></span>ACC<?php echo e($pengumpulan?->nilai ? ' — Nilai: '.$pengumpulan->nilai : ''); ?></span>
                    <?php elseif($statusTugas === 'revisi'): ?>
                    <span class="mp-badge error sm"><span class="dot"></span>Perlu Revisi</span>
                    <?php elseif($statusTugas === 'belum_dicek'): ?>
                    <span class="mp-badge warning sm"><span class="dot"></span>Dikumpul — Menunggu Penilaian</span>
                    <?php elseif($lewatMutlak): ?>
                    <span class="mp-badge error sm"><span class="dot"></span>Waktu Habis</span>
                    <?php elseif($lewat): ?>
                    <span class="mp-badge warning sm"><span class="dot"></span>Tenggat AC Terlewat (Masih Bisa Kumpul)</span>
                    <?php elseif($sisa !== null && $sisa <= 2): ?>
                    <span class="mp-badge warning sm"><span class="dot"></span>Segera!</span>
                    <?php else: ?>
                    <span class="mp-badge neutral sm"><span class="dot"></span>Belum Dikumpul</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isLate): ?>
                    <span class="mp-badge error sm" style="background:#FFF0F2;color:#DF1C41;border:1px solid #DF1C41;">Terlambat (Lewat Deadline AC)</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div style="font-size:12px;color:#666D80;">
                    Modul: <span style="font-weight:600;color:#353849;"><?php echo e($tugas->modul?->nama ?? '—'); ?></span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dl): ?>
                    · Deadline AC: <span style="font-weight:600;color:<?php echo e($deadlineColor); ?>;"><?php echo e($dl->format('d M Y, H:i')); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dlAcc): ?>
                    · Deadline ACC: <span style="font-weight:600;color:<?php echo e($lewatAcc ? '#DF1C41' : '#353849'); ?>;"><?php echo e($dlAcc->format('d M Y, H:i')); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($tugas->deskripsi)): ?>
                <div style="font-size:12px;color:#666D80;margin-top:4px;" class="line-clamp-2"><?php echo e($tugas->deskripsi); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="flex gap-2 flex-shrink-0">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$sudahKumpul && !$lewatMutlak): ?>
                <button @click="open = !open" class="mp-btn primary sm">Upload</button>
                <?php elseif($statusTugas === 'revisi' && !$lewatMutlak): ?>
                <button @click="open = !open" class="mp-btn warning sm">Kirim Ulang</button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sudahKumpul && $pengumpulan?->file_path): ?>
        <div class="mt-3 p-3 rounded-[8px]" style="background:#F4F6F8;border:1px solid #DFE1E7;">
            <div style="font-size:11px;font-weight:700;color:#353849;margin-bottom:6px;display:flex;align-items:center;justify-content:space-between;">
                <span>File yang Dikumpulkan:</span>
                <span style="font-size:10px;font-weight:500;color:#666D80;">Update terakhir: <?php echo e(\Carbon\Carbon::parse($pengumpulan->updated_at)->locale('id')->format('d M Y, H:i')); ?></span>
            </div>
            
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($pengumpulan->file_path, 'eoffice')); ?>" target="_blank" style="font-size:12px;font-weight:700;color:#0B266E;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Unduh File Terbaru
                </a>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pengumpulan->riwayat && $pengumpulan->riwayat->isNotEmpty()): ?>
                <div x-data="{ openRiwayat: false }" style="position:relative;">
                    <button type="button" @click="openRiwayat = !openRiwayat" class="mp-btn secondary sm" style="font-size:11px;padding:4px 8px;display:inline-flex;align-items:center;gap:2px;">
                        Riwayat Berkas (<?php echo e($pengumpulan->riwayat->count()); ?>)
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div x-show="openRiwayat" @click.away="openRiwayat = false" style="position:absolute;top:100%;right:0;background:#fff;border:1px solid #DFE1E7;border-radius:8px;padding:8px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);z-index:100;min-width:240px;display:flex;flex-direction:column;gap:6px;margin-top:4px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pengumpulan->riwayat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($r->file_path, 'eoffice')); ?>" target="_blank" style="font-size:11px;color:#353849;text-decoration:none;display:flex;flex-direction:column;padding:6px;border-radius:6px;transition:background .1s;text-align:left;" onmouseover="this.style.background='#F3F4F6'" onmouseout="this.style.background=''">
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <span style="font-weight:700;color:#0B266E;">#<?php echo e($pengumpulan->riwayat->count() - $index); ?> <?php echo e($r->is_revision ? 'Revisi' : 'Pertama'); ?></span>
                                <span style="font-size:9px;color:#888;"><?php echo e($r->created_at->format('H:i')); ?></span>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r->catatan): ?>
                            <span style="font-size:10px;color:#666D80;margin-top:2px;font-style:italic;" class="truncate">💬 <?php echo e($r->catatan); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <span style="font-size:9px;color:#A4ABB8;margin-top:2px;"><?php echo e($r->created_at->locale('id')->format('d M Y')); ?></span>
                        </a>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sudahKumpul && ($pengumpulan?->catatan_revisi || $pengumpulan?->file_revisi_asprak)): ?>
        <div class="mt-3 p-3 rounded-[8px]" style="background:#FADAE1;border:1px solid #DF1C41;">
            <div style="font-size:11px;font-weight:700;color:#7C1028;margin-bottom:2px;">Catatan Revisi dari Asisten Praktikum:</div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pengumpulan->catatan_revisi): ?>
            <div style="font-size:12px;color:#7C1028;margin-bottom:6px;"><?php echo e($pengumpulan->catatan_revisi); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pengumpulan->file_revisi_asprak): ?>
            <div style="margin-top:6px;">
                <a href="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($pengumpulan->file_revisi_asprak, 'eoffice')); ?>" target="_blank" style="font-size:11px;font-weight:700;color:#95122B;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Unduh File Lampiran Revisi
                </a>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div x-show="open" x-transition class="mt-4 pt-4" style="border-top:1px solid #ECEFF3;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($statusTugas === 'revisi'): ?>
            <div style="font-size:12px;font-weight:600;color:#D39C3D;margin-bottom:12px;">Kirim ulang file perbaikan:</div>
            <form method="POST" action="<?php echo e(route('eoffice.manprak.mahasiswa.tugas.kirim-ulang', $tugas->id)); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
            <?php else: ?>
            <form method="POST" action="<?php echo e(route('eoffice.manprak.mahasiswa.tugas.kumpul', $tugas->id)); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="flex flex-col gap-3">
                    <div>
                        <label class="block mb-1" style="font-size:12px;font-weight:600;color:#353849;">File Tugas <span style="color:#DF1C41;">*</span></label>
                        <input type="file" name="file" required class="mp-input w-full">
                        <div style="font-size:11px;color:#666D80;margin-top:4px;">Format: PDF, DOCX, ZIP, RAR (maks. 10MB)</div>
                    </div>
                    <div>
                        <label class="block mb-1" style="font-size:12px;font-weight:600;color:#353849;">Catatan (opsional)</label>
                        <textarea name="catatan" rows="2" placeholder="Catatan untuk asisten..."
                                  class="mp-input w-full" style="resize:none;"></textarea>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="open = false" class="mp-btn secondary md">Batal</button>
                        <button type="submit" class="mp-btn primary md">
                            <?php echo e($statusTugas === 'revisi' ? 'Kirim Perbaikan' : 'Kumpulkan'); ?>

                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum_backup_kode\mahasiswa\tugas.blade.php ENDPATH**/ ?>