<?php if (isset($component)) { $__componentOriginalc437526b4c56b06f9c16ab6722a84925 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc437526b4c56b06f9c16ab6722a84925 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-praktikum.layout','data' => ['pageTitle' => 'Dashboard Dosen — Manajemen Praktikum']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-praktikum.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Dashboard Dosen — Manajemen Praktikum']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php
        $name = auth()->user()->name;
        $nameParts = explode(' ', $name);
        $firstName = $nameParts[0];
    ?>

    
    <div class="mp-page-header">
        <div>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                <h1 class="mp-page-title">Dashboard Dosen</h1>
                <span class="mp-badge primary sm"><span class="dot"></span>Dosen</span>
            </div>
            <p class="mp-page-sub">Selamat datang, <?php echo e($firstName); ?> ·
                <?php echo e(now()->locale('id')->isoFormat('dddd, D MMMM YYYY')); ?> · <?php echo e($semesterLabel); ?></p>
        </div>
        <div class="mp-page-actions">
            <a href="<?php echo e(route('eoffice.manprak.dosen.pendaftaran-koor.index')); ?>" class="mp-btn secondary md"
                style="text-decoration:none;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M22 11l-3.5 3.5-1.5-1.5" />
                </svg>
                Seleksi Koordinator
            </a>
        </div>
    </div>

    
    <div class="sec-head">
        <span class="sec-bar"></span>
        <span class="sec-title">Ringkasan</span>
        <span class="sec-rule"></span>
    </div>

    
    <div class="mp-stats-grid cols-4">
        <div class="mp-stat">
            <div class="mp-stat-icon navy">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                </svg>
            </div>
            <div class="mp-stat-label">Praktikum Aktif</div>
            <div class="mp-stat-value"><?php echo e($totalPraktikumAktif ?? 0); ?></div>
            <div class="mp-stat-sub">diampu <?php echo e($semesterLabel); ?></div>
        </div>
        <div class="mp-stat">
            <div class="mp-stat-icon yellow">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
                </svg>
            </div>
            <div class="mp-stat-label">Total Mahasiswa</div>
            <div class="mp-stat-value"><?php echo e($totalMahasiswa ?? 0); ?></div>
            <div class="mp-stat-sub">semua praktikum</div>
        </div>
        <div class="mp-stat">
            <div class="mp-stat-icon sky">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z" />
                </svg>
            </div>
            <div class="mp-stat-label">Total Modul</div>
            <div class="mp-stat-value"><?php echo e($totalModul ?? 0); ?></div>
            <div class="mp-stat-sub">modul tersedia</div>
        </div>
        <div class="mp-stat">
            <div class="mp-stat-icon yellow">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8zM14 2v6h6M9 15l2 2 4-4" />
                </svg>
            </div>
            <div class="mp-stat-label">Nilai Pending</div>
            <div class="mp-stat-value"><?php echo e($nilaiPending ?? 0); ?></div>
            <div class="mp-stat-sub">perlu diinput</div>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($praktikumTanpaKoor) && $praktikumTanpaKoor->isNotEmpty()): ?>
        <div class="mp-alert error flex-shrink-0">
            <div class="flex items-center gap-2 mb-3">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                <span style="font-size:13px;font-weight:700;">Praktikum Belum Punya Koordinator</span>
            </div>
            <div class="flex flex-col gap-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $praktikumTanpaKoor; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="flex items-center justify-between bg-white rounded-[8px] px-3 py-2 gap-4">
                        <span style="font-size:13px;font-weight:500;color:#0D0D12;"><?php echo e($p->nama); ?></span>
                        <form method="POST" action="<?php echo e(route('eoffice.manprak.dosen.tunjuk-koor')); ?>"
                            class="flex items-center gap-2 flex-shrink-0">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="praktikum_id" value="<?php echo e($p->id); ?>">
                            <div x-data="koorSearch('<?php echo e($p->id); ?>')" class="relative">
                                <input type="hidden" name="nim" x-model="selectedNim">
                                <input type="text" x-model="searchQuery" @input="handleInput" @focus="if(searchQuery.length >= 2) showDropdown = true" @click.away="showDropdown = false" placeholder="Cari Nama/NIM..." class="mp-input" style="width:200px;font-size:12px;" autocomplete="off">
                                <div x-show="showDropdown && searchQuery.length >= 2" class="absolute z-50 w-full mt-1 bg-white border border-[#DFE1E7] rounded-md shadow-lg max-h-48 overflow-y-auto" style="display:none;">
                                    <template x-for="item in suggestions" :key="item.id">
                                        <div @click="selectItem(item)" class="px-3 py-2 text-[12px] cursor-pointer hover:bg-[#F6F8FA]" x-text="item.text"></div>
                                    </template>
                                    <div x-show="loading" class="px-3 py-2 text-[12px] text-gray-500">Mencari...</div>
                                    <div x-show="!loading && suggestions.length === 0" class="px-3 py-2 text-[12px] text-red-500">Hasil tidak ditemukan</div>
                                </div>
                            </div>
                            <button type="submit" class="mp-btn primary sm">Tunjuk</button>
                        </form>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="sec-head">
        <span class="sec-bar"></span>
        <span class="sec-title">Praktikum yang Diampu</span>
        <span class="sec-rule"></span>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($praktikums) && !$praktikums->isEmpty()): ?>
            <span class="mp-badge navy sm"><?php echo e($praktikums->count()); ?> praktikum</span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <div class="mp-card flex-shrink-0">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!isset($praktikums) || $praktikums->isEmpty()): ?>
            <div style="padding:48px;text-align:center;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                    stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                </svg>
                <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada praktikum yang diampu.</div>
            </div>
        <?php else: ?>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;padding:18px;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $praktikums; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div style="border:1px solid #DFE1E7;border-radius:14px;padding:18px;transition:border-color .15s,box-shadow .15s;"
                        onmouseover="this.style.borderColor='#B7C2DE';this.style.boxShadow='0 4px 14px rgba(11,38,110,.07)'"
                        onmouseout="this.style.borderColor='#DFE1E7';this.style.boxShadow=''">

                        
                        <div class="flex items-start justify-between mb-3 gap-3">
                            <div class="min-w-0">
                                <div style="font-size:14px;font-weight:700;color:#0D0D12;margin-bottom:3px;" class="truncate">
                                    <?php echo e($p->nama); ?></div>

                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->status === 'aktif'): ?>
                                <span class="mp-badge success sm flex-shrink-0"><span class="dot"></span>Aktif</span>
                            <?php else: ?>
                                <span class="mp-badge neutral sm flex-shrink-0"><span class="dot"></span>Nonaktif</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:14px;">
                            <div
                                style="text-align:center;padding:8px 6px;background:#F9FAFB;border-radius:10px;border:1px solid #DFE1E7;">
                                <div style="font-size:18px;font-weight:700;color:#0D0D12;line-height:1;">
                                    <?php echo e($p->daftar_praktikan_count ?? 0); ?></div>
                                <div style="font-size:10px;color:#666D80;margin-top:3px;">Mahasiswa</div>
                            </div>
                            <div
                                style="text-align:center;padding:8px 6px;background:#F9FAFB;border-radius:10px;border:1px solid #DFE1E7;">
                                <div style="font-size:18px;font-weight:700;color:#0D0D12;line-height:1;">
                                    <?php echo e($p->modul_count ?? 0); ?></div>
                                <div style="font-size:10px;color:#666D80;margin-top:3px;">Modul</div>
                            </div>
                            <div
                                style="text-align:center;padding:8px 6px;background:#F9FAFB;border-radius:10px;border:1px solid #DFE1E7;">
                                <div style="font-size:18px;font-weight:700;color:#0D0D12;line-height:1;">
                                    <?php echo e($p->asisten_praktikum_count ?? 0); ?></div>
                                <div style="font-size:10px;color:#666D80;margin-top:3px;">Asisten Praktikum</div>
                            </div>
                        </div>

                        
                        <div style="font-size:11px;color:#666D80;margin-bottom:12px;display:flex;align-items:center;gap:6px;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round">
                                <path
                                    d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M22 11l-3.5 3.5-1.5-1.5" />
                            </svg>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->koordinator): ?>
                                <span style="font-weight:600;color:#0D0D12;"><?php echo e($p->koordinator->name); ?></span>
                            <?php else: ?>
                                <span style="font-weight:600;color:#DF1C41;">Belum ditunjuk</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <div class="flex gap-2">
                            <a href="<?php echo e(route('eoffice.manprak.dosen.modul.index', $p->id)); ?>"
                                class="mp-btn secondary sm flex-1 text-center" style="text-decoration:none;">Lihat Modul</a>
                            <a href="<?php echo e(route('eoffice.manprak.dosen.nilai.index', $p->id)); ?>"
                                class="mp-btn primary sm flex-1 text-center" style="text-decoration:none;">Lihat Nilai</a>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('koorSearch', (praktikumId) => ({
            praktikumId: praktikumId,
            searchQuery: '',
            selectedNim: '',
            suggestions: [],
            showDropdown: false,
            loading: false,

            timeout: null,
            handleInput() {
                this.selectedNim = '';
                if (this.searchQuery.length < 2) {
                    this.suggestions = [];
                    this.showDropdown = false;
                    this.loading = false;
                    return;
                }
                this.loading = true;
                this.showDropdown = true;
                
                clearTimeout(this.timeout);
                this.timeout = setTimeout(() => {
                    this.fetchSuggestions();
                }, 400);
            },

            async fetchSuggestions() {
                try {
                    const response = await fetch(`<?php echo e(route('eoffice.manprak.dosen.search-praktikan')); ?>?q=${encodeURIComponent(this.searchQuery)}&praktikum_id=${this.praktikumId}`);
                    if (response.ok) {
                        this.suggestions = await response.json();
                    } else {
                        this.suggestions = [];
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                    this.suggestions = [];
                }
                this.loading = false;
            },

            selectItem(item) {
                this.selectedNim = item.id;
                this.searchQuery = item.text;
                this.showDropdown = false;
            }
        }));
    });
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc437526b4c56b06f9c16ab6722a84925)): ?>
<?php $attributes = $__attributesOriginalc437526b4c56b06f9c16ab6722a84925; ?>
<?php unset($__attributesOriginalc437526b4c56b06f9c16ab6722a84925); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc437526b4c56b06f9c16ab6722a84925)): ?>
<?php $component = $__componentOriginalc437526b4c56b06f9c16ab6722a84925; ?>
<?php unset($__componentOriginalc437526b4c56b06f9c16ab6722a84925); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-praktikum_backup_kode\dosen\dashboard.blade.php ENDPATH**/ ?>