<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKAPE — Dashboard Mahasiswa</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * {
            font-family: 'Inter Tight', sans-serif;
        }

        :root {
            --primary-50: #eef2ff;
            --primary-100: #e0e7ff;
            --primary-500: #4f46e5;
            --grey-0: #fff;
            --grey-50: #f9fafb;
            --grey-100: #f3f4f6;
            --grey-200: #e5e7eb;
            --grey-400: #9ca3af;
            --grey-500: #6b7280;
            --grey-600: #4b5563;
            --grey-700: #374151;
            --grey-800: #1f2937;
            --grey-900: #030712;
            --success-0: #f0fdf4;
            --success-50: #dcfce7;
            --success-100: #bbf7d0;
            --success-300: #16a34a;
            --warning-0: #fffbeb;
            --warning-50: #fef3c7;
            --warning-100: #fde68a;
            --warning-300: #d97706;
            --error-0: #fff1f2;
            --error-50: #ffe4e6;
            --error-200: #f87171;
            --error-300: #dc2626;
            --sky-500: #0ea5e9;
        }

        .sikape-card {
            background: #fff;
            border: 1px solid #DFE1E7;
            border-radius: 12px;
        }
    </style>
    <?php if (isset($component)) { $__componentOriginal798d336f107c986f84961f0c7e80911e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal798d336f107c986f84961f0c7e80911e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mobile-navigation-assets','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mobile-navigation-assets'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal798d336f107c986f84961f0c7e80911e)): ?>
<?php $attributes = $__attributesOriginal798d336f107c986f84961f0c7e80911e; ?>
<?php unset($__attributesOriginal798d336f107c986f84961f0c7e80911e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal798d336f107c986f84961f0c7e80911e)): ?>
<?php $component = $__componentOriginal798d336f107c986f84961f0c7e80911e; ?>
<?php unset($__componentOriginal798d336f107c986f84961f0c7e80911e); ?>
<?php endif; ?>
</head>

