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
                <div class="w-7 h-7 rounded-full bg-primary-50 flex items-center justify-center flex-shrink-0">
                    <span class="text-primary-600 font-bold text-[11px]">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </span>
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