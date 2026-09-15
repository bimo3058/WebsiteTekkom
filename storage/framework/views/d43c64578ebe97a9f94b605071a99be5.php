<?php if (isset($component)) { $__componentOriginal544c4f22773da773738fd88478b17597 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal544c4f22773da773738fd88478b17597 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-ruangan.layout','data' => ['pageTitle' => 'Katalog Ruangan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-ruangan.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Katalog Ruangan']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


    
    <div class="mp-page-header">
        <div class="flex flex-col md:flex-row md:items-center justify-between w-full gap-4">
            <div>
                <h1 class="mp-page-title">Katalog Ruangan</h1>
                <p class="mp-page-sub">Temukan ruangan yang sesuai kebutuhanmu. Klik "Detail" untuk info lengkap
                    atau "Jadwal" untuk langsung booking.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                    </svg>
                    <input type="text" id="searchInput" onkeyup="filterRooms()"
                        placeholder="Cari nama ruangan..."
                        class="pl-9 pr-4 py-2 text-[13px] w-56 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                <a href="<?php echo e(route('eoffice.peminjaman.user.kalender')); ?>"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-indigo-50 text-indigo-700 text-[13px] font-semibold border border-indigo-200 hover:bg-indigo-100 transition-colors">
                    Lihat Kalender
                </a>
            </div>
        </div>
    </div>



    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ruangans->count() > 0): ?>
        <div id="roomGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $ruangans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $fasilitas = is_array($room->fasilitas)
                        ? $room->fasilitas
                        : (json_decode($room->fasilitas, true) ?? []);
                    $bookedToday = $room->peminjamans
                        ->whereIn('status', ['menunggu', 'disetujui'])
                        ->count();
                    $facilityIcons = [
                        'Proyektor' => '📽️',
                        'AC' => '❄️',
                        'Whiteboard' => '🖊️',
                        'WiFi' => '📶',
                        'Sound System' => '🔊',
                        'CCTV' => '📷',
                        'Meja' => '🪑',
                        'Komputer' => '💻',
                    ];
                    $detailUrl = route('eoffice.peminjaman.user.booking.ruangan.show', $room->id);
                    $kalenderUrl = route('eoffice.peminjaman.user.kalender', ['ruangan_id' => $room->id]);
                ?>

                <div class="room-card bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all overflow-hidden flex flex-col"
                    data-name="<?php echo e(strtolower($room->nama)); ?>">

                    
                    <a href="<?php echo e($detailUrl); ?>"
                        class="block w-full aspect-video relative border-b border-gray-100 overflow-hidden group bg-gradient-to-br from-indigo-50 to-indigo-100">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($room->fotos->count() > 0): ?>
                            <img src="<?php echo e(app(\App\Services\SupabaseStorage::class)->getPublicUrl($room->fotos->first()->path_foto)); ?>" alt="Foto <?php echo e($room->nama); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <?php else: ?>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-indigo-300 mb-1 group-hover:text-indigo-400 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M3 3v18h18M8 17V9m4 8V5m4 12v-4" />
                                </svg>
                                <span class="text-[11px] font-semibold text-indigo-400 tracking-wide uppercase">Foto segera hadir</span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <span class="absolute top-3 right-3 bg-white text-[11px] font-bold text-indigo-700 border border-indigo-200 rounded-full px-2.5 py-0.5 shadow-sm">
                            👥 <?php echo e($room->kapasitas); ?> orang
                        </span>
                    </a>

                    
                    <div class="p-4 flex-1 flex flex-col">
                        <div class="mb-3">
                            <a href="<?php echo e($detailUrl); ?>" class="hover:text-indigo-700 transition-colors">
                                <h3 class="font-bold text-gray-900 text-[15px] leading-tight"><?php echo e($room->nama); ?></h3>
                            </a>
                            <p class="text-[12px] text-gray-500 mt-0.5">
                                🏢 <?php echo e($room->lokasi ?? 'Gedung Utama'); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($room->lantai): ?>
                                    · Lantai <?php echo e($room->lantai); ?>

                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                        </div>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($fasilitas) > 0): ?>
                            <div class="flex flex-wrap gap-1.5 mb-4">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = array_slice($fasilitas, 0, 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <span
                                        class="inline-flex items-center gap-1 text-[10px] font-semibold px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        <?php echo e($facilityIcons[$fas] ?? '✅'); ?> <?php echo e($fas); ?>

                                    </span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($fasilitas) > 4): ?>
                                    <span
                                        class="inline-flex items-center text-[10px] font-semibold px-2 py-0.5 rounded-md bg-gray-100 text-gray-500">
                                        +<?php echo e(count($fasilitas) - 4); ?> lainnya
                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="mb-4">
                                <span class="text-[12px] text-gray-400 italic">Tidak ada fasilitas tercatat.</span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        
                        <div class="mt-auto pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                            <div class="flex items-center gap-2">
                                
                                <a href="<?php echo e($detailUrl); ?>"
                                    class="inline-flex items-center px-4 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-[12px] font-semibold transition-colors border border-gray-200">
                                    Detail
                                </a>
                                
                                <a href="<?php echo e($kalenderUrl); ?>"
                                    class="inline-flex items-center px-4 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-[12px] font-bold transition-colors shadow-sm whitespace-nowrap">
                                    Jadwal
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        <div id="emptySearch" class="hidden text-center py-16">
            <p class="text-gray-500 font-medium">Tidak ada ruangan yang cocok dengan pencarianmu.</p>
        </div>
    <?php else: ?>
        <div class="text-center py-20 px-6 border border-gray-200 rounded-2xl bg-white mt-4">
            <div class="w-16 h-16 rounded-full bg-indigo-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3 3v18h18M8 17V9m4 8V5m4 12v-4" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Belum Ada Ruangan Aktif</h3>
            <p class="text-[13px] text-gray-500 max-w-xs mx-auto">Admin belum menambahkan atau mengaktifkan ruangan
                apapun. Silakan hubungi administrator.</p>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <script>
        function filterRooms() {
            const query = document.getElementById('searchInput').value.toLowerCase().trim();
            const cards = document.querySelectorAll('.room-card');
            let visibleCount = 0;
            cards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                if (name.includes(query)) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            const grid = document.getElementById('roomGrid');
            const empty = document.getElementById('emptySearch');
            if (grid) grid.classList.toggle('hidden', visibleCount === 0);
            if (empty) empty.classList.toggle('hidden', visibleCount > 0);
        }
    </script>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal544c4f22773da773738fd88478b17597)): ?>
<?php $attributes = $__attributesOriginal544c4f22773da773738fd88478b17597; ?>
<?php unset($__attributesOriginal544c4f22773da773738fd88478b17597); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal544c4f22773da773738fd88478b17597)): ?>
<?php $component = $__componentOriginal544c4f22773da773738fd88478b17597; ?>
<?php unset($__componentOriginal544c4f22773da773738fd88478b17597); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-ruangan\user\booking\index.blade.php ENDPATH**/ ?>