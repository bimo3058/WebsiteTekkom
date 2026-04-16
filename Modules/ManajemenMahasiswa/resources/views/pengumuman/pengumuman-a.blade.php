<x-manajemenmahasiswa::layouts.admin>

    @push('styles')
    <style>
        /* Override main-wrapper to be transparent */
        .main-wrapper {
            background: transparent !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        .pengumuman-header h4 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e1b4b;
            margin-bottom: 4px;
        }

        .pengumuman-header p {
            font-size: 0.95rem;
            color: #6b7280;
            margin-bottom: 0;
        }

        /* Search & Filter Bar */
        .search-filter-bar {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .search-input-wrapper {
            flex: 1;
            position: relative;
        }

        .search-input-wrapper input {
            width: 100%;
            padding: 12px 18px 12px 46px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #f3f0ff;
            font-size: 0.9rem;
            color: #4b5563;
            transition: all 0.25s ease;
            outline: none;
        }

        .search-input-wrapper input::placeholder {
            color: #9ca3af;
        }

        .search-input-wrapper input:focus {
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.15);
            background: #fff;
        }

        .search-input-wrapper .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }

        .filter-dropdown {
            position: relative;
        }

        .filter-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
            font-size: 0.9rem;
            color: #374151;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            min-width: 160px;
            justify-content: space-between;
        }

        .filter-btn:hover {
            border-color: #818cf8;
            background: #faf9ff;
        }

        .filter-btn.active {
            border-color: #818cf8;
            color: #4f46e5;
        }

        .filter-btn .chevron-icon {
            transition: transform 0.2s ease;
        }

        .filter-btn.open .chevron-icon {
            transform: rotate(180deg);
        }

        .filter-menu {
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 8px;
            min-width: 200px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            z-index: 100;
            display: none;
            animation: fadeInDown 0.2s ease;
        }

        .filter-menu.show {
            display: block;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .filter-menu-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.15s ease;
            font-size: 0.88rem;
            color: #374151;
        }

        .filter-menu-item:hover {
            background: #f5f3ff;
        }

        .filter-menu-item.selected {
            background: #ede9fe;
            color: #4f46e5;
            font-weight: 600;
        }

        .filter-menu-item .check-icon {
            width: 18px;
            color: #4f46e5;
            opacity: 0;
            transition: opacity 0.15s ease;
        }

        .filter-menu-item.selected .check-icon {
            opacity: 1;
        }

        /* Buat Post Button */
        .btn-buat-post {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            background: #4f46e5;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn-buat-post:hover {
            background: #4338ca;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        /* Announcement Cards */
        .pengumuman-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .pengumuman-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #818cf8;
            border-radius: 14px;
            padding: 22px 26px;
            transition: all 0.25s ease;
            cursor: pointer;
            text-decoration: none;
            display: block;
        }

        .pengumuman-card:hover {
            border-left-color: #4f46e5;
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.08);
            transform: translateY(-2px);
        }

        .pengumuman-card-body {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }

        .pengumuman-card-content {
            flex: 1;
            min-width: 0;
        }

        .pengumuman-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .pengumuman-card-title .megaphone-icon {
            flex-shrink: 0;
            color: #4f46e5;
        }

        .pengumuman-card-title h6 {
            font-size: 1rem;
            font-weight: 700;
            color: #1e1b4b;
            margin: 0;
            line-height: 1.4;
        }

        .pengumuman-card-desc {
            font-size: 0.88rem;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .pengumuman-card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .pengumuman-card-date {
            font-size: 0.82rem;
            color: #9ca3af;
            font-weight: 500;
        }

        .pengumuman-card-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.01em;
        }

        .badge-akademik {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-himpunan {
            background: #ede9fe;
            color: #6d28d9;
        }

        .badge-lowongan {
            background: #d1fae5;
            color: #059669;
        }

        .badge-event_prodi {
            background: #fef3c7;
            color: #d97706;
        }

        .badge-umum {
            background: #f3f4f6;
            color: #4b5563;
        }

        .badge-draft {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-published {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-archived {
            background: #f3f4f6;
            color: #6b7280;
        }

        .pengumuman-read-more {
            flex-shrink: 0;
            align-self: flex-end;
            font-size: 0.85rem;
            font-weight: 600;
            color: #4f46e5;
            text-decoration: none;
            transition: color 0.2s ease;
            white-space: nowrap;
        }

        .pengumuman-read-more:hover {
            color: #3730a3;
        }

        /* Pagination */
        .pengumuman-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin-top: 32px;
        }

        .pagination-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border: 1px solid #e5e7eb;
            border-radius: 50%;
            background: #fff;
            color: #6b7280;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .pagination-btn:hover:not(.disabled) {
            border-color: #818cf8;
            color: #4f46e5;
            background: #f5f3ff;
        }

        .pagination-btn.disabled {
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: none;
        }

        .pagination-info {
            font-size: 0.82rem;
            color: #9ca3af;
            font-weight: 500;
        }

        /* Empty State */
        .pengumuman-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            text-align: center;
        }

        .pengumuman-empty .empty-icon {
            width: 80px;
            height: 80px;
            background: #f5f3ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .pengumuman-empty .empty-icon svg {
            color: #818cf8;
        }

        .pengumuman-empty h5 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .pengumuman-empty p {
            font-size: 0.9rem;
            color: #9ca3af;
        }
    </style>
    @endpush

    <!-- Header -->
    <div class="pengumuman-header mb-4">
        <h4>Pengumuman & Informasi</h4>
        <p>Wadah Informasi untuk Mahasiswa dan Alumni</p>
    </div>

    <!-- Search + Filter + Buat Post -->
    <form id="pengumumanFilterForm" method="GET" action="{{ route('manajemenmahasiswa.pengumuman.index') }}">
        <div class="search-filter-bar mb-4">
            <!-- Search Input -->
            <div class="search-input-wrapper">
                <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
                <input type="text" name="search" id="searchInput" placeholder="Search"
                    value="{{ request('search') }}">
            </div>

            <!-- Filter Dropdown -->
            <div class="filter-dropdown">
                <input type="hidden" name="kategori" id="kategoriInput" value="{{ request('kategori', 'semua') }}">
                <button type="button" class="filter-btn" id="filterToggle" onclick="toggleFilterMenu()">
                    <span id="filterLabel">
                        @php
                            $kategoriMap = [
                                'semua' => 'Filter',
                                'akademik' => 'Akademik',
                                'himpunan' => 'Himpunan',
                                'lowongan' => 'Lowongan',
                                'event_prodi' => 'Event Prodi',
                            ];
                            $selectedKategori = request('kategori', 'semua');
                        @endphp
                        {{ $kategoriMap[$selectedKategori] ?? 'Filter' }}
                    </span>
                    <svg class="chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m6 9 6 6 6-6" />
                    </svg>
                </button>
                <div class="filter-menu" id="filterMenu">
                    @foreach([
                        'semua' => 'Semua Kategori',
                        'akademik' => 'Akademik',
                        'himpunan' => 'Himpunan',
                        'lowongan' => 'Lowongan',
                        'event_prodi' => 'Event Prodi'
                    ] as $value => $label)
                        <div class="filter-menu-item {{ $selectedKategori === $value ? 'selected' : '' }}"
                            onclick="selectFilter('{{ $value }}', '{{ $label }}')">
                            <svg class="check-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span>{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Buat Post Button -->
            <a href="{{ route('manajemenmahasiswa.pengumuman.create') }}" class="btn-buat-post">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Buat Post
            </a>
        </div>
    </form>

    <!-- List Pengumuman -->
    <div class="pengumuman-list">
        @forelse($pengumuman as $item)
            <a href="{{ route('manajemenmahasiswa.pengumuman.show', $item->id) }}" class="pengumuman-card">
                <div class="pengumuman-card-body">
                    <div class="pengumuman-card-content">
                        <div class="pengumuman-card-title">
                            <svg class="megaphone-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="m3 11 18-5v12L3 14v-3z"></path>
                                <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"></path>
                            </svg>
                            <h6>{{ $item->judul }}</h6>
                        </div>
                        <p class="pengumuman-card-desc">
                            {{ Str::limit(strip_tags($item->konten), 150) }}
                        </p>
                        <div class="pengumuman-card-meta">
                            <span class="pengumuman-card-date">
                                {{ $item->created_at->translatedFormat('d F Y') }}
                            </span>
                            <div style="display: flex; gap: 8px; align-items: center;">
                                @if($item->status_publish && $item->status_publish !== 'published')
                                    <span class="pengumuman-card-badge badge-{{ $item->status_publish }}">
                                        {{ ucfirst($item->status_publish) }}
                                    </span>
                                @endif
                                @if($item->kategori)
                                    <span class="pengumuman-card-badge badge-{{ $item->kategori }}">
                                        {{ ucfirst(str_replace('_', ' ', $item->kategori)) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <span class="pengumuman-read-more">Baca Selengkapnya</span>
                </div>
            </a>
        @empty
            <div class="pengumuman-empty">
                <div class="empty-icon">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m3 11 18-5v12L3 14v-3z"></path>
                        <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"></path>
                    </svg>
                </div>
                <h5>Belum ada pengumuman</h5>
                <p>Pengumuman terbaru akan muncul di sini</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($pengumuman->hasPages())
        <div class="pengumuman-pagination">
            @if($pengumuman->onFirstPage())
                <span class="pagination-btn disabled">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                </span>
            @else
                <a href="{{ $pengumuman->previousPageUrl() }}" class="pagination-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                </a>
            @endif

            <span class="pagination-info">
                Halaman {{ $pengumuman->currentPage() }} dari {{ $pengumuman->lastPage() }}
            </span>

            @if($pengumuman->hasMorePages())
                <a href="{{ $pengumuman->nextPageUrl() }}" class="pagination-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m9 18 6-6-6-6" />
                    </svg>
                </a>
            @else
                <span class="pagination-btn disabled">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m9 18 6-6-6-6" />
                    </svg>
                </span>
            @endif
        </div>
    @endif

    @push('scripts')
    <script>
        // Filter dropdown toggle
        function toggleFilterMenu() {
            const menu = document.getElementById('filterMenu');
            const btn = document.getElementById('filterToggle');
            menu.classList.toggle('show');
            btn.classList.toggle('open');
        }

        // Select filter option
        function selectFilter(value, label) {
            document.getElementById('kategoriInput').value = value;
            document.getElementById('filterLabel').textContent = value === 'semua' ? 'Filter' : label;

            // Update selected state
            document.querySelectorAll('.filter-menu-item').forEach(item => {
                item.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');

            // Close menu and submit
            toggleFilterMenu();
            document.getElementById('pengumumanFilterForm').submit();
        }

        // Close filter menu on outside click
        document.addEventListener('click', function (e) {
            const dropdown = document.querySelector('.filter-dropdown');
            if (!dropdown.contains(e.target)) {
                document.getElementById('filterMenu').classList.remove('show');
                document.getElementById('filterToggle').classList.remove('open');
            }
        });

        // Search on Enter key
        document.getElementById('searchInput').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('pengumumanFilterForm').submit();
            }
        });
    </script>
    @endpush

</x-manajemenmahasiswa::layouts.admin>
