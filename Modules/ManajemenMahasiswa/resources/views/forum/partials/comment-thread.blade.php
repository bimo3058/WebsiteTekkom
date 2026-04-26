{{-- YouTube-style comment thread partial --}}
@php 
    $depth = $depth ?? 0; 
    $repliedUser = $comment->getRepliedToUsername();
@endphp

<div class="comment-item" style="{{ $depth > 0 ? 'margin-bottom:12px; padding-bottom:12px; border-bottom: 1px solid #f8fafc;' : '' }}">
    <div class="d-flex gap-2">
        <div class="avatar-placeholder avatar-sm flex-shrink-0"
             style="background-color: {{ $comment->is_best_answer ? '#dcfce7' : ($depth === 0 ? '#fce7f3' : '#f3f4f6') }};
                    color: {{ $comment->is_best_answer ? '#16a34a' : ($depth === 0 ? '#db2777' : '#6b7280') }};
                    width: {{ $depth === 0 ? '36px' : '28px' }}; height: {{ $depth === 0 ? '36px' : '28px' }};
                    font-size: {{ $depth === 0 ? '14px' : '11px' }};">
            {{ strtoupper(substr($comment->author->name ?? '?', 0, 2)) }}
        </div>
        <div class="flex-grow-1 min-w-0">
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                <span class="fw-bold text-dark" style="font-size: {{ $depth === 0 ? '14px' : '13px' }};">{{ $comment->author->name ?? 'Unknown' }}</span>
                @if(isset($authorTiers[$comment->user_id]))
                    <span class="badge rounded-pill" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: #fff; font-size: 9px; font-weight: 600; padding: 2px 6px;" title="{{ $authorTiers[$comment->user_id]['tier_name'] }}">
                        {{ $authorTiers[$comment->user_id]['tier_icon'] }} Lv.{{ $authorTiers[$comment->user_id]['level'] }}
                    </span>
                @endif
                <span class="text-muted" style="font-size: {{ $depth === 0 ? '12px' : '11px' }};">• {{ $comment->created_at->diffForHumans() }}</span>
                @if($comment->is_best_answer)
                    <span class="best-answer-badge">⭐ Jawaban Terbaik</span>
                @endif
            </div>
            
            <p class="text-dark mb-1" style="font-size: {{ $depth === 0 ? '14px' : '13px' }}; line-height: 1.5;">
                @if($repliedUser)
                    <span class="reply-mention">{{ '@' . $repliedUser }}</span>
                @endif
                {!! nl2br(e($comment->konten)) !!}
            </p>

            {{-- Comment Actions --}}
            @php
                $commentVoteKey = \Modules\ManajemenMahasiswa\Models\Comment::class . '_' . $comment->id;
                $commentUserVote = $userVotes[$commentVoteKey] ?? null;
                $isAdmin = $user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan', 'gpm']);
            @endphp
            <div class="comment-actions">
                <div class="c-vote-pill">
                    <button class="vote-comment-btn {{ $commentUserVote && $commentUserVote->value === 1 ? 'active-up' : '' }}" data-comment-id="{{ $comment->id }}" data-value="1">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>
                    </button>
                    <span class="c-vote-count comment-vote-count-{{ $comment->id }}">{{ $comment->vote_count }}</span>
                    <button class="vote-comment-btn {{ $commentUserVote && $commentUserVote->value === -1 ? 'active-down' : '' }}" data-comment-id="{{ $comment->id }}" data-value="-1">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg>
                    </button>
                </div>
                @unless($thread->is_locked)
                <button type="button" class="c-action-btn toggle-reply-btn" data-comment-id="{{ $comment->id }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    Balas
                </button>
                @endunless
                
                @if($comment->user_id === $user->id)
                    <button type="button" class="c-action-btn toggle-edit-btn" data-comment-id="{{ $comment->id }}">
                        ✏️ Edit
                    </button>
                @endif
                
                @if($comment->user_id === $user->id || $isAdmin)
                    <form method="POST" action="{{ route('manajemenmahasiswa.forum.comments.destroy', $comment->id) }}" style="display:inline;" onsubmit="return confirm('Hapus komentar ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="c-action-btn" style="color:#ef4444;">Hapus</button>
                    </form>
                @endif
                {{-- Best Answer Button (hanya terlihat oleh OP, untuk komentar top-level yang belum ditandai) --}}
                @if($thread->user_id === $user->id && !$comment->is_best_answer && $depth === 0 && $comment->user_id !== $user->id)
                    <form method="POST" action="{{ route('manajemenmahasiswa.forum.best_answer', [$thread->id, $comment->id]) }}" style="display:inline;" onsubmit="return confirm('Tandai komentar ini sebagai Jawaban Terbaik?')">
                        @csrf
                        <button type="submit" class="c-action-btn" style="color:#16a34a; font-weight:600;">
                            ✅ Best Answer
                        </button>
                    </form>
                @endif
            </div>

            {{-- Inline Reply Form --}}
            @unless($thread->is_locked)
            <div class="inline-reply-form" id="reply-form-{{ $comment->id }}">
                <form method="POST" action="{{ route('manajemenmahasiswa.forum.comments.store', $thread->id) }}" class="reply-submit-form">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                    <textarea name="konten" rows="2" placeholder="Tulis balasan..." required minlength="3"></textarea>
                    <div class="reply-actions">
                        <button type="button" class="btn-cancel cancel-reply-btn" data-comment-id="{{ $comment->id }}">Batal</button>
                        <button type="submit" class="btn-reply-submit">Balas</button>
                    </div>
                </form>
            </div>
            @endunless

            {{-- Inline Edit Form --}}
            @if($comment->user_id === $user->id)
            <div class="inline-reply-form inline-edit-form" id="edit-form-{{ $comment->id }}">
                <form method="POST" action="{{ route('manajemenmahasiswa.forum.comments.update', $comment->id) }}" class="edit-submit-form">
                    @csrf
                    @method('PUT')
                    <textarea name="konten" rows="2" placeholder="Edit komentar..." required minlength="3">{{ $comment->konten }}</textarea>
                    <div class="reply-actions">
                        <button type="button" class="btn-cancel cancel-edit-btn" data-comment-id="{{ $comment->id }}">Batal</button>
                        <button type="submit" class="btn-reply-submit">Simpan Edit</button>
                    </div>
                </form>
            </div>
            @endif

            {{-- YouTube Style Flat Replies --}}
            @if($depth === 0)
                @php
                    $flatReplies = $comment->getFlattenedReplies();
                @endphp
                @if($flatReplies->isNotEmpty())
                    <button type="button" class="toggle-replies-btn" data-target="replies-container-{{ $comment->id }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        <span class="toggle-text">{{ $flatReplies->count() }} balasan</span>
                    </button>

                    <div class="replies-container" id="replies-container-{{ $comment->id }}" style="margin-left: 18px; border-left: 2px solid #e5e7eb; padding-left: 20px;">
                        @foreach($flatReplies as $replyComment)
                            @include('manajemenmahasiswa::forum.partials.comment-thread', ['comment' => $replyComment, 'depth' => 1])
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
