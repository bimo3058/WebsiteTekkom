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

        /* ── Page Header ─────────────────────────────── */
        .pg-header { margin-bottom: 28px; }
        .pg-header h4 {
            font-size: 1.6rem; font-weight: 800; color: #0D0D12;
            margin-bottom: 4px; letter-spacing: -.02em;
        }
        .pg-header p { font-size: .9rem; color: #808897; margin: 0; }

        /* ── Toolbar ─────────────────────────────────── */
        .pg-toolbar {
            display: flex; gap: 12px; align-items: center;
            margin-bottom: 28px; flex-wrap: wrap;
        }
        .pg-search-wrap { flex: 1; min-width: 200px; position: relative; }
        .pg-search-wrap svg {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%); color: #9ca3af; pointer-events: none;
        }
        .pg-search-wrap input {
            width: 100%; padding: 10px 16px 10px 42px;
            border: 1px solid #e5e7eb; border-radius: 10px;
            background: #fafafa; font-size: .88rem; color: #374151; outline: none;
            transition: all .2s;
        }
        .pg-search-wrap input:focus {
            border-color: #6B4FF4; background: #fff;
            box-shadow: 0 0 0 3px rgba(107,79,244,.1);
        }
        .pg-filter-dropdown { position: relative; }
        .pg-filter-btn {
            display: flex; align-items: center; gap: 8px; padding: 10px 18px;
            border: 1px solid #e5e7eb; border-radius: 10px; background: #fff;
            font-size: .88rem; font-weight: 500; color: #374151;
            cursor: pointer; transition: all .2s; white-space: nowrap; min-width: 150px;
            justify-content: space-between;
        }
        .pg-filter-btn:hover,
        .pg-filter-btn.active { border-color: #6B4FF4; color: #6B4FF4; background: #f5f3ff; }
        .pg-filter-btn .chevron { transition: transform .2s; }
        .pg-filter-btn.open .chevron { transform: rotate(180deg); }
        .pg-filter-menu {
            position: absolute; top: calc(100% + 6px); right: 0;
            background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
            padding: 6px; min-width: 190px;
            box-shadow: 0 12px 30px rgba(0,0,0,.1); z-index: 200; display: none;
        }
        .pg-filter-menu.show { display: block; }
        .pg-filter-item {
            display: flex; align-items: center; gap: 8px;
            padding: 9px 12px; border-radius: 8px; cursor: pointer;
            font-size: .85rem; color: #374151; transition: background .15s;
        }
        .pg-filter-item:hover { background: #f5f3ff; }
        .pg-filter-item.selected { background: #f5f3ff; color: #6B4FF4; font-weight: 600; }
        .pg-filter-item .pg-check { width: 16px; color: #6B4FF4; opacity: 0; }
        .pg-filter-item.selected .pg-check { opacity: 1; }
        .pg-perpage {
            height: 42px; padding: 0 12px; border: 1px solid #e5e7eb;
            border-radius: 10px; background: #fff; font-size: .85rem;
            font-weight: 600; color: #374151; cursor: pointer; outline: none; transition: all .2s;
        }
        .pg-perpage:hover { border-color: #6B4FF4; }
        .btn-buat-post {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 10px 20px; background: #6B4FF4; color: #fff;
            border-radius: 10px; font-size: .88rem; font-weight: 700;
            text-decoration: none; transition: all .2s; white-space: nowrap; flex-shrink: 0;
        }
        .btn-buat-post:hover {
            background: #8266F5; color: #fff;
            box-shadow: 0 4px 14px rgba(107,79,244,.35); transform: translateY(-1px);
        }

        /* ── Cards Grid ──────────────────────────────── */
        .pg-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
            margin-bottom: 32px;
        }
        @media (max-width: 992px) { .pg-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px)  { .pg-grid { grid-template-columns: 1fr; } }

        /* ── Single Card ─────────────────────────────── */
        .pg-card {
            background: #fff; border: 1px solid #e5e7eb; border-radius: 16px;
            overflow: hidden; display: flex; flex-direction: column;
            cursor: pointer; transition: box-shadow .25s, transform .25s;
        }
        .pg-card:hover { box-shadow: 0 12px 32px rgba(0,0,0,.1); transform: translateY(-3px); }
        .pg-card.pinned-global   { border-top: 3px solid #d97706; }
        .pg-card.pinned-personal { border-top: 3px solid #6B4FF4; }
        .pg-card.pinned-global.pinned-personal { border-top: 3px solid #d97706; }

        /* Image */
        .pg-card-img {
            position: relative; width: 100%; aspect-ratio: 16/9;
            overflow: hidden; background: #f3f4f6; flex-shrink: 0;
        }
        .pg-card-img img {
            width: 100%; height: 100%; object-fit: cover; display: block;
            transition: transform .4s ease;
        }
        .pg-card:hover .pg-card-img img { transform: scale(1.04); }
        .pg-card-img-placeholder {
            width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
        }

        /* Overlays on image */
        .pg-badge-overlay {
            position: absolute; top: 10px; left: 10px;
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 9px; border-radius: 50px; font-size: 10px;
            font-weight: 800; letter-spacing: .04em; text-transform: uppercase;
            backdrop-filter: blur(6px);
        }
        .pg-badge-pinned   { background: rgba(255,251,235,.92); color: #d97706; }
        .pg-badge-draft    { background: rgba(243,244,246,.92); color: #6b7280; }
        .pg-badge-archived { background: rgba(254,242,242,.92); color: #dc2626; }
        .pg-badge-pending  { background: rgba(255,251,235,.92); color: #d97706; }

        /* Top-right overlay actions (pin personal + bookmark) */
        .pg-img-actions {
            position: absolute; top: 10px; right: 10px;
            display: flex; flex-direction: column; gap: 6px;
        }
        .pg-icon-btn {
            width: 30px; height: 30px; border-radius: 50%;
            background: rgba(255,255,255,.9); backdrop-filter: blur(4px);
            border: none; cursor: pointer; display: flex; align-items: center;
            justify-content: center; transition: all .2s; color: #9ca3af;
        }
        .pg-icon-btn:hover { background: #fff; transform: scale(1.1); }
        .pg-icon-btn.pin-active-personal { color: #6B4FF4; }
        .pg-icon-btn.pin-active-global   { color: #d97706; }

        /* Card body */
        .pg-card-body { padding: 16px 18px 14px; flex: 1; display: flex; flex-direction: column; }

        /* Category */
        .pg-category {
            font-size: .72rem; font-weight: 800; letter-spacing: .08em;
            text-transform: uppercase; margin-bottom: 7px;
        }
        .pg-cat-akademik    { color: #1A8CD8; }
        .pg-cat-himpunan    { color: #6B4FF4; }
        .pg-cat-lowongan    { color: #0D9F5F; }
        .pg-cat-event_prodi { color: #C6930A; }
        .pg-cat-default     { color: #808897; }

        /* Title */
        .pg-card-title {
            font-size: 1rem; font-weight: 800; color: #0D0D12;
            line-height: 1.4; margin-bottom: 8px; letter-spacing: -.01em;
            display: -webkit-box; -webkit-line-clamp: 3;
            -webkit-box-orient: vertical; overflow: hidden;
        }

        /* Excerpt */
        .pg-card-excerpt {
            font-size: .82rem; color: #6b7280; line-height: 1.6;
            margin-bottom: 12px; flex: 1;
            display: -webkit-box; -webkit-line-clamp: 2;
            -webkit-box-orient: vertical; overflow: hidden;
        }

        /* Author + date */
        .pg-card-footer {
            display: flex; align-items: center; justify-content: space-between;
            gap: 8px; padding-top: 12px; border-top: 1px solid #f3f4f6; margin-top: auto;
        }
        .pg-author { display: flex; align-items: center; gap: 7px; min-width: 0; }
        .pg-avatar {
            width: 26px; height: 26px; border-radius: 50%; flex-shrink: 0;
            background: linear-gradient(135deg, #6B4FF4, #8266F5);
            color: #fff; font-size: 9px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
        }
        .pg-author-name {
            font-size: .77rem; font-weight: 600; color: #374151;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .pg-date { font-size: .74rem; color: #9ca3af; white-space: nowrap; flex-shrink: 0; }

        /* Action bar (edit/delete) at bottom of card */
        .pg-card-actions {
            display: flex; align-items: center; gap: 6px;
            padding: 10px 16px; border-top: 1px solid #f3f4f6;
            background: #fafafa; flex-shrink: 0;
        }
        .pg-action-btn {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 12px; border-radius: 7px; border: 1px solid #e5e7eb;
            background: #fff; font-size: .76rem; font-weight: 600; cursor: pointer;
            text-decoration: none; transition: all .15s; color: #374151;
        }
        .pg-action-btn:hover { border-color: #6B4FF4; color: #6B4FF4; background: #f5f3ff; }
        .pg-action-btn.danger:hover { border-color: #ef4444; color: #ef4444; background: #fef2f2; }
        .pg-action-btn.pin-on { border-color: #d97706; color: #d97706; background: #fffbeb; }
        .pg-spacer { flex: 1; }

        /* Empty state */
        .pg-empty {
            grid-column: 1/-1; padding: 72px 20px; text-align: center; color: #9ca3af;
        }
        .pg-empty-icon {
            width: 72px; height: 72px; border-radius: 50%;
            background: #f5f3ff; display: flex; align-items: center;
            justify-content: center; margin: 0 auto 16px; color: #6B4FF4;
        }
        .pg-empty h5 { font-size: 1rem; font-weight: 700; color: #374151; margin-bottom: 4px; }
        .pg-empty p  { font-size: .88rem; }

        /* Pagination */
        .pagination .page-link {
            color: #6B4FF4; border-color: #e5e7eb; border-radius: 8px;
            margin: 0 2px; font-size: .875rem; font-weight: 500;
            padding: 7px 13px; transition: all .2s;
        }
        .pagination .page-link:hover { background: #f5f3ff; border-color: #6B4FF4; }
        .pagination .page-item.active .page-link { background: #6B4FF4; border-color: #6B4FF4; color: #fff; }
        .pagination .page-item.disabled .page-link { color: #d1d5db; border-color: #e5e7eb; }
        .pg-pagination-info { font-size: .82rem; color: #9ca3af; font-weight: 500; }

        /* Lightbox */
        .lightbox-modal {
            display: none; position: fixed; inset: 0; z-index: 10000;
            background: rgba(0,0,0,.92); align-items: center; justify-content: center;
        }
        .lightbox-modal.active { display: flex; }
        .lightbox-content img {
            max-width: 90vw; max-height: 82vh; object-fit: contain;
            border-radius: 8px; box-shadow: 0 25px 60px rgba(0,0,0,.4);
        }
        .lightbox-close {
            position: fixed; top: 20px; right: 24px; width: 44px; height: 44px;
            border-radius: 50%; background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2);
            color: #fff; font-size: 20px; cursor: pointer;
            display: flex; align-items: center; justify-content: center; transition: all .2s;
        }
        .lightbox-close:hover { background: rgba(255,255,255,.22); }
    </style>
    <?php $__env->stopPush(); ?>

    <?php
        $authUser       = Auth::user();
        $canPinGlobal   = $authUser->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan']);
        $isAdminOrKoor  = $authUser->roles->pluck('name')->intersect(['superadmin','admin','dosen_koordinator'])->isNotEmpty();

        $kategoriMap = [
            'semua'       => 'Semua',
            'akademik'    => 'Akademik',
            'himpunan'    => 'Himpunan',
            'lowongan'    => 'Lowongan',
            'event_prodi' => 'Event Prodi',
        ];
        $selectedKategori = request('kategori', 'semua');

        $placeholderGradients = [
            'akademik'    => 'linear-gradient(135deg,#E8F4FF,#BFDBFE)',
            'himpunan'    => 'linear-gradient(135deg,#F5F3FF,#DDD6FE)',
            'lowongan'    => 'linear-gradient(135deg,#ECFDF5,#A7F3D0)',
            'event_prodi' => 'linear-gradient(135deg,#FFFBEB,#FDE68A)',
            'default'     => 'linear-gradient(135deg,#F8FAFC,#E2E8F0)',
        ];
    ?>

    
    <div class="pg-header">
        <h4>Pengumuman & Informasi</h4>
        <p>Wadah informasi untuk mahasiswa dan alumni</p>
    </div>

    
    <form id="pgFilterForm" method="GET" action="<?php echo e(route('manajemenmahasiswa.pengumuman.index')); ?>">
        <div class="pg-toolbar">
            <div class="pg-search-wrap">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
                <input type="text" name="search" id="pgSearchInput"
                    placeholder="Cari pengumuman..." value="<?php echo e(request('search')); ?>">
            </div>

            <div class="pg-filter-dropdown">
                <input type="hidden" name="kategori" id="pgKategoriInput" value="<?php echo e($selectedKategori); ?>">
                <button type="button" class="pg-filter-btn <?php echo e($selectedKategori !== 'semua' ? 'active' : ''); ?>"
                    id="pgFilterToggle" onclick="pgToggleFilter()">
                    <span id="pgFilterLabel"><?php echo e($kategoriMap[$selectedKategori] ?? 'Filter'); ?></span>
                    <svg class="chevron" width="14" height="14" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div class="pg-filter-menu" id="pgFilterMenu">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $kategoriMap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="pg-filter-item <?php echo e($selectedKategori === $value ? 'selected' : ''); ?>"
                            onclick="pgSelectFilter('<?php echo e($value); ?>', '<?php echo e($label); ?>')">
                            <svg class="pg-check" width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            <span><?php echo e($label); ?></span>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>

            <select name="per_page" class="pg-perpage"
                onchange="document.getElementById('pgFilterForm').submit()">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [9, 18, 27]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <option value="<?php echo e($opt); ?>" <?php echo e(request('per_page', 9) == $opt ? 'selected' : ''); ?>>
                        <?php echo e($opt); ?> / hal
                    </option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>

            <a href="<?php echo e(route('manajemenmahasiswa.pengumuman.create')); ?>" class="btn-buat-post">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Buat Post
            </a>
        </div>
    </form>

    
    <div class="pg-grid">
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

                $catKey   = $item->kategori ?? 'default';
                $catClass = 'pg-cat-' . ($item->kategori ?? 'default');
                $catLabel = $kategoriMap[$catKey] ?? ucfirst(str_replace('_',' ',$catKey));
                $gradient = $placeholderGradients[$catKey] ?? $placeholderGradients['default'];

                $authorName     = $item->author?->name ?? 'Kemahasiswaan';
                $authorInitials = strtoupper(substr($authorName, 0, 2));

                $canEdit   = $authUser->id === $item->user_id || $isAdminOrKoor;
                $canDelete = $authUser->id === $item->user_id || $isAdminOrKoor;

                $cardClass = 'pg-card'
                    . ($isPinnedGlobal   ? ' pinned-global'   : '')
                    . ($isPinnedPersonal ? ' pinned-personal' : '');
            ?>

            <div class="<?php echo e($cardClass); ?>"
                 data-href="<?php echo e(route('manajemenmahasiswa.pengumuman.show', $item->id)); ?>"
                 onclick="pgNavigate(event, this)">

                
                <div class="pg-card-img">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($thumbnailUrl): ?>
                        <img src="<?php echo e($thumbnailUrl); ?>" alt="<?php echo e($item->judul); ?>"
                             onclick="pgOpenLightbox(event,'<?php echo e($thumbnailUrl); ?>','<?php echo e(addslashes($item->judul)); ?>')">
                    <?php else: ?>
                        <div class="pg-card-img-placeholder" style="background:<?php echo e($gradient); ?>;">
                            <svg width="38" height="38" viewBox="0 0 24 24" fill="none"
                                stroke="#9ca3af" stroke-width="1.5">
                                <path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
                            </svg>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isPinnedGlobal): ?>
                        <span class="pg-badge-overlay pg-badge-pinned">
                            <svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5v6h2v-6h5v-2l-2-2z"/>
                            </svg>
                            Penting
                        </span>
                    <?php elseif($item->status_publish === 'pending_review'): ?>
                        <span class="pg-badge-overlay pg-badge-pending">⏳ Menunggu Verif</span>
                    <?php elseif($item->status_publish === 'draft'): ?>
                        <span class="pg-badge-overlay pg-badge-draft">Draft</span>
                    <?php elseif($item->status_publish === 'archived'): ?>
                        <span class="pg-badge-overlay pg-badge-archived">Archived</span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <div class="pg-img-actions" onclick="event.stopPropagation()">
                        
                        <form method="POST"
                            action="<?php echo e(route('manajemenmahasiswa.pengumuman.personal_pin', $item->id)); ?>"
                            style="margin:0;">
                            <?php echo csrf_field(); ?>
                            <button type="submit"
                                class="pg-icon-btn <?php echo e($isPinnedPersonal ? 'pin-active-personal' : ''); ?>"
                                title="<?php echo e($isPinnedPersonal ? 'Hapus pin pribadi' : 'Pin untuk saya'); ?>">
                                <svg width="13" height="13" viewBox="0 0 24 24"
                                    fill="<?php echo e($isPinnedPersonal ? 'currentColor' : 'none'); ?>"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/>
                                </svg>
                            </button>
                        </form>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canPinGlobal): ?>
                            <form method="POST"
                                action="<?php echo e(route('manajemenmahasiswa.pengumuman.pin', $item->id)); ?>"
                                style="margin:0;">
                                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                <button type="submit"
                                    class="pg-icon-btn <?php echo e($isPinnedGlobal ? 'pin-active-global' : ''); ?>"
                                    title="<?php echo e($isPinnedGlobal ? 'Unpin global' : 'Pin global (semua user)'); ?>">
                                    <svg width="13" height="13" viewBox="0 0 24 24"
                                        fill="<?php echo e($isPinnedGlobal ? 'currentColor' : 'none'); ?>"
                                        stroke="currentColor" stroke-width="2">
                                        <path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5v6h2v-6h5v-2l-2-2z"/>
                                    </svg>
                                </button>
                            </form>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="pg-card-body">
                    <div class="pg-category <?php echo e($catClass); ?>"><?php echo e($catLabel); ?></div>
                    <div class="pg-card-title"><?php echo e($item->judul); ?></div>
                    <p class="pg-card-excerpt">
                        <?php echo e(Str::limit(html_entity_decode(strip_tags($item->konten)), 110)); ?>

                    </p>
                    <div class="pg-card-footer">
                        <div class="pg-author">
                            <div class="pg-avatar"><?php echo e($authorInitials); ?></div>
                            <span class="pg-author-name"><?php echo e($authorName); ?></span>
                        </div>
                        <span class="pg-date">
                            <?php echo e(($item->published_at ?? $item->created_at)->translatedFormat('d M Y')); ?>

                        </span>
                    </div>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit || $canDelete): ?>
                    <div class="pg-card-actions" onclick="event.stopPropagation()">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canEdit): ?>
                            <a href="<?php echo e(route('manajemenmahasiswa.pengumuman.edit', $item->id)); ?>"
                                class="pg-action-btn" title="Edit">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.5">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                                Edit
                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canDelete): ?>
                            <form method="POST"
                                action="<?php echo e(route('manajemenmahasiswa.pengumuman.remove', $item->id)); ?>"
                                onsubmit="return confirm('Hapus pengumuman ini?')" style="margin:0;">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="pg-action-btn danger" title="Hapus">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2.5">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="pg-spacer"></div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <div class="pg-empty">
                <div class="pg-empty-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5">
                        <path d="m3 11 18-5v12L3 14v-3z"/>
                        <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
                    </svg>
                </div>
                <h5>Belum ada pengumuman</h5>
                <p>Pengumuman terbaru akan muncul di sini</p>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pengumuman->total() > 0): ?>
        <div class="mb-2">
            <span class="pg-pagination-info">
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

    
    <div class="lightbox-modal" id="pgLightbox">
        <button class="lightbox-close" onclick="pgCloseLightbox()">&times;</button>
        <div class="lightbox-content">
            <img id="pgLightboxImg" src="" alt="">
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
    function pgNavigate(event, card) {
        if (event.target.closest('form, button, a')) return;
        window.location.href = card.dataset.href;
    }
    function pgToggleFilter() {
        document.getElementById('pgFilterMenu')?.classList.toggle('show');
        document.getElementById('pgFilterToggle')?.classList.toggle('open');
    }
    function pgSelectFilter(value, label) {
        document.getElementById('pgKategoriInput').value = value;
        document.getElementById('pgFilterLabel').textContent = value === 'semua' ? 'Semua' : label;
        document.querySelectorAll('.pg-filter-item').forEach(i => i.classList.remove('selected'));
        event.currentTarget.classList.add('selected');
        pgToggleFilter();
        document.getElementById('pgFilterForm').submit();
    }
    document.addEventListener('click', e => {
        const d = document.querySelector('.pg-filter-dropdown');
        if (d && !d.contains(e.target)) {
            document.getElementById('pgFilterMenu')?.classList.remove('show');
            document.getElementById('pgFilterToggle')?.classList.remove('open');
        }
    });
    document.getElementById('pgSearchInput')?.addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); document.getElementById('pgFilterForm').submit(); }
    });
    function pgOpenLightbox(event, src) {
        event.stopPropagation();
        document.getElementById('pgLightboxImg').src = src;
        document.getElementById('pgLightbox').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function pgCloseLightbox() {
        document.getElementById('pgLightbox').classList.remove('active');
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') pgCloseLightbox(); });
    document.getElementById('pgLightbox')?.addEventListener('click', e => {
        if (e.target === document.getElementById('pgLightbox')) pgCloseLightbox();
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
<?php /**PATH C:\WebsiteTekkom - Copy\Modules\ManajemenMahasiswa\resources\views\pengumuman\pengumuman-a.blade.php ENDPATH**/ ?>