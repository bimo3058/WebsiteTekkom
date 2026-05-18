@php
    $totalVotes    = $poll->totalVotes();
    $isClosed      = $poll->isClosed();
    $userVotedId   = $poll->userVotedOptionId();
    $hasVoted      = $userVotedId !== null;
    $showResults   = $hasVoted || $isClosed;
    $csrfToken     = csrf_token();
    $voteUrl       = route('manajemenmahasiswa.forum.poll.vote', $threadId);
    $closeUrl      = route('manajemenmahasiswa.forum.poll.close', $threadId);
    $isOwner       = isset($threadOwnerId) && auth()->id() === $threadOwnerId;
@endphp

<div class="poll-container"
     id="poll-{{ $poll->id }}"
     data-is-open="{{ $isClosed ? '0' : '1' }}"
     data-voted-option="{{ $userVotedId ?? '' }}"
     data-vote-url="{{ $voteUrl }}"
     style="background:#f8fafc;border:1.5px solid #e5e7eb;border-radius:14px;padding:20px 22px;margin-bottom:22px;">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
        <div style="display:flex;align-items:center;gap:8px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#293C79" stroke-width="2.5"
                 stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <path d="M9 9h6M9 12h6M9 15h4"/>
            </svg>
            <span style="font-size:13px;font-weight:700;color:#293C79;">POLL</span>
        </div>
        <span class="poll-total-votes" style="font-size:12px;color:#9ca3af;font-weight:500;">
            {{ number_format($totalVotes) }} suara
        </span>
    </div>

    {{-- Options --}}
    <div class="poll-options" id="pollOptions-{{ $poll->id }}">
        @foreach($poll->options as $option)
            @php
                $pct     = $option->percentage($totalVotes);
                $isVoted = $userVotedId === $option->id;
                // Kursor: pointer jika poll terbuka (termasuk setelah vote → bisa ganti)
                $cursor  = $isClosed ? 'default' : 'pointer';
            @endphp
            <div class="poll-option {{ $showResults ? 'show-result' : '' }} {{ $isVoted ? 'user-voted' : '' }}"
                 data-option-id="{{ $option->id }}"
                 data-poll-id="{{ $poll->id }}"
                 style="position:relative;margin-bottom:10px;border-radius:10px;overflow:hidden;
                        border:1.5px solid {{ $isVoted ? '#293C79' : '#e5e7eb' }};
                        background:#fff;cursor:{{ $cursor }};transition:all 0.2s;">

                {{-- Progress bar --}}
                @if($showResults)
                    <div class="poll-bar"
                         style="position:absolute;top:0;left:0;height:100%;width:{{ $pct }}%;
                                background:{{ $isVoted ? 'rgba(41,60,121,0.12)' : 'rgba(0,0,0,0.04)' }};
                                transition:width 0.6s ease;border-radius:8px 0 0 8px;pointer-events:none;">
                    </div>
                @endif

                <div style="position:relative;display:flex;align-items:center;justify-content:space-between;
                             padding:11px 16px;z-index:1;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        {{-- Icon: centang jika voted, radio jika belum ada hasil, kosong jika hasil tapi bukan pilihan --}}
                        @if($isVoted)
                            <svg class="poll-check-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                 stroke="#293C79" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        @elseif(!$showResults)
                            <span class="poll-radio"
                                  style="width:16px;height:16px;border-radius:50%;border:2px solid #d1d5db;
                                         display:inline-block;flex-shrink:0;"></span>
                        @else
                            <span class="poll-check-icon" style="width:16px;display:inline-block;"></span>
                        @endif
                        <span class="poll-option-label"
                              style="font-size:14px;font-weight:{{ $isVoted ? '700' : '500' }};
                                     color:{{ $isVoted ? '#293C79' : '#374151' }};">
                            {{ $option->text }}
                        </span>
                    </div>
                    <span class="poll-pct"
                          style="font-size:13px;font-weight:700;white-space:nowrap;margin-left:8px;
                                 color:{{ $isVoted ? '#293C79' : '#6b7280' }};
                                 {{ $showResults ? '' : 'display:none;' }}">
                        {{ $pct }}%
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Footer --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:8px;flex-wrap:wrap;gap:6px;">
        <span class="poll-status-label" style="font-size:12px;color:#9ca3af;">
            @if($isClosed)
                <span class="poll-closed-badge" style="background:#fee2e2;color:#dc2626;padding:2px 8px;border-radius:20px;font-weight:600;font-size:11px;">
                    Poll ditutup
                </span>
                @if($poll->expires_at)
                    &middot; {{ $poll->expires_at->diffForHumans() }}
                @endif
            @elseif($poll->expires_at)
                Berakhir {{ $poll->expires_at->diffForHumans() }}
            @else
                Tidak ada batas waktu
            @endif
        </span>
        <div style="display:flex;align-items:center;gap:8px;">
            @if($hasVoted && !$isClosed)
                <span class="poll-change-hint" style="font-size:12px;color:#9ca3af;">
                    Klik opsi lain untuk ganti pilihan
                </span>
            @endif
            @if($isOwner)
                <button class="poll-toggle-btn"
                        data-poll-id="{{ $poll->id }}"
                        data-close-url="{{ $closeUrl }}"
                        data-is-closed="{{ $isClosed ? '1' : '0' }}"
                        style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;border:1.5px solid;cursor:pointer;background:transparent;transition:all 0.2s;
                               {{ $isClosed ? 'color:#16a34a;border-color:#16a34a;' : 'color:#dc2626;border-color:#dc2626;' }}">
                    {{ $isClosed ? 'Buka Polling' : 'Tutup Polling' }}
                </button>
            @endif
        </div>
    </div>
