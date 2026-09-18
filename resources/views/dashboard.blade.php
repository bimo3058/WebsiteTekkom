{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>

@php
    $user = auth()->user();
    $name = $user->name;
    $initials = strtoupper(substr($name, 0, 1));
    $sp = strpos($name, ' ');
    if ($sp !== false)
        $initials .= strtoupper(substr($name, $sp + 1, 1));

    $userRoles = $user->roles->pluck('name')->toArray();
    $isSuperadmin = in_array('superadmin', $userRoles);
    $isDosen = in_array('dosen', $userRoles);
    $isMahasiswa = in_array('mahasiswa', $userRoles);
@endphp

{{-- Style Box Wrap khas SITKOM untuk Dashboard --}}
<style>
    /* Hilangkan padding default agar wrap bisa full 100vh */
    .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }

    /* Header Dashboard Standalone */
    .dash-topbar {
        background: #fff;
        border-bottom: 1px solid var(--c-border);
        padding: 12px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
        height: 60px;
        position: sticky;
        top: 0;
        z-index: 20;
    }
    .dash-topbar-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .dash-topbar-logo {
        width: 32px;
        height: 32px;
    }
    .dash-topbar-brand {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .dash-topbar-title {
        font-family: 'Geist', 'Inter Tight', sans-serif;
        font-weight: 700;
        font-size: 14px;
        color: var(--c-fg);
        letter-spacing: -.01em;
        line-height: 1.2;
    }
    .dash-topbar-subtitle {
        font-size: 9px;
        color: var(--c-fg-placeholder);
        font-weight: 500;
        line-height: 1.2;
    }
    .dash-topbar-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .dash-topbar-user {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        padding: 6px 12px;
        border-radius: 8px;
        transition: background .15s;
    }
    .dash-topbar-user:hover {
        background: var(--c-bg);
    }
    .dash-topbar-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #8FA3D1, #5C78B8);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        flex-shrink: 0;
    }
    .dash-topbar-meta {
        display: flex;
        flex-direction: column;
        gap: 1px;
    }
    .dash-topbar-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--c-fg);
        line-height: 1.2;
    }
    .dash-topbar-role {
        font-size: 11px;
        color: var(--c-fg-muted);
        line-height: 1.2;
    }
    .dash-user-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        right: 20px;
        background: #fff;
        border: 1px solid var(--c-border);
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        min-width: 200px;
        z-index: 50;
        overflow: hidden;
    }
    .dash-user-dropdown a,
    .dash-user-dropdown button {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        width: 100%;
        text-align: left;
        font-size: 13px;
        font-weight: 500;
        color: var(--c-fg-sec);
        text-decoration: none;
        background: none;
        border: none;
        cursor: pointer;
        font-family: inherit;
        transition: background .15s;
        border-bottom: 1px solid var(--c-border);
    }
    .dash-user-dropdown a:last-child,
    .dash-user-dropdown button:last-child {
        border-bottom: none;
    }
    .dash-user-dropdown a:hover,
    .dash-user-dropdown button:hover {
        background: var(--c-bg);
    }
    .dash-user-dropdown button.logout {
        color: var(--c-error);
    }
    .dash-user-dropdown button.logout:hover {
        background: #FEF1F4;
    }
    .dash-user-dropdown svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }
    @media (max-width: 768px) {
        .dash-topbar-subtitle { display: none; }
        .dash-topbar-sep { display: none; }
    }
    @media (max-width: 640px) {
        .dash-topbar {
            padding: 10px 12px;
        }
        .dash-topbar-meta {
            display: none;
        }
    }

    /* Container luar */
    .dash-wrap {
        display: flex; flex-direction: column; height: calc(100vh - 60px);
        padding: 10px; box-sizing: border-box; font-family: 'Inter Tight', sans-serif;
    }

    /* Kotak utama (Box) */
    .dash-box {
        display: flex; flex-direction: column; flex: 1; min-height: 0;
        background: #fff; border: 1px solid var(--c-border);
        border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        overflow: hidden; width: 100%; box-sizing: border-box;
    }

    /* Area Header Box (Fixed di atas kotak) */
    .dash-box-header {
        background: #fff;
        border-bottom: 1px solid var(--c-border);
        flex-shrink: 0; width: 100%; box-sizing: border-box;
        padding: 16px 24px;
    }

    /* Area Konten Box (Scrollable) */
    .dash-box-body {
        flex: 1; overflow-y: auto; padding: 20px 24px;
        display: flex; flex-direction: column; gap: 24px;
    }

    .dash-box-body > * {
        flex-shrink: 0;
        width: 100%;
        min-width: 0;
    }

    /* Percantik scrollbar */
    .dash-box-body::-webkit-scrollbar { width: 6px; }
    .dash-box-body::-webkit-scrollbar-thumb {
        background: var(--c-border-strong);
        border-radius: 10px;
    }

    /* ── Mobile: scroll natively, bukan inner-scroll ── */
    @media (max-width: 767px) {
        .sitkom-content {
            padding: 8px 8px 80px !important;
            display: block !important;
            overflow: visible !important;
        }
        .dash-wrap {
            height: auto !important;
            min-height: 0 !important;
            padding: 0;
        }
        .dash-box {
            flex: none !important;
            min-height: 0 !important;
            overflow: visible !important;
            border-radius: 10px;
        }
        .dash-box-header {
            padding: 12px 14px;
            position: sticky;
            top: 52px;
            z-index: 10;
        }
        .dash-box-body {
            overflow-y: visible !important;
            flex: none !important;
            padding: 14px;
        }
    }
