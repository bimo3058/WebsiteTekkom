<x-manajemenmahasiswa::layouts.mahasiswa>

@push('styles')
<style>
    .forum-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        margin-bottom: 20px;
    }
    .avatar-placeholder {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background-color: #e0e7ff;
        color: #4f46e5;
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
    .post-actions button {
        background: #f3f4f6;
        border: none;
        padding: 8px 16px;
        border-radius: 20px;
        color: #4b5563;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-right: 12px;
        transition: all 0.15s;
        cursor: pointer;
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
    .tag-label {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
    }
    .tag-green { background: #dcfce7; color: #16a34a; }
    .tag-red { background: #fee2e2; color: #dc2626; }
    .tag-gray { background: #f3f4f6; color: #6b7280; }
    .tag-blue { background: #dbeafe; color: #2563eb; }
    .tag-purple { background: #f3e8ff; color: #7c3aed; }

    .comment-list {
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid #e5e7eb;
    }

    .comment-item {
        margin-bottom: 20px;
        padding-bottom: 20px;
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
        border-color: #6366f1;
    }
    .btn-post {
        background-color: #818cf8;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 8px 24px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-post:hover {
        background-color: #6366f1;
        color: white;
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
        margin-left: 48px;
        padding-left: 16px;
        border-left: 2px solid #e5e7eb;
        margin-top: 12px;
    }

    .vote-btn {
        background: none;
        border: none;
        padding: 2px 6px;
        cursor: pointer;
        font-size: 16px;
        color: #9ca3af;
        transition: all 0.15s;
    }
    .vote-btn:hover { color: #4f46e5; }
    .vote-btn.active-up { color: #16a34a; }
    .vote-btn.active-down { color: #dc2626; }
</style>
@endpush

{{-- Flash Message --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px; border: none; background: #dcfce7; color: #16a34a; font-weight: 600;">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="mb-4 d-flex align-items-center gap-3">
    <a href="{{ route('manajemenmahasiswa.forum.index') }}" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
        <span aria-hidden="true">&larr;</span>
    </a>
    <div>
        <h3 class="fw-bold mb-0 text-dark">Detail Diskusi</h3>
        <p class="text-muted text-sm fw-medium mb-0">Baca postingan dan ikuti diskusinya</p>
    </div>
</div>

<div class="forum-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar-placeholder">
                {{ strtoupper(substr($thread->author->name ?? '?', 0, 2)) }}
            </div>
            <div>
                <h5 class="fw-bold text-dark mb-0">{{ $thread->author->name ?? 'Unknown' }}</h5>
                <span class="text-primary fw-medium" style="font-size: 13px;">• {{ $thread->created_at->diffForHumans() }}</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="dropdown">
                <span class="text-muted" style="cursor: pointer; font-size: 24px; line-height: 1;"
                      data-bs-toggle="dropdown">⋯</span>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 8px;">
                    @if($thread->user_id === $user->id)
                        <li>
                            <form method="POST" action="{{ route('manajemenmahasiswa.forum.destroy', $thread->id) }}"
                                  onsubmit="return confirm('Yakin ingin menghapus thread ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger">🗑️ Hapus Thread</button>
                            </form>
                        </li>
                    @else
                        @if($user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm']))
                            <li>
                                <form method="POST" action="{{ route('manajemenmahasiswa.forum.pin', $thread->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="dropdown-item">
                                        @if($thread->is_pinned) 🔓 Unpin Thread @else 📌 Pin Thread @endif
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('manajemenmahasiswa.forum.destroy', $thread->id) }}"
                                      onsubmit="return confirm('Yakin ingin menghapus thread ini (sebagai admin)?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">🗑️ Hapus Thread (Admin)</button>
                                </form>
                            </li>
                        @endif
                        <li>
                            <button type="button" class="dropdown-item text-danger"
                                    data-bs-toggle="modal" data-bs-target="#reportModal"
                                    data-thread-id="{{ $thread->id }}"
                                    data-thread-title="{{ $thread->judul }}">
                                🚩 Laporkan Thread
                            </button>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <h4 class="fw-bold text-dark mb-3">{{ $thread->judul }}</h4>

    <div class="text-dark" style="font-size: 15px; margin-bottom: 24px; line-height: 1.6; overflow-wrap: break-word;">
        {!! nl2br(strip_tags($thread->konten, '<img><video><source><a><br>')) !!}
    </div>

    <!-- Labels -->
    <div class="d-flex gap-2 mb-4">
        <span class="tag-label {{ $thread->kategoriColor() }}">{{ $thread->kategoriLabel() }}</span>
        @if($thread->is_pinned)
            <span class="tag-label" style="background: #fef3c7; color: #d97706;">📌 Pinned</span>
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
        <button class="shadow-sm vote-thread-btn {{ $threadUserVote && $threadUserVote->value === 1 ? 'vote-active-up' : '' }}"
                data-thread-id="{{ $thread->id }}" data-value="1">
            <span class="me-1" style="font-size: 16px;">↑</span>
        </button>
        <span class="fw-bold text-dark mx-1" id="thread-vote-count" style="font-size: 15px;">{{ $thread->vote_count }}</span>
        <button class="shadow-sm vote-thread-btn {{ $threadUserVote && $threadUserVote->value === -1 ? 'vote-active-down' : '' }}"
                data-thread-id="{{ $thread->id }}" data-value="-1">
            <span style="font-size: 16px;">↓</span>
        </button>
        <button class="shadow-sm ms-2" style="cursor: default;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg> {{ $thread->comments_count ?? $thread->comment_count }} Komentar
        </button>
        <button class="shadow-sm ms-2 share-btn" data-url="{{ route('manajemenmahasiswa.forum.show', $thread->id) }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
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
                    <textarea name="konten" class="form-control mb-3 @error('konten') is-invalid @enderror"
                              rows="3" placeholder="Tulis komentar Anda di sini..." required minlength="3">{{ old('konten') }}</textarea>
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
        <div class="comment-item">
            <div class="d-flex gap-3">
                <div class="avatar-placeholder avatar-sm flex-shrink-0"
                     style="background-color: {{ $comment->is_best_answer ? '#dcfce7' : '#fce7f3' }};
                            color: {{ $comment->is_best_answer ? '#16a34a' : '#db2777' }};">
                    {{ strtoupper(substr($comment->author->name ?? '?', 0, 2)) }}
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="fw-bold text-dark" style="font-size: 14px;">{{ $comment->author->name ?? 'Unknown' }}</span>
                        <span class="text-muted" style="font-size: 12px;">• {{ $comment->created_at->diffForHumans() }}</span>
                        @if($comment->is_best_answer)
                            <span class="best-answer-badge">⭐ Jawaban Terbaik</span>
                        @endif
                    </div>
                    <p class="text-dark mb-2" style="font-size: 14px; line-height: 1.5;">
                        {!! nl2br(e($comment->konten)) !!}
                    </p>
                    <div class="d-flex align-items-center gap-3">
                        @php
                            $commentVoteKey = \Modules\ManajemenMahasiswa\Models\Comment::class . '_' . $comment->id;
                            $commentUserVote = $userVotes[$commentVoteKey] ?? null;
                        @endphp
                        <button class="vote-btn vote-comment-btn {{ $commentUserVote && $commentUserVote->value === 1 ? 'active-up' : '' }}"
                                data-comment-id="{{ $comment->id }}" data-value="1">
                            ↑ <span class="comment-vote-count-{{ $comment->id }}">{{ $comment->vote_count }}</span>
                        </button>
                        <button class="vote-btn vote-comment-btn {{ $commentUserVote && $commentUserVote->value === -1 ? 'active-down' : '' }}"
                                data-comment-id="{{ $comment->id }}" data-value="-1">
                            ↓
                        </button>
                        @if($comment->user_id === $user->id || $thread->user_id === $user->id)
                            <form method="POST" action="{{ route('manajemenmahasiswa.forum.comments.destroy', $comment->id) }}"
                                  style="display: inline;" onsubmit="return confirm('Hapus komentar ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-link p-0 text-muted fw-bold text-decoration-none" style="font-size: 13px;">
                                    Hapus
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Nested Replies --}}
                    @if($comment->replies->isNotEmpty())
                        @foreach($comment->replies as $reply)
                            <div class="reply-item mt-3">
                                <div class="d-flex gap-3">
                                    <div class="avatar-placeholder avatar-sm flex-shrink-0" style="background-color: #f3f4f6; color: #6b7280; width: 28px; height: 28px; font-size: 11px;">
                                        {{ strtoupper(substr($reply->author->name ?? '?', 0, 2)) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="fw-bold text-dark" style="font-size: 13px;">{{ $reply->author->name ?? 'Unknown' }}</span>
                                            <span class="text-muted" style="font-size: 11px;">• {{ $reply->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-dark mb-1" style="font-size: 13px; line-height: 1.4;">
                                            {!! nl2br(e($reply->konten)) !!}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
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
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert" style="border-radius: 10px; border: none; font-weight: 600;">
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
                        <p class="text-muted" style="font-size: 14px;">Apakah thread <strong id="reportThreadTitle"></strong> melanggar panduan komunitas?</p>
                        
                        <div class="mb-3">
                            <label for="alasan" class="form-label fw-bold" style="font-size: 14px;">Alasan Pelaporan <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="alasan" id="alasan" rows="4" placeholder="Tulis alasan spesifik (misal: SARA, Spam, Hoax)..." required minlength="5"></textarea>
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
    // CSRF token
    const csrfToken = '{{ csrf_token() }}';

    // Vote Thread (AJAX)
    document.querySelectorAll('.vote-thread-btn').forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            const threadId = this.dataset.threadId;
            const value = parseInt(this.dataset.value);

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

                // Update count
                document.getElementById('thread-vote-count').textContent = data.vote_count;

                // Update button states
                document.querySelectorAll('.vote-thread-btn').forEach(b => {
                    b.classList.remove('vote-active-up', 'vote-active-down');
                });

                if (data.user_vote === 1) {
                    document.querySelector('.vote-thread-btn[data-value="1"]').classList.add('vote-active-up');
                } else if (data.user_vote === -1) {
                    document.querySelector('.vote-thread-btn[data-value="-1"]').classList.add('vote-active-down');
                }
            } catch (err) {
                console.error('Vote error:', err);
            }
        });
    });

    // Vote Comment (AJAX)
    document.querySelectorAll('.vote-comment-btn').forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            const commentId = this.dataset.commentId;
            const value = parseInt(this.dataset.value);

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

                // Update count
                document.querySelectorAll(`.comment-vote-count-${commentId}`).forEach(el => {
                    el.textContent = data.vote_count;
                });

                // Update button states for this comment
                const buttons = this.parentElement.querySelectorAll('.vote-comment-btn');
                buttons.forEach(b => b.classList.remove('active-up', 'active-down'));

                if (data.user_vote === 1) {
                    this.parentElement.querySelector('[data-value="1"]').classList.add('active-up');
                } else if (data.user_vote === -1) {
                    this.parentElement.querySelector('[data-value="-1"]').classList.add('active-down');
                }
            } catch (err) {
                console.error('Vote error:', err);
            }
        });
    });

    // Share functionality (Copy Link)
    document.querySelectorAll('.share-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
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
            const button = event.relatedTarget;
            const threadId = button.getAttribute('data-thread-id');
            const threadTitle = button.getAttribute('data-thread-title');
            
            const modalTitleDisplay = reportModal.querySelector('#reportThreadTitle');
            const form = reportModal.querySelector('#reportForm');
            
            modalTitleDisplay.textContent = `"${threadTitle}"`;
            form.action = `{{ url('manajemen-mahasiswa/forum') }}/${threadId}/report`;
        });
    }
</script>
@endpush

</x-manajemenmahasiswa::layouts.mahasiswa>
