<?php $__env->startSection('title','Edit Periode'); ?>
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('capstone::pages.admin.periods.wizard', ['periodId'=>$pageParams['id']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('capstone::layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules/Capstone\resources/views/pages/admin/periods/_id_/edit.blade.php ENDPATH**/ ?>