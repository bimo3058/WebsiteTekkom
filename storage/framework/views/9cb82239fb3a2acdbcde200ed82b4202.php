<?php
    $pertanyaan    = $kompreJawaban->pertanyaan;
    $terpilihId    = $kompreJawaban->jawaban_dipilih;
    $opsiTerpilih  = $kompreJawaban->opsiTerpilih;
    $isBenar       = $opsiTerpilih && $opsiTerpilih->is_benar;
    $isTidakDijawab = !$terpilihId;
?>

<div class="bg-white rounded-xl border border-gray-200 mb-3 shadow-sm">
    
    <div class="px-4 py-3 border-b border-gray-100">
        <div class="flex items-start justify-between mb-1.5">
            <div class="flex items-center gap-2">
                <span class="text-[14px] font-bold text-gray-900"><?php echo e($kompreJawaban->urutan_soal); ?>.</span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pertanyaan?->cpl && (!isset($hideCplBadge) || !$hideCplBadge)): ?>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-gray-50 text-gray-500 border border-gray-200"
                          title="<?php echo e($pertanyaan->cpl->deskripsi); ?>">
                        <?php echo e(preg_replace('/^CPL-0*/', 'CPL ', $pertanyaan->cpl->kode)); ?>

                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isTidakDijawab): ?>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                    Kosong
                </span>
            <?php elseif($isBenar): ?>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-200/60">
                    Benar
                </span>
            <?php else: ?>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-rose-50 text-rose-600 border border-rose-200/60">
                    Salah
                </span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="prose max-w-none text-[13.5px] text-gray-800 leading-snug ml-5 prose-p:mt-0 prose-p:mb-0">
            <?php echo $pertanyaan->soal ?? '<span class="text-gray-400 italic">Teks soal tidak tersedia.</span>'; ?>

        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pertanyaan?->jawaban): ?>
        <div class="px-4 py-3 bg-gray-50/30 rounded-b-xl">
            <div class="ml-5 space-y-1.5">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pertanyaan->jawaban; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opsiIdx => $opsi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php
                        $isSelected = $terpilihId == $opsi->id;
                        $isCorrect  = $opsi->is_benar;
                        $letter     = $opsi->opsi ?? chr(65 + $opsiIdx);

                        if ($isCorrect) {
                            $optBorder  = 'border border-emerald-300';
                            $optBg      = 'bg-emerald-50/50';
                            $letterCls  = 'border border-emerald-200 text-emerald-600 bg-white';
                            $textCls    = 'text-gray-900 font-medium';
                            $opacity    = '';
                        } elseif ($isSelected && !$isCorrect) {
                            $optBorder  = 'border border-rose-200';
                            $optBg      = 'bg-rose-50/50';
                            $letterCls  = 'border border-rose-200 text-rose-600 bg-white';
                            $textCls    = 'text-gray-900 font-medium';
                            $opacity    = '';
                        } else {
                            $optBorder  = 'border border-gray-200';
                            $optBg      = 'bg-white hover:bg-gray-50/80';
                            $letterCls  = 'border border-gray-200 text-gray-500 bg-gray-50/50';
                            $textCls    = 'text-gray-600';
                            $opacity    = 'opacity-80';
                        }
                    ?>
                    <div class="flex items-start px-2.5 py-1.5 rounded-lg <?php echo e($optBorder); ?> <?php echo e($optBg); ?> <?php echo e($opacity); ?> transition-colors">
                        <div class="w-6 h-6 rounded flex items-center justify-center font-bold text-[11px] flex-shrink-0 mr-2.5 <?php echo e($letterCls); ?>">
                            <?php echo e($letter); ?>

                        </div>
                        <div class="flex-1 flex items-start justify-between gap-2">
                            <div class="<?php echo e($textCls); ?> text-[13px] leading-snug pt-0.5">
                                <?php echo $opsi->deskripsi; ?>

                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0 pt-0.5">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isCorrect && $isSelected): ?>
                                    <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-100/60 border border-emerald-200/60 px-2 py-0.5 rounded whitespace-nowrap">Kunci & Pilihan Anda</span>
                                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <?php elseif($isCorrect): ?>
                                    <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-100/60 border border-emerald-200/60 px-2 py-0.5 rounded whitespace-nowrap">Kunci Jawaban</span>
                                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <?php elseif($isSelected): ?>
                                    <span class="text-[10px] font-semibold text-rose-600 bg-rose-100/60 border border-rose-200/60 px-2 py-0.5 rounded whitespace-nowrap">Pilihan Anda</span>
                                    <svg class="w-4 h-4 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\admin\cbt\partials\_jawaban-card.blade.php ENDPATH**/ ?>