<x-manajemenmahasiswa::layouts.admin>

    @push('styles')
    <style>
        /* Token desain global SITKOM — disamakan dengan dashboard Super Admin
           (resources/views/components/sidebar.blade.php). Layout modul ini tidak
           mendefinisikan token tersebut, jadi harus dideklarasikan ulang di sini. */
        :root {
            --c-primary: #0B266E;
            --c-primary-hover: #091958;
            --c-primary-subtle: rgba(11, 38, 110, 0.08);
            --c-primary-border: #5C78B8;
            --c-bg: #F6F8FA;
            --c-fg: #0D0D12;
            --c-fg-sec: #353849;
            --c-fg-muted: #666D80;
            --c-fg-placeholder: #808897;
            --c-border: #DFE1E7;
            --c-border-strong: #C1C7CF;
            --c-success: #287F6E;
            --c-success-subtle: #DDF2EE;
            --c-error: #DF1C41;
            --c-error-subtle: #FADAE1;
            --c-warning: #956321;
            --c-warning-subtle: #F9ECCB;
            --c-sky: #0C4D6E;
            --c-sky-subtle: #D1F0F9;
            --shadow-card: 0px 1px 2px 0px rgba(228, 229, 231, 0.5);
        }

        /* Halaman ini menggambar kotak kontennya sendiri (.dash-wrap/.dash-box),
           jadi kotak bawaan .main-wrapper dari layout dimatikan. */
        .main-wrapper {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: visible !important;
        }

        /* ── Shell kotak: mengikuti dashboard Super Admin ───────────── */
        .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }
        .dash-wrap { display: flex; flex-direction: column; height: calc(100vh - 60px); padding: 10px; box-sizing: border-box; font-family: 'Inter Tight', sans-serif; }
        .dash-box { display: flex; flex-direction: column; flex: 1; min-height: 0; background: #fff; border: 1px solid var(--c-border, #DFE1E7); border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06); overflow: hidden; width: 100%; box-sizing: border-box; }
        .dash-box-header { background: #fff; border-bottom: 1px solid var(--c-border, #DFE1E7); flex-shrink: 0; width: 100%; box-sizing: border-box; padding: 16px 24px; }
        .dash-box-body { flex: 1; overflow-y: auto; padding: 20px 24px; }
        .dash-box-body::-webkit-scrollbar { width: 6px; }
        .dash-box-body::-webkit-scrollbar-thumb { background: var(--c-border-strong, #C1C7CF); border-radius: 10px; }
        @media (max-width: 767px) {
            .sitkom-content { padding: 8px 8px 80px !important; display: block !important; overflow: visible !important; }
            .dash-wrap { height: auto !important; min-height: 0 !important; padding: 0; }
            .dash-box { flex: none !important; min-height: 0 !important; overflow: visible !important; border-radius: 10px; }
            .dash-box-header { padding: 12px 14px; position: sticky; top: 52px; z-index: 10; }
            .dash-box-body { overflow-y: visible !important; flex: none !important; padding: 14px; }
        }

        /* ── Section label ──────────────────────────────────────────── */
        .pg-section-header { display:flex; align-items:center; gap:8px; margin-bottom:8px; }
        .pg-section-header::before { content:''; display:inline-block; width:3px; height:14px; border-radius:2px; background:var(--c-primary, #0B266E); }
        .pg-section-label { font-size:14px; font-weight:700; color:var(--c-fg, #0D0D12); }

        /* ── Toolbar ────────────────────────────────────────────────── */
        .search-filter-bar { display:flex; gap:8px; align-items:center; flex-wrap:wrap; margin-bottom:10px; }
        .search-input-wrapper { flex:1; min-width:200px; position:relative; }
        .search-input-wrapper input {
            width:100%; padding:8px 14px 8px 36px;
            border:1px solid var(--c-border, #DFE1E7); border-radius:8px; background:#fff;
            font-size:12px; color:var(--c-fg-sec, #353849); outline:none;
            transition:border-color .15s, box-shadow .15s;
        }
        .search-input-wrapper input::placeholder { color:var(--c-fg-placeholder, #808897); }
        .search-input-wrapper input:focus { border-color:var(--c-primary, #0B266E); box-shadow:0 0 0 3px rgba(11,38,110,.1); }
        .search-input-wrapper .search-icon {
            position:absolute; left:12px; top:50%; transform:translateY(-50%);
            color:var(--c-fg-placeholder, #808897); pointer-events:none;
        }


        .btn-buat-post {
            display:inline-flex; align-items:center; gap:6px; padding:8px 16px;
            background:var(--c-primary, #0B266E); border:1px solid var(--c-primary, #0B266E);
            color:#fff; border-radius:8px;
            font-size:12px; font-weight:600; text-decoration:none; cursor:pointer;
            transition:all .15s; white-space:nowrap; flex-shrink:0;
            box-shadow:0 2px 6px rgba(11,38,110,0.3);
        }
        .btn-buat-post:hover { background:var(--c-primary-hover, #091958); color:#fff; box-shadow:0 4px 12px rgba(11,38,110,0.4); }

        /* ── Kartu list ─────────────────────────────────────────────── */
        .pengumuman-list { display:flex; flex-direction:column; gap:10px; margin-bottom:10px; }
        .pengumuman-card {
            background:#fff; border:1px solid var(--c-border, #DFE1E7); border-radius:12px;
            padding:12px 14px; cursor:pointer; display:block;
            box-shadow:var(--shadow-card, 0px 1px 2px 0px rgba(228,229,231,0.5));
            transition:border-color .15s, box-shadow .15s;
        }
        .pengumuman-card:hover {
            border-color:var(--c-primary-border, #5C78B8);
            box-shadow:0 4px 14px rgba(11,38,110,0.07);
        }

        .pin-badge {
            display:inline-flex; align-items:center; gap:4px; padding:4px 8px;
            border-radius:8px; font-size:11px; font-weight:700; letter-spacing:.02em;
        }
        .pin-badge-global   { background:var(--c-warning-subtle, #F9ECCB); color:var(--c-warning, #956321); }
        .pin-badge-personal { background:var(--c-primary-subtle, #EEF1F8); color:var(--c-primary, #0B266E); }

        /* ── Isi kartu ──────────────────────────────────────────────── */
        .pengumuman-card-body { display:flex; align-items:stretch; gap:14px; }
        .pengumuman-thumbnail {
            width:72px; aspect-ratio:1080/1320; border-radius:8px; overflow:hidden;
            flex-shrink:0; background:var(--c-bg, #F6F8FA); border:1px solid var(--c-border, #DFE1E7);
            display:flex; align-items:center; justify-content:center;
        }
        .pengumuman-thumbnail img { width:100%; height:100%; object-fit:cover; display:block; }
        .pengumuman-thumbnail .no-image { width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:var(--c-border-strong, #C1C7CF); }

        .pengumuman-card-content { flex:1; min-width:0; display:flex; flex-direction:column; justify-content:center; gap:6px; }
        .pengumuman-card-title { display:flex; align-items:center; gap:8px; }
        .pengumuman-card-title .megaphone-icon { flex-shrink:0; color:var(--c-primary, #0B266E); }
        .pengumuman-card-title h6 { font-size:14px; font-weight:700; color:var(--c-fg, #0D0D12); margin:0; line-height:1.3; }
        .pengumuman-card-desc {
            font-size:11px; color:var(--c-fg-muted, #666D80); line-height:1.5; margin:0;
            display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
        }
        .pengumuman-card-tags { display:flex; flex-wrap:wrap; gap:6px; align-items:center; }
        .pengumuman-card-date { font-size:11px; color:var(--c-fg-muted, #666D80); font-weight:500; font-variant-numeric:tabular-nums; }

        .pengumuman-card-badge {
            display:inline-flex; align-items:center; padding:4px 9px;
            border-radius:8px; font-size:11px; font-weight:700; letter-spacing:.02em;
        }
        .badge-akademik       { background:var(--c-sky-subtle, #D1F0F9);     color:var(--c-sky, #0C4D6E); }
        .badge-himpunan       { background:var(--c-primary-subtle, #EEF1F8); color:var(--c-primary, #0B266E); }
        .badge-lowongan       { background:var(--c-success-subtle, #DDF2EE); color:var(--c-success, #287F6E); }
        .badge-event_prodi    { background:var(--c-warning-subtle, #F9ECCB); color:var(--c-warning, #956321); }
        .badge-umum           { background:var(--c-bg, #F6F8FA);             color:var(--c-fg-muted, #666D80); }
        .badge-draft          { background:var(--c-bg, #F6F8FA);             color:var(--c-fg-muted, #666D80); }
        .badge-published      { background:var(--c-success-subtle, #DDF2EE); color:var(--c-success, #287F6E); }
        .badge-archived       { background:var(--c-bg, #F6F8FA);             color:var(--c-fg-muted, #666D80); }
        .badge-pending_review { background:var(--c-warning-subtle, #F9ECCB); color:var(--c-warning, #956321); }

        .pengumuman-card-action {
            display:flex; flex-direction:column; align-items:flex-end;
            justify-content:center; padding-left:10px; flex-shrink:0;
        }

        /* ── Pagination ─────────────────────────────────────────────── */
        .pagination .page-link {
            color:var(--c-primary, #0B266E); border-color:var(--c-border, #DFE1E7); border-radius:8px;
            margin:0 2px; font-size:12px; font-weight:600; padding:7px 13px; transition:all .15s;
        }
        .pagination .page-link:hover { background:var(--c-primary-subtle, #EEF1F8); border-color:var(--c-primary-border, #5C78B8); }
        .pagination .page-item.active .page-link { background:var(--c-primary, #0B266E); border-color:var(--c-primary, #0B266E); color:#fff; }
        .pagination .page-item.disabled .page-link { color:var(--c-border-strong, #C1C7CF); border-color:var(--c-border, #DFE1E7); }
        .pagination-info-text { font-size:12px; color:var(--c-fg-muted, #666D80); font-weight:500; }

        /* ── Empty state ────────────────────────────────────────────── */
        .pengumuman-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:44px 20px; text-align:center; }
        .pengumuman-empty .empty-icon {
            width:64px; height:64px; border-radius:50%;
            background:var(--c-primary-subtle, #EEF1F8); color:var(--c-primary, #0B266E);
            display:flex; align-items:center; justify-content:center; margin-bottom:10px;
        }
        .pengumuman-empty h5 { font-size:14px; font-weight:700; color:var(--c-fg, #0D0D12); margin-bottom:4px; }
        .pengumuman-empty p  { font-size:12px; color:var(--c-fg-muted, #666D80); margin:0; }

        /* ── Lightbox ───────────────────────────────────────────────── */
        .zoomable-thumbnail { cursor:zoom-in; position:relative; }
        .zoomable-thumbnail::after {
            content:''; position:absolute; inset:0;
            background:rgba(13,13,18,.2); opacity:0; transition:opacity .2s;
        }
        .zoomable-thumbnail:hover::after { opacity:1; }
        .lightbox-modal {
            display:none; position:fixed; inset:0; z-index:10000;
            background:rgba(13,13,18,.92); align-items:center; justify-content:center;
        }
        .lightbox-modal.active { display:flex; }
        .lightbox-content img {
            max-width:90vw; max-height:82vh; object-fit:contain;
            border-radius:8px; box-shadow:0 25px 60px rgba(0,0,0,.4);
        }
        .lightbox-close {
            position:fixed; top:20px; right:24px; width:40px; height:40px;
            border-radius:50%; background:rgba(255,255,255,.12);
            border:1px solid rgba(255,255,255,.2); color:#fff;
            cursor:pointer; display:flex; align-items:center; justify-content:center;
            transition:background .15s; z-index:10001;
        }
        .lightbox-close:hover { background:rgba(255,255,255,.22); }
    </style>
    @endpush

    @php
        $authUser      = Auth::user();
        $canPinGlobal  = $authUser->hasAnyRole(['superadmin','admin','admin_kemahasiswaan']);
        $isAdminOrKoor = $authUser->roles->pluck('name')->intersect(['superadmin','admin','dosen_koordinator'])->isNotEmpty();

        // Daftar labelnya sekarang dipegang partial _filter-kategori.
        $selectedKategori = request('kategori','semua');
    @endphp

    <div class="dash-wrap">
        <div class="dash-box">

            {{-- ── Header ─────────────────────────────────── --}}
            <div class="dash-box-header">
                <x-manajemenmahasiswa::ui.page-header
                    title="Pengumuman & Informasi"
                    badge="Modul Mahasiswa"
                    subtitle="Wadah informasi untuk mahasiswa dan alumni">
                    <x-slot:actions>
                        <a href="{{ route('manajemenmahasiswa.pengumuman.create') }}" class="mk-btn mk-btn--primary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            <span>Buat Post</span>
                        </a>
                    </x-slot:actions>
                </x-manajemenmahasiswa::ui.page-header>
            </div>

            <div class="dash-box-body">

                {{-- ── Toolbar ──────────────────────────────── --}}
                <form id="pengumumanFilterForm" method="GET" action="{{ route('manajemenmahasiswa.pengumuman.index') }}">
                    <div class="search-filter-bar">
                        <div class="search-input-wrapper">
                            <svg class="search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                            </svg>
                            <input type="text" name="search" id="searchInput" placeholder="Cari pengumuman..."
                                value="{{ request('search') }}">
                        </div>

                        <x-manajemenmahasiswa::ui.select name="per_page" size="md" :block="false"
                            onchange="document.getElementById('pengumumanFilterForm').submit()">
                            @foreach([5,10,20,50] as $opt)
                                <option value="{{ $opt }}" {{ request('per_page',10) == $opt ? 'selected' : '' }}>
                                    {{ $opt }} / hal
                                </option>
                            @endforeach
                        </x-manajemenmahasiswa::ui.select>
                    </div>

                    @include('manajemenmahasiswa::pengumuman._filter-kategori', [
                        'selectedKategori' => $selectedKategori,
                        'formId'           => 'pengumumanFilterForm',
                    ])
                </form>

                <div class="pg-section-header">
                    <span class="pg-section-label">Daftar Pengumuman</span>
                </div>

                {{-- ── List ─────────────────────────────────── --}}
                <div class="pengumuman-list">
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
                            $canEdit   = $authUser->id === $item->user_id || $isAdminOrKoor;
                            $canDelete = $authUser->id === $item->user_id || $isAdminOrKoor;
                        @endphp
                        <div class="pengumuman-card"
                             data-href="{{ route('manajemenmahasiswa.pengumuman.show', $item->id) }}"
                             onclick="navigatePengumuman(event, this)">
                            <div class="pengumuman-card-body">
                                {{-- Thumbnail (klik untuk lihat gambar penuh) --}}
                                <div class="pengumuman-thumbnail {{ $thumbnailUrl ? 'zoomable-thumbnail' : '' }}"
                                     @if($thumbnailUrl) onclick="openLightbox(event,'{{ $thumbnailUrl }}','{{ addslashes($item->judul) }}')" @endif>
                                    @if($thumbnailUrl)
                                        <img src="{{ $thumbnailUrl }}" alt="Thumbnail">
                                    @else
                                        <div class="no-image">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
                                                <polyline points="21 15 16 10 5 21"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                {{-- Content --}}
                                <div class="pengumuman-card-content">
                                    <div class="pengumuman-card-title">
                                        <svg class="megaphone-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
                                        </svg>
                                        <h6>{{ $item->judul }}</h6>
                                    </div>
                                    <p class="pengumuman-card-desc">
                                        {{ Str::limit(html_entity_decode(strip_tags($item->konten)), 150) }}
                                    </p>
                                    <div class="pengumuman-card-tags">
                                        @if($isPinnedGlobal)
                                            <span class="pin-badge pin-badge-global">
                                                <svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5v6h2v-6h5v-2l-2-2z"/></svg>
                                                Pinned
                                            </span>
                                        @endif
                                        @if($isPinnedPersonal)
                                            <span class="pin-badge pin-badge-personal">
                                                <svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
                                                Pin Pribadi
                                            </span>
                                        @endif
                                        @if($item->status_publish && $item->status_publish !== 'published')
                                            <span class="pengumuman-card-badge badge-{{ $item->status_publish }}">
                                                @if($item->status_publish === 'pending_review') Menunggu Verifikasi
                                                @else {{ ucfirst($item->status_publish) }}
                                                @endif
                                            </span>
                                        @endif
                                        @if($item->kategori)
                                            <span class="pengumuman-card-badge badge-{{ $item->kategori }}">
                                                {{ ucfirst(str_replace('_',' ',$item->kategori)) }}
                                            </span>
                                        @endif
                                        <span class="pengumuman-card-date">
                                            {{ $item->created_at->translatedFormat('d F Y') }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Aksi: satu tombol "..." + dropdown, mengikuti pola kolom Aksi
                                     Direktori Alumni/Mahasiswa. Deretan tombol ikon sebelumnya
                                     diganti supaya baris daftar tetap lapang saat aksinya bertambah. --}}
                                <div class="pengumuman-card-action" onclick="event.stopPropagation()">
                                    <div style="position: relative; display: inline-block;" x-data="{ open: false }">
                                        <button type="button" class="mk-btn mk-btn--secondary mk-btn--icon mk-btn--sm"
                                            @click="open = !open" @click.outside="open = false"
                                            :aria-expanded="open" aria-haspopup="menu" title="Aksi lainnya">
                                            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24">
                                                <circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/>
                                            </svg>
                                        </button>

                                        <div class="mk-menu" role="menu" x-show="open" x-cloak style="display: none;"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100">

                                            @if($canEdit)
                                                <a href="{{ route('manajemenmahasiswa.pengumuman.edit', $item->id) }}"
                                                    class="mk-menu-item" role="menuitem">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                    </svg>
                                                    Edit
                                                </a>
                                            @endif

                                            <form method="POST" action="{{ route('manajemenmahasiswa.pengumuman.personal_pin', $item->id) }}">
                                                @csrf
                                                <button type="submit" role="menuitem"
                                                    class="mk-menu-item {{ $isPinnedPersonal ? 'is-active' : '' }}">
                                                    <svg width="14" height="14" viewBox="0 0 24 24"
                                                        fill="{{ $isPinnedPersonal ? 'currentColor' : 'none' }}"
                                                        stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/>
                                                    </svg>
                                                    {{ $isPinnedPersonal ? 'Lepas Pin Pribadi' : 'Pin Pribadi' }}
                                                </button>
                                            </form>

                                            @if($canPinGlobal)
                                                <form method="POST" action="{{ route('manajemenmahasiswa.pengumuman.pin', $item->id) }}">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" role="menuitem"
                                                        class="mk-menu-item {{ $isPinnedGlobal ? 'is-active' : '' }}">
                                                        <svg width="14" height="14" viewBox="0 0 24 24"
                                                            fill="{{ $isPinnedGlobal ? 'currentColor' : 'none' }}"
                                                            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5v6h2v-6h5v-2l-2-2z"/>
                                                        </svg>
                                                        {{ $isPinnedGlobal ? 'Lepas Pin Global' : 'Pin Global' }}
                                                    </button>
                                                </form>
                                            @endif

                                            @if($canDelete)
                                                <div class="mk-menu-sep"></div>
                                                <form method="POST" action="{{ route('manajemenmahasiswa.pengumuman.remove', $item->id) }}"
                                                    onsubmit="return mkConfirmSubmit(this, 'Hapus pengumuman ini?', { title: 'Hapus Pengumuman', confirmText: 'Ya, Hapus' })">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="mk-menu-item" role="menuitem">
                                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="3 6 5 6 21 6"/>
                                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="pengumuman-empty">
                            <div class="empty-icon">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
                                </svg>
                            </div>
                            <h5>Belum ada pengumuman</h5>
                            <p>Pengumuman terbaru akan muncul di sini</p>
                        </div>
                    @endforelse
                </div>

                {{-- ── Pagination ───────────────────────────── --}}
                @if($pengumuman->total() > 0)
                    <div class="mb-2">
                        <span class="pagination-info-text">
                            Menampilkan {{ $pengumuman->firstItem() }}–{{ $pengumuman->lastItem() }}
                            dari {{ $pengumuman->total() }} pengumuman
                        </span>
                    </div>
                @endif
                @if($pengumuman->hasPages())
                    <div class="d-flex justify-content-center mt-2 mb-2">
                        {{ $pengumuman->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                @endif

            </div> <!-- end dash-box-body -->
        </div> <!-- end dash-box -->
    </div> <!-- end dash-wrap -->

    {{-- Lightbox Modal --}}
    <div class="lightbox-modal" id="lightboxModal">
        <button class="lightbox-close" onclick="closeLightbox()" title="Tutup">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M18 6L6 18M6 6l12 12"/>
            </svg>
        </button>
        <div class="lightbox-content">
            <img id="lightboxImage" src="" alt="">
        </div>
    </div>

    @push('scripts')
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
    document.getElementById('searchInput')?.addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); document.getElementById('pengumumanFilterForm').submit(); }
    });
    </script>
    @endpush

</x-manajemenmahasiswa::layouts.admin>
