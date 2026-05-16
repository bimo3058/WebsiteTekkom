<x-manajemenmahasiswa::layouts.forum-layout>

    @push('styles')
        <style>
            /* ── Page Title ──────────────────────────────────────────────────── */
            .page-title {
                margin-bottom: 22px;
                display: flex;
                align-items: center;
                gap: 16px;
            }

            .page-title .back-btn {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #4b5563;
                text-decoration: none;
                transition: background 0.2s;
            }

            .page-title .back-btn:hover {
                background: #f3f4f6;
            }

            .page-title h1 {
                font-size: 26px;
                font-weight: 700;
                color: #111827;
                margin: 0 0 2px;
                letter-spacing: -0.02em;
            }

            .page-title p {
                font-size: 14px;
                color: #6b7280;
                margin: 0;
            }

            .forum-card {
                background: transparent;
                border-radius: 12px;
                padding: 24px 0;
                border: none;
                margin-bottom: 20px;
            }

            .avatar-placeholder {
                width: 48px;
                height: 48px;
                border-radius: 50%;
                background-color: #e0e7ff;
                color: #293C79;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                font-size: 18px;
            }

            .avatar-sm {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }

            .btn-join {
                background-color: #293C79;
                color: white;
                border: none;
                border-radius: 6px;
                padding: 4px 16px;
                font-size: 13px;
                font-weight: 600;
                transition: background-color 0.2s;
            }

            .btn-join:hover {
                background-color: #415086;
            }

            .post-actions .vote-pill {
                background: #f1f5f9;
                border-radius: 20px;
                display: inline-flex;
                align-items: center;
                padding: 1px;
                margin-right: 12px;
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

            .post-actions .vote-pill button:hover {
                background: #e2e8f0;
                color: #1e293b;
            }

            .post-actions .vote-pill button.vote-active-up {
                color: #ff4500;
            }

            .post-actions .vote-pill button.vote-active-up:hover {
                background: rgba(255, 69, 0, 0.1);
            }

            .post-actions .vote-pill button.vote-active-down {
                color: #7193ff;
            }

            .post-actions .vote-pill button.vote-active-down:hover {
                background: rgba(113, 147, 255, 0.1);
            }

            .post-actions .vote-pill span {
                font-weight: 700;
                font-size: 14px;
                padding: 0 4px;
                text-align: center;
                color: #1e293b;
            }

            .post-actions .vote-pill .v-separator {
                width: 1px;
                height: 18px;
                background-color: #cbd5e1;
                margin: 0 2px;
            }

            .post-actions .action-btn {
                background: #f1f5f9;
                border: none;
                padding: 6px 14px;
                border-radius: 20px;
                color: #4b5563;
                font-size: 14px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                margin-right: 12px;
                transition: background 0.15s;
            }

            .post-actions .action-btn:hover {
                background: #e2e8f0;
            }

            .tag-label {
                font-size: 11px;
                font-weight: 600;
                padding: 4px 12px;
                border-radius: 20px;
                display: inline-block;
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

            .comment-list {
                margin-top: 24px;
                padding-top: 24px;
                border-top: 1px solid #e5e7eb;
            }

            .comment-item {
                margin-bottom: 16px;
                padding-bottom: 16px;
                border-bottom: 1px solid #f3f4f6;
            }

            .comment-item:last-child {
                border-bottom: none;
                margin-bottom: 0;
                padding-bottom: 0;
            }

            .reply-form textarea {
                border-radius: 8px;
                resize: none;
                background-color: #f9fafb;
            }

            .reply-form textarea:focus {
                background-color: #ffffff;
                box-shadow: 0 0 0 2px #e0e7ff;
                border-color: #293C79;
            }

            .btn-post {
                background-color: #293C79;
                color: white;
                border: none;
                border-radius: 8px;
                padding: 8px 24px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: background 0.2s, opacity 0.2s;
            }

            .btn-post:hover {
                background-color: #415086;
                color: white;
            }

            .btn-post:disabled {
                opacity: 0.6;
                cursor: not-allowed;
            }

            .btn-post .spinner {
                width: 16px;
                height: 16px;
                border: 2px solid rgba(255, 255, 255, 0.4);
                border-top-color: #fff;
                border-radius: 50%;
                animation: spin 0.6s linear infinite;
            }

            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }

            .best-answer-badge {
                background: #dcfce7;
                color: #16a34a;
                font-size: 11px;
                font-weight: 700;
                padding: 2px 8px;
                border-radius: 4px;
            }

            .reply-item {
                margin-left: 24px;
                padding-left: 16px;
                border-left: 2px solid #e5e7eb;
                margin-top: 12px;
            }

            /* Comment action bar (Reddit style) */
            .comment-actions {
                display: flex;
                align-items: center;
                gap: 4px;
                margin-top: 6px;
            }

            .comment-actions .c-vote-pill {
                background: #f1f5f9;
                border-radius: 16px;
                display: inline-flex;
                align-items: center;
                padding: 1px;
                border: 1px solid #e2e8f0;
            }

            .comment-actions .c-vote-pill button {
                background: transparent;
                border: none;
                padding: 3px 8px;
                border-radius: 16px;
                color: #64748b;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s ease;
                cursor: pointer;
                font-size: 13px;
            }

            .comment-actions .c-vote-pill button:hover {
                background: #e2e8f0;
                color: #1e293b;
            }

            .comment-actions .c-vote-pill button.active-up {
                color: #ff4500;
            }

            .comment-actions .c-vote-pill button.active-down {
                color: #7193ff;
            }

            .comment-actions .c-vote-pill .c-vote-count {
                font-weight: 700;
                font-size: 12px;
                padding: 0 2px;
                text-align: center;
                color: #1e293b;
            }

            .comment-actions .c-vote-pill .cv-separator {
                width: 1px;
                height: 14px;
                background-color: #cbd5e1;
                margin: 0 1px;
            }

            .comment-actions .c-action-btn {
                background: transparent;
                border: none;
                padding: 4px 10px;
                border-radius: 16px;
                color: #64748b;
                font-size: 12px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                cursor: pointer;
                transition: background 0.15s;
            }

            .comment-actions .c-action-btn:hover {
                background: #f1f5f9;
            }

            /* Inline reply form */
            .inline-reply-form {
                margin-top: 10px;
                padding: 12px;
                background: #f8fafc;
                border-radius: 8px;
                border: 1px solid #e2e8f0;
                display: none;
            }

            .inline-reply-form.show {
                display: block;
            }

            .inline-reply-form textarea {
                width: 100%;
                border: 1px solid #e2e8f0;
                border-radius: 6px;
                padding: 8px 12px;
                font-size: 13px;
                resize: none;
                background: #fff;
                min-height: 60px;
            }

            .inline-reply-form textarea:focus {
                outline: none;
                border-color: #6F7DA4;
                box-shadow: 0 0 0 2px #e0e7ff;
            }

            .inline-reply-form .reply-actions {
                display: flex;
                justify-content: flex-end;
                gap: 8px;
                margin-top: 8px;
            }

            .inline-reply-form .btn-cancel {
                background: transparent;
                border: 1px solid #e2e8f0;
                padding: 4px 14px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
                color: #64748b;
                cursor: pointer;
            }

            .inline-reply-form .btn-cancel:hover {
                background: #f1f5f9;
            }

            .inline-reply-form .btn-reply-submit {
                background: #6F7DA4;
                border: none;
                padding: 4px 16px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
                color: #fff;
                cursor: pointer;
                transition: background 0.2s, opacity 0.2s;
            }

            .inline-reply-form .btn-reply-submit:hover {
                background: #293C79;
            }

            .inline-reply-form .btn-reply-submit:disabled {
                opacity: 0.6;
                cursor: not-allowed;
            }

            .edited-badge {
                font-size: 12px;
                color: #9ca3af;
                font-style: italic;
            }



            .personal-pin-badge-show {
                background: #dbeafe;
                color: #2563eb;
                font-size: 11px;
                font-weight: 700;
                padding: 2px 8px;
                border-radius: 4px;
            }

            /* YouTube style replies */
            .toggle-replies-btn {
                background: transparent;
                border: none;
                color: #3b82f6;
                font-weight: 600;
                font-size: 13px;
                padding: 6px 12px;
                border-radius: 20px;
                margin-top: 8px;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                transition: background 0.2s;
            }

            .toggle-replies-btn:hover {
                background: #eff6ff;
            }

            .toggle-replies-btn svg,
            .toggle-replies-btn>span:first-child {
                transition: transform 0.2s;
            }

            .toggle-replies-btn.open svg,
            .toggle-replies-btn.open>span:first-child {
                transform: rotate(180deg);
            }

            .replies-container {
                display: none;
                margin-top: 12px;
            }

            .replies-container.show {
                display: block;
            }

            .reply-mention {
                color: #3b82f6;
                font-weight: 600;
                margin-right: 4px;
            }
        </style>
    @endpush

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert"
            style="border-radius: 10px; border: none; background: #dcfce7; color: #16a34a; font-weight: 600;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="page-title">
        <a href="{{ route('manajemenmahasiswa.forum.index') }}" class="back-btn">
            <x-manajemenmahasiswa::ui.icon name="arrow-narrow-left" size="20" />
        </a>
        <div>
            <h1>Detail Diskusi</h1>
            <p>Baca postingan dan ikuti diskusinya</p>
        </div>
    </div>

    <div class="forum-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-placeholder">
                    {{ strtoupper(substr($thread->author->name ?? '?', 0, 2)) }}
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h5 class="fw-bold text-dark mb-0">{{ $thread->author->name ?? 'Unknown' }}</h5>
                        @include('manajemenmahasiswa::forum.partials.role-badge', ['roleUser' => $thread->author, 'badgeSize' => '11px'])
                        @if(isset($authorTiers[$thread->user_id]))
                            <span class="badge rounded-pill"
                                style="background: linear-gradient(135deg, #293C79 0%, #6F7DA4 100%); color: #fff; font-size: 10px; font-weight: 600; padding: 3px 8px;"
                                title="{{ $authorTiers[$thread->user_id]['tier_name'] }}">
                                {!! $authorTiers[$thread->user_id]['tier_icon'] !!}
                                Lv.{{ $authorTiers[$thread->user_id]['level'] }} —
                                {{ $authorTiers[$thread->user_id]['tier_name'] }}
                            </span>
                        @endif
                    </div>
                    <span class="text-primary fw-medium" style="font-size: 13px;">•
                        {{ $thread->created_at->diffForHumans() }}</span>
                    @if($thread->isEdited())
                        <span class="edited-badge">(diedit {{ $thread->updated_at->diffForHumans() }})</span>
                    @endif
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="dropdown">
                    <button type="button"
                        class="btn btn-link p-0 text-muted text-decoration-none shadow-none d-flex align-items-center"
                        data-bs-toggle="dropdown">
                        <x-manajemenmahasiswa::ui.icon name="dots" size="20" />
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 8px;">
                        {{-- Edit (owner only) --}}
                        @if($thread->user_id === $user->id)
                            <li>
                                <a href="{{ route('manajemenmahasiswa.forum.edit', $thread->id) }}"
                                    class="dropdown-item d-flex align-items-center gap-2">
                                    <x-manajemenmahasiswa::ui.icon name="file-01" size="14" /> Edit Thread
                                </a>
                            </li>
                        @endif
                        {{-- Lock / Unlock (admin only) --}}
                        @if($user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan']))
                            <li>
                                <form method="POST" action="{{ route('manajemenmahasiswa.forum.lock', $thread->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                        @if($thread->is_locked)
                                            <x-manajemenmahasiswa::ui.icon name="unlocked-01" size="14" /> Unlock Thread
                                        @else
                                            <x-manajemenmahasiswa::ui.icon name="locked-01" size="14" /> Kunci Thread
                                        @endif
                                    </button>
                                </form>
                            </li>
                        @endif
                        {{-- Pin Global (admin only) --}}
                        @if($user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan']))
                            <li>
                                <form method="POST" action="{{ route('manajemenmahasiswa.forum.pin', $thread->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                        @if($thread->is_pinned)
                                            <x-manajemenmahasiswa::ui.icon name="unlocked-01" size="14" /> Unpin Global
                                        @else
                                            <x-manajemenmahasiswa::ui.icon name="bookmark" size="14" /> Pin Global
                                        @endif
                                    </button>
                                </form>
                            </li>
                        @endif
                        {{-- Pin Pribadi --}}
                        <li>
                            <form method="POST"
                                action="{{ route('manajemenmahasiswa.forum.personal_pin', $thread->id) }}">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                    <x-manajemenmahasiswa::ui.icon name="bookmark" size="14" />
                                    @if($isPersonalPinned) Unpin Pribadi @else Pin Pribadi @endif
                                </button>
                            </form>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        {{-- Delete --}}
                        @if($thread->user_id === $user->id)
                            <li>
                                <form method="POST" action="{{ route('manajemenmahasiswa.forum.destroy', $thread->id) }}"
                                    onsubmit="return confirm('Yakin ingin menghapus thread ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                                        <x-manajemenmahasiswa::ui.icon name="minus-circle" size="14" /> Hapus Thread
                                    </button>
                                </form>
                            </li>
                        @elseif($user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan']))
                            <li>
                                <form method="POST" action="{{ route('manajemenmahasiswa.forum.destroy', $thread->id) }}"
                                    onsubmit="return confirm('Yakin ingin menghapus thread ini (sebagai admin)?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                                        <x-manajemenmahasiswa::ui.icon name="minus-circle" size="14" /> Hapus Thread (Admin)
                                    </button>
                                </form>
                            </li>
                        @endif
                        @if($thread->user_id !== $user->id && !$user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan']))
                            <li>
                                <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal"
                                    data-bs-target="#reportModal" data-thread-id="{{ $thread->id }}"
                                    data-thread-title="{{ $thread->judul }}">
                                    <x-manajemenmahasiswa::ui.icon name="alert-triangle" size="14" /> Laporkan Thread
                                </button>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <h4 class="fw-bold text-dark mb-3">{{ $thread->judul }}</h4>

        <div class="text-dark"
            style="font-size: 15px; margin-bottom: 24px; line-height: 1.6; overflow-wrap: break-word;">
            {!! nl2br(strip_tags($thread->konten, '<a><br>')) !!}
        </div>

        {{-- Poll --}}
        @if($thread->poll)
            @include('manajemenmahasiswa::forum._poll', ['poll' => $thread->poll, 'threadId' => $thread->id])
        @endif

        @php
            $mediaUrls = $thread->extractMediaUrls();
        @endphp
        @if(count($mediaUrls) > 0)
            @if(count($mediaUrls) == 1)
                {{-- Single media: show full width --}}
                <div class="mt-3 mb-4"
                    style="width: 100%; border-radius: 12px; border: 1px solid #e5e7eb; background: #f8fafc; overflow: hidden; display: flex; justify-content: center; align-items: center;">
                    @if($mediaUrls[0]['type'] === 'image')
                        <img src="{{ $mediaUrls[0]['url'] }}" alt="Media"
                            style="width: 100%; max-height: 600px; object-fit: contain;">
                    @else
                        <video src="{{ $mediaUrls[0]['url'] }}" controls
                            style="width: 100%; max-height: 600px; object-fit: contain;"></video>
                    @endif
                </div>
            @else
                {{-- Multiple media: carousel --}}
                <div id="threadMediaCarousel" class="carousel slide mt-3 mb-4" data-bs-ride="carousel"
                    style="border-radius: 12px; border: 1px solid #e5e7eb; overflow: hidden; background: #f8fafc;">
                    <div class="carousel-inner">
                        @foreach($mediaUrls as $idx => $media)
                            <div class="carousel-item {{ $idx === 0 ? 'active' : '' }}" style="height: 500px;">
                                @if($media['type'] === 'image')
                                    <img src="{{ $media['url'] }}" class="d-block w-100 h-100" style="object-fit: contain;"
                                        alt="Media {{ $idx + 1 }}">
                                @else
                                    <video src="{{ $media['url'] }}" controls class="d-block w-100 h-100"
                                        style="object-fit: contain;"></video>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#threadMediaCarousel"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"
                            style="filter: invert(1) grayscale(100); background-color: rgba(255,255,255,0.5); border-radius: 50%; padding: 20px;"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#threadMediaCarousel"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"
                            style="filter: invert(1) grayscale(100); background-color: rgba(255,255,255,0.5); border-radius: 50%; padding: 20px;"></span>
                        <span class="visually-hidden">Next</span>
                    </button>

                    <div class="carousel-indicators mb-2">
                        @foreach($mediaUrls as $idx => $media)
                            <button type="button" data-bs-target="#threadMediaCarousel" data-bs-slide-to="{{ $idx }}"
                                class="{{ $idx === 0 ? 'active' : '' }}" aria-current="{{ $idx === 0 ? 'true' : 'false' }}"
                                aria-label="Slide {{ $idx + 1 }}" style="background-color: #293C79;"></button>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

        <!-- Labels -->
        <div class="d-flex gap-2 mb-4 flex-wrap">
            @foreach($thread->getKategoriLabels() as $idx => $lbl)
                @php $colorClass = $thread->getKategoriColors()[$idx] ?? 'tag-gray'; @endphp
                <span class="tag-label {{ $colorClass }}">{{ $lbl }}</span>
            @endforeach
            @if($thread->is_pinned)
                <span class="tag-label"
                    style="background: #fef3c7; color: #d97706; display:inline-flex; align-items:center; gap:4px;">
                    <x-manajemenmahasiswa::ui.icon name="bookmark" size="12" /> Pinned
                </span>
            @endif
            @if($thread->is_locked)
                <span class="tag-label tag-red">🔒 Dikunci</span>
            @endif
        </div>

        <!-- Actions -->
        @php
            $threadVoteKey = \Modules\ManajemenMahasiswa\Models\Thread::class . '_' . $thread->id;
            $threadUserVote = $userVotes[$threadVoteKey] ?? null;
        @endphp
        <div class="post-actions d-flex align-items-center mb-4" id="thread-vote-area">
            <div class="vote-pill shadow-sm">
                <button
                    class="vote-thread-btn {{ $threadUserVote && $threadUserVote->value === 1 ? 'vote-active-up' : '' }}"
                    data-thread-id="{{ $thread->id }}" data-value="1">
                    <svg width="18" height="18" viewBox="0 0 24 24"
                        fill="{{ $threadUserVote && $threadUserVote->value === 1 ? 'currentColor' : 'none' }}"
                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="19" x2="12" y2="5"></line>
                        <polyline points="5 12 12 5 19 12"></polyline>
                    </svg>
                </button>
                <span class="thread-vote-count-{{ $thread->id }}">{{ $thread->vote_count }}</span>
                <div class="v-separator"></div>
                <button
                    class="vote-thread-btn {{ $threadUserVote && $threadUserVote->value === -1 ? 'vote-active-down' : '' }}"
                    data-thread-id="{{ $thread->id }}" data-value="-1">
                    <svg width="18" height="18" viewBox="0 0 24 24"
                        fill="{{ $threadUserVote && $threadUserVote->value === -1 ? 'currentColor' : 'none' }}"
                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <polyline points="19 12 12 19 5 12"></polyline>
                    </svg>
                </button>
            </div>
            <button class="action-btn shadow-sm" style="cursor: default;">
                <x-manajemenmahasiswa::ui.icon name="message-dots-circle" size="18" />
                {{ $thread->comments_count ?? $thread->comment_count }} Komentar
            </button>
            <button class="action-btn shadow-sm share-btn"
                data-url="{{ route('manajemenmahasiswa.forum.show', $thread->id) }}">
                <x-manajemenmahasiswa::ui.icon name="link-01" size="18" />
                Bagikan
            </button>
        </div>

        <!-- Reply Section -->
        @unless($thread->is_locked)
            <div class="reply-form mb-2">
                <form method="POST" action="{{ route('manajemenmahasiswa.forum.comments.store', $thread->id) }}">
                    @csrf
                    <div class="d-flex gap-3">
                        <div class="avatar-placeholder avatar-sm flex-shrink-0">
                            {{ strtoupper(substr($user->name ?? '?', 0, 2)) }}
                        </div>
                        <div class="flex-grow-1">
                            <textarea name="konten" class="form-control mb-3 @error('konten') is-invalid @enderror" rows="3"
                                placeholder="Tulis komentar Anda di sini..." required
                                minlength="3">{{ old('konten') }}</textarea>
                            @error('konten')
                                <div class="invalid-feedback mb-2">{{ $message }}</div>
                            @enderror
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn-post shadow-sm">Kirim Komentar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        @else
            <div class="alert alert-warning" style="border-radius: 8px; border: none; font-size: 14px;">
                🔒 Thread ini sudah dikunci. Tidak bisa menambahkan komentar baru.
            </div>
        @endunless

        <!-- Comments List -->
        <div class="comment-list">
            <h6 class="fw-bold text-dark mb-4">{{ $thread->comments_count ?? $thread->comment_count }} Komentar</h6>

            @forelse($comments as $comment)
                @include('manajemenmahasiswa::forum.partials.comment-thread', ['comment' => $comment, 'depth' => 0])
            @empty
                <div class="text-center py-4" style="color: #9ca3af;">
                    <p class="mb-0">Belum ada komentar.</p>
                </div>
            @endforelse

            <!-- Pagination -->
            @if($comments->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $comments->links() }}
                </div>
            @endif
        </div>
    </div>

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
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="reportModalLabel">
                            <x-manajemenmahasiswa::ui.icon name="alert-triangle" size="20" /> Laporkan Thread
                        </h5>
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

        <script>
            // CSRF token
            const csrfToken = '{{ csrf_token() }}';

            // Vote Thread (AJAX)
            document.querySelectorAll('.vote-thread-btn').forEach(btn => {
                btn.addEventListener('click', async function (e) {
                    e.preventDefault();
                    const threadId = this.dataset.threadId;
                    const value = parseInt(this.dataset.value);
                    const parent = this.closest('#thread-vote-area');

                    try {
                        const res = await fetch(`{{ url('manajemen-mahasiswa/forum') }}/${threadId}/vote`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ value })
                        });

                        const data = await res.json();

                        if (parent) {
                            parent.querySelectorAll('.vote-thread-btn').forEach(b => b.classList.remove('vote-active-up', 'vote-active-down'));
                            const countEl = parent.querySelector(`.thread-vote-count-${threadId}`);
                            countEl.textContent = data.vote_count;

                            if (data.user_vote === 1) {
                                parent.querySelector('.vote-thread-btn[data-value="1"]').classList.add('vote-active-up');
                                parent.querySelector('.vote-thread-btn[data-value="1"] svg').setAttribute('fill', 'currentColor');
                                parent.querySelector('.vote-thread-btn[data-value="-1"] svg').setAttribute('fill', 'none');
                            } else if (data.user_vote === -1) {
                                parent.querySelector('.vote-thread-btn[data-value="-1"]').classList.add('vote-active-down');
                                parent.querySelector('.vote-thread-btn[data-value="-1"] svg').setAttribute('fill', 'currentColor');
                                parent.querySelector('.vote-thread-btn[data-value="1"] svg').setAttribute('fill', 'none');
                            } else {
                                parent.querySelector('.vote-thread-btn[data-value="1"] svg').setAttribute('fill', 'none');
                                parent.querySelector('.vote-thread-btn[data-value="-1"] svg').setAttribute('fill', 'none');
                            }
                        }
                    } catch (err) {
                        console.error('Vote error:', err);
                    }
                });
            });

            // Vote Comment (AJAX)
            document.querySelectorAll('.vote-comment-btn').forEach(btn => {
                btn.addEventListener('click', async function (e) {
                    e.preventDefault();
                    const commentId = this.dataset.commentId;
                    const value = parseInt(this.dataset.value);
                    const parent = this.closest('.c-vote-pill');

                    try {
                        const res = await fetch(`{{ url('manajemen-mahasiswa/forum/comments') }}/${commentId}/vote`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ value })
                        });

                        const data = await res.json();

                        if (parent) {
                            parent.querySelectorAll('.vote-comment-btn').forEach(b => b.classList.remove('active-up', 'active-down'));
                            const countEl = parent.querySelector(`.comment-vote-count-${commentId}`);
                            countEl.textContent = data.vote_count;

                            if (data.user_vote === 1) {
                                parent.querySelector('.vote-comment-btn[data-value="1"]').classList.add('active-up');
                                parent.querySelector('.vote-comment-btn[data-value="1"] svg').setAttribute('fill', 'currentColor');
                                parent.querySelector('.vote-comment-btn[data-value="-1"] svg').setAttribute('fill', 'none');
                            } else if (data.user_vote === -1) {
                                parent.querySelector('.vote-comment-btn[data-value="-1"]').classList.add('active-down');
                                parent.querySelector('.vote-comment-btn[data-value="-1"] svg').setAttribute('fill', 'currentColor');
                                parent.querySelector('.vote-comment-btn[data-value="1"] svg').setAttribute('fill', 'none');
                            } else {
                                parent.querySelector('.vote-comment-btn[data-value="1"] svg').setAttribute('fill', 'none');
                                parent.querySelector('.vote-comment-btn[data-value="-1"] svg').setAttribute('fill', 'none');
                            }
                        }
                    } catch (err) {
                        console.error('Vote error:', err);
                    }
                });
            });

            // Share functionality (Copy Link)
            document.querySelectorAll('.share-btn').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetUrl = this.dataset.url;

                    navigator.clipboard.writeText(targetUrl).then(() => {
                        const originalHtml = this.innerHTML;
                        this.innerHTML = '<span style="font-size: 14px;">✅</span>';
                        setTimeout(() => {
                            this.innerHTML = originalHtml;
                        }, 2000);
                    }).catch(err => {
                        console.error('Failed to copy link: ', err);
                    });
                });
            });

            // Handling Report Modal data
            const reportModal = document.getElementById('reportModal');
            if (reportModal) {
                reportModal.addEventListener('show.bs.modal', function (event) {
                    const btn = event.relatedTarget;
                    reportModal.querySelector('#reportThreadTitle').textContent = `"${btn.dataset.threadTitle}"`;
                    reportModal.querySelector('#reportForm').action = `{{ url('manajemen-mahasiswa/forum') }}/${btn.dataset.threadId}/report`;
                });
            }



            // ---- Toggle Reply Forms (Reddit style) ----
            document.querySelectorAll('.toggle-reply-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const commentId = this.dataset.commentId;
                    const form = document.getElementById(`reply-form-${commentId}`);
                    if (!form) return;
                    // Close all other open reply forms
                    document.querySelectorAll('.inline-reply-form.show').forEach(f => {
                        if (f !== form) f.classList.remove('show');
                    });
                    form.classList.toggle('show');
                    if (form.classList.contains('show')) {
                        form.querySelector('textarea').focus();
                    }
                });
            });

            document.querySelectorAll('.cancel-reply-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const commentId = this.dataset.commentId;
                    const form = document.getElementById(`reply-form-${commentId}`);
                    if (form) {
                        form.classList.remove('show');
                        form.querySelector('textarea').value = '';
                    }
                });
            });

            // ---- Toggle Edit Forms ----
            document.querySelectorAll('.toggle-edit-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const commentId = this.dataset.commentId;
                    const form = document.getElementById(`edit-form-${commentId}`);
                    if (!form) return;
                    // Close all other open inline forms (reply or edit)
                    document.querySelectorAll('.inline-reply-form.show').forEach(f => {
                        if (f !== form) f.classList.remove('show');
                    });
                    form.classList.toggle('show');
                    if (form.classList.contains('show')) {
                        form.querySelector('textarea').focus();
                    }
                });
            });

            document.querySelectorAll('.cancel-edit-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const commentId = this.dataset.commentId;
                    const form = document.getElementById(`edit-form-${commentId}`);
                    if (form) {
                        form.classList.remove('show');
                    }
                });
            });

            // ---- Toggle Replies Visibility (YouTube style) ----
            document.querySelectorAll('.toggle-replies-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const targetId = this.dataset.target;
                    const container = document.getElementById(targetId);
                    const textSpan = this.querySelector('.toggle-text');

                    if (container) {
                        const isOpen = container.classList.contains('show');
                        if (isOpen) {
                            container.classList.remove('show');
                            this.classList.remove('open');
                        } else {
                            container.classList.add('show');
                            this.classList.add('open');
                        }
                    }
                });
            });

            // ---- Double-Post Prevention ----
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function (event) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (!submitBtn || submitBtn.disabled) {
                        if (submitBtn && submitBtn.disabled) { event.preventDefault(); return; }
                        return;
                    }
                    submitBtn.disabled = true;
                    // Add spinner for btn-post or btn-reply-submit
                    if (submitBtn.classList.contains('btn-post') || submitBtn.classList.contains('btn-reply-submit')) {
                        const origText = submitBtn.textContent;
                        submitBtn.dataset.origText = origText;
                        submitBtn.innerHTML = '<span class="spinner"></span> Mengirim...';
                    }
                });
            });

            // ---- Enter to Submit ----
            document.querySelectorAll('textarea').forEach(textarea => {
                textarea.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        const form = this.closest('form');
                        if (form) {
                            // Trigger submit event
                            const submitEvent = new Event('submit', { cancelable: true, bubbles: true });
                            form.dispatchEvent(submitEvent);

                            if (!submitEvent.defaultPrevented) {
                                form.submit();
                            }
                        }
                    }
                });
            });

        </script>
    @endpush

</x-manajemenmahasiswa::layouts.forum-layout>