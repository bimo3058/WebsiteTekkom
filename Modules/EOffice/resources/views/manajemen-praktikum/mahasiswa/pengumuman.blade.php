<x-eoffice::manajemen-praktikum.layout pageTitle="Pengumuman Praktikum">

    {{-- Page Header --}}
    <div class="mp-page-header">
        <div>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                <h1 class="mp-page-title">Pengumuman</h1>
                <span class="mp-badge warning sm"><span class="dot"></span>Mahasiswa</span>
            </div>
            <p class="mp-page-sub">Informasi dan pengumuman dari asisten dan koordinator praktikum ·
                {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
        </div>
    </div>

    @if($praktikumList->isEmpty())
        <div class="mp-alert warning flex-shrink-0">Anda belum terdaftar di praktikum manapun.</div>
    @else

        {{-- ── Pilih Praktikum ─────────────────────────────────────────────── --}}
        <div class="sec-head flex-shrink-0">
            <span class="sec-bar" style="background:#D39C3D;"></span>
            <span class="sec-title">Pilih Praktikum</span>
            <span class="sec-rule"></span>
        </div>

        <div class="mp-card flex-shrink-0">
            <div style="padding:14px 18px;">
                <form method="GET" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#808897" stroke-width="2"
                        stroke-linecap="round" style="flex-shrink:0;">
                        <path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2zM22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z" />
                    </svg>
                    <select name="praktikum_id" class="mp-input mp-select" style="max-width:360px;"
                        onchange="this.form.submit()">
                        @foreach($praktikumList as $p)
                            <option value="{{ $p->id }}" {{ ($praktikum?->id == $p->id) ? 'selected' : '' }}>
                                {{ $p->nama }}
                                @if($p->kode) [{{ $p->kode }}] @endif
                                · {{ $p->semester }} {{ $p->tahun_ajaran }}
                            </option>
                        @endforeach
                    </select>
                    @if($praktikum)
                        <span class="mp-badge warning sm"><span
                                class="dot"></span>{{ $pengumumans instanceof \Illuminate\Pagination\LengthAwarePaginator ? $pengumumans->total() : $pengumumans->count() }}
                            pengumuman</span>
                    @endif
                </form>
            </div>
        </div>

        @if($praktikum)
            {{-- Stats Bar --}}
            <div class="mp-stats-grid cols-2 flex-shrink-0" style="grid-template-columns:repeat(2,1fr);">
                <div class="mp-stat" style="display:flex;align-items:center;gap:14px;padding:14px 18px;">
                    <div class="mp-stat-icon" style="background:rgba(211,156,61,0.12);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D39C3D" stroke-width="2"
                            stroke-linecap="round">
                            <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" />
                        </svg>
                    </div>
                    <div>
                        <div class="mp-stat-value" style="font-size:22px;">
                            {{ $pengumumans instanceof \Illuminate\Pagination\LengthAwarePaginator ? $pengumumans->total() : $pengumumans->count() }}
                        </div>
                        <div class="mp-stat-label" style="margin:0;">Total Pengumuman</div>
                    </div>
                </div>
                <div class="mp-stat" style="display:flex;align-items:center;gap:14px;padding:14px 18px;">
                    <div class="mp-stat-icon" style="background:rgba(64,196,170,0.12);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#40C4AA" stroke-width="2"
                            stroke-linecap="round">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                    </div>
                    <div>
                        @php
                            $latest = ($pengumumans instanceof \Illuminate\Pagination\LengthAwarePaginator ? $pengumumans->getCollection() : $pengumumans)->first();
                        @endphp
                        <div class="mp-stat-value" style="font-size:14px;font-weight:600;">
                            {{ $latest ? $latest->created_at?->locale('id')->diffForHumans() : '—' }}</div>
                        <div class="mp-stat-label" style="margin:0;">Pengumuman Terbaru</div>
                    </div>
                </div>
            </div>

            {{-- Section head --}}
            <div class="sec-head flex-shrink-0">
                <span class="sec-bar"></span>
                <span class="sec-title">Daftar Pengumuman</span>
                <span class="sec-rule"></span>
                <span
                    class="mp-badge neutral sm">{{ $pengumumans instanceof \Illuminate\Pagination\LengthAwarePaginator ? $pengumumans->total() : $pengumumans->count() }}
                    total</span>
            </div>

            {{-- Announcement Cards --}}
            @forelse($pengumumans as $pg)
                @php
                    $nameParts = explode(' ', $pg->user?->name ?? 'SY');
                    $initials = strtoupper(substr($nameParts[0] ?? 'S', 0, 1) . substr($nameParts[1] ?? $nameParts[0] ?? 'Y', 0, 1));
                    $avColors = ['sky', 'navy', 'green', 'yellow', 'violet'];
                    $avColor = $avColors[crc32($pg->user?->email ?? '') % count($avColors)];
                    $isSistem = $pg->tipe_sistem !== null;
                    $isNew = $pg->created_at && $pg->created_at->gt(now()->subDays(2));
                @endphp
                <div class="mp-card flex-shrink-0" style="transition:border-color .2s, box-shadow .2s;"
                    onmouseover="this.style.borderColor='#B7C2DE';this.style.boxShadow='0 4px 14px rgba(11,38,110,.07)'"
                    onmouseout="this.style.borderColor='#DFE1E7';this.style.boxShadow=''">

                    @if($isNew)
                        <div style="height:3px;background:linear-gradient(90deg,#D39C3D,#F4C666);border-radius:14px 14px 0 0;"></div>
                    @endif

                    <div style="padding:20px;">
                        <div style="display:flex;align-items:flex-start;gap:14px;">
                            {{-- Avatar --}}
                            <div style="flex-shrink:0;margin-top:2px;">
                                @if($isSistem)
                                    <div
                                        style="width:36px;height:36px;border-radius:10px;background:rgba(11,38,110,0.1);display:flex;align-items:center;justify-content:center;">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2"
                                            stroke-linecap="round">
                                            <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" />
                                        </svg>
                                    </div>
                                @else
                                    @php
                                        $colorMap = ['sky' => '#106A97', 'navy' => '#0B266E', 'green' => '#174E43', 'yellow' => '#5B3D1E', 'violet' => '#5B21B6'];
                                        $bg = $colorMap[$avColor] ?? '#0B266E';
                                    @endphp
                                    <div
                                        style="width:36px;height:36px;border-radius:10px;background:{{ $bg }};display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;">
                                        {{ $initials }}
                                    </div>
                                @endif
                            </div>

                            <div style="flex:1;min-width:0;">
                                {{-- Title + Badges --}}
                                <div style="display:flex;align-items:flex-start;gap:8px;flex-wrap:wrap;margin-bottom:4px;">
                                    <div style="font-size:15px;font-weight:700;color:#0D0D12;flex:1;min-width:0;line-height:1.3;">
                                        {{ $pg->judul }}
                                    </div>
                                    @if($isNew)
                                        <span class="mp-badge warning sm" style="animation:pulse-badge 2s infinite;">
                                            <span class="dot"></span>Baru
                                        </span>
                                    @endif
                                    @if($isSistem)
                                        <span class="mp-badge sky sm">Sistem</span>
                                    @endif
                                </div>

                                {{-- Meta --}}
                                <div
                                    style="font-size:11px;color:#808897;margin-bottom:12px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
                                    <span style="display:flex;align-items:center;gap:4px;">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round">
                                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z" />
                                        </svg>
                                        <strong
                                            style="color:#666D80;">{{ $isSistem ? 'Sistem' : ($pg->user?->name ?? '—') }}</strong>
                                    </span>
                                    <span style="display:flex;align-items:center;gap:4px;">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round">
                                            <circle cx="12" cy="12" r="10" />
                                            <polyline points="12 6 12 12 16 14" />
                                        </svg>
                                        {{ $pg->created_at?->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}
                                    </span>
                                    @if($pg->praktikum)
                                        <span style="display:flex;align-items:center;gap:4px;">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round">
                                                <path
                                                    d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2zM22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z" />
                                            </svg>
                                            <strong style="color:#666D80;">{{ $pg->praktikum->nama }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div
                                    style="font-size:13px;color:#353849;line-height:1.7;white-space:pre-line;background:#F8FAFC;border:1px solid #EDF0F4;border-radius:8px;padding:14px;">
                                    {{ $pg->konten }}
                                </div>

                                @if(!empty($pg->lampiran))
                                <div style="margin-top:12px;display:flex;flex-wrap:wrap;gap:8px;">
                                    @foreach($pg->lampiran as $lamp)
                                    <a href="{{ app(\App\Services\SupabaseStorage::class)->getPublicUrl($lamp['path'], 'eoffice') }}" target="_blank"
                                       style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:#fff;border:1px solid #DFE1E7;border-radius:6px;font-size:12px;font-weight:500;color:#0B266E;text-decoration:none;transition:all .2s;"
                                       onmouseover="this.style.borderColor='#0B266E';this.style.background='#F0F4FA'" onmouseout="this.style.borderColor='#DFE1E7';this.style.background='#fff'">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                            <path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/>
                                        </svg>
                                        <span style="max-width:150px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $lamp['name'] }}">
                                            {{ $lamp['name'] }}
                                        </span>
                                    </a>
                                    @endforeach
                                </div>
                                @endif

                                @if($pg->tipe_sistem === 'buka')
                                    <div style="margin-top:14px;">
                                        <a href="{{ route('eoffice.manprak.mahasiswa.daftar-asprak.index') }}?praktikum_id={{ $pg->praktikum_id }}"
                                            class="mp-btn primary sm" style="text-decoration:none;display:inline-flex;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" style="margin-right:6px;">
                                                <path
                                                    d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8zM14 2v6h6M12 18v-6M9 15h6" />
                                            </svg>
                                            Daftar Sekarang →
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="mp-card flex-1 flex items-center justify-center" style="min-height:280px;">
                    <div style="padding:48px;text-align:center;">
                        <div
                            style="width:56px;height:56px;border-radius:14px;background:#F4F6F8;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                                stroke-linecap="round">
                                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" />
                            </svg>
                        </div>
                        <div style="font-size:15px;font-weight:600;color:#0D0D12;margin-bottom:6px;">Belum Ada Pengumuman</div>
                        <div style="font-size:13px;color:#666D80;max-width:260px;margin:0 auto;line-height:1.6;">
                            Pengumuman dari asisten atau koordinator praktikum Anda akan muncul di sini.
                        </div>
                    </div>
                </div>
            @endforelse

            {{-- Pagination --}}
            @if($pengumumans instanceof \Illuminate\Pagination\LengthAwarePaginator && $pengumumans->hasPages())
                <div class="flex-shrink-0" style="padding:8px 0;">{{ $pengumumans->links() }}</div>
            @endif
        @else
            <div class="mp-alert info flex-shrink-0">Silakan pilih praktikum terlebih dahulu untuk melihat pengumuman.</div>
        @endif

    @endif

    <style>
        @keyframes pulse-badge {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .6;
            }
        }

        .sec-head {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .sec-bar {
            width: 4px;
            height: 18px;
            background: #D39C3D;
            border-radius: 2px;
            flex-shrink: 0;
        }

        .sec-title {
            font-size: 14px;
            font-weight: 700;
            color: #0D0D12;
            white-space: nowrap;
        }

        .sec-rule {
            flex: 1;
            height: 1px;
            background: #ECEFF3;
        }
    </style>

</x-eoffice::manajemen-praktikum.layout>