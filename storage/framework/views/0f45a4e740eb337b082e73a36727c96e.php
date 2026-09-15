<?php if (isset($component)) { $__componentOriginal315efe54efa54bd7b6313284b579a4ac = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal315efe54efa54bd7b6313284b579a4ac = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.layouts.dosen-admin','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::layouts.dosen-admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php $__env->startSection('breadcrumbs'); ?>
        <a href="<?php echo e(route('banksoal.arsip.dosen.index')); ?>" class="text-slate-500 hover:text-navy transition-colors">Arsip Soal</a>
        <span class="text-slate-400 mx-2">/</span>
        <span class="text-slate-800 font-semibold">Upload PDF</span>
    <?php $__env->stopSection(); ?>

    <link rel="stylesheet" href="<?php echo e(asset('css/banksoal-ui.css')); ?>">
    <style>
        :root {
            --navy: #0B266E;
            --navy-light: rgba(11, 38, 110, 0.1);
        }
        .bg-navy { background-color: var(--navy); }
        .text-navy { color: var(--navy); }
        .border-navy { border-color: var(--navy); }
        .focus\:border-navy:focus { border-color: var(--navy); }
        .focus\:ring-navy:focus { --tw-ring-color: rgba(11, 38, 110, 0.5); }
        .shadow-navy { --tw-shadow-color: rgba(11, 38, 110, 0.2); }
        
        @keyframes popup {
            0% { opacity: 0; transform: scale(0.95) translateY(10px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        .animate-popup {
            animation: popup 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

    </style>

    <?php if (isset($component)) { $__componentOriginal7543ab43ddc66fd305c9f53ce0a7b55c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7543ab43ddc66fd305c9f53ce0a7b55c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.notification.alerts','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::notification.alerts'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7543ab43ddc66fd305c9f53ce0a7b55c)): ?>
<?php $attributes = $__attributesOriginal7543ab43ddc66fd305c9f53ce0a7b55c; ?>
<?php unset($__attributesOriginal7543ab43ddc66fd305c9f53ce0a7b55c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7543ab43ddc66fd305c9f53ce0a7b55c)): ?>
<?php $component = $__componentOriginal7543ab43ddc66fd305c9f53ce0a7b55c; ?>
<?php unset($__componentOriginal7543ab43ddc66fd305c9f53ce0a7b55c); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginal4f4ab3c60e35d72431cf46a8394b8c9b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f4ab3c60e35d72431cf46a8394b8c9b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.page-header','data' => ['title' => 'Upload PDF Arsip','subtitle' => 'Dokumen akan diarsipkan sebagai format PDF standar.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Upload PDF Arsip','subtitle' => 'Dokumen akan diarsipkan sebagai format PDF standar.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('actions', null, []); ?> 
            <a href="<?php echo e(route('banksoal.arsip.dosen.index')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 font-bold text-slate-700 transition-all hover:bg-slate-50 active:scale-95">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4f4ab3c60e35d72431cf46a8394b8c9b)): ?>
<?php $attributes = $__attributesOriginal4f4ab3c60e35d72431cf46a8394b8c9b; ?>
<?php unset($__attributesOriginal4f4ab3c60e35d72431cf46a8394b8c9b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4f4ab3c60e35d72431cf46a8394b8c9b)): ?>
<?php $component = $__componentOriginal4f4ab3c60e35d72431cf46a8394b8c9b; ?>
<?php unset($__componentOriginal4f4ab3c60e35d72431cf46a8394b8c9b); ?>
<?php endif; ?>

    <div class="max-w-3xl">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center gap-4">
                <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-50 text-rose-500">
                    <i class="fas fa-file-pdf text-xl"></i>
                </span>
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Informasi Arsip</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Lengkapi data untuk mengunggah file PDF.</p>
                </div>
            </div>
            
            <div class="p-6">
                <form action="<?php echo e(route('banksoal.arsip.dosen.upload-pdf')); ?>" method="POST" enctype="multipart/form-data" onsubmit="showLoader()">
                    <?php echo csrf_field(); ?>
                    <div class="space-y-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Mata Kuliah</label>
                            <select name="mk_id" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all" required>
                                <option value="">-- Pilih Mata Kuliah --</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $mataKuliahDosen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($mk->id); ?>"><?php echo e($mk->kode); ?> - <?php echo e($mk->nama); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Kategori Ujian</label>
                                <select name="tipe_ujian" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="uts">UTS (Ujian Tengah Semester)</option>
                                    <option value="uas">UAS (Ujian Akhir Semester)</option>
                                    <option value="kuis">Kuis</option>
                                    <option value="tugas">Tugas</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Nama / Keterangan Arsip</label>
                                <input type="text" name="nama_arsip" placeholder="Cth: Susulan Kelas A" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Tahun Akademik</label>
                                <input type="text" name="tahun_akademik" value="<?php echo e(date('Y')); ?>/<?php echo e(date('Y')+1); ?>" placeholder="Cth: <?php echo e(date('Y')); ?>/<?php echo e(date('Y')+1); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Semester</label>
                                <select name="semester" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all" required>
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                    <option value="Antara">Antara</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Metode Ujian</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="metode_ujian" value="online" checked class="w-4 h-4 text-navy border-slate-300 focus:ring-navy">
                                    <span class="text-sm text-slate-600">Terkomputerisasi (Online)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="metode_ujian" value="offline" class="w-4 h-4 text-navy border-slate-300 focus:ring-navy">
                                    <span class="text-sm text-slate-600">Cetak Kertas (Offline)</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">Tanggal Ujian <span class="text-slate-400 font-normal">(Opsional)</span></label>
                            <input type="date" name="tanggal_ujian" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:border-navy focus:ring-4 focus:ring-navy/5 outline-none transition-all">
                        </div>

                        <?php if (isset($component)) { $__componentOriginal0b118184b6353c55db98fb22b40633fc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0b118184b6353c55db98fb22b40633fc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.upload-zone','data' => ['name' => 'pdf_file','inputId' => 'pdf_file','accept' => 'application/pdf,.pdf','maxLabel' => 'PDF (Maks. 5MB)','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.upload-zone'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'pdf_file','inputId' => 'pdf_file','accept' => 'application/pdf,.pdf','maxLabel' => 'PDF (Maks. 5MB)','required' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0b118184b6353c55db98fb22b40633fc)): ?>
<?php $attributes = $__attributesOriginal0b118184b6353c55db98fb22b40633fc; ?>
<?php unset($__attributesOriginal0b118184b6353c55db98fb22b40633fc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0b118184b6353c55db98fb22b40633fc)): ?>
<?php $component = $__componentOriginal0b118184b6353c55db98fb22b40633fc; ?>
<?php unset($__componentOriginal0b118184b6353c55db98fb22b40633fc); ?>
<?php endif; ?>
                    </div>
                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <button type="submit" class="w-full py-3 rounded-xl bg-navy text-white text-sm font-bold hover:opacity-90 shadow-lg shadow-navy/20 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-upload"></i> Upload PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showLoader() {
            window.showLoader();
        }
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal315efe54efa54bd7b6313284b579a4ac)): ?>
<?php $attributes = $__attributesOriginal315efe54efa54bd7b6313284b579a4ac; ?>
<?php unset($__attributesOriginal315efe54efa54bd7b6313284b579a4ac); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal315efe54efa54bd7b6313284b579a4ac)): ?>
<?php $component = $__componentOriginal315efe54efa54bd7b6313284b579a4ac; ?>
<?php unset($__componentOriginal315efe54efa54bd7b6313284b579a4ac); ?>
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\pages\arsip\Dosen\create-pdf.blade.php ENDPATH**/ ?>