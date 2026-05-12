{{-- resources/views/dashboard.blade.php --}}

<x-app-layout>
<div class="min-h-screen bg-[#F6F8FA]">

    {{-- ── TOPBAR ────────────────────────────────────────────────────────── --}}
    <header x-data="{ open: false }" class="sticky top-0 z-40 h-14 bg-white border-b border-[#DFE1E7] flex items-center px-6 justify-between">

        {{-- Kiri: Logo + nama app --}}
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-lg overflow-hidden flex-shrink-0">
                <img src="{{ asset('images/UNDIPOfficial.png') }}"
                    class="w-full h-full object-cover"
                    alt="logo">
            </div>
            <div class="flex flex-col leading-tight">
                <span class="text-[14px] font-bold text-[#0D0D12] tracking-tight">SITKOM</span>
                <span class="text-[10px] text-gray-500">Sistem Informasi Teknik Komputer</span>
            </div>
        </div>

        {{-- Kanan: Avatar + Dropdown --}}
        <div class="relative">
            <button @click="open = !open"
                    class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg hover:bg-[#F6F8FA] transition-colors">

                <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden"
                     style="background:linear-gradient(135deg,#3C518B,#0B266E)">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="avatar" class="w-full h-full object-cover">
                    @else
                        <span class="text-white font-bold text-[11px] uppercase">
                            {{ substr(auth()->user()->name, 0, 2) }}
                        </span>
                    @endif
                </div>

                <div class="text-left hidden sm:block">
                    <p class="text-[12px] font-semibold text-[#0D0D12] leading-tight">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-[#A4ABB8] leading-tight">
                        {{ ucfirst(auth()->user()->roles->first()->name ?? 'User') }}
                    </p>
                </div>

                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round"
                     :style="open ? 'transform:rotate(180deg)' : ''"
                     style="transition:transform 0.2s;flex-shrink:0">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>

            {{-- Dropdown --}}
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.outside="open = false"
                 class="absolute right-0 mt-1 w-52 bg-white rounded-xl border border-[#DFE1E7] shadow-[0_12px_20px_-4px_rgba(22,22,43,0.08)] py-1 z-50"
                 style="display:none">

                <div class="px-3 py-2.5 border-b border-[#F0F1F4]">
                    <p class="text-[12px] font-semibold text-[#0D0D12] truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-[#A4ABB8] truncate">{{ auth()->user()->email }}</p>
                </div>

                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-2.5 px-3 py-2 text-[12px] text-[#353849] hover:bg-[#F6F8FA] transition-colors">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                    Profil Saya
                </a>

                <div class="border-t border-[#F0F1F4] my-1"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-2.5 px-3 py-2 text-[12px] text-[#DF1C41] hover:bg-[#FADAE1] transition-colors">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </header>

    {{-- ── BODY ─────────────────────────────────────────────────────────── --}}
    <div class="max-w-7xl mx-auto px-6 py-8">

        {{-- Greeting --}}
        <div class="mb-7">
            <h1 class="text-[20px] font-bold text-[#0D0D12] tracking-tight leading-tight">
                Selamat datang, <span class="text-[#0B266E]">{{ explode(' ', auth()->user()->name)[0] }}</span>
            </h1>
            <p class="text-[#808897] text-[13px] mt-1">Pilih modul yang ingin kamu akses hari ini.</p>
        </div>

        {{-- ── MODULE CARDS ─────────────────────────────────────────────── --}}
        @php
            $moduleColors = [
                'blue'   => ['icon_bg' => '#E8EDF7', 'icon_color' => '#0B266E', 'hover_border' => '#8FA3D1'],
                'purple' => ['icon_bg' => '#E8EDF7', 'icon_color' => '#3C518B', 'hover_border' => '#5C78B8'],
                'green'  => ['icon_bg' => '#DDF2EE', 'icon_color' => '#287F6E', 'hover_border' => '#40C4AA'],
                'orange' => ['icon_bg' => '#F9ECCB', 'icon_color' => '#956321', 'hover_border' => '#D39C3D'],
            ];

            $moduleIcons = [
                // Bank Soal -> Ikon Invoice / Lembar Kertas
                'quiz'        => '<path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9h6m-6 4h6"/>',
                'school'      => '<path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2M2 7h20v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7z M12 12v1"/>',
                'groups'      => '<path d="M17 20.6622V19.5C17 17.2909 15.2091 15.5 13 15.5H11C8.79086 15.5 7 17.2909 7 19.5V20.6622M17 20.6622C19.989 18.9331 22 15.7014 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 15.7014 4.01099 18.9331 7 20.6622M17 20.6622C15.5291 21.513 13.8214 22 12 22C10.1786 22 8.47087 21.513 7 20.6622M15 9C15 10.6569 13.6569 12 12 12C10.3431 12 9 10.6569 9 9C9 7.34315 10.3431 6 12 6C13.6569 6 15 7.34315 15 9Z"/>',
                'description' => '<path d="M22 10V17C22 18.6569 20.6569 20 19 20H5C3.34315 20 2 18.6569 2 17V10M22 10C22 8.34315 20.6569 7 19 7H16M22 10L14.4368 12.917C13.6611 13.2617 12.8306 13.4341 12 13.4341M2 10C2 8.34315 3.34315 7 5 7H8M2 10L9.56317 12.917C10.3389 13.2617 11.1694 13.4341 12 13.4341M8 7V6C8 4.89543 8.89543 4 10 4H14C15.1046 4 16 4.89543 16 6V7M8 7H16M12 13.4341V12M12 13.4341V15"/>',
                'bar_chart'   => '<path d="M4.8787 8.90834L10.5858 3.54999C11.3669 2.81667 12.6332 2.81667 13.4142 3.54999L19.1213 8.90834M4.8787 8.90834C4.31629 9.43653 4.00002 10.1531 4.00002 10.9V18.1833C4.00002 19.7389 5.34317 21 7.00002 21H9V16C9 14.8954 9.89543 14 11 14H13C14.1046 14 15 14.8954 15 16V21H17C18.6569 21 20 19.7389 20 18.1833V10.9C20 10.153 19.684 9.43656 19.1213 8.90834M4.8787 8.90834L3.00031 10.6722M19.1213 8.90834L21 10.6722"/>',
                'folder'      => '<path d="M12 4H6C4.34315 4 3 5.34315 3 7V18C3 19.6569 4.34315 21 6 21H17C18.6569 21 20 19.6569 20 18V12M7 17H12M7 13H15M21 5.5C21 6.88071 19.8807 8 18.5 8C17.1193 8 16 6.88071 16 5.5C16 4.11929 17.1193 3 18.5 3C19.8807 3 21 4.11929 21 5.5Z"/>',
                'settings'    => '<path d="M6.78883 3.18702L9.45314 2.08342C10.0104 1.85259 10.6493 2.11723 10.8801 2.6745C11.0637 3.11762 11.5233 3.38148 12.0029 3.38184C12.4828 3.38219 12.9361 3.11793 13.1197 2.67459C13.3506 2.11727 13.9895 1.85261 14.5469 2.08346L17.211 3.187C17.7683 3.41784 18.033 4.05676 17.8021 4.61407C17.6185 5.0574 17.7523 5.56484 18.0918 5.90389C18.4312 6.24278 18.9429 6.38129 19.386 6.19774C19.9433 5.96691 20.5822 6.23155 20.813 6.78882L21.9166 9.45314C22.1474 10.0104 21.8828 10.6493 21.3255 10.8801C20.8824 11.0637 20.6185 11.5234 20.6182 12.003C20.6178 12.4828 20.8821 12.9362 21.3254 13.1198C21.8827 13.3507 22.1473 13.9896 21.9165 14.5469L20.813 17.2111C20.5821 17.7684 19.9432 18.033 19.3859 17.8022C18.9426 17.6186 18.4351 17.7523 18.0961 18.0918C17.7572 18.4312 17.6187 18.9429 17.8023 19.386C18.0331 19.9433 17.7685 20.5821 17.2112 20.813L14.5469 21.9166C13.9896 22.1474 13.3507 21.8828 13.1199 21.3255C12.9363 20.8824 12.4766 20.6185 11.997 20.6182C11.5171 20.6178 11.0637 20.8821 10.8801 21.3254C10.6492 21.8827 10.0103 22.1474 9.45297 21.9165L6.78887 20.813C6.23152 20.5822 5.96686 19.9432 6.19772 19.3859C6.38136 18.9425 6.24769 18.4351 5.90812 18.096C5.56872 17.7571 5.05713 17.6187 4.61402 17.8022C4.05674 18.0331 3.41786 17.7684 3.18703 17.2112L2.08343 14.5469C1.8526 13.9896 2.11723 13.3507 2.67451 13.1199C3.11762 12.9363 3.38149 12.4766 3.38185 11.997C3.3822 11.5171 3.11794 11.0638 2.67458 10.8801C2.11724 10.6493 1.85257 10.0103 2.08343 9.45299L3.18693 6.78891C3.41779 6.23157 4.05675 5.9669 4.61409 6.19776C5.05745 6.38141 5.56487 6.24771 5.90395 5.90813C6.24284 5.56874 6.38129 5.05713 6.19775 4.61401C5.96692 4.05674 6.23155 3.41785 6.78883 3.18702Z"/>',
            ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            @foreach($cards as $card)
            @php
                $mc  = $moduleColors[$card['color']] ?? $moduleColors['blue'];
                $svg = $moduleIcons[$card['icon']] ?? $moduleIcons['folder'];
            @endphp

            <a href="{{ route($card['route']) }}"
               class="group bg-white border border-[#DFE1E7] rounded-2xl p-5 flex flex-col gap-4 transition-all duration-200 hover:shadow-[0_4px_8px_-2px_rgba(22,22,43,0.06)] hover:border-[{{ $mc['hover_border'] }}] relative overflow-hidden">

                {{-- Subtle bg shape --}}
                <div class="absolute -top-5 -right-5 w-20 h-20 rounded-full opacity-40 group-hover:opacity-80 transition-opacity pointer-events-none"
                     style="background:{{ $mc['icon_bg'] }}"></div>

                {{-- Icon --}}
                <div class="relative w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:{{ $mc['icon_bg'] }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                         stroke="{{ $mc['icon_color'] }}" stroke-width="1.8"
                         stroke-linecap="round" stroke-linejoin="round">
                        {!! $svg !!}
                    </svg>
                </div>

                {{-- Text --}}
                <div class="relative flex-1">
                    <h3 class="text-[13px] font-semibold text-[#0D0D12] group-hover:text-[#0B266E] transition-colors leading-tight mb-1.5">
                        {{ $card['title'] }}
                    </h3>
                    <p class="text-[#A4ABB8] text-[12px] leading-relaxed">
                        {{ $card['description'] }}
                    </p>
                </div>

                {{-- Arrow --}}
                <div class="flex justify-end">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                         stroke="#DFE1E7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="group-hover:stroke-[#0B266E] group-hover:translate-x-0.5 transition-all">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </div>
            </a>
            @endforeach
        </div>

        {{-- ── PENGUMUMAN ───────────────────────────────────────────────── --}}
        @php
            $tabs = [
                ['key' => 'all',           'label' => 'Semua'],
                ['key' => 'bank_soal',     'label' => 'Bank Soal'],
                ['key' => 'capstone',      'label' => 'Capstone TA'],
                ['key' => 'kemahasiswaan', 'label' => 'Kemahasiswaan'],
                ['key' => 'eoffice',       'label' => 'EOffice'],
            ];

            $badgeMap = [
                'bank_soal'     => ['bg' => '#E8EDF7', 'text' => '#0B266E', 'dot' => '#3C518B', 'border_l' => '#3C518B', 'label' => 'Bank Soal'],
                'capstone'      => ['bg' => '#E8EDF7', 'text' => '#091958', 'dot' => '#091958', 'border_l' => '#091958', 'label' => 'Capstone TA'],
                'kemahasiswaan' => ['bg' => '#DDF2EE', 'text' => '#287F6E', 'dot' => '#40C4AA', 'border_l' => '#40C4AA', 'label' => 'Kemahasiswaan'],
                'eoffice'       => ['bg' => '#F9ECCB', 'text' => '#956321', 'dot' => '#D39C3D', 'border_l' => '#D39C3D', 'label' => 'EOffice'],
            ];
        @endphp

        <div x-data="{ activeTab: 'all' }">

            {{-- Section header + Tabs --}}
            <div class="flex items-end justify-between mb-0">
                <div class="mb-3">
                    <h2 class="text-[14px] font-bold text-[#0D0D12] tracking-tight">Pengumuman</h2>
                    <p class="text-[11px] text-[#A4ABB8] mt-0.5 font-medium">Informasi terbaru dari setiap modul</p>
                </div>
            </div>

            {{-- Tab bar --}}
            <div class="flex items-end gap-0 border-b border-[#DFE1E7]">
                @foreach($tabs as $tab)
                <button
                    @click="activeTab = '{{ $tab['key'] }}'"
                    :class="activeTab === '{{ $tab['key'] }}'
                        ? 'border-b-2 border-[#0B266E] text-[#0B266E] font-semibold bg-white'
                        : 'text-[#A4ABB8] hover:text-[#666D80] border-b-2 border-transparent hover:bg-[#F6F8FA]'"
                    class="px-4 py-2.5 text-[12px] transition-all duration-150 -mb-px whitespace-nowrap rounded-t-lg">
                    {{ $tab['label'] }}
                    @if($tab['key'] !== 'all')
                    <span x-show="activeTab !== '{{ $tab['key'] }}'"
                          class="ml-1 inline-flex items-center justify-center w-4 h-4 rounded-full bg-[#F0F1F4] text-[9px] font-bold text-[#A4ABB8]">
                        {{ $announcementCounts[$tab['key']] ?? 0 }}
                    </span>
                    @endif
                </button>
                @endforeach
            </div>

            {{-- Announcement pane --}}
            <div class="bg-white border border-t-0 border-[#DFE1E7] rounded-b-2xl overflow-hidden">

                {{-- Tab: Semua --}}
                <div x-show="activeTab === 'all'"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    @forelse($announcements['all'] ?? [] as $item)
                    @php $b = $badgeMap[$item['module']] ?? $badgeMap['bank_soal']; @endphp
                    <div class="flex items-start gap-4 px-5 py-4 border-b border-[#F0F1F4] last:border-b-0 hover:bg-[#F6F8FA] transition-colors group"
                         style="border-left: 3px solid {{ $b['border_l'] }}">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-semibold"
                                      style="background:{{ $b['bg'] }};color:{{ $b['text'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full inline-block"
                                          style="background:{{ $b['dot'] }}"></span>
                                    {{ $b['label'] }}
                                </span>
                                <span class="text-[10px] text-[#C1C7CF]">{{ $item['date'] }}</span>
                            </div>
                            <p class="text-[13px] font-semibold text-[#0D0D12] leading-snug group-hover:text-[#0B266E] truncate transition-colors">
                                {{ $item['title'] }}
                            </p>
                            <p class="text-[12px] text-[#808897] mt-0.5 leading-relaxed line-clamp-2">
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
                    <div class="flex flex-col items-center justify-center py-12 text-[#DFE1E7]">
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
                     style="display:none">
                    @forelse($announcements[$moduleKey] ?? [] as $item)
                    <div class="flex items-start gap-4 px-5 py-4 border-b border-[#F0F1F4] last:border-b-0 hover:bg-[#F6F8FA] transition-colors group"
                         style="border-left: 3px solid {{ $b['border_l'] }}">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[10px] text-[#C1C7CF]">{{ $item['date'] }}</span>
                                @if(!empty($item['pinned']))
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                                      style="background:#F9ECCB;color:#956321">
                                    Penting
                                </span>
                                @endif
                            </div>
                            <p class="text-[13px] font-semibold text-[#0D0D12] leading-snug group-hover:text-[#0B266E] truncate transition-colors">
                                {{ $item['title'] }}
                            </p>
                            <p class="text-[12px] text-[#808897] mt-0.5 leading-relaxed line-clamp-2">
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
                    <div class="flex flex-col items-center justify-center py-12">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#DFE1E7" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                        <p class="text-[12px] text-[#A4ABB8]">Belum ada pengumuman dari {{ $b['label'] }}</p>
                    </div>
                    @endforelse
                </div>
                @endforeach

            </div>
        </div>

        {{-- ── FOOTER ──────────────────────────────────────────────────── --}}
        <div class="mt-8 pt-5 border-t border-[#DFE1E7] flex items-center justify-between flex-wrap gap-2">
            <p class="text-[11px] text-[#A4ABB8]">
                &copy; {{ date('Y') }} <span class="font-semibold text-[#666D80]">{{ config('app.name') }}</span>. All rights reserved.
            </p>
            <p class="text-[10px] text-[#C1C7CF] font-semibold uppercase tracking-widest">SITKOM · Teknik Komputer UNDIP</p>
        </div>

    </div>
</div>
</x-app-layout>
