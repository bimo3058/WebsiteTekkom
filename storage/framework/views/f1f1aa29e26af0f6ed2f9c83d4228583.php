<?php
    $isPaginated = $jadwals instanceof \Illuminate\Pagination\LengthAwarePaginator;
    $from = $isPaginated ? ($jadwals->firstItem() ?? 0) : ($jadwals->count() > 0 ? 1 : 0);
    $to   = $isPaginated ? ($jadwals->lastItem() ?? 0) : $jadwals->count();
    $totalPageData = $isPaginated ? $jadwals->total() : $jadwals->count();
    $currentPage = $isPaginated ? $jadwals->currentPage() : 1;
    $lastPage    = $isPaginated ? $jadwals->lastPage() : 1;
    $currentPerPage = request('perPage', 10);
    $perPageOptions = [5, 10, 25, 50, 100];
?>
<div style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; background:#fff; border-top:1px solid var(--c-border); flex-wrap:wrap; gap:10px;">

    
    <div style="display:flex; align-items:center; gap:10px;" x-data="{ open: false }">
        <div style="display:flex; align-items:center; gap:6px;">
            <span style="font-size:12px; font-weight:500; color:var(--c-fg-muted);">Per page</span>
            <div style="position:relative;">
                <button type="button" @click="open = !open" @click.outside="open = false"
                        class="flex flex-row items-center justify-center gap-1.5 h-[28px] px-2 bg-white border rounded-md text-xs font-semibold whitespace-nowrap cursor-pointer transition-colors box-border"
                        style="border-color:var(--c-border); color:var(--c-fg); font-family:inherit;"
                        :style="open ? 'border-color:var(--c-primary);' : ''">
                    <span class="leading-none"><?php echo e($currentPerPage); ?></span>
                    <svg class="shrink-0 transition-transform duration-150 w-3 h-3" :class="open ? 'rotate-180' : ''" style="color:var(--c-fg-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg>
                </button>

                <div x-show="open" x-cloak
                     x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     class="absolute bottom-[calc(100%+5px)] left-0 bg-white border rounded-lg shadow-lg min-w-[80px] z-50 overflow-hidden"
                     style="border-color:var(--c-border); display:none;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $perPageOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <a href="<?php echo e(request()->fullUrlWithQuery(['perPage' => $opt, 'page' => 1])); ?>"
                       style="display:block; padding:7px 14px; font-size:12px; font-weight:<?php echo e($currentPerPage == $opt ? '700' : '500'); ?>; color:<?php echo e($currentPerPage == $opt ? 'var(--c-primary)' : 'var(--c-fg-sec)'); ?>; text-decoration:none; background:<?php echo e($currentPerPage == $opt ? 'rgba(11,38,110,0.06)' : 'transparent'); ?>; transition:background .12s;"
                       onmouseover="if(<?php echo e($currentPerPage); ?> !== <?php echo e($opt); ?>) this.style.background='var(--c-bg)'" onmouseout="if(<?php echo e($currentPerPage); ?> !== <?php echo e($opt); ?>) this.style.background='transparent'">
                        <?php echo e($opt); ?>

                    </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
        </div>

        <div style="width:1px; height:14px; background:var(--c-border);"></div>

        
        <span style="font-size:12px; color:var(--c-fg-sec);">
            Showing <strong style="color:var(--c-fg); font-weight:700;"><?php echo e($from); ?></strong> to <strong style="color:var(--c-fg); font-weight:700;"><?php echo e($to); ?></strong> of <strong style="color:var(--c-fg); font-weight:700;"><?php echo e(number_format($totalPageData)); ?></strong> results
        </span>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lastPage > 1): ?>
    <div style="display:flex; align-items:center; gap:4px;">
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($currentPage > 1): ?>
        <a href="<?php echo e($isPaginated ? $jadwals->appends(request()->query())->previousPageUrl() : '#'); ?>"
           style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;"
           onmouseover="this.style.background='var(--c-bg)'; this.style.borderColor='var(--c-border-strong)'"
           onmouseout="this.style.background='#fff'; this.style.borderColor='var(--c-border)'">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
        </a>
        <?php else: ?>
        <span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid #F3F4F6; border-radius:6px; background:#FAFAFA; color:#D1D5DB; cursor:not-allowed;">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>
        </span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php
            $range = 2;
            $start = max(1, $currentPage - $range);
            $end   = min($lastPage, $currentPage + $range);
        ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($start > 1): ?>
        <a href="<?php echo e($isPaginated ? $jadwals->appends(request()->query())->url(1) : '#'); ?>"
           style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; font-size:12px; font-weight:500; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;"
           onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'">1</a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($start > 2): ?>
        <span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; font-size:12px; color:var(--c-fg-muted);">…</span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($p = $start; $p <= $end; $p++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <a href="<?php echo e($isPaginated ? $jadwals->appends(request()->query())->url($p) : '#'); ?>"
           style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border-radius:6px; font-size:12px; font-weight:<?php echo e($p === $currentPage ? '700' : '500'); ?>; text-decoration:none; transition:all .15s;
               <?php echo e($p === $currentPage ? 'background:var(--c-primary); color:#fff; border:1px solid var(--c-primary); box-shadow:0 2px 6px rgba(94,83,244,0.3);' : 'border:1px solid var(--c-border); background:#fff; color:var(--c-fg-sec);'); ?>">
            <?php echo e($p); ?>

        </a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($end < $lastPage): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($end < $lastPage - 1): ?>
        <span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; font-size:12px; color:var(--c-fg-muted);">…</span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <a href="<?php echo e($isPaginated ? $jadwals->appends(request()->query())->url($lastPage) : '#'); ?>"
           style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; font-size:12px; font-weight:500; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;"
           onmouseover="this.style.background='var(--c-bg)'" onmouseout="this.style.background='#fff'"><?php echo e($lastPage); ?></a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($currentPage < $lastPage): ?>
        <a href="<?php echo e($isPaginated ? $jadwals->appends(request()->query())->nextPageUrl() : '#'); ?>"
           style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid var(--c-border); border-radius:6px; background:#fff; color:var(--c-fg-sec); text-decoration:none; transition:all .15s;"
           onmouseover="this.style.background='var(--c-bg)'; this.style.borderColor='var(--c-border-strong)'"
           onmouseout="this.style.background='#fff'; this.style.borderColor='var(--c-border)'">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg>
        </a>
        <?php else: ?>
        <span style="width:28px; height:28px; display:flex; align-items:center; justify-content:center; border:1px solid #F3F4F6; border-radius:6px; background:#FAFAFA; color:#D1D5DB; cursor:not-allowed;">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg>
        </span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\alokasi-sesi\_pagination.blade.php ENDPATH**/ ?>