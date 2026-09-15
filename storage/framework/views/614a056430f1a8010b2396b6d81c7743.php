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
        <span class="text-slate-800 font-semibold">Buat Soal Baru</span>
    <?php $__env->stopSection(); ?>
    <?php if (isset($component)) { $__componentOriginal4f4ab3c60e35d72431cf46a8394b8c9b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f4ab3c60e35d72431cf46a8394b8c9b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.page-header','data' => ['title' => 'Buat Soal Baru','subtitle' => 'Lengkapi formulir untuk menambahkan butir soal ke bank soal.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Buat Soal Baru','subtitle' => 'Lengkapi formulir untuk menambahkan butir soal ke bank soal.']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.panel','data' => ['title' => 'Form Soal','subtitle' => 'Gunakan format pilihan ganda dan tandai satu jawaban benar.','padding' => 'p-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.panel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Form Soal','subtitle' => 'Gunakan format pilihan ganda dan tandai satu jawaban benar.','padding' => 'p-0']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <form action="<?php echo e(route('banksoal.soal.dosen.store')); ?>" method="POST" id="formSoal">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label for="mk_id" class="mb-2 block text-sm font-semibold text-slate-700">Mata Kuliah</label>
                    <select name="mk_id" id="mk_id" class="w-full px-3 py-2.5 text-sm rounded-lg border border-slate-200 focus:outline-none" required>
                        <option value="">Pilih Mata Kuliah...</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $mataKuliahDosen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($mk->id); ?>" <?php echo e(old('mk_id') == $mk->id ? 'selected' : ''); ?>><?php echo e($mk->kode); ?> - <?php echo e($mk->nama); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['mk_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label for="cpl_id" class="mb-2 block text-sm font-semibold text-slate-700">CPL</label>
                    <select name="cpl_id" id="cpl_id" class="w-full px-3 py-2.5 text-sm rounded-lg border border-slate-200 focus:outline-none" required><option value="">Pilih CPL...</option></select>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['cpl_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label for="cpmk_id" class="mb-2 block text-sm font-semibold text-slate-700">CPMK</label>
                    <select name="cpmk_id" id="cpmk_id" class="w-full px-3 py-2.5 text-sm rounded-lg border border-slate-200 focus:outline-none" required><option value="">Pilih CPMK...</option></select>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['cpmk_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label for="kesulitan" class="mb-2 block text-sm font-semibold text-slate-700">Tingkat Kesulitan</label>
                    <select name="kesulitan" id="kesulitan" class="w-full px-3 py-2.5 text-sm rounded-lg border border-slate-200 focus:outline-none" required>
                        <option value="easy" <?php echo e(old('kesulitan') == 'easy' ? 'selected' : ''); ?>>Easy</option>
                        <option value="intermediate" <?php echo e(old('kesulitan') == 'intermediate' ? 'selected' : ''); ?>>Intermediate</option>
                        <option value="advanced" <?php echo e(old('kesulitan') == 'advanced' ? 'selected' : ''); ?>>Advanced</option>
                    </select>
                </div>
                <div>
                    <label for="bobot" class="mb-2 block text-sm font-semibold text-slate-700">Bobot / Skor</label>
                    <input type="number" id="bobot" name="bobot" min="1" max="10" value="<?php echo e(old('bobot', 10)); ?>" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:outline-none" required oninput="if(this.value > 10) this.value = 10; if(this.value < 1) this.value = 1;">
                </div>
            </div>

            <div class="px-6 pb-6">
                <label class="mb-2 block text-sm font-semibold text-slate-700">Tipe Pertanyaan</label>
                <div class="flex items-center gap-5 text-sm font-medium text-slate-700">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="tipe_soal" value="pilihan_ganda" id="radioPg" <?php echo e(old('tipe_soal', 'pilihan_ganda') == 'pilihan_ganda' ? 'checked' : ''); ?> class="w-4 h-4" onchange="toggleTipeSoal(this.value)"> 
                        Pilihan Ganda
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="tipe_soal" value="essay" id="radioEssay" <?php echo e(old('tipe_soal') == 'essay' ? 'checked' : ''); ?> class="w-4 h-4" onchange="toggleTipeSoal(this.value)"> 
                        Essay
                    </label>
                </div>
            </div>

            <div class="px-6 pb-6">
                <label for="soal" class="mb-2 block text-sm font-semibold text-slate-700">Pertanyaan</label>
                <!-- Hidden input untuk disubmit ke form -->
                <input type="hidden" name="soal" id="soalInput" value="<?php echo e(old('soal')); ?>">
                <div id="editor" class="bg-white"><?php echo old('soal'); ?></div>
                <p class="mt-2 text-xs text-slate-500">Anda dapat menambahkan format teks rata-kiri/kanan, list, tabel, dan gambar menggunakan menu di atas.</p>
            </div>

            <div id="opsiContainerWrapper" class="border-t border-slate-200 px-6 py-5 <?php echo e(old('tipe_soal', 'pilihan_ganda') == 'essay' ? 'hidden' : ''); ?>">
                <div class="mb-3 flex items-center justify-between"><label class="text-sm font-semibold text-slate-700">Opsi Jawaban & Kunci</label><button type="button" id="addOptionBtn" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">+ Tambah Opsi</button></div>
                <div id="optionsContainer" class="space-y-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i < max(count(old('jawaban', []) ?: [0, 1, 2]), 3); $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <label class="option-item flex items-start gap-3 rounded-lg border border-slate-200 p-4 <?php echo e(old('jawaban_benar') == (string)$i ? 'border-green-400 bg-green-50' : ''); ?>">
                            <input type="radio" name="jawaban_benar" value="<?php echo e($i); ?>" class="mt-1 h-4 w-4 text-green-600 focus:ring-green-500" <?php echo e(old('jawaban_benar') == (string)$i ? 'checked' : ''); ?> required>
                            <div class="flex-1"><span class="option-title mb-1 block text-[11px] font-bold uppercase tracking-wide <?php echo e(old('jawaban_benar') == (string)$i ? 'text-green-700' : 'text-slate-400'); ?>">Pilihan <?php echo e(chr(65 + $i)); ?> <?php echo e(old('jawaban_benar') == (string)$i ? '(Kunci Jawaban)' : ''); ?></span><input type="text" name="jawaban[<?php echo e($i); ?>][teks]" value="<?php echo e(old('jawaban.'.$i.'.teks')); ?>" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none" required></div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i > 2): ?><button type="button" class="remove-btn text-red-500 hover:text-red-600" aria-label="Hapus opsi"><i class="fas fa-times-circle"></i></button><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </label>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>

            <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-6 py-4">
                <a href="<?php echo e(route('banksoal.soal.dosen.index')); ?>" class="text-sm font-semibold text-slate-600 hover:text-slate-700">Batalkan</a>
                <div class="flex items-center gap-2"><button type="submit" name="submit_action" value="draft" class="rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary transition-colors">Simpan Draft</button><button type="submit" name="submit_action" value="publish" class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary/90 transition-colors">Publikasi Soal</button></div>
            </div>
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
                        ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                        ['blockquote', 'code-block'],
                        [{ 'header': 1 }, { 'header': 2 }],               // custom button values
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
                        [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
                        [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
                        [{ 'align': [] }],
                        ['clean'],                                         // remove formatting button
                        ['link', 'image', 'video']                         // link and image, video
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

            // Sinkronkan Quill HTML ke hidden input sebelum submit form
            var form = document.getElementById('formSoal');
            form.addEventListener('submit', function() {
                // Populate hidden input on submit
                var soalInput = document.getElementById('soalInput');
                // Mengambil HTML dari editor, jika kosong/hanya whitespace dikembalikan string kosong supaya validasi server menangkap
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
            const oldCplId = "<?php echo e(old('cpl_id')); ?>";
            const oldCpmkId = "<?php echo e(old('cpmk_id')); ?>";
            
            mkSelect.addEventListener('change', function() {
                const mkId = this.value;
                cplSelect.innerHTML = '<option value="">Memuat CPL...</option>';
                cpmkSelect.innerHTML = '<option value="">Pilih CPMK...</option>';
                if (mkId) {
                    fetch(`<?php echo e(route('banksoal.rps.dosen.cpl', '')); ?>/${mkId}`)
                        .then(r => r.json())
                        .then(data => {
                            cplSelect.innerHTML = '<option value="">Pilih CPL...</option>';
                            data.forEach(c => {
                                const selected = oldCplId == c.id ? 'selected' : '';
                                cplSelect.innerHTML += `<option value="${c.id}" ${selected}>${c.kode} - ${c.deskripsi.substring(0, 60)}...</option>`;
                            });
                            // Trigger change on CPL to load CPMK if old value exists
                            if (oldCplId) cplSelect.dispatchEvent(new Event('change'));
                        })
                        .catch(() => { cplSelect.innerHTML = '<option value="">Gagal memuat cpl</option>'; });
                } else {
                    cplSelect.innerHTML = '<option value="">Pilih CPL...</option>';
                }
            });

            cplSelect.addEventListener('change', function() {
                const cplId = this.value;
                cpmkSelect.innerHTML = '<option value="">Memuat CPMK...</option>';
                if (cplId) {
                    fetch(`<?php echo e(route('banksoal.rps.dosen.cpmk')); ?>?cpl_id=${cplId}&mk_id=${mkSelect.value}`)
                        .then(r => r.json())
                        .then(data => {
                            cpmkSelect.innerHTML = '<option value="">Pilih CPMK...</option>';
                            data.forEach(c => {
                                const selected = oldCpmkId == c.id ? 'selected' : '';
                                cpmkSelect.innerHTML += `<option value="${c.id}" ${selected}>${c.kode} - ${c.deskripsi.substring(0, 60)}...</option>`;
                            });
                        })
                        .catch(() => { cpmkSelect.innerHTML = '<option value="">Gagal memuat cpmk</option>'; });
                } else {
                    cpmkSelect.innerHTML = '<option value="">Pilih CPMK...</option>';
                }
            });

            if (mkSelect.value) { mkSelect.dispatchEvent(new Event('change')); }
            const container = document.getElementById('optionsContainer');
            const addBtn = document.getElementById('addOptionBtn');
            function updateStyles() {
                const boxes = container.querySelectorAll('.option-item');
                boxes.forEach((box, index) => {
                    const radio = box.querySelector('input[type="radio"]');
                    const title = box.querySelector('.option-title');
                    const letter = String.fromCharCode(65 + index);
                    radio.value = index;
                    if (radio.checked) {
                        box.classList.add('border-green-400', 'bg-green-50');
                        title.classList.remove('text-slate-400');
                        title.classList.add('text-green-700');
                        title.textContent = `Pilihan ${letter} (Kunci Jawaban)`;
                    } else {
                        box.classList.remove('border-green-400', 'bg-green-50');
                        title.classList.add('text-slate-400');
                        title.classList.remove('text-green-700');
                        title.textContent = `Pilihan ${letter}`;
                    }
                });
            }
            addBtn.addEventListener('click', function() {
                const optIndex = container.children.length;
                const letter = String.fromCharCode(65 + optIndex);
                const label = document.createElement('label');
                div.className = 'option-item flex items-start gap-3 rounded-lg border border-slate-200 p-4';
                label.innerHTML = `<input type="radio" name="jawaban_benar" value="${optIndex}" class="mt-1 h-4 w-4 text-green-600 focus:ring-green-500" required><div class="flex-1"><span class="option-title mb-1 block text-[11px] font-bold uppercase tracking-wide text-slate-400">Pilihan ${letter}</span><input type="text" name="jawaban[${optIndex}][teks]" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none" required></div><button type="button" class="remove-btn text-red-500 hover:text-red-600" aria-label="Hapus opsi"><i class="fas fa-times-circle"></i></button>`;
                container.appendChild(label);
                updateStyles();
            });
            container.addEventListener('change', updateStyles);
            container.addEventListener('click', function(e) { if (e.target.closest('.remove-btn')) { e.preventDefault(); e.target.closest('.option-item').remove(); updateStyles(); } });
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\pages\bank-soal\Dosen\create.blade.php ENDPATH**/ ?>