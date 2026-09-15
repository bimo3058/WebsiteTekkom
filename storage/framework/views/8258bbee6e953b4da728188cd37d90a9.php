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
        <a href="<?php echo e(route('banksoal.soal.dosen.index')); ?>" class="text-slate-500 hover:text-primary transition-colors">Bank Soal</a>
        <span class="mx-2 text-slate-300">/</span>
        <span class="text-slate-800 font-semibold">Edit Soal</span>
    <?php $__env->stopSection(); ?>
    <?php if (isset($component)) { $__componentOriginal4f4ab3c60e35d72431cf46a8394b8c9b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f4ab3c60e35d72431cf46a8394b8c9b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.page-header','data' => ['title' => 'Edit Soal','subtitle' => 'Perbarui detail pertanyaan dan opsi jawaban.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Edit Soal','subtitle' => 'Perbarui detail pertanyaan dan opsi jawaban.']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('actions', null, []); ?> 
            <a href="<?php echo e(route('banksoal.soal.dosen.index')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"><i class="fas fa-arrow-left"></i> Kembali</a>
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

    <?php if (isset($component)) { $__componentOriginaled3368a8117ee5d527ad55a40e435525 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled3368a8117ee5d527ad55a40e435525 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.panel','data' => ['title' => 'Form Edit Soal','subtitle' => 'Pastikan jawaban benar tetap ditandai sebelum menyimpan.','padding' => 'p-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Form Edit Soal','subtitle' => 'Pastikan jawaban benar tetap ditandai sebelum menyimpan.','padding' => 'p-0']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($review) && !empty($review->catatan)): ?>
        <div class="p-6 pb-0">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array(strtolower($soal->status), ['revisi', 'ditolak'])): ?>
                <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle mt-1 text-red-500"></i>
                    <div>
                        <h4 class="text-sm font-bold text-red-800">Catatan Revisi dari GPM</h4>
                        <p class="mt-1 text-sm text-red-700 leading-relaxed"><?php echo e($review->catatan); ?></p>
                    </div>
                </div>
            </div>
            <?php else: ?>
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-history mt-1 text-amber-500"></i>
                    <div>
                        <h4 class="text-sm font-bold text-amber-800">Riwayat Catatan GPM Sebelumnya</h4>
                        <p class="mt-1 text-sm text-amber-700 leading-relaxed"><?php echo e($review->catatan); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        
        <form action="<?php echo e(route('banksoal.soal.dosen.update', $soal->id)); ?>" method="POST" id="formSoal">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2 lg:grid-cols-3">
                <div><label for="mk_id" class="mb-2 block text-sm font-semibold text-slate-700">Mata Kuliah</label><select name="mk_id" id="mk_id" class="w-full px-3 py-2.5 text-sm rounded-lg border border-slate-200 focus:outline-none" required><option value="">Pilih Mata Kuliah...</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $mataKuliahDosen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($mk->id); ?>" <?php echo e(old('mk_id', $soal->mk_id) == $mk->id ? 'selected' : ''); ?>><?php echo e($mk->kode); ?> - <?php echo e($mk->nama); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></div>
                <div><label for="cpl_id" class="mb-2 block text-sm font-semibold text-slate-700">Keterkaitan CPL / Topik</label><select name="cpl_id" id="cpl_id" class="w-full px-3 py-2.5 text-sm rounded-lg border border-slate-200 focus:outline-none" required><option value="">Pilih CPL...</option></select></div>
                <div><label for="cpmk_id" class="mb-2 block text-sm font-semibold text-slate-700">Keterkaitan CPMK</label><select name="cpmk_id" id="cpmk_id" class="w-full px-3 py-2.5 text-sm rounded-lg border border-slate-200 focus:outline-none" required><option value="">Pilih CPMK...</option></select></div>
                <div><label for="kesulitan" class="mb-2 block text-sm font-semibold text-slate-700">Tingkat Kesulitan</label><select name="kesulitan" id="kesulitan" class="w-full px-3 py-2.5 text-sm rounded-lg border border-slate-200 focus:outline-none" required><option value="easy" <?php echo e(old('kesulitan', $soal->kesulitan) == 'easy' ? 'selected' : ''); ?>>Easy</option><option value="intermediate" <?php echo e(old('kesulitan', $soal->kesulitan) == 'intermediate' ? 'selected' : ''); ?>>Intermediate</option><option value="advanced" <?php echo e(old('kesulitan', $soal->kesulitan) == 'advanced' ? 'selected' : ''); ?>>Advanced</option></select></div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Tipe Pertanyaan</label>
                    <div class="flex items-center gap-5 text-sm font-medium text-slate-700 mt-3">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="tipe_soal" value="pilihan_ganda" <?php echo e(old('tipe_soal', $soal->tipe_soal ?? 'pilihan_ganda') == 'pilihan_ganda' ? 'checked' : ''); ?> class="w-4 h-4" onchange="toggleTipeSoal(this.value)"> 
                            Pilihan Ganda
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="tipe_soal" value="essay" <?php echo e(old('tipe_soal', $soal->tipe_soal) == 'essay' ? 'checked' : ''); ?> class="w-4 h-4" onchange="toggleTipeSoal(this.value)"> 
                            Essay
                        </label>
                    </div>
                </div>
            </div>
            <div class="px-6 pb-6">
                <label for="soal" class="mb-2 block text-sm font-semibold text-slate-700">Deskripsi Soal</label>
                <input type="hidden" name="soal" id="soalInput" value="<?php echo e(old('soal', $soal->soal)); ?>">
                <div id="editor" class="bg-white"><?php echo old('soal', $soal->soal); ?></div>
                <p class="mt-2 text-xs text-slate-500">Anda dapat menambahkan format teks rata-kiri/kanan, list, tabel, dan gambar menggunakan menu di atas.</p>
            </div>
            <div id="opsiContainerWrapper" class="border-t border-slate-200 px-6 py-5 <?php echo e(old('tipe_soal', $soal->tipe_soal ?? 'pilihan_ganda') == 'essay' ? 'hidden' : ''); ?>">
                <div class="mb-3 flex items-center justify-between"><label class="text-sm font-semibold text-slate-700">Pilihan Jawaban</label><button type="button" id="addOptionBtn" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50"><i class="fas fa-plus mr-1"></i> Tambah Opsi</button></div>
                <div id="optionsContainer" class="space-y-3">
                    <?php $oldJawaban = old('jawaban', $soal->jawaban->toArray()); $oldJawabanBenar = old('jawaban_benar', $soal->jawaban->search(fn($j) => $j['is_benar'])); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $oldJawaban; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $jawab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <label class="option-item flex items-start gap-3 rounded-lg border p-4 <?php echo e((string)$oldJawabanBenar === (string)$i ? 'border-green-400 bg-green-50' : 'border-slate-200'); ?>"><input type="radio" name="jawaban_benar" value="<?php echo e($i); ?>" <?php echo e((string)$oldJawabanBenar === (string)$i ? 'checked' : ''); ?> required class="mt-1 h-4 w-4 text-green-600 focus:ring-green-500"><div class="flex-1"><span class="option-title mb-1 block text-[11px] font-bold uppercase tracking-wide <?php echo e((string)$oldJawabanBenar === (string)$i ? 'text-green-700' : 'text-slate-400'); ?>">Pilihan <?php echo e(chr(65 + $i)); ?> <?php echo e((string)$oldJawabanBenar === (string)$i ? '(Kunci Jawaban)' : ''); ?></span><input type="text" name="jawaban[<?php echo e($i); ?>][teks]" value="<?php echo e($jawab['deskripsi'] ?? $jawab['teks'] ?? ''); ?>" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none" required></div><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i > 1): ?><button type="button" class="remove-btn text-red-500 hover:text-red-600" aria-label="Hapus opsi"><i class="fas fa-times-circle"></i></button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
            <div class="flex items-center justify-end border-t border-slate-200 bg-slate-50 px-6 py-4"><button type="submit" class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary/90 transition-colors"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button></div>
        </form>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled3368a8117ee5d527ad55a40e435525)): ?>
