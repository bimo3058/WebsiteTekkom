<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKAPE — Pendaftaran KP</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * { font-family: 'Inter Tight', sans-serif; }
        :root {
            --primary-50:#eef2ff;--primary-100:#e0e7ff;--primary-500:#4f46e5;
            --grey-0:#fff;--grey-50:#f9fafb;--grey-100:#f3f4f6;--grey-200:#e5e7eb;
            --grey-400:#9ca3af;--grey-500:#6b7280;--grey-600:#4b5563;
            --grey-700:#374151;--grey-800:#1f2937;--grey-900:#030712;
            --success-0:#f0fdf4;--success-50:#dcfce7;--success-100:#bbf7d0;--success-300:#16a34a;
            --warning-0:#fffbeb;--warning-50:#fef3c7;--warning-100:#fde68a;--warning-300:#d97706;
            --error-0:#fff1f2;--error-50:#ffe4e6;--error-200:#f87171;--error-300:#dc2626;
            --sky-500:#0ea5e9;
        }
        .sikape-card { background:#fff; border:1px solid #DFE1E7; border-radius:12px; }
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

        
        <div class="flex-1 flex flex-col min-h-0 overflow-hidden rounded-lg" style="margin:8px; border:1px solid #DFE1E7; background:#fff;">

            <?php echo $__env->make('eoffice::kp.mahasiswa.partials.topbar', ['breadcrumb' => 'Pendaftaran KP'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">

            <div class="max-w-4xl mx-auto">
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Pendaftaran Kerja Praktik</h1>
                        <p class="text-sm text-slate-500 mt-1">Lengkapi data di bawah ini untuk mengajukan Kerja Praktik.</p>
                    </div>
                </div>

                <div class="space-y-6">

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-red-500 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm text-red-700 font-medium"><?php echo e(session('error')); ?></p>
                    </div>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php
                    $step1_done = $existingKp ? true : false;
                    $step2_done = $existingKp ? $existingKp->is_acc_admin : false;
                    $step3_done = $existingKp ? !empty($existingKp->dosen_pembimbing_id) : false;
                    
                    $steps = [
                        ['label' => 'Mendaftar', 'desc' => $step1_done ? 'Selesai' : 'Belum', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', 'done' => $step1_done],
                        ['label' => 'Verifikasi Koor', 'desc' => $step2_done ? 'Selesai' : ($step1_done ? 'Menunggu' : 'Belum'), 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'done' => $step2_done],
                        ['label' => 'Pengumuman Dosen', 'desc' => $step3_done ? 'Selesai' : ($step2_done ? 'Menunggu' : 'Belum'), 'icon' => 'M8 7v14m-4-4h8M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z', 'done' => $step3_done],
                    ];
                    $cur = 0;
                    if ($step3_done) {
                        $cur = 2;
                    } elseif ($step1_done) {
                        $cur = 1;
                    }
                ?>
                <div class="sikape-card p-6">
                    <p class="text-xs font-semibold uppercase tracking-widest mb-5" style="color:var(--grey-400);">Status Pendaftaran Kerja Praktik</p>
                    <div class="flex items-start gap-0 mb-8">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="flex-1 flex flex-col items-center relative">
                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i > 0): ?>
                            <div class="absolute h-0.5 z-0"
                                    style="top:20px; left:0; right:50%; background:<?php echo e($i <= $cur ? '#4f46e5' : 'var(--grey-200)'); ?>;"></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i < count($steps) - 1): ?>
                            <div class="absolute h-0.5 z-0"
                                    style="top:20px; left:50%; right:0; background:<?php echo e($i < $cur ? '#4f46e5' : 'var(--grey-200)'); ?>;"></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            
                            <div class="w-10 h-10 rounded-full flex items-center justify-center z-10 relative transition-all duration-300 flex-shrink-0"
                                    style="background:<?php echo e($i <= $cur ? '#4f46e5' : 'var(--grey-100)'); ?>;
                                        <?php echo e($i === $cur ? 'box-shadow:0 0 0 4px #e0e7ff;' : ''); ?>">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i < $cur || $step['done']): ?>
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <?php else: ?>
                                <svg class="w-5 h-5" style="color:<?php echo e($i <= $cur ? 'white' : 'var(--grey-400)'); ?>;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="<?php echo e($step['icon']); ?>"/></svg>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>

                            
                            <p class="text-xs font-semibold mt-2 text-center" style="color:<?php echo e($i <= $cur ? 'var(--grey-800)' : 'var(--grey-400)'); ?>;"><?php echo e($step['label']); ?></p>
                            <p class="text-[10px] text-center" style="color:<?php echo e($i <= $cur ? 'var(--grey-500)' : 'var(--grey-300)'); ?>;"><?php echo e($step['desc']); ?></p>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step3_done && $existingKp->dosenPembimbing): ?>
                        <div class="p-4 bg-white rounded-lg border border-slate-200 shadow-sm">
                            <p class="text-slate-700 font-medium text-sm">
                                Anda sudah terdaftar sebagai mahasiswa kerja praktik, dan akan di bimbing oleh <strong class="text-slate-900"><?php echo e($existingKp->dosenPembimbing->nama_lengkap ?? $existingKp->dosenPembimbing->name); ?></strong>
                            </p>
                        </div>
                    <?php elseif($step1_done && !$step2_done): ?>
                        <div class="p-4 bg-blue-50 rounded-lg border border-blue-100 shadow-sm">
                            <p class="text-blue-800 font-medium text-sm">Berhasil mendaftar! silahkan menunggu verifikasi oleh koordinator</p>
                        </div>
                    <?php elseif($step2_done && !$step3_done): ?>
                        <div class="p-4 bg-white rounded-lg border border-slate-200 shadow-sm">
                            <p class="text-slate-700 font-medium text-sm">Persyaratan Anda telah di-ACC oleh Koordinator KP. Silakan menunggu pengumuman Dosen Pembimbing.</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($existingKp): ?>
                    
                <?php elseif(!$registrationOpen): ?>
                    
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8 text-center mt-10">
                        <div class="w-16 h-16 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h2 class="text-xl font-bold text-slate-900 mb-2">Pendaftaran Ditutup</h2>
                        <p class="text-slate-600 mb-4">
                            Saat ini periode pendaftaran Kerja Praktik sedang ditutup.
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($startDate || $endDate): ?>
                                <br><br>
                                <span class="inline-flex bg-slate-50 border border-slate-200 rounded px-3 py-2 text-sm font-medium">
                                    Jadwal Pendaftaran: 
                                    <?php echo e($startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : 'Kapan Saja'); ?>

                                    - 
                                    <?php echo e($endDate ? \Carbon\Carbon::parse($endDate)->format('d M Y') : 'Kapan Saja'); ?>

                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </p>
                        <p class="text-sm text-slate-500 mb-6">Silakan cek kembali di lain waktu atau hubungi Koordinator KP untuk informasi lebih lanjut.</p>
                        <a href="<?php echo e(route('eoffice.kp.mahasiswa.dashboard')); ?>" class="inline-flex items-center px-6 py-3 bg-slate-900 text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors">
                            Kembali ke Dashboard
                        </a>
                    </div>
                <?php else: ?>
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showReminder): ?>
                        <div class="mb-6 bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3 shadow-sm">
                            <div class="p-2 bg-amber-100 rounded-lg text-amber-700 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    <h4 class="text-sm font-bold text-amber-900">⏰ Pengingat Batas Akhir</h4>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periodeAktif): ?>
                                        <span class="px-2.5 py-0.5 bg-amber-200 text-amber-800 text-[10px] rounded-full uppercase tracking-wider font-semibold">
                                            Periode: <?php echo e($periodeAktif->semester); ?> <?php echo e($periodeAktif->tahun_ajaran); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($endDate): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-rose-100 text-rose-700 text-[10px] rounded-full font-bold border border-rose-200">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            Deadline: <?php echo e(\Carbon\Carbon::parse($endDate)->translatedFormat('d F Y')); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <p class="text-xs text-amber-800 leading-relaxed">
                                    Pastikan Anda menyelesaikan pengajuan pendaftaran sebelum batas waktu yang telah ditentukan.
                                </p>
                            </div>
                        </div>

                    <?php elseif($periodeAktif): ?>
                        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3 shadow-sm">
                            <div class="p-2 bg-blue-100 rounded-lg text-blue-700 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    <h4 class="text-sm font-bold text-blue-900">Pemberitahuan Pendaftaran</h4>
                                    <span class="px-2.5 py-0.5 bg-blue-200 text-blue-800 text-[10px] rounded-full uppercase tracking-wider font-semibold">
                                        Periode: <?php echo e($periodeAktif->semester); ?> <?php echo e($periodeAktif->tahun_ajaran); ?>

                                    </span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($endDate): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-indigo-100 text-indigo-700 text-[10px] rounded-full font-bold border border-indigo-200">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            Berakhir: <?php echo e(\Carbon\Carbon::parse($endDate)->translatedFormat('d F Y')); ?>

                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <p class="text-xs text-blue-700 leading-relaxed">
                                    Pendaftaran Kerja Praktik untuk periode ini sedang dibuka. Pastikan Anda melengkapi data yang dibutuhkan.
                                </p>
                            </div>
                        </div>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50/50">
                            <h2 class="text-base font-bold text-slate-800">Formulir Pengajuan</h2>
                            <p class="text-sm text-slate-500 mt-0.5">Pastikan data yang Anda isi sudah benar dan final.</p>
                        </div>
                        <form id="form-pendaftaran" action="<?php echo e(route('eoffice.kp.mahasiswa.pendaftaran.store')); ?>" method="POST" enctype="multipart/form-data" class="p-6">
                            <?php echo csrf_field(); ?>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                
                                <div class="md:col-span-2">
                                    <label for="judul_kp" class="block text-sm font-medium text-slate-700 mb-1">Rencana Topik / Judul KP <span class="text-red-500">*</span></label>
                                    <input type="text" name="judul_kp" id="judul_kp" value="<?php echo e(old('judul_kp')); ?>" required placeholder="Contoh: Pembuatan Sistem Otomasi Jaringan..."
                                            class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-4 border">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['judul_kp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                
                                <div class="md:col-span-2">
                                    <label for="instansi_kp" class="block text-sm font-medium text-slate-700 mb-1">Rencana Tempat Instansi <span class="text-red-500">*</span></label>
                                    <input type="text" name="instansi_kp" id="instansi_kp" value="<?php echo e(old('instansi_kp')); ?>" required placeholder="Contoh: PT Telekomunikasi Indonesia (Telkom)"
                                            class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-4 border">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['instansi_kp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                
                                <div>
                                    <label for="ipk" class="block text-sm font-medium text-slate-700 mb-1">Indeks Prestasi Kumulatif (IPK) <span class="text-red-500">*</span></label>
                                    <input type="number" name="ipk" id="ipk" value="<?php echo e(old('ipk')); ?>" step="0.01" min="0" max="4.00" required placeholder="Contoh: 3.50"
                                            class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-4 border">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['ipk'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                
                                <div>
                                    <label for="kelas" class="block text-sm font-medium text-slate-700 mb-1">Kelas <span class="text-red-500">*</span></label>
                                    <select name="kelas" id="kelas" required
                                            class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-4 border text-slate-700 bg-white">
                                        <option value="" disabled <?php echo e(old('kelas') == '' ? 'selected' : ''); ?>>Pilih Kelas</option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $listKelas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <option value="<?php echo e($kls); ?>" <?php echo e(old('kelas') == $kls ? 'selected' : ''); ?>><?php echo e($kls); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </select>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['kelas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                
                                <div class="md:col-span-2">
                                    <label for="sks_diambil" class="block text-sm font-medium text-slate-700 mb-1">Jumlah SKS yang Telah Diambil <span class="text-red-500">*</span></label>
                                    <input type="number" name="sks_diambil" id="sks_diambil" value="<?php echo e(old('sks_diambil')); ?>" min="0" required placeholder="Contoh: 110"
                                            class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-4 border">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['sks_diambil'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                
                                <div>
                                    <label for="tanggal_mulai" class="block text-sm font-medium text-slate-700 mb-1">Rencana Tanggal Mulai <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="<?php echo e(old('tanggal_mulai')); ?>" required
                                            class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-4 border text-slate-600">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['tanggal_mulai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                
                                <div>
                                    <label for="tanggal_selesai" class="block text-sm font-medium text-slate-700 mb-1">Rencana Tanggal Selesai <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="<?php echo e(old('tanggal_selesai')); ?>" required
                                            class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm py-2.5 px-4 border text-slate-600">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['tanggal_selesai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                            </div>

                            
                            <div class="mt-8 border-t border-slate-200 pt-6">
                                <h3 class="text-sm font-bold text-slate-800">Dokumen yang Diperlukan</h3>
                                <p class="text-[11px] text-slate-500 mt-0.5">Silakan download template (jika tersedia) dan upload file yang diminta.</p>
                                
                                <div class="mt-4 space-y-3">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $templatesDokumen ?? collect(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tmpl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl border border-slate-200 bg-white hover:border-indigo-300 hover:shadow-sm transition-all gap-4">
                                            
                                            
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 bg-slate-50 text-slate-500 shadow-sm border border-slate-100">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-800"><?php echo e($tmpl->title); ?></p>
                                                    <p class="text-[11px] text-slate-500 mt-0.5"><?php echo e($tmpl->description ?? 'Dokumen pra-KP'); ?></p>
                                                </div>
                                            </div>

                                            
                                            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tmpl->is_downloadable): ?>
                                                    <a href="<?php echo e(route('eoffice.kp.mahasiswa.dokumen.template', $tmpl->id)); ?>"
                                                       class="inline-flex items-center px-3 py-2 text-xs font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                        Template
                                                    </a>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tmpl->is_uploadable): ?>
                                                    <div class="relative w-full sm:w-auto" x-data="{ fileName: '' }">
                                                        <input type="file" name="dokumen_<?php echo e($tmpl->id); ?>" id="dokumen_<?php echo e($tmpl->id); ?>" 
                                                               class="sr-only peer" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg"
                                                               x-on:change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                                                        <label for="dokumen_<?php echo e($tmpl->id); ?>"
                                                               class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 cursor-pointer transition-colors peer-focus:ring-2 peer-focus:ring-offset-2 peer-focus:ring-slate-300">
                                                            <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                            <span x-text="fileName ? fileName : 'Upload File'" class="truncate max-w-[120px]"></span>
                                                        </label>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        <div class="flex flex-col items-center justify-center py-6 text-center bg-slate-50 rounded-xl border border-slate-200">
                                            <p class="text-xs font-medium text-slate-500">Tidak ada dokumen yang diperlukan saat ini.</p>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="mt-8 border-t border-slate-200 pt-6 flex justify-end bg-slate-50/50 -mx-6 -mb-6 px-6 pb-6 rounded-b-xl border">
                                <button type="submit" class="px-8 py-3 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 shadow-sm transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 whitespace-nowrap active:scale-95">
                                    Kirim Pengajuan KP
                                </button>
                            </div>
                        </form>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\kp\mahasiswa\pendaftaran.blade.php ENDPATH**/ ?>