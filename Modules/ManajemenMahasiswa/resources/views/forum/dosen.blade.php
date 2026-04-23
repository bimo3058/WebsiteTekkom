<x-manajemenmahasiswa::layouts.dosen>

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
                background: rgba(255, 255, 255, 0.2);
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

            .thread-thumbnail {
                width: 100%;
                max-height: 200px;
                object-fit: cover;
                border-radius: 8px;
                border: 1px solid #e5e7eb;
                margin-bottom: 12px;
            }

            .edited-badge {
                font-size: 11px;
                color: #9ca3af;
                font-style: italic;
            }

            .personal-pin-badge {
                background: #dbeafe;
                color: #2563eb;
                font-size: 10px;
                font-weight: 700;
                padding: 2px 8px;
                border-radius: 4px;
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }
        </style>
    @endpush

    <div class="mb-5">
        <h3 class="fw-bold mb-1 text-dark">Forum Diskusi</h3>
        <p class="text-dark fw-bold" style="font-size: 14px;">Wadah komunikasi mahasiswa & alumni</p>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert"
            style="border-radius: 10px; border: none; background: #dcfce7; color: #16a34a; font-weight: 600;">
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
                    Leaderboard
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
                                            <span
                                                style="font-size: 11px; opacity: 0.7;">+{{ $entry->badges->count() - 3 }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center" style="opacity: 0.7;">Belum ada data leaderboard
                                    </td>
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
                    Streak Kamu Hari Ini : {{ $userStats['current_streak'] }} Hari
                </h6>
                <div class="mb-3 ps-4">
                    <span style="font-size: 14px; font-weight: 500;">Rank : #{{ $userStats['rank'] }}</span>
                </div>
                <div class="mb-3 ps-4 d-flex align-items-center gap-2">
                    <span></span> <span style="font-size: 14px; font-weight: 500;">Level Kamu :
                        {{ $userStats['level'] }}</span>
                </div>
                <div class="ps-4">
                    <div class="d-flex align-items-center gap-2">
                        <span></span>
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
                            <span title="{{ $badge->name }}: {{ $badge->description }}"
                                style="font-size: 18px; cursor: help;">{{ $badge->icon }}</span>
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
                <span class="search-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg></span>
                <input type="text" name="search" class="form-control search-input w-100" placeholder="Cari diskusi..."
                    value="{{ request('search') }}">
            </div>

            <div class="d-flex gap-3">
                <select name="kategori" class="form-select border-1"
                    style="border-radius: 8px; height: 42px; min-width: 130px;"
                    onchange="document.getElementById('forumFilterForm').submit()">
                    <option value="semua" {{ request('kategori') == 'semua' || !request('kategori') ? 'selected' : '' }}>
                        Semua</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ request('kategori') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <a href="{{ route('manajemenmahasiswa.forum.create') }}" class="btn-post text-decoration-none">
                    Post
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </a>
            </div>
        </div>
    </form>

    <!-- Forum Posts -->
    @forelse($threads as $thread)
        <a href="{{ route('manajemenmahasiswa.forum.show', $thread->id) }}" class="text-decoration-none">
            <div class="forum-card" data-thread-id="{{ $thread->id }}">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-placeholder">
                            {{ strtoupper(substr($thread->author->name ?? '?', 0, 2)) }}
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h6 class="fw-bold text-dark mb-0">{{ $thread->author->name ?? 'Unknown' }}</h6>
                                <span class="text-primary fw-medium" style="font-size: 12px;">•
                                    {{ $thread->created_at->diffForHumans() }}</span>
                                @if($thread->isEdited())
                                    <span class="edited-badge">(diedit)</span>
                                @endif
                                @if($thread->is_pinned)
                                    <span class="pinned-badge">📌 Pinned</span>
                                @endif
                                <span class="personal-pin-badge" data-personal-pin="{{ $thread->id }}" style="display: none;">📌 Pin Pribadi</span>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown" onclick="event.preventDefault(); event.stopPropagation();">
                        <span class="text-muted fw-bold" style="cursor: pointer; font-size: 20px; line-height: 1;"
                            data-bs-toggle="dropdown">⋯</span>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 8px;">
                            {{-- Edit (owner + admin) --}}
                            @if($thread->user_id === $user->id || $user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm']))
                                <li>
                                    <a href="{{ route('manajemenmahasiswa.forum.edit', $thread->id) }}" class="dropdown-item">✏️ Edit</a>
                                </li>
                            @endif
                            {{-- Pin Global (admin only) --}}
                            @if($user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm']))
                                <li>
                                    <form method="POST" action="{{ route('manajemenmahasiswa.forum.pin', $thread->id) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="dropdown-item">
                                            @if($thread->is_pinned) 🔓 Unpin Global @else 📌 Pin Global @endif
                                        </button>
                                    </form>
                                </li>
                            @endif
                            {{-- Pin Pribadi (semua role) --}}
                            <li>
                                <button type="button" class="dropdown-item personal-pin-toggle" data-thread-id="{{ $thread->id }}">
                                    📌 <span class="personal-pin-label" data-thread-id="{{ $thread->id }}">Pin Pribadi</span>
                                </button>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            {{-- Delete --}}
                            @if($thread->user_id === $user->id)
                                <li>
                                    <form method="POST" action="{{ route('manajemenmahasiswa.forum.destroy', $thread->id) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus thread ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">🗑️ Hapus</button>
                                    </form>
                                </li>
                            @elseif($user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm']))
                                <li>
                                    <form method="POST" action="{{ route('manajemenmahasiswa.forum.destroy', $thread->id) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus thread ini (sebagai admin)?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">🗑️ Hapus (Admin)</button>
                                    </form>
                                </li>
                            @endif
                            @if($thread->user_id !== $user->id)
                                <li>
                                    <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal"
                                        data-bs-target="#reportModal" data-thread-id="{{ $thread->id }}"
                                        data-thread-title="{{ $thread->judul }}">
                                        🚩 Laporkan Thread
                                    </button>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <h6 class="fw-bold text-dark mb-2">{{ $thread->judul }}</h6>

                {{-- Image thumbnail --}}
                @if($thread->getFirstImageUrl())
                    <img src="{{ $thread->getFirstImageUrl() }}" alt="Thumbnail" class="thread-thumbnail">
                @endif

                <p class="text-dark" style="font-size: 14px; margin-bottom: 12px; line-height: 1.5;">
                    {{ Str::limit($thread->getTextContent() ?: strip_tags($thread->konten), 200) }}
                </p>

                <!-- Labels -->
                <div class="d-flex gap-2 mb-3">
                    <span class="tag-label {{ $thread->kategoriColor() }}">{{ $thread->kategoriLabel() }}</span>
                    @if($thread->is_locked)
                        <span class="tag-label tag-red">🔒 Dikunci</span>
                    @endif
                </div>

                <!-- Actions -->
                @php
                    $threadVoteKey = \Modules\ManajemenMahasiswa\Models\Thread::class . '_' . $thread->id;
                    $threadUserVote = $userVotes[$threadVoteKey] ?? null;
                @endphp
                <div class="post-actions d-flex align-items-center"
                    onclick="event.preventDefault(); event.stopPropagation();">
                    <button
                        class="vote-thread-btn {{ $threadUserVote && $threadUserVote->value === 1 ? 'vote-active-up' : '' }}"
                        data-thread-id="{{ $thread->id }}" data-value="1">
                        <span class="me-1" style="font-size: 14px;">↑</span>
                    </button>
                    <span class="fw-bold text-dark mx-1 thread-vote-count-{{ $thread->id }}"
                        style="font-size: 14px; min-width: 15px; text-align: center;">{{ $thread->vote_count }}</span>
                    <button
                        class="vote-thread-btn {{ $threadUserVote && $threadUserVote->value === -1 ? 'vote-active-down' : '' }}"
                        data-thread-id="{{ $thread->id }}" data-value="-1">
                        <span style="font-size: 14px;">↓</span>
                    </button>
                    <button class="ms-2"
                        onclick="window.location.href='{{ route('manajemenmahasiswa.forum.show', $thread->id) }}'">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" class="me-1">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                        {{ $thread->comments_count ?? $thread->comment_count }}
                    </button>
                    <button class="share-btn ms-1" data-url="{{ route('manajemenmahasiswa.forum.show', $thread->id) }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                        </svg>
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

    @if($errors->has('alasan'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert"
            style="border-radius: 10px; border: none; font-weight: 600;">
            {{ $errors->first('alasan') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Report Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 12px; border: none;">
                <form id="reportForm" method="POST" action="">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-dark" id="reportModalLabel">🚩 Laporkan Thread</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted" style="font-size: 14px;">Apakah thread <strong
                                id="reportThreadTitle"></strong> melanggar panduan komunitas?</p>

                        <div class="mb-3">
                            <label for="alasan" class="form-label fw-bold" style="font-size: 14px;">Alasan Pelaporan
                                <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="alasan" id="alasan" rows="4"
                                placeholder="Tulis alasan spesifik (misal: SARA, Spam, Hoax)..." required
                                minlength="5"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Kirim Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            const csrfToken = '{{ csrf_token() }}';

            // ---- Personal Pin (localStorage) ----
            const PERSONAL_PINS_KEY = 'forum_personal_pins';
            function getPersonalPins() { try { return JSON.parse(localStorage.getItem(PERSONAL_PINS_KEY)) || []; } catch { return []; } }
            function togglePersonalPin(threadId) {
                let pins = getPersonalPins();
                const idx = pins.indexOf(threadId);
                if (idx > -1) pins.splice(idx, 1); else pins.push(threadId);
                localStorage.setItem(PERSONAL_PINS_KEY, JSON.stringify(pins));
                refreshPersonalPinUI();
            }
            function refreshPersonalPinUI() {
                const pins = getPersonalPins();
                document.querySelectorAll('[data-personal-pin]').forEach(b => { b.style.display = pins.includes(parseInt(b.dataset.personalPin)) ? 'inline-flex' : 'none'; });
                document.querySelectorAll('.personal-pin-label').forEach(l => { l.textContent = pins.includes(parseInt(l.dataset.threadId)) ? 'Unpin Pribadi' : 'Pin Pribadi'; });
            }
            document.querySelectorAll('.personal-pin-toggle').forEach(btn => btn.addEventListener('click', function(e) { e.preventDefault(); togglePersonalPin(parseInt(this.dataset.threadId)); }));
            refreshPersonalPinUI();

            // ---- Vote Thread (AJAX) ----
            document.querySelectorAll('.vote-thread-btn').forEach(btn => {
                btn.addEventListener('click', async function (e) {
                    e.preventDefault();
                    const threadId = this.dataset.threadId;
                    const value = parseInt(this.dataset.value);
                    try {
                        const res = await fetch(`{{ url('manajemen-mahasiswa/forum') }}/${threadId}/vote`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                            body: JSON.stringify({ value })
                        });
                        const data = await res.json();
                        document.querySelectorAll(`.thread-vote-count-${threadId}`).forEach(el => el.textContent = data.vote_count);
                        const parent = this.closest('.post-actions');
                        if (parent) {
                            parent.querySelectorAll('.vote-thread-btn').forEach(b => b.classList.remove('vote-active-up', 'vote-active-down'));
                            if (data.user_vote === 1) parent.querySelector('.vote-thread-btn[data-value="1"]').classList.add('vote-active-up');
                            else if (data.user_vote === -1) parent.querySelector('.vote-thread-btn[data-value="-1"]').classList.add('vote-active-down');
                        }
                    } catch (err) { console.error('Vote error:', err); }
                });
            });

            // ---- Share (Copy Link) ----
            document.querySelectorAll('.share-btn').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    navigator.clipboard.writeText(this.dataset.url).then(() => {
                        const orig = this.innerHTML;
                        this.innerHTML = '<span style="font-size:14px">✅</span>';
                        setTimeout(() => this.innerHTML = orig, 2000);
                    });
                });
            });

            // ---- Report Modal ----
            const reportModal = document.getElementById('reportModal');
            if (reportModal) {
                reportModal.addEventListener('show.bs.modal', function (event) {
                    const btn = event.relatedTarget;
                    reportModal.querySelector('#reportThreadTitle').textContent = `"${btn.dataset.threadTitle}"`;
                    reportModal.querySelector('#reportForm').action = `{{ url('manajemen-mahasiswa/forum') }}/${btn.dataset.threadId}/report`;
                });
            }
        </script>
    @endpush

</x-manajemenmahasiswa::layouts.dosen>