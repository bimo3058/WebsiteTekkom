<?php if (isset($component)) { $__componentOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.layouts.gpm-master','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::layouts.gpm-master'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.page-header','data' => ['title' => 'Manajemen Template RPS','subtitle' => 'Kelola template RPS yang dapat diunduh oleh dosen']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Manajemen Template RPS','subtitle' => 'Kelola template RPS yang dapat diunduh oleh dosen']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

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

    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
            <h3 class="text-sm font-semibold text-slate-900 flex items-center gap-2 mb-4">
                <i class="fas fa-upload text-primary"></i> Upload Template RPS Baru
            </h3>

            <form action="<?php echo e(route('banksoal.rps.gpm.template.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-4" onsubmit="if(this.checkValidity()){ window.showLoader(); return true; }">
                <?php echo csrf_field(); ?>

                <div>
                    <label for="fileTemplate" class="text-xs font-semibold text-slate-600 flex items-center gap-2">
                        <i class="fas fa-file-word text-primary"></i> File Template (Word/DOCX)
                    </label>
                    <div id="uploadBox" class="mt-2 rounded-xl border-2 border-dashed border-slate-200 p-6 text-center cursor-pointer">
                        <div class="text-2xl text-slate-400 mb-2"><i class="fas fa-cloud-upload-alt"></i></div>
                        <div class="text-sm font-semibold text-slate-900">Klik di sini atau seret file untuk upload</div>
                        <div class="text-xs text-slate-500">Format: .doc atau .docx (Maksimal 1MB)</div>
                        <input type="file" id="fileTemplate" name="dokumen" accept=".doc,.docx" required class="hidden">
                        <div id="fileSelected" class="mt-3 hidden">
                            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-700">
                                <i class="fas fa-check-circle mr-2"></i> File terpilih: <span id="selectedFileName"></span>
                            </div>
                        </div>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['dokumen'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="mt-2 text-xs text-rose-600"><i class="fas fa-exclamation-circle"></i> <?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div>
                    <label for="keterangan" class="text-xs font-semibold text-slate-600 flex items-center gap-2">
                        <i class="fas fa-note-sticky text-slate-500"></i> Catatan / Keterangan (Opsional)
                    </label>
                    <textarea
                        id="keterangan"
                        name="keterangan"
                        rows="3"
                        placeholder="Masukkan keterangan tentang template ini, misal: Perbaikan format, update standar, dll."
                        class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
                    ></textarea>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['keterangan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="mt-2 text-xs text-rose-600"><i class="fas fa-exclamation-circle"></i> <?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-3">
                    <button type="reset" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600">Reset</button>
                    <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-white hover:bg-primary/90" id="submitBtn">
                        <i class="fas fa-arrow-up-from-bracket mr-2"></i> Upload Template
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-primary to-primary/80 px-6 py-4 text-white">
                <h3 class="text-sm font-semibold flex items-center gap-2">
                    <i class="fas fa-list"></i> Daftar Template (<?php echo e($templates->count()); ?> versi)
                </h3>
            </div>
            <div class="p-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($templates->isEmpty()): ?>
                    <div class="flex flex-col items-center gap-2 py-6 text-slate-500">
                        <i class="fas fa-file-circle-question text-3xl text-slate-300"></i>
                        <p class="text-sm font-semibold">Belum ada template yang diupload</p>
                        <p class="text-xs">Upload template pertama Anda di atas untuk memulai</p>
                    </div>
                <?php else: ?>
                    <ul class="divide-y divide-slate-200">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <li class="py-4 flex flex-wrap items-center gap-4">
                                <div class="flex items-center gap-3 flex-1">
                                    <i class="fas fa-file-word text-2xl text-primary"></i>
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">
                                            <?php echo e($template->original_filename); ?>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($template->is_latest): ?>
                                                <span class="ml-2 inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">
                                                    <i class="fas fa-star mr-1"></i> Versi Terbaru
                                                </span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                        <div class="text-xs text-slate-500">
                                            <span class="inline-flex items-center rounded-full bg-primary/10 px-2 py-0.5 text-[11px] font-semibold text-primary">v<?php echo e($template->version); ?></span>
                                            <span class="ml-2">Upload oleh: <strong><?php echo e($template->uploadedBy->name); ?></strong></span>
                                            <span class="ml-2">Tanggal: <strong><?php echo e($template->created_at->format('d M Y H:i')); ?></strong></span>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($template->keterangan): ?>
                                                <span class="block">Keterangan: <em><?php echo e($template->keterangan); ?></em></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($template->is_latest): ?>
                                        <span class="rounded-lg bg-emerald-50 px-3 py-1 text-[11px] font-semibold text-emerald-700">AKTIF</span>
                                    <?php else: ?>
                                        <form action="<?php echo e(route('banksoal.rps.gpm.template.destroy', $template->id)); ?>" method="POST" onsubmit="if(confirm('Yakin ingin menghapus template ini?')){ window.showLoader(); return true; } else { return false; }">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50" title="Hapus template">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </li>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </ul>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="rounded-xl border border-primary/20 bg-primary/10 p-4 text-primary">
            <h4 class="text-sm font-semibold flex items-center gap-2">
                <i class="fas fa-info-circle"></i> Informasi Penting
            </h4>
            <ul class="mt-2 text-xs list-disc pl-5 space-y-1">
                <li>Template disimpan dengan struktur folder: <code>TemplateRPS_V1</code>, <code>TemplateRPS_V2</code>, dst</li>
                <li>Dosen hanya dapat mengunduh <strong>versi tertinggi</strong> (V dengan angka paling besar)</li>
                <li>Versi sebelumnya tetap tersimpan untuk referensi dan riwayat</li>
                <li>Hanya satu template yang dapat menjadi "Versi Terbaru" pada saat tertentu</li>
                <li>Ketika upload template baru, template sebelumnya otomatis menjadi tidak aktif</li>
                <li>Template disimpan di Supabase: <code>rps/templates/rps/</code></li>
            </ul>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const uploadBox = document.getElementById('uploadBox');
            const fileInput = document.getElementById('fileTemplate');
            const fileSelected = document.getElementById('fileSelected');
            const selectedFileName = document.getElementById('selectedFileName');

            if (!uploadBox || !fileInput) return;

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadBox.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadBox.addEventListener(eventName, () => {
                    uploadBox.classList.add('border-primary/30', 'bg-primary/10');
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadBox.addEventListener(eventName, () => {
                    uploadBox.classList.remove('border-primary/30', 'bg-primary/10');
                });
            });

            uploadBox.addEventListener('drop', (e) => {
                const files = e.dataTransfer.files;
                fileInput.files = files;
                updateFileDisplay();
            });

            fileInput.addEventListener('change', updateFileDisplay);

            function updateFileDisplay() {
                if (fileInput.files.length > 0) {
                    const fileName = fileInput.files[0].name;
                    selectedFileName.textContent = fileName;
                    fileSelected.classList.remove('hidden');
                } else {
                    fileSelected.classList.add('hidden');
                }
            }

            uploadBox.addEventListener('click', () => {
                fileInput.click();
            });
        });
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039)): ?>
<?php $attributes = $__attributesOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039; ?>
<?php unset($__attributesOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039)): ?>
<?php $component = $__componentOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039; ?>
<?php unset($__componentOriginalbfe3dfc9f8c7e29aaca1eb4d19fe5039); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\pages\rps\Gpm\template-management.blade.php ENDPATH**/ ?>