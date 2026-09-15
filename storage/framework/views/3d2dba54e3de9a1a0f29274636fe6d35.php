<?php if (isset($component)) { $__componentOriginal544c4f22773da773738fd88478b17597 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal544c4f22773da773738fd88478b17597 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-ruangan.layout','data' => ['pageTitle' => 'Detail Ruangan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-ruangan.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Detail Ruangan']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


    
    <div class="flex items-center gap-2 text-[12px] text-gray-500 mb-4">
        <a href="<?php echo e(route('eoffice.peminjaman.user.booking')); ?>" class="hover:text-indigo-600 font-medium transition-colors">
            Katalog Ruangan
        </a>
        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-800 font-semibold"><?php echo e($room->nama); ?></span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        
        <div class="lg:col-span-2 space-y-5">

            
            <div class="mp-card overflow-hidden">
                <div class="aspect-video w-full relative flex items-center justify-center bg-gradient-to-br from-indigo-100 via-indigo-50 to-blue-50">
                    <style>
                        .gallery-slider::-webkit-scrollbar { display: none; }
                        .gallery-slider { -ms-overflow-style: none; scrollbar-width: none; }
                    </style>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($room->fotos->count() > 0): ?>
                        <div class="gallery-slider" style="display: flex; overflow-x: auto; scroll-snap-type: x mandatory; width: 100%; height: 100%;">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $room->fotos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $foto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div style="flex: 0 0 100%; width: 100%; height: 100%; position: relative; scroll-snap-align: start;">
                                    <img src="<?php echo e(app(\App\Services\SupabaseStorage::class)->getPublicUrl($foto->path_foto)); ?>" alt="Foto <?php echo e($room->nama); ?>" class="w-full h-full object-cover">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($room->fotos->count() > 1): ?>
                                        <div class="absolute top-3 left-3 bg-black/50 text-white text-[10px] font-bold px-2 py-1 rounded backdrop-blur-sm">
                                            <?php echo e($loop->iteration); ?> / <?php echo e($room->fotos->count()); ?>

                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($room->fotos->count() > 1): ?>
                            <!-- Swipe hint -->
                            <div class="absolute inset-y-0 right-0 w-12 bg-gradient-to-l from-black/20 to-transparent pointer-events-none flex items-center justify-end pr-2 text-white/60">
                                <svg class="w-6 h-6 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <div class="text-center">
                            <svg class="w-16 h-16 text-indigo-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3v18h18M8 17V9m4 8V5m4 12v-4" />
                            </svg>
                            <p class="text-sm font-semibold text-indigo-400 uppercase tracking-wider">Foto Ruangan Segera Hadir</p>
                            <p class="text-[11px] text-indigo-300 mt-1">Foto akan ditambahkan oleh administrator</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900"><?php echo e($room->nama); ?></h1>
                            <p class="text-[13px] text-gray-500 mt-1">
                                🏢 <?php echo e($room->lokasi ?? 'Gedung Utama'); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($room->lantai): ?>
                                    &nbsp;·&nbsp; Lantai <?php echo e($room->lantai); ?>

                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                        </div>
                        <span class="flex-shrink-0 bg-indigo-50 text-indigo-700 border border-indigo-200 text-sm font-bold px-3 py-1.5 rounded-full">
                            👥 <?php echo e($room->kapasitas); ?> orang
                        </span>
                    </div>
                </div>
            </div>

            
            <div class="mp-card">
                <div class="mp-card-header">
                    <h2 class="font-bold text-gray-800 text-[15px]">Informasi Ruangan</h2>
                </div>
                <div class="mp-card-body p-5">
                    <div class="grid grid-cols-3 divide-x divide-gray-100">
                        <div class="pr-6 text-center">
                            <p class="text-3xl mb-1.5">🏢</p>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1">Gedung</p>
                            <p class="text-[14px] font-bold text-gray-800"><?php echo e($room->lokasi ?? 'Gedung Utama'); ?></p>
                        </div>
                        <div class="px-6 text-center">
                            <p class="text-3xl mb-1.5">📐</p>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1">Lantai</p>
                            <p class="text-[14px] font-bold text-gray-800"><?php echo e($room->lantai ?? '–'); ?></p>
                        </div>
                        <div class="pl-6 text-center">
                            <p class="text-3xl mb-1.5">👥</p>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-1">Kapasitas</p>
                            <p class="text-[14px] font-bold text-gray-800"><?php echo e($room->kapasitas); ?> orang</p>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="mp-card">
                <div class="mp-card-header">
                    <h2 class="font-bold text-gray-800 text-[15px]">Fasilitas Tersedia</h2>
                </div>
                <div class="mp-card-body p-5">
                    <?php
                        $facilityIcons = [
                            'Proyektor' => ['icon' => '📽️', 'color' => 'bg-purple-50 text-purple-700 border-purple-100'],
                            'AC' => ['icon' => '❄️', 'color' => 'bg-blue-50 text-blue-700 border-blue-100'],
                            'Whiteboard' => ['icon' => '🖊️', 'color' => 'bg-gray-50 text-gray-700 border-gray-200'],
                            'WiFi' => ['icon' => '📶', 'color' => 'bg-indigo-50 text-indigo-700 border-indigo-100'],
                            'Sound System' => ['icon' => '🔊', 'color' => 'bg-amber-50 text-amber-700 border-amber-100'],
                            'CCTV' => ['icon' => '📷', 'color' => 'bg-red-50 text-red-700 border-red-100'],
                            'Meja' => ['icon' => '🪑', 'color' => 'bg-orange-50 text-orange-700 border-orange-100'],
                            'Komputer' => ['icon' => '💻', 'color' => 'bg-emerald-50 text-emerald-700 border-emerald-100'],
                        ];
                    ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($fasilitas) > 0): ?>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $fasilitas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php $fInfo = $facilityIcons[$fas] ?? ['icon' => '✅', 'color' => 'bg-gray-50 text-gray-700 border-gray-200']; ?>
                                <div class="flex items-center gap-2.5 p-3 rounded-xl border <?php echo e($fInfo['color']); ?>">
                                    <span class="text-xl"><?php echo e($fInfo['icon']); ?></span>
                                    <span class="text-[13px] font-semibold"><?php echo e($fas); ?></span>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-400 italic text-sm">Belum ada fasilitas yang tercatat untuk ruangan ini.</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

        </div>

        
        <div class="space-y-5">

            
            <div class="mp-card bg-gradient-to-br from-indigo-600 to-indigo-700 text-white overflow-hidden">
                <div class="p-5">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-[15px] font-bold mb-1">Ingin memakai ruangan ini?</h3>
                    <p class="text-[12px] text-indigo-200 mb-4 leading-relaxed">Cek kalender jadwal mingguan untuk slot kosong yang tersedia lalu ajukan booking langsung!</p>
                    <a href="<?php echo e($kalenderUrl); ?>"
                        class="block w-full py-2.5 text-center text-[13px] font-bold bg-white text-indigo-700 rounded-lg hover:bg-indigo-50 transition-colors shadow-sm">
                        📅 Lihat Kalender & Booking
                    </a>
                </div>
            </div>

            
            <div class="mp-card">
                <div class="mp-card-header flex items-center justify-between">
                    <h2 class="font-bold text-gray-800 text-[14px]">Jadwal 7 Hari ke Depan</h2>
                    <span class="text-[11px] font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">
                        <?php echo e($upcomingBookings->count()); ?> booking
                    </span>
                </div>
                <div class="mp-card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($upcomingBookings->count() > 0): ?>
                        <div class="divide-y divide-gray-100">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $upcomingBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="py-3 px-4 flex items-start gap-3">
                                    <div class="mt-0.5">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bk->status == 'disetujui'): ?>
                                            <span class="w-2 h-2 rounded-full bg-red-500 block mt-1.5"></span>
                                        <?php else: ?>
                                            <span class="w-2 h-2 rounded-full bg-amber-400 block mt-1.5"></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                    <div>
                                        <p class="text-[12px] font-semibold text-gray-800">
                                            <?php echo e(\Carbon\Carbon::parse($bk->tanggal_pinjam)->translatedFormat('D, d M')); ?>

                                        </p>
                                        <p class="text-[11px] text-gray-500 mt-0.5">
                                            <?php echo e(\Carbon\Carbon::parse($bk->jam_mulai)->format('H:i')); ?> –
                                            <?php echo e(\Carbon\Carbon::parse($bk->jam_selesai)->format('H:i')); ?> WIB
                                        </p>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bk->status == 'disetujui'): ?>
                                            <span class="inline-block mt-1 text-[9px] font-bold uppercase tracking-wide bg-red-100 text-red-600 px-1.5 py-0.5 rounded">Terisi</span>
                                        <?php else: ?>
                                            <span class="inline-block mt-1 text-[9px] font-bold uppercase tracking-wide bg-amber-100 text-amber-600 px-1.5 py-0.5 rounded">Pending</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="py-8 text-center px-4">
                            <p class="text-2xl mb-2">🟢</p>
                            <p class="text-[13px] font-bold text-gray-700 mb-0.5">Kosong</p>
                            <p class="text-[11px] text-gray-400">Tidak ada booking dalam 7 hari ke depan!</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <a href="<?php echo e(route('eoffice.peminjaman.user.booking')); ?>"
                class="flex items-center justify-center gap-2 w-full py-2.5 text-[13px] font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
                ← Kembali ke Katalog
            </a>
        </div>
    </div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal544c4f22773da773738fd88478b17597)): ?>
<?php $attributes = $__attributesOriginal544c4f22773da773738fd88478b17597; ?>
<?php unset($__attributesOriginal544c4f22773da773738fd88478b17597); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal544c4f22773da773738fd88478b17597)): ?>
<?php $component = $__componentOriginal544c4f22773da773738fd88478b17597; ?>
<?php unset($__componentOriginal544c4f22773da773738fd88478b17597); ?>
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-ruangan\user\booking\detail.blade.php ENDPATH**/ ?>