<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['praktikum', 'activeTab' => '']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['praktikum', 'activeTab' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<div x-data="{ st: 0 }" x-init="
        const box = document.querySelector('.mp-box-body');
        if (box) {
            let ticking = false;
            box.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(() => {
                        st = box.scrollTop;
                        ticking = false;
                    });
                    ticking = true;
                }
            });
        }
     " class="sticky z-20 bg-white"
    style="top: -200px; margin: -20px -24px 0 -24px; padding: 20px 24px 0 24px; border-bottom: 1px solid var(--c-border); margin-bottom: 16px;">

    
    <div
        style="position: relative; overflow: hidden; border-radius: 12px; border: 1px solid var(--c-border); height: 240px; margin-bottom: 4px; transform: translateZ(0);">

        
        <div class="absolute inset-0 flex items-center justify-center bg-[#F3F4F6]" style="border-radius: inherit;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="#828896"
                :style="`transform: scale(${Math.max(0.6, 1 - (st / 200) * 0.4)}); opacity: ${Math.max(0, 1 - (st / 150))};`">
                <path
                    d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
            </svg>
        </div>

        
        <div class="absolute inset-0 pointer-events-none"
            style="border-radius: inherit; background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.2) 60%, rgba(0,0,0,0) 100%);">
        </div>

        
        <div class="absolute inset-0 pointer-events-none"
            :style="`border-radius: inherit; opacity: ${Math.min(1, st / 200)}; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);`">
        </div>

        
        <div class="absolute inset-0 flex flex-col justify-end pointer-events-none" style="padding: 14px 24px;">
            <h1 class="font-[800] text-white m-0 tracking-[-0.5px] origin-bottom-left"
                :style="`font-size: 28px; transform: translateY(${-Math.min(6, (st / 200) * 6)}px) scale(${Math.max(0.714, 1 - (st / 200) * 0.286)}); text-shadow: 0 ${Math.max(1, 2 - (st/200)*1)}px ${Math.max(4, 8 - (st/200)*4)}px rgba(0,0,0,0.6);`">
                <?php echo e($praktikum->nama ?? 'Praktikum'); ?>

            </h1>
        </div>
    </div>

    <?php
        $activeStyle = "padding:12px 24px; font-weight:600; font-size:14px; color:#293C79; text-decoration:none; border-bottom:2px solid #293C79;";
        $inactiveStyle = "padding:12px 24px; font-weight:500; font-size:14px; color:var(--c-fg-muted); text-decoration:none;";

        $tabs = [
            ['id' => 'pengumuman', 'label' => 'Pengumuman', 'route' => route('eoffice.manprak.asprak.pengumuman.index')],
            ['id' => 'modul', 'label' => 'Modul', 'route' => route('eoffice.manprak.asprak.modul.index')],
            ['id' => 'tugas', 'label' => 'Tugas', 'route' => route('eoffice.manprak.asprak.tugas.index')],
            ['id' => 'absensi', 'label' => 'Absensi & Nilai', 'route' => route('eoffice.manprak.asprak.absensi.index')],
            ['id' => 'praktikan', 'label' => 'Daftar Praktikan', 'route' => route('eoffice.manprak.asprak.daftar-praktikan.index')],
        ];
    ?>

    
    <div style="display: flex; gap: 8px; overflow-x: auto; white-space: nowrap; scrollbar-width: none;">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <a href="<?php echo e($tab['route']); ?>" style="<?php echo e($activeTab === $tab['id'] ? $activeStyle : $inactiveStyle); ?>">
                <?php echo e($tab['label']); ?>

            </a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
</div>
<?php /**PATH C:\Users\User\manajemen_praktikum_\Modules/EOffice\resources/views/components/manajemen-praktikum/asprak-header.blade.php ENDPATH**/ ?>