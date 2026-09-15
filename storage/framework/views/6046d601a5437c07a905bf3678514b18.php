

<div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
    <span style="width:3px;height:14px;border-radius:2px;background:var(--c-primary);"></span>
    <span style="font-size:14px;font-weight:700;color:var(--c-fg);">Modul Sistem</span>
</div>

<?php
    $moduleIcons = [
        'bank_soal'           => 'M6 4h12v16H6zM9 8h6M9 12h6M9 16h4',
        'capstone'            => 'M3 17l5-5 4 4 8-8M14 8h6v6',
        'manajemen_mahasiswa' => 'M9 11a3.5 3.5 0 100-7 3.5 3.5 0 000 7zM2.5 20a6.5 6.5 0 0113 0M17 11.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z',
        'eoffice'             => 'M12 21a9 9 0 100-18 9 9 0 000 18zM12 7v5l3 2',
    ];
    $moduleTags = [
        'bank_soal'           => ['label' => 'SIBASO',   'bg' => '#F9ECCB', 'color' => '#956321'],
        'capstone'            => ['label' => 'SICATA',   'bg' => '#D1F0F9', 'color' => '#0C4D6E'],
        'manajemen_mahasiswa' => ['label' => 'SIMENMA',  'bg' => '#DDF2EE', 'color' => '#287F6E'],
        'eoffice'             => ['label' => 'SIPERKOM', 'bg' => '#FADAE1', 'color' => '#95122B'],
    ];
?>

<div class="dash-modules" style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px;">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $moduleKey => $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
    <?php
        $isActive = $module['is_active'];
        $icon     = $moduleIcons[$moduleKey] ?? $moduleIcons['bank_soal'];
        $tag      = $moduleTags[$moduleKey] ?? ['label' => strtoupper($moduleKey), 'bg' => 'var(--c-bg)', 'color' => 'var(--c-fg-muted)'];
    ?>
    <div style="background:#fff;border:1px solid var(--c-border);border-radius:14px;padding:14px;display:flex;flex-direction:column;gap:10px;box-shadow:var(--shadow-card);transition:border-color .15s,box-shadow .15s;"
         onmouseover="this.style.borderColor='var(--c-primary-border)';this.style.boxShadow='0 4px 14px rgba(11,38,110,0.07)'"
         onmouseout="this.style.borderColor='var(--c-border)';this.style.boxShadow='var(--shadow-card)'">

        <div style="display:flex;align-items:center;justify-content:space-between;">
            <span style="display:inline-flex;align-items:center;gap:6px;padding:5px 9px;border-radius:8px;font-size:11px;font-weight:700;letter-spacing:.02em;background:<?php echo e($tag['bg']); ?>;color:<?php echo e($tag['color']); ?>;">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="<?php echo e($icon); ?>"/>
                </svg>
                <?php echo e($tag['label']); ?>

            </span>
            <form action="<?php echo e(route('superadmin.modules.toggle', $module['slug'])); ?>" method="POST" style="margin:0;">
                <?php echo csrf_field(); ?>
                <button type="submit" title="<?php echo e($isActive ? 'Nonaktifkan' : 'Aktifkan'); ?>"
                        style="position:relative;width:34px;height:20px;border-radius:9999px;border:none;cursor:pointer;background:<?php echo e($isActive ? 'var(--c-primary)' : 'var(--c-border-strong)'); ?>;transition:background .2s;padding:0;">
                    <div style="position:absolute;top:2px;left:2px;width:16px;height:16px;border-radius:50%;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,0.2);transition:transform .2s;transform:<?php echo e($isActive ? 'translateX(14px)' : 'none'); ?>;"></div>
                </button>
            </form>
        </div>

        <div>
            <h3 style="font-size:14px;font-weight:700;color:var(--c-fg);line-height:1.3;"><?php echo e($module['name']); ?></h3>
            <p style="font-size:11.5px;color:var(--c-fg-muted);line-height:1.5;margin-top:4px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                <?php echo e($module['description'] ?? 'Sistem Informasi ' . $module['name'] . ', manajemen fungsional untuk modul ' . $tag['label'] . '.'); ?>

            </p>
        </div>

        <div style="padding-top:10px;border-top:1px solid var(--c-border);display:flex;align-items:center;justify-content:space-between;margin-top:auto;">
            <span style="font-size:11px;color:var(--c-fg-muted);font-variant-numeric:tabular-nums;">v<?php echo e($module['version'] ?? '1.0'); ?></span>
            <a href="<?php echo e(route('superadmin.modules')); ?>"
               style="font-size:12px;font-weight:600;color:var(--c-primary);text-decoration:none;transition:opacity .15s;"
               onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                Settings →
            </a>
        </div>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
</div>

<style>
@media (max-width: 1280px) { .dash-modules { grid-template-columns: repeat(2, 1fr) !important; } }
@media (max-width: 640px)  { .dash-modules { grid-template-columns: 1fr !important; } }
</style>
<?php /**PATH C:\WebsiteTekkom - Copy\resources\views\superadmin\dashboard\_modules.blade.php ENDPATH**/ ?>