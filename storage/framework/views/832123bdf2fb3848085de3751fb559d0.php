<div class="soal-container">
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($soal): ?>
        <div class="mb-4">
            <h3 class="text-lg font-bold">Mata Kuliah: <?php echo e($soal->mk_nama); ?> (<?php echo e($soal->mk_kode); ?>)</h3>
            <p class="text-sm text-gray-600">CPL: <?php echo e($soal->cpl_kode); ?> - <?php echo e($soal->cpl_deskripsi); ?></p>
            <div class="mt-2 text-md prose prose-sm max-w-none"><?php echo $soal->soal; ?></div>
            <p class="text-sm text-gray-500">Bobot: <?php echo e($soal->bobot); ?> | Kesulitan: <?php echo e($soal->kesulitan); ?></p>
        </div>

        
        <div class="jawaban-container">
            <h4 class="font-semibold mb-2">Pilihan Jawaban:</h4>
            <ul class="space-y-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $jawaban; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opsi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <li class="p-2 border rounded <?php echo e($opsi->is_benar ? 'bg-green-100 border-green-500' : 'bg-gray-50 border-gray-200'); ?>">
                        <span class="font-bold"><?php echo e($opsi->opsi); ?>.</span> 
                        <?php echo e($opsi->deskripsi); ?>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($opsi->is_benar): ?>
                            <span class="text-green-600 text-sm font-bold ml-2">(Jawaban Benar)</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </li>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </ul>
        </div>
    <?php else: ?>
        <p class="text-red-500">Soal tidak ditemukan.</p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div><?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\gpm\validasi\index.blade.php ENDPATH**/ ?>