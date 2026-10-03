<x-manajemenmahasiswa::layouts.mahasiswa>

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
        .dash-box-body { flex: 1; min-height: 0; display: flex; flex-direction: column; overflow-y: auto; padding: 20px 24px; }
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
        .pg-toolbar { display:flex; gap:8px; align-items:center; margin-bottom:10px; flex-wrap:wrap; }
        .pg-search-wrap { flex:1; min-width:200px; position:relative; }
        .pg-search-wrap svg { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--c-fg-placeholder, #808897); pointer-events:none; }
        .pg-search-wrap input {
            width:100%; padding:8px 14px 8px 36px;
            border:1px solid var(--c-border, #DFE1E7); border-radius:8px;
            background:#fff; font-size:12px; color:var(--c-fg-sec, #353849); outline:none;
            transition:border-color .15s, box-shadow .15s;
        }
        .pg-search-wrap input::placeholder { color:var(--c-fg-placeholder, #808897); }
        .pg-search-wrap input:focus { border-color:var(--c-primary, #0B266E); box-shadow:0 0 0 3px rgba(11,38,110,.1); }


        /* ── Cards grid ─────────────────────────────────────────────── */
        .pg-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:10px; }
        @media (max-width: 1280px) { .pg-grid { grid-template-columns:repeat(2,1fr); } }
        @media (max-width: 640px)  { .pg-grid { grid-template-columns:1fr; } }

        .pg-card {
            background:#fff; border:1px solid var(--c-border, #DFE1E7); border-radius:14px;
            overflow:hidden; display:flex; flex-direction:column; cursor:pointer;
            box-shadow:var(--shadow-card, 0px 1px 2px 0px rgba(228,229,231,0.5));
            transition:border-color .15s, box-shadow .15s;
        }
        .pg-card:hover { border-color:var(--c-primary-border, #5C78B8); box-shadow:0 4px 14px rgba(11,38,110,0.07); }

        /* Image */
        .pg-card-img { position:relative; width:100%; aspect-ratio:16/9; overflow:hidden; background:var(--c-bg, #F6F8FA); flex-shrink:0; }
        .pg-card-img img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .4s ease; }
        .pg-card:hover .pg-card-img img { transform:scale(1.03); }
        .pg-card-img-placeholder { width:100%; height:100%; display:flex; align-items:center; justify-content:center; }

        /* Overlay badges */
        .pg-pinned-overlay {
            position:absolute; top:10px; left:10px;
            display:inline-flex; align-items:center; gap:4px;
            padding:4px 9px; border-radius:8px; font-size:11px; font-weight:700; letter-spacing:.02em;
            backdrop-filter:blur(6px);
        }
        .pg-pinned-global   { background:var(--c-warning-subtle, #F9ECCB); color:var(--c-warning, #956321); }
        .pg-pinned-personal { background:var(--c-primary-subtle, #EEF1F8); color:var(--c-primary, #0B266E); }

        /* Bookmark pin button */
        .pg-bookmark {
            position:absolute; top:10px; right:10px;
            width:28px; height:28px; border-radius:8px;
            background:rgba(255,255,255,.92); backdrop-filter:blur(4px);
            border:1px solid var(--c-border, #DFE1E7); cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            transition:all .15s; color:var(--c-fg-muted, #666D80);
        }
        .pg-bookmark:hover { background:#fff; border-color:var(--c-primary-border, #5C78B8); color:var(--c-primary, #0B266E); }
        .pg-bookmark.active { background:#fff; border-color:var(--c-primary-border, #5C78B8); color:var(--c-primary, #0B266E); }

        /* Card body */
        .pg-card-body { padding:14px; flex:1; display:flex; flex-direction:column; gap:8px; }

        .pg-category {
            display:inline-flex; align-items:center; align-self:flex-start;
            padding:5px 9px; border-radius:8px;
            font-size:11px; font-weight:700; letter-spacing:.02em;
        }
        .pg-cat-akademik    { background:var(--c-sky-subtle, #D1F0F9);     color:var(--c-sky, #0C4D6E); }
        .pg-cat-himpunan    { background:var(--c-primary-subtle, #EEF1F8); color:var(--c-primary, #0B266E); }
        .pg-cat-lowongan    { background:var(--c-success-subtle, #DDF2EE); color:var(--c-success, #287F6E); }
        .pg-cat-event_prodi { background:var(--c-warning-subtle, #F9ECCB); color:var(--c-warning, #956321); }
        .pg-cat-default     { background:var(--c-bg, #F6F8FA);             color:var(--c-fg-muted, #666D80); }

        .pg-card-title {
            font-size:14px; font-weight:700; color:var(--c-fg, #0D0D12); line-height:1.3;
            display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;
        }
        .pg-card-excerpt {
            font-size:11px; color:var(--c-fg-muted, #666D80); line-height:1.5; margin:0; flex:1;
            display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
        }

        .pg-card-footer {
            display:flex; align-items:center; justify-content:space-between; gap:8px;
            padding-top:10px; border-top:1px solid var(--c-border, #DFE1E7); margin-top:auto;
        }
        .pg-author { display:flex; align-items:center; gap:7px; min-width:0; }
        .pg-avatar {
            width:26px; height:26px; border-radius:50%; flex-shrink:0;
            background:var(--c-primary-subtle, #EEF1F8); color:var(--c-primary, #0B266E);
            font-size:10px; font-weight:700; display:flex; align-items:center; justify-content:center;
        }
        .pg-author-name { font-size:12px; font-weight:600; color:var(--c-fg-sec, #353849); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .pg-date { font-size:11px; color:var(--c-fg-muted, #666D80); white-space:nowrap; flex-shrink:0; font-variant-numeric:tabular-nums; }

        /* ── Empty state ────────────────────────────────────────────── */
        .pg-empty { grid-column:1/-1; padding:44px 20px; text-align:center; color:var(--c-fg-muted, #666D80); }
        .pg-empty-icon {
            width:64px; height:64px; border-radius:50%;
            background:var(--c-primary-subtle, #EEF1F8); color:var(--c-primary, #0B266E);
            display:flex; align-items:center; justify-content:center; margin:0 auto 14px;
        }
        .pg-empty h5 { font-size:14px; font-weight:700; color:var(--c-fg, #0D0D12); margin-bottom:4px; }
        .pg-empty p  { font-size:12px; margin:0; }

        /* ── Pagination ─────────────────────────────────────────────── */
        .pagination .page-link {
            color:var(--c-primary, #0B266E); border-color:var(--c-border, #DFE1E7); border-radius:8px;
            margin:0 2px; font-size:12px; font-weight:600; padding:7px 13px; transition:all .15s;
        }
        .pagination .page-link:hover { background:var(--c-primary-subtle, #EEF1F8); border-color:var(--c-primary-border, #5C78B8); }
        .pagination .page-item.active .page-link { background:var(--c-primary, #0B266E); border-color:var(--c-primary, #0B266E); color:#fff; }
        .pagination .page-item.disabled .page-link { color:var(--c-border-strong, #C1C7CF); border-color:var(--c-border, #DFE1E7); }

        /* ── Lightbox ───────────────────────────────────────────────── */
        .lightbox-modal {
            display:none; position:fixed; inset:0; z-index:10000;
            background:rgba(13,13,18,.92); align-items:center; justify-content:center;
        }
        .lightbox-modal.active { display:flex; }
        .lightbox-content { position:relative; max-width:90vw; max-height:85vh; }
        .lightbox-content img {
            max-width:90vw; max-height:82vh; object-fit:contain;
            border-radius:8px; box-shadow:0 25px 60px rgba(0,0,0,.4);
        }
        .lightbox-close {
            position:fixed; top:20px; right:24px; width:40px; height:40px;
            border-radius:50%; background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.2);
            color:#fff; cursor:pointer;
            display:flex; align-items:center; justify-content:center; transition:background .15s;
        }
        .lightbox-close:hover { background:rgba(255,255,255,.22); }
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

        $placeholderTints = [
            'akademik'    => '#D1F0F9',
            'himpunan'    => '#EEF1F8',
            'lowongan'    => '#DDF2EE',
            'event_prodi' => '#F9ECCB',
            'default'     => '#F6F8FA',
        ];
    @endphp

    <div class="dash-wrap">
        <div class="dash-box">

            {{-- ── Header ─────────────────────────────────── --}}
            <div class="dash-box-header">
                <x-manajemenmahasiswa::ui.page-header
                    title="Pengumuman & Informasi"
                    badge="Modul Mahasiswa"
                    subtitle="Wadah informasi terbaru untuk mahasiswa dan alumni" />
            </div>

            <div class="dash-box-body">

                {{-- ── Toolbar: Search + Filter + Per Page ──── --}}
                <form id="pgFilterForm" method="GET" action="{{ route('manajemenmahasiswa.pengumuman.index') }}">
                    <div class="pg-toolbar">
                        <div class="pg-search-wrap">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                            </svg>
                            <input type="text" name="search" id="pgSearchInput"
                                placeholder="Cari pengumuman..." value="{{ request('search') }}">
                        </div>


                        {{-- Pemilih jumlah kartu ada di footer daftar ("Per page"),
                             mengikuti pola tabel global SITKOM. --}}
                    </div>

                    @include('manajemenmahasiswa::pengumuman._filter-kategori', [
                        'selectedKategori' => $selectedKategori,
                        'formId'           => 'pgFilterForm',
                    ])
                </form>

                <div class="pg-section-header">
                    <span class="pg-section-label">Daftar Pengumuman</span>
                </div>

                {{-- ── Cards Grid ───────────────────────────── --}}
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
                            $catLabel = $item->kategori
                                ? ($kategoriMap[$catKey] ?? ucfirst(str_replace('_',' ',$catKey)))
                                : 'Umum';
                            $tint     = $placeholderTints[$catKey] ?? $placeholderTints['default'];

                            $authorName     = $item->author?->name ?? 'Kemahasiswaan';
                            $authorInitials = strtoupper(substr($authorName, 0, 2));

                        @endphp

                        <div class="pg-card"
                             data-href="{{ route('manajemenmahasiswa.pengumuman.show', $item->id) }}"
                             onclick="pgNavigate(event, this)">

                            {{-- Image --}}
                            <div class="pg-card-img">
                                @if($thumbnailUrl)
                                    <img src="{{ $thumbnailUrl }}" alt="{{ $item->judul }}"
                                         onclick="pgOpenLightbox(event,'{{ $thumbnailUrl }}','{{ addslashes($item->judul) }}')">
                                @else
                                    <div class="pg-card-img-placeholder" style="background: {{ $tint }};">
                                        <svg width="34" height="34" viewBox="0 0 24 24" fill="none"
                                            stroke="#808897" stroke-width="1.5">
                                            <path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
                                        </svg>
                                    </div>
                                @endif

                                {{-- Pinned overlay badges --}}
                                @if($isPinnedGlobal)
                                    <span class="pg-pinned-overlay pg-pinned-global">
                                        <svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5v6h2v-6h5v-2l-2-2z"/>
                                        </svg>
                                        Penting
                                    </span>
                                @elseif($isPinnedPersonal)
                                    <span class="pg-pinned-overlay pg-pinned-personal">
                                        <svg width="9" height="9" viewBox="0 0 24 24" fill="currentColor">
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
                                        <svg width="13" height="13" viewBox="0 0 24 24"
                                            fill="{{ $isPinnedPersonal ? 'currentColor' : 'none' }}"
                                            stroke="currentColor" stroke-width="2">
                                            <path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>

                            {{-- Body --}}
                            <div class="pg-card-body">
                                <div class="pg-category {{ $catClass }}">{{ $catLabel }}</div>
                                <div class="pg-card-title">{{ $item->judul }}</div>
                                <p class="pg-card-excerpt">
                                    {{ Str::limit(html_entity_decode(strip_tags($item->konten)), 120) }}
                                </p>
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
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none"
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

                {{-- ── Pagination ───────────────────────────── --}}
                {{-- Footer bersama: Per page + Showing X to Y of Z results + nomor
                     halaman. Grid kartu, jadi pilihannya kelipatan 6. --}}
                @include('manajemenmahasiswa::partials.table-footer', [
                    'paginator'      => $pengumuman,
                    'perPageOptions' => \Modules\ManajemenMahasiswa\Support\PerPage::KARTU,
                    'standalone'     => true,
                ])

            </div> <!-- end dash-box-body -->
        </div> <!-- end dash-box -->
    </div> <!-- end dash-wrap -->

    {{-- ── Lightbox ─────────────────────────────────── --}}
    <div class="lightbox-modal" id="pgLightbox">
        <button class="lightbox-close" onclick="pgCloseLightbox()" title="Tutup">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M18 6L6 18M6 6l12 12"/>
            </svg>
        </button>
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
