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

    <?php $__env->startSection('breadcrumbs'); ?>
    <span class="text-slate-500 hover:text-primary transition-colors">Manajemen Modul</span>
    <span class="mx-2 text-slate-300">/</span>
    <a href="<?php echo e(route('banksoal.soal.gpm.validasi-bank-soal')); ?>" class="text-slate-500 hover:text-primary transition-colors">Validasi Soal</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Review Soal</span>
    <?php $__env->stopSection(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.ui.page-header','data' => ['title' => 'Validasi Bank Soal','subtitle' => 'Evaluasi kesesuaian butir soal dengan CPL']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::ui.page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Validasi Bank Soal','subtitle' => 'Evaluasi kesesuaian butir soal dengan CPL']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('actions', null, []); ?> 
            <a href="<?php echo e(route('banksoal.soal.gpm.validasi-bank-soal')); ?>" class="inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-sm">
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

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="text-sm text-slate-700">
            <span class="font-semibold">Mata Kuliah:</span> <?php echo e($soal->mk_nama); ?> (<?php echo e($soal->mk_kode); ?>)
            <span class="mx-2 text-slate-300">|</span>
            <span class="font-semibold">Dosen:</span> Budi Santoso
        </div>
        <div class="flex items-center gap-3 text-sm text-slate-700">
            <span class="font-semibold">Review Progress:</span>
            Soal <?php echo e($currentIndex ?? $soal->id); ?> dari <?php echo e($totalSoalMK ?? '?'); ?>

            <div class="h-2 w-40 rounded-full bg-slate-200 overflow-hidden">
                <div class="h-full bg-primary" data-progress="<?php echo e((int) ($progressPercentage ?? 0)); ?>"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="bg-slate-50 rounded-2xl border border-slate-200 p-5">
            <div class="flex items-center gap-2 text-primary font-semibold mb-4">
                <i class="far fa-dot-circle"></i>
                Target Capaian Pembelajaran (CPL)
            </div>

            <div class="text-[11px] uppercase tracking-wider font-semibold text-slate-500">Kode Capaian</div>
            <div class="text-lg font-bold text-slate-900 mb-4"><?php echo e($soal->cpl_kode); ?></div>

            <div class="text-[11px] uppercase tracking-wider font-semibold text-slate-500">Deskripsi Kompetensi (CPL)</div>
            <p class="text-sm text-slate-600 leading-relaxed mb-5"><?php echo e($soal->cpl_deskripsi); ?></p>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($soal->cpmk_kode): ?>
                <div class="h-px bg-slate-200 my-4"></div>
                <div class="flex items-center gap-2 text-primary font-semibold mb-4">
                    <i class="far fa-dot-circle"></i>
                    Target Capaian Mata Kuliah (CPMK)
                </div>
                <div class="text-[11px] uppercase tracking-wider font-semibold text-slate-500">Kode Capaian</div>
                <div class="text-lg font-bold text-slate-900 mb-4"><?php echo e($soal->cpmk_kode); ?></div>
                <div class="text-[11px] uppercase tracking-wider font-semibold text-slate-500">Deskripsi Kompetensi (CPMK)</div>
                <p class="text-sm text-slate-600 leading-relaxed"><?php echo e($soal->cpmk_deskripsi); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <span class="mt-4 inline-flex items-center rounded-full bg-primary px-3 py-1 text-[11px] font-semibold text-white">
                Level Kognitif: C4 (Menganalisis)
            </span>
        </div>

        <div class="lg:col-span-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($review) && !empty($review->catatan)): ?>
                <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 p-4 flex items-start gap-3">
                    <i class="fas fa-history text-amber-600 mt-0.5"></i>
                    <div>
                        <p class="text-xs font-semibold text-amber-800">Riwayat Catatan GPM Terakhir</p>
                        <p class="text-sm text-amber-700"><?php echo e($review->catatan); ?></p>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600">Soal ID. <?php echo e($soal->id); ?></span>
                    <span class="text-xs text-slate-500">Tipe: Pilihan Ganda</span>
                </div>

                <div class="text-base font-semibold text-slate-900 leading-relaxed mb-6">
                    <?php echo $soal->soal; ?>

                </div>

                <div class="space-y-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $opsi_jawaban; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opsi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="flex items-center gap-3 rounded-lg border <?php echo e($opsi->is_benar ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200 bg-white'); ?> px-4 py-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full <?php echo e($opsi->is_benar ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-700'); ?> text-xs font-semibold">
                                <?php echo e($opsi->opsi); ?>

                            </div>
                            <div class="text-sm <?php echo e($opsi->is_benar ? 'text-emerald-800 font-medium' : 'text-slate-600'); ?> flex-1"><?php echo e($opsi->deskripsi); ?></div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($opsi->is_benar): ?>
                                <i class="far fa-check-circle text-emerald-500"></i>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>

                <form action="<?php echo e(route('banksoal.soal.gpm.validasi-bank-soal.store', ['mk_id' => request('mk_id')])); ?>" method="POST" class="mt-6" id="validasiForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="pertanyaan_id" value="<?php echo e($soal->id); ?>">
                    <input type="hidden" name="status_review" id="statusReview" value="Sesuai">

                    <div class="border-t border-dashed border-slate-200 pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Parameter Penilaian</p>
                            <span class="text-[11px] font-medium text-slate-400">Total <?php echo e($totalBobot); ?> Poin</span>
                        </div>

                        <div class="space-y-3 max-h-[300px] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-slate-200">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $parameters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $param): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 shadow-sm">
                                    <p class="text-sm font-semibold text-slate-700"><?php echo e($index + 1); ?>. <?php echo e($param->aspek); ?> <span class="text-primary">(<?php echo e($param->bobot); ?> poin)</span></p>
                                    <div class="mt-3 flex gap-6 text-sm text-slate-600">
                                        <label class="inline-flex items-center gap-2 cursor-pointer font-medium hover:text-primary transition-colors">
                                            <input type="radio" name="parameter_<?php echo e($param->id); ?>" value="1" data-bobot="<?php echo e($param->bobot); ?>" class="w-4 h-4 text-primary border-slate-300 focus:ring-primary" onchange="hitungSkor()" required> Sesuai
                                        </label>
                                        <label class="inline-flex items-center gap-2 cursor-pointer font-medium hover:text-rose-600 transition-colors">
                                            <input type="radio" name="parameter_<?php echo e($param->id); ?>" value="0" data-bobot="<?php echo e($param->bobot); ?>" class="w-4 h-4 text-rose-600 border-slate-300 focus:ring-rose-500" onchange="hitungSkor()" required> Tidak Sesuai
                                        </label>
                                    </div>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <div class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-600">Tidak ada parameter penilaian yang tersedia</div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="mt-5 border-y border-dashed border-slate-200 py-4 flex items-center justify-between bg-white">
                            <span class="text-sm font-bold text-slate-700">Skor Evaluasi (Otomatis)</span>
                            <span class="text-2xl font-black text-slate-300" id="nilaiAkhir">0/<?php echo e($totalBobot); ?></span>
                        </div>

                        <div class="mt-5">
                            <label for="catatan" class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 block">Catatan Revisi</label>
                            <textarea id="catatan" name="catatan" placeholder="Masukkan feedback untuk dosen..." class="w-full min-h-[120px] rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"></textarea>
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row gap-3 pt-2">
                            <button type="submit" class="flex-1 rounded-xl border border-rose-200 px-4 py-3 text-sm font-bold text-rose-600 hover:bg-rose-50 transition-colors flex items-center justify-center gap-2" id="btnKembalikan" onclick="setKembalikan()">
                                Kembalikan Ke Dosen <i class="fas fa-undo"></i>
                            </button>
                            <button type="submit" class="flex-1 rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white hover:bg-primary/90 disabled:bg-slate-200 disabled:text-slate-400 disabled:cursor-not-allowed transition-all flex items-center justify-center gap-2" id="btnSetujui" onclick="setSetuju()">
                                Valid dan Lanjut <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-progress]').forEach((bar) => {
            const progress = Number(bar.getAttribute('data-progress') || 0);
            bar.style.width = `${progress}%`;
        });

        function hitungSkor() {
            const form = document.getElementById('validasiForm');
            const nilaiAkhirEl = document.getElementById('nilaiAkhir');
            let totalNilai = 0;

            const inputs = form.querySelectorAll('input[type="radio"]:checked');
            inputs.forEach(input => {
                if (input.value === '1') {
                    totalNilai += parseInt(input.getAttribute('data-bobot')) || 0;
                }
            });

            nilaiAkhirEl.textContent = totalNilai + '/<?php echo e($totalBobot); ?>';
            
            // Ubah warna skor mengikuti nilai
            const MIN_SCORE = <?php echo e($skorMinimum); ?>;
            if (totalNilai >= MIN_SCORE) {
                nilaiAkhirEl.classList.remove('text-slate-300', 'text-rose-600');
                nilaiAkhirEl.classList.add('text-emerald-600');
            } else if (totalNilai > 0) {
                nilaiAkhirEl.classList.remove('text-slate-300', 'text-emerald-600');
                nilaiAkhirEl.classList.add('text-rose-600');
            } else {
                nilaiAkhirEl.classList.remove('text-emerald-600', 'text-rose-600');
                nilaiAkhirEl.classList.add('text-slate-300');
            }

            updateButtonState(totalNilai);
        }

        function updateButtonState(score) {
            const MIN_SCORE = <?php echo e($skorMinimum); ?>;
            const btnSetujui = document.getElementById('btnSetujui');
            if (!btnSetujui) return;

            const totalParams = <?php echo e($parameters->count()); ?>;
            const allAnswered = document.querySelectorAll('input[type="radio"]:checked').length === totalParams;
            
            if (!allAnswered || score < MIN_SCORE) {
                btnSetujui.disabled = true;
                btnSetujui.setAttribute('title', `Seluruh parameter wajib diisi. Nilai < ${MIN_SCORE} silakan kembalikan soal.`);
            } else {
                btnSetujui.disabled = false;
                btnSetujui.setAttribute('title', '');
            }
        }

        function setKembalikan() {
            const catatan = document.getElementById('catatan');
            catatan.required = true;
            document.getElementById('statusReview').value = 'Revisi Total';
        }

        function setSetuju() {
            const form = document.getElementById('validasiForm');
            const totalParams = <?php echo e($parameters->count()); ?>;
            const allAnswered = document.querySelectorAll('input[type="radio"]:checked').length === totalParams;
            if(!allAnswered) {
                // Biarkan validasi HTML5 handle prevent form submission
                return;
            }
            
            const btnSetujui = document.getElementById('btnSetujui');
            if(btnSetujui.disabled) {
                // Pencegahan ekstra
                event.preventDefault();
                return;
            }
            
            const catatan = document.getElementById('catatan');
            catatan.required = false;
            document.getElementById('statusReview').value = 'Sesuai';
        }

        document.getElementById('btnSetujui').addEventListener('mousedown', function (e) {
            const totalParams = <?php echo e($parameters->count()); ?>;
            const allAnswered = document.querySelectorAll('input[type="radio"]:checked').length === totalParams;
            if(!allAnswered) {
                return;
            }
            if (this.disabled) {
                e.preventDefault();
                const MIN_SCORE = <?php echo e($skorMinimum); ?>;
                alert(`Nilai di bawah standar (< ${MIN_SCORE}). Silakan kembalikan soal ini dengan menyertakan catatan revisi.`);
            }
        });
        
        // Initial hitung
        hitungSkor();
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
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\gpm\validasi-bank-soal-review.blade.php ENDPATH**/ ?>