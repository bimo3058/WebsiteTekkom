<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKAPE — Dokumen KP</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }

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

<body style="background:#f9fafb;" x-data="{ sidebarOpen: false, globalUploading: false }">
    <div class="flex h-screen w-full overflow-hidden">

        <?php echo $__env->make('eoffice::kp.mahasiswa.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="flex-1 flex flex-col min-h-0 overflow-hidden">

            
            <div class="flex-1 flex flex-col min-h-0 overflow-hidden rounded-lg"
                style="margin:8px; border:1px solid #DFE1E7; background:#fff;">

                <?php echo $__env->make('eoffice::kp.mahasiswa.partials.topbar', ['breadcrumb' => 'Dokumen KP'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
                        <div x-data="{ show: true }" x-show="show" x-transition
                            class="mb-6 flex items-center justify-between bg-emerald-50 border border-emerald-200 p-4 rounded-xl shadow-sm">
                            <div class="flex items-center">
                                <div
                                    class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center mr-3 flex-shrink-0">
                                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-emerald-900 font-bold">Berhasil!</p>
                                    <p class="text-xs text-emerald-700"><?php echo e(session('success')); ?></p>
                                </div>
                            </div>
                            <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error') || $errors->any()): ?>
                        <div x-data="{ show: true }" x-show="show" x-transition
                            class="mb-6 flex items-center justify-between bg-rose-50 border border-rose-200 p-4 rounded-xl shadow-sm">
                            <div class="flex items-center">
                                <div
                                    class="w-8 h-8 rounded-full bg-rose-500 flex items-center justify-center mr-3 flex-shrink-0">
                                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-rose-900 font-bold">Gagal disubmit!</p>
                                    <p class="text-xs text-rose-700">
                                        <?php echo e(session('error') ?? 'Pastikan format file sesuai dan ukuran tidak melebihi batas.'); ?>

                                    </p>
                                </div>
                            </div>
                            <button @click="show = false" class="text-rose-400 hover:text-rose-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <div class="mb-8">
                        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Dokumen Pelaksanaan KP</h1>
                        <p class="text-sm text-slate-500 mt-1">Lengkapi seluruh tahapan dokumen berikut untuk
                            menyelesaikan rangkaian Kerja Praktik Anda.</p>
                    </div>

                    
                    <?php
                        $step1 = !empty($kp->judul_kp) && !empty($kp->instansi_kp);

                        $semuaLengkap = true;
                        $adaPending = false;
                        $adaDraft = false;
                        $semuaApproved = true;

                        foreach ($templatesDokumen as $tmpl) {
                            $docGroup = $dokumenByJenis->get($tmpl->title);
                            $latestDoc = $docGroup ? $docGroup->sortByDesc('created_at')->first() : null;

                            if (!$latestDoc) {
                                $semuaLengkap = false;
                                $semuaApproved = false;
                            } else {
                                $statusVal = strtolower($latestDoc->status_validasi ?? '');
                                $apprStatus = strtolower($latestDoc->approval_status ?? '');
                                $status = ($statusVal === 'draft') ? 'draft' : ($apprStatus ?: $statusVal);
                                if (in_array($status, ['draft', 'belum', 'ditolak', 'rejected', 'revision'])) {
                                    $adaDraft = true;
                                    $semuaApproved = false;
                                } elseif (in_array($status, ['pending', 'menunggu'])) {
                                    $adaPending = true;
                                    $semuaApproved = false;
                                }
                            }
                        }
                        
                        $isPernahDikunci = false;

                        foreach ($templatesDokumen as $tmpl) {
                            $jenis = $tmpl->title;
                            $docGroup = $dokumenByJenis->get($jenis);
                            $latestDoc = $docGroup ? $docGroup->sortByDesc('created_at')->first() : null;

                            if ($latestDoc) {
                                $status = strtolower($latestDoc->status_validasi ?? '');
                                
                                if (in_array($status, ['menunggu', 'pending', 'ditolak', 'rejected', 'revisi', 'revision'])) {
                                    $isPernahDikunci = true;
                                }
                                if ($tmpl->approver_role !== 'tanpa_review' && in_array($status, ['disetujui', 'approved'])) {
                                    $isPernahDikunci = true;
                                }
                                if ($status === 'draft' && !empty($latestDoc->revision_note) && $latestDoc->revision_note !== '-') {
                                    $isPernahDikunci = true;
                                }
                            }
                        }

                        if (!$step1 || !$semuaLengkap || $adaDraft) {
                            $curState = 0; // Melengkapi Dokumen
                        } elseif ($adaPending) {
                            $curState = 1; // Menunggu Validasi Dosen
                        } elseif ($semuaApproved && count($templatesDokumen) > 0) {
                            $curState = 2; // Selesai
                        } else {
                            $curState = 0;
                        }

                        $steps = [
                            ['label' => 'Pemberkasan', 'desc' => $curState > 0 ? 'Selesai' : 'Sedang Berjalan', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            ['label' => 'Validasi Dosen', 'desc' => $curState > 1 ? 'Selesai' : ($curState === 1 ? 'Sedang Diproses' : 'Belum'), 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                            ['label' => 'Selesai', 'desc' => $curState === 2 ? 'Lanjut Seminar' : 'Belum Terkunci', 'icon' => 'M5 13l4 4L19 7'],
                        ];

                        $currentStep = $curState + 1;
                    ?>

                    <div class="sikape-card p-6 mb-8">
                        <p class="text-xs font-semibold uppercase tracking-widest mb-5" style="color:var(--grey-400);">
                            Progres Dokumen</p>
                        <div class="flex items-start gap-0">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="flex-1 flex flex-col items-center relative">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i > 0): ?>
                                        <div class="absolute h-0.5 z-0"
                                            style="top:20px; left:0; right:50%; background:<?php echo e($i <= $curState ? '#4f46e5' : 'var(--grey-200)'); ?>;">
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i < count($steps) - 1): ?>
                                        <div class="absolute h-0.5 z-0"
                                            style="top:20px; left:50%; right:0; background:<?php echo e($i < $curState ? '#4f46e5' : 'var(--grey-200)'); ?>;">
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <div class="w-10 h-10 rounded-full flex items-center justify-center z-10 relative transition-all duration-300 flex-shrink-0"
                                        style="background:<?php echo e($i <= $curState ? '#4f46e5' : 'var(--grey-100)'); ?>;
                                                                                                                            <?php echo e($i === $curState ? 'box-shadow:0 0 0 4px #e0e7ff;' : ''); ?>">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i < $curState): ?>
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        <?php else: ?>
                                            <svg class="w-5 h-5"
                                                style="color:<?php echo e($i <= $curState ? 'white' : 'var(--grey-400)'); ?>;" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                    d="<?php echo e($step['icon']); ?>" />
                                            </svg>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>

                                    <p class="text-xs font-semibold mt-2 text-center"
                                        style="color:<?php echo e($i <= $curState ? 'var(--grey-800)' : 'var(--grey-400)'); ?>;">
                                        <?php echo e($step['label']); ?>

                                    </p>
                                    <p class="text-[10px] text-center"
                                        style="color:<?php echo e($i <= $curState ? 'var(--grey-500)' : 'var(--grey-300)'); ?>;">
                                        <?php echo e($step['desc']); ?>

                                    </p>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </div> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$isOpen): ?>
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-8 text-center mt-10 mb-8">
                            <div class="w-16 h-16 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-slate-900 mb-2">Fase Saat KP Ditutup</h2>
                            <p class="text-slate-600 mb-4">
                                Saat ini periode unggah dokumen (Saat KP) sedang ditutup atau belum dimulai.
                            </p>
                            <p class="text-sm text-slate-500 mb-6">Silakan cek kembali di lain waktu atau hubungi
                                Koordinator KP untuk informasi lebih lanjut.</p>
                        </div>
                    <?php else: ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showReminder): ?>
                            <div
                                class="mb-6 bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3 shadow-sm">
                                <div class="p-2 bg-amber-100 rounded-lg text-amber-700 flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                        <h4 class="text-sm font-bold text-amber-900">⏰ Pengingat Batas Akhir</h4>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($endDate): ?>
                                            <span
                                                class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-rose-100 text-rose-700 text-[10px] rounded-full font-bold border border-rose-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Deadline: <?php echo e(\Carbon\Carbon::parse($endDate)->translatedFormat('d F Y')); ?>

                                            </span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <p class="text-xs text-amber-800 leading-relaxed">
                                        Pastikan Anda menyelesaikan pengunggahan dokumen sebelum batas waktu yang telah
                                        ditentukan.
                                    </p>
                                </div>
                            </div>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <div class="grid grid-cols-1 gap-8" x-data="{
                            showModal: false,
                            instansi: <?php echo e(json_encode($kp->instansi_kp ?? '', JSON_HEX_APOS | JSON_HEX_QUOT)); ?>,
                            judul: <?php echo e(json_encode($kp->judul_kp ?? '', JSON_HEX_APOS | JSON_HEX_QUOT)); ?>,
                            tglMulai: '<?php echo e($kp->tanggal_mulai ?? ''); ?>',
                            tglSelesai: '<?php echo e($kp->tanggal_selesai ?? ''); ?>',
                            
                            get isDataLengkap() {
                                return this.instansi.trim() !== '' && this.judul.trim() !== '' && this.tglMulai !== '' && this.tglSelesai !== '';
                            }
                        }">
                            
                            <div class="space-y-8">

                                
                                <div id="step-1"
                                    class="bg-white rounded-2xl border transition-all duration-300 <?php echo e($currentStep === 1 ? 'border-indigo-500 ring-4 ring-indigo-200 shadow-lg shadow-indigo-100/50' : 'border-slate-200 shadow-sm'); ?> overflow-hidden">
                                    <div
                                        class="px-6 py-4 border-b border-slate-100 flex items-center justify-between <?php echo e($currentStep === 1 ? 'bg-indigo-50/30' : ''); ?>">
                                        <div class="flex flex-col gap-1">
                                            <h2 class="text-base font-bold text-slate-800">Data Instansi & Laporan</h2>
                                            <p class="text-xs text-slate-500">Silakan melengkapi dan memverifikasi data di
                                                bawah ini. Anda dapat memperbarui data jika terdapat perbedaan dengan
                                                informasi yang diajukan pada saat pendaftaran awal.</p>
                                        </div>

                                    </div>
                                    <div class="p-6">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tempat
                                                    Instansi</label>
                                                <input type="text" x-model="instansi" <?php echo e(($currentStep > 1 || $isPernahDikunci) ? 'disabled' : ''); ?>

                                                    placeholder="Nama instansi tempat Anda diterima..."
                                                    class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-4 border bg-slate-50/50 focus:bg-white transition-all disabled:bg-slate-100 disabled:text-slate-500 disabled:cursor-not-allowed">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Judul
                                                    Laporan</label>
                                                <input type="text" x-model="judul" <?php echo e(($currentStep > 1 || $isPernahDikunci) ? 'disabled' : ''); ?>

                                                    placeholder="Judul laporan akhir KP Anda..."
                                                    class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-4 border bg-slate-50/50 focus:bg-white transition-all disabled:bg-slate-100 disabled:text-slate-500 disabled:cursor-not-allowed">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal
                                                    Mulai KP</label>
                                                <input type="date" x-model="tglMulai" <?php echo e(($currentStep > 1 || $isPernahDikunci) ? 'disabled' : ''); ?>

                                                    class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-4 border bg-slate-50/50 focus:bg-white transition-all disabled:bg-slate-100 disabled:text-slate-500 disabled:cursor-not-allowed">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal
                                                    Selesai KP</label>
                                                <input type="date" x-model="tglSelesai" <?php echo e(($currentStep > 1 || $isPernahDikunci) ? 'disabled' : ''); ?>

                                                    class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-4 border bg-slate-50/50 focus:bg-white transition-all disabled:bg-slate-100 disabled:text-slate-500 disabled:cursor-not-allowed">
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                                <div class="space-y-6">
                                    <div class="flex items-center justify-between px-2">
                                        <h2 class="text-sm font-bold text-slate-500 uppercase tracking-widest">Dokumen
                                            Pelaksanaan</h2>
                                        <span class="text-[10px] text-slate-400 italic">Daftar dokumen sesuai ketentuan
                                            Koordinator KP</span>
                                    </div>

                                    <div class="mt-4 space-y-3">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $templatesDokumen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tmpl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <?php
                                                $jenis = $tmpl->title;
                                                $docGroup = $dokumenByJenis->get($jenis);
                                                $latestDoc = $docGroup ? $docGroup->sortByDesc('created_at')->first() : null;

                                                if ($latestDoc && strtolower($latestDoc->status_validasi ?? '') === 'draft') {
                                                    $status = 'draft';
                                                } else {
                                                    $status = $latestDoc ? strtolower($latestDoc->approval_status ?? $latestDoc->status_validasi) : 'belum';
                                                }

                                                $statusMap = [
                                                    'belum' => ['label' => 'Belum Ada', 'class' => 'bg-slate-100 text-slate-500'],
                                                    'draft' => ['label' => 'Di-Draft (Disimpan)', 'class' => 'bg-slate-100 text-slate-600'],
                                                    'menunggu' => ['label' => 'Menunggu Validasi', 'class' => 'bg-amber-100 text-amber-700'],
                                                    'pending' => ['label' => 'Menunggu Validasi', 'class' => 'bg-amber-100 text-amber-700'],
                                                    'disetujui' => ['label' => 'Disetujui', 'class' => 'bg-emerald-100 text-emerald-700'],
                                                    'approved' => ['label' => 'Disetujui', 'class' => 'bg-emerald-100 text-emerald-700'],
                                                    'ditolak' => ['label' => 'Revisi', 'class' => 'bg-rose-100 text-rose-700'],
                                                    'rejected' => ['label' => 'Ditolak', 'class' => 'bg-red-100 text-red-700'],
                                                    'revision' => ['label' => 'Revisi', 'class' => 'bg-rose-100 text-rose-700'],
                                                ];
                                                $st = $statusMap[$status] ?? $statusMap['belum'];
                                            ?>

                                            <div
                                                class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl border border-slate-200 bg-white hover:border-indigo-300 hover:shadow-sm transition-all gap-4">

                                                
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 bg-slate-50 text-slate-500 shadow-sm border border-slate-100">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="1.8"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <p class="text-sm font-bold text-slate-800"><?php echo e($tmpl->title); ?></p>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tmpl->is_uploadable): ?>
                                                                <span
                                                                    class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest <?php echo e($st['class']); ?>">
                                                                    <?php echo e($st['label']); ?>

                                                                </span>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        </div>
                                                        <p class="text-[11px] text-slate-500 mt-0.5">
                                                            <?php echo e($tmpl->description ?? 'Dokumen pelaksanaan KP'); ?>

                                                        </p>

                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tmpl->is_uploadable && $latestDoc): ?>
                                                            <div class="mt-1 flex items-center gap-2">
                                                                <p class="text-[10px] text-slate-400">
                                                                    v.<?php echo e(\Carbon\Carbon::parse($latestDoc->created_at)->format('d.m.Y')); ?>

                                                                </p>
                                                                <a href="<?php echo e($latestDoc->file_url); ?>" target="_blank"
                                                                    class="text-[10px] font-bold text-indigo-600 hover:text-indigo-700">LIHAT
                                                                    FILE</a>
                                                            </div>
                                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($status, ['ditolak', 'rejected', 'revisi', 'revision']) && !empty($latestDoc->revision_note) && $latestDoc->revision_note !== '-'): ?>
                                                                <div
                                                                    class="mt-2 p-2.5 bg-red-50/80 border border-red-100 rounded-lg text-[11.5px] text-red-700 font-medium">
                                                                    💡 <strong
                                                                        class="uppercase text-[10px] tracking-wider text-red-800">Catatan
                                                                        Revisi:</strong> <br>
                                                                    <span
                                                                        class="leading-relaxed mt-0.5 inline-block"><?php echo e($latestDoc->revision_note); ?></span>
                                                                </div>
                                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </div>
                                                </div>

                                                
                                                <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tmpl->is_downloadable && !empty($tmpl->file_path)): ?>
                                                        <a href="<?php echo e(route('eoffice.kp.mahasiswa.dokumen.template', $tmpl->id)); ?>"
                                                            class="inline-flex items-center px-3 py-2 text-xs font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                            </svg>
                                                            Template
                                                        </a>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tmpl->is_uploadable): ?>
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($status, ['pending', 'menunggu', 'approved', 'disetujui'])): ?>
                                                            <div class="flex items-center gap-2">
                                                                <span
                                                                    class="inline-flex items-center px-4 py-2 text-xs font-bold text-slate-500 bg-slate-50 border border-slate-200 rounded-lg">
                                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                                    </svg>
                                                                    Terkunci (<?php echo e($st['label']); ?>)
                                                                </span>
                                                            </div>
                                                        <?php else: ?>
                                                            <form action="<?php echo e(route('eoffice.kp.mahasiswa.dokumen.store')); ?>"
                                                                method="POST" enctype="multipart/form-data"
                                                                class="flex flex-wrap items-center gap-2 w-full sm:w-auto"
                                                                x-data="{ isUploading: false }">
                                                                <?php echo csrf_field(); ?>
                                                                <input type="hidden" name="jenis_dokumen" value="<?php echo e($tmpl->title); ?>">
                                                                <input type="hidden" name="instansi_kp" x-bind:value="instansi">
                                                                <input type="hidden" name="judul_kp" x-bind:value="judul">
                                                                <input type="hidden" name="tanggal_mulai" x-bind:value="tglMulai">
                                                                <input type="hidden" name="tanggal_selesai" x-bind:value="tglSelesai">
                                                                <div class="relative w-full sm:w-auto" x-show="!isUploading">
                                                                    <input type="file" name="file" id="dokumen_<?php echo e($tmpl->id); ?>"
                                                                        class="sr-only peer" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg"
                                                                        required
                                                                        x-on:change="isUploading = true; globalUploading = true; $event.target.closest('form').submit()">
                                                                    <label for="dokumen_<?php echo e($tmpl->id); ?>"
                                                                        class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 cursor-pointer transition-colors peer-focus:ring-2 peer-focus:ring-offset-2 peer-focus:ring-slate-300">
                                                                        <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                                        </svg>
                                                                        <span class="truncate max-w-[120px]">
                                                                            <?php echo e($latestDoc ? 'Unggah Ulang' : 'Upload File'); ?>

                                                                        </span>
                                                                    </label>
                                                                </div>
                                                                <div x-show="isUploading" style="display: none;"
                                                                    class="inline-flex items-center justify-center px-4 py-2 text-xs font-medium text-slate-500 bg-slate-50 border border-slate-200 rounded-lg">
                                                                    <svg class="animate-spin w-4 h-4 mr-1.5 text-indigo-500"
                                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                        viewBox="0 0 24 24">
                                                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                            stroke="currentColor" stroke-width="4"></circle>
                                                                        <path class="opacity-75" fill="currentColor"
                                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                        </path>
                                                                    </svg>
                                                                    Menyimpan Draft...
                                                                </div>
                                                            </form>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                            </div>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                            <div
                                                class="flex flex-col items-center justify-center py-10 bg-white rounded-2xl border border-slate-200">
                                                <div
                                                    class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 mb-3">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                                <p class="text-sm font-bold text-slate-600">Belum Ada Dokumen</p>
                                                <p class="text-xs text-slate-400 mt-1">Koordinator KP belum mengunggah template
                                                    dokumen.</p>
                                            </div>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>


                                
                                <div class="mt-8 flex justify-end">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($curState === 0): ?>
                                        <div x-show="!isDataLengkap || <?php echo e(!$semuaLengkap ? 'true' : 'false'); ?>">
                                            <button disabled
                                                class="inline-flex items-center gap-2 px-6 py-3 bg-slate-300 text-white text-sm font-bold rounded-xl cursor-not-allowed">
                                                Lengkapi Semua Dokumen & Form Dahulu
                                            </button>
                                        </div>
                                        <div x-cloak x-show="isDataLengkap && <?php echo e($semuaLengkap ? 'true' : 'false'); ?>">
                                            <form x-ref="kunciForm" action="<?php echo e(route('eoffice.kp.mahasiswa.dokumen.submit_validasi')); ?>"
                                                method="POST">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="instansi_kp" x-bind:value="instansi">
                                                <input type="hidden" name="judul_kp" x-bind:value="judul">
                                                <input type="hidden" name="tanggal_mulai" x-bind:value="tglMulai">
                                                <input type="hidden" name="tanggal_selesai" x-bind:value="tglSelesai">
                                                <button type="button" @click="showModal = true"
                                                    class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition-all shadow-sm active:scale-95">
                                                    Simpan
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    <?php elseif($curState === 1): ?>
                                        <button disabled
                                            class="inline-flex items-center gap-2 px-6 py-3 bg-slate-200 text-slate-500 text-sm font-bold rounded-xl cursor-not-allowed">
                                            Simpan
                                        </button>
                                    <?php elseif($curState === 2): ?>
                                        <form action="<?php echo e(route('eoffice.kp.mahasiswa.dokumen.lanjut_pasca_kp')); ?>"
                                            method="POST">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit"
                                                onclick="return confirm('Selamat! Semua persyaratan telah Disetujui Dosen.\n\nKlik OK untuk masuk ke Halaman Pendaftaran Seminar.')"
                                                class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition-all shadow-sm active:scale-95">
                                                Lanjut Registrasi Seminar
                                                <svg class="w-5 h-5 animate-bounce" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                </svg>
                                            </button>
                                        </form>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                            
                            
                            <div x-cloak style="display: none;" x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                                <!-- Backdrop -->
                                <div x-show="showModal" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    @click="showModal = false">
                                </div>
                            
                                <!-- Modal Panel -->
                                <div x-show="showModal" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 overflow-hidden"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                                    
                                    <div class="flex items-start gap-4">
                                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-slate-900">Peringatan Sistem</h3>
                                            <p class="mt-2 text-[13px] text-slate-600 leading-relaxed">
                                                Pastikan kembali status seluruh dokumen Anda sudah lengkap dan benar. Setelah disimpan, data laporan dan dokumen ini akan dikunci dan <span class="font-bold text-slate-800">tidak dapat diubah lagi</span> kecuali jika ditolak/direvisi.
                                            </p>
                                            <p class="mt-3 text-[13px] font-bold text-amber-800">Lanjutkan menyimpan?</p>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-8 flex justify-end gap-3 pt-5 border-t border-slate-100">
                                        <button @click="showModal = false" type="button" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-slate-600 hover:bg-slate-50 transition-all">
                                            Batal
                                        </button>
                                        <button @click="$refs.kunciForm.submit()" type="button" class="px-5 py-2.5 rounded-xl bg-blue-600 text-sm font-bold text-white hover:bg-blue-700 shadow-sm transition-all focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 hover:-translate-y-0.5">
                                            Yakin, Simpan & Kunci
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                </main>
            </div>
        </div>
    </div>

    
    <div x-cloak x-show="globalUploading" class="fixed inset-0 z-[999] flex flex-col items-center justify-center bg-slate-900/50 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="bg-white p-8 rounded-3xl shadow-2xl flex flex-col items-center max-w-sm w-full mx-4 border border-slate-100 transform transition-all">
            <div class="relative w-20 h-20 mb-6 flex items-center justify-center">
                <div class="absolute inset-0 rounded-full border-4 border-indigo-100"></div>
                <div class="absolute inset-0 rounded-full border-4 border-indigo-600 border-t-transparent animate-spin"></div>
                <svg class="w-8 h-8 text-indigo-600 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
            </div>
            <h3 class="text-xl font-black text-slate-800 mb-2">Mengunggah File...</h3>
            <p class="text-sm font-medium text-slate-500 text-center leading-relaxed">
                Tindakan Anda sedang diproses oleh sistem. Harap tunggu sejenak dan jangan menutup atau merefresh halaman ini.
            </p>
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\kp\mahasiswa\dokumen.blade.php ENDPATH**/ ?>