</style>

{{-- Header Dashboard Standalone --}}
<div class="dash-topbar" x-data="{ userDropdown: false }" @click.outside="userDropdown = false">
    <div class="dash-topbar-left">
        <img src="{{ asset('images/UNDIPOfficial.png') }}" alt="UNDIP" class="dash-topbar-logo">
        <div class="dash-topbar-brand">
            <div class="dash-topbar-title">SITKOM</div>
            <div class="dash-topbar-subtitle">Sistem Informasi Teknik Komputer</div>
        </div>
    </div>
    <div class="dash-topbar-right">
        <div class="dash-topbar-user" @click="userDropdown = !userDropdown">
            <div class="dash-topbar-avatar" @if($isSuperadmin) style="background:#F3F4F6;color:#6B7280;border:1px solid #E5E7EB;" @endif>
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                @else
                    {{ $initials }}
                @endif
            </div>
            <div class="dash-topbar-meta">
                <div class="dash-topbar-name">{{ $user->name }}</div>
                <div class="dash-topbar-role">
                    @if($isSuperadmin) Super Admin
                    @elseif($isDosen) Dosen
                    @elseif($isMahasiswa) Mahasiswa
                    @else User
                    @endif
                </div>
            </div>
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" style="transition:transform .15s;" :style="userDropdown ? 'transform:rotate(180deg)' : ''">
                <path d="M6 9l6 6 6-6"/>
            </svg>
        </div>

        {{-- Dropdown Menu --}}
        <div x-show="userDropdown"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="dash-user-dropdown"
             style="display:none;">
            <a href="{{ route('profile.edit') }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 15a3 3 0 100-6 3 3 0 000 6z"/>
                    <path d="M19.4 15a1.7 1.7 0 00.3 1.8l.1.1a2 2 0 11-2.8 2.8l-.1-.1a1.7 1.7 0 00-1.8-.3 1.7 1.7 0 00-1 1.5V21a2 2 0 11-4 0v-.1a1.7 1.7 0 00-1-1.5 1.7 1.7 0 00-1.8.3l-.1.1A2 2 0 114.4 17l.1-.1a1.7 1.7 0 00.3-1.8 1.7 1.7 0 00-1.5-1H3a2 2 0 110-4h.1a1.7 1.7 0 001.5-1A1.7 1.7 0 004.4 7l-.1-.1A2 2 0 117.1 4l.1.1a1.7 1.7 0 001.8.3 1.7 1.7 0 001-1.5V3a2 2 0 114 0v.1a1.7 1.7 0 001 1.5 1.7 1.7 0 001.8-.3l.1-.1A2 2 0 1119.6 7l-.1.1a1.7 1.7 0 00-.3 1.8 1.7 1.7 0 001.5 1H21a2 2 0 110 4h-.1a1.7 1.7 0 00-1.5 1z"/>
                </svg>
                Settings
            </a>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="logout">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>

