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
        <a href="#" class="hover:text-primary transition-colors text-gray-500">Sistem Ujian</a>
        <span class="mx-2 text-gray-300">/</span>
        <span class="text-gray-900 font-semibold">Aktivasi Sesi</span>
    <?php $__env->stopSection(); ?>

    <div class="w-full">
        <!-- Page Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Aktivasi Sesi & Token Ujian</h1>
                <p class="text-[13px] text-gray-500 mt-0.5">Aktivasi dan generate token ujian untuk memulai CBT.</p>
            </div>

            <!-- Context Switcher -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($periodes->isEmpty()): ?>
                <div class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-50 border border-amber-200 text-amber-700 text-[13px] font-medium rounded-xl flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Belum ada periode. <a href="<?php echo e(route('banksoal.periode.setup')); ?>" class="underline font-bold">Buat Periode Pertama</a>
                </div>
            <?php else: ?>
                <?php
                    $periodesForJs = $periodes->map(fn($p) => [
                        'id' => $p->id,
                        'nama' => $p->nama_periode,
                        'url' => route('banksoal.aktivasi.index', ['periode_id' => $p->id]),
                    ])->values();
                    $selectedPeriodeId = request('periode_id');
                    $selectedPeriode = $periodes->firstWhere('id', $selectedPeriodeId);
                ?>
                <div x-data="{
                        open: false,
                        search: '',
                        periodeList: <?php echo e($periodesForJs->toJson()); ?>,
                        get filtered() {
                            const q = this.search.trim().toLowerCase();
                            if (!q) return this.periodeList;
                            return this.periodeList.filter(p => p.nama.toLowerCase().includes(q));
                        },
                        toggle() {
                            this.open = !this.open;
                            if (this.open) this.$nextTick(() => this.$refs.searchInput?.focus());
                        },
                        close() { this.open = false; this.search = ''; }
                     }" class="relative flex-shrink-0" @keydown.escape.window="close()">

                    <!-- Trigger Button -->
                    <button type="button" @click="toggle()" class="group inline-flex items-center gap-2.5 pl-4 pr-3 py-2 rounded-lg text-[13px] font-semibold border transition-all duration-200 shadow-sm
                                   <?php echo e($selectedPeriode
            ? 'bg-white text-gray-900 border-gray-300 hover:border-primary/40 hover:shadow-md'
            : 'bg-primary text-white border-primary hover:bg-primary/90'); ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedPeriode): ?>
                            <span class="w-2 h-2 rounded-full bg-emerald-400 flex-shrink-0"></span>
                            <span class=""><?php echo e($selectedPeriode->nama_periode); ?></span>
                        <?php else: ?>
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>Pilih Periode Ujian</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <svg class="w-4 h-4 flex-shrink-0 transition-transform duration-200 <?php echo e($selectedPeriode ? 'text-gray-400' : 'text-white/70'); ?>"
                            :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Popover Panel -->
                    <div x-show="open" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 -translate-y-1" @click.outside="close()"
                        class="absolute top-full right-0 mt-2 z-50 bg-white border border-gray-200 rounded-xl shadow-xl w-72 overflow-hidden"
                        style="display:none">

                        <!-- Search Header -->
                        <div class="p-3 border-b border-gray-100">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"></path>
                                </svg>
                                <input x-ref="searchInput" x-model="search" type="text" placeholder="Cari nama periode..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-[13px] text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            </div>
                            <p class="text-[11px] text-gray-400 mt-2 pl-1">
                                <span x-text="filtered.length"></span> dari <?php echo e($periodes->count()); ?> periode
                            </p>
                        </div>

                        <!-- Period List -->
                        <div class="overflow-y-auto max-h-64 py-1.5">
                            <template x-for="p in filtered" :key="p.id">
                                <a :href="p.url" class="flex items-center gap-3 px-4 py-2.5 text-[13px] font-medium transition-colors hover:bg-gray-50 group/item"
                                    :class="p.id == <?php echo e($selectedPeriodeId ?? 'null'); ?> ? 'bg-primary/10 text-primary' : 'text-gray-900'">
                                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" :class="p.id == <?php echo e($selectedPeriodeId ?? 'null'); ?> ? 'bg-primary' : 'bg-gray-300'"></span>
                                    <span x-text="p.nama" class="flex-1 whitespace-normal break-words text-left"></span>
                                    <svg x-show="p.id == <?php echo e($selectedPeriodeId ?? 'null'); ?>" class="w-3.5 h-3.5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </a>
                            </template>

                            <div x-show="filtered.length === 0" class="px-4 py-8 text-center">
                                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"></path>
                                </svg>
                                <p class="text-[13px] text-gray-400 font-medium">Tidak ditemukan</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!request('periode_id')): ?>
            <div class="mb-6 bg-primary/5 border border-primary/20 rounded-xl p-4 flex gap-3">
                <svg class="w-5 h-5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-[13px] text-primary font-medium mt-0.5">
                    Data Sesi Ujian akan tampil setelah Anda memilih <strong class="font-bold">Periode Ujian</strong> di atas.
                </p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <!-- Card Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $jadwals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jadwal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $isExpired = false;
                    if ($jadwal->tanggal_ujian && $jadwal->waktu_selesai) {
                        $waktuSelesai = \Carbon\Carbon::parse($jadwal->tanggal_ujian->format('Y-m-d') . ' ' . $jadwal->waktu_selesai);
                        $isExpired = now()->gte($waktuSelesai);
                    }
                ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col relative transition-shadow hover:shadow-md">

                    <!-- Status Badge -->
                    <div class="absolute top-4 right-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isExpired || $jadwal->status->value === 'selesai'): ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold tracking-wider uppercase border border-gray-200 bg-gray-50 text-gray-500 shadow-sm">
                                Selesai
                            </span>
                        <?php elseif($jadwal->status->value === 'aktif'): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold tracking-wider uppercase border border-emerald-200 bg-emerald-50 text-emerald-600 shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> AKTIF
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold tracking-wider uppercase border border-amber-200 bg-amber-50 text-amber-600 shadow-sm">
                                Menunggu
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="p-5 flex-1">
                        <div class="text-[11px] font-bold tracking-wider text-primary uppercase mb-1">
                            <?php echo e(\Carbon\Carbon::parse($jadwal->tanggal_ujian)->translatedFormat('l, d F Y')); ?>

                        </div>
                        <h3 class="text-[16px] font-bold text-gray-900 mb-2">
                            <?php echo e(is_numeric($jadwal->nama_sesi) ? 'Sesi ' . $jadwal->nama_sesi : $jadwal->nama_sesi); ?>

                        </h3>

                        <div class="flex items-center gap-2 text-[13px] text-gray-500 mb-1 font-medium">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <?php echo e(\Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i')); ?> - <?php echo e(\Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i')); ?>

                        </div>
                        <div class="flex items-center gap-2 text-[13px] text-gray-500 mb-4 font-medium">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <?php echo e($jadwal->pendaftars_count); ?> / <?php echo e($jadwal->kuota); ?> Mahasiswa
                        </div>

                        <!-- Token Section -->
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mt-auto">
                            <div class="text-[12px] font-semibold text-gray-500 mb-1">Token Ujian Mahasiswa</div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($jadwal->token): ?>
                                <div class="flex items-center justify-between">
                                    <span class="font-mono text-xl font-black text-gray-800 tracking-widest"><?php echo e($jadwal->token); ?></span>
                                </div>
                            <?php else: ?>
                                <span class="text-[13px] font-medium text-gray-400 italic">Belum di-generate.</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div class="bg-gray-50 border-t border-gray-200 px-5 py-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isExpired || $jadwal->status->value === 'selesai'): ?>
                            <button disabled class="w-full text-center px-4 py-2 bg-gray-100 text-gray-400 cursor-not-allowed text-[13px] font-semibold rounded-lg border border-gray-200">
                                Waktu Sesi Berakhir
                            </button>
                        <?php elseif($jadwal->status->value === 'aktif'): ?>
                            <button disabled class="w-full text-center px-4 py-2 bg-emerald-50 text-emerald-600 border border-emerald-200 cursor-default text-[13px] font-bold rounded-lg flex items-center justify-center gap-2 shadow-sm">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Sesi Berjalan
                            </button>
                        <?php elseif($jadwal->status->value === 'menunggu_jadwal'): ?>
                            <form action="<?php echo e(route('banksoal.aktivasi.toggle', $jadwal->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>
                                <input type="hidden" name="status" value="aktif">
                                <button type="submit" class="w-full text-center px-4 py-2 bg-primary hover:bg-primary/90 text-white text-[13px] font-semibold rounded-lg transition-colors shadow-sm focus:ring-2 focus:ring-primary/20">
                                    Aktifkan Sesi & Token
                                </button>
                            </form>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('periode_id')): ?>
                <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-gray-200 border-dashed">
                    <div class="w-14 h-14 bg-gray-50 flex items-center justify-center rounded-2xl mx-auto mb-4 border border-gray-100 shadow-sm">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-[14px] font-semibold text-gray-900 tracking-tight">Belum Ada Sesi</h3>
                    <p class="text-[13px] text-gray-500 mt-1">Belum ada jadwal sesi ujian pada periode ini.</p>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\BankSoal\resources\views\aktivasi\index.blade.php ENDPATH**/ ?>