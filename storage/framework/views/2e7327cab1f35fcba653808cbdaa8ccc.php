<?php if (isset($component)) { $__componentOriginal3e4fe3b26585e820af7a1b481c38fe80 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e4fe3b26585e820af7a1b481c38fe80 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'banksoal::components.layouts.admin','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banksoal::layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php $__env->startSection('breadcrumbs'); ?>
    <a href="#" class="text-slate-500 hover:text-primary transition-colors">Ujian Komprehensif</a>
    <span class="mx-2 text-slate-300">/</span>
    <span class="text-slate-800 font-semibold">Jadwal Sesi</span>
    <?php $__env->stopSection(); ?>

    <div x-data="{ openModal: false }" class="w-full">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Pengaturan Jadwal Sesi</h1>
                <p class="text-[13px] text-gray-500 mt-0.5">Kelola sesi dan kuota ujian berdasarkan periode.</p>
            </div>
            
            <button @click="openModal = true" <?php if(!$selectedPeriode): ?> disabled <?php endif; ?>
                class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-primary/90 transition-colors rounded-lg px-4 py-2.5 text-white font-medium text-[13px] shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Sesi
            </button>
        </div>



        <!-- Date Selector Section (Reaktif berdasarkan Periode yang dipilih) -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedPeriode): ?>
        <div class="flex flex-col gap-2 mb-8">
            <span class="text-[13px] text-slate-500 font-medium">Rentang Ujian Periode Ini:</span>
            <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-thin scrollbar-thumb-slate-200">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedPeriode->tanggal_mulai_ujian && $selectedPeriode->tanggal_selesai_ujian): ?>
                    <button class="px-5 py-2.5 bg-primary/10 text-primary text-[13px] font-semibold border border-primary/20 rounded-xl whitespace-nowrap transition-colors shadow-sm cursor-default">
                        <?php echo e(\Carbon\Carbon::parse($selectedPeriode->tanggal_mulai_ujian)->translatedFormat('d F Y')); ?> - <?php echo e(\Carbon\Carbon::parse($selectedPeriode->tanggal_selesai_ujian)->translatedFormat('d F Y')); ?>

                    </button>
                <?php else: ?>
                    <span class="text-sm text-slate-400 italic">Tanggal Ujian belum diatur di Setup Periode.</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <!-- Empty State Info (Jika periode belum dipilih) -->
        <div class="mb-8 p-4 bg-yellow-50 border border-yellow-100 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-sm text-yellow-800">
                Silakan pilih <strong>Periode Ujian</strong> di atas untuk memunculkan pengaturan Jadwal Sesi yang sesuai dengan masa ujian.
            </p>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="mb-8">
            <?php echo $__env->make('banksoal::jadwal._table', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        <!-- Modal Popup: Tambah/Edit Sesi Baru -->
        <div x-show="openModal" tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" style="display: none;">
            
            <!-- Dimmed Backdrop -->
            <div x-show="openModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" 
                 @click="openModal = false">
            </div>

            <!-- Modal Content Wrapper -->
            <div x-show="openModal" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="relative w-full max-w-xl bg-white rounded-2xl shadow-xl flex flex-col overflow-hidden max-h-full">
                
                <!-- Modal Header -->
                <div class="px-6 py-5 flex items-center justify-between border-b border-transparent">
                    <h3 class="text-lg font-bold text-slate-800 tracking-tight">Tambah Sesi Baru</h3>
                    <button @click="openModal = false" class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 p-2 rounded-xl transition-colors">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="px-6 pb-6 overflow-y-auto">
                    
                    <!-- Alert -->
                    <div class="mb-5 bg-primary/10 border border-primary/20 rounded-xl p-3.5 flex gap-3 mt-2">
                        <svg class="w-5 h-5 text-primary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-[13px] leading-relaxed text-primary/80 font-medium">
                            Pastikan seluruh data yang Anda masukkan sudah benar sebelum melakukan simpan.
                        </div>
                    </div>

                    <!-- Setup Form Grid -->
                    <form action="<?php echo e(route('banksoal.periode.jadwal.store')); ?>" method="POST" id="formTambahSesi" class="space-y-5">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="periode_ujian_id" value="<?php echo e($selectedPeriodeId); ?>">
                        
                        <!-- Box 1: Nama Sesi -->
                        <div>
                            <label class="block text-sm text-slate-700 mb-2 font-medium">Nama Sesi</label>
                            <input type="text" name="nama_sesi" placeholder="Sesi 1" required class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-800 placeholder-slate-400 transition-shadow">
                        </div>

                        <!-- Dropdown Tanggal Ujian -->
                        <div>
                            <label class="block text-sm text-slate-700 mb-2 font-medium">Tanggal Ujian (Berdasarkan Rentang Periode)</label>
                            <div class="relative">
                                <select name="tanggal_ujian" required class="w-full appearance-none px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-800 transition-shadow font-medium cursor-pointer">
                                    <option value="">Pilih Tanggal Ujian...</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedPeriode && $selectedPeriode->tanggal_mulai_ujian && $selectedPeriode->tanggal_selesai_ujian): ?>
                                        <?php
                                            $startDate = \Carbon\Carbon::parse($selectedPeriode->tanggal_mulai_ujian);
                                            $endDate = \Carbon\Carbon::parse($selectedPeriode->tanggal_selesai_ujian);
                                            for($d = $startDate; $d->lte($endDate); $d->addDay()) {
                                                echo '<option value="' . $d->format('Y-m-d') . '">' . $d->format('d F Y') . '</option>';
                                            }
                                        ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        <!-- Box 2 & 3: Waktu Mulai & Selesai -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm text-slate-700 mb-2 font-medium">Waktu Mulai</label>
                                <input type="time" name="waktu_mulai" required class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-800 transition-shadow">
                            </div>
                            <div>
                                <label class="block text-sm text-slate-700 mb-2 font-medium">Waktu Selesai</label>
                                <input type="time" name="waktu_selesai" required class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-800 transition-shadow">
                            </div>
                        </div>

                        <!-- Ruangan -->
                        <div>
                            <label class="block text-sm text-slate-700 mb-2 font-medium">Ruangan</label>
                            <input type="text" name="ruangan" placeholder="Lab Jaringan" required class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-800 placeholder-slate-400 transition-shadow">
                        </div>

                        <!-- Box 4: Kapasitas -->
                        <div>
                            <label class="block text-sm text-slate-700 mb-2 font-medium">Kapasitas Maksimal</label>
                            <input type="number" name="kuota" placeholder="50" min="1" step="1" required class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-slate-800 placeholder-slate-400 transition-shadow">
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 flex flex-col sm:flex-row items-center justify-center gap-3 rounded-b-2xl bg-white border-t border-slate-100">
                    <button @click="openModal = false" type="button" class="w-full px-5 py-2.5 text-sm font-bold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 focus:outline-none transition-colors">
                        Batal
                    </button>
                    <button type="button" onclick="document.getElementById('formTambahSesi').submit()" class="w-full px-5 py-2.5 text-sm font-bold text-white bg-slate-700 hover:bg-slate-800 shadow-sm rounded-xl focus:outline-none transition-colors">
                        Simpan Sesi
                    </button>
                </div>
            </div>

        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3e4fe3b26585e820af7a1b481c38fe80)): ?>
