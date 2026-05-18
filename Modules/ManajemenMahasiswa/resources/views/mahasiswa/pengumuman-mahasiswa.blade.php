<x-manajemenmahasiswa::layouts.mahasiswa>

    @push('styles')
    <style>
        /* ── Page Header ─────────────────────────────── */
        .pg-header { margin-bottom: 28px; }
        .pg-header h4 {
            font-size: 1.6rem; font-weight: 800; color: #0D0D12;
            margin-bottom: 4px; letter-spacing: -.02em;
        }
        .pg-header p { font-size: .9rem; color: #808897; margin: 0; }

        /* ── Search & Filter Bar ─────────────────────── */
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

        /* ── Category Tab Filter ─────────────────────── */
        .pg-tabs {
            display: flex; gap: 0; margin-bottom: 24px;
            border-bottom: 2px solid #f3f4f6; overflow-x: auto;
            scrollbar-width: none;
        }
        .pg-tabs::-webkit-scrollbar { display: none; }
        .pg-tab {
            padding: 8px 18px; font-size: .85rem; font-weight: 600;
            color: #9ca3af; cursor: pointer; border-bottom: 2px solid transparent;
            margin-bottom: -2px; white-space: nowrap; transition: all .15s;
            background: none; border-top: none; border-left: none; border-right: none;
        }
        .pg-tab:hover { color: #6B4FF4; }
        .pg-tab.active { color: #6B4FF4; border-bottom-color: #6B4FF4; }

        /* ── Cards Grid ──────────────────────────────── */
        .pg-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 32px;
        }
        @media (max-width: 992px) { .pg-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px)  { .pg-grid { grid-template-columns: 1fr; } }

        /* ── Single Card ─────────────────────────────── */
        .pg-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            cursor: pointer;
            transition: box-shadow .25s, transform .25s;
        }
        .pg-card:hover {
            box-shadow: 0 12px 32px rgba(0,0,0,.1);
            transform: translateY(-3px);
        }
        .pg-card.pinned-global { border-top: 3px solid #d97706; }
        .pg-card.pinned-personal { border-top: 3px solid #6B4FF4; }
        .pg-card.pinned-global.pinned-personal { border-top: 3px solid #d97706; }

        /* Image area */
        .pg-card-img {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            overflow: hidden;
            background: #f3f4f6;
            flex-shrink: 0;
        }
        .pg-card-img img {
            width: 100%; height: 100%; object-fit: cover; display: block;
            transition: transform .4s ease;
        }
        .pg-card:hover .pg-card-img img { transform: scale(1.04); }

        /* Placeholder gradient when no image */
        .pg-card-img-placeholder {
            width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
        }
        .pg-card-img-placeholder svg { opacity: .3; }

        /* Pinned badge overlay */
        .pg-pinned-overlay {
            position: absolute; top: 10px; left: 10px;
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px 10px; border-radius: 50px; font-size: 10px;
            font-weight: 800; letter-spacing: .04em; backdrop-filter: blur(6px);
            text-transform: uppercase;
        }
        .pg-pinned-global  { background: rgba(255,251,235,.9); color: #d97706; }
        .pg-pinned-personal { background: rgba(245,243,255,.9); color: #6B4FF4; }

        /* Bookmark pin button */
        .pg-bookmark {
            position: absolute; top: 10px; right: 10px;
            width: 32px; height: 32px; border-radius: 50%;
            background: rgba(255,255,255,.9); backdrop-filter: blur(4px);
            border: none; cursor: pointer; display: flex; align-items: center;
            justify-content: center; transition: all .2s; color: #9ca3af;
        }
        .pg-bookmark:hover { background: #fff; color: #6B4FF4; transform: scale(1.1); }
        .pg-bookmark.active { background: #fff; color: #6B4FF4; }

        /* Card body */
        .pg-card-body { padding: 18px 20px 16px; flex: 1; display: flex; flex-direction: column; }

        /* Category text */
        .pg-category {
            font-size: .72rem; font-weight: 800; letter-spacing: .08em;
            text-transform: uppercase; margin-bottom: 8px; display: flex;
            align-items: center; gap: 6px;
        }
        .pg-cat-akademik   { color: #1A8CD8; }
        .pg-cat-himpunan   { color: #6B4FF4; }
        .pg-cat-lowongan   { color: #0D9F5F; }
        .pg-cat-event_prodi { color: #C6930A; }
        .pg-cat-default    { color: #808897; }

        /* Title */
        .pg-card-title {
            font-size: 1.05rem; font-weight: 800; color: #0D0D12;
            line-height: 1.4; margin-bottom: 10px; letter-spacing: -.01em;
            display: -webkit-box; -webkit-line-clamp: 3;
            -webkit-box-orient: vertical; overflow: hidden;
        }

        /* Excerpt */
        .pg-card-excerpt {
            font-size: .83rem; color: #6b7280; line-height: 1.6;
            margin-bottom: 14px; flex: 1;
            display: -webkit-box; -webkit-line-clamp: 2;
            -webkit-box-orient: vertical; overflow: hidden;
        }

        /* Footer: author + date */
        .pg-card-footer {
            display: flex; align-items: center; justify-content: space-between;
            gap: 8px; padding-top: 14px; border-top: 1px solid #f3f4f6;
            margin-top: auto;
        }
        .pg-author { display: flex; align-items: center; gap: 8px; min-width: 0; }
        .pg-avatar {
            width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0;
            background: linear-gradient(135deg, #6B4FF4, #8266F5);
            color: #fff; font-size: 10px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
        }
        .pg-author-name {
            font-size: .78rem; font-weight: 600; color: #374151;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .pg-date { font-size: .75rem; color: #9ca3af; white-space: nowrap; flex-shrink: 0; }

        /* ── Empty State ─────────────────────────────── */
        .pg-empty {
            grid-column: 1 / -1; padding: 72px 20px;
            text-align: center; color: #9ca3af;
        }
        .pg-empty-icon {
            width: 72px; height: 72px; border-radius: 50%;
            background: #f5f3ff; display: flex; align-items: center;
            justify-content: center; margin: 0 auto 16px; color: #6B4FF4;
        }
        .pg-empty h5 { font-size: 1rem; font-weight: 700; color: #374151; margin-bottom: 4px; }
        .pg-empty p  { font-size: .88rem; }

        /* ── Pagination ──────────────────────────────── */
        .pagination .page-link {
            color: #6B4FF4; border-color: #e5e7eb; border-radius: 8px;
            margin: 0 2px; font-size: .875rem; font-weight: 500;
            padding: 7px 13px; transition: all .2s;
        }
        .pagination .page-link:hover { background: #f5f3ff; border-color: #6B4FF4; }
        .pagination .page-item.active .page-link { background: #6B4FF4; border-color: #6B4FF4; color: #fff; }
        .pagination .page-item.disabled .page-link { color: #d1d5db; border-color: #e5e7eb; }
        .pg-pagination-info { font-size: .82rem; color: #9ca3af; font-weight: 500; }

        /* ── Lightbox ────────────────────────────────── */
        .lightbox-modal {
            display: none; position: fixed; inset: 0; z-index: 10000;
            background: rgba(0,0,0,.92); align-items: center; justify-content: center;
        }
        .lightbox-modal.active { display: flex; }
        .lightbox-content { position: relative; max-width: 90vw; max-height: 85vh; }
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
    @endpush

    @php
        $kategoriMap = [
            'semua'       => 'Semua',
            'akademik'    => 'Akademik',
            'himpunan'    => 'Himpunan',
            'lowongan'    => 'Lowongan',
            'event_prodi' => 'Event Prodi',
        ];
        $selectedKategori = request('kategori', 'semua');

        $placeholderGradients = [
            'akademik'    => 'linear-gradient(135deg, #E8F4FF 0%, #BFDBFE 100%)',
            'himpunan'    => 'linear-gradient(135deg, #F5F3FF 0%, #DDD6FE 100%)',
            'lowongan'    => 'linear-gradient(135deg, #ECFDF5 0%, #A7F3D0 100%)',
            'event_prodi' => 'linear-gradient(135deg, #FFFBEB 0%, #FDE68A 100%)',
            'default'     => 'linear-gradient(135deg, #F8FAFC 0%, #E2E8F0 100%)',
        ];
    @endphp

    {{-- ── Page Header ────────────────────────────── --}}
    <div class="pg-header">
        <h4>Pengumuman & Informasi</h4>
        <p>Wadah informasi terbaru untuk mahasiswa dan alumni</p>
    </div>

    {{-- ── Toolbar: Search + Filter + Per Page ────── --}}
    <form id="pgFilterForm" method="GET" action="{{ route('manajemenmahasiswa.pengumuman.index') }}">
        <div class="pg-toolbar">
            <div class="pg-search-wrap">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
                <input type="text" name="search" id="pgSearchInput"
                    placeholder="Cari pengumuman..." value="{{ request('search') }}">
            </div>

            <div class="pg-filter-dropdown">
                <input type="hidden" name="kategori" id="pgKategoriInput" value="{{ $selectedKategori }}">
                <button type="button" class="pg-filter-btn {{ $selectedKategori !== 'semua' ? 'active' : '' }}"
                    id="pgFilterToggle" onclick="pgToggleFilter()">
                    <span id="pgFilterLabel">{{ $kategoriMap[$selectedKategori] ?? 'Filter' }}</span>
                    <svg class="chevron" width="14" height="14" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div class="pg-filter-menu" id="pgFilterMenu">
                    @foreach($kategoriMap as $value => $label)
                        <div class="pg-filter-item {{ $selectedKategori === $value ? 'selected' : '' }}"
                            onclick="pgSelectFilter('{{ $value }}', '{{ $label }}')">
                            <svg class="pg-check" width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <select name="per_page" class="pg-perpage"
                onchange="document.getElementById('pgFilterForm').submit()">
                @foreach([9, 18, 27] as $opt)
                    <option value="{{ $opt }}" {{ request('per_page', 9) == $opt ? 'selected' : '' }}>
                        {{ $opt }} / hal
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- ── Cards Grid ──────────────────────────────── --}}
    <div class="pg-grid">
        @forelse($pengumuman as $item)
            @php
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

                $cardClass = 'pg-card'
                    . ($isPinnedGlobal   ? ' pinned-global'   : '')
                    . ($isPinnedPersonal ? ' pinned-personal' : '');
            @endphp

            <div class="{{ $cardClass }}"
                 data-href="{{ route('manajemenmahasiswa.pengumuman.show', $item->id) }}"
                 onclick="pgNavigate(event, this)">

                {{-- Image ----------------------------------------- --}}
                <div class="pg-card-img">
                    @if($thumbnailUrl)
                        <img src="{{ $thumbnailUrl }}" alt="{{ $item->judul }}"
                             onclick="pgOpenLightbox(event,'{{ $thumbnailUrl }}','{{ addslashes($item->judul) }}')">
                    @else
                        <div class="pg-card-img-placeholder" style="background: {{ $gradient }};">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                stroke="#9ca3af" stroke-width="1.5">
                                <path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
                            </svg>
                        </div>
                    @endif

                    {{-- Pinned overlay badges --}}
                    @if($isPinnedGlobal)
                        <span class="pg-pinned-overlay pg-pinned-global">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5v6h2v-6h5v-2l-2-2z"/>
                            </svg>
                            Penting
                        </span>
                    @elseif($isPinnedPersonal)
                        <span class="pg-pinned-overlay pg-pinned-personal">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/>
                            </svg>
                            Pin Saya
                        </span>
                    @endif

                    {{-- Personal pin bookmark button --}}
                    <form method="POST"
                        action="{{ route('manajemenmahasiswa.pengumuman.personal_pin', $item->id) }}"
                        onclick="event.stopPropagation()" style="margin:0;">
                        @csrf
                        <button type="submit"
                            class="pg-bookmark {{ $isPinnedPersonal ? 'active' : '' }}"
                            title="{{ $isPinnedPersonal ? 'Hapus pin' : 'Pin pengumuman ini' }}">
                            <svg width="14" height="14" viewBox="0 0 24 24"
                                fill="{{ $isPinnedPersonal ? 'currentColor' : 'none' }}"
                                stroke="currentColor" stroke-width="2">
                                <path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/>
                            </svg>
                        </button>
                    </form>
                </div>

                {{-- Body ------------------------------------------ --}}
                <div class="pg-card-body">
                    {{-- Category --}}
                    <div class="pg-category {{ $catClass }}">
                        @if($item->kategori)
                            <span>{{ $catLabel }}</span>
                        @else
                            <span class="pg-cat-default">Umum</span>
                        @endif
                    </div>

                    {{-- Title --}}
                    <div class="pg-card-title">{{ $item->judul }}</div>

                    {{-- Excerpt --}}
                    <p class="pg-card-excerpt">
                        {{ Str::limit(html_entity_decode(strip_tags($item->konten)), 120) }}
                    </p>

                    {{-- Footer: author + date --}}
                    <div class="pg-card-footer">
                        <div class="pg-author">
                            <div class="pg-avatar">{{ $authorInitials }}</div>
                            <span class="pg-author-name">{{ $authorName }}</span>
                        </div>
                        <span class="pg-date">
                            {{ ($item->published_at ?? $item->created_at)->translatedFormat('d M Y') }}
                        </span>
                    </div>
                </div>
            </div>

        @empty
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
        @endforelse
    </div>

    {{-- ── Pagination ──────────────────────────────── --}}
    @if($pengumuman->total() > 0)
        <div class="mb-2">
            <span class="pg-pagination-info">
                Menampilkan {{ $pengumuman->firstItem() }}–{{ $pengumuman->lastItem() }}
                dari {{ $pengumuman->total() }} pengumuman
            </span>
        </div>
    @endif
    @if($pengumuman->hasPages())
        <div class="d-flex justify-content-center mt-2 mb-4">
            {{ $pengumuman->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    @endif

    {{-- ── Lightbox ─────────────────────────────────── --}}
    <div class="lightbox-modal" id="pgLightbox">
        <button class="lightbox-close" onclick="pgCloseLightbox()">&times;</button>
        <div class="lightbox-content">
            <img id="pgLightboxImg" src="" alt="">
        </div>
    </div>

    @push('scripts')
    <script>
    // ── Navigation ──────────────────────────────────────────────────────────────
    function pgNavigate(event, card) {
        if (event.target.closest('form, button, a')) return;
        window.location.href = card.dataset.href;
    }

    // ── Filter dropdown ─────────────────────────────────────────────────────────
    function pgToggleFilter() {
        const menu = document.getElementById('pgFilterMenu');
        const btn  = document.getElementById('pgFilterToggle');
        menu.classList.toggle('show');
        btn.classList.toggle('open');
    }
    function pgSelectFilter(value, label) {
        document.getElementById('pgKategoriInput').value = value;
        document.getElementById('pgFilterLabel').textContent = value === 'semua' ? 'Filter' : label;
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

    // ── Search on Enter ─────────────────────────────────────────────────────────
    document.getElementById('pgSearchInput')?.addEventListener('keydown', e => {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('pgFilterForm').submit();
        }
    });

    // ── Lightbox ────────────────────────────────────────────────────────────────
    function pgOpenLightbox(event, src, title) {
        event.stopPropagation();
        document.getElementById('pgLightboxImg').src = src;
        document.getElementById('pgLightbox').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function pgCloseLightbox() {
        document.getElementById('pgLightbox').classList.remove('active');
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') pgCloseLightbox();
    });
    document.getElementById('pgLightbox')?.addEventListener('click', e => {
        if (e.target === document.getElementById('pgLightbox')) pgCloseLightbox();
    });
    </script>
    @endpush

</x-manajemenmahasiswa::layouts.mahasiswa>
