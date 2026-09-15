
<p class="panel-title">Push Notifications</p>
<p class="panel-sub">Get alerts for anything changes</p>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
    ['label'=>'Transaction Confirmation','desc'=>'Sent automatically to the customer after they place their order.','on'=>true],
    ['label'=>'Transaction Edited','desc'=>'Sent to the customer after their order is edited (if you select this option).','on'=>false],
    ['label'=>'Transaction Invoice','desc'=>'Sent to the customer when the order has an outstanding balance.','on'=>true],
    ['label'=>'Transaction Cancelled','desc'=>'Sent automatically to the customer if their order is cancelled (if you select this option).','on'=>true],
    ['label'=>'Transaction Refund','desc'=>'Sent automatically to the customer if their order is refunded (if you select this option).','on'=>true],
    ['label'=>'Payment Error','desc'=>"Sent automatically to the customer if their payment can't be processed during checkout.",'on'=>false],
]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
<div style="display:flex;align-items:flex-start;justify-content:space-between;padding:16px 0;border-bottom:1px solid #f1f5f9;">
    <div style="padding-right:24px;">
        <p style="font-size:13px;font-weight:600;color:#0f172a;margin-bottom:2px;"><?php echo e($n['label']); ?></p>
        <p style="font-size:12px;color:#64748b;line-height:1.5;"><?php echo e($n['desc']); ?></p>
    </div>
    <div class="toggle-track <?php echo e($n['on'] ? 'on' : 'off'); ?>" style="flex-shrink:0;margin-top:2px;">
        <div class="toggle-thumb"></div>
    </div>
</div>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\resources\views\profile\partials\settings-panel-notifikasi.blade.php ENDPATH**/ ?>