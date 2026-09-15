<?php $peer = $peer ?? false; ?>

<?php $__env->startSection('title', $peer ? 'Peer Review' : 'Tipe Penilaian'); ?>
<?php $__env->startSection('content'); ?>
<div x-data="adminAssessmentConfig(<?php echo e($peer ? 'true' : 'false'); ?>,false)" class="space-y-6">
    <div><h1 class="text-3xl font-bold tracking-tight"><?php echo e($peer ? 'Peer Review' : 'Tipe Penilaian'); ?></h1><p class="mt-1 text-muted-foreground">Konfigurasi komponen penilaian untuk setiap periode.</p></div>
    <?php echo $__env->make('capstone::pages.admin.shared.toolbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('capstone::partials.loading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div x-show="!loading && !error" x-cloak class="overflow-hidden rounded-xl border bg-card">
        <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="border-b bg-muted/40"><tr><th class="p-4">Tipe Penilaian</th><th class="p-4">Komponen</th><th class="p-4">Jumlah</th><th class="p-4 text-right">Aksi</th></tr></thead><tbody><template x-for="item in visible" :key="item.id"><tr class="border-b"><td class="p-4 font-medium" x-text="item.name"></td><td class="p-4"><div class="flex flex-wrap gap-2"><template x-for="component in item.components" :key="component.id"><span class="rounded-full border px-2 py-1 text-xs" x-text="component.code || component.name"></span></template><span x-show="!item.components.length" class="text-muted-foreground">Belum dikonfigurasi</span></div></td><td class="p-4" x-text="item.components.length"></td><td class="p-4 text-right"><a :href="editUrl(item)" class="text-primary underline" x-text="period?.is_finalized?'Lihat konfigurasi':'Edit konfigurasi'"></a></td></tr></template></tbody></table></div>
        <p x-show="!filtered.length" class="p-8 text-center text-muted-foreground">Pilih periode untuk menampilkan konfigurasi.</p>
        <?php echo $__env->make('capstone::pages.admin.shared.pagination', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('capstone::layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules/Capstone\resources/views/pages/admin/configuration/index.blade.php ENDPATH**/ ?>