<?php $attributes = $__attributesOriginal3e4fe3b26585e820af7a1b481c38fe80; ?>
<?php unset($__attributesOriginal3e4fe3b26585e820af7a1b481c38fe80); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e4fe3b26585e820af7a1b481c38fe80)): ?>
<?php $component = $__componentOriginal3e4fe3b26585e820af7a1b481c38fe80; ?>
<?php unset($__componentOriginal3e4fe3b26585e820af7a1b481c38fe80); ?>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const mulaiEl   = document.getElementById('waktu_mulai_input');
    const selesaiEl = document.getElementById('waktu_selesai_input');
    if (!mulaiEl || !selesaiEl) return;

    function updateMinSelesai() {
        if (!mulaiEl.value) return;
        const [h, m] = mulaiEl.value.split(':').map(Number);
        const total  = h * 60 + m + 100;
        const minH   = String(Math.floor(total / 60) % 24).padStart(2, '0');
        const minM   = String(total % 60).padStart(2, '0');
        selesaiEl.min = minH + ':' + minM;
        // Reset nilai jika waktu selesai yang dipilih tidak lagi valid
        if (selesaiEl.value && selesaiEl.value < selesaiEl.min) {
            selesaiEl.value = selesaiEl.min;
        }
    }

    mulaiEl.addEventListener('change', updateMinSelesai);
    mulaiEl.addEventListener('input',  updateMinSelesai);
});
</script>

<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\jadwal\index.blade.php ENDPATH**/ ?>