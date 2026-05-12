@php
    $hideNav = request()->routeIs('dashboard')
        || request()->routeIs('profile.*')
        || request()->is('superadmin*')
        || request()->is('bank-soal*')
        || request()->is('capstone*')
        || request()->is('eoffice*')
        || request()->is('manajemen-mahasiswa*')
        || request()->is('users/*')
        || request()->routeIs('superadmin.users.*');
@endphp

@if(!$hideNav)
<nav x-data="{ open: false }" style="background:#0B266E;border-bottom:1px solid #091958;font-family:'Inter Tight',system-ui,sans-serif;">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between" style="height:64px;">

            {{-- Left: brand + nav --}}
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
                        <img src="{{ asset('images/UNDIPOfficial.png') }}" alt="SITKOM" style="width:32px;height:32px;object-fit:contain;">
                        <span style="font-family:'Geist','Inter Tight',sans-serif;font-weight:700;color:#fff;font-size:16px;letter-spacing:-.01em;">SITKOM</span>
                    </a>
                </div>

                <div class="hidden sm:flex sm:items-center" style="margin-left:32px;gap:4px;">
                    <a href="{{ route('dashboard') }}" style="
                        padding:8px 14px;border-radius:8px;font-size:13px;font-weight:600;
                        color:#fff;text-decoration:none;
                        background:{{ request()->routeIs('dashboard') ? 'rgba(255,255,255,0.12)' : 'transparent' }};
                        transition:background .15s;"
                        onmouseover="this.style.background='rgba(255,255,255,0.10)'"
                        onmouseout="this.style.background='{{ request()->routeIs('dashboard') ? 'rgba(255,255,255,0.12)' : 'transparent' }}'">
                        {{ __('Dashboard') }}
                    </a>
                </div>
            </div>

            {{-- Right: user menu --}}
            <div class="hidden sm:flex sm:items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button style="display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border:1px solid rgba(255,255,255,.18);
                            border-radius:8px;background:rgba(255,255,255,.06);color:#fff;font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;transition:background .15s;"
                            onmouseover="this.style.background='rgba(255,255,255,0.12)'"
                            onmouseout="this.style.background='rgba(255,255,255,0.06)'">
                            <div style="width:24px;height:24px;border-radius:50%;background:linear-gradient(135deg,#8FA3D1,#5C78B8);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span>{{ Auth::user()->name }}</span>
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="trigger">
                        <button style="display:inline-flex;align-items:center;justify-content:space-between;min-width:180px;padding:6px 10px;border:1px solid rgba(255,255,255,.18);
                            border-radius:8px;background:rgba(255,255,255,.06);color:#fff;font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;transition:background .15s;"
                            onmouseover="this.style.background='rgba(255,255,255,0.12)'"
                            onmouseout="this.style.background='rgba(255,255,255,0.06)'">
                            
                            {{-- Wrapper untuk Avatar dan Nama supaya tetap berdampingan di kiri --}}
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:24px;height:24px;border-radius:50%;background:linear-gradient(135deg,#8FA3D1,#5C78B8);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span>{{ Auth::user()->name }}</span>
                            </div>

                            {{-- Ikon panah otomatis terdorong ke ujung kanan --}}
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Mobile toggle --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open" style="padding:8px;border-radius:8px;color:#fff;background:transparent;border:none;cursor:pointer;">
                    <svg style="width:24px;height:24px;" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden" style="background:#091958;border-top:1px solid rgba(255,255,255,.08);">
        <div style="padding:8px 16px;display:flex;flex-direction:column;gap:2px;">
            <a href="{{ route('dashboard') }}" style="padding:10px 12px;border-radius:8px;font-size:13px;font-weight:500;color:#fff;text-decoration:none;background:{{ request()->routeIs('dashboard') ? 'rgba(255,255,255,0.12)' : 'transparent' }};">
                {{ __('Dashboard') }}
            </a>
        </div>
        <div style="padding:12px 16px;border-top:1px solid rgba(255,255,255,.08);">
            <div style="font-weight:600;color:#fff;font-size:14px;">{{ Auth::user()->name }}</div>
            <div style="font-size:12px;color:#C2CEEA;margin-top:2px;">{{ Auth::user()->email }}</div>
            <div style="margin-top:10px;display:flex;flex-direction:column;gap:2px;">
                <a href="{{ route('profile.edit') }}" style="padding:8px 0;font-size:13px;color:#fff;text-decoration:none;">{{ __('Profile') }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" style="padding:8px 0;font-size:13px;color:#fff;text-decoration:none;display:block;">
                        {{ __('Log Out') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>
@endif
