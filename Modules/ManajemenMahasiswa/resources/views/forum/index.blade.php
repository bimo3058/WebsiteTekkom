<x-manajemenmahasiswa::layouts.forum-layout>

    @push('styles')
        <style>
            /* ── Page Title ──────────────────────────────────────────────────── */
            .page-title {
                margin-bottom: 22px;
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

            /* ── Cards ───────────────────────────────────────────────────────── */
            .dashboard-card {
                background: #fff;
                border-radius: 12px;
                border: 1px solid #e5e7eb;
                padding: 20px;
                margin-bottom: 20px;
            }

            .forum-card {
                background: #fff;
                border-radius: 12px;
                border: 1px solid #e5e7eb;
                padding: 20px;
                margin-bottom: 20px;
                transition: transform 0.15s ease, box-shadow 0.15s ease;
            }

            .forum-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
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
                background-color: #4f46e5;
                color: white;
                border: none;
                border-radius: 6px;
                padding: 4px 16px;
                font-size: 13px;
                font-weight: 600;
                transition: background-color 0.2s;
            }

            .btn-join:hover {
                background-color: #4338ca;
            }

            .btn-post {
                background-color: #4f46e5;
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
                transition: background-color 0.2s;
            }

            .btn-post:hover {
                background-color: #4338ca;
                color: white;
            }

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
            }

            .post-actions .action-btn:hover {
                background: #e2e8f0;
            }

            .search-input {
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 7px 12px 7px 32px;
                font-size: 13px;
                color: #374151;
                outline: none;
                background: #f9fafb;
                height: 42px;
            }

            .search-input:focus {
                border-color: #4f46e5;
                background: #ffffff;
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
                font-weight: 600;
                font-size: 12px;
                color: #6b7280;
                border-bottom: 1px solid #f3f4f6;
                padding: 10px 14px;
                background-color: #f9fafb;
                text-transform: uppercase;
                letter-spacing: 0.04em;
            }

            .leaderboard-table td {
                border-bottom: 1px solid #f3f4f6;
                color: #374151;
                font-size: 13px;
                padding: 12px 14px;
                background-color: transparent;
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

            /* ── Admin Report Panel ──────────────────────────────────────────── */
            .report-panel {
                background: #fff;
                border: 1px solid #fecaca;
                border-radius: 12px;
                margin-bottom: 20px;
                overflow: hidden;
            }

            .report-panel-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 14px 20px;
                background: #fef2f2;
                cursor: pointer;
                transition: background 0.2s;
            }

            .report-panel-header:hover {
                background: #fee2e2;
            }

            .report-panel-header .chevron-icon {
                transition: transform 0.3s;
            }

            .report-panel-header .chevron-icon.rotated {
                transform: rotate(180deg);
            }

            .report-panel-header h6 {
                font-size: 14px;
                font-weight: 700;
                color: #991b1b;
                margin: 0;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .report-panel-header .report-badge {
                background: #ef4444;
                color: #fff;
                font-size: 11px;
                font-weight: 700;
                padding: 2px 8px;
                border-radius: 20px;
            }

            .report-panel-body {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease;
            }

            .report-panel-body.open {
                max-height: 2000px;
            }

            .report-item {
                padding: 14px 20px;
                border-top: 1px solid #fecaca;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .report-item:first-child {
                border-top: none;
            }

            .report-item-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 12px;
            }

            .report-thread-title {
                font-size: 14px;
                font-weight: 700;
                color: #111827;
                text-decoration: none;
                transition: color 0.2s;
            }

            .report-thread-title:hover {
                color: #6366f1;
            }

            .report-reason-text {
                background: #fef2f2;
                border: 1px solid #fecaca;
                border-radius: 6px;
                padding: 8px 12px;
                font-size: 12px;
                color: #991b1b;
                line-height: 1.4;
            }

            .report-meta-line {
                font-size: 11px;
                color: #6b7280;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .report-actions-row {
                display: flex;
                gap: 6px;
                flex-wrap: wrap;
            }

            .report-action-btn {
                padding: 5px 12px;
                border-radius: 6px;
                font-size: 11px;
                font-weight: 600;
                border: 1px solid #e5e7eb;
                background: #fff;
                color: #374151;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                transition: all 0.15s;
                text-decoration: none;
            }

            .report-action-btn:hover {
                border-color: #6366f1;
                color: #6366f1;
            }

            .report-action-btn.danger {
                border-color: #fecaca;
                color: #dc2626;
            }

            .report-action-btn.danger:hover {
                background: #fef2f2;
            }

            .report-action-btn.warning {
                border-color: #fde68a;
                color: #d97706;
            }

            .report-action-btn.warning:hover {
                background: #fffbeb;
            }

            /* Pagination Custom Layout */
            .pagination-container nav > .d-sm-flex {
                flex-direction: column-reverse;
                align-items: center !important;
                gap: 0.75rem;
            }
            .pagination-container nav > .d-sm-flex > div:last-child {
                margin-bottom: 0.25rem;
            }
            .pagination-container .pagination {
                margin-bottom: 0;
            }
        </style>
    @endpush

    <div class="page-title">
        <h1>Forum Diskusi</h1>
        <p>Wadah komunikasi mahasiswa & alumni</p>
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
            <div class="gamification-card h-100" style="overflow:hidden; border: 1px solid #e5e7eb; border-radius: 12px; background: #fff;">
                <div style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%); padding: 16px 20px; border-radius: 12px 12px 0 0;">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width: 36px; height: 36px; background: rgba(255,255,255,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0" style="color: #fff; font-size: 15px; letter-spacing: -0.01em;">Leaderboard</h6>
                            <small style="color: rgba(255,255,255,0.7); font-size: 11px;">Top kontributor forum</small>
                        </div>
                    </div>
                </div>
                <div style="padding: 0;">
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm mb-0 leaderboard-table">
                            <thead>
                                <tr>
                                    <th style="width: 10%; padding-left: 20px;">#</th>
                                    <th style="width: 40%">User</th>
                                    <th style="width: 20%">Level</th>
                                    <th style="width: 30%">Badges</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leaderboard as $index => $entry)
                                    <tr style="{{ $index < 3 ? 'background:' . ['#fffbeb','#f8fafc','#fdf4f0'][$index] . ';' : '' }}">
                                        <td style="padding-left: 20px;">
                                            @if($index === 0)
                                                <span style="font-size: 18px; filter: drop-shadow(0 1px 2px rgba(234,179,8,0.4));">🥇</span>
                                            @elseif($index === 1)
                                                <span style="font-size: 18px; filter: drop-shadow(0 1px 2px rgba(148,163,184,0.4));">🥈</span>
                                            @elseif($index === 2)
                                                <span style="font-size: 18px; filter: drop-shadow(0 1px 2px rgba(180,83,9,0.3));">🥉</span>
                                            @else
                                                <span style="color: #94a3b8; font-weight: 600;">{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-semibold" style="color: #1e293b; font-size: 13px;">{{ $entry->name }}</span>
                                        </td>
                                        <td>
                                            <span class="d-inline-flex align-items-center gap-1" style="font-size: 13px;">
                                                <span title="{{ $entry->tier_name }}">{!! $entry->tier_icon !!}</span>
                                                <span style="color: #6366f1; font-weight: 600;">Lv.{{ $entry->level }}</span>
                                            </span>
                                        </td>
                                        <td>
                                            @foreach($entry->badges->take(3) as $badge)
                                                @if($badge->image)
                                                    <img src="{{ asset($badge->image) }}?v={{ time() }}" title="{{ $badge->name }}" style="width: 22px; height: 22px; object-fit: contain; margin-right: 2px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                                    <span style="display:none;">{{ $badge->icon }}</span>
                                                @else
                                                    <span title="{{ $badge->name }}">{{ $badge->icon }}</span>
                                                @endif
                                            @endforeach
                                            @if($entry->badges->count() > 3)
                                                <span style="font-size: 11px; opacity: 0.7;">+{{ $entry->badges->count() - 3 }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4" style="color: #94a3b8;">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-2"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                                            <br>Belum ada data leaderboard
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr style="border-top: 2px dashed #e2e8f0; background: #f0f9ff;">
                                    <td style="padding-left: 20px;">
                                        <span style="background: #6366f1; color: #fff; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 10px;">#{{ $userStats['rank'] }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-semibold" style="color: #1e293b; font-size: 13px;">{{ $user->name }}</span>
                                        <span style="font-size: 10px; color: #6366f1; font-weight: 600; margin-left: 4px; background: #eef2ff; padding: 1px 6px; border-radius: 4px;">Anda</span>
                                    </td>
                                    <td>
                                        <span class="d-inline-flex align-items-center gap-1" style="font-size: 13px;">
                                            <span>{!! $userStats['tier_icon'] !!}</span>
                                            <span style="color: #6366f1; font-weight: 600;">Lv.{{ $userStats['level'] }}</span>
                                        </span>
                                    </td>
                                    <td>
                                        @foreach($userStats['badges']->take(3) as $badge)
                                            @if($badge->image)
                                                <img src="{{ asset($badge->image) }}?v={{ time() }}" title="{{ $badge->name }}" style="width: 22px; height: 22px; object-fit: contain; margin-right: 2px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                                <span style="display:none;">{{ $badge->icon }}</span>
                                            @else
                                                <span title="{{ $badge->name }}">{{ $badge->icon }}</span>
                                            @endif
                                        @endforeach
                                        @if($userStats['badges']->count() > 3)
                                            <span style="font-size: 11px; opacity: 0.7;">+{{ $userStats['badges']->count() - 3 }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Stats Card -->
        <div class="col-md-5">
            <div class="gamification-card h-100" style="overflow:hidden; border: 1px solid #e5e7eb; border-radius: 12px; background: #fff;">
                {{-- Streak Banner --}}
                <div style="background: linear-gradient(135deg, #f97316 0%, #ea580c 50%, #dc2626 100%); padding: 18px 20px; border-radius: 12px 12px 0 0; position: relative; overflow: hidden;">
                    {{-- Decorative circles --}}
                    <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                    <div style="position: absolute; bottom: -30px; right: 40px; width: 60px; height: 60px; background: rgba(255,255,255,0.06); border-radius: 50%;"></div>

                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="#fbbf24" stroke="#fbbf24" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>
                        </div>
                        <div>
                            <div style="color: rgba(255,255,255,0.8); font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Streak Harian</div>
                            <div style="color: #fff; font-size: 28px; font-weight: 800; line-height: 1; letter-spacing: -0.02em;">{{ $userStats['current_streak'] }} <span style="font-size: 14px; font-weight: 600; opacity: 0.8;">Hari</span></div>
                        </div>
                    </div>
                </div>

                {{-- Stats Content --}}
                <div style="padding: 16px 20px 20px;">
                    {{-- Rank & Level Grid --}}
                    <div class="d-flex gap-3 mb-3">
                        {{-- Rank --}}
                        <div style="flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; text-align: center;">
                            <div style="font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px;">Rank</div>
                            <div style="font-size: 22px; font-weight: 800; color: #1e293b; letter-spacing: -0.02em;">#{{ $userStats['rank'] }}</div>
                        </div>
                        {{-- Level --}}
                        <div style="flex: 1; background: #f5f3ff; border: 1px solid #e9e5ff; border-radius: 10px; padding: 12px 14px; text-align: center;">
                            <div style="font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px;">Level</div>
                            <div style="font-size: 22px; font-weight: 800; color: #6366f1; letter-spacing: -0.02em;">
                                <span>{!! $userStats['tier_icon'] !!}</span> {{ $userStats['level'] }}
                            </div>
                            <div style="font-size: 11px; color: #8b5cf6; font-weight: 600;">{{ $userStats['tier_name'] }}</div>
                        </div>
                    </div>

                    {{-- XP Progress --}}
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span style="font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.03em;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px; vertical-align: -2px;"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                                Experience
                            </span>
                            <span style="font-size: 12px; font-weight: 700; color: #6366f1;">{{ $userStats['total_xp'] }} / {{ $userStats['xp_for_next'] }} XP</span>
                        </div>
                        @php
                            $progressPct = $userStats['xp_needed'] > 0
                                ? min(100, round(($userStats['xp_current'] / $userStats['xp_needed']) * 100))
                                : 100;
                        @endphp
                        <div style="height: 10px; background: #e2e8f0; border-radius: 6px; overflow: hidden;">
                            <div style="height: 100%; width: {{ $progressPct }}%; background: linear-gradient(90deg, #6366f1, #8b5cf6, #a855f7); border-radius: 6px; transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span style="font-size: 10px; color: #94a3b8; font-weight: 500;">Level {{ $userStats['level'] }}</span>
                            <span style="font-size: 10px; color: #94a3b8; font-weight: 500;">Level {{ $userStats['level'] + 1 }}</span>
                        </div>
                    </div>

                    {{-- Badges --}}
                    @if($userStats['badges']->isNotEmpty())
                        <div class="mt-3">
                            <div style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 8px;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 3px; vertical-align: -2px;"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                                Badges
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                @foreach($userStats['badges'] as $badge)
                                    <div style="background: #f5f3ff; border: 1px solid #e9e5ff; border-radius: 8px; padding: 4px 10px; display: inline-flex; align-items: center; gap: 4px;">
                                        @if($badge->image)
                                            <img src="{{ asset($badge->image) }}?v={{ time() }}" title="{{ $badge->name }}: {{ $badge->description }}" style="width: 20px; height: 20px; object-fit: contain;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                            <span style="display:none; font-size: 16px;" title="{{ $badge->name }}: {{ $badge->description }}">{{ $badge->icon }}</span>
                                        @else
                                            <span title="{{ $badge->name }}: {{ $badge->description }}" style="font-size: 16px;">{{ $badge->icon }}</span>
                                        @endif
                                        <span style="font-size: 11px; font-weight: 600; color: #6366f1;">{{ $badge->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Area -->
    <form method="GET" action="{{ route('manajemenmahasiswa.forum.index') }}" id="forumFilterForm">
        <div class="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center mb-3">
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

        {{-- Sort Tabs --}}
        <input type="hidden" name="sort" id="sortInput" value="{{ request('sort', 'terbaru') }}">
        <div class="d-flex gap-2 mb-4">
            @php $currentSort = request('sort', 'terbaru'); @endphp
            <button type="button" class="btn btn-sm rounded-pill fw-semibold px-3 {{ $currentSort === 'terbaru' ? 'btn-dark' : 'btn-outline-secondary' }}"
                onclick="document.getElementById('sortInput').value='terbaru'; document.getElementById('forumFilterForm').submit();">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:2px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Terbaru
            </button>
            <button type="button" class="btn btn-sm rounded-pill fw-semibold px-3 {{ $currentSort === 'hot' ? 'btn-dark' : 'btn-outline-secondary' }}"
                onclick="document.getElementById('sortInput').value='hot'; document.getElementById('forumFilterForm').submit();">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:2px;"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg> Hot
            </button>
            <button type="button" class="btn btn-sm rounded-pill fw-semibold px-3 {{ $currentSort === 'top' ? 'btn-dark' : 'btn-outline-secondary' }}"
                onclick="document.getElementById('sortInput').value='top'; document.getElementById('forumFilterForm').submit();">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:2px;"><polyline points="18 15 12 9 6 15"/></svg> Top
            </button>
        </div>
    </form>

    {{-- Admin Report Panel --}}
    @if($user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan']) && $forumReports->isNotEmpty())
        <div class="report-panel">
            <div class="report-panel-header" onclick="this.nextElementSibling.classList.toggle('open'); this.querySelector('.chevron-icon').classList.toggle('rotated')">
                <h6>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#991b1b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" x2="4" y1="22" y2="15"/></svg> Laporan Masuk
                    <span class="report-badge">{{ $forumReports->count() }}</span>
                </h6>
                <svg class="chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#991b1b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transition:transform 0.3s;"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
            <div class="report-panel-body">
                @foreach($forumReports as $report)
                    <div class="report-item">
                        <div class="report-item-header">
                            <div>
                                @if($report->thread)
                                    <a href="{{ route('manajemenmahasiswa.forum.show', $report->thread_id) }}" class="report-thread-title">
                                        {{ $report->thread->judul }}
                                    </a>
                                @else
                                    <span class="report-thread-title" style="color:#9ca3af;text-decoration:line-through;">Thread telah dihapus</span>
                                @endif
                            </div>
                            <span style="font-size:11px; color:#9ca3af; white-space:nowrap;">{{ $report->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="report-meta-line">
                            Dilaporkan oleh <strong>{{ $report->reporter->name ?? 'Unknown' }}</strong>
                            @if($report->thread && $report->thread->author)
                                &nbsp;• Thread oleh <strong>{{ $report->thread->author->name }}</strong>
                            @endif
                        </div>
                        <div class="report-reason-text"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" x2="4" y1="22" y2="15"/></svg> {{ $report->alasan }}</div>
                        <div class="report-actions-row">
                            @if($report->thread)
                                <a href="{{ route('manajemenmahasiswa.forum.show', $report->thread_id) }}" class="report-action-btn"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg> Lihat</a>
                                @if(!($report->thread->is_locked ?? false))
                                    <form method="POST" action="{{ route('manajemenmahasiswa.forum.reports.lock_thread', $report->id) }}" style="display:inline;" onsubmit="return confirm('Kunci thread ini?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="report-action-btn warning"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Kunci</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('manajemenmahasiswa.forum.reports.delete_thread', $report->id) }}" style="display:inline;" onsubmit="return confirm('HAPUS thread ini secara permanen?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="report-action-btn danger"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg> Hapus Thread</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('manajemenmahasiswa.forum.reports.dismiss', $report->id) }}" style="display:inline;" onsubmit="return confirm('Abaikan laporan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="report-action-btn"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Abaikan</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Forum Posts -->
    <div class="forum-cards-container">
    @forelse($threads as $thread)
        <div class="forum-card" data-thread-id="{{ $thread->id }}" style="cursor: pointer;">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-placeholder">
                            {{ strtoupper(substr($thread->author->name ?? '?', 0, 2)) }}
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h6 class="fw-bold text-dark mb-0">{{ $thread->author->name ?? 'Unknown' }}</h6>
                                @include('manajemenmahasiswa::forum.partials.role-badge', ['roleUser' => $thread->author])
                                @if(isset($authorTiers[$thread->user_id]))
                                    <span class="badge rounded-pill" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: #fff; font-size: 10px; font-weight: 600; padding: 3px 8px;" title="{{ $authorTiers[$thread->user_id]['tier_name'] }}">
                                        {!! $authorTiers[$thread->user_id]['tier_icon'] !!} Lv.{{ $authorTiers[$thread->user_id]['level'] }}
                                    </span>
                                @endif
                                <span class="text-primary fw-medium" style="font-size: 12px;">•
                                    {{ $thread->created_at->diffForHumans() }}</span>
                                @if($thread->isEdited())
                                    <span class="edited-badge">(diedit)</span>
                                @endif
                                @if($thread->is_pinned)
                                    <span class="pinned-badge">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M14 2l8 8-2 2-3-3-4 4v7h-2v-7l-4-4-3 3-2-2 8-8z" />
                                        </svg>
                                        Pinned
                                    </span>
                                @endif
                                <span class="personal-pin-badge" data-personal-pin="{{ $thread->id }}" style="display: {{ $thread->is_personal_pinned ? 'inline-flex' : 'none' }};">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="margin-right:2px;"><line x1="12" y1="17" x2="12" y2="22"/><path d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6a3 3 0 0 0-6 0v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24Z"/></svg> Pin Pribadi
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button type="button" class="btn btn-link p-0 text-muted fw-bold text-decoration-none shadow-none d-flex align-items-center" data-bs-toggle="dropdown">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 8px;">
                            {{-- Edit (owner only) --}}
                            @if($thread->user_id === $user->id)
                                <li>
                                    <a href="{{ route('manajemenmahasiswa.forum.edit', $thread->id) }}" class="dropdown-item d-flex align-items-center gap-2">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg> Edit
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
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg> Unlock Thread
                                            @else
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Kunci Thread
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
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg> Unpin Global
                                            @else
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="17" x2="12" y2="22"/><path d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6a3 3 0 0 0-6 0v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24Z"/></svg> Pin Global
                                            @endif
                                        </button>
                                    </form>
                                </li>
                            @endif
                            {{-- Pin Pribadi (semua role) --}}
                            <li>
                                <form method="POST" action="{{ route('manajemenmahasiswa.forum.personal_pin', $thread->id) }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="17" x2="12" y2="22"/><path d="M5 17h14v-1.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V6a3 3 0 0 0-6 0v4.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24Z"/></svg>
                                        @if($thread->is_personal_pinned) Unpin Pribadi @else Pin Pribadi @endif
                                    </button>
                                </form>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            {{-- Delete --}}
                            @if($thread->user_id === $user->id)
                                <li>
                                    <form method="POST" action="{{ route('manajemenmahasiswa.forum.destroy', $thread->id) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus thread ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg> Hapus
                                        </button>
                                    </form>
                                </li>
                            @elseif($user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan']))
                                <li>
                                    <form method="POST" action="{{ route('manajemenmahasiswa.forum.destroy', $thread->id) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus thread ini (sebagai admin)?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg> Hapus (Admin)
                                        </button>
                                    </form>
                                </li>
                            @endif
                            @if($thread->user_id !== $user->id && !$user->hasAnyRole(['superadmin', 'admin', 'admin_kemahasiswaan']))
                                <li>
                                    <button type="button" class="dropdown-item text-danger d-flex align-items-center gap-2" data-bs-toggle="modal"
                                        data-bs-target="#reportModal" data-thread-id="{{ $thread->id }}"
                                        data-thread-title="{{ $thread->judul }}">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" x2="4" y1="22" y2="15"/></svg> Laporkan Thread
                                    </button>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <div class="d-flex gap-3 mb-3">
                    <div class="flex-grow-1 min-w-0">
                        <h6 class="fw-bold text-dark mb-2">{{ $thread->judul }}</h6>
                        <p class="text-dark mb-0" style="font-size: 14px; line-height: 1.5;">
                            {{ Str::limit($thread->getTextContent() ?: strip_tags($thread->konten), 200) }}
                        </p>
                    </div>
                </div>

                @if($thread->getFirstImageUrl())
                    <div class="mt-2 mb-3" style="width: 100%; max-height: 512px; overflow: hidden; border-radius: 12px; border: 1px solid #e5e7eb; background: #f8fafc; display: flex; justify-content: center; align-items: center;">
                        <img src="{{ $thread->getFirstImageUrl() }}" alt="Thumbnail" style="width: 100%; max-height: 512px; object-fit: contain;">
                    </div>
                @endif

                <!-- Labels -->
                <div class="d-flex gap-2 mb-3 flex-wrap">
                    @foreach($thread->getKategoriLabels() as $idx => $lbl)
                        @php $colorClass = $thread->getKategoriColors()[$idx] ?? 'tag-gray'; @endphp
                        <span class="tag-label {{ $colorClass }}">{{ $lbl }}</span>
                    @endforeach
                    @if($thread->is_locked)
                        <span class="tag-label tag-red">🔒 Dikunci</span>
                    @endif
                </div>

                <!-- Actions -->
                @php
                    $threadVoteKey = \Modules\ManajemenMahasiswa\Models\Thread::class . '_' . $thread->id;
                    $threadUserVote = $userVotes[$threadVoteKey] ?? null;
                @endphp
                <div class="post-actions d-flex align-items-center mt-2">
                    <div class="vote-pill shadow-sm">
                        <button class="vote-thread-btn {{ $threadUserVote && $threadUserVote->value === 1 ? 'vote-active-up' : '' }}" data-thread-id="{{ $thread->id }}" data-value="1">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="{{ $threadUserVote && $threadUserVote->value === 1 ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>
                        </button>
                        <span class="thread-vote-count-{{ $thread->id }}">{{ $thread->vote_count }}</span>
                        <div class="v-separator"></div>
                        <button class="vote-thread-btn {{ $threadUserVote && $threadUserVote->value === -1 ? 'vote-active-down' : '' }}" data-thread-id="{{ $thread->id }}" data-value="-1">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="{{ $threadUserVote && $threadUserVote->value === -1 ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg>
                        </button>
                    </div>
                    <button class="action-btn ms-2" onclick="window.location.href='{{ route('manajemenmahasiswa.forum.show', $thread->id) }}'">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        {{ $thread->comments_count ?? $thread->comment_count }}
                    </button>
                    <button class="action-btn share-btn ms-1" data-url="{{ route('manajemenmahasiswa.forum.show', $thread->id) }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                        Bagikan
                    </button>
                </div>
            </div>
    @empty
        <div class="empty-state">
            <div class="icon">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                </svg>
            </div>
            <h5 class="fw-bold text-dark">Belum ada diskusi</h5>
            <p>Jadilah yang pertama memulai diskusi!</p>
            <a href="{{ route('manajemenmahasiswa.forum.create') }}" class="btn-post text-decoration-none">
                Buat Post Pertama
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    class="ms-1">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
            </a>
        </div>
    @endforelse
    </div>

    <!-- Pagination -->
    @if($threads->hasPages())
        <div class="d-flex justify-content-center mt-4 mb-4 pagination-container">
            {{ $threads->appends(request()->query())->links('pagination::bootstrap-5') }}
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
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M4 2v20h2v-7h10l-2-4 2-4H6V2H4z" />
                            </svg>
                            Laporkan Thread
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
            const csrfToken = '{{ csrf_token() }}';



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
            // ---- Forum Card Click Handler ----
            document.querySelectorAll('.forum-card').forEach(card => {
                card.addEventListener('click', function(e) {
                    if (e.target.closest('.dropdown') || e.target.closest('.post-actions')) {
                        return;
                    }
                    const threadId = this.dataset.threadId;
                    window.location.href = `{{ url('manajemen-mahasiswa/forum') }}/${threadId}`;
                });
            });
        </script>
    @endpush

</x-manajemenmahasiswa::layouts.forum-layout>