</div>

@once
<style>
    /* Hover hanya untuk poll yang terbuka */
    .poll-container[data-is-open="1"] .poll-option:hover {
        border-color: #a5b4fc !important;
        background: #f5f3ff !important;
    }
    .poll-container[data-is-open="1"] .poll-option.user-voted:hover {
        border-color: #293C79 !important;
        background: #E7E8F0 !important;
    }
    .poll-container[data-is-open="0"] .poll-option {
        cursor: default !important;
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Toggle close/open poll (owner only)
    document.querySelectorAll('.poll-toggle-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const pollId   = this.dataset.pollId;
            const closeUrl = this.dataset.closeUrl;
            const container = document.getElementById('poll-' + pollId);
            btn.disabled = true;
            btn.style.opacity = '0.6';

            fetch(closeUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ $csrfToken }}',
                    'Accept': 'application/json',
                },
            })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                btn.style.opacity = '';
                if (!data.success) {
                    alert(data.error || 'Gagal mengubah status polling.');
                    return;
                }

                const nowClosed = data.is_closed;

                // Update container attribute
                container.dataset.isOpen = nowClosed ? '0' : '1';
                btn.dataset.isClosed = nowClosed ? '1' : '0';

                // Update button label & color
                if (nowClosed) {
                    btn.textContent = 'Buka Polling';
                    btn.style.color = '#16a34a';
                    btn.style.borderColor = '#16a34a';
                } else {
                    btn.textContent = 'Tutup Polling';
                    btn.style.color = '#dc2626';
                    btn.style.borderColor = '#dc2626';
                }

                // Update status label
                const statusLabel = container.querySelector('.poll-status-label');
                if (statusLabel) {
                    let badge = statusLabel.querySelector('.poll-closed-badge');
                    if (nowClosed) {
                        if (!badge) {
                            badge = document.createElement('span');
                            badge.className = 'poll-closed-badge';
                            badge.style.cssText = 'background:#fee2e2;color:#dc2626;padding:2px 8px;border-radius:20px;font-weight:600;font-size:11px;';
                            statusLabel.prepend(badge);
                        }
                        badge.textContent = 'Poll ditutup';
                    } else {
                        if (badge) badge.remove();
                    }
                }

                // Disable/enable option clicks
                container.querySelectorAll('.poll-option').forEach(function (opt) {
                    opt.style.cursor = nowClosed ? 'default' : 'pointer';
                    opt.style.pointerEvents = nowClosed ? 'none' : '';
                });

                // Hide change hint if now closed
                const hint = container.querySelector('.poll-change-hint');
                if (hint) hint.style.display = nowClosed ? 'none' : '';
            })
            .catch(() => {
                btn.disabled = false;
                btn.style.opacity = '';
                alert('Terjadi kesalahan. Coba lagi.');
            });
        });
    });

    // Attach listener ke SEMUA opsi dalam poll yang TERBUKA
    document.querySelectorAll('.poll-container[data-is-open="1"] .poll-option').forEach(function (el) {
        el.addEventListener('click', function () {
            const optionId  = parseInt(this.dataset.optionId);
            const pollId    = this.dataset.pollId;
            const container = document.getElementById('poll-' + pollId);
            const voteUrl   = container.dataset.voteUrl;

            const currentVotedId = parseInt(container.dataset.votedOption) || null;

            // Klik opsi yang sama → abaikan
            if (currentVotedId === optionId) return;

            // Nonaktifkan sementara selama fetch
            container.querySelectorAll('.poll-option').forEach(o => {
                o.style.pointerEvents = 'none';
                o.style.opacity = '0.7';
            });

            fetch(voteUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ $csrfToken }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ option_id: optionId }),
            })
            .then(r => r.json())
            .then(data => {
                // Re-aktifkan semua opsi
                container.querySelectorAll('.poll-option').forEach(o => {
                    o.style.pointerEvents = '';
                    o.style.opacity = '';
                });

                if (!data.success) {
                    alert(data.error || 'Gagal memilih.');
                    return;
                }

                // Simpan option yang baru dipilih
                container.dataset.votedOption = data.voted_option_id;

                // Update total suara
                container.querySelector('.poll-total-votes').textContent =
                    data.total_votes.toLocaleString('id-ID') + ' suara';

                // Update hint footer
                const hint = container.querySelector('.poll-change-hint');
                if (hint) hint.style.display = '';
                else {
                    const footer = container.querySelector('[style*="margin-top:8px"]');
                    if (footer) {
                        let hintEl = document.createElement('span');
                        hintEl.className = 'poll-change-hint';
                        hintEl.style.cssText = 'font-size:12px;color:#9ca3af;';
                        hintEl.textContent = 'Klik opsi lain untuk ganti pilihan';
                        footer.appendChild(hintEl);
                    }
                }

                // Update tiap opsi
                container.querySelectorAll('.poll-option').forEach(function (optEl) {
                    const oid   = parseInt(optEl.dataset.optionId);
                    const match = data.options.find(o => o.id === oid);
                    if (!match) return;

                    const isVoted = oid === data.voted_option_id;

                    // Tandai class
                    optEl.classList.add('show-result');
                    optEl.classList.toggle('user-voted', isVoted);

                    // Border
                    optEl.style.border = '1.5px solid ' + (isVoted ? '#293C79' : '#e5e7eb');

                    // Progress bar
                    let bar = optEl.querySelector('.poll-bar');
                    if (!bar) {
                        bar = document.createElement('div');
                        bar.className = 'poll-bar';
                        bar.style.cssText = 'position:absolute;top:0;left:0;height:100%;transition:width 0.6s ease;border-radius:8px 0 0 8px;pointer-events:none;';
                        optEl.insertBefore(bar, optEl.firstChild);
                    }
                    bar.style.width      = match.percentage + '%';
                    bar.style.background = isVoted ? 'rgba(41,60,121,0.12)' : 'rgba(0,0,0,0.04)';

                    // Icon: centang jika voted, spacer jika tidak
                    const iconEl = optEl.querySelector('.poll-check-icon');
                    if (iconEl) {
                        iconEl.outerHTML = isVoted
                            ? `<svg class="poll-check-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#293C79" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`
                            : `<span class="poll-check-icon" style="width:16px;display:inline-block;"></span>`;
                    }
                    const radioEl = optEl.querySelector('.poll-radio');
                    if (radioEl) {
                        radioEl.outerHTML = isVoted
                            ? `<svg class="poll-check-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#293C79" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`
                            : `<span class="poll-check-icon" style="width:16px;display:inline-block;"></span>`;
                    }

                    // Label style
                    const labelEl = optEl.querySelector('.poll-option-label');
                    if (labelEl) {
                        labelEl.style.fontWeight = isVoted ? '700' : '500';
                        labelEl.style.color      = isVoted ? '#293C79' : '#374151';
                    }

                    // Persentase
                    const pctEl = optEl.querySelector('.poll-pct');
                    if (pctEl) {
                        pctEl.textContent     = match.percentage + '%';
                        pctEl.style.color     = isVoted ? '#293C79' : '#6b7280';
                        pctEl.style.display   = '';
                    }
                });
            })
            .catch(() => {
                container.querySelectorAll('.poll-option').forEach(o => {
                    o.style.pointerEvents = '';
                    o.style.opacity = '';
                });
                alert('Terjadi kesalahan. Coba lagi.');
            });
        });
    });
});
</script>
@endonce
