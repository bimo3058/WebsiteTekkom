<?php
    $selectedPeriodeId = request('periode_id', '');
?>
<div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; min-width:0;">
    
    <form method="GET" action="<?php echo e(route('banksoal.pendaftaran.alokasi-sesi.index')); ?>" id="searchForm" style="display:flex; align-items:center; gap:8px; margin:0;">
        <input type="hidden" name="perPage" value="<?php echo e(request('perPage', 10)); ?>">
        <input type="hidden" name="periode_id" value="<?php echo e($selectedPeriodeId); ?>" id="hiddenPeriodeId">

    </form>
    
    <button type="button" @click="openModal = true" <?php if(!$selectedPeriodeId): ?> disabled <?php endif; ?>
            class="flex items-center justify-center gap-1.5 h-[34px] px-3.5 border rounded-lg text-[12.5px] font-semibold whitespace-nowrap cursor-pointer transition-all box-border bg-primary text-white hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed border-transparent shadow-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        Tambah Sesi
    </button>
</div>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\alokasi-sesi\_search_filter.blade.php ENDPATH**/ ?>