{{-- Recursive comment thread partial (Reddit-style nesting) --}}
@php $depth = $depth ?? 0; @endphp
<div class="comment-item" style="{{ $depth > 0 ? 'margin-left:'.min($depth * 20, 80).'px; padding-left:14px; border-left:2px solid ' . ($depth === 1 ? '#e2e8f0' : ($depth === 2 ? '#f1f5f9' : '#f8fafc')) . '; margin-bottom:12px; padding-bottom:12px;' : '' }}">
    <div class="d-flex gap-2">
        <div class="avatar-placeholder avatar-sm flex-shrink-0"
             style="background-color: {{ $comment->is_best_answer ? '#dcfce7' : ($depth === 0 ? '#fce7f3' : '#f3f4f6') }};
                    color: {{ $comment->is_best_answer ? '#16a34a' : ($depth === 0 ? '#db2777' : '#6b7280') }};
                    width: {{ $depth === 0 ? '36px' : '28px' }}; height: {{ $depth === 0 ? '36px' : '28px' }};
                    font-size: {{ $depth === 0 ? '14px' : '11px' }};">
            {{ strtoupper(substr($comment->author->name ?? '?', 0, 2)) }}
        </div>
        <div class="flex-grow-1 min-w-0">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="fw-bold text-dark" style="font-size: {{ $depth === 0 ? '14px' : '13px' }};">{{ $comment->author->name ?? 'Unknown' }}</span>
                <span class="text-muted" style="font-size: {{ $depth === 0 ? '12px' : '11px' }};">• {{ $comment->created_at->diffForHumans() }}</span>
                @if($comment->is_best_answer)
                    <span class="best-answer-badge">⭐ Jawaban Terbaik</span>
                @endif
            </div>
            <p class="text-dark mb-1" style="font-size: {{ $depth === 0 ? '14px' : '13px' }}; line-height: 1.5;">
                {!! nl2br(e($comment->konten)) !!}
            </p>

            {{-- Comment Actions --}}
            @php
                $commentVoteKey = \Modules\ManajemenMahasiswa\Models\Comment::class . '_' . $comment->id;
                $commentUserVote = $userVotes[$commentVoteKey] ?? null;
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
                @if($comment->user_id === $user->id || $thread->user_id === $user->id)
                    <form method="POST" action="{{ route('manajemenmahasiswa.forum.comments.destroy', $comment->id) }}" style="display:inline;" onsubmit="return confirm('Hapus komentar ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="c-action-btn" style="color:#ef4444;">Hapus</button>
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

            {{-- Recursive Nested Replies --}}
            @if($comment->allReplies && $comment->allReplies->isNotEmpty())
                @foreach($comment->allReplies as $childComment)
                    @include('manajemenmahasiswa::forum.partials.comment-thread', ['comment' => $childComment, 'depth' => $depth + 1])
                @endforeach
            @endif
        </div>
    </div>
</div>