<div class="dash-wrap">
    <div class="dash-box">

        {{-- Area Header (Fixed) --}}
        <div class="dash-box-header">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
                <div>
                    <h1 style="font-size:22px; font-weight:700; color:var(--c-fg); letter-spacing:-0.02em; line-height:1.2; margin-bottom:3px;">
                        Selamat datang, <span style="color:var(--c-primary);">{{ explode(' ', auth()->user()->name)[0] }}</span>
                    </h1>
                    <p style="font-size:12px; color:var(--c-fg-muted);">
                        Pilih modul yang ingin kamu akses hari ini
                        <span style="margin-left:4px; color:var(--c-fg-placeholder);">·</span>
                        <span style="margin-left:4px;">{{ now()->translatedFormat('l, d F Y') }}</span>
                    </p>
                </div>
            </div>
        </div>

        {{-- Area Konten (Scrollable) --}}
        <div class="dash-box-body">

            {{-- MODULE CARDS --}}
            <div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
                    <span style="width:3px;height:14px;border-radius:2px;background:var(--c-primary);"></span>
                    <span style="font-size:14px;font-weight:700;color:var(--c-fg);">Modul Aplikasi</span>
                </div>

                @php
                    $moduleIcons = [
                        'quiz'        => '<path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>', // Clipboard checklist - Bank Soal
                        'school'      => '<path d="M12 14l9-5-9-5-9 5 9 5zM12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zM12 14v6.25"/>', // Graduation cap - Capstone
                        'groups'      => '<path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>', // User groups - Manajemen Mahasiswa
                        'folder_open' => '<path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>', // Document - E-Office
                    ];

                    $moduleTags = [
                        'blue'   => ['label' => 'SIBASO',   'bg' => '#F9ECCB', 'color' => '#956321'],
                        'purple' => ['label' => 'SICATA',   'bg' => '#D1F0F9', 'color' => '#0C4D6E'],
                        'green'  => ['label' => 'SIMENMA',  'bg' => '#DDF2EE', 'color' => '#287F6E'],
                        'orange' => ['label' => 'SIPERKOM', 'bg' => '#FADAE1', 'color' => '#95122B'],
                    ];
                @endphp

                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;" class="dash-modules">
                    @foreach($cards as $card)
                    @php
                        $svg = $moduleIcons[$card['icon']] ?? $moduleIcons['folder_open'];
                        $tag = $moduleTags[$card['color']] ?? ['label' => 'APP', 'bg' => 'var(--c-bg)', 'color' => 'var(--c-fg-muted)'];
                    @endphp
                    <a href="{{ route($card['route']) }}"
                       style="background:#fff;border:1px solid var(--c-border);border-radius:14px;padding:14px;display:flex;flex-direction:column;gap:10px;text-decoration:none;box-shadow:var(--shadow-card);transition:border-color .15s,box-shadow .15s,transform .15s;"
                       onmouseover="this.style.borderColor='var(--c-primary-border)';this.style.boxShadow='0 4px 14px rgba(11,38,110,0.07)';this.style.transform='translateY(-2px)'"
                       onmouseout="this.style.borderColor='var(--c-border)';this.style.boxShadow='var(--shadow-card)';this.style.transform='translateY(0)'">

                        <div style="display:flex;align-items:center;justify-content:space-between;">
                            <span style="display:inline-flex;align-items:center;gap:6px;padding:5px 9px;border-radius:8px;font-size:11px;font-weight:700;letter-spacing:.02em;background:{{ $tag['bg'] }};color:{{ $tag['color'] }};">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    {!! $svg !!}
                                </svg>
                                {{ $tag['label'] }}
                            </span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--c-border-strong)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transition:all .15s;">
                                <polyline points="9 18 15 12 9 6"/>
                            </svg>
                        </div>

                        <div style="flex:1;display:flex;flex-direction:column;gap:4px;">
                            <h3 style="font-size:14px;font-weight:700;color:var(--c-fg);line-height:1.3;margin:0;">{{ $card['title'] }}</h3>
                            <p style="font-size:11.5px;color:var(--c-fg-muted);line-height:1.5;margin:0;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                {{ $card['description'] }}
                            </p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- PENGUMUMAN --}}
            <div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
                    <span style="width:3px;height:14px;border-radius:2px;background:var(--c-primary);"></span>
                    <span style="font-size:14px;font-weight:700;color:var(--c-fg);">Pengumuman</span>
                </div>

                @php
                    $tabs = [
                        ['key' => 'all',           'label' => 'Semua'],
                        ['key' => 'bank_soal',     'label' => 'Bank Soal'],
                        ['key' => 'capstone',      'label' => 'Capstone TA'],
                        ['key' => 'kemahasiswaan', 'label' => 'Kemahasiswaan'],
                        ['key' => 'eoffice',       'label' => 'EOffice'],
                    ];
                    $badgeMap = [
                        'bank_soal'     => ['bg' => '#F9ECCB', 'text' => '#956321', 'dot' => '#D39C3D', 'border_l' => '#D39C3D', 'label' => 'Bank Soal'],
                        'capstone'      => ['bg' => '#D1F0F9', 'text' => '#0C4D6E', 'dot' => '#3B82F6', 'border_l' => '#3B82F6', 'label' => 'Capstone TA'],
                        'kemahasiswaan' => ['bg' => '#DDF2EE', 'text' => '#287F6E', 'dot' => '#10B981', 'border_l' => '#10B981', 'label' => 'Kemahasiswaan'],
                        'eoffice'       => ['bg' => '#FADAE1', 'text' => '#95122B', 'dot' => '#EF4444', 'border_l' => '#EF4444', 'label' => 'EOffice'],
                    ];
                @endphp

                <div style="background:#fff;border:1px solid var(--c-border);border-radius:14px;overflow:hidden;box-shadow:var(--shadow-card);">

                    {{-- Tabs --}}
                    <div x-data="{ activeTab: 'all' }" class="w-full">
                        <div style="display:flex;border-bottom:1px solid var(--c-border);overflow-x:auto;scrollbar-width:none;gap:2px;padding:0 16px;">
                            @foreach($tabs as $tab)
                            <button @click="activeTab = '{{ $tab['key'] }}'"
                                    :class="activeTab === '{{ $tab['key'] }}' ? 'border-[var(--c-primary)] text-[var(--c-primary)]' : 'border-transparent text-[#808897] hover:text-[#353849]'"
                                    style="flex-shrink:0;padding:12px 16px;border-bottom:2px solid transparent;font-size:12px;font-weight:600;transition:all .15s;white-space:nowrap;">
                                {{ $tab['label'] }}
                                @if(isset($announcementCounts[$tab['key']]) && $announcementCounts[$tab['key']] > 0)
                                <span :class="activeTab === '{{ $tab['key'] }}' ? 'bg-[var(--c-primary)] text-white' : 'bg-[#F0F1F4] text-[#808897]'"
                                      style="display:inline-block;margin-left:6px;padding:1px 6px;border-radius:9999px;font-size:10px;font-weight:700;min-width:18px;text-align:center;transition:all .15s;">
                                    {{ $announcementCounts[$tab['key']] }}
                                </span>
                                @endif
                            </button>
                            @endforeach
                        </div>

                        {{-- Tab: Semua --}}
                        <div x-show="activeTab === 'all'"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="w-full flex flex-col">
                            @forelse($announcements['all'] ?? [] as $item)
                            <div class="w-full flex items-start gap-4 px-5 py-4 border-b border-[#F0F1F4] last:border-b-0 hover:bg-[#F6F8FA] transition-colors group {{ !empty($item['url']) ? 'cursor-pointer' : '' }}"
                                 @if(!empty($item['url'])) onclick="window.location.href='{{ e($item['url']) }}'" @endif>
                                <div class="flex-1 min-w-0 w-full">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <span class="text-[10px] text-[#C1C7CF] flex-shrink-0">{{ $item['date'] }}</span>
                                        @if(!empty($item['badge']))
                                        @php $b = $badgeMap[$item['badge']] ?? ['bg' => '#F0F1F4', 'text' => '#808897']; @endphp
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full flex-shrink-0"
                                              style="background:{{ $b['bg'] }};color:{{ $b['text'] }}">
                                            {{ $b['label'] }}
                                        </span>
                                        @endif
                                        @if(!empty($item['pinned']))
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full flex-shrink-0 bg-[#FEF3C7] text-[#92400E]">
                                            Penting
                                        </span>
                                        @endif
                                    </div>
                                    <p class="text-[13px] font-semibold text-[#0D0D12] leading-snug group-hover:text-[#0B266E] truncate transition-colors w-full">
                                        {{ $item['title'] }}
                                    </p>
                                    <p class="text-[12px] text-[#808897] mt-0.5 leading-relaxed line-clamp-2 w-full whitespace-normal">
                                        {{ $item['body'] }}
                                    </p>
                                </div>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                     stroke="#DFE1E7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                     class="flex-shrink-0 mt-1 group-hover:stroke-[#0B266E] transition-colors">
                                    <polyline points="9 18 15 12 9 6"/>
                                </svg>
                            </div>
                            @empty
                            <div class="flex flex-col items-center justify-center py-12 w-full text-[#DFE1E7]">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-2">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                                </svg>
                                <p class="text-[12px] text-[#A4ABB8]">Belum ada pengumuman</p>
                            </div>
                            @endforelse
                        </div>

                        {{-- Tab: Per modul --}}
                        @foreach(['bank_soal', 'capstone', 'kemahasiswaan', 'eoffice'] as $moduleKey)
                        @php $b = $badgeMap[$moduleKey]; @endphp
                        <div x-show="activeTab === '{{ $moduleKey }}'"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="w-full flex flex-col"
                             style="display:none">
                            @forelse($announcements[$moduleKey] ?? [] as $item)
                            <div class="w-full flex items-start gap-4 px-5 py-4 border-b border-[#F0F1F4] last:border-b-0 hover:bg-[#F6F8FA] transition-colors group {{ !empty($item['url']) ? 'cursor-pointer' : '' }}"
                                 style="border-left: 3px solid {{ $b['border_l'] }}"
                                 @if(!empty($item['url'])) onclick="window.location.href='{{ e($item['url']) }}'" @endif>
                                <div class="flex-1 min-w-0 w-full">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <span class="text-[10px] text-[#C1C7CF] flex-shrink-0">{{ $item['date'] }}</span>
                                        @if(!empty($item['pinned']))
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full flex-shrink-0"
                                              style="background:{{ $b['bg'] }};color:{{ $b['text'] }}">
                                            Penting
                                        </span>
                                        @endif
                                    </div>
                                    <p class="text-[13px] font-semibold text-[#0D0D12] leading-snug group-hover:text-[#0B266E] truncate transition-colors w-full">
                                        {{ $item['title'] }}
                                    </p>
                                    <p class="text-[12px] text-[#808897] mt-0.5 leading-relaxed line-clamp-2 w-full whitespace-normal">
                                        {{ $item['body'] }}
                                    </p>
                                </div>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                     stroke="#DFE1E7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                     class="flex-shrink-0 mt-1 group-hover:stroke-[#0B266E] transition-colors">
                                    <polyline points="9 18 15 12 9 6"/>
                                </svg>
                            </div>
                            @empty
                            <div class="flex flex-col items-center justify-center py-12 w-full text-[#DFE1E7]">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-2">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                                </svg>
                                <p class="text-[12px] text-[#A4ABB8]">Belum ada pengumuman dari {{ $b['label'] }}</p>
                            </div>
                            @endforelse
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
@media (max-width: 1280px) { .dash-modules { grid-template-columns: repeat(2, 1fr) !important; } }
@media (max-width: 640px)  { .dash-modules { grid-template-columns: 1fr !important; } }
</style>

</x-app-layout>
