<?php if (isset($component)) { $__componentOriginal544c4f22773da773738fd88478b17597 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal544c4f22773da773738fd88478b17597 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-ruangan.layout','data' => ['pageTitle' => ''.e($viewMode === 'akademik' ? 'Kelola Jadwal Akademik' : 'Kelola Event & Maintenance').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-ruangan.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => ''.e($viewMode === 'akademik' ? 'Kelola Jadwal Akademik' : 'Kelola Event & Maintenance').'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


    <div
        x-data="{ showModal: false, showImportModal: false, formType: '<?php echo e($viewMode === 'akademik' ? 'rutin' : 'spesifik'); ?>', kategoriType: '<?php echo e($viewMode === 'akademik' ? 'Jadwal Akademik (Kuliah)' : 'Maintenance / Perbaikan'); ?>' }">
        <div class="mp-page-header">
            <div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($viewMode === 'akademik'): ?>
                    <h1 class="mp-page-title">Kelola Jadwal Akademik</h1>
                    <p class="mp-page-sub">Atur dan import blocking waktu khusus untuk agenda perkuliahan rutin Fakultas.
                    </p>
                <?php else: ?>
                    <h1 class="mp-page-title">Kelola Event & Maintenance</h1>
                    <p class="mp-page-sub">Atur blocking waktu insidental untuk rapat dosen, acara himpunan, atau perawatan
                        ruangan.</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="mp-page-actions flex items-center gap-3">
                <form action="<?php echo e(route('eoffice.peminjaman.admin.jadwal-internal.index')); ?>" method="GET" class="flex">
                    <select name="kategori"
                        class="mp-input text-[13px] py-2 h-[42px] px-3 w-[220px] bg-white border border-gray-200 rounded-lg shadow-sm"
                        onchange="this.form.submit()">
                        <option value="">Semua Kategori Acara</option>
                        <option value="Event / Kegiatan" <?php echo e(request('kategori') == 'Event / Kegiatan' ? 'selected' : ''); ?>>Event / Kegiatan Mahasiswa</option>
                        <option value="Rapat Internal" <?php echo e(request('kategori') == 'Rapat Internal' ? 'selected' : ''); ?>>
                            Rapat Internal Dosen</option>
                        <option value="Ujian / Evaluasi" <?php echo e(request('kategori') == 'Ujian / Evaluasi' ? 'selected' : ''); ?>>Ujian / Evaluasi (UTS/UAS)</option>
                        <option value="Maintenance / Perbaikan" <?php echo e(request('kategori') == 'Maintenance / Perbaikan' ? 'selected' : ''); ?>>Maintenance / Perbaikan</option>
                        <option value="Lainnya" <?php echo e(request('kategori') == 'Lainnya' ? 'selected' : ''); ?>>Lainnya...
                        </option>
                    </select>
                </form>

                <button @click="showModal = true" class="mp-btn primary md">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Tambah
                </button>
            </div>
        </div> <!-- Close mp-page-header -->

        <!-- Add Modal Alpine Component -->
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

                <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-sm" aria-hidden="true"
                    @click="showModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative">

                    <div>
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-[18px] font-bold text-gray-900 font-['Inter_Tight']" id="modal-title">Tambah
                                Jadwal Baru</h3>
                            <button @click="showModal = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <form action="<?php echo e(route('eoffice.peminjaman.admin.jadwal-internal.store')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Pilih
                                            Ruangan</label>
                                        <select name="ruangan_id" required class="mp-input text-[14px]">
                                            <option value="" disabled selected>-- Pilih Ruangan --</option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $ruangans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                <option value="<?php echo e($r->id); ?>"><?php echo e($r->nama); ?> - Lt. <?php echo e($r->lantai); ?></option>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Kategori</label>
                                        <select name="kategori" x-model="kategoriType" required
                                            class="mp-input text-[14px]">
                                            <option value="Event / Kegiatan">Event / Kegiatan Mahasiswa</option>
                                            <option value="Rapat Internal">Rapat Internal Dosen</option>
                                            <option value="Ujian / Evaluasi">Ujian / Evaluasi (UTS/UAS)</option>
                                            <option value="Maintenance / Perbaikan">Maintenance / Perbaikan</option>
                                            <option value="Lainnya">Lainnya...</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-2 text-left">
                                    <label
                                        class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Nama
                                        Acara</label>
                                    <input type="text" name="keterangan" required class="mp-input text-[14px] w-full"
                                        placeholder="Misal: Rapat Evaluasi Kurikulum...">
                                </div>

                                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-4">
                                    <h4
                                        class="text-[13px] font-bold text-slate-800 border-b border-slate-200 pb-2 mb-3">
                                        Pengaturan Waktu Pelaksanaan</h4>

                                    <input type="hidden" name="tipe_jadwal" x-model="formType">

                                    <div class="grid grid-cols-2 gap-4">
                                        <!-- Tampil Jika Tipe Spesifik -->
                                        <template x-if="formType === 'spesifik'">
                                            <div>
                                                <label
                                                    class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Tanggal
                                                    Spesifik</label>
                                                <input type="date" name="tanggal_spesifik" required
                                                    class="mp-input text-[14px]">
                                            </div>
                                        </template>

                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label
                                                    class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Mulai</label>
                                                <input type="time" name="jam_mulai" required
                                                    class="mp-input text-[14px]">
                                            </div>
                                            <div>
                                                <label
                                                    class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Akhir</label>
                                                <input type="time" name="jam_selesai" required
                                                    class="mp-input text-[14px]">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="showModal = false" class="mp-btn secondary md">Batal</button>
                        <button type="submit" class="mp-btn primary md">Simpan Konfigurasi</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <!-- Error Block -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div
            class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl relative text-[13px] font-medium shadow-sm">
            <strong class="font-bold mr-1">Terdapat Kesalahan Input!</strong>
            <ul class="mt-1 list-disc list-inside">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <li><?php echo e($error); ?></li>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </ul>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="mp-card" style="margin-top: 15px;">
        <div class="mp-card-body">
            <div class="mp-table-wrap">
                <table class="mp-table" style="table-layout: auto; width: 100%;">
                    <thead>
                        <tr>
                            <th>HARI / TANGGAL</th>
                            <th>WAKTU</th>
                            <th>NAMA ACARA</th>
                            <th>RUANGAN</th>
                            <th style="width: 80px; text-align: center;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $jadwals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr class="mp-tr">
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($j->tipe_jadwal === 'rutin'): ?>
                                        <?php
                                            $namaHari = ['-', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                                        ?>
                                        <div style="font-weight: 500; color:#0D0D12;">
                                            <?php echo e($namaHari[$j->hari] ?? 'Tidak Valid'); ?>

                                        </div>
                                    <?php else: ?>
                                        <div style="font-weight: 500; color:#0D0D12;">
                                            <?php echo e(\Carbon\Carbon::parse($j->tanggal_spesifik)->translatedFormat('d F Y')); ?>

                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <div style="font-weight: 500; color: #0D0D12;">
                                        <?php echo e(substr($j->jam_mulai, 0, 5)); ?> - <?php echo e(substr($j->jam_selesai, 0, 5)); ?>

                                    </div>
                                </td>
                                <td style="max-width: 300px;">
                                    <div
                                        style="font-size: 13px; color:#4B5563; white-space: normal; overflow-wrap: anywhere; word-break: break-word;">
                                        <?php echo e($j->keterangan ?: '-'); ?>

                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 500; color: #0D0D12;">
                                        <?php echo e($j->ruangan->nama ?? 'Tidak Diketahui'); ?>

                                        <span
                                            style="font-size: 13px; color: #666D80; margin-left: 4px; font-weight: 500;">(Lt.
                                            <?php echo e($j->ruangan->lantai ?? '-'); ?>)</span>
                                    </div>
                                </td>
                                <td style="text-align: center;"
                                    x-data="{ showDropdown: false, showEditModal: false, formType: '<?php echo e($j->tipe_jadwal); ?>', kategoriType: '<?php echo e($j->kategori); ?>' }">
                                    <div class="relative inline-flex justify-center w-full relative z-[1]">
                                        <button type="button" @click="showDropdown = !showDropdown"
                                            @click.away="showDropdown = false"
                                            class="text-gray-500 hover:text-gray-800 hover:bg-gray-100 p-1.5 rounded-md transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                            </svg>
                                        </button>

                                        <div x-show="showDropdown" style="display:none;"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="origin-top-right absolute right-5 top-0 mt-8 bg-white rounded-xl shadow-[0_4px_16px_rgba(0,0,0,0.08)] border border-gray-100 p-1.5 z-20 w-[140px]">

                                            <button type="button" @click="showEditModal = true; showDropdown = false"
                                                class="w-full text-left px-2.5 py-1.5 text-[12px] text-gray-700 hover:bg-gray-100 font-semibold rounded-md focus:outline-none flex items-center gap-2 transition-colors">
                                                <svg class="w-[14px] h-[14px] text-gray-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                                Edit Jadwal
                                            </button>

                                            <form
                                                action="<?php echo e(route('eoffice.peminjaman.admin.jadwal-internal.destroy', $j->id)); ?>"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus blokir jadwal ini?');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit"
                                                    class="w-full text-left px-2.5 py-1.5 mt-0.5 text-[12px] text-red-600 hover:bg-red-50 font-semibold rounded-md focus:outline-none flex items-center gap-2 transition-colors">
                                                    <svg class="w-[14px] h-[14px] text-red-600" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                    Hapus Jadwal
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- EDIT MODAL -->
                                    <div x-show="showEditModal" style="display: none;"
                                        class="fixed inset-0 z-[100] overflow-y-auto text-left"
                                        aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                        <div
                                            class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

                                            <div x-show="showEditModal"
                                                x-transition:enter="transition ease-out duration-300"
                                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                                x-transition:leave="transition ease-in duration-200"
                                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                                class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-md"
                                                aria-hidden="true" @click="showEditModal = false"></div>
                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                                                aria-hidden="true">&#8203;</span>

                                            <div x-show="showEditModal"
                                                x-transition:enter="transition ease-out duration-300"
                                                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                x-transition:leave="transition ease-in duration-200"
                                                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative">

                                                <div class="flex items-center justify-between mb-5">
                                                    <h3 class="text-[18px] font-bold text-gray-900" id="modal-title">
                                                        Edit
                                                        Jadwal Internal</h3>
                                                    <button type="button" @click="showEditModal = false"
                                                        class="text-gray-400 hover:text-gray-500">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>

                                                <form
                                                    action="<?php echo e(route('eoffice.peminjaman.admin.jadwal-internal.update', $j->id)); ?>"
                                                    method="POST">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('PUT'); ?>
                                                    <div class="space-y-4">
                                                        <div class="grid grid-cols-2 gap-4">
                                                            <div>
                                                                <label
                                                                    class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Tipe
                                                                    Ruangan</label>
                                                                <select name="ruangan_id" required
                                                                    class="mp-input text-[14px]">
                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $ruangans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                                        <option value="<?php echo e($r->id); ?>" <?php echo e($r->id == $j->ruangan_id ? 'selected' : ''); ?>>
                                                                            <?php echo e($r->nama); ?> - Lt.
                                                                            <?php echo e($r->lantai); ?>

                                                                        </option>
                                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label
                                                                    class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Kategori</label>
                                                                <select name="kategori" x-model="kategoriType" required
                                                                    class="mp-input text-[14px]">
                                                                    <option value="Event / Kegiatan" <?php echo e($j->kategori == 'Event / Kegiatan' ? 'selected' : ''); ?>>Event / Kegiatan
                                                                        Mahasiswa</option>
                                                                    <option value="Rapat Internal" <?php echo e($j->kategori == 'Rapat Internal' ? 'selected' : ''); ?>>Rapat Internal Dosen
                                                                    </option>
                                                                    <option value="Ujian / Evaluasi" <?php echo e($j->kategori == 'Ujian / Evaluasi' ? 'selected' : ''); ?>>Ujian / Evaluasi
                                                                        (UTS/UAS)</option>
                                                                    <option value="Maintenance / Perbaikan" <?php echo e($j->kategori == 'Maintenance / Perbaikan' ? 'selected' : ''); ?>>Maintenance / Perbaikan
                                                                    </option>
                                                                    <option value="Lainnya" <?php echo e($j->kategori == 'Lainnya' ? 'selected' : ''); ?>>Lainnya...</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="mt-2 text-left">
                                                            <label
                                                                class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Nama
                                                                Acara</label>
                                                            <input type="text" name="keterangan"
                                                                value="<?php echo e($j->keterangan); ?>" required
                                                                class="mp-input text-[14px] w-full"
                                                                placeholder="Misal: Rapat Evaluasi Kurikulum...">
                                                        </div>

                                                        <div
                                                            class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-4">
                                                            <h4
                                                                class="text-[13px] font-bold text-slate-800 border-b border-slate-200 pb-2 mb-3">
                                                                Pengaturan Waktu Pelaksanaan</h4>

                                                            <input type="hidden" name="tipe_jadwal"
                                                                value="<?php echo e($j->tipe_jadwal); ?>">

                                                            <div class="grid grid-cols-2 gap-4">
                                                                <div>
                                                                    <label
                                                                        class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Tanggal
                                                                        Spesifik</label>
                                                                    <input type="date" name="tanggal_spesifik"
                                                                        value="<?php echo e($j->tanggal_spesifik); ?>" required
                                                                        class="mp-input text-[14px]">
                                                                </div>
                                                                </template>

                                                                <div class="grid grid-cols-2 gap-2">
                                                                    <div>
                                                                        <label
                                                                            class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Mulai</label>
                                                                        <input type="time" name="jam_mulai"
                                                                            value="<?php echo e(substr($j->jam_mulai, 0, 5)); ?>"
                                                                            required class="mp-input text-[14px]">
                                                                    </div>
                                                                    <div>
                                                                        <label
                                                                            class="block mb-1 text-xs font-semibold text-gray-700 uppercase tracking-widest">Akhir</label>
                                                                        <input type="time" name="jam_selesai"
                                                                            value="<?php echo e(substr($j->jam_selesai, 0, 5)); ?>"
                                                                            required class="mp-input text-[14px]">
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-100">
                                                        <button type="button" @click="showEditModal = false"
                                                            class="mp-btn secondary md">Batal</button>
                                                        <button type="submit" class="mp-btn primary md">Simpan
                                                            Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="5" style="text-align:center; padding: 40px; color: #666D80;">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#E2E8F0"
                                        stroke-width="1.5" stroke-linecap="round" style="margin: 0 auto 10px auto;">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    <div style="font-weight: 600; font-size:14px;">Belum Ada Jadwal Internal & Akademik
                                    </div>
                                    <div style="font-size:12px; margin-top:4px;">Gunakan tombol Tambah Jadwal di pojok
                                        kanan
                                        atas untuk mulai mengunci ruangan.</div>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div style="padding: 16px;">
                <?php echo e($jadwals->appends(['tipe' => $tipe])->links('pagination::tailwind')); ?>

            </div>
        </div>
    </div>

    </div> <!-- Close Alpine Wrapper -->
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal544c4f22773da773738fd88478b17597)): ?>
<?php $attributes = $__attributesOriginal544c4f22773da773738fd88478b17597; ?>
<?php unset($__attributesOriginal544c4f22773da773738fd88478b17597); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal544c4f22773da773738fd88478b17597)): ?>
<?php $component = $__componentOriginal544c4f22773da773738fd88478b17597; ?>
<?php unset($__componentOriginal544c4f22773da773738fd88478b17597); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-ruangan\admin\jadwal\maintenance.blade.php ENDPATH**/ ?>