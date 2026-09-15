<?php if (isset($component)) { $__componentOriginal5f8703e57583ba4eb55a214d5c40ce87 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5f8703e57583ba4eb55a214d5c40ce87 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'manajemenmahasiswa::components.layouts.admin','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('manajemenmahasiswa::layouts.admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


    <?php $__env->startPush('styles'); ?>
    <style>
        .main-wrapper { background:transparent !important; box-shadow:none !important; padding:0 !important; }

        .pengumuman-header h4 { font-size:1.5rem; font-weight:700; color:#1e1b4b; margin-bottom:4px; }
        .pengumuman-header p  { font-size:.95rem; color:#6b7280; margin-bottom:0; }

        /* Toolbar */
        .search-filter-bar { display:flex; gap:16px; align-items:center; }
        .search-input-wrapper { flex:1; position:relative; }
        .search-input-wrapper input {
            width:100%; padding:12px 18px 12px 46px;
            border:1px solid #e5e7eb; border-radius:12px; background:#f3f0ff;
            font-size:.9rem; color:#4b5563; transition:all .25s; outline:none;
        }
        .search-input-wrapper input::placeholder { color:#9ca3af; }
        .search-input-wrapper input:focus {
            border-color:#6B4FF4; box-shadow:0 0 0 3px rgba(107,79,244,.12); background:#fff;
        }
        .search-input-wrapper .search-icon {
            position:absolute; left:16px; top:50%; transform:translateY(-50%);
            color:#9ca3af; pointer-events:none;
        }
        .filter-dropdown { position:relative; }
        .filter-btn {
            display:flex; align-items:center; gap:8px; padding:12px 20px;
            border:1px solid #e5e7eb; border-radius:12px; background:#fff;
            font-size:.9rem; color:#374151; font-weight:500; cursor:pointer;
            transition:all .2s; white-space:nowrap; min-width:160px; justify-content:space-between;
        }
        .filter-btn:hover  { border-color:#6B4FF4; background:#F5F3FF; }
        .filter-btn.active { border-color:#6B4FF4; color:#6B4FF4; }
        .filter-btn .chevron-icon { transition:transform .2s; }
        .filter-btn.open .chevron-icon { transform:rotate(180deg); }
        .filter-menu {
            position:absolute; top:calc(100% + 6px); right:0; background:#fff;
            border:1px solid #e5e7eb; border-radius:12px; padding:8px; min-width:200px;
            box-shadow:0 10px 25px rgba(0,0,0,.08); z-index:100; display:none;
        }
        .filter-menu.show { display:block; }
        .filter-menu-item {
            display:flex; align-items:center; gap:10px; padding:10px 14px;
            border-radius:8px; cursor:pointer; transition:background .15s;
            font-size:.88rem; color:#374151;
        }
        .filter-menu-item:hover { background:#f5f3ff; }
        .filter-menu-item.selected { background:#F5F3FF; color:#6B4FF4; font-weight:600; }
        .filter-menu-item .check-icon { width:18px; color:#6B4FF4; opacity:0; transition:opacity .15s; }
        .filter-menu-item.selected .check-icon { opacity:1; }

        /* Buat Post */
        .btn-buat-post {
            display:inline-flex; align-items:center; gap:8px; padding:12px 28px;
            background:#6B4FF4; color:#fff; border:none; border-radius:12px;
            font-size:.9rem; font-weight:600; text-decoration:none; cursor:pointer;
            transition:all .2s; white-space:nowrap;
        }
        .btn-buat-post:hover {
            background:#8266F5; color:#fff; transform:translateY(-1px);
            box-shadow:0 4px 12px rgba(107,79,244,.3);
        }

        /* List cards */
        .pengumuman-list { display:flex; flex-direction:column; gap:16px; }
        .pengumuman-card {
            background:#fff; border:1px solid #DDE1E8; border-radius:12px;
            padding:22px 26px; transition:all .25s; cursor:pointer; display:block;
            box-shadow:0 1px 3px rgba(22,22,43,.06),0 1px 2px rgba(22,22,43,.04);
        }
        .pengumuman-card:hover {
            border-color:#C6CBD2;
            box-shadow:0 4px 8px -2px rgba(22,22,43,.06),0 2px 4px -2px rgba(22,22,43,.04);
            transform:translateY(-1px);
        }
        .pengumuman-card.pinned-global   { border-left:3px solid #d97706; background:#fffbf0; }
        .pengumuman-card.pinned-personal { border-left:3px solid #2563eb; background:#f0f6ff; }
        .pengumuman-card.pinned-global.pinned-personal { border-left:3px solid #d97706; background:#fffbf0; }

        .pin-badge {
            display:inline-flex; align-items:center; gap:4px; padding:2px 8px;
            border-radius:4px; font-size:10px; font-weight:700;
            letter-spacing:.03em; text-transform:uppercase;
        }
        .pin-badge-global   { background:#fef3c7; color:#d97706; }
        .pin-badge-personal { background:#dbeafe; color:#2563eb; }

        /* Action icon buttons */
        .btn-action-icon {
            display:inline-flex; align-items:center; justify-content:center;
            width:34px; height:34px; border-radius:8px; border:1px solid #e5e7eb;
            background:#fff; color:#6b7280; cursor:pointer; transition:all .2s; padding:0;
        }
        .btn-action-icon:hover       { border-color:#6B4FF4; background:#f5f3ff; color:#6B4FF4; transform:translateY(-1px); }
        .btn-action-icon.btn-edit:hover   { border-color:#f59e0b; background:#fffbeb; color:#f59e0b; }
        .btn-action-icon.btn-delete:hover { border-color:#ef4444; background:#fef2f2; color:#ef4444; }
        .btn-action-icon.active-personal  { border-color:#2563eb; background:#dbeafe; color:#2563eb; }
        .btn-action-icon.active-global    { border-color:#d97706; background:#fef3c7; color:#d97706; }
        .action-buttons { display:flex; align-items:center; gap:6px; flex-wrap:wrap; justify-content:flex-end; }

        .pengumuman-card-body { display:flex; align-items:stretch; gap:20px; }
        .pengumuman-thumbnail {
            width:90px; aspect-ratio:1080/1320; border-radius:10px; overflow:hidden;
            flex-shrink:0; background:#f9fafb; border:1px solid #e5e7eb;
            display:flex; align-items:center; justify-content:center;
        }
        .pengumuman-thumbnail img { width:100%; height:100%; object-fit:cover; display:block; }
        .pengumuman-thumbnail .no-image { width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#d1d5db; }

        .pengumuman-card-content { flex:1; min-width:0; display:flex; flex-direction:column; justify-content:center; }
        .pengumuman-card-title { display:flex; align-items:center; gap:10px; margin-bottom:8px; }
        .pengumuman-card-title .megaphone-icon { flex-shrink:0; color:#6B4FF4; }
        .pengumuman-card-title h6 { font-size:1.05rem; font-weight:700; color:#1e1b4b; margin:0; line-height:1.4; }
        .pengumuman-card-desc {
            font-size:.88rem; color:#788297; line-height:1.6; margin-bottom:12px;
            display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
        }
        .pengumuman-card-tags { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:12px; }
        .pengumuman-card-meta { display:flex; justify-content:space-between; align-items:center; }
        .pengumuman-card-date { font-size:.82rem; color:#A1ADB8; font-weight:500; }

        .pengumuman-card-badge {
            display:inline-flex; align-items:center; padding:4px 12px;
            border-radius:20px; font-size:.75rem; font-weight:600; letter-spacing:.01em;
        }
        .badge-akademik    { background:#E8F4FF; color:#1A8CD8; }
        .badge-himpunan    { background:#F5F3FF; color:#6B4FF4; }
        .badge-lowongan    { background:#E6FBF0; color:#0D9F5F; }
        .badge-event_prodi { background:#FFF9E6; color:#C6930A; }
        .badge-umum        { background:#EEF0F5; color:#606B80; }
        .badge-draft       { background:#FFF0ED; color:#D94425; }
        .badge-published   { background:#E6FBF0; color:#0D9F5F; }
        .badge-archived    { background:#F6F8FB; color:#788297; }
        .badge-pending_review { background:#FEF9E7; color:#B7791F; }

        .pengumuman-card-action {
            display:flex; flex-direction:column; align-items:flex-end;
            justify-content:space-between; padding-left:16px; min-width:130px;
        }
        .pengumuman-read-more {
            display:inline-flex; align-items:center; gap:4px; flex-shrink:0;
            font-size:.85rem; font-weight:600; color:#6B4FF4; text-decoration:none;
            transition:all .2s; white-space:nowrap;
        }
        .pengumuman-read-more:hover { color:#8266F5; }

        /* Pagination */
        .pagination .page-link {
            color:#6B4FF4; border-color:#e5e7eb; border-radius:8px;
            margin:0 2px; font-size:.875rem; font-weight:500;
            padding:8px 14px; transition:all .2s;
        }
        .pagination .page-link:hover { background:#F5F3FF; border-color:#6B4FF4; }
        .pagination .page-item.active .page-link { background:#6B4FF4; border-color:#6B4FF4; color:#fff; }
        .pagination .page-item.disabled .page-link { color:#d1d5db; border-color:#e5e7eb; }
        .pagination-info-text { font-size:.82rem; color:#9ca3af; font-weight:500; }

        /* Empty state */
        .pengumuman-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:60px 20px; text-align:center; }
        .pengumuman-empty .empty-icon { width:80px; height:80px; background:#f5f3ff; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-bottom:20px; }
        .pengumuman-empty .empty-icon svg { color:#6B4FF4; }
        .pengumuman-empty h5 { font-size:1.1rem; font-weight:600; color:#374151; margin-bottom:6px; }
        .pengumuman-empty p { font-size:.9rem; color:#9ca3af; }

        /* Lightbox */
        .zoomable-thumbnail { cursor:zoom-in; position:relative; }
        .zoomable-thumbnail::after {
            content:''; position:absolute; inset:0;
            background:rgba(0,0,0,.2); opacity:0; transition:opacity .2s;
            border-radius:10px;
        }
        .zoomable-thumbnail:hover::after { opacity:1; }
        .lightbox-modal {
            display:none; position:fixed; inset:0; z-index:10000;
            background:rgba(0,0,0,.92); align-items:center; justify-content:center;
        }
        .lightbox-modal.active { display:flex; }
        .lightbox-content img {
            max-width:90vw; max-height:82vh; object-fit:contain;
            border-radius:8px; box-shadow:0 25px 60px rgba(0,0,0,.4);
        }
        .lightbox-close {
            position:fixed; top:20px; right:24px; width:44px; height:44px;
            border-radius:50%; background:rgba(255,255,255,.12);
            border:1px solid rgba(255,255,255,.2); color:#fff; font-size:20px;
            cursor:pointer; display:flex; align-items:center; justify-content:center;
            transition:all .2s; z-index:10001;
        }
        .lightbox-close:hover { background:rgba(255,255,255,.22); }
    </style>
    <?php $__env->stopPush(); ?>

    <?php
        $authUser      = Auth::user();
        $canPinGlobal  = $authUser->hasAnyRole(['superadmin','admin','admin_kemahasiswaan']);
        $isAdminOrKoor = $authUser->roles->pluck('name')->intersect(['superadmin','admin','dosen_koordinator'])->isNotEmpty();

        $kategoriMap      = ['semua'=>'Filter','akademik'=>'Akademik','himpunan'=>'Himpunan','lowongan'=>'Lowongan','event_prodi'=>'Event Prodi'];
        $selectedKategori = request('kategori','semua');
    ?>

    <!-- Header -->
    <div class="pengumuman-header mb-4">
        <h4>Pengumuman & Informasi</h4>
        <p>Wadah Informasi untuk Mahasiswa dan Alumni</p>
    </div>

    <!-- Toolbar -->
    <form id="pengumumanFilterForm" method="GET" action="<?php echo e(route('manajemenmahasiswa.pengumuman.index')); ?>">
        <div class="search-filter-bar mb-4">
            <div class="search-input-wrapper">
                <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
                <input type="text" name="search" id="searchInput" placeholder="Cari pengumuman..."
                    value="<?php echo e(request('search')); ?>">
            </div>

            <div class="filter-dropdown">
                <input type="hidden" name="kategori" id="kategoriInput" value="<?php echo e($selectedKategori); ?>">
                <button type="button" class="filter-btn <?php echo e($selectedKategori !== 'semua' ? 'active' : ''); ?>"
                    id="filterToggle" onclick="toggleFilterMenu()">
                    <span id="filterLabel"><?php echo e($kategoriMap[$selectedKategori] ?? 'Filter'); ?></span>
                    <svg class="chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div class="filter-menu" id="filterMenu">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['semua'=>'Semua Kategori','akademik'=>'Akademik','himpunan'=>'Himpunan','lowongan'=>'Lowongan','event_prodi'=>'Event Prodi']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="filter-menu-item <?php echo e($selectedKategori === $value ? 'selected' : ''); ?>"
                            onclick="selectFilter('<?php echo e($value); ?>', '<?php echo e($label); ?>')">
                            <svg class="check-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            <span><?php echo e($label); ?></span>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>

            <select name="per_page" onchange="document.getElementById('pengumumanFilterForm').submit()"
                style="height:44px;padding:0 12px;border:1px solid #e5e7eb;border-radius:12px;background:#f3f0ff;font-size:.85rem;font-weight:600;color:#4b5563;cursor:pointer;outline:none;transition:all .2s;">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [5,10,20,50]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <option value="<?php echo e($opt); ?>" <?php echo e(request('per_page',10) == $opt ? 'selected' : ''); ?>>
                        <?php echo e($opt); ?> / hal
                    </option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>

            <a href="<?php echo e(route('manajemenmahasiswa.pengumuman.create')); ?>" class="btn-buat-post">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Buat Post
            </a>
        </div>
    </form>

    <!-- List -->
    <div class="pengumuman-list">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pengumuman; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <?php
                $lampiran = collect($item->repoMulmed ?? []);
                $images   = $lampiran->filter(fn($f) => in_array(
                    strtolower(pathinfo($f->nama_file ?? '', PATHINFO_EXTENSION)),
                    ['jpg','jpeg','png','gif','webp']
                ));
                $thumbnailUrl     = $images->first()
                    ? app(\App\Services\SupabaseStorage::class)->getPublicUrl($images->first()->path_file)
                    : null;
                $isPinnedGlobal   = (bool) $item->is_pinned;
                $isPinnedPersonal = (bool) $item->is_personal_pinned;
                $canEdit   = $authUser->id === $item->user_id || $isAdminOrKoor;
                $canDelete = $authUser->id === $item->user_id || $isAdminOrKoor;
                $cardClass = 'pengumuman-card'
                    . ($isPinnedGlobal   ? ' pinned-global'   : '')
                    . ($isPinnedPersonal ? ' pinned-personal' : '');
            ?>
            <div class="<?php echo e($cardClass); ?>"
                 data-href="<?php echo e(route('manajemenmahasiswa.pengumuman.show', $item->id)); ?>"
                 onclick="navigatePengumuman(event, this)">
                <div class="pengumuman-card-body">
                    <!-- Thumbnail -->
                    <div class="pengumuman-thumbnail <?php echo e($thumbnailUrl ? 'zoomable-thumbnail' : ''); ?>"
                         <?php if($thumbnailUrl): ?> onclick="openLightbox(event,'<?php echo e($thumbnailUrl); ?>','<?php echo e(addslashes($item->judul)); ?>')" <?php endif; ?>>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($thumbnailUrl): ?>
                            <img src="<?php echo e($thumbnailUrl); ?>" alt="Thumbnail">
                        <?php else: ?>
                            <div class="no-image">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <!-- Content -->
                    <div class="pengumuman-card-content">
                        <div class="pengumuman-card-title">
                            <svg class="megaphone-icon" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
                            </svg>
                            <h6><?php echo e($item->judul); ?></h6>
                        </div>
                        <p class="pengumuman-card-desc">
                            <?php echo e(Str::limit(html_entity_decode(strip_tags($item->konten)), 150)); ?>

                        </p>
                        <div class="pengumuman-card-tags">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isPinnedGlobal): ?>
                                <span class="pin-badge pin-badge-global">
                                    <svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5v6h2v-6h5v-2l-2-2z"/></svg>
                                    Pinned
                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isPinnedPersonal): ?>
                                <span class="pin-badge pin-badge-personal">
                                    <svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
                                    Pin Pribadi
                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->status_publish && $item->status_publish !== 'published'): ?>
                                <span class="pengumuman-card-badge badge-<?php echo e($item->status_publish); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->status_publish === 'pending_review'): ?> ⏳ Menunggu Verifikasi
                                    <?php else: ?> <?php echo e(ucfirst($item->status_publish)); ?>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->kategori): ?>
                                <span class="pengumuman-card-badge badge-<?php echo e($item->kategori); ?>">
                                    <?php echo e(ucfirst(str_replace('_',' ',$item->kategori))); ?>

                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="pengumuman-card-meta">
                            <span class="pengumuman-card-date">
                                <?php echo e($item->created_at->translatedFormat('d F Y')); ?>

                            </span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pengumuman-card-action" onclick="event.stopPropagation()">
                        <div class="action-buttons mb-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit): ?>
                                <a href="<?php echo e(route('manajemenmahasiswa.pengumuman.edit', $item->id)); ?>"
                                    class="btn-action-icon btn-edit" title="Edit">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDelete): ?>
                                <form method="POST" action="<?php echo e(route('manajemenmahasiswa.pengumuman.remove', $item->id)); ?>"
                                    onsubmit="return confirm('Hapus pengumuman ini?')" style="margin:0;">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn-action-icon btn-delete" title="Hapus">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        </svg>
                                    </button>
                                </form>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <form method="POST" action="<?php echo e(route('manajemenmahasiswa.pengumuman.personal_pin', $item->id)); ?>" style="margin:0;">
                                <?php echo csrf_field(); ?>
                                <button type="submit"
                                    class="btn-action-icon <?php echo e($isPinnedPersonal ? 'active-personal' : ''); ?>"
                                    title="<?php echo e($isPinnedPersonal ? 'Unpin Pribadi' : 'Pin Pribadi'); ?>">
                                    <svg width="14" height="14" viewBox="0 0 24 24"
                                        fill="<?php echo e($isPinnedPersonal ? 'currentColor' : 'none'); ?>"
                                        stroke="currentColor" stroke-width="2">
                                        <path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/>
                                    </svg>
                                </button>
                            </form>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canPinGlobal): ?>
                                <form method="POST" action="<?php echo e(route('manajemenmahasiswa.pengumuman.pin', $item->id)); ?>" style="margin:0;">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <button type="submit"
                                        class="btn-action-icon <?php echo e($isPinnedGlobal ? 'active-global' : ''); ?>"
                                        title="<?php echo e($isPinnedGlobal ? 'Unpin Global' : 'Pin Global'); ?>">
                                        <svg width="14" height="14" viewBox="0 0 24 24"
                                            fill="<?php echo e($isPinnedGlobal ? 'currentColor' : 'none'); ?>"
                                            stroke="currentColor" stroke-width="2">
                                            <path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5v6h2v-6h5v-2l-2-2z"/>
                                        </svg>
                                    </button>
                                </form>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="pengumuman-empty">
                <div class="empty-icon">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
                    </svg>
                </div>
                <h5>Belum ada pengumuman</h5>
                <p>Pengumuman terbaru akan muncul di sini</p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pengumuman->total() > 0): ?>
        <div class="mt-4 mb-2">
            <span class="pagination-info-text">
                Menampilkan <?php echo e($pengumuman->firstItem()); ?>–<?php echo e($pengumuman->lastItem()); ?>

                dari <?php echo e($pengumuman->total()); ?> pengumuman
            </span>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pengumuman->hasPages()): ?>
        <div class="d-flex justify-content-center mt-2 mb-4">
            <?php echo e($pengumuman->appends(request()->query())->links('pagination::bootstrap-5')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Lightbox Modal -->
    <div class="lightbox-modal" id="lightboxModal">
        <button class="lightbox-close" onclick="closeLightbox()">&times;</button>
        <div class="lightbox-content">
            <img id="lightboxImage" src="" alt="">
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
    function openLightbox(event, src, title) {
        event.stopPropagation();
        document.getElementById('lightboxImage').src = src;
        document.getElementById('lightboxModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeLightbox() {
        document.getElementById('lightboxModal').classList.remove('active');
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });
    document.getElementById('lightboxModal')?.addEventListener('click', e => {
        if (e.target === document.getElementById('lightboxModal')) closeLightbox();
    });

    function navigatePengumuman(event, card) {
        if (event.target.closest('form, button, a')) return;
        window.location.href = card.dataset.href;
    }
    function toggleFilterMenu() {
        document.getElementById('filterMenu')?.classList.toggle('show');
        document.getElementById('filterToggle')?.classList.toggle('open');
    }
    function selectFilter(value, label) {
        document.getElementById('kategoriInput').value = value;
        document.getElementById('filterLabel').textContent = value === 'semua' ? 'Filter' : label;
        document.querySelectorAll('.filter-menu-item').forEach(i => i.classList.remove('selected'));
        event.currentTarget.classList.add('selected');
        toggleFilterMenu();
        document.getElementById('pengumumanFilterForm').submit();
    }
    document.addEventListener('click', e => {
        const d = document.querySelector('.filter-dropdown');
        if (d && !d.contains(e.target)) {
            document.getElementById('filterMenu')?.classList.remove('show');
            document.getElementById('filterToggle')?.classList.remove('open');
        }
    });
    document.getElementById('searchInput')?.addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); document.getElementById('pengumumanFilterForm').submit(); }
    });
    </script>
    <?php $__env->stopPush(); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5f8703e57583ba4eb55a214d5c40ce87)): ?>
<?php $attributes = $__attributesOriginal5f8703e57583ba4eb55a214d5c40ce87; ?>
<?php unset($__attributesOriginal5f8703e57583ba4eb55a214d5c40ce87); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5f8703e57583ba4eb55a214d5c40ce87)): ?>
<?php $component = $__componentOriginal5f8703e57583ba4eb55a214d5c40ce87; ?>
<?php unset($__componentOriginal5f8703e57583ba4eb55a214d5c40ce87); ?>
<?php endif; ?>
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\ManajemenMahasiswa\resources\views\pengumuman\pengumuman-admin.blade.php ENDPATH**/ ?>