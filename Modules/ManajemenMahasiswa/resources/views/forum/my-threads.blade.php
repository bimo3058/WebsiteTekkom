<x-manajemenmahasiswa::layouts.forum-layout>

    @push('styles')
        <style>
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

            .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }
            .dash-wrap { display: flex; flex-direction: column; height: calc(100vh - 60px); padding: 10px; box-sizing: border-box; }
            .dash-box { display: flex; flex-direction: column; flex: 1; min-height: 0; background: #fff; border: 1px solid #DFE1E7; border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06); overflow: hidden; width: 100%; box-sizing: border-box; }
            .dash-box-header { background: #fff; border-bottom: 1px solid #DFE1E7; flex-shrink: 0; width: 100%; box-sizing: border-box; padding: 16px 24px; }
            .dash-box-body { flex: 1; min-height: 0; display: flex; flex-direction: column; overflow-y: auto; padding: 20px 24px; }
            .dash-box-body::-webkit-scrollbar { width: 6px; }
            .dash-box-body::-webkit-scrollbar-thumb { background: #C1C7CF; border-radius: 10px; }
            @media (max-width: 767px) {
                .sitkom-content { padding: 8px 8px 80px !important; display: block !important; overflow: visible !important; }
                .dash-wrap { height: auto !important; padding: 0; }
                .dash-box { flex: none !important; overflow: visible !important; border-radius: 10px; }
                .dash-box-header { padding: 12px 14px; position: sticky; top: 52px; z-index: 10; }
                .dash-box-body { overflow-y: visible !important; flex: none !important; padding: 14px; }
            }

            .forum-card {
                background: #fff;
                border-radius: 12px;
                border: 1px solid #DFE1E7;
                padding: 16px 20px;
                margin-bottom: 10px;
                transition: all 0.2s ease;
                box-shadow: 0px 1px 2px 0px rgba(228, 229, 231, 0.5);
            }

            .forum-card:hover {
                border-color: #C1C7CF;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
                transform: translateY(-1px);
            }

            .avatar-placeholder {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: linear-gradient(135deg, #eef2ff, #dbe4f5);
                color: #0B266E;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 12px;
                border: 2px solid #eef2ff;
            }

            .btn-post {
                background-color: #0B266E;
                color: white;
                border: none;
                border-radius: 8px;
                padding: 0 14px;
                height: 36px;
                font-size: 13px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                white-space: nowrap;
                transition: all 0.2s ease;
                text-decoration: none;
            }

            .btn-post:hover { background-color: #091958; color: white; }

            .search-wrapper { position: relative; flex-grow: 1; }

            .search-input {
                border: 1px solid #DFE1E7;
                border-radius: 8px;
                padding: 0 14px 0 36px;
                font-size: 13px;
                font-weight: 500;
                color: #374151;
                outline: none;
                background: #fff;
                height: 36px;
                transition: all 0.2s ease;
                width: 100%;
            }

            .search-input::placeholder { color: #808897; }

            .search-input:focus {
                border-color: #0B266E;
                box-shadow: 0 0 0 3px rgba(11, 38, 110, 0.1);
            }

            .search-icon {
                position: absolute;
                left: 16px;
                top: 50%;
                transform: translateY(-50%);
                color: #9ca3af;
                pointer-events: none;
                width: 18px;
                height: 18px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .search-icon svg { width: 18px; height: 18px; }


            .post-actions .vote-pill {
                background: #f1f5f9;
                border-radius: 20px;
                display: inline-flex;
                align-items: center;
                padding: 1px;
                margin-right: 8px;
                border: 1px solid #e2e8f0;
            }

            .post-actions .vote-pill button {
                background: transparent;
                border: none;
                padding: 5px 10px;
                border-radius: 20px;
                color: #64748b;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s ease;
            }

            .post-actions .vote-pill span {
                font-weight: 700;
                font-size: 13px;
                padding: 0 4px;
                text-align: center;
                color: #1e293b;
            }

            .post-actions .vote-pill .v-separator {
                width: 1px;
                height: 16px;
                background-color: #cbd5e1;
                margin: 0 2px;
            }

            .post-actions .action-btn {
                background: #f1f5f9;
                border: none;
                padding: 6px 14px;
                border-radius: 20px;
                color: #4b5563;
                font-size: 13px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                margin-right: 8px;
                transition: background 0.15s;
                text-decoration: none;
            }

            .post-actions .action-btn:hover { background: #e2e8f0; color: #1e293b; }


            .tag-label {
                font-size: 11px;
                font-weight: 600;
                padding: 4px 12px;
                border-radius: 20px;
                display: inline-block;
            }

            .tag-green  { background: #dcfce7; color: #16a34a; }
            .tag-red    { background: #fee2e2; color: #dc2626; }
            .tag-gray   { background: #f3f4f6; color: #6b7280; }
            .tag-blue   { background: #dbeafe; color: #2563eb; }
            .tag-purple { background: #f3e8ff; color: #7c3aed; }

            .pinned-badge {
                background: #fef3c7;
                color: #d97706;
                font-size: 10px;
                font-weight: 700;
                padding: 2px 8px;
                border-radius: 4px;
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }

            .edited-badge {
                font-size: 11px;
                color: #9ca3af;
                font-style: italic;
            }

            .empty-state {
                text-align: center;
                padding: 80px 20px;
                color: #9ca3af;
            }

            .empty-state h5 { color: #374151; font-weight: 700; }


        </style>
    @endpush

    <div class="dash-wrap">
    <div class="dash-box">
    <div class="dash-box-header">
        <x-manajemenmahasiswa::ui.page-header
            title="Forum Saya"
            subtitle="Thread yang kamu buat di Forum Diskusi" />
    </div>
    <div class="dash-box-body">

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert"
            style="border-radius: 8px; border: none; background: #DDF2EE; color: #287F6E; font-weight: 600; font-size: 13px;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-4">
            <div style="background:#fff; border:1px solid #DFE1E7; border-radius:10px; padding:12px 16px; box-shadow:0px 1px 2px 0px rgba(228,229,231,0.5); display:flex; align-items:center; gap:12px;">
                <div style="width:28px; height:28px; border-radius:7px; background:rgba(11,38,110,0.08); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <x-manajemenmahasiswa::ui.icon name="message-text-square" size="14" style="color:#0B266E;" />
                </div>
                <div>
                    <div style="font-size:11px; font-weight:500; color:#666D80; line-height:1; margin-bottom:3px;">Total Post</div>
                    <div style="font-size:20px; font-weight:800; color:#0D0D12; letter-spacing:-0.02em; line-height:1;">{{ $totalThreads }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div style="background:#fff; border:1px solid #DFE1E7; border-radius:10px; padding:12px 16px; box-shadow:0px 1px 2px 0px rgba(228,229,231,0.5); display:flex; align-items:center; gap:12px;">
                <div style="width:28px; height:28px; border-radius:7px; background:rgba(11,38,110,0.08); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:11px; font-weight:500; color:#666D80; line-height:1; margin-bottom:3px;">Total Upvote</div>
                    <div style="font-size:20px; font-weight:800; color:#0D0D12; letter-spacing:-0.02em; line-height:1;">{{ $totalVotes }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div style="background:#fff; border:1px solid #DFE1E7; border-radius:10px; padding:12px 16px; box-shadow:0px 1px 2px 0px rgba(228,229,231,0.5); display:flex; align-items:center; gap:12px;">
                <div style="width:28px; height:28px; border-radius:7px; background:rgba(11,38,110,0.08); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <x-manajemenmahasiswa::ui.icon name="message-dots-circle" size="14" style="color:#0B266E;" />
                </div>
                <div>
                    <div style="font-size:11px; font-weight:500; color:#666D80; line-height:1; margin-bottom:3px;">Total Komentar</div>
                    <div style="font-size:20px; font-weight:800; color:#0D0D12; letter-spacing:-0.02em; line-height:1;">{{ $totalComments }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('manajemenmahasiswa.forum.my') }}" id="myForumFilterForm">
        @php $currentSort = request('sort', 'terbaru'); @endphp

        {{-- Row 1: Search + Buat Post --}}
        <div class="d-flex gap-2 mb-2 flex-wrap align-items-center">
            <div class="search-wrapper" style="flex:1; min-width:180px;">
                <span class="search-icon">
                    <x-manajemenmahasiswa::ui.icon name="search-01" size="16" />
                </span>
                <input type="text" name="search" class="search-input" placeholder="Cari thread kamu..."
                    value="{{ request('search') }}">
            </div>
            <a href="{{ route('manajemenmahasiswa.forum.create') }}" class="mk-btn mk-btn--primary flex-shrink-0">
                <x-manajemenmahasiswa::ui.icon name="plus" size="16" /> Buat Post
            </a>
        </div>

        {{-- Row 2: Category + Sort Dropdown --}}
        <div class="d-flex gap-2 mb-4 flex-wrap align-items-center">
            <x-manajemenmahasiswa::ui.select name="kategori" size="md" :block="false" min-width="148"
                class="flex-shrink-0"
                onchange="document.getElementById('myForumFilterForm').submit()">
                <option value="semua" {{ !request('kategori') || request('kategori') == 'semua' ? 'selected' : '' }}>
                    Semua Kategori</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" {{ request('kategori') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </x-manajemenmahasiswa::ui.select>
            <x-manajemenmahasiswa::ui.select name="sort" size="md" :block="false" min-width="130"
                class="flex-shrink-0"
                onchange="document.getElementById('myForumFilterForm').submit()">
                <option value="terbaru" {{ $currentSort === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                <option value="top"     {{ $currentSort === 'top'     ? 'selected' : '' }}>Top</option>
            </x-manajemenmahasiswa::ui.select>
        </div>
    </form>

    {{-- Thread List --}}
    @forelse($threads as $thread)
        <div class="forum-card" data-thread-id="{{ $thread->id }}" style="cursor: pointer;">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-placeholder">
                        {{ strtoupper(substr($user->name ?? '?', 0, 2)) }}
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span style="font-size:16px; font-weight:700; color:#0D0D12;">{{ $user->name }}</span>
                            @if($thread->is_pinned)
                                <span class="pinned-badge">
                                    <x-manajemenmahasiswa::ui.icon name="bookmark" size="12" /> Pinned
                                </span>
                            @endif
                            @if($thread->is_locked)
                                <span class="tag-label tag-red">
                                    <x-manajemenmahasiswa::ui.icon name="locked-01" size="10" /> Dikunci
                                </span>
                            @endif
                        </div>
                        <div style="display:flex; align-items:center; gap:6px; margin-top:2px;">
                            <span style="font-size:11px; color:#666D80; font-weight:500;">{{ $thread->created_at->diffForHumans() }}</span>
                            @if($thread->isEdited())
                                <span style="font-size:11px; color:#808897; font-style:italic;">(diedit)</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Three-dot menu --}}
                <div style="position: relative;" x-data="{ open: false }" @click.stop>
                    <button type="button" class="mk-btn mk-btn--secondary mk-btn--icon mk-btn--sm"
                        @click.stop="open = !open" @click.outside="open = false"
                        :aria-expanded="open" aria-haspopup="menu" title="Aksi lainnya">
                        <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24">
                            <circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/>
                        </svg>
                    </button>
                    <div class="mk-menu" role="menu" x-show="open" x-cloak style="display:none; right:0; left:auto;"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100">
                        <a href="{{ route('manajemenmahasiswa.forum.edit', $thread->id) }}"
                            class="mk-menu-item" role="menuitem">
                            <x-manajemenmahasiswa::ui.icon name="file-01" size="14" /> Edit
                        </a>
                        <form method="POST" action="{{ route('manajemenmahasiswa.forum.destroy', $thread->id) }}"
                            onsubmit="return mkConfirmSubmit(this, 'Yakin ingin menghapus thread ini?', { title: 'Hapus Thread', confirmText: 'Ya, Hapus' })">
                            @csrf @method('DELETE')
                            <button type="submit" class="mk-menu-item" role="menuitem">
                                <x-manajemenmahasiswa::ui.icon name="minus-circle" size="14" /> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <h6 style="font-size:16px; font-weight:700; color:#0D0D12; margin-bottom:6px; line-height:1.3;">{{ $thread->judul }}</h6>
                <p style="font-size:14px; color:#666D80; margin:0; line-height:1.6;">
                    {{ Str::limit($thread->getTextContent() ?: strip_tags($thread->konten), 200) }}
                </p>
            </div>

            @if($thread->getFirstImageUrl())
                <div class="mt-2 mb-3"
                    style="width: 100%; max-height: 300px; overflow: hidden; border-radius: 10px; border: 1px solid #DFE1E7; background: #f8fafc; display: flex; justify-content: center; align-items: center;">
                    <img src="{{ $thread->getFirstImageUrl() }}" alt="Thumbnail"
                        style="width: 100%; max-height: 300px; object-fit: contain;">
                </div>
            @endif

            {{-- Kategori --}}
            <div class="d-flex gap-2 mb-3 flex-wrap">
                @foreach($thread->getKategoriLabels() as $idx => $lbl)
                    @php $colorClass = $thread->getKategoriColors()[$idx] ?? 'tag-gray'; @endphp
                    <span class="tag-label {{ $colorClass }}">{{ $lbl }}</span>
                @endforeach
            </div>

            {{-- Actions --}}
            <div class="post-actions d-flex align-items-center mt-2 flex-wrap gap-1">
                <div class="vote-pill shadow-sm">
                    <button disabled style="cursor: default;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="19" x2="12" y2="5"></line>
                            <polyline points="5 12 12 5 19 12"></polyline>
                        </svg>
                    </button>
                    <span>{{ $thread->vote_count }}</span>
                    <div class="v-separator"></div>
                    <button disabled style="cursor: default;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <polyline points="19 12 12 19 5 12"></polyline>
                        </svg>
                    </button>
                </div>

                <button class="action-btn"
                    onclick="event.stopPropagation(); window.location.href='{{ route('manajemenmahasiswa.forum.show', $thread->id) }}'">
                    <x-manajemenmahasiswa::ui.icon name="message-dots-circle" size="16" />
                    {{ $thread->comments_count ?? $thread->comment_count }}
                </button>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <div style="margin-bottom: 16px; color: #d1d5db;">
                <x-manajemenmahasiswa::ui.icon name="message-text-square" size="52" />
            </div>
            <h5>Belum ada thread</h5>
            <p style="font-size: 14px;">Kamu belum pernah membuat thread. Yuk mulai diskusi!</p>
            <a href="{{ route('manajemenmahasiswa.forum.create') }}" class="btn-post text-decoration-none mt-2">
                <x-manajemenmahasiswa::ui.icon name="plus-circle" size="16" /> Buat Post Pertama
            </a>
        </div>
    @endforelse

    {{-- Pagination --}}
    {{-- Footer bersama: Per page + Showing X to Y of Z results + nomor halaman --}}
    @include('manajemenmahasiswa::partials.table-footer', [
        'paginator'      => $threads,
        'perPageOptions' => \Modules\ManajemenMahasiswa\Support\PerPage::TABEL,
        'standalone'     => true,
    ])

    </div>{{-- /dash-box-body --}}
    </div>{{-- /dash-box --}}
    </div>{{-- /dash-wrap --}}

    @push('scripts')
        <script>
            document.querySelectorAll('.forum-card').forEach(card => {
                card.addEventListener('click', function (e) {
                    if (e.target.closest('.post-actions') || e.target.closest('[x-data]')) return;
                    window.location.href = `{{ url('manajemen-mahasiswa/forum') }}/${this.dataset.threadId}`;
                });
            });
        </script>
    @endpush

</x-manajemenmahasiswa::layouts.forum-layout>
