<?php if (isset($component)) { $__componentOriginal327816c8748951109e999046d52cab34 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal327816c8748951109e999046d52cab34 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.layouts.dosen','data' => ['title' => 'Form Penilaian KP']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::layouts.dosen'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Form Penilaian KP']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php $__env->startSection('breadcrumbs'); ?>
        <a href="<?php echo e(route('eoffice.kp.dosen.bimbingan.show', $kp->id)); ?>"
            class="text-slate-400 hover:text-slate-700 transition-colors mr-2">
            <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <span class="text-[#272835] font-semibold" style="font-family:'Inter Tight',sans-serif;">Form Penilaian Dosen
            Pembimbing</span>
    <?php $__env->stopSection(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div
            class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="font-medium text-sm"><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Info Mahasiswa -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
        <div class="flex items-center gap-4">
            <div
                class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-lg border-2 border-white shadow-sm flex-shrink-0">
                <?php echo e(strtoupper(substr($kp->nama_mahasiswa ?? $kp->nim ?? 'M', 0, 2))); ?>

            </div>
            <div>
                <h1 class="text-lg font-bold text-slate-900"><?php echo e($kp->nama_mahasiswa ?? 'Mahasiswa'); ?></h1>
                <p class="text-sm text-slate-500">NIM: <span
                        class="font-semibold text-slate-700"><?php echo e($kp->nim ?? '-'); ?></span></p>
                <p class="text-xs text-slate-400 mt-0.5 line-clamp-1"><?php echo e($kp->judul_kp ?? '-'); ?></p>
            </div>
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
            <p class="text-sm font-semibold text-blue-800">Nilai Dosen Pembimbing</p>
            <p class="text-xs text-blue-600 mt-1">Bagian ini hanya untuk <strong>Nilai Seminar Pembimbing</strong>.
                Nilai Lapangan diisi oleh Koordinator KP.</p>
        </div>
    </div>

    <!-- Form Penilaian -->
    <form action="<?php echo e(route('eoffice.kp.dosen.bimbingan.penilaian.store', $kp->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Nilai Seminar Pembimbing</h2>
            </div>

            <div class="p-6 space-y-6">

                <?php
                    $komponenDosen = null;
                    if ($kp->periode && $kp->periode->komponenNilai) {
                        $komponenDosen = $kp->periode->komponenNilai->where('role_penilai', 'dosen_pembimbing');
                    }
                ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($komponenDosen && $komponenDosen->isNotEmpty()): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $komponenDosen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $komp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $existingValue = '';
                            if ($kp->nilaiDetail) {
                                $detail = $kp->nilaiDetail->where('komponen_id', $komp->id)->first();
                                if ($detail)
                                    $existingValue = $detail->nilai_angka;
                            }
                        ?>
                        <div>
                            <label for="nilai_<?php echo e($komp->id); ?>" class="block text-sm font-semibold text-slate-700 mb-2">
                                <?php echo e($komp->nama_komponen); ?> (Bobot: <?php echo e($komp->bobot); ?>%) <span class="text-red-500">*</span>
                                <span class="text-slate-400 font-normal">(0 - 100)</span>
                            </label>
                            <input type="number" id="nilai_<?php echo e($komp->id); ?>" name="nilai_<?php echo e($komp->id); ?>" min="0" max="100"
                                step="0.01" value="<?php echo e(old('nilai_' . $komp->id, $existingValue)); ?>" placeholder="Contoh: 85"
                                data-bobot="<?php echo e($komp->bobot); ?>"
                                class="w-full px-4 py-3 text-2xl font-bold text-slate-900 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder:text-slate-300 placeholder:font-normal placeholder:text-base dynamic-grade-input"
                                oninput="updateAverage()" required>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['nilai_' . $komp->id];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-600 text-xs mt-1.5"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                    <!-- Grade Preview -->
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <p class="text-xs text-slate-500 font-medium">Nilai Rata-rata / Grade Otomatis</p>
                                <p class="text-xs text-slate-400 mt-0.5">Berdasarkan total penjumlahan input</p>
                            </div>
                            <div class="text-right flex items-baseline gap-2">
                                <p class="text-2xl font-bold" id="averageDisplay" style="color: #64748b">0.00</p>
                                <p class="text-4xl font-extrabold" id="gradeDisplay" style="color: #94a3b8">-</p>
                            </div>
                        </div>
                        <!-- Grade Scale -->
                        <div class="mt-4 grid grid-cols-5 gap-1.5 text-center text-[10px]">
                            <div class="bg-red-100 text-red-700 rounded-lg p-1.5 font-semibold">E<br><span
                                    class="font-normal">0-44</span></div>
                            <div class="bg-amber-100 text-amber-700 rounded-lg p-1.5 font-semibold">D<br><span
                                    class="font-normal">45-54</span></div>
                            <div class="bg-yellow-100 text-yellow-700 rounded-lg p-1.5 font-semibold">C/C+<br><span
                                    class="font-normal">55-64</span></div>
                            <div class="bg-blue-100 text-blue-700 rounded-lg p-1.5 font-semibold">B/B+<br><span
                                    class="font-normal">65-79</span></div>
                            <div class="bg-emerald-100 text-emerald-700 rounded-lg p-1.5 font-semibold">A<br><span
                                    class="font-normal">80-100</span></div>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- Fallback untuk periode tanpa komponen rubrik -->
                    <!-- Input Nilai Seminar -->
                    <div>
                        <label for="nilai_seminar_pembimbing" class="block text-sm font-semibold text-slate-700 mb-2">
                            Nilai Seminar <span class="text-red-500">*</span>
                            <span class="text-slate-400 font-normal">(0 - 100)</span>
                        </label>
                        <input type="number" id="nilai_seminar_pembimbing" name="nilai_seminar_pembimbing" min="0" max="100"
                            step="0.01"
                            value="<?php echo e($kp->penilaian ? $kp->penilaian->nilai_seminar_pembimbing : old('nilai_seminar_pembimbing')); ?>"
                            placeholder="Contoh: 85"
                            class="w-full px-4 py-3 text-2xl font-bold text-slate-900 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder:text-slate-300 placeholder:font-normal placeholder:text-base dynamic-grade-input-legacy"
                            oninput="updateLegacyGrade()" required>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['nilai_seminar_pembimbing'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-600 text-xs mt-1.5"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <!-- Grade Preview Legacy -->
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-slate-500 font-medium">Grade Otomatis</p>
                                <p class="text-xs text-slate-400 mt-0.5">Berdasarkan nilai yang dimasukkan</p>
                            </div>
                            <div class="text-right flex items-baseline gap-2">
                                <p class="text-4xl font-extrabold" id="gradeDisplayLegacy" style="color: #94a3b8">-</p>
                            </div>
                        </div>

                        <!-- Grade Scale -->
                        <div class="mt-4 grid grid-cols-5 gap-1.5 text-center text-[10px]">
                            <div class="bg-red-100 text-red-700 rounded-lg p-1.5 font-semibold">E<br><span
                                    class="font-normal">0-44</span></div>
                            <div class="bg-amber-100 text-amber-700 rounded-lg p-1.5 font-semibold">D<br><span
                                    class="font-normal">45-54</span></div>
                            <div class="bg-yellow-100 text-yellow-700 rounded-lg p-1.5 font-semibold">C/C+<br><span
                                    class="font-normal">55-64</span></div>
                            <div class="bg-blue-100 text-blue-700 rounded-lg p-1.5 font-semibold">B/B+<br><span
                                    class="font-normal">65-79</span></div>
                            <div class="bg-emerald-100 text-emerald-700 rounded-lg p-1.5 font-semibold">A<br><span
                                    class="font-normal">80-100</span></div>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>



                <!-- Nilai Akhir (read-only info) -->
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kp->penilaian && $kp->penilaian->nilai_akhir): ?>
                    <div class="bg-slate-900 rounded-xl p-4 flex items-center justify-between text-white">
                        <div>
                            <p class="text-xs text-slate-400 font-medium">Nilai Akhir (setelah semua komponen diisi)</p>
                        </div>
                        <p class="text-3xl font-extrabold"><?php echo e($kp->penilaian->nilai_akhir); ?></p>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <!-- Actions -->
            <div class="px-6 pb-6 flex flex-col sm:flex-row gap-3">
                <a href="<?php echo e(route('eoffice.kp.dosen.bimbingan.show', $kp->id)); ?>"
                    class="flex-1 text-center px-6 py-3 border border-slate-300 text-slate-700 font-medium rounded-xl hover:bg-slate-50 transition-colors text-sm">
                    Batal
                </a>
                <button type="submit"
                    class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-sm transition-colors text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Nilai
                </button>
            </div>
        </div>
    </form>

    <?php $__env->startPush('scripts'); ?>
        <script>
            function getGrade(nilai) {
                const n = parseFloat(nilai);
                if (isNaN(n)) return { letter: '-', color: '#94a3b8' };
                if (n >= 80) return { letter: 'A', color: '#10b981' };
                if (n >= 75) return { letter: 'B+', color: '#3b82f6' };
                if (n >= 65) return { letter: 'B', color: '#3b82f6' };
                if (n >= 60) return { letter: 'C+', color: '#f59e0b' };
                if (n >= 55) return { letter: 'C', color: '#f59e0b' };
                if (n >= 45) return { letter: 'D', color: '#f97316' };
                return { letter: 'E', color: '#ef4444' };
            }

            function updateAverage() {
                const inputs = document.querySelectorAll('.dynamic-grade-input');
                let totalWeightedScore = 0;
                let count = 0;
                inputs.forEach(ip => {
                    const val = parseFloat(ip.value);
                    const bobot = parseFloat(ip.getAttribute('data-bobot')) || 0;
                    if (!isNaN(val)) {
                        totalWeightedScore += (val * (bobot / 100));
                    }
                    count++;
                });
                const avg = totalWeightedScore;
                const elAvg = document.getElementById('averageDisplay');
                if (elAvg) elAvg.textContent = avg.toFixed(2);

                const grade = getGrade(avg);
                const el = document.getElementById('gradeDisplay');
                if (el) {
                    el.textContent = grade.letter;
                    el.style.color = grade.color;
                }
            }

            function updateLegacyGrade() {
                const input = document.querySelector('.dynamic-grade-input-legacy');
                if (!input) return;
                const value = input.value;
                const grade = getGrade(value);
                const el = document.getElementById('gradeDisplayLegacy');
                if (el) {
                    el.textContent = grade.letter;
                    el.style.color = grade.color;
                }
            }

            // Inisialisasi saat load
            updateAverage();
            updateLegacyGrade();
        </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal327816c8748951109e999046d52cab34)): ?>
<?php $attributes = $__attributesOriginal327816c8748951109e999046d52cab34; ?>
<?php unset($__attributesOriginal327816c8748951109e999046d52cab34); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal327816c8748951109e999046d52cab34)): ?>
<?php $component = $__componentOriginal327816c8748951109e999046d52cab34; ?>
<?php unset($__componentOriginal327816c8748951109e999046d52cab34); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\dosen\penilaian.blade.php ENDPATH**/ ?>