
<?php ($items = $items ?? collect()); ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($items->count()): ?>
    <div style="display:flex; flex-direction:column; gap:8px;">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <a href="<?php echo e($a->public_url); ?>" target="_blank" rel="noopener"
               style="display:flex; align-items:center; gap:10px; text-decoration:none; background:#fafafa; border:1px solid #DFE1E7; border-radius:10px; padding:9px 12px; transition:all .15s;">
                <span style="width:34px; height:34px; flex-shrink:0; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; font-size:10px; font-weight:800; letter-spacing:.03em; color:#fff; background:<?php echo e($a->isImage() ? '#0ea5e9' : '#dc2626'); ?>;">
                    <?php echo e($a->isImage() ? 'IMG' : 'PDF'); ?>

                </span>
                <span style="flex:1; min-width:0;">
                    <span style="display:block; font-size:.87rem; font-weight:700; color:#0D0D12; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?php echo e($a->judul); ?></span>
                    <span style="display:block; font-size:.72rem; color:#666D80; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?php echo e($a->nama_file); ?></span>
                </span>
                <span style="flex-shrink:0; font-size:.8rem; font-weight:600; color:#0B266E;">Lihat&nbsp;↗</span>
            </a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
<?php else: ?>
    <div style="font-size:.82rem; color:#666D80;">Belum ada dokumen aturan yang diunggah admin.</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\ManajemenMahasiswa\resources\views\verifikasi\_aturan_links.blade.php ENDPATH**/ ?>