<?php $attributes = $__attributesOriginaled3368a8117ee5d527ad55a40e435525; ?>
<?php unset($__attributesOriginaled3368a8117ee5d527ad55a40e435525); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled3368a8117ee5d527ad55a40e435525)): ?>
<?php $component = $__componentOriginaled3368a8117ee5d527ad55a40e435525; ?>
<?php unset($__componentOriginaled3368a8117ee5d527ad55a40e435525); ?>
<?php endif; ?>

    <?php $__env->startPush('styles'); ?>
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <style>
        .ql-toolbar.ql-snow {
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            border-color: #e2e8f0;
            background-color: #f8fafc;
            padding: 0.75rem;
        }
        .ql-container.ql-snow {
            border-bottom-left-radius: 0.75rem;
            border-bottom-right-radius: 0.75rem;
            border-color: #e2e8f0;
            min-height: 150px;
            font-family: inherit;
            font-size: 0.875rem;
        }
        .ql-editor {
            min-height: 150px;
        }
        .ql-editor.ql-blank::before {
            color: #94a3b8;
            font-style: normal;
        }
    </style>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <?php $__env->stopPush(); ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var quill = new Quill('#editor', {
                theme: 'snow',
                placeholder: 'Tuliskan deskripsi pertanyaan di sini...',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{ 'header': 1 }, { 'header': 2 }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'script': 'sub'}, { 'script': 'super' }],
                        [{ 'indent': '-1'}, { 'indent': '+1' }],
                        [{ 'size': ['small', false, 'large', 'huge'] }],
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'align': [] }],
                        ['clean'],
                        ['link', 'image', 'video']
                    ]
                }
            });

            // Kustomisasi Handler Upload Gambar untuk mengatur batas ukuran file (Maks 2MB)
            quill.getModule('toolbar').addHandler('image', function() {
                const input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.click();

                input.onchange = () => {
                    const file = input.files[0];
                    if (!file) return;

                    const maxMB = 2; // Maksimal ukuran dalam MB
                    const maxSize = maxMB * 1024 * 1024; 
                    if (file.size > maxSize) {
                        alert(`Ukuran gambar terlalu besar! Maksimal ${maxMB}MB.`);
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const range = quill.getSelection(true);
                        quill.insertEmbed(range.index, 'image', e.target.result);
                        quill.setSelection(range.index + 1);
                    };
                    reader.readAsDataURL(file);
                };
            });

            var form = document.getElementById('formSoal');
            form.addEventListener('submit', function() {
                var soalInput = document.getElementById('soalInput');
                var htmlContent = quill.root.innerHTML;
                if (quill.getText().trim().length === 0 && !htmlContent.includes('<img')) {
                    soalInput.value = '';
                } else {
                    soalInput.value = htmlContent;
                }
                
                if (form.checkValidity()) {
                    window.showLoader();
                }
            });

            const mkSelect = document.getElementById('mk_id');
            const cplSelect = document.getElementById('cpl_id');
            const cpmkSelect = document.getElementById('cpmk_id');
            const oldCplId = "<?php echo e(old('cpl_id', $soal->cpl_id)); ?>";
            const oldCpmkId = "<?php echo e(old('cpmk_id', $soal->cpmk_id)); ?>";
            
            mkSelect.addEventListener('change', function() {
                const mkId = this.value;
                cplSelect.innerHTML = '<option value="">Memuat CPL...</option>';
                cpmkSelect.innerHTML = '<option value="">Pilih CPMK...</option>';
                if (mkId) {
                    fetch(`<?php echo e(route('banksoal.rps.dosen.cpl', '')); ?>/${mkId}`).then(r => r.json()).then(data => {
                        cplSelect.innerHTML = '<option value="">Pilih CPL...</option>';
                        data.forEach(c => { const selected = oldCplId == c.id ? 'selected' : ''; cplSelect.innerHTML += `<option value="${c.id}" ${selected}>${c.kode} - ${c.deskripsi.substring(0, 60)}...</option>`; });
                        if (oldCplId) cplSelect.dispatchEvent(new Event('change'));
                    }).catch(() => { cplSelect.innerHTML = '<option value="">Gagal memuat cpl</option>'; });
                } else {
                    cplSelect.innerHTML = '<option value="">Pilih CPL...</option>';
                }
            });

            cplSelect.addEventListener('change', function() {
                const cplId = this.value;
                cpmkSelect.innerHTML = '<option value="">Memuat CPMK...</option>';
                if (cplId) {
                    fetch(`<?php echo e(route('banksoal.rps.dosen.cpmk')); ?>?cpl_id=${cplId}`).then(r => r.json()).then(data => {
                        cpmkSelect.innerHTML = '<option value="">Pilih CPMK...</option>';
                        data.forEach(c => { const selected = oldCpmkId == c.id ? 'selected' : ''; cpmkSelect.innerHTML += `<option value="${c.id}" ${selected}>${c.kode} - ${c.deskripsi.substring(0, 60)}...</option>`; });
                    }).catch(() => { cpmkSelect.innerHTML = '<option value="">Gagal memuat cpmk</option>'; });
                } else {
                    cpmkSelect.innerHTML = '<option value="">Pilih CPMK...</option>';
                }
            });

            if (mkSelect.value) { mkSelect.dispatchEvent(new Event('change')); }
            const container = document.getElementById('optionsContainer');
            const addBtn = document.getElementById('addOptionBtn');
            let optIndex = container.children.length;
            function updateStyles() {
                const boxes = container.querySelectorAll('.option-item');
                boxes.forEach((box, index) => {
                    const radio = box.querySelector('input[type="radio"]');
                    const title = box.querySelector('.option-title');
                    const letter = String.fromCharCode(65 + index);
                    radio.value = index;
                    if (radio.checked) { box.classList.add('border-green-400', 'bg-green-50'); box.classList.remove('border-slate-200'); title.classList.remove('text-slate-400'); title.classList.add('text-green-700'); title.textContent = `Pilihan ${letter} (Kunci Jawaban)`; }
                    else { box.classList.remove('border-green-400', 'bg-green-50'); box.classList.add('border-slate-200'); title.classList.add('text-slate-400'); title.classList.remove('text-green-700'); title.textContent = `Pilihan ${letter}`; }
                });
            }
            addBtn.addEventListener('click', function() {
                const div = document.createElement('label');
                div.className = 'option-item flex items-start gap-3 rounded-lg border border-slate-200 p-4';
                div.innerHTML = `<input type="radio" name="jawaban_benar" value="${optIndex}" required class="mt-1 h-4 w-4 text-green-600 focus:ring-green-500"><div class="flex-1"><span class="option-title mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-400">Pilihan ${String.fromCharCode(65 + optIndex)}</span><input type="text" name="jawaban[${optIndex}][teks]" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none" required></div><button type="button" class="remove-btn text-red-500 hover:text-red-600" aria-label="Hapus opsi"><i class="fas fa-times-circle"></i></button>`;
                container.appendChild(div);
                optIndex++;
                updateStyles();
            });
            container.addEventListener('change', updateStyles);
            container.addEventListener('click', function(e) { if (e.target.closest('.remove-btn')) { e.target.closest('.option-item').remove(); updateStyles(); } });
            updateStyles();

            // Set initial state based on old value
            const initialTipeSoal = document.querySelector('input[name="tipe_soal"]:checked')?.value || 'pilihan_ganda';
            toggleTipeSoal(initialTipeSoal);
        });

        function toggleTipeSoal(tipe) {
            const wrapper = document.getElementById('opsiContainerWrapper');
            const requiredInputs = wrapper.querySelectorAll('input[required]');
            
            if (tipe === 'essay') {
                wrapper.classList.add('hidden');
                // Remove required attribute from multiple choice inputs when essay is selected
                requiredInputs.forEach(input => {
                    input.dataset.wasRequired = 'true';
                    input.required = false;
                });
            } else {
                wrapper.classList.remove('hidden');
                // Restore required attribute
                const wasRequiredInputs = wrapper.querySelectorAll('input[data-was-required="true"]');
                wasRequiredInputs.forEach(input => {
                    input.required = true;
                });
            }
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\pages\bank-soal\Dosen\edit.blade.php ENDPATH**/ ?>