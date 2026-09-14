        <header class="mp-topbar">
            <nav class="mp-breadcrumb" aria-label="Breadcrumb">
                <button type="button" class="mp-icon-button mp-mobile-menu" x-ref="mpMenuButton"
                        @click="sidebarOpen = true; $nextTick(() => $refs.mpSidebarToggle.focus())"
                        :aria-expanded="sidebarOpen" aria-controls="mp-navigation" aria-label="Buka menu praktikum">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <a class="mp-breadcrumb-root" href="{{ route('eoffice.dashboard') }}">E-Office</a>
                <span class="mp-breadcrumb-root" aria-hidden="true">/</span>
                <a href="{{ route('eoffice.manprak.dashboard') }}">Praktikum</a>
                <span aria-hidden="true">/</span>
                <strong aria-current="page" title="{{ $pageTitle ?? 'Dashboard' }}">{{ $pageTitle ?? 'Dashboard' }}</strong>
            </nav>
            <div class="mp-topbar-actions">

                {{-- ── Praktikum Switcher Kontekstual ── --}}
                @php
                    $switcherPraktikumList = collect();
                    $switcherActiveId = null;
                    $switcherContext = null;

                    // 1. Context Koordinator
                    if (str_contains($currentRoute, 'manprak.koor') || str_contains($currentRoute, 'manprak.koordinator')) {
                        if ($isKoor) {
                            $switcherContext = 'koor';
                            $switcherPraktikumList = \Modules\EOffice\Models\Praktikum::where('koor_id', $user->id)
                                ->whereIn('status', ['aktif', 'nonaktif'])
                                ->orderByRaw("status = 'aktif' DESC")
                                ->orderBy('created_at', 'desc')
                                ->get();
                            $switcherActiveId = session('koor_praktikum_id') ?? $switcherPraktikumList->first()?->id;
                        }
                    } 
                    // 2. Context Asisten Praktikum (Asprak)
                    elseif (str_contains($currentRoute, 'manprak.asprak')) {
                        if ($isAsprak) {
                            $switcherContext = 'asprak';
                            $aspraks = \Modules\EOffice\Models\AsprakPraktikum::with('praktikum')
                                ->where('user_id', $user->id)
                                ->where('role', 'asprak')
                                ->whereNull('deleted_at')
                                ->get();
                            $switcherPraktikumList = $aspraks->pluck('praktikum')->filter()->unique('id');
                            $switcherActiveId = session('manprak_asprak_praktikum_id') ?? $switcherPraktikumList->first()?->id;
                        }
                    } 
                    // 3. Context Mahasiswa (Praktikan)
                    elseif (str_contains($currentRoute, 'manprak.mahasiswa')) {
                        if ($isMhs) {
                            $switcherContext = 'mahasiswa';
                            $dps = \Modules\EOffice\Models\DaftarPraktikan::with('praktikum')
                                ->where('user_id', $user->id)
                                ->get();
                            $switcherPraktikumList = $dps->pluck('praktikum')->filter()->unique('id');
                            $switcherActiveId = session('mhs_praktikum_id') ?? $switcherPraktikumList->first()?->id;
                        }
                    }

                    $switcherActivePraktikum = $switcherPraktikumList->firstWhere('id', $switcherActiveId) ?? $switcherPraktikumList->first();
                @endphp

                @if($switcherContext && $switcherPraktikumList->count() > 0)
                <div class="mp-praktikum-switcher" x-data="{ open: false }" @keydown.escape.stop="open = false">
                    <button type="button" @click="open = !open" @click.outside="open = false" :aria-expanded="open" aria-label="Pilih praktikum"
                            class="flex items-center gap-[8px] px-3 py-[7px] rounded-[9px] border border-[#DFE1E7] bg-white hover:bg-[#F6F8FA] transition-colors cursor-pointer"
                            style="max-width:260px;">
                        {{-- Status dot --}}
                        <span class="w-[7px] h-[7px] rounded-full flex-shrink-0"
                              style="background:{{ ($switcherActivePraktikum?->status === 'aktif') ? '#40C4AA' : '#A4ABB8' }};"></span>
                        <span class="text-[12px] font-semibold text-[#0D0D12] truncate" style="max-width:160px;">
                            {{ $switcherActivePraktikum?->nama ?? 'Pilih Praktikum' }}
                        </span>
                        <svg class="w-[10px] h-[10px] flex-shrink-0 text-[#A4ABB8] transition-transform duration-150"
                             :class="open ? 'rotate-180' : ''"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 top-[calc(100%+6px)] z-50 bg-white border border-[#DFE1E7] rounded-[12px] shadow-[0_8px_24px_rgba(0,0,0,.12)] overflow-hidden"
                         style="width:300px;max-width:calc(100vw - 32px);max-height:60vh;overflow-y:auto;">
                        <div class="px-4 py-[10px] border-b border-[#F0F1F4]">
                            <div class="text-[10px] font-bold text-[#A4ABB8] uppercase tracking-[.06em]">Ganti Tampilan Praktikum</div>
                        </div>
                        @foreach($switcherPraktikumList as $p)
                        @if($switcherContext === 'koor')
                        <form method="POST" action="{{ route('eoffice.manprak.koor.switch-praktikum') }}">
                            @csrf
                            <input type="hidden" name="praktikum_id" value="{{ $p->id }}">
                        @else
                        {{-- Form GET Dinamis untuk Asprak & Mahasiswa --}}
                        <form method="GET" action="{{ url()->current() }}">
                            {{-- Mempertahankan query parameters yang sedang aktif (misal filter dll) --}}
                            @foreach(request()->except('praktikum_id') as $key => $value)
                                @if(is_array($value))
                                    @foreach($value as $v)
                                        <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <input type="hidden" name="praktikum_id" value="{{ $p->id }}">
                        @endif
                            <button type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-[10px] text-left transition-colors hover:bg-[#F6F8FA] border-none cursor-pointer"
                                    style="background:{{ $p->id === $switcherActiveId ? '#EEF1FA' : 'transparent' }};">
                                <span class="w-[7px] h-[7px] rounded-full flex-shrink-0"
                                      style="background:{{ $p->status === 'aktif' ? '#40C4AA' : '#A4ABB8' }};"></span>
                                <div class="flex-1 min-w-0">
                                    <div class="text-[13px] font-{{ $p->id === $switcherActiveId ? 'semibold' : 'medium' }} text-[#0D0D12] truncate">{{ $p->nama }}</div>
                                    <div class="text-[11px] text-[#666D80]">{{ $p->kode ?? 'Tanpa kode' }} · {{ ucfirst($p->status) }}</div>
                                </div>
                                @if($p->id === $switcherActiveId)
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                                @endif
                            </button>
                        </form>
                        @endforeach
                    </div>
                </div>
                @endif
                {{-- ── /Praktikum Switcher Kontekstual ── --}}


                @if($notifCount > 0)
                    <span class="mp-notification-count" title="{{ $notifCount }} notifikasi belum dibaca" aria-label="{{ $notifCount }} notifikasi belum dibaca">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9 M10 21a2 2 0 0 0 4 0"/></svg>
                        {{ $notifCount > 99 ? '99+' : $notifCount }}
                    </span>
                @endif
                <a href="{{ route('profile.edit') }}" class="mp-account" title="Pengaturan profil" aria-label="Pengaturan profil {{ $name }}">
                    <span class="mp-account-avatar" aria-hidden="true">
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="" width="34" height="34">
                        @else
                            {{ $initials }}
                        @endif
                    </span>
                    <span class="mp-account-meta"><strong>{{ $name }}</strong><span>{{ $activeRoleLabel }}</span></span>
                </a>
            </div>
        </header>
