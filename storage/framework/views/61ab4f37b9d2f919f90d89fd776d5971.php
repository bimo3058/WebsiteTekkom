<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['praktikum']));

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

foreach (array_filter((['praktikum']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $currentRoute = request()->route()?->getName();
    $isPengumuman = $currentRoute == 'eoffice.manprak.mahasiswa.pengumuman.index';
    $isModul = $currentRoute == 'eoffice.manprak.mahasiswa.modul.index' || str_contains($currentRoute, 'mahasiswa.modul.');
    $isTugas = $currentRoute == 'eoffice.manprak.mahasiswa.tugas.index' || str_contains($currentRoute, 'mahasiswa.tugas.');
    $isNilai = $currentRoute == 'eoffice.manprak.mahasiswa.nilai.index';
    $isPraktikan = $currentRoute == 'eoffice.manprak.mahasiswa.daftar-praktikan.index';
    
    $prakId = $praktikum ? $praktikum->id : null;
    
    $namaHalaman = 'PENGUMUMAN';
    if ($isModul) $namaHalaman = 'MODUL';
    elseif ($isTugas) $namaHalaman = 'TUGAS';
    elseif ($isNilai) $namaHalaman = 'ABSENSI & NILAI';
    elseif ($isPraktikan) $namaHalaman = 'DAFTAR PRAKTIKAN';
?>


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
     " class="sticky z-20 bg-white transition-all duration-300"
    style="top: -200px; margin: -20px -24px 0 -24px; padding: 20px 24px 0 24px; border-bottom: 1px solid var(--c-border); margin-bottom: 16px;">


    
    <div
        style="position: relative; overflow: hidden; border-radius: 12px; border: 1px solid var(--c-border); height: 240px; margin-bottom: 4px; transform: translateZ(0);">

        
        <div class="absolute inset-0 flex items-center justify-center bg-[#F3F4F6]" style="border-radius: inherit;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($praktikum && $praktikum->cover_path): ?>
                <img src="<?php echo e(app(\App\Services\SupabaseStorage::class)->publicUrl($praktikum->cover_path, 'eoffice')); ?>"
                     class="w-full h-full object-cover"
                     :style="`transform: scale(${Math.max(1, 1 + (st / 200) * 0.1)}); opacity: ${Math.max(0.3, 1 - (st / 300))}; filter: blur(${Math.min(8, st / 15)}px);`"
                     alt="Cover Praktikum">
            <?php else: ?>
                <svg width="48" height="48" viewBox="0 0 24 24" fill="#828896"
                    :style="`transform: scale(${Math.max(0.6, 1 - (st / 200) * 0.4)}); opacity: ${Math.max(0, 1 - (st / 150))};`">
                    <path
                        d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                </svg>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
        // Moved to the top for breadcrumbs
    ?>

    
    <div style="display: flex; gap: 8px; overflow-x: auto;" class="no-scrollbar">
        <a href="<?php echo e(route('eoffice.manprak.mahasiswa.pengumuman.index', ['praktikum_id' => $prakId])); ?>"
            style="padding: 12px 24px; font-weight: <?php echo e($isPengumuman ? '600' : '500'); ?>; font-size: 14px; color: <?php echo e($isPengumuman ? '#293C79' : 'var(--c-fg-muted)'); ?>; text-decoration: none; <?php echo $isPengumuman ? 'border-bottom: 2px solid #293C79;' : ''; ?>">Pengumuman</a>
        
        <a href="<?php echo e(route('eoffice.manprak.mahasiswa.modul.index', ['praktikum_id' => $prakId])); ?>"
            style="padding: 12px 24px; font-weight: <?php echo e($isModul ? '600' : '500'); ?>; font-size: 14px; color: <?php echo e($isModul ? '#293C79' : 'var(--c-fg-muted)'); ?>; text-decoration: none; <?php echo $isModul ? 'border-bottom: 2px solid #293C79;' : ''; ?>">Modul</a>
        
        <a href="<?php echo e(route('eoffice.manprak.mahasiswa.tugas.index', ['praktikum_id' => $prakId])); ?>"
            style="padding: 12px 24px; font-weight: <?php echo e($isTugas ? '600' : '500'); ?>; font-size: 14px; color: <?php echo e($isTugas ? '#293C79' : 'var(--c-fg-muted)'); ?>; text-decoration: none; <?php echo $isTugas ? 'border-bottom: 2px solid #293C79;' : ''; ?>">Tugas</a>
        
        <a href="<?php echo e(route('eoffice.manprak.mahasiswa.nilai.index', ['praktikum_id' => $prakId])); ?>"
            style="padding: 12px 24px; font-weight: <?php echo e($isNilai ? '600' : '500'); ?>; font-size: 14px; color: <?php echo e($isNilai ? '#293C79' : 'var(--c-fg-muted)'); ?>; text-decoration: none; <?php echo $isNilai ? 'border-bottom: 2px solid #293C79;' : ''; ?>">Absensi & Nilai</a>
        
        <a href="<?php echo e(route('eoffice.manprak.mahasiswa.daftar-praktikan.index', ['praktikum_id' => $prakId])); ?>"
            style="padding: 12px 24px; font-weight: <?php echo e($isPraktikan ? '600' : '500'); ?>; font-size: 14px; color: <?php echo e($isPraktikan ? '#293C79' : 'var(--c-fg-muted)'); ?>; text-decoration: none; <?php echo $isPraktikan ? 'border-bottom: 2px solid #293C79;' : ''; ?>">Daftar Praktikan</a>
    </div>
</div>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules/EOffice\resources/views/components/manajemen-praktikum/mhs-header.blade.php ENDPATH**/ ?>