<body style="background:#f9fafb;" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen w-full overflow-hidden">

        <?php echo $__env->make('eoffice::kp.mahasiswa.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="flex-1 flex flex-col min-h-0 overflow-hidden">

            
            <div class="flex-1 flex flex-col min-h-0 overflow-hidden rounded-lg"
                style="margin:8px; border:1px solid #DFE1E7; background:#fff;">

                <?php echo $__env->make('eoffice::kp.mahasiswa.partials.topbar', ['breadcrumb' => 'Dashboard'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                        <div class="mb-5 flex items-center gap-3 p-4 rounded-xl border"
                            style="background:var(--success-0);border-color:var(--success-50);color:var(--success-300);">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm font-medium"><?php echo e(session('success')); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
                        <div class="mb-5 flex items-center gap-3 p-4 rounded-xl border"
                            style="background:var(--error-0);border-color:var(--error-50);color:var(--error-300);">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm font-medium"><?php echo e(session('error')); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <div class="mb-6">
                        <h1 class="text-2xl font-semibold" style="color:var(--grey-900);">Dashboard KP</h1>
                        <p class="text-sm mt-1" style="color:var(--grey-500);">Selamat datang, <strong
                                style="color:var(--grey-700);"><?php echo e($mahasiswa->nama_lengkap); ?></strong>. Pantau seluruh
                            progres KP Anda di sini.</p>
                    </div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kp): ?>
                        <?php
                            $steps = [
                                ['label' => 'Pra KP', 'desc' => 'Pendaftaran', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                                ['label' => 'Saat KP', 'desc' => 'Pelaksanaan', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                                ['label' => 'Pasca KP', 'desc' => 'Seminar', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                                ['label' => 'Selesai', 'desc' => 'Penilaian', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ];
                            $stepMap = ['Pra-KP' => 0, 'pending' => 0, 'Saat KP' => 1, 'active' => 1, 'Pasca KP' => 2, 'Selesai' => 3, 'completed' => 3];
                            $cur = $stepMap[$kp->status_kp] ?? 0;
                        ?>
                        <div class="sikape-card p-6 mb-6">
                            <p class="text-xs font-semibold uppercase tracking-widest mb-5" style="color:var(--grey-400);">
                                Progres Saat Ini</p>
                            <div class="flex items-start gap-0">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <div class="flex-1 flex flex-col items-center relative">

                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i > 0): ?>
                                            <div class="absolute h-0.5 z-0"
                                                style="top:20px; left:0; right:50%; background:<?php echo e($i <= $cur ? '#4f46e5' : 'var(--grey-200)'); ?>;">
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i < count($steps) - 1): ?>
                                            <div class="absolute h-0.5 z-0"
                                                style="top:20px; left:50%; right:0; background:<?php echo e($i < $cur ? '#4f46e5' : 'var(--grey-200)'); ?>;">
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center z-10 relative transition-all duration-300 flex-shrink-0"
                                            style="background:<?php echo e($i <= $cur ? '#4f46e5' : 'var(--grey-100)'); ?>;
                                            <?php echo e($i === $cur ? 'box-shadow:0 0 0 4px #e0e7ff;' : ''); ?>">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i < $cur): ?>
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                            <?php else: ?>
                                                <svg class="w-5 h-5" style="color:<?php echo e($i <= $cur ? 'white' : 'var(--grey-400)'); ?>;"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                        d="<?php echo e($step['icon']); ?>" />
                                                </svg>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>

                                        
                                        <p class="text-xs font-semibold mt-2 text-center"
                                            style="color:<?php echo e($i <= $cur ? 'var(--grey-800)' : 'var(--grey-400)'); ?>;">
                                            <?php echo e($step['label']); ?></p>
                                        <p class="text-[10px] text-center"
                                            style="color:<?php echo e($i <= $cur ? 'var(--grey-500)' : 'var(--grey-300)'); ?>;">
                                            <?php echo e($step['desc']); ?></p>

                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                        <?php
                            $badgeMap = [
                                'Pra-KP' => ['bg' => 'var(--warning-50)', 'color' => 'var(--warning-300)'],
                                'Saat KP' => ['bg' => '#eff6ff', 'color' => '#2563eb'],
                                'Pasca KP' => ['bg' => '#f5f3ff', 'color' => '#7c3aed'],
                                'Selesai' => ['bg' => 'var(--success-50)', 'color' => 'var(--success-300)'],
                            ];
                            $bs = $badgeMap[$kp->status_kp ?? ''] ?? ['bg' => 'var(--grey-100)', 'color' => 'var(--grey-500)'];
                        ?>

                        <div class="sikape-card p-5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider mb-2"
                                style="color:var(--grey-400);">Status KP</p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kp): ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold"
                                    style="background:<?php echo e($bs['bg']); ?>;color:<?php echo e($bs['color']); ?>;"><?php echo e($kp->status_kp); ?></span>
                            <?php else: ?>
                                <p class="text-sm font-semibold" style="color:var(--grey-400);">Belum Daftar</p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="sikape-card p-5">
                            <p class="text-[11px] font-semibold uppercase tracking-wider mb-2"
                                style="color:var(--grey-400);">Dosen Pembimbing</p>
                            <p class="text-sm font-semibold truncate" style="color:var(--grey-800);">
                                <?php echo e($kp?->dosenPembimbing?->nama_lengkap ?? $kp?->dosenPembimbing?->name ?? '—'); ?></p>
                        </div>

                        <div class="sikape-card p-5 flex flex-col justify-center">
                            <p class="text-[11px] font-semibold uppercase tracking-wider mb-1"
                                style="color:var(--grey-400);">Nilai Akhir KP</p>
                            <p class="text-2xl font-bold truncate mt-1" style="color:var(--grey-900);">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($finalGradeDisplay !== null): ?>
                                    <?php echo e($finalGradeDisplay); ?>

                                <?php else: ?>
                                    <span class="text-sm font-semibold" style="color:var(--grey-400);">Belum Dinilai</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                        
                        <div class="lg:col-span-2 sikape-card overflow-hidden flex flex-col">
                            <div class="px-6 py-5 border-b flex items-center justify-between"
                                style="border-color:var(--grey-100);">
                                <h2 class="text-base font-bold" style="color:var(--grey-900);">Timeline KP</h2>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($timeline) && $timeline->count() > 0): ?>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                        style="background:var(--primary-50);color:var(--primary-500);"><?php echo e($timeline->count()); ?>

                                        item</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="flex-1 overflow-y-auto px-6 py-6">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($timeline) && $timeline->count() > 0): ?>
                                    <div class="space-y-6">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $timeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <?php
                                                $lampiran = $item->lampiran ?? null;
                                                $ext = $lampiran ? strtolower(pathinfo($lampiran, PATHINFO_EXTENSION)) : null;
                                                $isPdf = $ext === 'pdf';
                                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                $fileUrl = $lampiran ? Storage::url($lampiran) : null;
                                            ?>
                                            <div class="rounded-xl border overflow-hidden"
                                                style="border-color:var(--grey-200);">
                                                
                                                <div class="px-5 py-4 flex items-start justify-between gap-3"
                                                    style="background:var(--grey-50);">
                                                    <div class="flex items-start gap-3">
                                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5"
                                                            style="background:var(--primary-100);color:var(--primary-500);">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <p class="text-sm font-bold" style="color:var(--grey-900);">
                                                                <?php echo e($item->judul); ?></p>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->konten): ?>
                                                                <p class="text-xs mt-0.5 leading-relaxed"
                                                                    style="color:var(--grey-600);"><?php echo e($item->konten); ?></p>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                            <p class="text-[10px] mt-1 font-medium uppercase tracking-wider"
                                                                style="color:var(--grey-400);">
                                                                <?php echo e($item->created_at->format('d M Y')); ?></p>
                                                        </div>
                                                    </div>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fileUrl): ?>
                                                        <div class="flex items-center gap-2 flex-shrink-0">
                                                            <a href="<?php echo e($fileUrl); ?>" target="_blank" download
                                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all"
                                                                style="background:var(--primary-50);color:var(--primary-500);border:1px solid var(--primary-100);">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                                </svg>
                                                                Unduh
                                                            </a>
                                                        </div>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>

                                                
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fileUrl): ?>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isPdf): ?>
                                                        <div x-data="{ fullscreen: false }" class="w-full">
                                                            
                                                            <div class="relative group" style="background:#f1f5f9; height:300px;">
                                                                <iframe src="<?php echo e($fileUrl); ?>#toolbar=0&navpanes=0&scrollbar=1"
                                                                    class="w-full h-full border-0 pointer-events-none"
                                                                    title="<?php echo e($item->judul); ?>" loading="lazy"></iframe>
                                                                
                                                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"
                                                                    style="background:rgba(15,23,42,0.35);" @click="fullscreen = true">
                                                                    <button
                                                                        class="bg-white text-slate-900 px-4 py-2 rounded-lg font-bold text-xs shadow-lg flex items-center gap-2">
                                                                        <svg class="w-4 h-4 text-indigo-600" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                                                        </svg>
                                                                        Buka Fullscreen
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            
                                                            <div x-show="fullscreen"
                                                                class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-8"
                                                                style="display:none;background:rgba(15,23,42,0.8);backdrop-filter:blur(4px);"
                                                                x-transition>
                                                                <div class="relative w-full max-w-5xl bg-white rounded-2xl overflow-hidden shadow-2xl flex flex-col"
                                                                    style="height:90vh;" @click.away="fullscreen = false">
                                                                    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100"
                                                                        style="background:#f8fafc;">
                                                                        <div>
                                                                            <h3 class="font-bold text-slate-800 text-sm">
                                                                                <?php echo e($item->judul); ?></h3>
                                                                            <p class="text-[11px] text-slate-500 mt-0.5">Pratinjau
                                                                                Dokumen PDF</p>
                                                                        </div>
                                                                        <div class="flex items-center gap-2">
                                                                            <a href="<?php echo e($fileUrl); ?>" target="_blank"
                                                                                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                                    viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                                        stroke-width="2"
                                                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                                                </svg>
                                                                                Buka di Tab Baru
                                                                            </a>
                                                                            <button @click="fullscreen = false"
                                                                                class="p-1.5 text-slate-400 hover:bg-rose-50 hover:text-rose-500 rounded-lg transition-colors">
                                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                                    viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                                </svg>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-1 p-2" style="background:#f1f5f9;">
                                                                        <iframe src="<?php echo e($fileUrl); ?>"
                                                                            class="w-full h-full rounded-xl bg-white shadow-sm border-0"
                                                                            title="<?php echo e($item->judul); ?>"></iframe>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php elseif($isImage): ?>
                                                        <div class="p-4" style="background:#f8fafc;">
                                                            <img src="<?php echo e($fileUrl); ?>" alt="<?php echo e($item->judul); ?>"
                                                                class="max-w-full mx-auto rounded-lg shadow-sm"
                                                                style="max-height:480px; object-fit:contain;">
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="px-5 py-4 flex items-center gap-3"
                                                            style="background:#fffbeb;border-top:1px solid #fde68a;">
                                                            <svg class="w-5 h-5 flex-shrink-0" style="color:#d97706;" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                            <div>
                                                                <p class="text-xs font-semibold" style="color:#92400e;">Lampiran
                                                                    tersedia</p>
                                                                <p class="text-[11px] mt-0.5" style="color:#b45309;">File
                                                                    <?php echo e(strtoupper($ext)); ?> — klik tombol "Unduh" untuk mengunduh.</p>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="flex flex-col items-center justify-center py-12 text-center">
                                        <div class="w-12 h-12 rounded-full mb-3 flex items-center justify-center"
                                            style="background:var(--grey-100);">
                                            <svg class="w-6 h-6" style="color:var(--grey-400);" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold" style="color:var(--grey-500);">Belum ada timeline
                                            KP</p>
                                        <p class="text-xs mt-1" style="color:var(--grey-400);">Koordinator KP belum
                                            memposting jadwal timeline.</p>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        
                        <div class="sikape-card overflow-hidden flex flex-col max-h-[600px]">
                            <div class="px-5 py-4 border-b flex items-center justify-between"
                                style="border-color:var(--grey-100);">
                                <h2 class="text-sm font-semibold" style="color:var(--grey-800);">Pengumuman</h2>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                    style="background:var(--primary-50);color:var(--primary-500);"><?php echo e($pengumuman->count()); ?></span>
                            </div>
                            <div class="flex-1 divide-y overflow-y-auto" style="divide-color:var(--grey-100);">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pengumuman; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ann): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <div class="px-5 py-4 hover:bg-slate-50 transition-colors">
                                        <div class="flex items-start gap-3">
                                            <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0"
                                                style="background:var(--primary-500);"></div>
                                            <div>
                                                <p class="text-sm font-semibold" style="color:var(--grey-800);">
                                                    <?php echo e($ann->judul); ?></p>
                                                <p class="text-xs mt-1 line-clamp-2 leading-relaxed"
                                                    style="color:var(--grey-500);"><?php echo e(Str::limit($ann->konten, 100)); ?></p>
                                                <p class="text-[10px] mt-2 font-medium uppercase tracking-wider"
                                                    style="color:var(--grey-400);"><?php echo e($ann->created_at->diffForHumans()); ?>

                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <div class="flex flex-col items-center justify-center py-12 px-5 text-center">
                                        <svg class="w-10 h-10 mb-3" style="color:var(--grey-300);" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                        </svg>
                                        <p class="text-xs font-semibold" style="color:var(--grey-500);">Belum ada pengumuman
                                        </p>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        
                        <div class="sikape-card overflow-hidden flex flex-col max-h-[600px] mt-6">
                            <div class="px-5 py-4 border-b flex items-center justify-between"
                                style="border-color:var(--grey-100);">
                                <h2 class="text-sm font-semibold" style="color:var(--grey-800);">Template Dokumen (Fase
                                    <?php echo e(ucwords(str_replace('_', ' ', $activePhase))); ?>)</h2>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                    style="background:var(--primary-50);color:var(--primary-500);"><?php echo e($templates->count()); ?></span>
                            </div>
                            <div class="flex-1 divide-y overflow-y-auto" style="divide-color:var(--grey-100);">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <div class="px-5 py-4 hover:bg-slate-50 transition-colors">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-start gap-3">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold" style="color:var(--grey-800);">
                                                        <?php echo e($template->title); ?></p>
                                                    <p class="text-[10px] mt-0.5 text-slate-500">
                                                        <?php echo e(strtoupper($template->file_type)); ?> •
                                                        <?php echo e($template->created_at->diffForHumans()); ?></p>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($template->description): ?>
                                                        <p class="text-xs mt-1 text-slate-600 line-clamp-1">
                                                            <?php echo e($template->description); ?></p>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            </div>
                                            <a href="<?php echo e(route('eoffice.kp.mahasiswa.dokumen.template', $template->id)); ?>"
                                                class="inline-flex items-center justify-center p-2 rounded-lg bg-white border border-slate-200 text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 transition-all shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <div class="flex flex-col items-center justify-center py-10 px-5 text-center">
                                        <div
                                            class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center mb-3 text-slate-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <p class="text-xs font-semibold" style="color:var(--grey-500);">Tidak ada template
                                            dokumen untuk fase ini</p>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                    </div>

                </main>
            </div>
        </div>
    </div>
    <?php if (isset($component)) { $__componentOriginal0d3b2b7a1a27f99acddd84d2b2599baa = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0d3b2b7a1a27f99acddd84d2b2599baa = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.mobile-navigation','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mobile-navigation'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0d3b2b7a1a27f99acddd84d2b2599baa)): ?>
<?php $attributes = $__attributesOriginal0d3b2b7a1a27f99acddd84d2b2599baa; ?>
<?php unset($__attributesOriginal0d3b2b7a1a27f99acddd84d2b2599baa); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0d3b2b7a1a27f99acddd84d2b2599baa)): ?>
<?php $component = $__componentOriginal0d3b2b7a1a27f99acddd84d2b2599baa; ?>
<?php unset($__componentOriginal0d3b2b7a1a27f99acddd84d2b2599baa); ?>
<?php endif; ?>
</body>

</html>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\kp\mahasiswa\dashboard.blade.php ENDPATH**/ ?>