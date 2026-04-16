<x-manajemenmahasiswa::layouts.mahasiswa>

    @push('styles')
        <style>
            .bg-gradient-purple {
                background: #4D4DFF;
            }

            .forum-card {
                background: #ffffff;
                border-radius: 12px;
                padding: 20px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
                margin-bottom: 20px;
                transition: transform 0.15s ease, box-shadow 0.15s ease;
            }

            .forum-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.08), 0 4px 8px -2px rgba(0, 0, 0, 0.04);
            }

            .avatar-placeholder {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background-color: #e0e7ff;
                color: #4f46e5;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                font-size: 14px;
            }

            .btn-join {
                background-color: #818cf8;
                color: white;
                border: none;
                border-radius: 6px;
                padding: 4px 16px;
                font-size: 13px;
                font-weight: 600;
                transition: background-color 0.2s;
            }

            .btn-join:hover {
                background-color: #6366f1;
            }

            .btn-post {
                background-color: #818cf8;
                color: white;
                border: none;
                border-radius: 8px;
                padding: 0 20px;
                height: 42px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                white-space: nowrap;
            }

            .btn-post:hover {
                background-color: #6366f1;
                color: white;
            }

            .post-actions button {
                background: #f3f4f6;
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
            }

            .post-actions button:hover {
                background: #e5e7eb;
            }

            .post-actions button.vote-active-up {
                background: #dcfce7;
                color: #16a34a;
            }

            .post-actions button.vote-active-down {
                background: #fee2e2;
                color: #dc2626;
            }

            .search-input {
                background-color: #f3f4f6;
                border: none;
                border-radius: 8px;
                height: 42px;
                padding-left: 36px;
            }

            .search-input:focus {
                background-color: #ffffff;
                box-shadow: 0 0 0 2px #e0e7ff;
            }

            .search-wrapper {
                position: relative;
                flex-grow: 1;
            }

            .search-icon {
                position: absolute;
                left: 12px;
                top: 50%;
                transform: translateY(-50%);
                color: #9ca3af;
                font-size: 14px;
            }

            .leaderboard-table th {
                font-weight: 500;
                font-size: 13px;
                color: rgba(255, 255, 255, 0.8);
                border: none;
                padding-bottom: 15px;
                background-color: #4D4DFF;
            }

            .leaderboard-table td {
                border: none;
                color: #ffffff;
                font-size: 14px;
                padding: 6px 0;
                background-color: #4D4DFF;
            }

            .tag-label {
                font-size: 11px;
                font-weight: 600;
                padding: 2px 8px;
                border-radius: 4px;
            }

            .tag-green {
                background: #dcfce7;
                color: #16a34a;
            }

            .tag-red {
                background: #fee2e2;
                color: #dc2626;
            }

            .tag-gray {
                background: #f3f4f6;
                color: #6b7280;
            }

            .tag-blue {
                background: #dbeafe;
                color: #2563eb;
            }

            .tag-purple {
                background: #f3e8ff;
                color: #7c3aed;
            }

            .xp-progress-bar {
                height: 8px;
                background: rgba(255,255,255,0.2);
                border-radius: 4px;
                overflow: hidden;
                margin-top: 6px;
            }

            .xp-progress-fill {
                height: 100%;
                background: #fbbf24;
                border-radius: 4px;
                transition: width 0.5s ease;
            }

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

            .empty-state {
                text-align: center;
                padding: 60px 20px;
                color: #9ca3af;
            }

            .empty-state .icon {
                font-size: 48px;
                margin-bottom: 16px;
            }
        </style>
    @endpush

    <div class="mb-5">
        <h3 class="fw-bold mb-1 text-dark">Forum Diskusi</h3>
        <p class="text-dark fw-bold" style="font-size: 14px;">Wadah komunikasi mahasiswa & alumni</p>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px; border: none; background: #dcfce7; color: #16a34a; font-weight: 600;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header Cards -->
    <div class="row mb-4">
        <!-- Leaderboard -->
        <div class="col-md-7 mb-3 mb-md-0">
            <div class="bg-gradient-purple rounded-4 p-4 h-100 text-white shadow-sm">
                <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                    🏆 Leaderboard
                </h6>
                <div class="table-responsive">
                    <table class="table table-borderless table-sm mb-0 leaderboard-table">
                        <thead>
                            <tr>
                                <th style="width: 10%">No.</th>
                                <th style="width: 40%">User</th>
                                <th style="width: 20%">Level</th>
                                <th style="width: 30%">Badges</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaderboard as $index => $entry)
                                <tr>
                                    <td>{{ $index + 1 }}.</td>
                                    <td>{{ $entry->name }}</td>
                                    <td>Lvl {{ $entry->level }}</td>
                                    <td>
                                        @foreach($entry->badges->take(3) as $badge)
                                            <span title="{{ $badge->name }}">{{ $badge->icon }}</span>
                                        @endforeach
                                        @if($entry->badges->count() > 3)
                                            <span style="font-size: 11px; opacity: 0.7;">+{{ $entry->badges->count() - 3 }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center" style="opacity: 0.7;">Belum ada data leaderboard</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Streak -->
        <div class="col-md-5">
            <div
                class="bg-gradient-purple rounded-4 p-4 h-100 text-white shadow-sm d-flex flex-column justify-content-center">
                <h6 class="fw-bold mb-4 d-flex align-items-center gap-2">
                    🔥 Streak Kamu Hari Ini : {{ $userStats['current_streak'] }} Hari
                </h6>
                <div class="mb-3 ps-4">
                    <span style="font-size: 14px; font-weight: 500;">Rank : #{{ $userStats['rank'] }}</span>
                </div>
                <div class="mb-3 ps-4 d-flex align-items-center gap-2">
                    <span>🏵️</span> <span style="font-size: 14px; font-weight: 500;">Level Kamu : {{ $userStats['level'] }}</span>
                </div>
                <div class="ps-4">
                    <div class="d-flex align-items-center gap-2">
                        <span>📊</span>
                        <span style="font-size: 14px; font-weight: 500;">
                            Exp : {{ $userStats['total_xp'] }}/{{ $userStats['xp_for_next'] }}
                        </span>
                    </div>
                    <div class="xp-progress-bar mt-2" style="width: 80%;">
                        @php
                            $progressPct = $userStats['xp_needed'] > 0
                                ? min(100, round(($userStats['xp_current'] / $userStats['xp_needed']) * 100))
                                : 100;
                        @endphp
                        <div class="xp-progress-fill" style="width: {{ $progressPct }}%;"></div>
                    </div>
                </div>

                {{-- Badges --}}
                @if($userStats['badges']->isNotEmpty())
                    <div class="mt-3 ps-4 d-flex align-items-center gap-1 flex-wrap">
                        @foreach($userStats['badges'] as $badge)
                            <span title="{{ $badge->name }}: {{ $badge->description }}" style="font-size: 18px; cursor: help;">{{ $badge->icon }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Search & Filter Area -->
    <form method="GET" action="{{ route('manajemenmahasiswa.forum.index') }}" id="forumFilterForm">
        <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center mb-4">
            <div class="search-wrapper w-100 me-0 me-md-2">
                <span class="search-icon">🔍</span>
                <input type="text" name="search" class="form-control search-input w-100"
                       placeholder="Cari diskusi..."
                       value="{{ request('search') }}">
            </div>

            <div class="d-flex gap-3">
                <select name="kategori" class="form-select border-1" style="border-radius: 8px; height: 42px; min-width: 130px;"
                        onchange="document.getElementById('forumFilterForm').submit()">
                    <option value="semua" {{ request('kategori') == 'semua' || !request('kategori') ? 'selected' : '' }}>Semua</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ request('kategori') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <a href="{{ route('manajemenmahasiswa.forum.create') }}" class="btn-post text-decoration-none">
                    Post ⊕
                </a>
            </div>
        </div>
    </form>

    <!-- Forum Posts -->
    @forelse($threads as $thread)
        <a href="{{ route('manajemenmahasiswa.forum.show', $thread->id) }}" class="text-decoration-none">
            <div class="forum-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-placeholder">
                            {{ strtoupper(substr($thread->author->name ?? '?', 0, 2)) }}
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h6 class="fw-bold text-dark mb-0">{{ $thread->author->name ?? 'Unknown' }}</h6>
                                <span class="text-primary fw-medium" style="font-size: 12px;">• {{ $thread->created_at->diffForHumans() }}</span>
                                @if($thread->is_pinned)
                                    <span class="pinned-badge">📌 Pinned</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($thread->user_id === $user->id)
                        <div class="dropdown" onclick="event.preventDefault(); event.stopPropagation();">
                            <span class="text-muted fw-bold" style="cursor: pointer; font-size: 20px; line-height: 1;"
                                  data-bs-toggle="dropdown">⋯</span>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 8px;">
                                <li>
                                    <form method="POST" action="{{ route('manajemenmahasiswa.forum.destroy', $thread->id) }}"
                                          onsubmit="return confirm('Yakin ingin menghapus thread ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">🗑️ Hapus</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>

                <h6 class="fw-bold text-dark mb-2">{{ $thread->judul }}</h6>
                <p class="text-dark" style="font-size: 14px; margin-bottom: 12px; line-height: 1.5;">
                    {{ Str::limit(strip_tags($thread->konten), 200) }}
                </p>

                <!-- Labels -->
                <div class="d-flex gap-2 mb-3">
                    <span class="tag-label {{ $thread->kategoriColor() }}">{{ $thread->kategoriLabel() }}</span>
                    @if($thread->is_locked)
                        <span class="tag-label tag-red">🔒 Dikunci</span>
                    @endif
                </div>

                <!-- Actions -->
                <div class="post-actions d-flex align-items-center">
                    <!-- Up/Down vote -->
                    <button onclick="event.preventDefault();">
                        <span class="me-1" style="font-size: 14px;">↑</span>
                        {{ $thread->vote_count }}
                        <span class="ms-1" style="font-size: 14px;">↓</span>
                    </button>
                    <!-- Comments -->
                    <button onclick="event.preventDefault();">
                        <span class="me-1" style="font-size: 14px;">💬</span> {{ $thread->comments_count ?? $thread->comment_count }}
                    </button>
                </div>
            </div>
        </a>
    @empty
        <div class="empty-state">
            <div class="icon">💬</div>
            <h5 class="fw-bold text-dark">Belum ada diskusi</h5>
            <p>Jadilah yang pertama memulai diskusi!</p>
            <a href="{{ route('manajemenmahasiswa.forum.create') }}" class="btn-post text-decoration-none">
                Buat Post Pertama ⊕
            </a>
        </div>
    @endforelse

    <!-- Pagination -->
    @if($threads->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $threads->appends(request()->query())->links() }}
        </div>
    @endif

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @endpush

</x-manajemenmahasiswa::layouts.mahasiswa>