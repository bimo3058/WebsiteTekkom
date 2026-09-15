<?php $__env->startSection('content'); ?>
    <h1>Hello Mahasiswa</h1>

    <p>Module: <?php echo config('banksoal.name'); ?></p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('banksoal::layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\dashboard\mahasiswa.blade.php ENDPATH**/ ?>