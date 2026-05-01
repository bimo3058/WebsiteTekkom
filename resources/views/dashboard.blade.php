{{-- resources/views/dashboard.blade.php --}}

<x-app-layout>
<div class="min-h-screen bg-[#F8F9FA]">

    {{-- ── Topbar ─────────────────────────────────────────────────────────── --}}
    <header x-data="{ open: false }" class="sticky top-0 z-40 h-14 bg-white border-b border-slate-200 flex items-center px-6 justify-between">

        {{-- Kiri: Logo + nama app --}}
        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 bg-primary-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-slate-800 tracking-tight">{{ config('app.name') }}</span>
        </div>

        {{-- Kanan: Avatar + Dropdown --}}
        <div class="relative">
            <button @click="open = !open"
                    class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg hover:bg-slate-100 transition-colors group">
                
                {{-- Avatar circle --}}
                <div class="w-7 h-7 rounded-full bg-primary-50 flex items-center justify-center flex-shrink-0 overflow-hidden border border-primary-100">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="avatar" class="w-full h-full object-cover">
                    @else
                        <span class="text-primary-600 font-bold text-[11px] uppercase">
                            {{ substr(auth()->user()->name, 0, 2) }}
                        </span>
                    @endif
                </div>

                <div class="text-left hidden sm:block">
                    <p class="text-[12px] font-semibold text-slate-800 leading-tight">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-slate-400 leading-tight">
                        {{ ucfirst(auth()->user()->roles->first()->name ?? 'User') }}
                    </p>
                </div>
                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200"
                     :class="open ? 'rotate-180' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
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
                 class="absolute right-0 mt-1 w-52 bg-white rounded-xl border border-slate-200 shadow-lg py-1 z-50"
                 style="display:none">

                {{-- Info user --}}
                <div class="px-3 py-2.5 border-b border-slate-100">
                    <p class="text-[12px] font-semibold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email }}</p>
                </div>

                {{-- Profil --}}
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-2 px-3 py-2 text-[12px] text-slate-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profil Saya
                </a>

                <div class="border-t border-slate-100 my-1"></div>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-2 px-3 py-2 text-[12px] text-rose-600 hover:bg-rose-50 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </header>

    {{-- ── Body ────────────────────────────────────────────────────────────── --}}
    <div class="max-w-7xl mx-auto px-6 py-10">

        {{-- Greeting --}}
        <div class="mb-8">
            <h1 class="text-xl font-bold text-slate-800 tracking-tight">
                Selamat datang, <span class="text-primary-600">{{ explode(' ', auth()->user()->name)[0] }}</span> 👋
            </h1>
            <p class="text-slate-500 text-sm mt-1">Pilih modul yang ingin kamu akses hari ini.</p>
        </div>

        {{-- ── Module Cards ─────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($cards as $card)
            @php
                $colorMap = [
                    'blue'   => ['bg' => 'bg-blue-50',    'text' => 'text-blue-600',    'border' => 'hover:border-blue-200',    'hover' => 'group-hover:text-blue-700'],
                    'purple' => ['bg' => 'bg-purple-50',  'text' => 'text-purple-600',  'border' => 'hover:border-purple-200',  'hover' => 'group-hover:text-purple-700'],
                    'green'  => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'hover:border-emerald-200', 'hover' => 'group-hover:text-emerald-700'],
                    'orange' => ['bg' => 'bg-orange-50',  'text' => 'text-orange-600',  'border' => 'hover:border-orange-200',  'hover' => 'group-hover:text-orange-700'],
                ];
                $c = $colorMap[$card['color']] ?? $colorMap['blue'];
            @endphp

            <a href="{{ route($card['route']) }}"
               class="group relative bg-white border border-slate-200 {{ $c['border'] }} rounded-2xl p-5 hover:shadow-md transition-all duration-200 flex flex-col gap-5 overflow-hidden">

                {{-- Dekorasi lingkaran --}}
                <div class="absolute -top-4 -right-4 w-20 h-20 rounded-full {{ $c['bg'] }} opacity-40 group-hover:opacity-70 transition-opacity pointer-events-none"></div>

                {{-- Icon --}}
                <div class="relative w-10 h-10 rounded-xl {{ $c['bg'] }} flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined {{ $c['text'] }}" style="font-size:20px">
                        {{ $card['icon'] }}
                    </span>
                </div>

                {{-- Text --}}
                <div class="relative flex-1">
                    <h3 class="text-sm font-semibold text-slate-800 {{ $c['hover'] }} transition-colors leading-tight mb-1">
                        {{ $card['title'] }}
                    </h3>
                    <p class="text-slate-400 text-xs leading-relaxed">
                        {{ $card['description'] }}
                    </p>
                </div>

                {{-- Arrow --}}
                <div class="flex justify-end">
                    <svg class="w-4 h-4 text-slate-200 group-hover:text-slate-400 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
            @endforeach
        </div>

        {{-- ── Pengumuman Global ────────────────────────────────────────────────── --}}
        <div class="mt-8" x-data="{ activeTab: 'all' }">

            {{-- Section header --}}
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 tracking-tight">Pengumuman</h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">Informasi terbaru dari setiap modul</p>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="flex items-end gap-1 border-b border-slate-200">
                @php
                    $tabs = [
                        ['key' => 'all',           'label' => 'Semua',          'color' => 'blue'],
                        ['key' => 'bank_soal',     'label' => 'Bank Soal',      'color' => 'blue'],
                        ['key' => 'capstone',      'label' => 'Capstone TA',    'color' => 'purple'],
                        ['key' => 'kemahasiswaan', 'label' => 'Kemahasiswaan',  'color' => 'green'],
                        ['key' => 'eoffice',       'label' => 'EOffice',        'color' => 'orange'],
                    ];
                    $tabActiveClass = [
                        'blue'   => 'border-blue-500 text-blue-600',
                        'purple' => 'border-purple-500 text-purple-600',
                        'green'  => 'border-emerald-500 text-emerald-600',
                        'orange' => 'border-orange-500 text-orange-600',
                    ];
                @endphp

                @foreach($tabs as $tab)
                <button
                    @click="activeTab = '{{ $tab['key'] }}'"
                    :class="activeTab === '{{ $tab['key'] }}'
                        ? 'border-b-2 {{ $tabActiveClass[$tab['color']] }} bg-white font-semibold'
                        : 'text-slate-400 hover:text-slate-600 border-b-2 border-transparent'"
                    class="px-3.5 py-2 text-[12px] transition-all duration-150 -mb-px whitespace-nowrap">
                    {{ $tab['label'] }}
                    @if($tab['key'] !== 'all')
                    <span
                        x-show="activeTab !== '{{ $tab['key'] }}'"
                        class="ml-1 inline-flex items-center justify-center w-4 h-4 rounded-full bg-slate-100 text-[9px] font-bold text-slate-400">
                        {{-- ganti dengan count nyata, misal: $announcementCounts[$tab['key']] ?? 0 --}}
                        {{ $announcementCounts[$tab['key']] ?? 0 }}
                    </span>
                    @endif
                </button>
                @endforeach
            </div>

            {{-- Pane konten --}}
            <div class="bg-white border border-t-0 border-slate-200 rounded-b-2xl">

                {{-- Helper macro badge warna --}}
                @php
                    $badgeMap = [
                        'bank_soal'     => ['bg' => 'bg-blue-50',    'text' => 'text-blue-600',    'dot' => 'bg-blue-400',    'label' => 'Bank Soal'],
                        'capstone'      => ['bg' => 'bg-purple-50',  'text' => 'text-purple-600',  'dot' => 'bg-purple-400',  'label' => 'Capstone TA'],
                        'kemahasiswaan' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'dot' => 'bg-emerald-400', 'label' => 'Kemahasiswaan'],
                        'eoffice'       => ['bg' => 'bg-orange-50',  'text' => 'text-orange-600',  'dot' => 'bg-orange-400',  'label' => 'EOffice'],
                    ];
                    $borderMap = [
                        'bank_soal'     => 'border-l-blue-400',
                        'capstone'      => 'border-l-purple-400',
                        'kemahasiswaan' => 'border-l-emerald-400',
                        'eoffice'       => 'border-l-orange-400',
                    ];
                @endphp

                {{-- Tab: Semua --}}
                <div x-show="activeTab === 'all'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                    @forelse($announcements['all'] ?? [] as $item)
                    @php $b = $badgeMap[$item['module']] ?? $badgeMap['bank_soal']; $br = $borderMap[$item['module']] ?? ''; @endphp
                    <div class="flex items-start gap-3.5 px-5 py-4 border-b border-slate-100 last:border-b-0 hover:bg-slate-50/50 transition-colors group border-l-2 {{ $br }}">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full {{ $b['bg'] }} {{ $b['text'] }} text-[10px] font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $b['dot'] }}"></span>
                                    {{ $b['label'] }}
                                </span>
                                <span class="text-[10px] text-slate-300">{{ $item['date'] }}</span>
                            </div>
                            <p class="text-[12.5px] font-semibold text-slate-800 leading-snug group-hover:text-slate-900 truncate">
                                {{ $item['title'] }}
                            </p>
                            <p class="text-[11.5px] text-slate-400 mt-0.5 leading-relaxed line-clamp-2">
                                {{ $item['body'] }}
                            </p>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-200 group-hover:text-slate-400 flex-shrink-0 mt-1 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center py-12 text-slate-300">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <p class="text-[12px]">Belum ada pengumuman</p>
                    </div>
                    @endforelse
                </div>

                {{-- Tab: Per modul --}}
                @foreach(['bank_soal', 'capstone', 'kemahasiswaan', 'eoffice'] as $moduleKey)
                @php $b = $badgeMap[$moduleKey]; $br = $borderMap[$moduleKey]; @endphp
                <div x-show="activeTab === '{{ $moduleKey }}'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none">
                    @forelse($announcements[$moduleKey] ?? [] as $item)
                    <div class="flex items-start gap-3.5 px-5 py-4 border-b border-slate-100 last:border-b-0 hover:bg-slate-50/50 transition-colors group border-l-2 {{ $br }}">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[10px] text-slate-300">{{ $item['date'] }}</span>
                                @if(!empty($item['pinned']))
                                <span class="text-[10px] font-semibold text-amber-500 bg-amber-50 px-2 py-0.5 rounded-full">📌 Penting</span>
                                @endif
                            </div>
                            <p class="text-[12.5px] font-semibold text-slate-800 leading-snug group-hover:text-slate-900 truncate">
                                {{ $item['title'] }}
                            </p>
                            <p class="text-[11.5px] text-slate-400 mt-0.5 leading-relaxed line-clamp-2">
                                {{ $item['body'] }}
                            </p>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-200 group-hover:text-slate-400 flex-shrink-0 mt-1 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center py-12 text-slate-300">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <p class="text-[12px]">Belum ada pengumuman dari {{ $b['label'] }}</p>
                    </div>
                    @endforelse
                </div>
                @endforeach
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-10 pt-6 border-t border-slate-200 flex items-center justify-between">
            <p class="text-[11px] text-slate-400">
                &copy; {{ date('Y') }} <span class="font-semibold text-slate-500">{{ config('app.name') }}</span>. All rights reserved.
            </p>
            <p class="text-[10px] text-slate-300 font-bold uppercase tracking-widest">LuminHR System</p>
        </div>
    </div>
</div>
</x-app-layout>