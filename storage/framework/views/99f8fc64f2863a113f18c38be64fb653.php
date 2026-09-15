<?php $__env->startSection('title','My Schedule'); ?>
<?php $__env->startSection('content'); ?>
<div x-data="capstoneSchedules" class="space-y-6"><div><h1 class="text-3xl font-bold tracking-tight">My Schedule</h1><p class="text-muted-foreground">View your bimbingan sessions, seminar proposals, expo events, and TA defense schedule.</p></div><?php echo $__env->make('capstone::partials.loading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><div x-show="!loading && !error" x-cloak><?php echo $__env->make('capstone::partials.schedule-calendar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('capstone::layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\Capstone\tests/../resources/views/pages\mahasiswa\schedule\index.blade.php ENDPATH**/ ?>