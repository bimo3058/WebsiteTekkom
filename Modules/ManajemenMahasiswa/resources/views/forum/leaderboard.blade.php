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
            .dash-box-body { flex: 1; overflow-y: auto; padding: 20px 24px; }
            .dash-box-body::-webkit-scrollbar { width: 6px; }
            .dash-box-body::-webkit-scrollbar-thumb { background: #C1C7CF; border-radius: 10px; }
            @media (max-width: 767px) {
                .sitkom-content { padding: 8px 8px 80px !important; display: block !important; overflow: visible !important; }
                .dash-wrap { height: auto !important; padding: 0; }
                .dash-box { flex: none !important; overflow: visible !important; border-radius: 10px; }
                .dash-box-header { padding: 12px 14px; position: sticky; top: 52px; z-index: 10; }
                .dash-box-body { overflow-y: visible !important; flex: none !important; padding: 14px; }
            }

            /* ── Table ───────────────────────────────────────────────────────── */
            .lb-card {
                background: #fff;
                border-radius: 12px;
                border: 1px solid #DFE1E7;
                overflow: hidden;
                box-shadow: 0px 1px 2px 0px rgba(228, 229, 231, 0.5);
            }

            .lb-card-header {
                background: #FAFAFA;
                border-bottom: 1px solid #DFE1E7;
                padding: 12px 20px;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .lb-card-header h6 {
                color: #0D0D12;
                font-size: 13px;
                font-weight: 700;
                margin: 0;
            }

            .lb-table {
                width: 100%;
                border-collapse: collapse;
            }

            .lb-table thead th {
                font-size: 11px;
                font-weight: 700;
                color: #6b7280;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                padding: 12px 16px;
                background: #f9fafb;
                border-bottom: 1px solid #f0f0f4;
                white-space: nowrap;
            }

            .lb-table tbody tr {
                border-bottom: 1px solid #f3f4f6;
                transition: background 0.15s;
            }

            .lb-table tbody tr:last-child { border-bottom: none; }

            .lb-table tbody tr:hover { background: #f8fafc; }

            .lb-table tbody tr.is-me {
                background: #eff6ff;
                border-left: 3px solid #0B266E;
            }

            .lb-table tbody tr.is-me:hover { background: #dbeafe; }

            .lb-table td {
                padding: 14px 16px;
                font-size: 13px;
                color: #374151;
                vertical-align: middle;
            }

            /* Rank cell */
            .rank-cell { width: 52px; text-align: center; }

            .rank-badge-lb {
                width: 30px;
                height: 30px;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-weight: 800;
                font-size: 13px;
                color: #fff;
                box-shadow: 0 2px 6px rgba(0,0,0,0.12);
            }

            .rank-1-lb { background: linear-gradient(135deg, #fbbf24, #d97706); border: 2px solid #fef3c7; }
            .rank-2-lb { background: linear-gradient(135deg, #cbd5e1, #64748b); border: 2px solid #f1f5f9; }
            .rank-3-lb { background: linear-gradient(135deg, #f97316, #b45309); border: 2px solid #ffedd5; }
            .rank-other-lb { background: #f1f5f9; color: #64748b; font-size: 12px; }

            .avatar-sm {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                background: #e0e7ff;
                color: #0B266E;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 13px;
                flex-shrink: 0;
            }

            .avatar-sm.is-me-avatar {
                background: #0B266E;
                color: #fff;
            }

            .tier-chip {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                background: #E7E8F0;
                border: 1px solid #d0d4e8;
                border-radius: 20px;
                padding: 3px 10px;
                font-size: 12px;
                font-weight: 600;
                color: #0B266E;
                white-space: nowrap;
            }

            .xp-chip {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                font-size: 13px;
                font-weight: 700;
                color: #0B266E;
            }

            .streak-chip {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                font-size: 12px;
                font-weight: 600;
                color: #ea580c;
            }

            .badge-list { display: flex; gap: 4px; flex-wrap: wrap; align-items: center; }

            .me-tag {
                background: #0B266E;
                color: #fff;
                font-size: 10px;
                font-weight: 700;
                padding: 2px 7px;
                border-radius: 20px;
                margin-left: 6px;
                letter-spacing: 0.03em;
            }

            .outside-top-notice {
                text-align: center;
                padding: 18px;
                color: #6b7280;
                font-size: 13px;
                background: #f9fafb;
                border-top: 1px solid #f3f4f6;
            }
        </style>
    @endpush

    <div class="dash-wrap">
    <div class="dash-box">
    <div class="dash-box-header">
        <x-manajemenmahasiswa::ui.page-header
            title="Leaderboard"
            subtitle="Top kontributor Forum Diskusi berdasarkan XP" />
    </div>
    <div class="dash-box-body">

    {{-- My Rank Banner — compact --}}
    <div style="background: linear-gradient(135deg, #0B266E 0%, #091958 60%, #5C78B8 100%); border-radius: 12px; padding: 14px 20px; margin-bottom: 20px; display: flex; align-items: center; gap: 16px; flex-wrap: wrap; position: relative; overflow: hidden; box-shadow: 0px 1px 2px 0px rgba(228,229,231,0.5);">
        <div style="position: absolute; top: -30px; right: -30px; width: 100px; height: 100px; background: rgba(255,255,255,0.06); border-radius: 50%;"></div>

        {{-- Rank --}}
        <div style="display: flex; align-items: baseline; gap: 4px;">
            <span style="color: rgba(255,255,255,0.65); font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Rank</span>
            <span style="color: #fbbf24; font-size: 32px; font-weight: 900; line-height: 1; letter-spacing: -0.03em;">#{{ $userStats['rank'] }}</span>
        </div>

        <div style="width: 1px; height: 36px; background: rgba(255,255,255,0.2);"></div>

        {{-- Stats chips --}}
        <div class="d-flex gap-2 flex-wrap">
            <div style="background: rgba(255,255,255,0.12); border-radius: 8px; padding: 6px 12px; text-align: center;">
                <div style="font-size: 14px; font-weight: 800; color: #fff; line-height: 1;">{!! $userStats['tier_icon'] !!} {{ $userStats['level'] }}</div>
                <div style="font-size: 9px; font-weight: 600; color: rgba(255,255,255,0.65); text-transform: uppercase; letter-spacing: 0.04em; margin-top: 1px;">Level</div>
            </div>
            <div style="background: rgba(255,255,255,0.12); border-radius: 8px; padding: 6px 12px; text-align: center;">
                <div style="font-size: 14px; font-weight: 800; color: #fff; line-height: 1;">{{ number_format($userStats['total_xp']) }}</div>
                <div style="font-size: 9px; font-weight: 600; color: rgba(255,255,255,0.65); text-transform: uppercase; letter-spacing: 0.04em; margin-top: 1px;">XP</div>
            </div>
            <div style="background: rgba(255,255,255,0.12); border-radius: 8px; padding: 6px 12px; text-align: center;">
                <div style="font-size: 14px; font-weight: 800; color: #fff; line-height: 1;">{{ $userStats['current_streak'] }}</div>
                <div style="font-size: 9px; font-weight: 600; color: rgba(255,255,255,0.65); text-transform: uppercase; letter-spacing: 0.04em; margin-top: 1px;">Streak</div>
            </div>
        </div>

        <div style="margin-left: auto; position: relative; z-index: 1;">
            <span style="font-size: 12px; font-weight: 600; color: rgba(255,255,255,0.8);">{!! $userStats['tier_icon'] !!} {{ $userStats['tier_name'] }}</span>
        </div>
    </div>

    {{-- Leaderboard Table --}}
    <div class="lb-card">
        <div class="lb-card-header">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/>
                <path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/>
                <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/>
                <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/>
            </svg>
            <h6>Top Kontributor</h6>
            <span style="margin-left: auto; font-size: 11px; color: rgba(255,255,255,0.65); font-weight: 500;">
                Top {{ $leaderboard->count() }} pengguna
            </span>
        </div>

        <div class="table-responsive">
            <table class="lb-table">
                <thead>
                    <tr>
                        <th class="rank-cell">#</th>
                        <th>Pengguna</th>
                        <th>Tier / Level</th>
                        <th>Total XP</th>
                        <th>Streak</th>
                        <th>Badges</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaderboard as $index => $entry)
                        @php $isMe = $entry->user_id === $user->id; @endphp
                        <tr class="{{ $isMe ? 'is-me' : '' }}">
                            {{-- Rank --}}
                            <td class="rank-cell">
                                @if($index === 0)
                                    <span class="rank-badge-lb rank-1-lb">1</span>
                                @elseif($index === 1)
                                    <span class="rank-badge-lb rank-2-lb">2</span>
                                @elseif($index === 2)
                                    <span class="rank-badge-lb rank-3-lb">3</span>
                                @else
                                    <span class="rank-badge-lb rank-other-lb">{{ $index + 1 }}</span>
                                @endif
                            </td>

                            {{-- User --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm {{ $isMe ? 'is-me-avatar' : '' }}">
                                        {{ strtoupper(substr($entry->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-1 flex-wrap">
                                            <span class="fw-semibold" style="color: #1e293b; font-size: 13px;">
                                                {{ $entry->name }}
                                            </span>
                                            @if($isMe)
                                                <span class="me-tag">Kamu</span>
                                            @endif
                                        </div>
                                        @if($entry->user)
                                            @include('manajemenmahasiswa::forum.partials.role-badge', ['roleUser' => $entry->user])
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Tier & Level --}}
                            <td>
                                <span class="tier-chip">
                                    {!! $entry->tier_icon !!} Lv.{{ $entry->level }} &bull; {{ $entry->tier_name }}
                                </span>
                            </td>

                            {{-- XP --}}
                            <td>
                                <span class="xp-chip">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="#fbbf24" stroke="#fbbf24"
                                        stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                                    </svg>
                                    {{ number_format($entry->total_xp) }}
                                </span>
                            </td>

                            {{-- Streak --}}
                            <td>
                                @if($entry->current_streak > 0)
                                    <span class="streak-chip">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="#f97316"
                                            stroke="#f97316" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>
                                        </svg>
                                        {{ $entry->current_streak }} hari
                                    </span>
                                @else
                                    <span style="color: #d1d5db; font-size: 12px;">—</span>
                                @endif
                            </td>

                            {{-- Badges --}}
                            <td>
                                <div class="badge-list">
                                    @forelse($entry->badges->take(4) as $badge)
                                        @if($badge->image)
                                            <img src="{{ asset($badge->image) }}" title="{{ $badge->name }}"
                                                style="width: 22px; height: 22px; object-fit: contain;"
                                                onerror="this.style.display='none';">
                                        @else
                                            <span title="{{ $badge->name }}" style="font-size: 18px;">{{ $badge->icon }}</span>
                                        @endif
                                    @empty
                                        <span style="color: #d1d5db; font-size: 12px;">—</span>
                                    @endforelse
                                    @if($entry->badges->count() > 4)
                                        <span style="font-size: 11px; color: #94a3b8; font-weight: 600;">
                                            +{{ $entry->badges->count() - 4 }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 48px; color: #9ca3af;">
                                Belum ada data leaderboard.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Jika user tidak masuk top 50 --}}
        @if($userRankInList === null)
            <div class="outside-top-notice">
                Kamu belum masuk top 50. Terus berkontribusi di forum untuk naik peringkat!
            </div>
        @endif
    </div>

    </div>{{-- /dash-box-body --}}
    </div>{{-- /dash-box --}}
    </div>{{-- /dash-wrap --}}

</x-manajemenmahasiswa::layouts.forum-layout>
