<?php if (isset($component)) { $__componentOriginal0c501b5a3ea6a4d95bdc63fd0f1f3cbc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0c501b5a3ea6a4d95bdc63fd0f1f3cbc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.layouts.mahasiswa','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::layouts.mahasiswa'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php
        $nim = old('nim', optional(auth()->user()->student)->student_number ?? auth()->user()->external_id);
        $semesterAktif = 1;

        if ($nim && strlen($nim) >= 8) {
            $kodeTahun = substr($nim, 6, 2);
            if (is_numeric($kodeTahun)) {
                $tahunMasuk = 2000 + (int) $kodeTahun;
                $tahunSekarang = (int) date('Y');
                $bulanSekarang = (int) date('n');

                if ($bulanSekarang == 1) {
                    $semesterAktif = (($tahunSekarang - 1) - $tahunMasuk) * 2 + 1;
                } elseif ($bulanSekarang >= 2 && $bulanSekarang <= 7) {
                    $semesterAktif = ($tahunSekarang - $tahunMasuk) * 2;
                } else {
                    $semesterAktif = ($tahunSekarang - $tahunMasuk) * 2 + 1;
                }

                if ($semesterAktif < 1)
                    $semesterAktif = 1;
            }
        }
    ?>

    <div class="flex flex-col lg:flex-row gap-8 xl:gap-16 items-start w-full">

        <!-- LEFT COLUMN: Info -->
        <div class="w-full lg:w-[42%] flex flex-col">

            <div
                class="inline-flex items-center gap-3 text-slate-900 font-bold tracking-[0.2em] text-[10px] uppercase mb-5 border-b border-slate-200 pb-3">
                <span>Periode <?php echo e($activePeriode->nama_periode); ?></span>
            </div>

            <h1 class="text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight mb-4">
                Pendaftaran<br>
                <span class="text-slate-400">Ujian Komprehensif.</span>
            </h1>

            <p class="text-sm text-slate-600 leading-relaxed max-w-lg mb-6">
                Formulir ini digunakan untuk pendaftaran Ujian Komprehensif Program Studi S1 Teknik Komputer bulan
                <strong><?php echo e(\Carbon\Carbon::parse($activePeriode->tanggal_mulai)->translatedFormat('F Y')); ?></strong>.
                Pendaftaran hanya dibuka untuk mahasiswa minimal semester 7 dan diprioritaskan bagi mahasiswa yang telah
                siap mengikuti Sidang Tugas Akhir.<br><br>
                Dengan mengisi formulir ini, Anda menyatakan bersedia mematuhi seluruh aturan ujian yang
                berlaku.<br><br>
                Form akan ditutup pada hari
                <strong><?php echo e(\Carbon\Carbon::parse($activePeriode->tanggal_selesai)->translatedFormat('l, d F Y')); ?></strong>
                pukul <strong>23.59 WIB</strong>.
            </p>

            <!-- Info Grid (Compact) -->
            <div class="grid grid-cols-2 gap-x-8 gap-y-5 border-t border-slate-200 pt-5">
                <div>
                    <h4 class="font-bold text-slate-900 text-[10px] tracking-widest uppercase mb-1">Pelaksanaan</h4>
                    <p class="text-xs font-medium text-slate-700">
                        <?php echo e($activePeriode->tanggal_mulai_ujian ? \Carbon\Carbon::parse($activePeriode->tanggal_mulai_ujian)->translatedFormat('d F Y') : '-'); ?>

                        &ndash;
                        <?php echo e($activePeriode->tanggal_selesai_ujian ? \Carbon\Carbon::parse($activePeriode->tanggal_selesai_ujian)->translatedFormat('d F Y') : '-'); ?>

                    </p>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-[10px] tracking-widest uppercase mb-1">Durasi</h4>
                    <p class="text-xs font-medium text-slate-700">100 Menit</p>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-[10px] tracking-widest uppercase mb-1">Lokasi</h4>
                    <p class="text-xs font-medium text-slate-700">Lab. Jaringan Komputer</p>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 text-[10px] tracking-widest uppercase mb-1">Syarat</h4>
                    <p class="text-xs font-medium text-slate-700">Minimal Semester 7</p>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Form -->
        <div class="w-full lg:w-[58%]">
            <div class="bg-white p-8 border border-slate-200 rounded-2xl shadow-sm lg:sticky lg:top-4">

                <div class="mb-5 pb-4 border-b border-slate-200">
                    <h2 class="text-lg font-extrabold text-slate-900 tracking-tight uppercase">Data Mahasiswa</h2>
                </div>

                <form action="<?php echo e(route('komprehensif.mahasiswa.pendaftaran.store')); ?>" method="POST" class="space-y-4">
                    <?php echo csrf_field(); ?>

                    <!-- Seksi 1: Identitas -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- NIM -->
                        <div class="space-y-1">
                            <label class="block text-[10px] text-slate-900 font-bold uppercase tracking-widest">Nomor
                                Induk</label>
                            <input type="text" name="nim" required readonly
                                value="<?php echo e(old('nim', optional(auth()->user()->student)->student_number ?? auth()->user()->external_id)); ?>"
                                class="w-full h-11 bg-slate-50 border border-slate-200 text-slate-500 font-mono text-sm px-3 outline-none cursor-not-allowed rounded-xl" />
                        </div>
                        <!-- Semester -->
                        <div class="space-y-1">
                            <label
                                class="block text-[10px] text-slate-900 font-bold uppercase tracking-widest">Semester</label>
                            <input type="number" name="semester" required readonly value="<?php echo e($semesterAktif); ?>"
                                class="w-full h-11 bg-slate-50 border border-slate-200 text-slate-500 font-mono text-sm px-3 outline-none cursor-not-allowed rounded-xl" />
                        </div>
                    </div>

                    <!-- Nama -->
                    <div class="space-y-1">
                        <label class="block text-[10px] text-slate-900 font-bold uppercase tracking-widest">Nama
                            Lengkap</label>
                        <input type="text" name="nama" required readonly value="<?php echo e(old('nama', auth()->user()->name)); ?>"
                            class="w-full h-11 bg-slate-50 border border-slate-200 text-slate-500 font-medium text-sm px-3 outline-none cursor-not-allowed rounded-xl" />
                    </div>

                    <!-- Seksi 2: Kontak & Akademik -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- WA -->
                        <div class="space-y-1">
                            <label class="block text-[10px] text-slate-900 font-bold uppercase tracking-widest">WhatsApp
                                <span class="text-red-500">*</span></label>
                            <input type="text" name="kontak_wa" required value="<?php echo e(old('kontak_wa')); ?>"
                                class="w-full h-11 bg-white border border-slate-300 focus:border-slate-900 focus:ring-0 text-slate-900 font-mono text-sm px-3 transition-colors outline-none <?php $__errorArgs = ['kontak_wa'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> rounded-xl"
                                placeholder="08xxxxxxxx" />
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['kontak_wa'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-[11px] text-red-600 font-bold"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <!-- Target Lulus -->
                        <div class="space-y-1">
                            <label class="block text-[10px] text-slate-900 font-bold uppercase tracking-widest">Target
                                Lulus <span class="text-red-500">*</span></label>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activePeriode->target_wisuda_options && count($activePeriode->target_wisuda_options) > 0): ?>
                                <select name="target_wisuda" required
                                    class="w-full h-11 bg-white border border-slate-300 focus:border-slate-900 focus:ring-0 text-slate-900 font-medium text-sm px-3 transition-colors outline-none cursor-pointer <?php $__errorArgs = ['target_wisuda'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> rounded-xl">
                                    <option value="" disabled <?php echo e(old('target_wisuda') ? '' : 'selected'); ?>>PILIH TARGET WISUDA</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $activePeriode->target_wisuda_options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <option value="<?php echo e($opt); ?>" <?php echo e(old('target_wisuda') === $opt ? 'selected' : ''); ?>><?php echo e($opt); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </select>
                            <?php else: ?>
                                <input type="text" name="target_wisuda" required value="<?php echo e(old('target_wisuda')); ?>"
                                    class="w-full h-11 bg-white border border-slate-300 focus:border-slate-900 focus:ring-0 text-slate-900 font-medium text-sm px-3 transition-colors outline-none <?php $__errorArgs = ['target_wisuda'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> rounded-xl"
                                    placeholder="Contoh: Periode 183 (Apr-Jun '26)" />
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['target_wisuda'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-[11px] text-red-600 font-bold"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 w-full"></div>

                    <!-- Seksi 3: Dosen Pembimbing -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Dosbing 1 -->
                        <div class="space-y-1">
                            <label
                                class="block text-[10px] text-slate-900 font-bold uppercase tracking-widest">Pembimbing
                                1 <span class="text-red-500">*</span></label>
                            <select name="dosen_pembimbing_1_id" required
                                class="w-full h-11 bg-white border border-slate-300 focus:border-slate-900 focus:ring-0 text-slate-900 font-medium text-sm px-3 transition-colors outline-none cursor-pointer <?php $__errorArgs = ['dosen_pembimbing_1_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> rounded-xl">
                                <option value="" disabled selected>PILIH DOSEN</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $dosens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dosen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <option value="<?php echo e($dosen->id); ?>" <?php echo e(old('dosen_pembimbing_1_id') == $dosen->id ? 'selected' : ''); ?>>
                                        <?php echo e(strtoupper($dosen->name)); ?>

                                    </option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['dosen_pembimbing_1_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-[11px] text-red-600 font-bold"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <!-- Dosbing 2 -->
                        <div class="space-y-1">
                            <label
                                class="block text-[10px] text-slate-900 font-bold uppercase tracking-widest">Pembimbing
                                2</label>
                            <select name="dosen_pembimbing_2_id"
                                class="w-full h-11 bg-white border border-slate-300 focus:border-slate-900 focus:ring-0 text-slate-900 font-medium text-sm px-3 transition-colors outline-none cursor-pointer <?php $__errorArgs = ['dosen_pembimbing_2_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> rounded-xl">
                                <option value="" selected>PILIH DOSEN</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $dosens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dosen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <option value="<?php echo e($dosen->id); ?>" <?php echo e(old('dosen_pembimbing_2_id') == $dosen->id ? 'selected' : ''); ?>>
                                        <?php echo e(strtoupper($dosen->name)); ?>

                                    </option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['dosen_pembimbing_2_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-[11px] text-red-600 font-bold"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full h-12 inline-flex items-center justify-center bg-primary hover:bg-primary/90 text-white text-xs font-bold tracking-widest uppercase transition-colors rounded-xl rounded-xl shadow-sm">
                        Submit Pendaftaran &rarr;
                    </button>
                </form>
            </div>
        </div>

    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0c501b5a3ea6a4d95bdc63fd0f1f3cbc)): ?>
<?php $attributes = $__attributesOriginal0c501b5a3ea6a4d95bdc63fd0f1f3cbc; ?>
<?php unset($__attributesOriginal0c501b5a3ea6a4d95bdc63fd0f1f3cbc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0c501b5a3ea6a4d95bdc63fd0f1f3cbc)): ?>
<?php $component = $__componentOriginal0c501b5a3ea6a4d95bdc63fd0f1f3cbc; ?>
<?php unset($__componentOriginal0c501b5a3ea6a4d95bdc63fd0f1f3cbc); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\mahasiswa\pendaftaran-form.blade.php ENDPATH**/ ?>